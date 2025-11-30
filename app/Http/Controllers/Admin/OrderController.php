<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems', 'shippingAddress', 'user', 'payment')->orderByDesc('id')->get();
        return view('admin.pages.orders', compact('orders'));
    }
    public function showOrderDetail($id)
    {
        $order = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment')->find($id);

        return view('admin.pages.order-detail', compact('order'));
    }
    // public function sendMailInvoice(Request $request)
    // {
    //     $id = $request->id;
    //     $order = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment')->find($id);
    //     try {
    //         Mail::send('admin.emails.invoice', compact('order'), function ($message) use ($order) {
    //             $message->to($order->user->email)
    //                 ->subject('Hóa đơn đặt của khách hàng - ' . $order->shippingAddress->full_name);
    //         });
    //         return response()->json(['success' => true, 'message' => 'Hóa đơn đã được gửi qua email!']);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Không thể gửi hóa đơn. Vui lòng thử lại  ' . $th->getMessage()], 500);
    //     }
    // }
    public function cancelOrder(Request $request)
    {
        $id = $request->id;
        $order = Order::find($id);

        if ($order) {
            foreach ($order->orderItems as $item) {
                $item->product->increment('stock', $item->quantity);
            }
            $order->status = 'canceled';
            $order->save();
            return response()->json([
                'status' => true,
                'message' => 'Đơn hàng đa hủy thành công',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Đơn hàng Không tồn tại',
        ]);
    }
}
