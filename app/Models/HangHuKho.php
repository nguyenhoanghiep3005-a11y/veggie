<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HangHuKho extends Model
{
    use HasFactory;

    protected $table = 'hang_hu_kho';

    protected $primaryKey = 'ma_hang_hu_kho';

    protected $fillable = [
        'ma_lo_hang_kho',
        'ma_san_pham',
        'ten_san_pham',
        'so_luong',
        'ly_do',
        'xay_ra_luc',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'xay_ra_luc' => 'datetime',
    ];

    // Lấy lô hàng phát sinh hàng hư.
    public function loHangKho()
    {
        return $this->belongsTo(LoHangKho::class, 'ma_lo_hang_kho');
    }

    // Lấy sản phẩm bị hư.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }

    // Lấy các tệp minh chứng hàng hư.
    public function minhChungs()
    {
        return $this->hasMany(MinhChungHangHuKho::class, 'ma_hang_hu_kho');
    }
}