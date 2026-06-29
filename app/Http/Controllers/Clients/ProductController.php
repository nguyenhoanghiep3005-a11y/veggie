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
        // Lấy danh mục và sản phẩm của từng danh mục
        $categories = Category::with('products')->get();

        // Lấy danh sách sản phẩm còn hàng  kèm ảnh đầu tiên
        $products = Product::with('firstImage', 'inventories')
            ->where('status', 'int_stock')
            ->whereHas('inventories', function ($query) {
                $query->where('quantity_remaining', '>', 0)
                    ->whereDate('expired_at', '>=', now()->toDateString())
                    ->whereNotIn('condition', ['expired', 'damaged', 'sold_out']);
            })
            ->paginate(9);

        // Gán URL ảnh cho từng sản phẩm
        foreach ($products as $product) {
            $product->image_url = $product->firstImage?->image
                ? asset('storage/uploads/products/' . $product->firstImage->image)
                : asset('storage/uploads/products/default.png');
        }

        // Trả về giao diện danh sách sản phẩm
        return view('clients.pages.products', compact('categories', 'products'));
    }

    public function filter(Request $request)
    {
        // Tạo query filter sản phẩm
        $query = Product::with('firstImage', 'inventories')
            ->where('status', 'int_stock')
            ->whereHas('inventories', function ($query) {
                $query->where('quantity_remaining', '>', 0)
                    ->whereDate('expired_at', '>=', now()->toDateString())
                    ->whereNotIn('condition', ['expired', 'damaged', 'sold_out']);
            });

        // Lọc theo danh mục
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo giá
        if ($request->has('minPrice') && $request->has('maxPrice')) {
            $query->whereBetween('price', [$request->minPrice, $request->maxPrice]);
        }

        // Sắp xếp sản phẩm
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

        // Lấy danh sách sau khi lọc
        $products = $query->paginate(9);

        // Gán hình ảnh cho từng sản phẩm
        foreach ($products as $product) {
            $product->image_url = $product->firstImage?->image
                ? asset('storage/uploads/products/' . $product->firstImage->image)
                : asset('storage/uploads/products/default.png');
        }

        // Trả HTML sản phẩm + phân trang qua AJAX
        return Response()->json([
            'products' => view('clients.components.products_grid', compact('products'))->render(),
            'pagination' => $products->links('clients.components.pagination.pagination_custom')->toHtml()
        ]);
    }

    public function detail($slug)
    {
        // Chi tiết sản phẩm + ảnh + đánh giá
        $product = Product::with(['category', 'images', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Sản phẩm liên quan cùng danh mục
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'int_stock')
            ->whereHas('inventories', function ($query) {
                $query->where('quantity_remaining', '>', 0)
                    ->whereDate('expired_at', '>=', now()->toDateString())
                    ->whereNotIn('condition', ['expired', 'damaged', 'sold_out']);
            })
            ->limit(6)
            ->get();

        // Trả về giao diện chi tiết sản phẩm
        return view('clients.pages.product-detail', compact('product', 'relatedProducts'));
    }

    public function ViewCart()
    {
        // Giao diện giỏ hàng
        return view('clients.pages.cart');
    }
}
