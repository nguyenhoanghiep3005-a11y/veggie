<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinhChungHangHuKho extends Model
{
    use HasFactory;

    protected $table = 'minh_chung_hang_hu_kho';

    protected $primaryKey = 'ma_minh_chung_hang_hu_kho';

    protected $fillable = [
        'ma_hang_hu_kho',
        'o_dia',
        'duong_dan',
        'ten_goc',
        'loai_mime',
        'loai_tep',
        'kich_thuoc',
    ];

    protected $casts = [
        'kich_thuoc' => 'integer',
    ];

    // Lấy bản ghi hàng hư trong kho chứa minh chứng này.
    public function hangHuKho()
    {
        return $this->belongsTo(HangHuKho::class, 'ma_hang_hu_kho');
    }
}