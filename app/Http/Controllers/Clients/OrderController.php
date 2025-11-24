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
        ->firstOrfail();
        foreach($order->orderItems as $item)
        {
            $item->product->increment('stock', $item->quantity);
        }
        // update order status cancel
        $order->update(['status'=> 'canceled']);
        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công và sản phẩm được hoàn kho');
    }
}
