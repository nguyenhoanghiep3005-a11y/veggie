<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    // Them hoac cap nhat tai khoan khach hang mau.
    public function run()
    {
        $nguoiDungs = [
            [
                'ten' => 'Nguyen Van A',
                'email' => 'nguyenvana@example.com',
                'so_dien_thoai' => '0123456789',
                'dia_chi' => 'Da Nang, Vietnam',
            ],
            [
                'ten' => 'Tran Thi B',
                'email' => 'tranthib@example.com',
                'so_dien_thoai' => '0987654321',
                'dia_chi' => 'Gia Lai, Vietnam',
            ],
            [
                'ten' => 'Nguyen Hoang Hiep',
                'email' => 'nguyenhoanghiep@example.com',
                'so_dien_thoai' => '0987654321',
                'dia_chi' => 'Ho Chi Minh, Vietnam',
            ],
        ];

        foreach ($nguoiDungs as $nguoiDung) {
            NguoiDung::updateOrCreate(
                ['email' => $nguoiDung['email']],
                [
                    'ten' => $nguoiDung['ten'],
                    'mat_khau' => bcrypt('123456'),
                    'so_dien_thoai' => $nguoiDung['so_dien_thoai'],
                    'dia_chi' => $nguoiDung['dia_chi'],
                    'trang_thai' => 'hoat_dong',
                    'ma_vai_tro' => 3,
                ]
            );
        }
    }
}