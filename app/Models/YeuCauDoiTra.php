<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuCauDoiTra extends Model
{
    use HasFactory;

    public const LOAI_HANG_LOI = 'hang_loi';

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

    // Lay ten loai yeu cau doi tra.
    public function tenLoai()
    {
        if ($this->loai == self::LOAI_HANG_LOI) {
            return 'Đổi trả do hàng hư hỏng hoặc bị lỗi';
        }

        return '-';
    }

    // Lay ten trang thai xu ly yeu cau doi tra.
    public function tenTrangThai()
    {
        if ($this->trang_thai == 'cho_duyet') {
            return 'Chờ duyệt yêu cầu';
        }

        if ($this->trang_thai == 'da_duyet') {
            return 'Đã duyệt yêu cầu';
        }

        if ($this->trang_thai == 'dang_xu_ly') {
            return 'Đang xử lý đổi trả';
        }

        if ($this->trang_thai == 'dang_giao_hang_doi') {
            return 'Đang giao hàng đổi';
        }

        if ($this->trang_thai == 'hoan_tat') {
            return 'Hoàn tất đổi trả';
        }

        return '-';
    }

    // Lay lop mau hien thi trang thai doi tra.
    public function lopTrangThai()
    {
        if ($this->trang_thai == 'cho_duyet') {
            return 'custom-badge badge badge-warning';
        }

        if ($this->trang_thai == 'da_duyet' || $this->trang_thai == 'dang_xu_ly') {
            return 'custom-badge badge badge-info';
        }

        if ($this->trang_thai == 'dang_giao_hang_doi') {
            return 'custom-badge badge badge-primary';
        }

        if ($this->trang_thai == 'hoan_tat') {
            return 'custom-badge badge badge-success';
        }

        return 'custom-badge badge badge-secondary';
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