<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinhChungPhieuHangHu extends Model
{
    use HasFactory;

    protected $table = 'minh_chung_phieu_hang_hu';

    protected $primaryKey = 'ma_minh_chung_phieu_hang_hu';

    protected $fillable = [
        'ma_phieu_hang_hu',
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

    // Lấy phiếu hàng hư chứa minh chứng này.
    public function phieuHangHu()
    {
        return $this->belongsTo(PhieuHangHu::class, 'ma_phieu_hang_hu');
    }
    // Tao duong dan day du de admin bam xem minh chung.
    public function getDuongDanHienThiAttribute()
    {
        if (! $this->duong_dan) {
            return '#';
        }

        if ($this->o_dia == 'cloudinary') {
            return $this->duong_dan;
        }

        if (str_starts_with($this->duong_dan, 'http://') || str_starts_with($this->duong_dan, 'https://')) {
            return $this->duong_dan;
        }

        return asset('storage/'.$this->duong_dan);
    }
}