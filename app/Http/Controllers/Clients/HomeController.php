<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\OrderItem;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::with('products.firstImage', 'products.reviews', 'products.inventories')->get();

        foreach ($categories as $category) {
            $products = collect();

            foreach ($category->products as $product) {
                if ($product->status == 'int_stock' && $product->stock > 0) {
                    $products->push($product);
                }
            }

            $category->setRelation('products', $products);
        }

        $promotionProducts = $this->promotionProducts();
        $bestSellerCategories = $this->bestSellerCategories($categories);

        return view('clients.pages.home', compact('categories', 'promotionProducts', 'bestSellerCategories'));
    }

    private function promotionProducts()
    {
        $inventories = Inventory::with('product.category', 'product.firstImage', 'product.reviews')
            ->orderBy('expired_at')
            ->orderBy('id')
            ->get();

        $promotionProducts = collect();
        $addedProductIds = [];

        foreach ($inventories as $inventory) {
            if (!$this->isPromotionInventory($inventory)) {
                continue;
            }

            if (in_array($inventory->product_id, $addedProductIds)) {
                continue;
            }

            $product = $inventory->product;
            $product->promotion_price = $inventory->sellingPrice();

            $promotionProducts->push($product);
            $addedProductIds[] = $inventory->product_id;
        }

        return $promotionProducts;
    }

    private function isPromotionInventory($inventory)
    {
        if (!$inventory->product) {
            return false;
        }

        if ($inventory->product->status != 'int_stock') {
            return false;
        }

        if (!$inventory->isAvailable()) {
            return false;
        }

        if ($inventory->adjusted_price === null || $inventory->adjusted_price <= 0) {
            return false;
        }

        return $inventory->adjusted_price < $inventory->product->price;
    }

    private function bestSellerCategories($categories)
    {
        $soldQuantities = $this->soldQuantitiesByProduct();
        $bestSellerCategories = collect();

        foreach ($categories as $category) {
            $products = collect();

            foreach ($category->products as $product) {
                if (!isset($soldQuantities[$product->id])) {
                    continue;
                }

                if ($this->hasActivePromotion($product)) {
                    continue;
                }

                $product->sold_quantity = $soldQuantities[$product->id];
                $products->push($product);
            }

            $products = $products->sortByDesc('sold_quantity')->values();

            if ($products->isEmpty()) {
                continue;
            }

            $bestSellerCategory = clone $category;
            $bestSellerCategory->setRelation('products', $products);
            $bestSellerCategories->push($bestSellerCategory);
        }

        return $bestSellerCategories;
    }

    private function soldQuantitiesByProduct()
    {
        $soldQuantities = [];
        $orderItems = OrderItem::with('order')->get();

        foreach ($orderItems as $item) {
            if (!$item->order) {
                continue;
            }

            if (in_array($item->order->status, ['canceled', 'cancelled'])) {
                continue;
            }

            if (!isset($soldQuantities[$item->product_id])) {
                $soldQuantities[$item->product_id] = 0;
            }

            $soldQuantities[$item->product_id] += (int) $item->quantity;
        }

        return $soldQuantities;
    }

    private function hasActivePromotion($product)
    {
        foreach ($product->inventories as $inventory) {
            if (!$inventory->isAvailable()) {
                continue;
            }

            if ($inventory->adjusted_price !== null
                && $inventory->adjusted_price > 0
                && $inventory->adjusted_price < $product->price) {
                return true;
            }
        }

        return false;
    }
}
