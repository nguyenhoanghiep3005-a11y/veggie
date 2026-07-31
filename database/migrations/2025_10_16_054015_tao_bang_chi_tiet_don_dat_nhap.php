<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang chi_tiet_don_dat_nhap.
    public function up()
    {
        Schema::create('chi_tiet_don_dat_nhap', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_chi_tiet_don_dat_nhap');
            $table->foreignId('ma_don_dat_nhap')
                ->constrained('don_dat_nhap', 'ma_don_dat_nhap')
                ->cascadeOnDelete();
            $table->foreignId('ma_san_pham')
                ->constrained('san_pham', 'ma_san_pham');
            $table->unsignedInteger('so_luong_dat');
            $table->unsignedInteger('so_luong_nhan')->default(0);
            $table->unsignedInteger('so_luong_tu_choi')->default(0);
            $table->unsignedInteger('so_luong_da_nhap')->default(0);
            $table->date('ngay_san_xuat')->nullable();
            $table->date('han_su_dung')->nullable();
            $table->timestamps();
            $table->unique(['ma_don_dat_nhap', 'ma_san_pham']);
        });
    }

    // Xoa bang chi_tiet_don_dat_nhap.
    public function down()
    {
        Schema::dropIfExists('chi_tiet_don_dat_nhap');
    }
};