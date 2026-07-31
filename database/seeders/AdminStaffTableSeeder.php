<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use Illuminate\Database\Seeder;

class AdminStaffTableSeeder extends Seeder
{
    // Them hoac cap nhat tai khoan admin va nhan vien.
    public function run()
    {
        $nguoiDungs = [
            [
                'ten' => 'Admin',
                'email' => 'admin@example.com',
                'so_dien_thoai' => '019999999',
                'dia_chi' => 'Da Nang, Vietnam',
                'ma_vai_tro' => 1,
            ],
            [
                'ten' => 'Staff',
                'email' => 'staff@example.com',
                'so_dien_thoai' => '018889999',
                'dia_chi' => 'Da Nang, Vietnam',
                'ma_vai_tro' => 2,
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
                    'ma_vai_tro' => $nguoiDung['ma_vai_tro'],
                ]
            );
        }
    }
}