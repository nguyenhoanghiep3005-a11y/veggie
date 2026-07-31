<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // Dang ky cac dich vu cua ung dung.
    public function register()
    {
    }

    // Khoi tao cau hinh chung cua ung dung.
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}