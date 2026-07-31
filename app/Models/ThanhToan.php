<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    protected $table = 'thanh_toan';

    protected $primaryKey = 'ma_thanh_toan';

    protected $fillable = [
        'ma_don_hang',
        'phuong_thuc',
        'ma_giao_dich',
        'trang_thai',
        'thanh_toan_luc',
        'so_tien',
    ];

    protected $casts = [
        'thanh_toan_luc' => 'datetime',
        'so_tien' => 'decimal:2',
    ];

    // Lấy đơn hàng của giao dịch thanh toán.
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'ma_don_hang');
    }
}