<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang vai_tro_quyen.
    public function up()
    {
        Schema::create('vai_tro_quyen', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_vai_tro_quyen');
            $table->foreignId('ma_vai_tro')
                ->constrained('vai_tro', 'ma_vai_tro')
                ->cascadeOnDelete();
            $table->foreignId('ma_quyen')
                ->constrained('quyen', 'ma_quyen')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['ma_vai_tro', 'ma_quyen']);
        });
    }

    // Xoa bang vai_tro_quyen.
    public function down()
    {
        Schema::dropIfExists('vai_tro_quyen');
    }
};