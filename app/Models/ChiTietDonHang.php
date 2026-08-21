<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class ChiTietDonHang extends Model
{
    protected $table = 'chi_tiet_don_hang';

    protected $primaryKey = 'ma_chi_tiet_don_hang';

    protected $fillable = [
        'ma_don_hang',
        'ma_san_pham',
        'ma_lo_hang_kho',
        'so_luong',
        'gia',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'gia' => 'decimal:2',
    ];

    // Lay san pham cua dong don hang.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }

    // Lay don hang chua dong san pham.
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'ma_don_hang');
    }

    // Lay lo hang kho da xuat cho dong don hang.
    public function loHangKho()
    {
        return $this->belongsTo(LoHangKho::class, 'ma_lo_hang_kho');
    }

    // Chuan bi du lieu de hoan so luong ve dung lo hang.
    public function layPhanBoTonKhoDeHoan()
    {
        if (! $this->ma_lo_hang_kho) {
            throw new RuntimeException(
                'Khong the hoan ton kho vi don hang khong con thong tin lo.'
            );
        }

        return [[
            'ma_lo_hang_kho' => $this->ma_lo_hang_kho,
            'so_luong' => $this->so_luong,
            'gia' => $this->gia,
        ]];
    }
}
