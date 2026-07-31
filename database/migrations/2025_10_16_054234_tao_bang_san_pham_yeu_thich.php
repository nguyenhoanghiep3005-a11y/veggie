<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang san_pham_yeu_thich.
    public function up()
    {
        Schema::create('san_pham_yeu_thich', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_san_pham_yeu_thich');
            $table->foreignId('ma_nguoi_dung')
                ->constrained('nguoi_dung', 'ma_nguoi_dung')
                ->cascadeOnDelete();
            $table->foreignId('ma_san_pham')
                ->constrained('san_pham', 'ma_san_pham')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['ma_nguoi_dung', 'ma_san_pham']);
        });
    }

    // Xoa bang san_pham_yeu_thich.
    public function down()
    {
        Schema::dropIfExists('san_pham_yeu_thich');
    }
};