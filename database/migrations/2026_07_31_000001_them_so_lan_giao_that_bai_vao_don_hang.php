<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Them so lan giao that bai de phan biet lan 1 va lan 2.
    public function up()
    {
        Schema::table('don_hang', function (Blueprint $table) {
            $table->unsignedTinyInteger('so_lan_giao_that_bai')
                ->default(0)
                ->after('ly_do_giao_that_bai');
        });
    }

    // Xoa cot so lan giao that bai.
    public function down()
    {
        Schema::table('don_hang', function (Blueprint $table) {
            $table->dropColumn('so_lan_giao_that_bai');
        });
    }
};