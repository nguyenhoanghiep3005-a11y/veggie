<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang danh_gia.
    public function up()
    {
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_danh_gia');
            $table->foreignId('ma_nguoi_dung')
                ->constrained('nguoi_dung', 'ma_nguoi_dung')
                ->cascadeOnDelete();
            $table->foreignId('ma_san_pham')
                ->constrained('san_pham', 'ma_san_pham')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('so_sao');
            $table->string('binh_luan')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang danh_gia.
    public function down()
    {
        Schema::dropIfExists('danh_gia');
    }
};