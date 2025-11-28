<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {

    // CHO NGƯỜI CHƯA ĐĂNG NHẬP
    Route::middleware(['check.auth.admin'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    });
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // CHO NGƯỜI ĐÃ ĐĂNG NHẬP
    Route::middleware(['auth.custom'])->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.pages.dashboard');
        })->name('admin.dashboard');
    });
    // CHỈ VÀO ĐƯỢC KHI CÓ QUYỀN
    Route::middleware(['permission:manage_user'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/user/upgrade', [UserController::class, 'upgrade']);
        Route::post('/user/updateStatus', [UserController::class, 'updateStatus']);
    });
       Route::middleware(['permission:manage_categories'])->group(function () {
        Route::get('/categories/add', [CategoryController::class, 'showFormAddCate'])->name('admin.categories.add');
        Route::post('/categories/add', [CategoryController::class, 'addCategory'])->name('admin.categories.add');
    
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories/update', [CategoryController::class, 'updateCategory']);
    });
});
