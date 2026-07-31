<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NguoiDung extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'nguoi_dung';

    protected $primaryKey = 'ma_nguoi_dung';

    protected $fillable = [
        'ten',
        'email',
        'mat_khau',
        'trang_thai',
        'so_dien_thoai',
        'dia_chi',
        'ma_vai_tro',
        'ma_kich_hoat',
    ];

    // Lấy vai trò của người dùng.
    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'ma_vai_tro');
    }

    // Lấy danh sách đánh giá của người dùng.
    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'ma_nguoi_dung');
    }

    // Lấy danh sách địa chỉ giao hàng của người dùng.
    public function diaChiGiaoHangs()
    {
        return $this->hasMany(DiaChiGiaoHang::class, 'ma_nguoi_dung');
    }

    // Lấy danh sách phiếu giảm giá người dùng đã nhận.
    public function phieuGiamGias()
    {
        return $this->belongsToMany(
            PhieuGiamGia::class,
            'nguoi_dung_phieu_giam_gia',
            'ma_nguoi_dung',
            'ma_phieu_giam_gia'
        )->withPivot(['ngay_nhan', 'ngay_su_dung'])->withTimestamps();
    }

    // Kiem tra nguoi dung co mot quyen quan tri hay khong.
    public function coQuyen($tenQuyen)
    {
        if (! $this->vaiTro) {
            return false;
        }

        foreach ($this->vaiTro->cacQuyen as $quyen) {
            if ($quyen->ten == $tenQuyen) {
                return true;
            }
        }

        return false;
    }
    // Cho Laravel biết cột đang lưu mật khẩu của người dùng.
    public function getAuthPasswordName()
    {
        return 'mat_khau';
    }

    // Kiểm tra tài khoản đang chờ kích hoạt.
    public function dangChoKichHoat()
    {
        return $this->trang_thai == 'cho_kich_hoat';
    }
}