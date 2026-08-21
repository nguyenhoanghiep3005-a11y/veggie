<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang chi_tiet_don_hang.
    public function up()
    {
        Schema::create('chi_tiet_don_hang', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_chi_tiet_don_hang');
            $table->foreignId('ma_don_hang')
                ->constrained('don_hang', 'ma_don_hang')
                ->cascadeOnDelete();
            $table->foreignId('ma_san_pham')
                ->constrained('san_pham', 'ma_san_pham');
            $table->unsignedInteger('so_luong');
            $table->decimal('gia', 10, 2);
            $table->timestamps();
        });
    }

    // Xoa bang chi_tiet_don_hang.
    public function down()
    {
        Schema::dropIfExists('chi_tiet_don_hang');
    }
};
