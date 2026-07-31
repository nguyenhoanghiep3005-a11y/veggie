<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang hang hu kho va bang minh chung.
    public function up()
    {
        Schema::create('hang_hu_kho', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_hang_hu_kho');
            $table->foreignId('ma_lo_hang_kho')
                ->nullable()
                ->constrained('lo_hang_kho', 'ma_lo_hang_kho')
                ->nullOnDelete();
            $table->foreignId('ma_san_pham')
                ->nullable()
                ->constrained('san_pham', 'ma_san_pham')
                ->nullOnDelete();
            $table->string('ten_san_pham');
            $table->unsignedInteger('so_luong');
            $table->text('ly_do');
            $table->timestamp('xay_ra_luc')->nullable();
            $table->timestamps();
            $table->index(['ma_san_pham', 'xay_ra_luc']);
        });

        Schema::create('minh_chung_hang_hu_kho', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_minh_chung_hang_hu_kho');
            $table->foreignId('ma_hang_hu_kho')
                ->constrained('hang_hu_kho', 'ma_hang_hu_kho')
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

    // Xoa bang minh chung va hang hu kho.
    public function down()
    {
        Schema::dropIfExists('minh_chung_hang_hu_kho');
        Schema::dropIfExists('hang_hu_kho');
    }
};