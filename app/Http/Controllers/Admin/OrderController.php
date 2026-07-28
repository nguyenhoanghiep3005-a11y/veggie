<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    // Hiển thị danh sách tất cả đơn hàng cho admin.
    public function index()
    {
        $orders = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment', 'returnRequest')
            ->orderByDesc('id')
            ->get();

        return view('admin.pages.orders', compact('orders'));
    }

    // Hiển thị chi tiết một đơn hàng cho admin.
    public function showOrderDetail($id)
    {
        $order = Order::with([
            'orderItems.product.images',
            'shippingAddress',
            'user',
            'payment',
            'returnRequest',
        ])->findOrFail($id);

        return view('admin.pages.order-detail', compact('order'));
    }

    // Admin xác nhận đơn hàng đang chờ xử lý.
    public function confirmOrder(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:orders,id',
        ]);

        try {
            DB::beginTransaction();
            $order = Order::with(['orderItems.product', 'shippingAddress', 'user', 'payment'])
                ->lockForUpdate()
                ->findOrFail($request->integer('id'));

            if ($order->status != 'pending') {
                throw new Exception('Chỉ có thể xác nhận đơn đang chờ xử lý.');
            }

            $order->update(['status' => 'confirmed']);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }

        $invoice = $this->sendInvoiceForOrder($order);
        $message = 'Xác nhận đơn hàng thành công.';

        if ($invoice['sent']) {
            $message .= ' Hóa đơn đã được gửi tới email khách hàng.';
        } elseif (! $invoice['skipped']) {
            $message .= ' Tuy nhiên chưa gửi được hóa đơn: '.$invoice['message'];
        }

        return response()->json([
            'status' => true,
            'invoice_sent' => $invoice['sent'],
            'message' => $message,
        ]);
    }

    // Admin chuyển đơn đã xác nhận sang đang giao.
    public function shipOrder(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:orders,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $order = Order::lockForUpdate()->findOrFail($request->integer('id'));

                if ($order->status != 'confirmed') {
                    throw new Exception('Chỉ có thể giao đơn hàng đã được xác nhận.');
                }

                $order->update(['status' => 'shipping']);
            });

            return response()->json(['status' => true, 'message' => 'Đơn hàng đang được giao.']);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    // Admin đánh dấu đơn đang giao là đã giao thành công.
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:orders,id',
            'status' => 'required|in:completed',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $order = Order::with('payment')->lockForUpdate()->findOrFail($request->integer('id'));

                if ($order->status != 'shipping') {
                    throw new Exception('Chỉ có thể hoàn thành đơn đang giao.');
                }

                $order->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                $this->completePaymentForDeliveredOrder($order);
            });

            return response()->json(['status' => true, 'message' => 'Cập nhật trạng thái thành công!']);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    // Admin hủy đơn và hoàn tồn kho sản phẩm.
    public function cancelOrder(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:orders,id',
            'cancel_reason' => 'required|string|max:1000',
        ], [
            'cancel_reason.required' => 'Vui lòng nhập lý do hủy đơn hàng.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $order = Order::with('orderItems.product')->lockForUpdate()->findOrFail($request->integer('id'));

                if (! in_array($order->status, ['pending', 'confirmed', 'shipping'], true)) {
                    throw new Exception('Không thể hủy đơn hàng ở trạng thái hiện tại.');
                }

                $this->restoreProductStock($order);

                $order->update([
                    'status' => 'canceled',
                    'canceled_by' => 'admin',
                    'cancel_reason' => $request->input('cancel_reason'),
                ]);
            });

            return response()->json(['status' => true, 'message' => 'Hủy đơn hàng thành công và đã hoàn tồn sản phẩm.']);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    // Cập nhật thanh toán COD khi đơn đã giao thành công.
    private function completePaymentForDeliveredOrder($order)
    {
        if (! $order->payment) {
            return;
        }

        if ($order->payment->status == 'completed') {
            return;
        }

        $order->payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    // Hoàn lại tồn kho cho từng sản phẩm trong đơn bị hủy.
    private function restoreProductStock($order)
    {
        foreach ($order->orderItems as $item) {
            if (! $item->product) {
                continue;
            }

            $product = Product::lockForUpdate()->find($item->product_id);
            if (! $product) {
                continue;
            }

            $allocations = [];
            if (isset($item->stock_allocations)) {
                $allocations = $item->stock_allocations;
            }

            $product->restoreStock((int) $item->quantity, $allocations);
        }
    }

    // Gửi hóa đơn qua email sau khi admin xác nhận đơn hàng.
    private function sendInvoiceForOrder($order)
    {
        if (! $order->user_id || ! $order->user) {
            return [
                'sent' => false,
                'skipped' => true,
                'message' => 'Khách vãng lai không cần gửi hóa đơn qua email.',
            ];
        }

        $email = $order->user->email;

        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['sent' => false, 'skipped' => false, 'message' => 'Tài khoản khách hàng chưa có email hợp lệ.'];
        }

        try {
            Mail::to($email)->send(new OrderInvoiceMail($order));

            return ['sent' => true, 'skipped' => false, 'message' => 'Đã gửi hóa đơn.'];
        } catch (Exception $exception) {
            Log::error('Lỗi gửi hóa đơn tự động khi xác nhận đơn hàng', [
                'order_id' => $order->id,
                'recipient' => $email,
                'exception' => $exception,
            ]);

            return ['sent' => false, 'skipped' => false, 'message' => 'Kiểm tra lại cấu hình email của hệ thống.'];
        }
    }
}
