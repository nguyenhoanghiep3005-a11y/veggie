<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang dia_chi_giao_hang.
    public function up()
    {
        Schema::create('dia_chi_giao_hang', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_dia_chi_giao_hang');
            $table->foreignId('ma_nguoi_dung')
                ->nullable()
                ->constrained('nguoi_dung', 'ma_nguoi_dung')
                ->cascadeOnDelete();
            $table->string('ho_ten');
            $table->string('so_dien_thoai');
            $table->string('dia_chi');
            $table->string('tinh_thanh');
            $table->unsignedBigInteger('ma_tinh')->nullable();
            $table->unsignedBigInteger('ma_huyen')->nullable();
            $table->string('ma_xa', 50)->nullable();
            $table->boolean('mac_dinh')->default(false);
            $table->timestamps();
        });
    }

    // Xoa bang dia_chi_giao_hang.
    public function down()
    {
        Schema::dropIfExists('dia_chi_giao_hang');
    }
};