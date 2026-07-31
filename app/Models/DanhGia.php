<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danh_gia';

    protected $primaryKey = 'ma_danh_gia';

    protected $fillable = ['ma_nguoi_dung', 'ma_san_pham', 'so_sao', 'binh_luan'];

    protected $casts = [
        'so_sao' => 'integer',
    ];

    // Lấy sản phẩm được đánh giá.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }

    // Lấy người dùng đã gửi đánh giá.
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }
}