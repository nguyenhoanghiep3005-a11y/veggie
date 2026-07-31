<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    protected $table = 'chi_tiet_don_hang';

    protected $primaryKey = 'ma_chi_tiet_don_hang';

    protected $fillable = [
        'ma_don_hang',
        'ma_san_pham',
        'so_luong',
        'gia',
        'phan_bo_ton_kho',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'gia' => 'decimal:2',
        'phan_bo_ton_kho' => 'array',
    ];

    // Lấy sản phẩm của dòng đơn hàng.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }

    // Lấy đơn hàng chứa dòng sản phẩm.
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'ma_don_hang');
    }
}