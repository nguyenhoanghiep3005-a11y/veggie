<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category' => 'thuc-pham-kho',
                'slug' => 'nam-huong-kho',
                'name' => 'Nấm hương khô 100g',
                'price' => 95000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 40, 'expires_in_days' => 240, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                    ['quantity' => 12, 'expires_in_days' => 45, 'adjusted_price' => 79000, 'note' => 'Dữ liệu mẫu - lô khuyến mãi'],
                ],
            ],
            [
                'category' => 'thuc-pham-kho',
                'slug' => 'moc-nhi-kho',
                'name' => 'Mộc nhĩ khô 100g',
                'price' => 68000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 35, 'expires_in_days' => 220, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'thuc-pham-kho',
                'slug' => 'mien-dong-500g',
                'name' => 'Miến dong 500g',
                'price' => 52000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 50, 'expires_in_days' => 300, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'thuc-pham-kho',
                'slug' => 'banh-da-nem-gao-500g',
                'name' => 'Bánh đa nem gạo 500g',
                'price' => 45000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 45, 'expires_in_days' => 180, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'gia-vi',
                'slug' => 'tieu-den-xay',
                'name' => 'Tiêu đen xay 100g',
                'price' => 52000,
                'unit' => 'hũ',
                'inventories' => [
                    ['quantity' => 45, 'expires_in_days' => 365, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                    ['quantity' => 10, 'expires_in_days' => 55, 'adjusted_price' => 45000, 'note' => 'Dữ liệu mẫu - lô khuyến mãi'],
                ],
            ],
            [
                'category' => 'gia-vi',
                'slug' => 'bot-nghe-nguyen-chat',
                'name' => 'Bột nghệ nguyên chất 100g',
                'price' => 60000,
                'unit' => 'hũ',
                'inventories' => [
                    ['quantity' => 30, 'expires_in_days' => 365, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'gia-vi',
                'slug' => 'ot-bot-han-quoc-100g',
                'name' => 'Ớt bột Hàn Quốc 100g',
                'price' => 49000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 35, 'expires_in_days' => 270, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'gia-vi',
                'slug' => 'que-thanh-100g',
                'name' => 'Quế thanh 100g',
                'price' => 55000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 25, 'expires_in_days' => 365, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'gao',
                'slug' => 'gao-st25',
                'name' => 'Gạo ST25 5kg',
                'price' => 185000,
                'unit' => 'túi',
                'inventories' => [
                    ['quantity' => 40, 'expires_in_days' => 180, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                    ['quantity' => 8, 'expires_in_days' => 40, 'adjusted_price' => 169000, 'note' => 'Dữ liệu mẫu - lô khuyến mãi'],
                ],
            ],
            [
                'category' => 'gao',
                'slug' => 'gao-lut-huyet-rong',
                'name' => 'Gạo lứt huyết rồng 2kg',
                'price' => 120000,
                'unit' => 'túi',
                'inventories' => [
                    ['quantity' => 32, 'expires_in_days' => 180, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'gao',
                'slug' => 'gao-nang-hoa-5kg',
                'name' => 'Gạo nàng hoa 5kg',
                'price' => 170000,
                'unit' => 'túi',
                'inventories' => [
                    ['quantity' => 36, 'expires_in_days' => 180, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'gao',
                'slug' => 'gao-thom-lai-5kg',
                'name' => 'Gạo thơm lài 5kg',
                'price' => 155000,
                'unit' => 'túi',
                'inventories' => [
                    ['quantity' => 34, 'expires_in_days' => 180, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'hat-dinh-duong',
                'slug' => 'hat-dieu-rang-muoi',
                'name' => 'Hạt điều rang muối 250g',
                'price' => 145000,
                'unit' => 'hộp',
                'inventories' => [
                    ['quantity' => 30, 'expires_in_days' => 210, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                    ['quantity' => 10, 'expires_in_days' => 120, 'adjusted_price' => 129000, 'note' => 'Dữ liệu mẫu - lô khuyến mãi'],
                ],
            ],
            [
                'category' => 'hat-dinh-duong',
                'slug' => 'hanh-nhan-rang-bo',
                'name' => 'Hạnh nhân rang bơ 250g',
                'price' => 165000,
                'unit' => 'hộp',
                'inventories' => [
                    ['quantity' => 26, 'expires_in_days' => 210, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'hat-dinh-duong',
                'slug' => 'hat-oc-cho-250g',
                'name' => 'Hạt óc chó 250g',
                'price' => 175000,
                'unit' => 'hộp',
                'inventories' => [
                    ['quantity' => 24, 'expires_in_days' => 210, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                    ['quantity' => 8, 'expires_in_days' => 90, 'adjusted_price' => 155000, 'note' => 'Dữ liệu mẫu - lô khuyến mãi'],
                ],
            ],
            [
                'category' => 'hat-dinh-duong',
                'slug' => 'hat-macca-nut-vo-250g',
                'name' => 'Hạt macca nứt vỏ 250g',
                'price' => 190000,
                'unit' => 'hộp',
                'inventories' => [
                    ['quantity' => 20, 'expires_in_days' => 210, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'kho-ca',
                'slug' => 'kho-ca-loc-500g',
                'name' => 'Khô cá lóc 500g',
                'price' => 180000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 22, 'expires_in_days' => 150, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                    ['quantity' => 6, 'expires_in_days' => 45, 'adjusted_price' => 159000, 'note' => 'Dữ liệu mẫu - lô khuyến mãi'],
                ],
            ],
            [
                'category' => 'kho-ca',
                'slug' => 'kho-ca-sac-500g',
                'name' => 'Khô cá sặc 500g',
                'price' => 165000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 18, 'expires_in_days' => 150, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'kho-ca',
                'slug' => 'kho-ca-dua-500g',
                'name' => 'Khô cá dứa 500g',
                'price' => 220000,
                'unit' => 'gói',
                'inventories' => [
                    ['quantity' => 16, 'expires_in_days' => 150, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                ],
            ],
            [
                'category' => 'kho-ca',
                'slug' => 'kho-ca-com-rim-250g',
                'name' => 'Khô cá cơm rim 250g',
                'price' => 85000,
                'unit' => 'hộp',
                'inventories' => [
                    ['quantity' => 30, 'expires_in_days' => 120, 'adjusted_price' => null, 'note' => 'Dữ liệu mẫu - lô thường'],
                    ['quantity' => 10, 'expires_in_days' => 75, 'adjusted_price' => 75000, 'note' => 'Dữ liệu mẫu - lô khuyến mãi'],
                ],
            ],
        ];

        foreach ($products as $item) {
            $category = Category::where('slug', $item['category'])->first();

            if (!$category) {
                continue;
            }

            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'category_id' => $category->id,
                    'description' => $item['name'] . ' phù hợp bảo quản khô, dùng hằng ngày.',
                    'price' => $item['price'],
                    'status' => 'int_stock',
                    'unit' => $item['unit'],
                    'average_rating' => 5,
                ]
            );

            Inventory::where('product_id', $product->id)
                ->where('note', 'like', 'Dữ liệu mẫu%')
                ->delete();

            foreach ($item['inventories'] as $inventory) {
                $expiredAt = now()->addDays($inventory['expires_in_days'])->toDateString();

                Inventory::create([
                    'product_id' => $product->id,
                    'quantity_imported' => $inventory['quantity'],
                    'quantity_remaining' => $inventory['quantity'],
                    'quantity_damaged' => 0,
                    'damaged_item_numbers' => null,
                    'imported_at' => now()->subDays(7)->toDateString(),
                    'expired_at' => $expiredAt,
                    'condition' => Inventory::checkCondition('fresh', $inventory['quantity'], $expiredAt),
                    'adjusted_price' => $inventory['adjusted_price'],
                    'note' => $inventory['note'],
                ]);
            }
        }
    }
}