<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang quyen.
    public function up()
    {
        Schema::create('quyen', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ma_quyen');
            $table->string('ten')->unique();
            $table->timestamps();
        });
    }

    // Xoa bang quyen.
    public function down()
    {
        Schema::dropIfExists('quyen');
    }
};