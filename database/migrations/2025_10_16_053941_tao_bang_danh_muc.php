<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang danh_muc.
    public function up()
    {
        Schema::create('danh_muc', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_danh_muc');
            $table->string('ten')->unique();
            $table->string('duong_dan')->unique();
            $table->text('mo_ta')->nullable();
            $table->string('hinh_anh')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang danh_muc.
    public function down()
    {
        Schema::dropIfExists('danh_muc');
    }
};