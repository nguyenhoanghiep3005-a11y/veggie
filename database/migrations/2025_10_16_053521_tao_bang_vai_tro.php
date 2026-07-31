<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang vai_tro.
    public function up()
    {
        Schema::create('vai_tro', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_vai_tro');
            $table->string('ten')->unique();
            $table->timestamps();
        });
    }

    // Xoa bang vai_tro.
    public function down()
    {
        Schema::dropIfExists('vai_tro');
    }
};