<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuCauDoiTra extends Model
{
    use HasFactory;

    protected $table = 'yeu_cau_doi_tra';

    protected $primaryKey = 'ma_yeu_cau_doi_tra';

    protected $fillable = [
        'ma_don_hang',
        'loai',
        'mo_ta',
        'trang_thai',
        'yeu_cau_luc',
        'duyet_luc',
        'nhan_hang_luc',
    ];

    protected $casts = [
        'yeu_cau_luc' => 'datetime',
        'duyet_luc' => 'datetime',
        'nhan_hang_luc' => 'datetime',
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'ma_don_hang');
    }

    public function chiTiets()
    {
        return $this->hasMany(
            ChiTietYeuCauDoiTra::class,
            'ma_yeu_cau_doi_tra'
        );
    }

    public function minhChungs()
    {
        return $this->hasMany(
            MinhChungYeuCauDoiTra::class,
            'ma_yeu_cau_doi_tra'
        );
    }

    // Giu dinh dang mang cho cac man hinh cu, du lieu duoc lay tu quan he.
    public function getSanPhamAttribute()
    {
        return $this->chiTiets->map(function ($chiTiet) {
            return [
                'ma_chi_tiet_don_hang' => $chiTiet->ma_chi_tiet_don_hang,
                'so_luong' => $chiTiet->so_luong,
                'phan_bo_hang_doi' => $chiTiet->da_xuat_hang_doi
                    ? [['so_luong' => $chiTiet->so_luong]]
                    : [],
            ];
        })->all();
    }

    public function getMinhChungAttribute()
    {
        return $this->minhChungs->map(function ($minhChung) {
            return [
                'o_dia' => $minhChung->o_dia,
                'duong_dan' => $minhChung->duong_dan,
                'ten_goc' => $minhChung->ten_goc,
                'loai_mime' => $minhChung->loai_mime,
                'loai_tep' => $minhChung->loai_tep,
                'kich_thuoc' => $minhChung->kich_thuoc,
                'ma_cong_khai' => $minhChung->ma_cong_khai,
            ];
        })->all();
    }

    public function tongSoLuong()
    {
        return (int) $this->chiTiets->sum('so_luong');
    }
}
