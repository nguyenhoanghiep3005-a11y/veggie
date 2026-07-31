<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PhieuGiamGia extends Model
{
    use HasFactory;

    public const AP_DUNG_TAT_CA = 'tat_ca';
    public const AP_DUNG_KHACH_HANG = 'khach_hang';

    protected $table = 'phieu_giam_gia';

    protected $primaryKey = 'ma_phieu_giam_gia';

    protected $fillable = [
        'ma_giam_gia',
        'phan_tram_giam',
        'gia_tri_don_toi_thieu',
        'so_tien_giam_toi_da',
        'het_han_luc',
        'gioi_han_su_dung',
        'so_lan_da_dung',
        'loai_ap_dung',
        'dang_hoat_dong',
    ];

    protected $casts = [
        'phan_tram_giam' => 'decimal:2',
        'gia_tri_don_toi_thieu' => 'decimal:2',
        'so_tien_giam_toi_da' => 'decimal:2',
        'het_han_luc' => 'datetime',
        'gioi_han_su_dung' => 'integer',
        'so_lan_da_dung' => 'integer',
        'dang_hoat_dong' => 'boolean',
    ];

    // Lấy danh sách đơn hàng đã dùng phiếu giảm giá.
    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'ma_phieu_giam_gia');
    }

    // Lấy danh sách người dùng được nhận phiếu giảm giá.
    public function nguoiDungs()
    {
        return $this->belongsToMany(
            NguoiDung::class,
            'nguoi_dung_phieu_giam_gia',
            'ma_phieu_giam_gia',
            'ma_nguoi_dung'
        )->withPivot(['ngay_nhan', 'ngay_su_dung'])->withTimestamps();
    }

    // Kiểm tra phiếu giảm giá có thể dùng cho đơn hàng hiện tại không.
    public function coTheSuDung($maNguoiDung, $tamTinh = 0)
    {
        $loi = $this->kiemTraDieuKienSuDung($maNguoiDung, $tamTinh);

        return $loi == null;
    }

    // Trả về thông báo khi phiếu giảm giá không đủ điều kiện sử dụng.
    public function kiemTraDieuKienSuDung($maNguoiDung, $tamTinh = 0)
    {
        if (! $this->conHieuLuc()) {
            return 'Mã giảm giá không còn hiệu lực hoặc đã hết lượt sử dụng.';
        }

        if ($this->loai_ap_dung != self::AP_DUNG_TAT_CA) {
            if (! $maNguoiDung) {
                return 'Mã giảm giá này chỉ áp dụng cho khách hàng được chỉ định.';
            }

            $duocGan = DB::table('nguoi_dung_phieu_giam_gia')
                ->where('ma_phieu_giam_gia', $this->ma_phieu_giam_gia)
                ->where('ma_nguoi_dung', $maNguoiDung)
                ->exists();

            if (! $duocGan) {
                return 'Mã giảm giá này chỉ áp dụng cho khách hàng được chỉ định.';
            }
        }

        if ($maNguoiDung) {
            $daSuDung = DB::table('nguoi_dung_phieu_giam_gia')
                ->where('ma_phieu_giam_gia', $this->ma_phieu_giam_gia)
                ->where('ma_nguoi_dung', $maNguoiDung)
                ->whereNotNull('ngay_su_dung')
                ->exists();

            if ($daSuDung) {
                return 'Bạn đã sử dụng mã giảm giá này.';
            }
        }

        if ($tamTinh > 0 && $tamTinh < $this->gia_tri_don_toi_thieu) {
            return 'Đơn hàng cần tối thiểu '.number_format($this->gia_tri_don_toi_thieu, 0, ',', '.').' đ để dùng mã này.';
        }

        return null;
    }

    // Tính số tiền được giảm theo phần trăm và mức giảm tối đa.
    public function tinhSoTienGiam($tamTinh)
    {
        if ($tamTinh < $this->gia_tri_don_toi_thieu) {
            return 0;
        }

        $soTienGiam = round($tamTinh * ($this->phan_tram_giam / 100), 2);

        if ($this->so_tien_giam_toi_da != null && $soTienGiam > $this->so_tien_giam_toi_da) {
            $soTienGiam = $this->so_tien_giam_toi_da;
        }

        if ($soTienGiam > $tamTinh) {
            return $tamTinh;
        }

        return $soTienGiam;
    }

    // Kiểm tra phiếu giảm giá còn bật, còn hạn và còn lượt sử dụng.
    public function conHieuLuc()
    {
        if (! $this->dang_hoat_dong) {
            return false;
        }

        if ($this->het_han_luc && $this->het_han_luc->isPast()) {
            return false;
        }

        if ($this->gioi_han_su_dung != null && $this->so_lan_da_dung >= $this->gioi_han_su_dung) {
            return false;
        }

        return true;
    }

    // Lọc các phiếu giảm giá còn hiệu lực.
    public function scopeConHieuLuc($query)
    {
        return $query
            ->where('dang_hoat_dong', true)
            ->where(function ($query) {
                $query->whereNull('het_han_luc')->orWhere('het_han_luc', '>', now());
            })
            ->where(function ($query) {
                $query->whereNull('gioi_han_su_dung')
                    ->orWhereColumn('so_lan_da_dung', '<', 'gioi_han_su_dung');
            });
    }
}