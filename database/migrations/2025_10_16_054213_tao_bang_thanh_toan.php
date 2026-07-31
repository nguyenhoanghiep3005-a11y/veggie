<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang thanh_toan.
    public function up()
    {
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_thanh_toan');
            $table->foreignId('ma_don_hang')
                ->unique()
                ->constrained('don_hang', 'ma_don_hang')
                ->cascadeOnDelete();
            $table->enum('phuong_thuc', ['tien_mat', 'paypal']);
            $table->string('ma_giao_dich')->nullable();
            $table->enum('trang_thai', [
                'chua_thanh_toan',
                'da_thanh_toan',
                'da_hoan_tien',
            ])->default('chua_thanh_toan');
            $table->timestamp('thanh_toan_luc')->nullable();
            $table->decimal('so_tien', 12, 2);
            $table->timestamps();
        });
    }

    // Xoa bang thanh_toan.
    public function down()
    {
        Schema::dropIfExists('thanh_toan');
    }
};