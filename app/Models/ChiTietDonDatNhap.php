<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDonDatNhap extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_don_dat_nhap';

    protected $primaryKey = 'ma_chi_tiet_don_dat_nhap';

    protected $fillable = [
        'ma_don_dat_nhap',
        'ma_san_pham',
        'so_luong_dat',
        'so_luong_nhan',
        'so_luong_tu_choi',
        'so_luong_da_nhap',
        'ngay_san_xuat',
        'han_su_dung',
    ];

    protected $casts = [
        'so_luong_dat' => 'integer',
        'so_luong_nhan' => 'integer',
        'so_luong_tu_choi' => 'integer',
        'so_luong_da_nhap' => 'integer',
        'ngay_san_xuat' => 'date',
        'han_su_dung' => 'date',
    ];

    // Lấy đơn đặt nhập chứa dòng chi tiết này.
    public function donDatNhap()
    {
        return $this->belongsTo(DonDatNhap::class, 'ma_don_dat_nhap');
    }

    // Lấy sản phẩm được đặt nhập.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }
}