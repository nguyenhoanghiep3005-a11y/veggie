<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems','shippingAddress', 'user', 'payment')->orderByDesc('id')->get();
        return view('admin.pages.orders',compact('orders'));
    }
    public function showOrderDetail($id)
    {
        $order = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment')->find($id);
        
        return view('admin.pages.order-detail', compact('order'));
    }
}
