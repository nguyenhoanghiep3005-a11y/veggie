<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang yeu_cau_doi_tra.
    public function up()
    {
        Schema::create('yeu_cau_doi_tra', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_yeu_cau_doi_tra');
            $table->foreignId('ma_don_hang')
                ->unique()
                ->constrained('don_hang', 'ma_don_hang')
                ->cascadeOnDelete();
            $table->enum('loai', ['hang_loi'])->default('hang_loi');
            $table->text('mo_ta');
            $table->json('san_pham');
            $table->json('minh_chung')->nullable();
            $table->enum('trang_thai', [
                'cho_duyet',
                'da_duyet',
                'da_nhan_hang',
                'hoan_tat',
            ])->default('cho_duyet');
            $table->timestamp('yeu_cau_luc')->nullable();
            $table->timestamp('duyet_luc')->nullable();
            $table->timestamp('nhan_hang_luc')->nullable();
            $table->timestamps();
            $table->index(['loai', 'yeu_cau_luc']);
        });
    }

    // Xoa bang yeu_cau_doi_tra.
    public function down()
    {
        Schema::dropIfExists('yeu_cau_doi_tra');
    }
};