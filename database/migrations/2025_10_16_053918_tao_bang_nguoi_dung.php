<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang nguoi_dung.
    public function up()
    {
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_nguoi_dung');
            $table->string('ten');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->enum('trang_thai', [
                'cho_kich_hoat',
                'hoat_dong',
                'bi_khoa',
                'da_xoa',
            ])->default('cho_kich_hoat');
            $table->string('so_dien_thoai')->nullable();
            $table->text('dia_chi')->nullable();
            $table->foreignId('ma_vai_tro')
                ->constrained('vai_tro', 'ma_vai_tro');
            $table->string('ma_kich_hoat')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang nguoi_dung.
    public function down()
    {
        Schema::dropIfExists('nguoi_dung');
    }
};