<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietYeuCauDoiTra extends Model
{
    protected $table = 'chi_tiet_yeu_cau_doi_tra';

    protected $primaryKey = 'ma_chi_tiet_yeu_cau_doi_tra';

    protected $fillable = [
        'ma_yeu_cau_doi_tra',
        'ma_chi_tiet_don_hang',
        'so_luong',
        'da_xuat_hang_doi',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'da_xuat_hang_doi' => 'boolean',
    ];

    public function yeuCauDoiTra()
    {
        return $this->belongsTo(YeuCauDoiTra::class, 'ma_yeu_cau_doi_tra');
    }

    public function chiTietDonHang()
    {
        return $this->belongsTo(ChiTietDonHang::class, 'ma_chi_tiet_don_hang');
    }
}
