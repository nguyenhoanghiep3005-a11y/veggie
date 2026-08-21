<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsTableSeeder extends Seeder
{
    // Gan toan bo quyen quan tri cho vai tro quan tri.
    public function run()
    {
        $quyenAdmin = [1, 2, 3, 4];

        $this->ganQuyenChoVaiTro(1, $quyenAdmin);
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
