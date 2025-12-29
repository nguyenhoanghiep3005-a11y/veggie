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
    $products = Product::where('stock', '>', 0)->get();
    $orders = Order::with('shippingAddress')->latest()->limit(20)->get();
    return view('admin.pages.dashboard',compact('user','categories','products','orders'));
   }
}
