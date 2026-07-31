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

    // Lấy đơn hàng có yêu cầu đổi trả.
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'ma_don_hang');
    }

    // Lấy tên loại yêu cầu đổi trả.
    public function tenLoai()
    {
        if ($this->loai == self::LOAI_HANG_LOI) {
            return 'Đổi trả do hàng hư hỏng hoặc bị lỗi';
        }

        return '-';
    }

    // Lấy tên trạng thái xử lý yêu cầu đổi trả.
    public function tenTrangThai()
    {
        if ($this->trang_thai == 'cho_duyet') {
            return 'Chờ duyệt';
        }

        if ($this->trang_thai == 'da_duyet') {
            return 'Đã duyệt, chờ nhận hàng';
        }

        if ($this->trang_thai == 'da_nhan_hang') {
            return 'Đã nhận hàng đổi trả';
        }

        if ($this->trang_thai == 'hoan_tat') {
            return 'Hoàn tất đổi trả';
        }

        return '-';
    }

    // Lấy lớp màu hiển thị trạng thái đổi trả.
    public function lopTrangThai()
    {
        if ($this->trang_thai == 'cho_duyet') {
            return 'custom-badge badge badge-warning';
        }

        if ($this->trang_thai == 'da_duyet' || $this->trang_thai == 'da_nhan_hang') {
            return 'custom-badge badge badge-info';
        }

        if ($this->trang_thai == 'hoan_tat') {
            return 'custom-badge badge badge-success';
        }

        return 'custom-badge badge badge-secondary';
    }

    // Tính tổng số lượng sản phẩm khách yêu cầu đổi trả.
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