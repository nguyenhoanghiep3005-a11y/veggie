<?php

use App\Http\Controllers\Clients\DanhGiaController;
use App\Http\Controllers\Clients\DatLaiMatKhauController;
use App\Http\Controllers\Clients\DiaChiGhnController;
use App\Http\Controllers\Clients\DonHangController;
use App\Http\Controllers\Clients\GioHangController;
use App\Http\Controllers\Clients\PhieuGiamGiaController;
use App\Http\Controllers\Clients\QuenMatKhauController;
use App\Http\Controllers\Clients\SanPhamController;
use App\Http\Controllers\Clients\TaiKhoanController;
use App\Http\Controllers\Clients\ThanhToanController;
use App\Http\Controllers\Clients\TimKiemController;
use App\Http\Controllers\Clients\TrangChuController;
use App\Http\Controllers\Clients\XacThucController;
use App\Http\Controllers\Clients\YeuThichController;
use Illuminate\Support\Facades\Route;

// Trang chủ và các trang công khai.
Route::get('/', [TrangChuController::class, 'hienThiTrangChu'])->name('trang-chu');
Route::get('/phieu-giam-gia', [PhieuGiamGiaController::class, 'hienThiDanhSachPhieuGiamGia'])
    ->name('phieu-giam-gia.danh-sach');
Route::view('/lien-he', 'clients.pages.lien-he')->name('lien-he');

// Đăng ký, đăng nhập và đặt lại mật khẩu.
Route::middleware('guest')->group(function () {
    Route::get('/dang-ky', [XacThucController::class, 'hienThiFormDangKy'])
        ->name('dang-ky.hien-thi');
    Route::post('/dang-ky', [XacThucController::class, 'dangKy'])
        ->name('dang-ky.xu-ly');
    Route::get('/dang-nhap', [XacThucController::class, 'hienThiFormDangNhap'])
        ->name('dang-nhap.hien-thi');
    Route::post('/dang-nhap', [XacThucController::class, 'dangNhap'])
        ->name('dang-nhap.xu-ly');
    Route::get('/quen-mat-khau', [QuenMatKhauController::class, 'hienThiFormQuenMatKhau'])
        ->name('quen-mat-khau.hien-thi');
    Route::post('/quen-mat-khau', [QuenMatKhauController::class, 'guiLienKetDatLaiMatKhau'])
        ->name('quen-mat-khau.gui-lien-ket');
    Route::get('/dat-lai-mat-khau/{token}', [DatLaiMatKhauController::class, 'hienThiFormDatLaiMatKhau'])
        ->name('password.reset');
    Route::post('/dat-lai-mat-khau', [DatLaiMatKhauController::class, 'datLaiMatKhau'])
        ->name('dat-lai-mat-khau.xu-ly');
});

Route::get('/kich-hoat-tai-khoan/{token}', [XacThucController::class, 'kichHoatTaiKhoan'])
    ->name('kich-hoat-tai-khoan');

// Các chức năng yêu cầu đăng nhập.
Route::middleware('auth.custom')->group(function () {
    Route::get('/dang-xuat', [XacThucController::class, 'dangXuat'])->name('dang-xuat');

    Route::prefix('tai-khoan')->group(function () {
        Route::get('/', [TaiKhoanController::class, 'hienThiTaiKhoan'])->name('tai-khoan.hien-thi');
        Route::put('/cap-nhat', [TaiKhoanController::class, 'capNhatTaiKhoan'])->name('tai-khoan.cap-nhat');
        Route::post('/doi-mat-khau', [TaiKhoanController::class, 'doiMatKhau'])->name('tai-khoan.doi-mat-khau');
        Route::post('/dia-chi', [TaiKhoanController::class, 'themDiaChi'])->name('tai-khoan.dia-chi.them');
        Route::put('/dia-chi/{maDiaChi}', [TaiKhoanController::class, 'datDiaChiMacDinh'])->name('tai-khoan.dia-chi.dat-mac-dinh');
        Route::delete('/dia-chi/{maDiaChi}', [TaiKhoanController::class, 'xoaDiaChi'])->name('tai-khoan.dia-chi.xoa');
    });

    Route::get('/don-hang/{maDonHang}', [DonHangController::class, 'hienThiChiTietDonHang'])
        ->name('don-hang.chi-tiet');
    Route::post('/don-hang/{maDonHang}/huy', [DonHangController::class, 'huyDonHang'])
        ->name('don-hang.huy');
    Route::post('/don-hang/{maDonHang}/yeu-cau-doi-tra', [DonHangController::class, 'guiYeuCauDoiTra'])
        ->name('don-hang.gui-yeu-cau-doi-tra');

    Route::post('/phieu-giam-gia/{phieuGiamGia}/nhan', [PhieuGiamGiaController::class, 'nhanPhieuGiamGia'])
        ->name('phieu-giam-gia.nhan');

    Route::post('/danh-gia', [DanhGiaController::class, 'themDanhGia'])->name('danh-gia.them');
    Route::get('/danh-gia/{sanPham}', [DanhGiaController::class, 'hienThiDanhSachDanhGia'])->name('danh-gia.danh-sach');

    Route::get('/yeu-thich', [YeuThichController::class, 'hienThiDanhSachYeuThich'])->name('yeu-thich.danh-sach');
    Route::post('/yeu-thich/them', [YeuThichController::class, 'themSanPhamYeuThich'])->name('yeu-thich.them');
    Route::post('/yeu-thich/xoa', [YeuThichController::class, 'xoaSanPhamYeuThich'])->name('yeu-thich.xoa');
});

// Địa giới dùng để tính phí Giao Hàng Nhanh.
Route::get('/giao-hang-nhanh/tinh-thanh', [DiaChiGhnController::class, 'layTinhThanh'])
    ->name('giao-hang-nhanh.tinh-thanh');
Route::get('/giao-hang-nhanh/quan-huyen', [DiaChiGhnController::class, 'layHuyen'])
    ->name('giao-hang-nhanh.quan-huyen');
Route::get('/giao-hang-nhanh/phuong-xa', [DiaChiGhnController::class, 'layXa'])
    ->name('giao-hang-nhanh.phuong-xa');

// Thanh toán cho khách vãng lai và người dùng đã đăng nhập.
Route::get('/thanh-toan', [ThanhToanController::class, 'hienThiThanhToan'])->name('thanh-toan.hien-thi');
Route::post('/thanh-toan/phi-van-chuyen', [ThanhToanController::class, 'tinhPhiVanChuyen'])
    ->name('thanh-toan.phi-van-chuyen');
Route::get('/thanh-toan/dia-chi', [ThanhToanController::class, 'layDiaChi'])
    ->name('thanh-toan.dia-chi');
Route::post('/thanh-toan/phieu-giam-gia', [ThanhToanController::class, 'apDungMaGiamGia'])
    ->name('thanh-toan.ap-dung-phieu-giam-gia');
Route::post('/thanh-toan/dat-hang', [ThanhToanController::class, 'datHang'])
    ->name('thanh-toan.dat-hang');
Route::post('/thanh-toan/paypal', [ThanhToanController::class, 'datHangPayPal'])
    ->name('thanh-toan.paypal');

// Sản phẩm.
Route::get('/san-pham', [SanPhamController::class, 'hienThiDanhSachSanPham'])->name('san-pham.danh-sach');
Route::get('/san-pham/loc', [SanPhamController::class, 'locSanPham'])->name('san-pham.loc');
Route::get('/san-pham/{slug}/bien-the', [SanPhamController::class, 'layThongTinBienTheSanPham'])
    ->name('san-pham.bien-the');
Route::get('/san-pham/{slug}', [SanPhamController::class, 'hienThiChiTietSanPham'])
    ->name('san-pham.chi-tiet');

// Giỏ hàng.
Route::post('/gio-hang/them', [GioHangController::class, 'themVaoGioHang'])->name('gio-hang.them');
Route::post('/gio-hang/xoa-nho', [GioHangController::class, 'xoaKhoiGioHangNho'])->name('gio-hang.xoa-nho');
Route::get('/gio-hang/nho', [GioHangController::class, 'hienThiGioHangNho'])->name('gio-hang.nho');
Route::get('/gio-hang', [GioHangController::class, 'hienThiGioHang'])->name('gio-hang.hien-thi');
Route::post('/gio-hang/cap-nhat', [GioHangController::class, 'capNhatGioHang'])->name('gio-hang.cap-nhat');
Route::post('/gio-hang/xoa', [GioHangController::class, 'xoaKhoiGioHang'])->name('gio-hang.xoa');

Route::get('/tim-kiem', [TimKiemController::class, 'timKiem'])->name('tim-kiem');

require __DIR__.'/admin.php';
