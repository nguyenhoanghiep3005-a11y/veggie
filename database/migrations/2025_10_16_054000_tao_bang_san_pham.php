<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang san_pham.
    public function up()
    {
        Schema::create('san_pham', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_san_pham');
            $table->string('ten');
            $table->string('duong_dan')->unique();
            $table->foreignId('ma_danh_muc')
                ->constrained('danh_muc', 'ma_danh_muc');
            $table->text('mo_ta')->nullable();
            $table->decimal('gia', 10, 2);
            $table->unsignedInteger('ton_kho')->default(0);
            $table->enum('trang_thai', ['con_hang', 'het_hang'])
                ->default('het_hang');
            $table->string('don_vi')->nullable();
            $table->decimal('danh_gia_trung_binh', 3, 2)->default(0);
            $table->timestamps();
            $table->index(['trang_thai', 'ton_kho']);
        });
    }

    // Xoa bang san_pham.
    public function down()
    {
        Schema::dropIfExists('san_pham');
    }
};