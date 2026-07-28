<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $wantedSlugs = [];

        foreach ($this->catalog() as $categorySlug => $items) {
            $category = Category::where('slug', $categorySlug)->first();
            if (! $category) {
                continue;
            }

            foreach ($items as $item) {
                foreach ($item['variants'] as $unit => $price) {
                    $slug = Str::slug($item['name'].'-'.$unit);
                    $wantedSlugs[] = $slug;

                    Product::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $item['name'],
                            'category_id' => $category->id,
                            'description' => $this->description($item),
                            'price' => $price,
                            'stock' => 0,
                            'status' => 'out_of_stock',
                            'unit' => $unit,
                            'average_rating' => 5,
                        ]
                    );
                }
            }
        }

        $this->removeUnusedSampleProducts($wantedSlugs);
    }

    private function catalog(): array
    {
        return [
            'thuc-pham-kho' => [
                [
                    'name' => 'Nấm đông cô',
                    'origin' => 'Lâm Đồng, Việt Nam',
                    'storage' => 'Để nơi khô ráo, thoáng mát, đậy kín sau khi mở bao bì.',
                    'variants' => ['100g' => 95000, '250g' => 220000, '500g' => 420000],
                ],
                [
                    'name' => 'Mộc nhĩ khô',
                    'origin' => 'Hòa Bình, Việt Nam',
                    'storage' => 'Tránh ẩm, tránh ánh nắng trực tiếp.',
                    'variants' => ['100g' => 68000, '250g' => 150000, '500g' => 285000],
                ],
                [
                    'name' => 'Miến dong',
                    'origin' => 'Bắc Kạn, Việt Nam',
                    'storage' => 'Bảo quản nơi khô, buộc kín sau khi dùng.',
                    'variants' => ['500g' => 52000, '1kg' => 98000, '2kg' => 188000],
                ],
                [
                    'name' => 'Bánh đa nem gạo',
                    'origin' => 'Tây Ninh, Việt Nam',
                    'storage' => 'Để nơi khô ráo, tránh làm gãy vỡ.',
                    'variants' => ['250g' => 25000, '500g' => 45000, '1kg' => 85000],
                ],
            ],
            'gia-vi' => [
                [
                    'name' => 'Tiêu đen xay',
                    'origin' => 'Đắk Lắk, Việt Nam',
                    'storage' => 'Đóng kín nắp sau khi sử dụng.',
                    'variants' => ['100g' => 52000, '250g' => 115000, '500g' => 220000],
                ],
                [
                    'name' => 'Bột nghệ nguyên chất',
                    'origin' => 'Nghệ An, Việt Nam',
                    'storage' => 'Bảo quản trong hũ kín, tránh ẩm.',
                    'variants' => ['100g' => 60000, '250g' => 135000, '500g' => 260000],
                ],
                [
                    'name' => 'Ớt bột Hàn Quốc',
                    'origin' => 'Nhập khẩu Hàn Quốc',
                    'storage' => 'Để nơi thoáng mát, tránh ánh nắng trực tiếp.',
                    'variants' => ['100g' => 49000, '250g' => 110000, '500g' => 210000],
                ],
                [
                    'name' => 'Quế thanh',
                    'origin' => 'Yên Bái, Việt Nam',
                    'storage' => 'Để nơi khô ráo để giữ mùi thơm.',
                    'variants' => ['100g' => 55000, '250g' => 125000, '500g' => 240000],
                ],
            ],
            'gao' => [
                [
                    'name' => 'Gạo ST25',
                    'origin' => 'Sóc Trăng, Việt Nam',
                    'storage' => 'Để nơi khô thoáng, tránh côn trùng.',
                    'variants' => ['2kg' => 82000, '5kg' => 185000, '10kg' => 360000],
                ],
                [
                    'name' => 'Gạo lứt huyết rồng',
                    'origin' => 'Long An, Việt Nam',
                    'storage' => 'Bảo quản kín, tránh nơi ẩm thấp.',
                    'variants' => ['1kg' => 62000, '2kg' => 120000, '5kg' => 285000],
                ],
                [
                    'name' => 'Gạo nàng hoa',
                    'origin' => 'Đồng Tháp, Việt Nam',
                    'storage' => 'Để nơi khô thoáng, dùng trong 6 tháng sau khi mở bao.',
                    'variants' => ['2kg' => 72000, '5kg' => 170000, '10kg' => 330000],
                ],
                [
                    'name' => 'Gạo thơm lài',
                    'origin' => 'An Giang, Việt Nam',
                    'storage' => 'Bảo quản trong thùng kín, tránh ánh nắng.',
                    'variants' => ['2kg' => 66000, '5kg' => 155000, '10kg' => 300000],
                ],
            ],
            'hat-dinh-duong' => [
                [
                    'name' => 'Hạt điều rang muối',
                    'origin' => 'Bình Phước, Việt Nam',
                    'storage' => 'Đóng kín sau khi mở túi để giữ độ giòn.',
                    'variants' => ['250g' => 145000, '500g' => 270000, '1kg' => 520000],
                ],
                [
                    'name' => 'Hạnh nhân rang bơ',
                    'origin' => 'Nhập khẩu Mỹ, rang đóng gói tại Việt Nam',
                    'storage' => 'Bảo quản kín, tránh nhiệt độ cao.',
                    'variants' => ['250g' => 165000, '500g' => 310000, '1kg' => 600000],
                ],
                [
                    'name' => 'Hạt óc chó',
                    'origin' => 'Nhập khẩu Mỹ',
                    'storage' => 'Bảo quản nơi mát, có thể để ngăn mát sau khi mở.',
                    'variants' => ['250g' => 175000, '500g' => 330000, '1kg' => 640000],
                ],
                [
                    'name' => 'Hạt macca nứt vỏ',
                    'origin' => 'Đắk Lắk, Việt Nam',
                    'storage' => 'Để nơi khô ráo, dùng kẹp kín miệng túi.',
                    'variants' => ['250g' => 190000, '500g' => 360000, '1kg' => 700000],
                ],
            ],
        ];
    }

    private function description(array $item): string
    {
        return $item['name'].' được đóng gói theo từng khối lượng, phù hợp nhu cầu sử dụng gia đình.'
            ."\nNguồn gốc: {$item['origin']}"
            ."\nBảo quản: {$item['storage']}";
    }

    private function removeUnusedSampleProducts(array $wantedSlugs): void
    {
        $products = Product::whereNotIn('slug', $wantedSlugs)->get();

        foreach ($products as $product) {
            $hasRelatedData = DB::table('order_items')->where('product_id', $product->id)->exists()
                || DB::table('wishlists')->where('product_id', $product->id)->exists()
                || DB::table('purchase_order_items')->where('product_id', $product->id)->exists()
                || DB::table('import_receipt_items')->where('product_id', $product->id)->exists()
                || DB::table('damage_slip_items')->where('product_id', $product->id)->exists()
                || $product->reviews()->exists();

            if ($hasRelatedData) {
                continue;
            }

            $product->images()->delete();
            $product->delete();
        }
    }
}
