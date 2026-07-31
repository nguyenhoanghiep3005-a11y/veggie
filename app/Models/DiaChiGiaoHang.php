<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaChiGiaoHang extends Model
{
    protected $table = 'dia_chi_giao_hang';

    protected $primaryKey = 'ma_dia_chi_giao_hang';

    protected $fillable = [
        'ma_nguoi_dung',
        'ho_ten',
        'so_dien_thoai',
        'dia_chi',
        'tinh_thanh',
        'ma_tinh',
        'ma_huyen',
        'ma_xa',
        'mac_dinh',
    ];

    protected $casts = [
        'mac_dinh' => 'boolean',
    ];

    // Lấy người dùng sở hữu địa chỉ giao hàng.
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    // Lấy các đơn hàng đã dùng địa chỉ giao hàng.
    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'ma_dia_chi_giao_hang');
    }

    // Kiểm tra địa chỉ đã có đủ mã địa giới GHN.
    public function coDiaChiGhn()
    {
        return ! empty($this->ma_tinh)
            && ! empty($this->ma_huyen)
            && ! empty($this->ma_xa);
    }
}