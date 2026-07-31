<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuHangHu extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_phieu_hang_hu';

    protected $primaryKey = 'ma_chi_tiet_phieu_hang_hu';

    protected $fillable = [
        'ma_phieu_hang_hu',
        'ma_san_pham',
        'so_luong',
        'ghi_chu',
    ];

    protected $casts = [
        'so_luong' => 'integer',
    ];

    // Lấy phiếu hàng hư chứa dòng chi tiết này.
    public function phieuHangHu()
    {
        return $this->belongsTo(PhieuHangHu::class, 'ma_phieu_hang_hu');
    }

    // Lấy sản phẩm bị hư trong dòng chi tiết.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }
}