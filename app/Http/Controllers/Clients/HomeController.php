<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh mục kèm sản phẩm còn hàng để hiển thị ở trang chủ.
        $categories = Category::with(['products.firstImage', 'products.reviews', 'products.orderItems.order'])
            ->get();

        foreach ($categories as $category) {
            foreach ($category->products as $product) {
                $this->prepareHomeProduct($product);
            }
        }

        // Lấy sản phẩm khuyến mãi.
        $promotionProducts = [];
        $products = Product::with('firstImage', 'reviews', 'orderItems.order')
            ->where('status', 'int_stock')
            ->where('stock', '>', 0)
            ->orderByDesc('updated_at')
            ->get();

        foreach ($products as $product) {
            if (! $product->is_on_sale) {
                continue;
            }

            $this->preparePromotionProduct($product);
            $promotionProducts[] = $product;

            if (count($promotionProducts) == 12) {
                break;
            }
        }

        // Tính số lượng đã bán để lấy sản phẩm bán chạy.
        $soldQuantities = $this->soldQuantitiesByProduct();
        $bestSellerCategories = [];
        $tabIndex = 0;

        foreach ($categories as $category) {
            $bestSellerProducts = [];

            foreach ($category->products as $product) {
                if (! isset($soldQuantities[$product->id])) {
                    continue;
                }

                if ($product->is_on_sale) {
                    continue;
                }

                $product->sold_quantity = $soldQuantities[$product->id];
                $bestSellerProducts[] = $product;
            }

            if (count($bestSellerProducts) == 0) {
                continue;
            }

            usort($bestSellerProducts, function ($firstProduct, $secondProduct) {
                if ($secondProduct->sold_quantity > $firstProduct->sold_quantity) {
                    return 1;
                }

                if ($secondProduct->sold_quantity < $firstProduct->sold_quantity) {
                    return -1;
                }

                return 0;
            });

            $category->home_products = $bestSellerProducts;

            if ($tabIndex == 0) {
                $category->home_tab_class = 'active show';
                $category->home_content_class = 'active show';
            } else {
                $category->home_tab_class = '';
                $category->home_content_class = '';
            }

            $bestSellerCategories[] = $category;
            $tabIndex++;
        }

        // Gom sản phẩm để include modal một lần cuối trang.
        $homeModalProducts = [];
        $addedProductIds = [];

        foreach ($promotionProducts as $product) {
            $homeModalProducts[] = $product;
            $addedProductIds[$product->id] = true;
        }

        foreach ($bestSellerCategories as $category) {
            foreach ($category->home_products as $product) {
                if (isset($addedProductIds[$product->id])) {
                    continue;
                }

                $homeModalProducts[] = $product;
                $addedProductIds[$product->id] = true;
            }
        }

        return view('clients.pages.home', compact('categories', 'promotionProducts', 'bestSellerCategories', 'homeModalProducts'));
    }

    private function soldQuantitiesByProduct(): array
    {
        // Cộng số lượng đã bán, bỏ qua đơn hàng đã hủy.
        $soldQuantities = [];
        $orderItems = OrderItem::with('order')->get();

        foreach ($orderItems as $item) {
            if (! $item->order) {
                continue;
            }

            if (in_array($item->order->status, ['canceled', 'cancelled'], true)) {
                continue;
            }

            if (! isset($soldQuantities[$item->product_id])) {
                $soldQuantities[$item->product_id] = 0;
            }

            $soldQuantities[$item->product_id] += (int) $item->quantity;
        }

        return $soldQuantities;
    }

    private function preparePromotionProduct(Product $product): void
    {
        // Chuẩn bị giá khuyến mãi và phần trăm giảm giá cho từng sản phẩm.
        $salePrice = $product->promotion_price ?? $product->current_price;
        $discountPercent = 0;

        if ($product->price > 0 && $salePrice < $product->price) {
            $discountPercent = round((($product->price - $salePrice) / $product->price) * 100);
        }

        $this->prepareHomeProduct($product);
        $product->home_sale_price = $salePrice;
        $product->home_discount_percent = $discountPercent;
    }

    private function prepareHomeProduct(Product $product): void
    {
        // Chuẩn bị điểm đánh giá để view chỉ việc hiển thị.
        $product->home_avg_rating = $product->reviews->avg('rating') ?? 0;
        $product->home_total_reviews = $product->reviews->count();
        $product->sold_quantity = $product->soldQuantity();
    }
}