<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsTableSeeder extends Seeder
{
    // Gan quyen mac dinh cho admin va nhan vien.
    public function run()
    {
        $quyenAdmin = [1, 2, 3, 4];
        $quyenNhanVien = [2, 3];

        $this->ganQuyenChoVaiTro(1, $quyenAdmin);
        $this->ganQuyenChoVaiTro(2, $quyenNhanVien);
    }

    // Gan danh sach quyen cho mot vai tro.
    private function ganQuyenChoVaiTro($maVaiTro, $maQuyens)
    {
        foreach ($maQuyens as $maQuyen) {
            DB::table('vai_tro_quyen')->updateOrInsert(
                [
                    'ma_vai_tro' => $maVaiTro,
                    'ma_quyen' => $maQuyen,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}