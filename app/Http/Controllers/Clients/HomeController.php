<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
   public function index()
{
    // Lấy tất cả danh mục kèm sản phẩm
    $categories = Category::with('products')->get();
    foreach($categories as $category) {
        foreach($category->products as $product){
            $product->image_url = $product->firstImage?->image 
                ? asset('storage/uploads/products/'. $product->firstImage->image) 
                : asset('storage/uploads/products/product_default.png');
        }
    }
    return view('clients.pages.home', compact('categories'));
}

}
