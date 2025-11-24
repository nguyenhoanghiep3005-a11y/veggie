<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        $products = Product::with('firstImage')->where('status', 'int_stock')->paginate(9);
        /** @var \App\Models\Product $product */
        foreach ($products as $product) {
            $product->image_url = $product->firstImage?->image
                ? asset('storage/uploads/products/' . $product->firstImage->image)
                : asset('storage/uploads/products/product_default.png');
        }


        return view('clients.pages.products', compact('categories', 'products'));
    }
    public function filter(Request $request)
    {
        $query = Product::query();
        if (
            $request->has('category_id') && $request->category_id != ''
        ) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('minPrice') && $request->has('maxPrice')) {
            $query->whereBetween('price', [$request->minPrice, $request->maxPrice]);
        }

        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;

                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;

                default:
                    $query->orderBy('id', 'desc');
                    break;
            }
        }

        $products = $query->paginate(9);
        /** @var \App\Models\Product $product */
        foreach ($products as $product) {
            $product->image_url = $product->firstImage?->image
                ? asset('storage/uploads/products/' . $product->firstImage->image)
                : asset('storage/uploads/products/product_default.png');
        }
        return Response()->json([
            'products' => view('clients.components.products_grid', compact('products'))->render(),
            'pagination' => $products->links('clients.components.pagination.pagination_custom')->toHtml()
        ]);
    }
    public function detail($slug){
        $product = Product::with(['category', 'images', 'reviews.user'])->where('slug', $slug)->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->limit(6)
        ->get();
        return view('clients.pages.product-detail', compact('product','relatedProducts'));
    
    }
    public function ViewCart()
    {
        return view('clients.pages.cart');
    }
}
