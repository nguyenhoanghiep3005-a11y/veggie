<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Clients\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROUTES ADMIN
| Tất cả route dành cho trang quản trị nằm trong prefix 'admin'
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | NHÓM ROUTE DÀNH CHO ADMIN CHƯA ĐĂNG NHẬP
    | Middleware: check.auth.admin
    | - Nếu đã đăng nhập rồi → không cho vào login nữa
    |--------------------------------------------------------------------------
    */
    Route::middleware(['check.auth.admin'])->group(function () {

        // Hiển thị form đăng nhập admin
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
            ->name('admin.login');

        // Xử lý đăng nhập admin
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->name('admin.login.post');
    });

    // Đăng xuất admin
    Route::get('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');

    /*
    |--------------------------------------------------------------------------
    | NHÓM ROUTE DÀNH CHO ADMIN ĐÃ ĐĂNG NHẬP
    | Middleware: auth.custom
    | - Bảo vệ các trang chỉ dành cho admin
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth.custom'])->group(function () {

        // Dashboard quản trị (trang tổng quan)
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ NGƯỜI DÙNG (User Management)
    | Middleware: permission:manage_user
    | - Chỉ admin có quyền manage_user mới được sử dụng chức năng
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:manage_user'])->group(function () {

        // Danh sách người dùng
        Route::get('/users', [UserController::class, 'index'])
            ->name('admin.users.index');

        // Nâng cấp user thành Staff
        Route::post('/user/upgrade', [UserController::class, 'upgrade']);

        // Cập nhật trạng thái người dùng (ban/delete)
        Route::post('/user/updateStatus', [UserController::class, 'updateStatus']);
    });

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ DANH MỤC (Category)
    | Middleware: permission:manage_categories
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:manage_categories'])->group(function () {

        // Hiển thị form thêm danh mục
        Route::get('/categories/add', [CategoryController::class, 'showFormAddCate'])
            ->name('admin.categories.add');

        // Xử lý thêm danh mục
        Route::post('/categories/add', [CategoryController::class, 'addCategory'])
            ->name('admin.categories.add');

        // Danh sách danh mục
        Route::get('/categories', [CategoryController::class, 'index'])
            ->name('admin.categories.index');

        // Cập nhật danh mục (AJAX)
        Route::post('/categories/update', [CategoryController::class, 'updateCategory']);

        // Xóa danh mục
        Route::post('/categories/delete', [CategoryController::class, 'deleteCategory']);
    });

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ SẢN PHẨM (Product)
    | Middleware: permission:manage_products
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:manage_products'])->group(function () {

        // Hiển thị form thêm sản phẩm
        Route::get('/product/add', [AdminProductController::class, 'showFormAddProduct'])
            ->name('admin.product.add');

        // Xử lý thêm sản phẩm
        Route::post('/product/add', [AdminProductController::class, 'addProduct'])
            ->name('admin.product.add');

        // Danh sách sản phẩm
        Route::get('/products', [AdminProductController::class, 'index'])
            ->name('admin.products.index');

        // Cập nhật sản phẩm (AJAX)
        Route::post('/product/update', [AdminProductController::class, 'updateProduct']);

        // Xoá sản phẩm
        Route::post('/product/delete', [AdminProductController::class, 'deleteProduct']);
    });

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ ĐƠN HÀNG (Order)
    | Middleware: permission:manage_order
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:manage_order'])->group(function () {

        // Danh sách đơn hàng
        Route::get('/orders', [OrderController::class, 'index'])
            ->name('admin.order.index');

        // Admin xác nhận đơn hàng  chuyển sang trạng thái "Đang giao"
        Route::post('/order/confirm', [AdminProductController::class, 'confirmOrder']);

        // Chi tiết đơn hàng
        Route::get('order/order-detail/{id}', [OrderController::class, 'showOrderDetail'])
            ->name('admin.order-detail');

        // Hủy đơn hàng
        Route::post('/order-detail/cancel-order', [OrderController::class, 'cancelOrder']);
    });
});
