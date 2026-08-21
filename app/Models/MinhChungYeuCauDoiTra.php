<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinhChungYeuCauDoiTra extends Model
{
    protected $table = 'minh_chung_yeu_cau_doi_tra';

    protected $primaryKey = 'ma_minh_chung_yeu_cau_doi_tra';

    protected $fillable = [
        'ma_yeu_cau_doi_tra',
        'o_dia',
        'duong_dan',
        'ten_goc',
        'loai_mime',
        'loai_tep',
        'kich_thuoc',
        'ma_cong_khai',
    ];

    protected $casts = [
        'kich_thuoc' => 'integer',
    ];

    public function yeuCauDoiTra()
    {
        return $this->belongsTo(YeuCauDoiTra::class, 'ma_yeu_cau_doi_tra');
    }
}
