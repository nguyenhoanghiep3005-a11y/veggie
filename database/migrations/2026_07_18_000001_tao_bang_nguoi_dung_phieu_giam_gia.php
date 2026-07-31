<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang nguoi_dung_phieu_giam_gia.
    public function up()
    {
        Schema::create('nguoi_dung_phieu_giam_gia', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_nguoi_dung_phieu_giam_gia');
            $table->foreignId('ma_nguoi_dung')
                ->constrained('nguoi_dung', 'ma_nguoi_dung')
                ->cascadeOnDelete();
            $table->foreignId('ma_phieu_giam_gia')
                ->constrained('phieu_giam_gia', 'ma_phieu_giam_gia')
                ->cascadeOnDelete();
            $table->timestamp('ngay_nhan')->useCurrent();
            $table->timestamp('ngay_su_dung')->nullable();
            $table->timestamps();
            $table->unique(['ma_nguoi_dung', 'ma_phieu_giam_gia']);
        });
    }

    // Xoa bang nguoi_dung_phieu_giam_gia.
    public function down()
    {
        Schema::dropIfExists('nguoi_dung_phieu_giam_gia');
    }
};