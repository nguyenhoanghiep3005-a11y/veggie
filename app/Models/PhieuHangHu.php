<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuHangHu extends Model
{
    use HasFactory;

    protected $table = 'phieu_hang_hu';

    protected $primaryKey = 'ma_phieu_hang_hu';

    protected $fillable = [
        'so_phieu',
        'ma_don_dat_nhap',
        'ma_phieu_nhap',
        'ma_don_hang',
        'ma_nha_cung_cap',
        'ly_do',
        'xay_ra_luc',
    ];

    protected $casts = [
        'xay_ra_luc' => 'datetime',
    ];

    // Lấy đơn đặt nhập liên quan.
    public function donDatNhap()
    {
        return $this->belongsTo(DonDatNhap::class, 'ma_don_dat_nhap');
    }

    // Lấy phiếu nhập liên quan.
    public function phieuNhap()
    {
        return $this->belongsTo(PhieuNhap::class, 'ma_phieu_nhap');
    }

    // Lấy đơn hàng phát sinh hàng hoàn bị hư.
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'ma_don_hang');
    }

    // Lấy nhà cung cấp của hàng hư.
    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'ma_nha_cung_cap');
    }

    // Lấy các dòng sản phẩm bị hư.
    public function chiTietPhieuHangHus()
    {
        return $this->hasMany(ChiTietPhieuHangHu::class, 'ma_phieu_hang_hu');
    }

    // Lấy các tệp minh chứng hàng hư.
    public function minhChungs()
    {
        return $this->hasMany(MinhChungPhieuHangHu::class, 'ma_phieu_hang_hu');
    }

    // Tạo số phiếu hàng hư tự động.
    public static function taoSoPhieu()
    {
        $soThuTu = ((int) self::max('ma_phieu_hang_hu')) + 1;

        return 'DS-'.str_pad($soThuTu, 4, '0', STR_PAD_LEFT);
    }

    // Lấy nguồn phát sinh phiếu hàng hư để hiển thị.
    public function tenNguon()
    {
        if ($this->ma_don_hang) {
            return 'Hàng hoàn từ đơn hàng';
        }

        if ($this->ma_phieu_nhap) {
            return 'Phiếu nhập hàng';
        }

        if ($this->ma_don_dat_nhap) {
            return 'Đơn đặt nhập';
        }

        return 'Điều chỉnh kho';
    }

    // Tính tổng số lượng hàng hư.
    public function tongSoLuong()
    {
        return (int) $this->chiTietPhieuHangHus->sum('so_luong');
    }

    // Gom tên sản phẩm hư để hiển thị trong danh sách.
    public function tomTatSanPham()
    {
        $tenSanPhams = [];

        foreach ($this->chiTietPhieuHangHus as $chiTiet) {
            $tenSanPham = 'Sản phẩm đã xóa';

            if ($chiTiet->sanPham) {
                $tenSanPham = $chiTiet->sanPham->ten_hien_thi;
            }

            $tenSanPhams[] = $tenSanPham;
        }

        return implode(', ', $tenSanPhams);
    }
}