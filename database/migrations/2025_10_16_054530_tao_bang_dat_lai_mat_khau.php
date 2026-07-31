<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao bang luu ma dat lai mat khau cua Laravel.
    public function up()
    {
        Schema::create('dat_lai_mat_khau', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    // Xoa bang dat lai mat khau.
    public function down()
    {
        Schema::dropIfExists('dat_lai_mat_khau');
    }
};