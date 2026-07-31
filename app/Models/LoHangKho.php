<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoHangKho extends Model
{
    use HasFactory;

    protected $table = 'lo_hang_kho';

    protected $primaryKey = 'ma_lo_hang_kho';

    protected $fillable = [
        'ma_chi_tiet_phieu_nhap',
        'ma_phieu_nhap',
        'ma_san_pham',
        'ma_nha_cung_cap',
        'so_luong_nhap',
        'so_luong_con',
        'gia_khuyen_mai',
        'ngay_san_xuat',
        'han_su_dung',
    ];

    protected $casts = [
        'so_luong_nhap' => 'integer',
        'so_luong_con' => 'integer',
        'gia_khuyen_mai' => 'decimal:2',
        'ngay_san_xuat' => 'date',
        'han_su_dung' => 'date',
    ];

    // Lấy dòng chi tiết phiếu nhập đã tạo lô hàng.
    public function chiTietPhieuNhap()
    {
        return $this->belongsTo(ChiTietPhieuNhap::class, 'ma_chi_tiet_phieu_nhap');
    }

    // Lấy phiếu nhập của lô hàng.
    public function phieuNhap()
    {
        return $this->belongsTo(PhieuNhap::class, 'ma_phieu_nhap');
    }

    // Lấy sản phẩm của lô hàng.
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'ma_san_pham');
    }

    // Lấy nhà cung cấp của lô hàng.
    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'ma_nha_cung_cap');
    }

    // Kiểm tra lô hàng đã hết hạn.
    public function daHetHan()
    {
        if ($this->han_su_dung == null) {
            return false;
        }

        return $this->han_su_dung->isBefore(today());
    }

    // Kiểm tra lô hàng sắp hết hạn.
    public function sapHetHan()
    {
        if ($this->so_luong_con <= 0 || $this->han_su_dung == null || $this->daHetHan()) {
            return false;
        }

        return today()->diffInDays($this->han_su_dung) <= SanPham::SO_NGAY_CAN_HAN;
    }

    // Kiểm tra lô hàng còn hạn bán bình thường.
    public function conMoi()
    {
        if ($this->so_luong_con <= 0 || $this->daHetHan() || $this->sapHetHan()) {
            return false;
        }

        return true;
    }

    // Kiểm tra lô hàng có giá khuyến mãi hợp lệ.
    public function coKhuyenMai()
    {
        $giaGoc = 0;
        if ($this->sanPham) {
            $giaGoc = (float) $this->sanPham->gia;
        }

        $giaKhuyenMai = 0;
        if ($this->gia_khuyen_mai) {
            $giaKhuyenMai = (float) $this->gia_khuyen_mai;
        }

        return $giaKhuyenMai > 0 && $giaGoc > 0 && $giaKhuyenMai < $giaGoc;
    }

    // Lấy tên trạng thái hạn sử dụng.
    public function tenHanSuDung()
    {
        if ($this->han_su_dung == null) {
            return 'Chưa có hạn sử dụng';
        }

        if ($this->daHetHan()) {
            return 'Hết hạn';
        }

        if ($this->sapHetHan()) {
            return 'Cận hạn';
        }

        return 'Còn mới';
    }

    // Lấy lớp màu hiển thị hạn sử dụng.
    public function lopHanSuDung()
    {
        if ($this->han_su_dung == null) {
            return 'label-default';
        }

        if ($this->daHetHan()) {
            return 'label-danger';
        }

        if ($this->sapHetHan()) {
            return 'label-warning';
        }

        return 'label-success';
    }
}