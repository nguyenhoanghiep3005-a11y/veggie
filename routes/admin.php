<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderReturnController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
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

        // Chặn hoặc bỏ chặn tài khoản khách hàng
        Route::post('/user/updateStatus', [UserController::class, 'updateStatus'])
            ->name('admin.users.update-status');
    });

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ DANH MỤC (Category)
    | Middleware: permission:manage_categories
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:manage_categories'])->group(function () {

        // Hiển thị form thêm danh mục
        Route::get('/categories/add', [CategoryController::class, 'create'])
            ->name('admin.categories.add');

        // Xử lý thêm danh mục
        Route::post('/categories/add', [CategoryController::class, 'store'])
            ->name('admin.categories.store');

        // Danh sách danh mục
        Route::get('/categories', [CategoryController::class, 'index'])
            ->name('admin.categories.index');

        // Cập nhật danh mục (AJAX)
        Route::post('/categories/update', [CategoryController::class, 'update']);

        // Xóa danh mục
        Route::post('/categories/delete', [CategoryController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ SẢN PHẨM (Product)
    | Middleware: permission:manage_products
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:manage_products'])->group(function () {

        // Hiển thị form thêm sản phẩm
        Route::get('/product/add', [AdminProductController::class, 'create'])
            ->name('admin.product.add');

        // Xử lý thêm sản phẩm
        Route::post('/product/add', [AdminProductController::class, 'store'])
            ->name('admin.product.store');

        // Danh sách sản phẩm
        Route::get('/products', [AdminProductController::class, 'index'])
            ->name('admin.products.index');

        Route::get('/warehouses', [WarehouseController::class, 'index'])
            ->name('admin.warehouses.index');

        Route::get('/warehouses/damages', [WarehouseController::class, 'damages'])
            ->name('admin.warehouses.damages');

        Route::post('/warehouses/{stock}/adjust', [WarehouseController::class, 'adjust'])
            ->name('admin.warehouses.adjust');

        // Cập nhật sản phẩm (AJAX)
        Route::post('/product/update', [AdminProductController::class, 'update'])
            ->name('admin.product.update');

        // Xoá sản phẩm
        Route::post('/product/delete', [AdminProductController::class, 'destroy']);

        Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons.index');
        Route::post('/coupons', [CouponController::class, 'store'])->name('admin.coupons.store');
        Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('admin.coupons.update');
        Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('admin.coupons.destroy');

        Route::get('/suppliers', [SupplierController::class, 'index'])->name('admin.suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('admin.suppliers.store');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('admin.suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('admin.suppliers.destroy');

        // Phiếu đặt mua là chứng từ gốc; khi nhập hàng sẽ cộng trực tiếp vào tồn sản phẩm.
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])
            ->name('admin.purchase-orders.index');
        Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])
            ->name('admin.purchase-orders.create');
        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])
            ->name('admin.purchase-orders.store');
        Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'show'])
            ->name('admin.purchase-orders.show');
        Route::get('/purchase-orders/{id}/import', [PurchaseOrderController::class, 'showImportForm'])
            ->name('admin.purchase-orders.import.form');
        Route::post('/purchase-orders/{id}/import', [PurchaseOrderController::class, 'processImport'])
            ->name('admin.purchase-orders.import.process');
        Route::post('/purchase-orders/{id}/destroy', [PurchaseOrderController::class, 'destroy'])
            ->name('admin.purchase-orders.destroy');

        Route::get('/import-receipts', [PurchaseOrderController::class, 'importReceipts'])
            ->name('admin.import-receipts.index');
        Route::get('/import-receipts/{receipt}', [PurchaseOrderController::class, 'showImportReceipt'])
            ->name('admin.import-receipts.show');

        Route::get('/damage-slips', [PurchaseOrderController::class, 'damageSlips'])
            ->name('admin.damage-slips.index');
        Route::get('/damage-slips/{damageSlip}', [PurchaseOrderController::class, 'showDamageSlip'])
            ->name('admin.damage-slips.show');

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
            ->name('admin.orders.index');

        // Chi tiết đơn hàng
        Route::get('order/order-detail/{id}', [OrderController::class, 'showOrderDetail'])
            ->name('admin.order-detail');

        // Xác nhận đơn hàng (pending → confirmed)
        Route::post('/order/confirm', [OrderController::class, 'confirmOrder'])
            ->name('admin.order.confirm');

        // Giao hàng (confirmed → shipping)
        Route::post('/order/ship', [OrderController::class, 'shipOrder'])
            ->name('admin.order.ship');

        // Cập nhật trạng thái đơn hàng (shipping → completed)
        Route::post('/order/update-status', [OrderController::class, 'updateStatus'])
            ->name('admin.order.updateStatus');

        // Hủy đơn hàng + hoàn lại số lượng sản phẩm
        Route::post('/order/cancel', [OrderController::class, 'cancelOrder'])
            ->name('admin.order.cancel');

        // Xử lý đổi/trả hàng lỗi ngay trong chi tiết đơn hàng.
        Route::post('/order-returns/{orderReturn}/approve', [OrderReturnController::class, 'approve'])
            ->name('admin.order-returns.approve');
        Route::post('/order-returns/{orderReturn}/receive', [OrderReturnController::class, 'receive'])
            ->name('admin.order-returns.receive');
        Route::post('/order-returns/{orderReturn}/complete', [OrderReturnController::class, 'complete'])
            ->name('admin.order-returns.complete');
    });
});
