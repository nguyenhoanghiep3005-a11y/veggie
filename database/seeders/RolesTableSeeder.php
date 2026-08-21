<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    // Them hoac cap nhat cac vai tro mac dinh.
    public function run()
    {
        $vaiTros = [
            1 => 'quan_tri',
            3 => 'khach_hang',
        ];

        foreach ($vaiTros as $maVaiTro => $tenVaiTro) {
            DB::table('vai_tro')->updateOrInsert(
                ['ma_vai_tro' => $maVaiTro],
                [
                    'ten' => $tenVaiTro,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
