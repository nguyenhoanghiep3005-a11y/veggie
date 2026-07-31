<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang nha_cung_cap.
    public function up()
    {
        Schema::create('nha_cung_cap', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_nha_cung_cap');
            $table->string('ten');
            $table->string('so_dien_thoai', 50)->nullable();
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    // Xoa bang nha_cung_cap.
    public function down()
    {
        Schema::dropIfExists('nha_cung_cap');
    }
};