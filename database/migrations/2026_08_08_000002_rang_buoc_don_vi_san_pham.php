<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Chi cho phep don vi san pham la gram hoac kilogram.
    public function up()
    {
        DB::table('san_pham')
            ->whereRaw("LOWER(TRIM(don_vi)) LIKE '%kg%'")
            ->update(['don_vi' => 'kg']);

        DB::table('san_pham')
            ->where(function ($query) {
                $query->whereNull('don_vi')
                    ->orWhereNotIn('don_vi', ['g', 'kg']);
            })
            ->update(['don_vi' => 'g']);

        DB::statement("ALTER TABLE san_pham MODIFY don_vi ENUM('g','kg') NOT NULL DEFAULT 'g'");
    }

    // Tra cot don vi ve kieu chuoi nhu ban dau.
    public function down()
    {
        DB::statement("ALTER TABLE san_pham MODIFY don_vi VARCHAR(191) NULL");
    }
};