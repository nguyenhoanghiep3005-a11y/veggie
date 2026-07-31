<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonDatNhap extends Model
{
    use HasFactory;

    protected $table = 'don_dat_nhap';

    protected $primaryKey = 'ma_don_dat_nhap';

    protected $fillable = [
        'so_don',
        'ma_nha_cung_cap',
        'trang_thai',
        'ghi_chu',
        'ngay_dat',
        'nhan_hang_luc',
        'mo_ta_hang_loi',
        'bao_nha_cung_cap_luc',
    ];

    protected $casts = [
        'ngay_dat' => 'date',
        'nhan_hang_luc' => 'datetime',
        'bao_nha_cung_cap_luc' => 'datetime',
    ];

    // Lấy nhà cung cấp của đơn đặt nhập.
    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'ma_nha_cung_cap');
    }

    // Lấy các dòng sản phẩm trong đơn đặt nhập.
    public function chiTietDonDatNhaps()
    {
        return $this->hasMany(ChiTietDonDatNhap::class, 'ma_don_dat_nhap');
    }

    // Lấy các phiếu nhập thuộc đơn đặt nhập.
    public function phieuNhaps()
    {
        return $this->hasMany(PhieuNhap::class, 'ma_don_dat_nhap');
    }

    // Lấy các phiếu hàng hư thuộc đơn đặt nhập.
    public function phieuHangHus()
    {
        return $this->hasMany(PhieuHangHu::class, 'ma_don_dat_nhap');
    }

    // Tạo số đơn đặt nhập tự động.
    public static function taoSoDon()
    {
        $soThuTu = ((int) self::max('ma_don_dat_nhap')) + 1;

        return 'PO-'.str_pad($soThuTu, 4, '0', STR_PAD_LEFT);
    }

    // Tính tổng số lượng đã đặt.
    public function tongSoLuongDat()
    {
        return (int) $this->chiTietDonDatNhaps->sum('so_luong_dat');
    }

    // Tính tổng số lượng đã nhập kho.
    public function tongSoLuongDaNhap()
    {
        return (int) $this->chiTietDonDatNhaps->sum('so_luong_da_nhap');
    }

    // Tính tổng số lượng hàng bị từ chối.
    public function tongSoLuongTuChoi()
    {
        return (int) $this->chiTietDonDatNhaps->sum('so_luong_tu_choi');
    }

    // Lấy tên trạng thái đơn đặt nhập.
    public function tenTrangThai()
    {
        if ($this->trang_thai == 'cho_nhap_hang') {
            return 'Chờ nhập hàng';
        }

        if ($this->trang_thai == 'da_nhap_hang') {
            return 'Đã nhập hàng';
        }

        return $this->trang_thai;
    }

    // Lấy lớp màu hiển thị trạng thái đơn đặt nhập.
    public function lopTrangThai()
    {
        if ($this->trang_thai == 'cho_nhap_hang') {
            return 'badge badge-warning';
        }

        if ($this->trang_thai == 'da_nhap_hang') {
            return 'badge badge-success';
        }

        return 'badge badge-secondary';
    }
}