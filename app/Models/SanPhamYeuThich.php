<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPhamYeuThich extends Model
{
    protected $table = 'san_pham_yeu_thich';

    protected $primaryKey = 'ma_san_pham_yeu_thich';

    protected $fillable = ['ma_nguoi_dung', 'ma_san_pham'];

    // Lấy người dùng đã thêm sản phẩm yêu thích.
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    // Lấy sản phẩm đã được thêm vào danh sách yêu thích.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }
}