<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HinhAnhSanPham extends Model
{
    protected $table = 'hinh_anh_san_pham';

    protected $primaryKey = 'ma_hinh_anh_san_pham';

    protected $fillable = ['ma_san_pham', 'hinh_anh'];

    protected $appends = ['duong_dan_hinh_anh'];

    // Lấy sản phẩm của hình ảnh.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }

    // Tạo đường dẫn đầy đủ để hiển thị hình ảnh.
    public function getDuongDanHinhAnhAttribute()
    {
        if ($this->hinh_anh) {
            return asset('storage/'.$this->hinh_anh);
        }

        return asset('storage/uploads/products/default.png');
    }
}