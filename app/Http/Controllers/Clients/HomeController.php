<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products' => function ($query) {
            $query->with('firstImage', 'reviews', 'inventories')
                ->where('status', 'int_stock')
                ->whereHas('inventories', function ($query) {
                    $query->where('quantity_remaining', '>', 0)
                        ->whereDate('expired_at', '>=', now()->toDateString())
                        ->whereNotIn('condition', ['expired', 'damaged', 'sold_out']);
                });
        }])->get();

        foreach ($categories as $category) {
            foreach ($category->products as $product) {
                $this->setProductImageUrl($product);
            }
        }

        $promotionInventories = Inventory::with('product.category', 'product.firstImage', 'product.reviews')
            ->where('quantity_remaining', '>', 0)
            ->whereDate('expired_at', '>=', now()->toDateString())
            ->where('condition', 'near_expiry')
            ->whereNotNull('adjusted_price')
            ->where('adjusted_price', '>', 0)
            ->orderBy('expired_at')
            ->orderBy('id')
            ->get()
            ->filter(function ($inventory) {
                return $inventory->product
                    && $inventory->product->status == 'int_stock'
                    && $inventory->adjusted_price < $inventory->product->price;
            });

        $promotionProducts = $promotionInventories
            ->unique('product_id')
            ->map(function ($inventory) {
                $product = $inventory->product;
                $product->promotion_price = $inventory->sellingPrice();

                return $product;
            })
            ->values();

        foreach ($promotionProducts as $product) {
            $this->setProductImageUrl($product);
        }

        return view('clients.pages.home', compact('categories', 'promotionProducts'));
    }

    private function setProductImageUrl($product)
    {
        if ($product->firstImage && $product->firstImage->image) {
            $product->image_url = asset('storage/' . $product->firstImage->image);
        } else {
            $product->image_url = asset('storage/uploads/products/default.png');
        }
    }
}
