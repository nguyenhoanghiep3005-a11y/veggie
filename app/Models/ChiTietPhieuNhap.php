<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuNhap extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_phieu_nhap';

    protected $primaryKey = 'ma_chi_tiet_phieu_nhap';

    protected $fillable = [
        'ma_phieu_nhap',
        'ma_chi_tiet_don_dat_nhap',
        'ma_san_pham',
        'so_luong',
        'ngay_san_xuat',
        'han_su_dung',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'ngay_san_xuat' => 'date',
        'han_su_dung' => 'date',
    ];

    // Lấy phiếu nhập chứa dòng chi tiết này.
    public function phieuNhap()
    {
        return $this->belongsTo(PhieuNhap::class, 'ma_phieu_nhap');
    }

    // Lấy dòng đặt nhập tương ứng với sản phẩm đã nhận.
    public function chiTietDonDatNhap()
    {
        return $this->belongsTo(ChiTietDonDatNhap::class, 'ma_chi_tiet_don_dat_nhap');
    }

    // Lấy sản phẩm đã nhập.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }

    // Lấy lô hàng được tạo từ dòng nhập này.
    public function loHangKho()
    {
        return $this->hasOne(LoHangKho::class, 'ma_chi_tiet_phieu_nhap');
    }
}