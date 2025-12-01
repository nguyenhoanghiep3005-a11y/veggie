<?php

use App\Http\Controllers\Clients\CheckoutController;
use App\Http\Controllers\Clients\AccountController;
use App\Http\Controllers\Clients\AuthController;
use App\Http\Controllers\Clients\ForgotPasswordController;
use App\Http\Controllers\Clients\HomeController;
use App\Http\Controllers\Clients\ProductController;
use App\Http\Controllers\Clients\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\CartController;
use App\Http\Controllers\Clients\ContactController;
use App\Http\Controllers\Clients\OrderController;
use App\Http\Controllers\Clients\ReviewController;
use App\Http\Controllers\Clients\SearchController;
use App\Http\Controllers\Clients\WishlistController;


Route::prefix('/')->group(function () {

    // ========================
    //  TRANG CHỦ
    // ========================
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');  // Trang chủ website
    Route::get('/about', function () {
        return view('clients.pages.about');
    })->name('about');

    Route::get('/service', function () {
        return view('clients.pages.service');
    })->name('service');

    Route::get('/team', function () {
        return view('clients.pages.team');
    })->name('team');

    Route::get('/faq', function () {
        return view('clients.pages.faq');
    })->name('faq');


    // =======================================================
    //  USER CHƯA ĐĂNG NHẬP (middleware: guest)
    //  Không cho phép vào các trang này nếu đã đăng nhập
    // =======================================================
    Route::middleware('guest')->group(function () {

        // --- Đăng ký tài khoản ---
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('post-register');

        // --- Đăng nhập ---
        Route::get('/login', [AuthController::class, 'showloginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('post-login');

        // --- Quên mật khẩu ---
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetlink'])->name('password.email');

        // --- Trang reset mật khẩu ---
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
    });

    // ========================
    //  ĐĂNG XUẤT
    // ========================
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // ========================
    //  KÍCH HOẠT TÀI KHOẢN QUA EMAIL
    // ========================
    Route::get('/activate/{token}', [AuthController::class, 'activate'])->name('activate');


    // =======================================================
    //  ROUTE CHỈ DÀNH CHO USER ĐÃ ĐĂNG NHẬP (auth.custom)
    // =======================================================
    Route::middleware(['auth.custom'])->group(function () {

        // ========================
        //  THÔNG TIN TÀI KHOẢN
        // ========================
        Route::prefix('account')->group(function () {

            // Trang tài khoản
            Route::get('/', [AccountController::class, 'index'])->name('account');

            // Cập nhật thông tin tài khoản
            Route::put('/update', [AccountController::class, 'update'])->name('account.update');

            // Đổi mật khẩu
            Route::post('/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');

            // Thêm địa chỉ giao hàng
            Route::post('/addresses', [AccountController::class, 'addAddress'])->name('account.addresses.add');

            // Cập nhật địa chỉ giao hàng
            Route::put('/addresses/{id}', [AccountController::class, 'updatePrimaryAddress'])->name('account.addresses.update');

            // Xóa địa chỉ
            Route::delete('/addresses/{id}', [AccountController::class, 'deleteAddress'])->name('account.addresses.delete');
        });

        // ========================
        //  CHECKOUT / THANH TOÁN
        // ========================
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

        // Lấy địa chỉ được chọn để fill vào form
        Route::get('/checkout/get-address', [CheckoutController::class, 'getAddress']);

        // Đặt hàng COD
        Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');

        // Thanh toán PayPal
        Route::post('/checkout/paypal', [CheckoutController::class, 'placeOrderPayPal'])->name('checkout.placeOrderPayPal');

        // Chi tiết đơn hàng của user
        Route::get('/order/{id}', [OrderController::class, 'showOrder'])->name('order.show');

        // User hủy đơn
        Route::post('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');


        // ========================
        //  ĐÁNH GIÁ SẢN PHẨM
        // ========================
        Route::post('/review', [ReviewController::class, 'createReview']);
        Route::get('/review/{product}', [ReviewController::class, 'index']);


        // ========================
        //  WISHLIST (Danh sách yêu thích)
        // ========================
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/add', [WishlistController::class, 'addToWishList']);
        Route::post('/wishlist/remove', [WishlistController::class, 'removeWishListItem']);
    });


    // =======================================================
    //  SẢN PHẨM
    // =======================================================

    // Danh sách sản phẩm
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Lọc sản phẩm bằng AJAX
    Route::get('products/filter', [ProductController::class, 'filter'])->name('products.filter');

    // Chi tiết sản phẩm
    Route::get('/products/{slug}', [ProductController::class, 'detail'])->name('product.detail');


    // =======================================================
    //  GIỎ HÀNG
    // =======================================================

    // Thêm sản phẩm vào giỏ
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

    // Xóa item khỏi mini cart
    Route::post('/cart/remove', [CartController::class, 'removeFormMiniCart'])->name('cart.remove');

    // Load mini cart AJAX
    Route::get('/mini-cart', [CartController::class, 'loadMiniCart'])->name('cart.mini');

    // Trang giỏ hàng
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.index');

    // Update số lượng sản phẩm trong cart
    Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');

    // Xóa sản phẩm ở trang giỏ (khác mini cart)
    Route::post('/cart/remove-cart', [CartController::class, 'removeCartItem'])->name('cart.remove');


    // =======================================================
    //  TÌM KIẾM
    // =======================================================
    Route::get('/search', [SearchController::class, 'index'])->name('search');


    // =======================================================
    //  LIÊN HỆ
    // =======================================================
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'sendContact'])->name('contact.send');

});  // END PREFIX /

/*
|--------------------------------------------------------------------------
| Nhúng route admin (tách file riêng)
|--------------------------------------------------------------------------
*/
require __DIR__.'/admin.php';
