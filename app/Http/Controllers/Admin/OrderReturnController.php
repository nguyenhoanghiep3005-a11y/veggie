<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderReturnController extends Controller
{
    // Admin duyệt yêu cầu đổi/trả hàng lỗi của khách.
    public function approve($id)
    {
        $orderReturn = OrderReturn::with('order')->find($id);

        if (! $orderReturn || ! $orderReturn->order) {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu đổi/trả không tồn tại.',
            ]);
        }

        if ($orderReturn->order->status != 'return_requested') {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu này không còn ở trạng thái chờ duyệt.',
            ]);
        }

        $orderReturn->order->status = 'return_pickup';
        $orderReturn->order->save();

        $orderReturn->approved_at = now();
        $orderReturn->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã duyệt yêu cầu đổi/trả. Đơn hàng chuyển sang chờ nhận hàng lỗi.',
        ]);
    }

    // Admin xác nhận đã nhận hàng lỗi và trừ kho sản phẩm giao đổi.
    public function receive($id)
    {
        $orderReturn = OrderReturn::with('order.orderItems.product')->find($id);

        if (! $orderReturn || ! $orderReturn->order) {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu đổi/trả không tồn tại.',
            ]);
        }

        if ($orderReturn->order->status != 'return_pickup') {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu này chưa ở trạng thái chờ nhận hàng lỗi.',
            ]);
        }

        $returnItems = $orderReturn->items;
        if (! is_array($returnItems) || count($returnItems) == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu đổi/trả chưa có sản phẩm hợp lệ.',
            ]);
        }

        DB::beginTransaction();

        try {
            $itemsAfterReceive = [];

            foreach ($returnItems as $item) {
                $orderItemId = 0;
                $quantity = 0;

                if (isset($item['order_item_id'])) {
                    $orderItemId = (int) $item['order_item_id'];
                }

                if (isset($item['quantity'])) {
                    $quantity = (int) $item['quantity'];
                }

                $orderItem = null;
                foreach ($orderReturn->order->orderItems as $orderProductItem) {
                    if ($orderProductItem->id == $orderItemId) {
                        $orderItem = $orderProductItem;
                    }
                }

                if (! $orderItem || ! $orderItem->product || $quantity <= 0 || $quantity > $orderItem->quantity) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => 'Sản phẩm hoặc số lượng đổi/trả không hợp lệ.',
                    ]);
                }

                $product = Product::lockForUpdate()->find($orderItem->product_id);
                if (! $product || $product->sellableStock() < $quantity) {
                    DB::rollBack();

                    $productName = 'Đổi hàng';
                    if ($orderItem->product) {
                        $productName = $orderItem->product->display_name;
                    }

                    return response()->json([
                        'status' => false,
                        'message' => 'Sản phẩm "'.$productName.'" không đủ tồn để giao đổi.',
                    ]);
                }

                $item['replacement_allocations'] = $product->consumeStock($quantity);
                $itemsAfterReceive[] = $item;
            }

            $orderReturn->items = $itemsAfterReceive;
            $orderReturn->received_at = now();
            $orderReturn->save();

            $orderReturn->order->status = 'replacement_shipping';
            $orderReturn->order->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Đã nhận hàng lỗi. Đơn hàng chuyển sang trạng thái đang giao sản phẩm đổi cho khách.',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Không thể xử lý yêu cầu đổi/trả.',
            ]);
        }
    }

    // Admin hoàn tất yêu cầu đổi hàng sau khi giao sản phẩm đổi cho khách.
    public function complete($id)
    {
        $orderReturn = OrderReturn::with('order')->find($id);

        if (! $orderReturn || ! $orderReturn->order) {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu đổi/trả không tồn tại.',
            ]);
        }

        if ($orderReturn->order->status != 'replacement_shipping') {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ hoàn tất khi đơn đang giao sản phẩm đổi.',
            ]);
        }

        $orderReturn->order->status = 'replacement_completed';
        $orderReturn->order->save();

        $orderReturn->status = 'approved';
        $orderReturn->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã hoàn tất yêu cầu đổi hàng.',
        ]);
    }
}
