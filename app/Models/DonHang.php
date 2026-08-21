<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;

    protected $table = 'don_hang';

    protected $primaryKey = 'ma_don_hang';

    protected $fillable = [
        'ma_nguoi_dung',
        'tong_tien',
        'tam_tinh',
        'phi_van_chuyen',
        'so_tien_giam',
        'ma_giam_gia',
        'ma_phieu_giam_gia',
        'trang_thai',
        'nguoi_huy',
        'ly_do_huy',
        'ly_do_giao_that_bai',
        'giao_that_bai_luc',
        'hoan_ve_cua_hang_luc',
        'cua_hang_nhan_lai_luc',
        'tinh_trang_hang_hoan',
        'ly_do_hang_hoan_hu',
        'da_hoan_ton_kho',
        'hoan_ton_kho_luc',
        'hoan_tat_luc',
        'ma_dia_chi_giao_hang',
        'so_lan_giao_that_bai',
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'tam_tinh' => 'decimal:2',
        'phi_van_chuyen' => 'decimal:2',
        'so_tien_giam' => 'decimal:2',
        'da_hoan_ton_kho' => 'boolean',
        'giao_that_bai_luc' => 'datetime',
        'hoan_ve_cua_hang_luc' => 'datetime',
        'cua_hang_nhan_lai_luc' => 'datetime',
        'hoan_ton_kho_luc' => 'datetime',
        'hoan_tat_luc' => 'datetime',
        'so_lan_giao_that_bai' => 'integer',
    ];

    // Lấy các dòng sản phẩm trong đơn hàng.
    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'ma_don_hang');
    }

    // Lấy người dùng đã đặt đơn hàng.
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    // Lấy địa chỉ giao hàng đã lưu trong tài khoản.
    public function diaChiGiaoHang()
    {
        return $this->belongsTo(DiaChiGiaoHang::class, 'ma_dia_chi_giao_hang');
    }

    // Lấy giao dịch thanh toán của đơn hàng.
    public function thanhToan()
    {
        return $this->hasOne(ThanhToan::class, 'ma_don_hang');
    }

    // Lấy phiếu giảm giá đã dùng cho đơn hàng.
    public function phieuGiamGia()
    {
        return $this->belongsTo(PhieuGiamGia::class, 'ma_phieu_giam_gia');
    }

    // Lấy yêu cầu đổi trả của đơn hàng.
    public function yeuCauDoiTra()
    {
        return $this->hasOne(YeuCauDoiTra::class, 'ma_don_hang');
    }

    // Lay dia chi giao hang thong qua khoa ngoai cua don.
    public function layDiaChiGiaoHang()
    {
        return $this->diaChiGiaoHang;
    }

    // Lay thoi diem bat dau tinh han doi tra.
    public function thoiDiemBatDauDoiTra()
    {
        if ($this->trang_thai == 'hoan_thanh') {
            return $this->hoan_tat_luc;
        }

        return null;
    }

    // Lay han cuoi duoc gui yeu cau doi tra.
    public function hanDoiTra()
    {
        $thoiDiemBatDau = $this->thoiDiemBatDauDoiTra();
        if (! $thoiDiemBatDau) {
            return null;
        }

        return $thoiDiemBatDau->copy()->addDays(3);
    }

    // Kiem tra don hang con du dieu kien gui yeu cau doi tra.
    public function conHanDoiTra()
    {
        if ($this->trang_thai != 'hoan_thanh' || $this->yeuCauDoiTra) {
            return false;
        }

        $hanDoiTra = $this->hanDoiTra();
        if (! $hanDoiTra) {
            return false;
        }

        return now()->lessThanOrEqualTo($hanDoiTra);
    }

    // Lay han doi tra da dinh dang de hien thi.
    public function tenHanDoiTra()
    {
        $hanDoiTra = $this->hanDoiTra();

        if ($hanDoiTra) {
            return $hanDoiTra->format('d/m/Y H:i');
        }

        return '-';
    }

    // Kiem tra don giao that bai lan dau co the giao lai.
    public function coTheGiaoLai()
    {
        return $this->trang_thai == 'giao_that_bai'
            && (int) $this->so_lan_giao_that_bai == 1;
    }

    // Kiem tra don giao that bai co the hoan ve cua hang.
    public function coTheHoanVeCuaHang()
    {
        return $this->trang_thai == 'giao_that_bai'
            && (int) $this->so_lan_giao_that_bai >= 1;
    }
}
