<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc';

    protected $primaryKey = 'ma_danh_muc';

    protected $fillable = ['ten', 'duong_dan', 'mo_ta', 'hinh_anh'];

    protected $appends = ['duong_dan_hinh_anh'];

    // Lấy các sản phẩm thuộc danh mục.
    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'ma_danh_muc');
    }

    // Tạo đường dẫn đầy đủ để hiển thị hình ảnh danh mục.
    public function getDuongDanHinhAnhAttribute()
    {
        if ($this->hinh_anh) {
            return asset('storage/'.$this->hinh_anh);
        }

        return asset('storage/uploads/categories/default.png');
    }
}