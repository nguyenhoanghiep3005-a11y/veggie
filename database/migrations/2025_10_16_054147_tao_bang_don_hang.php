<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang don_hang.
    public function up()
    {
        Schema::create('don_hang', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_don_hang');
            $table->foreignId('ma_nguoi_dung')
                ->nullable()
                ->constrained('nguoi_dung', 'ma_nguoi_dung')
                ->nullOnDelete();
            $table->decimal('tong_tien', 12, 2);
            $table->decimal('tam_tinh', 12, 2)->default(0);
            $table->decimal('phi_van_chuyen', 12, 2)->default(0);
            $table->decimal('so_tien_giam', 12, 2)->default(0);
            $table->string('ma_giam_gia', 50)->nullable();
            $table->foreignId('ma_phieu_giam_gia')
                ->nullable()
                ->constrained('phieu_giam_gia', 'ma_phieu_giam_gia')
                ->nullOnDelete();
            $table->enum('trang_thai', [
                'cho_xac_nhan',
                'da_xac_nhan',
                'dang_giao',
                'hoan_thanh',
                'giao_that_bai',
                'dang_hoan_hang',
                'da_hoan_ve_kho',
                'da_huy',
            ])->default('cho_xac_nhan');
            $table->enum('nguoi_huy', ['khach_hang', 'quan_tri'])->nullable();
            $table->text('ly_do_huy')->nullable();
            $table->text('ly_do_giao_that_bai')->nullable();
            $table->timestamp('giao_that_bai_luc')->nullable();
            $table->timestamp('hoan_ve_cua_hang_luc')->nullable();
            $table->timestamp('cua_hang_nhan_lai_luc')->nullable();
            $table->enum('tinh_trang_hang_hoan', ['nguyen_ven', 'hu_hong'])->nullable();
            $table->text('ly_do_hang_hoan_hu')->nullable();
            $table->boolean('da_hoan_ton_kho')->default(false);
            $table->timestamp('hoan_ton_kho_luc')->nullable();
            $table->timestamp('hoan_tat_luc')->nullable();
            $table->foreignId('ma_dia_chi_giao_hang')
                ->nullable()
                ->constrained('dia_chi_giao_hang', 'ma_dia_chi_giao_hang')
                ->nullOnDelete();
            $table->json('du_lieu_dia_chi_giao_hang')->nullable();
            $table->timestamps();
            $table->index(['trang_thai', 'created_at']);
        });
    }

    // Xoa bang don_hang.
    public function down()
    {
        Schema::dropIfExists('don_hang');
    }
};