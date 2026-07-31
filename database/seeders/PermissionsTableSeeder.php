<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    // Them hoac cap nhat cac quyen quan tri mac dinh.
    public function run()
    {
        $quyens = [
            1 => 'quan_ly_nguoi_dung',
            2 => 'quan_ly_san_pham',
            3 => 'quan_ly_don_hang',
            4 => 'quan_ly_danh_muc',
        ];

        foreach ($quyens as $maQuyen => $tenQuyen) {
            DB::table('quyen')->updateOrInsert(
                ['ma_quyen' => $maQuyen],
                [
                    'ten' => $tenQuyen,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}