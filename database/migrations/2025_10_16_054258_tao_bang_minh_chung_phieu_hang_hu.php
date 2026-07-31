<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang minh_chung_phieu_hang_hu.
    public function up()
    {
        Schema::create('minh_chung_phieu_hang_hu', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_minh_chung_phieu_hang_hu');
            $table->foreignId('ma_phieu_hang_hu')
                ->constrained('phieu_hang_hu', 'ma_phieu_hang_hu')
                ->cascadeOnDelete();
            $table->string('o_dia')->default('public');
            $table->string('duong_dan');
            $table->string('ten_goc')->nullable();
            $table->string('loai_mime', 100)->nullable();
            $table->enum('loai_tep', ['hinh_anh', 'video'])->default('hinh_anh');
            $table->unsignedBigInteger('kich_thuoc')->default(0);
            $table->timestamps();
        });
    }

    // Xoa bang minh_chung_phieu_hang_hu.
    public function down()
    {
        Schema::dropIfExists('minh_chung_phieu_hang_hu');
    }
};