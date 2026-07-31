<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang chi_tiet_phieu_hang_hu.
    public function up()
    {
        Schema::create('chi_tiet_phieu_hang_hu', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_chi_tiet_phieu_hang_hu');
            $table->foreignId('ma_phieu_hang_hu')
                ->constrained('phieu_hang_hu', 'ma_phieu_hang_hu')
                ->cascadeOnDelete();
            $table->foreignId('ma_san_pham')
                ->constrained('san_pham', 'ma_san_pham');
            $table->unsignedInteger('so_luong');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang chi_tiet_phieu_hang_hu.
    public function down()
    {
        Schema::dropIfExists('chi_tiet_phieu_hang_hu');
    }
};