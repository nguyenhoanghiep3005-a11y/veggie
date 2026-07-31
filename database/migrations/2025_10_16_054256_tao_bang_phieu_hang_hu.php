<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang phieu_hang_hu.
    public function up()
    {
        Schema::create('phieu_hang_hu', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_phieu_hang_hu');
            $table->string('so_phieu', 50)->unique();
            $table->foreignId('ma_don_dat_nhap')
                ->nullable()
                ->constrained('don_dat_nhap', 'ma_don_dat_nhap')
                ->nullOnDelete();
            $table->foreignId('ma_phieu_nhap')
                ->nullable()
                ->constrained('phieu_nhap', 'ma_phieu_nhap')
                ->nullOnDelete();
            $table->foreignId('ma_don_hang')
                ->nullable()
                ->constrained('don_hang', 'ma_don_hang')
                ->nullOnDelete();
            $table->foreignId('ma_nha_cung_cap')
                ->nullable()
                ->constrained('nha_cung_cap', 'ma_nha_cung_cap')
                ->nullOnDelete();
            $table->text('ly_do');
            $table->timestamp('xay_ra_luc')->nullable();
            $table->timestamps();
            $table->index('xay_ra_luc');
        });
    }

    // Xoa bang phieu_hang_hu.
    public function down()
    {
        Schema::dropIfExists('phieu_hang_hu');
    }
};