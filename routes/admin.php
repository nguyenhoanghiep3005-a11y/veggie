<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Clients\ProductController;
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
    // Quản lý danh mục
    Route::middleware(['permission:manage_categories'])->group(function () {
        Route::get('/categories/add', [CategoryController::class, 'showFormAddCate'])->name('admin.categories.add');
        Route::post('/categories/add', [CategoryController::class, 'addCategory'])->name('admin.categories.add');

        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories/update', [CategoryController::class, 'updateCategory']);
        Route::post('/categories/delete', [CategoryController::class, 'deleteCategory']);
    });
    //quan ly san pham
    Route::middleware(['permission:manage_products'])->group(function () {
        Route::get('/product/add', [AdminProductController::class, 'showFormAddProduct'])->name('admin.product.add');
        Route::post('/product/add', [AdminProductController::class, 'addProduct'])->name('admin.product.add');

        Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::post('/product/update', [AdminProductController::class, 'updateProduct']);
        Route::post('/product/delete', [AdminProductController::class, 'deleteProduct']);
    });
});
