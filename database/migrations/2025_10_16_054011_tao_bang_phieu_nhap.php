<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang phieu_nhap.
    public function up()
    {
        Schema::create('phieu_nhap', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_phieu_nhap');
            $table->string('so_phieu', 50)->unique();
            $table->foreignId('ma_don_dat_nhap')
                ->nullable()
                ->constrained('don_dat_nhap', 'ma_don_dat_nhap')
                ->nullOnDelete();
            $table->foreignId('ma_nha_cung_cap')
                ->nullable()
                ->constrained('nha_cung_cap', 'ma_nha_cung_cap')
                ->nullOnDelete();
            $table->timestamp('nhan_hang_luc')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang phieu_nhap.
    public function down()
    {
        Schema::dropIfExists('phieu_nhap');
    }
};