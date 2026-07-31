<?php

use App\Http\Controllers\Admin\DanhMucController;
use App\Http\Controllers\Admin\DoiTraController;
use App\Http\Controllers\Admin\DonHangController;
use App\Http\Controllers\Admin\KhoController;
use App\Http\Controllers\Admin\NguoiDungController;
use App\Http\Controllers\Admin\NhaCungCapController;
use App\Http\Controllers\Admin\NhapKhoController;
use App\Http\Controllers\Admin\PhieuGiamGiaController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\TongQuanController;
use App\Http\Controllers\Admin\XacThucQuanTriController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    // Đăng nhập và đăng xuất admin.
    Route::middleware('check.auth.admin')->group(function () {
        Route::get('/dang-nhap', [XacThucQuanTriController::class, 'hienThiFormDangNhap'])
            ->name('admin.dang-nhap.hien-thi');
        Route::post('/dang-nhap', [XacThucQuanTriController::class, 'dangNhap'])
            ->name('admin.dang-nhap.xu-ly');
        Route::get('/login', [XacThucQuanTriController::class, 'hienThiFormDangNhap'])
            ->name('admin.login');
        Route::post('/login', [XacThucQuanTriController::class, 'dangNhap'])
            ->name('admin.login.xu-ly');
    });
    Route::get('/dang-xuat', [XacThucQuanTriController::class, 'dangXuat'])
        ->name('admin.dang-xuat');

    Route::middleware('auth.custom')->group(function () {
        Route::get('/tong-quan', [TongQuanController::class, 'hienThiTongQuan'])
            ->name('admin.tong-quan');
    });

    // Quản lý người dùng.
    Route::middleware('permission:quan_ly_nguoi_dung')->group(function () {
        Route::get('/nguoi-dung', [NguoiDungController::class, 'hienThiDanhSachNguoiDung'])
            ->name('admin.nguoi-dung.danh-sach');
        Route::post('/nguoi-dung/cap-nhat-trang-thai', [NguoiDungController::class, 'capNhatTrangThaiNguoiDung'])
            ->name('admin.nguoi-dung.cap-nhat-trang-thai');
    });

    // Quản lý danh mục.
    Route::middleware('permission:quan_ly_danh_muc')->group(function () {
        Route::get('/danh-muc/them', [DanhMucController::class, 'hienThiFormThemDanhMuc'])
            ->name('admin.danh-muc.them');
        Route::post('/danh-muc/them', [DanhMucController::class, 'themDanhMuc'])
            ->name('admin.danh-muc.luu');
        Route::get('/danh-muc', [DanhMucController::class, 'hienThiDanhSachDanhMuc'])
            ->name('admin.danh-muc.danh-sach');
        Route::post('/danh-muc/cap-nhat', [DanhMucController::class, 'capNhatDanhMuc'])
            ->name('admin.danh-muc.cap-nhat');
        Route::post('/danh-muc/xoa', [DanhMucController::class, 'xoaDanhMuc'])
            ->name('admin.danh-muc.xoa');
    });

    // Quản lý sản phẩm, kho và nhập hàng.
    Route::middleware('permission:quan_ly_san_pham')->group(function () {
        Route::get('/san-pham/them', [SanPhamController::class, 'hienThiFormThemSanPham'])->name('admin.san-pham.them');
        Route::post('/san-pham/them', [SanPhamController::class, 'themSanPham'])->name('admin.san-pham.luu');
        Route::get('/san-pham', [SanPhamController::class, 'hienThiDanhSachSanPham'])->name('admin.san-pham.danh-sach');
        Route::post('/san-pham/cap-nhat', [SanPhamController::class, 'capNhatSanPham'])->name('admin.san-pham.cap-nhat');
        Route::post('/san-pham/xoa', [SanPhamController::class, 'xoaSanPham'])->name('admin.san-pham.xoa');

        Route::get('/kho-hang', [KhoController::class, 'hienThiDanhSachKho'])->name('admin.kho-hang.danh-sach');
        Route::get('/kho-hang/hang-hu', [KhoController::class, 'hienThiDanhSachHangHu'])->name('admin.kho-hang.hang-hu');
        Route::post('/kho-hang/{loHangKho}/dieu-chinh', [KhoController::class, 'dieuChinhLoHang'])->name('admin.kho-hang.dieu-chinh');

        Route::get('/phieu-giam-gia', [PhieuGiamGiaController::class, 'hienThiDanhSachPhieuGiamGia'])->name('admin.phieu-giam-gia.danh-sach');
        Route::post('/phieu-giam-gia', [PhieuGiamGiaController::class, 'themPhieuGiamGia'])->name('admin.phieu-giam-gia.luu');
        Route::put('/phieu-giam-gia/{phieuGiamGia}', [PhieuGiamGiaController::class, 'capNhatPhieuGiamGia'])->name('admin.phieu-giam-gia.cap-nhat');
        Route::delete('/phieu-giam-gia/{phieuGiamGia}', [PhieuGiamGiaController::class, 'xoaPhieuGiamGia'])->name('admin.phieu-giam-gia.xoa');

        Route::get('/nha-cung-cap', [NhaCungCapController::class, 'hienThiDanhSachNhaCungCap'])->name('admin.nha-cung-cap.danh-sach');
        Route::post('/nha-cung-cap', [NhaCungCapController::class, 'themNhaCungCap'])->name('admin.nha-cung-cap.luu');
        Route::put('/nha-cung-cap/{nhaCungCap}', [NhaCungCapController::class, 'capNhatNhaCungCap'])->name('admin.nha-cung-cap.cap-nhat');
        Route::delete('/nha-cung-cap/{nhaCungCap}', [NhaCungCapController::class, 'xoaNhaCungCap'])->name('admin.nha-cung-cap.xoa');

        Route::get('/don-dat-nhap', [NhapKhoController::class, 'hienThiDanhSachDonDatNhap'])->name('admin.don-dat-nhap.danh-sach');
        Route::get('/don-dat-nhap/them', [NhapKhoController::class, 'hienThiFormThemDonDatNhap'])->name('admin.don-dat-nhap.them');
        Route::post('/don-dat-nhap', [NhapKhoController::class, 'themDonDatNhap'])->name('admin.don-dat-nhap.luu');
        Route::get('/don-dat-nhap/{maDonDatNhap}', [NhapKhoController::class, 'hienThiChiTietDonDatNhap'])->name('admin.don-dat-nhap.chi-tiet');
        Route::get('/don-dat-nhap/{maDonDatNhap}/nhap-kho', [NhapKhoController::class, 'hienThiFormNhapKho'])->name('admin.don-dat-nhap.nhap-kho');
        Route::post('/don-dat-nhap/{maDonDatNhap}/nhap-kho', [NhapKhoController::class, 'nhapKho'])->name('admin.don-dat-nhap.xu-ly-nhap-kho');
        Route::post('/don-dat-nhap/{maDonDatNhap}/xoa', [NhapKhoController::class, 'xoaDonDatNhap'])->name('admin.don-dat-nhap.xoa');

        Route::get('/phieu-nhap', [NhapKhoController::class, 'hienThiDanhSachPhieuNhap'])->name('admin.phieu-nhap.danh-sach');
        Route::get('/phieu-nhap/{phieuNhap}', [NhapKhoController::class, 'hienThiChiTietPhieuNhap'])->name('admin.phieu-nhap.chi-tiet');
        Route::get('/phieu-hang-hu', [NhapKhoController::class, 'hienThiDanhSachPhieuHangHu'])->name('admin.phieu-hang-hu.danh-sach');
        Route::get('/phieu-hang-hu/{phieuHangHu}', [NhapKhoController::class, 'hienThiChiTietPhieuHangHu'])->name('admin.phieu-hang-hu.chi-tiet');
    });

    // Quản lý đơn hàng và đổi trả.
    Route::middleware('permission:quan_ly_don_hang')->group(function () {
        Route::get('/don-hang', [DonHangController::class, 'hienThiDanhSachDonHang'])->name('admin.don-hang.danh-sach');
        Route::get('/don-hang/{maDonHang}', [DonHangController::class, 'hienThiChiTietDonHang'])->name('admin.don-hang.chi-tiet');
        Route::post('/don-hang/xac-nhan', [DonHangController::class, 'xacNhanDonHang'])->name('admin.don-hang.xac-nhan');
        Route::post('/don-hang/giao-hang', [DonHangController::class, 'giaoDonHang'])->name('admin.don-hang.giao-hang');
        Route::post('/don-hang/hoan-tat', [DonHangController::class, 'capNhatTrangThaiDonHang'])->name('admin.don-hang.hoan-tat');
        Route::post('/don-hang/giao-that-bai', [DonHangController::class, 'ghiNhanGiaoHangThatBai'])->name('admin.don-hang.giao-that-bai');
        Route::post('/don-hang/giao-lai', [DonHangController::class, 'giaoLaiDonHang'])->name('admin.don-hang.giao-lai');
        Route::post('/don-hang/hoan-ve-cua-hang', [DonHangController::class, 'chuyenHangHoanVeCuaHang'])->name('admin.don-hang.hoan-ve-cua-hang');
        Route::post('/don-hang/nhan-hang-hoan', [DonHangController::class, 'xacNhanNhanHangHoan'])->name('admin.don-hang.nhan-hang-hoan');
        Route::post('/don-hang/hoan-tien-paypal', [DonHangController::class, 'xacNhanHoanTienPayPal'])->name('admin.don-hang.hoan-tien-paypal');
        Route::post('/don-hang/huy', [DonHangController::class, 'huyDonHang'])->name('admin.don-hang.huy');

        Route::post('/doi-tra/{maYeuCauDoiTra}/duyet', [DoiTraController::class, 'duyetYeuCau'])->name('admin.doi-tra.duyet');
        Route::post('/doi-tra/{maYeuCauDoiTra}/nhan-hang', [DoiTraController::class, 'nhanHangDoiTra'])->name('admin.doi-tra.nhan-hang');
        Route::post('/doi-tra/{maYeuCauDoiTra}/giao-hang-doi', [DoiTraController::class, 'giaoHangDoi'])->name('admin.doi-tra.giao-hang-doi');
        Route::post('/doi-tra/{maYeuCauDoiTra}/hoan-tat', [DoiTraController::class, 'hoanTatDoiTra'])->name('admin.doi-tra.hoan-tat');
    });
});