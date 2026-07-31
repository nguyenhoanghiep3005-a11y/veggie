<?php

namespace Database\Seeders;

use App\Models\DanhMuc;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    // Them hoac cap nhat danh muc mau.
    public function run()
    {
        $danhMucs = [
            [
                'ten' => 'Thuc Pham Kho',
                'duong_dan' => 'thuc-pham-kho',
                'mo_ta' => 'Cac loai thuc pham kho dong goi, de bao quan.',
                'hinh_anh' => 'uploads/categories/1782975650_6a460ca274db3.png',
            ],
            [
                'ten' => 'Gia vi',
                'duong_dan' => 'gia-vi',
                'mo_ta' => 'Gia vi nau an dung hang ngay.',
                'hinh_anh' => 'uploads/categories/1782975699_6a460cd39b937.png',
            ],
            [
                'ten' => 'Gao',
                'duong_dan' => 'gao',
                'mo_ta' => 'Cac loai gao trang, gao lut va gao dac san.',
                'hinh_anh' => 'uploads/categories/1782975722_6a460cea6a2c9.png',
            ],
            [
                'ten' => 'Hat Dinh Duong',
                'duong_dan' => 'hat-dinh-duong',
                'mo_ta' => 'Hat an vat va hat dinh duong tot cho suc khoe.',
                'hinh_anh' => 'uploads/categories/1782975680_6a460cc07b6a4.png',
            ],
        ];

        foreach ($danhMucs as $danhMuc) {
            DanhMuc::updateOrCreate(
                ['duong_dan' => $danhMuc['duong_dan']],
                $danhMuc
            );
        }
    }
}