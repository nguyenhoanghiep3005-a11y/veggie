<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $maVaiTroKhachHang = DB::table('vai_tro')
            ->where('ten', 'khach_hang')
            ->value('ma_vai_tro');

        if (! $maVaiTroKhachHang) {
            $maVaiTroKhachHang = 3;
        }

        $maVaiTroStaffs = DB::table('vai_tro')
            ->where('ten', 'staff')
            ->orWhere('ma_vai_tro', 2)
            ->pluck('ma_vai_tro');

        if ($maVaiTroStaffs->isEmpty()) {
            return;
        }

        DB::table('nguoi_dung')
            ->whereIn('ma_vai_tro', $maVaiTroStaffs)
            ->update([
                'ma_vai_tro' => $maVaiTroKhachHang,
                'updated_at' => now(),
            ]);

        DB::table('vai_tro_quyen')
            ->whereIn('ma_vai_tro', $maVaiTroStaffs)
            ->delete();

        DB::table('vai_tro')
            ->whereIn('ma_vai_tro', $maVaiTroStaffs)
            ->delete();
    }

    public function down()
    {
        DB::table('vai_tro')->updateOrInsert(
            ['ma_vai_tro' => 2],
            [
                'ten' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
};
