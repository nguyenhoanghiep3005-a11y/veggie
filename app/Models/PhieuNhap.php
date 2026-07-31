<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuNhap extends Model
{
    use HasFactory;

    protected $table = 'phieu_nhap';

    protected $primaryKey = 'ma_phieu_nhap';

    protected $fillable = [
        'so_phieu',
        'ma_don_dat_nhap',
        'ma_nha_cung_cap',
        'nhan_hang_luc',
        'ghi_chu',
    ];

    protected $casts = [
        'nhan_hang_luc' => 'datetime',
    ];

    // Lấy đơn đặt nhập liên quan.
    public function donDatNhap()
    {
        return $this->belongsTo(DonDatNhap::class, 'ma_don_dat_nhap');
    }

    // Lấy nhà cung cấp của phiếu nhập.
    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'ma_nha_cung_cap');
    }

    // Lấy các dòng sản phẩm trong phiếu nhập.
    public function chiTietPhieuNhaps()
    {
        return $this->hasMany(ChiTietPhieuNhap::class, 'ma_phieu_nhap');
    }

    // Lấy các lô hàng được tạo từ phiếu nhập.
    public function loHangKhos()
    {
        return $this->hasMany(LoHangKho::class, 'ma_phieu_nhap');
    }

    // Tạo số phiếu nhập tự động.
    public static function taoSoPhieu()
    {
        $soThuTu = ((int) self::max('ma_phieu_nhap')) + 1;

        return 'IR-'.str_pad($soThuTu, 4, '0', STR_PAD_LEFT);
    }

    // Tính tổng số lượng sản phẩm trong phiếu nhập.
    public function tongSoLuong()
    {
        return (int) $this->chiTietPhieuNhaps->sum('so_luong');
    }
}