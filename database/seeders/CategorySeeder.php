<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Thực Phẩm Khô', 'slug' => 'thuc-pham-kho', 'description' => 'Các loại thực phẩm khô đóng gói, dễ bảo quản.', 'image' => 'uploads/categories/1782975650_6a460ca274db3.png'],
            ['name' => 'Gia vị', 'slug' => 'gia-vi', 'description' => 'Gia vị nấu ăn dùng hằng ngày.', 'image' => 'uploads/categories/1782975699_6a460cd39b937.png'],
            ['name' => 'Gạo', 'slug' => 'gao', 'description' => 'Các loại gạo trắng, gạo lứt và gạo đặc sản.', 'image' => 'uploads/categories/1782975722_6a460cea6a2c9.png'],
            ['name' => 'Hạt Dinh Dưỡng', 'slug' => 'hat-dinh-duong', 'description' => 'Hạt ăn vặt và hạt dinh dưỡng tốt cho sức khỏe.', 'image' => 'uploads/categories/1782975680_6a460cc07b6a4.png'],
        ];

        Category::whereNotIn('slug', collect($categories)->pluck('slug')->all())
            ->doesntHave('products')
            ->delete();

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
