<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Services\CloudinaryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    protected $cloudinary;

    // Gắn service upload ảnh/video minh chứng đổi trả.
    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    // Hiển thị chi tiết đơn hàng của khách đang đăng nhập.
    public function showOrder($id)
    {
        $order = Order::with([
            'orderItems.product.images',
            'user',
            'shippingAddress',
            'payment',
            'returnRequest',
        ])->where('user_id', Auth::id())->findOrFail($id);

        $returnRequest = $order->returnRequest;
        $returnItems = $this->returnItemsByOrderItem($returnRequest);
        $canRequestReturn = $order->isReturnPeriodAvailable();
        $shippingInfo = $this->shippingInfo($order);

        return view('clients.pages.order-detail', compact(
            'order',
            'returnRequest',
            'returnItems',
            'canRequestReturn',
            'shippingInfo'
        ));
    }

    // Sắp xếp sản phẩm đổi/trả theo id dòng đơn hàng để view lấy ra dễ hơn.
    private function returnItemsByOrderItem($returnRequest)
    {
        $returnItems = [];

        if (! $returnRequest || ! is_array($returnRequest->items)) {
            return $returnItems;
        }

        foreach ($returnRequest->items as $returnItem) {
            if (isset($returnItem['order_item_id'])) {
                $returnItems[$returnItem['order_item_id']] = $returnItem;
            }
        }

        return $returnItems;
    }

    // Lấy thông tin giao hàng để Blade chỉ việc hiển thị.
    private function shippingInfo($order)
    {
        $shippingInfo = [
            'name' => '—',
            'phone' => '—',
            'address' => '—',
            'city' => '—',
        ];

        $shippingAddress = $order->shippingAddress;

        if ($shippingAddress) {
            $shippingInfo['name'] = $shippingAddress->full_name;
            $shippingInfo['phone'] = $shippingAddress->phone;
            $shippingInfo['address'] = $shippingAddress->address;
            $shippingInfo['city'] = $shippingAddress->city;
        }

        return $shippingInfo;
    }

    // Khách gửi yêu cầu đổi/trả khi sản phẩm lỗi hoặc hư hỏng.
    public function requestReturn(Request $request, $id)
    {
        $order = Order::with(['orderItems.product', 'returnRequest'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status != 'completed' || $order->returnRequest) {
            return back()->with('error', 'Chỉ đơn hàng đã hoàn thành mới được gửi yêu cầu đổi/trả hàng lỗi.');
        }

        if (! $order->isReturnPeriodAvailable()) {
            return back()->with('error', 'Đơn hàng chỉ được gửi yêu cầu đổi/trả trong vòng 3 ngày kể từ khi nhận hàng.');
        }

        $data = $request->validate([
            'description' => 'required|string|max:3000',
            'items' => 'required|array',
            'items.*.quantity' => 'required|integer|min:0',
            'evidence' => 'required|array|min:1',
            'evidence.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
        ], [
            'description.required' => 'Vui lòng mô tả tình trạng hàng lỗi.',
            'items.required' => 'Vui lòng chọn sản phẩm cần đổi/trả.',
            'evidence.required' => 'Hàng lỗi phải có ít nhất một ảnh hoặc video minh chứng.',
            'evidence.*.max' => 'Mỗi ảnh/video không được vượt quá 50MB.',
        ]);

        $returnItems = [];

        foreach ($data['items'] as $orderItemId => $itemInput) {
            $quantity = 0;
            if (isset($itemInput['quantity'])) {
                $quantity = (int) $itemInput['quantity'];
            }

            if ($quantity <= 0) {
                continue;
            }

            $orderItem = null;
            foreach ($order->orderItems as $item) {
                if ($item->id == (int) $orderItemId) {
                    $orderItem = $item;
                }
            }

            if (! $orderItem) {
                return back()->withInput()->with('error', 'Sản phẩm đổi/trả không thuộc đơn hàng này.');
            }

            if ($quantity > $orderItem->quantity) {
                return back()->withInput()->with('error', 'Số lượng đổi/trả không được lớn hơn số lượng đã mua.');
            }

            $returnItems[] = [
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'quantity' => $quantity,
            ];
        }

        if ($returnItems === []) {
            return back()->withInput()->with('error', 'Vui lòng nhập ít nhất một sản phẩm cần đổi/trả.');
        }

        $storedPaths = [];

        try {
            DB::beginTransaction();
            $order = Order::with('returnRequest')->lockForUpdate()->findOrFail($id);

            if ($order->status != 'completed' || $order->returnRequest) {
                throw new Exception('Đơn hàng không còn đủ điều kiện gửi yêu cầu.');
            }

            if (! $order->isReturnPeriodAvailable()) {
                throw new Exception('Đơn hàng chỉ được gửi yêu cầu đổi/trả trong vòng 3 ngày kể từ khi nhận hàng.');
            }

            $files = $request->file('evidence', []);
            $uploadFiles = [];
            foreach ($files as $file) {
                if ($file) {
                    $uploadFiles[] = $file;
                }
            }

            $media = $this->cloudinary->uploadMany(
                $uploadFiles,
                config('cloudinary.folders.order_returns'),
                $storedPaths
            );

            $order->update(['status' => 'return_requested']);

            OrderReturn::create([
                'order_id' => $order->id,
                'type' => OrderReturn::TYPE_DAMAGED,
                'description' => $data['description'],
                'items' => $returnItems,
                'media' => $media,
                'requested_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Đã gửi yêu cầu đổi/trả hàng lỗi. Yêu cầu đang chờ duyệt.');
        } catch (Exception $exception) {
            DB::rollBack();

            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return back()->withInput()->with('error', $exception->getMessage());
        }
    }

    // Khách hủy đơn hàng khi đơn còn đang chờ xác nhận.
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:1000',
        ], [
            'cancel_reason.required' => 'Vui lòng nhập lý do hủy đơn hàng.',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $order = Order::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->with('orderItems.product')
                    ->lockForUpdate()
                    ->firstOrFail();

                foreach ($order->orderItems as $item) {
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

                $order->update([
                    'status' => 'canceled',
                    'canceled_by' => 'client',
                    'cancel_reason' => $request->input('cancel_reason'),
                ]);
            });

            return back()->with('success', 'Đơn hàng đã được hủy và hoàn tồn sản phẩm.');
        } catch (Exception $exception) {
            return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại.');
        }
    }
}
