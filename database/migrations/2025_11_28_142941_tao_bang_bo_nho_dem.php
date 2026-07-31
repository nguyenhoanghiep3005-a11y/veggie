<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tao hai bang bo nho dem mac dinh cua Laravel.
    public function up()
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    // Xoa hai bang bo nho dem.
    public function down()
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};