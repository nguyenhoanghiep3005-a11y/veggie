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
        'du_lieu_dia_chi_giao_hang',
        'so_lan_giao_that_bai',
    ];

    protected $casts = [
        'tong_tien' => 'decimal:2',
        'tam_tinh' => 'decimal:2',
        'phi_van_chuyen' => 'decimal:2',
        'so_tien_giam' => 'decimal:2',
        'du_lieu_dia_chi_giao_hang' => 'array',
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

    // Lấy địa chỉ giao hàng từ dữ liệu đơn hoặc địa chỉ tài khoản.
    public function layDiaChiGiaoHang()
    {
        if ($this->du_lieu_dia_chi_giao_hang) {
            $data = $this->du_lieu_dia_chi_giao_hang;
            $diaChiGiaoHang = new DiaChiGiaoHang();
            $diaChiGiaoHang->ho_ten = $data['ho_ten'] ?? 'Khách vãng lai';
            $diaChiGiaoHang->so_dien_thoai = $data['so_dien_thoai'] ?? '-';
            $diaChiGiaoHang->dia_chi = $data['dia_chi'] ?? '-';
            $diaChiGiaoHang->tinh_thanh = $data['tinh_thanh'] ?? '-';
            $diaChiGiaoHang->ma_tinh = $data['ma_tinh'] ?? null;
            $diaChiGiaoHang->ma_huyen = $data['ma_huyen'] ?? null;
            $diaChiGiaoHang->ma_xa = $data['ma_xa'] ?? null;

            return $diaChiGiaoHang;
        }

        return $this->diaChiGiaoHang;
    }

    // Lay thoi diem bat dau tinh han doi tra.
    public function thoiDiemBatDauDoiTra()
    {
        if ($this->hoan_tat_luc) {
            return $this->hoan_tat_luc;
        }

        if ($this->trang_thai == 'hoan_thanh') {
            return $this->updated_at;
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

    // Kiem tra don hang co yeu cau doi tra hay khong.
    public function coYeuCauDoiTra()
    {
        return $this->yeuCauDoiTra ? true : false;
    }

    // Lay ten trang thai doi tra de hien thi tren don hang.
    public function tenTrangThaiDoiTra()
    {
        if (! $this->yeuCauDoiTra) {
            return '';
        }

        return $this->yeuCauDoiTra->tenTrangThai();
    }

    // Lay lop mau trang thai doi tra de hien thi tren don hang.
    public function lopTrangThaiDoiTra()
    {
        if (! $this->yeuCauDoiTra) {
            return '';
        }

        return $this->yeuCauDoiTra->lopTrangThai();
    }

    // Lay ten trang thai don hang cho admin.
    public function tenTrangThai()
    {
        if ($this->coYeuCauDoiTra()) {
            return $this->tenTrangThaiDoiTra();
        }

        if ($this->trang_thai == 'da_huy') {
            return $this->nguoi_huy == 'quan_tri'
                ? 'Đã hủy bởi Shop'
                : 'Đã hủy';
        }

        $tenTrangThais = [
            'cho_xac_nhan' => 'Chờ xác nhận',
            'da_xac_nhan' => 'Đã xác nhận',
            'dang_giao' => 'Đang giao hàng',
            'hoan_thanh' => 'Hoàn thành',
            'giao_that_bai' => 'Giao hàng thất bại',
            'dang_hoan_hang' => 'Đang hoàn hàng',
            'da_hoan_ve_kho' => 'Đã hoàn về kho',
            'da_huy' => 'Đã hủy',
        ];

        if (isset($tenTrangThais[$this->trang_thai])) {
            return $tenTrangThais[$this->trang_thai];
        }

        return $this->trang_thai;
    }

    // Lay lop mau trang thai cho giao dien admin.
    public function lopTrangThaiQuanTri()
    {
        if ($this->coYeuCauDoiTra()) {
            return $this->lopTrangThaiDoiTra();
        }

        if ($this->trang_thai == 'cho_xac_nhan') {
            return 'custom-badge badge badge-warning';
        }

        if ($this->trang_thai == 'da_xac_nhan') {
            return 'custom-badge badge badge-primary';
        }

        if ($this->trang_thai == 'dang_giao' || $this->trang_thai == 'dang_hoan_hang') {
            return 'custom-badge badge badge-info';
        }

        if ($this->trang_thai == 'hoan_thanh' || $this->trang_thai == 'da_hoan_ve_kho') {
            return 'custom-badge badge badge-success';
        }

        if ($this->trang_thai == 'da_huy' || $this->trang_thai == 'giao_that_bai') {
            return 'custom-badge badge badge-danger';
        }

        return 'custom-badge badge badge-secondary';
    }

    // Quy doi trang thai noi bo thanh trang thai de khach hang de theo doi.
    public function maTrangThaiHienThiKhachHang()
    {
        if (in_array($this->trang_thai, ['dang_hoan_hang', 'da_hoan_ve_kho'])) {
            return 'giao_that_bai';
        }

        if ($this->trang_thai == 'da_huy' && $this->ly_do_giao_that_bai) {
            return 'giao_that_bai';
        }

        return $this->trang_thai;
    }

    // Lay ten trang thai don hang cho khach hang.
    public function tenTrangThaiKhachHang()
    {
        if ($this->coYeuCauDoiTra()) {
            return $this->tenTrangThaiDoiTra();
        }

        $trangThaiHienThi = $this->maTrangThaiHienThiKhachHang();
        $tenTrangThais = [
            'cho_xac_nhan' => 'Chờ xác nhận',
            'da_xac_nhan' => 'Đã xác nhận',
            'dang_giao' => 'Đang giao hàng',
            'hoan_thanh' => 'Hoàn thành',
            'giao_that_bai' => 'Giao hàng thất bại',
            'da_huy' => 'Đã hủy',
        ];

        if (isset($tenTrangThais[$trangThaiHienThi])) {
            return $tenTrangThais[$trangThaiHienThi];
        }

        return $this->tenTrangThai();
    }

    // Lay ghi chu trang thai de khach hang hieu tinh huong giao hang that bai.
    public function ghiChuTrangThaiKhachHang()
    {
        if ($this->trang_thai == 'giao_that_bai') {
            if ($this->coTheGiaoLai()) {
                return 'Đơn hàng giao không thành công. Shop đang chuẩn bị giao lại.';
            }

            return 'Đơn hàng giao lại vẫn không thành công.';
        }

        if ($this->trang_thai == 'dang_hoan_hang') {
            return 'Đơn hàng giao lại vẫn không thành công và đang được hoàn về cửa hàng.';
        }

        if ($this->trang_thai == 'da_hoan_ve_kho') {
            return 'Đơn hàng giao lại vẫn không thành công và hàng đã được hoàn về cửa hàng.';
        }

        if ($this->trang_thai == 'da_huy' && $this->ly_do_giao_that_bai) {
            return 'Đơn hàng giao lại vẫn không thành công và đã kết thúc xử lý tại cửa hàng.';
        }

        if ($this->trang_thai == 'da_huy') {
            return 'Đơn hàng đã bị hủy.';
        }

        return '';
    }

    // Lay lop mau trang thai cho giao dien client.
    public function lopTrangThaiKhachHang()
    {
        if ($this->coYeuCauDoiTra()) {
            $lopTrangThai = $this->lopTrangThaiDoiTra();

            if ($lopTrangThai == 'custom-badge badge badge-warning') {
                return 'bg-warning';
            }

            if ($lopTrangThai == 'custom-badge badge badge-info') {
                return 'bg-info';
            }

            if ($lopTrangThai == 'custom-badge badge badge-primary') {
                return 'bg-primary';
            }

            if ($lopTrangThai == 'custom-badge badge badge-success') {
                return 'bg-success';
            }

            return 'bg-secondary';
        }

        $trangThaiHienThi = $this->maTrangThaiHienThiKhachHang();

        if ($trangThaiHienThi == 'cho_xac_nhan') {
            return 'bg-warning';
        }

        if ($trangThaiHienThi == 'da_xac_nhan') {
            return 'bg-primary';
        }

        if ($trangThaiHienThi == 'dang_giao') {
            return 'bg-info';
        }

        if ($trangThaiHienThi == 'hoan_thanh') {
            return 'bg-success';
        }

        if ($trangThaiHienThi == 'da_huy' || $trangThaiHienThi == 'giao_that_bai') {
            return 'bg-danger';
        }

        return 'bg-secondary';
    }

    // Kiem tra don hang co the giao lai sau lan that bai dau tien.
    public function coTheGiaoLai()
    {
        return $this->trang_thai == 'giao_that_bai'
            && (int) $this->so_lan_giao_that_bai < 2;
    }

    // Kiem tra don hang da du dieu kien chuyen sang hoan ve cua hang.
    public function coTheHoanVeCuaHang()
    {
        return $this->trang_thai == 'giao_that_bai'
            && (int) $this->so_lan_giao_that_bai >= 2;
    }
    // Lay ten dong tong tien cua don hang.
    public function tenTongTien()
    {
        return 'Tổng tiền đơn hàng';
    }
}