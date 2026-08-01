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
        'san_pham',
        'minh_chung',
        'trang_thai',
        'yeu_cau_luc',
        'duyet_luc',
        'nhan_hang_luc',
    ];

    protected $casts = [
        'san_pham' => 'array',
        'minh_chung' => 'array',
        'yeu_cau_luc' => 'datetime',
        'duyet_luc' => 'datetime',
        'nhan_hang_luc' => 'datetime',
    ];

    // Lay don hang co yeu cau doi tra.
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'ma_don_hang');
    }

    // Tinh tong so luong san pham khach yeu cau doi tra.
    public function tongSoLuong()
    {
        $tongSoLuong = 0;
        $sanPhamDoiTras = $this->san_pham;

        if (! is_array($sanPhamDoiTras)) {
            return 0;
        }

        foreach ($sanPhamDoiTras as $sanPhamDoiTra) {
            if (isset($sanPhamDoiTra['so_luong'])) {
                $tongSoLuong += (int) $sanPhamDoiTra['so_luong'];
            }
        }

        return $tongSoLuong;
    }
}