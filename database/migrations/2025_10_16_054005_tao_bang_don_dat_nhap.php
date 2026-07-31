<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang don_dat_nhap.
    public function up()
    {
        Schema::create('don_dat_nhap', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_don_dat_nhap');
            $table->string('so_don', 50)->unique();
            $table->foreignId('ma_nha_cung_cap')
                ->nullable()
                ->constrained('nha_cung_cap', 'ma_nha_cung_cap')
                ->nullOnDelete();
            $table->enum('trang_thai', ['cho_nhap_hang', 'da_nhap_hang'])
                ->default('cho_nhap_hang');
            $table->text('ghi_chu')->nullable();
            $table->date('ngay_dat')->nullable();
            $table->timestamp('nhan_hang_luc')->nullable();
            $table->text('mo_ta_hang_loi')->nullable();
            $table->timestamp('bao_nha_cung_cap_luc')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang don_dat_nhap.
    public function down()
    {
        Schema::dropIfExists('don_dat_nhap');
    }
};