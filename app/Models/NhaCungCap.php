<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    use HasFactory;

    protected $table = 'nha_cung_cap';

    protected $primaryKey = 'ma_nha_cung_cap';

    protected $fillable = [
        'ten',
        'so_dien_thoai',
        'mo_ta',
    ];

    // Lấy các lô hàng của nhà cung cấp.
    public function loHangKhos()
    {
        return $this->hasMany(LoHangKho::class, 'ma_nha_cung_cap');
    }

    // Lấy các đơn đặt nhập gửi cho nhà cung cấp.
    public function donDatNhaps()
    {
        return $this->hasMany(DonDatNhap::class, 'ma_nha_cung_cap');
    }

    // Lấy các phiếu nhập của nhà cung cấp.
    public function phieuNhaps()
    {
        return $this->hasMany(PhieuNhap::class, 'ma_nha_cung_cap');
    }

    // Lấy các phiếu hàng hư của nhà cung cấp.
    public function phieuHangHus()
    {
        return $this->hasMany(PhieuHangHu::class, 'ma_nha_cung_cap');
    }
}