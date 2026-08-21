<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    // Them hoac cap nhat tai khoan quan tri mac dinh.
    public function run()
    {
        NguoiDung::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'ten' => 'Admin',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '019999999',
                'dia_chi' => 'Da Nang, Vietnam',
                'trang_thai' => 'hoat_dong',
                'ma_vai_tro' => 1,
            ]
        );
    }
}
