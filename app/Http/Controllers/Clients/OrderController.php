<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function showOrder($id)
    {
        $order = Order::with(['orderItems.product', 'user', 'shippingAddress', 'payment'])->findOrFail($id);
        $userId = Auth::id();
        return view('clients.pages.order-detail', compact('order'));
    }
    public function cancel($id)
    {
        $order = Order::where('id', $id)
        ->where('user_id', Auth::id())
        ->where('status','pending')
        ->with('orderItems.inventory.product', 'orderItems.product')
        ->firstOrfail();
        $this->restoreInventoryFromOrder($order);
        // update order status cancel
        $order->update(['status'=> 'canceled']);
        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công và sản phẩm được hoàn kho');
    }
    private function restoreInventoryFromOrder($order)
    {
        foreach ($order->orderItems as $item) {
            $inventory = $item->inventory;

            if (!$inventory && $item->product) {
                $inventory = $item->product->inventories()->orderBy('expired_at')->first();
            }

            if ($inventory) {
                $inventory->quantity_remaining = $inventory->quantity_remaining + $item->quantity;
                $maxRemainingQuantity = max(0, $inventory->quantity_imported - $inventory->quantity_damaged);

                if ($inventory->quantity_remaining > $maxRemainingQuantity) {
                    $inventory->quantity_remaining = $maxRemainingQuantity;
                }

                $inventory->refreshCondition();
                $inventory->save();

                if ($item->product) {
                    $this->updateProductStatus($item->product);
                } elseif ($inventory->product) {
                    $this->updateProductStatus($inventory->product);
                }
            }
        }
    }

    private function updateProductStatus($product)
    {
        $availableQuantity = $product->availableInventories()->sum('quantity_remaining');

        if ($availableQuantity > 0) {
            $product->status = 'int_stock';
        } else {
            $product->status = 'out_of_stock';
        }

        $product->save();
    }
}
