<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        if (! $keyword) {
            return redirect()->back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm');
        }
        $products = Product::with('firstImage', 'images', 'reviews', 'orderItems.order')
            ->where('status', 'int_stock')
            ->where('stock', '>', 0)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%$keyword%");
            })
            ->paginate(12);

        foreach ($products as $product) {
            $product->sold_quantity = $product->soldQuantity();
        }

        return view('clients.pages.products-search', compact('products'));
    }
}
