<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang phieu_giam_gia.
    public function up()
    {
        Schema::create('phieu_giam_gia', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_phieu_giam_gia');
            $table->string('ma_giam_gia', 50)->unique();
            $table->decimal('phan_tram_giam', 5, 2);
            $table->decimal('gia_tri_don_toi_thieu', 12, 2)->default(0);
            $table->decimal('so_tien_giam_toi_da', 12, 2)->nullable();
            $table->dateTime('het_han_luc');
            $table->unsignedInteger('gioi_han_su_dung')->nullable();
            $table->unsignedInteger('so_lan_da_dung')->default(0);
            $table->enum('loai_ap_dung', ['tat_ca', 'khach_hang'])
                ->default('tat_ca');
            $table->boolean('dang_hoat_dong')->default(true);
            $table->timestamps();
            $table->index(['dang_hoat_dong', 'het_han_luc']);
        });
    }

    // Xoa bang phieu_giam_gia.
    public function down()
    {
        Schema::dropIfExists('phieu_giam_gia');
    }
};
