<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang hinh_anh_san_pham.
    public function up()
    {
        Schema::create('hinh_anh_san_pham', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_hinh_anh_san_pham');
            $table->foreignId('ma_san_pham')
                ->constrained('san_pham', 'ma_san_pham')
                ->cascadeOnDelete();
            $table->string('hinh_anh')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang hinh_anh_san_pham.
    public function down()
    {
        Schema::dropIfExists('hinh_anh_san_pham');
    }
};