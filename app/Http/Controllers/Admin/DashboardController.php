<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
   {
    $user = User::where('role_id',3)->latest()->get();
    $categories = Category::with('products')->get();
    $products = Product::whereHas('inventories', function ($query) {
        $query->where('quantity_remaining', '>', 0)
            ->whereDate('expired_at', '>=', now()->toDateString())
            ->whereNotIn('condition', ['expired', 'damaged', 'sold_out']);
    })->get();
    $orders = Order::with('shippingAddress')->latest()->limit(20)->get();
    return view('admin.pages.dashboard',compact('user','categories','products','orders'));
   }
}
