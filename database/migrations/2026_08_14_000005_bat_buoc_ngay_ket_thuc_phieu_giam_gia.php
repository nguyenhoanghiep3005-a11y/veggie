<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('phieu_giam_gia')
            ->whereNull('het_han_luc')
            ->update(['het_han_luc' => DB::raw('DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 30 DAY)')]);

        Schema::table('phieu_giam_gia', function (Blueprint $table) {
            $table->dateTime('het_han_luc')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('phieu_giam_gia', function (Blueprint $table) {
            $table->dateTime('het_han_luc')->nullable()->change();
        });
    }
};
