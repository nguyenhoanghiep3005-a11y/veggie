<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang chi_tiet_phieu_nhap.
    public function up()
    {
        Schema::create('chi_tiet_phieu_nhap', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_chi_tiet_phieu_nhap');
            $table->foreignId('ma_phieu_nhap')
                ->constrained('phieu_nhap', 'ma_phieu_nhap')
                ->cascadeOnDelete();
            $table->foreignId('ma_chi_tiet_don_dat_nhap')
                ->nullable()
                ->constrained('chi_tiet_don_dat_nhap', 'ma_chi_tiet_don_dat_nhap')
                ->nullOnDelete();
            $table->foreignId('ma_san_pham')
                ->constrained('san_pham', 'ma_san_pham');
            $table->unsignedInteger('so_luong');
            $table->date('ngay_san_xuat')->nullable();
            $table->date('han_su_dung')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang chi_tiet_phieu_nhap.
    public function down()
    {
        Schema::dropIfExists('chi_tiet_phieu_nhap');
    }
};