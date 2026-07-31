<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang lo_hang_kho.
    public function up()
    {
        Schema::create('lo_hang_kho', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_lo_hang_kho');
            $table->foreignId('ma_chi_tiet_phieu_nhap')
                ->unique()
                ->constrained('chi_tiet_phieu_nhap', 'ma_chi_tiet_phieu_nhap')
                ->cascadeOnDelete();
            $table->foreignId('ma_phieu_nhap')
                ->constrained('phieu_nhap', 'ma_phieu_nhap')
                ->cascadeOnDelete();
            $table->foreignId('ma_san_pham')
                ->constrained('san_pham', 'ma_san_pham');
            $table->foreignId('ma_nha_cung_cap')
                ->nullable()
                ->constrained('nha_cung_cap', 'ma_nha_cung_cap')
                ->nullOnDelete();
            $table->unsignedInteger('so_luong_nhap');
            $table->unsignedInteger('so_luong_con')->default(0);
            $table->decimal('gia_khuyen_mai', 10, 2)->nullable();
            $table->date('ngay_san_xuat')->nullable();
            $table->date('han_su_dung')->nullable();
            $table->timestamps();
            $table->index(['ma_san_pham', 'so_luong_con', 'han_su_dung']);
        });
    }

    // Xoa bang lo_hang_kho.
    public function down()
    {
        Schema::dropIfExists('lo_hang_kho');
    }
};