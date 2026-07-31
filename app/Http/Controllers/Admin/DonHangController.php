<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\HoaDonDonHangMail;
use App\Models\DonHang;
use App\Models\HangHuKho;
use App\Models\SanPham;
use App\Services\MinhChungService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DonHangController extends Controller
{
    private $dichVuMinhChung;

    // Khoi tao dich vu luu minh chung hang hoan bi hu.
    public function __construct(MinhChungService $dichVuMinhChung)
    {
        $this->dichVuMinhChung = $dichVuMinhChung;
    }

    // Hien thi danh sach don hang co phan trang.
    public function hienThiDanhSachDonHang()
    {
        $donHangs = DonHang::with([
            'chiTietDonHangs.sanPham',
            'diaChiGiaoHang',
            'nguoiDung',
            'thanhToan',
            'yeuCauDoiTra',
        ])->orderBy('ma_don_hang', 'desc')->paginate(20);

        foreach ($donHangs as $donHang) {
            $this->chuanBiDonHangHienThi($donHang);
        }

        return view('admin.pages.don-hang', compact('donHangs'));
    }

    // Hien thi chi tiet don hang va thong tin doi tra.
    public function hienThiChiTietDonHang($maDonHang)
    {
        $donHang = DonHang::with([
            'chiTietDonHangs.sanPham.hinhAnhs',
            'diaChiGiaoHang',
            'nguoiDung',
            'thanhToan',
            'yeuCauDoiTra',
        ])->findOrFail($maDonHang);

        $this->chuanBiDonHangHienThi($donHang);

        $yeuCauDoiTra = $donHang->yeuCauDoiTra;
        $sanPhamDoiTras = $this->laySanPhamDoiTra($donHang, $yeuCauDoiTra);
        $minhChungDoiTras = $this->layMinhChungDoiTra($yeuCauDoiTra);
        $ngayYeuCau = $this->dinhDangNgay(
            $yeuCauDoiTra ? $yeuCauDoiTra->yeu_cau_luc : null
        );
        $ngayDuyet = $this->dinhDangNgay(
            $yeuCauDoiTra ? $yeuCauDoiTra->duyet_luc : null
        );
        $ngayNhanHang = $this->dinhDangNgay(
            $yeuCauDoiTra ? $yeuCauDoiTra->nhan_hang_luc : null
        );

        return view('admin.pages.chi-tiet-don-hang', compact(
            'donHang',
            'yeuCauDoiTra',
            'sanPhamDoiTras',
            'minhChungDoiTras',
            'ngayYeuCau',
            'ngayDuyet',
            'ngayNhanHang'
        ));
    }

    // Xac nhan don hang dang cho xu ly.
    public function xacNhanDonHang(Request $request)
    {
        $data = $this->kiemTraMaDonHang($request);
        $donHang = DonHang::with(['nguoiDung', 'chiTietDonHangs.sanPham'])
            ->find($data['ma_don_hang']);

        if (! $donHang || $donHang->trang_thai != 'cho_xac_nhan') {
            return $this->phanHoiLoi(
                'Chỉ xác nhận được đơn đang chờ xác nhận.'
            );
        }

        $donHang->trang_thai = 'da_xac_nhan';
        $donHang->save();

        $daGuiHoaDon = $this->guiHoaDon($donHang);

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => $daGuiHoaDon
                ? 'Đã xác nhận đơn và gửi hóa đơn.'
                : 'Đã xác nhận đơn hàng.',
            'da_gui_hoa_don' => $daGuiHoaDon,
        ]);
    }

    // Chuyen don da xac nhan sang dang giao hang.
    public function giaoDonHang(Request $request)
    {
        $data = $this->kiemTraMaDonHang($request);
        $donHang = DonHang::find($data['ma_don_hang']);

        if (! $donHang || $donHang->trang_thai != 'da_xac_nhan') {
            return $this->phanHoiLoi(
                'Chỉ giao được đơn đã xác nhận.'
            );
        }

        $donHang->trang_thai = 'dang_giao';
        $donHang->save();

        return $this->phanHoiThanhCong('Đơn hàng đang được giao.');
    }

    // Xac nhan don dang giao da giao thanh cong.
    public function capNhatTrangThaiDonHang(Request $request)
    {
        $data = $this->kiemTraMaDonHang($request);
        $donHang = DonHang::with('thanhToan')->find($data['ma_don_hang']);

        if (! $donHang || $donHang->trang_thai != 'dang_giao') {
            return $this->phanHoiLoi(
                'Chỉ hoàn tất được đơn đang giao.'
            );
        }

        $donHang->trang_thai = 'hoan_thanh';
        $donHang->hoan_tat_luc = now();
        $donHang->save();

        if (
            $donHang->thanhToan
            && $donHang->thanhToan->phuong_thuc == 'tien_mat'
        ) {
            $donHang->thanhToan->trang_thai = 'da_thanh_toan';
            $donHang->thanhToan->thanh_toan_luc = now();
            $donHang->thanhToan->save();
        }

        return $this->phanHoiThanhCong('Đã giao hàng thành công.');
    }

    // Ghi nhan khach tu choi nhan hoac khong lien lac duoc.
    public function ghiNhanGiaoHangThatBai(Request $request)
    {
        $data = $request->validate([
            'ma_don_hang' => 'required|integer|exists:don_hang,ma_don_hang',
            'ly_do_giao_that_bai' => 'required|string|max:1000',
        ]);

        $donHang = DonHang::with('thanhToan')->find($data['ma_don_hang']);

        if (! $donHang || $donHang->trang_thai != 'dang_giao') {
            return $this->phanHoiLoi(
                'Chỉ báo giao thất bại khi đơn đang giao.'
            );
        }

        $donHang->trang_thai = 'giao_that_bai';
        $donHang->ly_do_giao_that_bai =
            trim($data['ly_do_giao_that_bai']);
        $donHang->giao_that_bai_luc = now();
        $donHang->so_lan_giao_that_bai = (int) $donHang->so_lan_giao_that_bai + 1;
        $donHang->save();

        if (
            $donHang->thanhToan
            && $donHang->thanhToan->phuong_thuc == 'tien_mat'
        ) {
            $donHang->thanhToan->trang_thai = 'chua_thanh_toan';
            $donHang->thanhToan->save();
        }

        return $this->phanHoiThanhCong(
            'Đã ghi nhận giao hàng thất bại.'
        );
    }

    // Chuyen don giao that bai sang dang hoan ve cua hang.
    public function chuyenHangHoanVeCuaHang(Request $request)
    {
        $data = $this->kiemTraMaDonHang($request);
        $donHang = DonHang::find($data['ma_don_hang']);

        if (! $donHang || $donHang->trang_thai != 'giao_that_bai') {
            return $this->phanHoiLoi(
                'Đơn phải giao thất bại trước khi hoàn về cửa hàng.'
            );
        }

        $donHang->trang_thai = 'dang_hoan_hang';
        $donHang->hoan_ve_cua_hang_luc = now();
        $donHang->save();

        return $this->phanHoiThanhCong(
            'Đơn hàng đang hoàn về cửa hàng.'
        );
    }

    // Chuyen don giao that bai quay lai dang giao khi khach van muon nhan hang.
    public function giaoLaiDonHang(Request $request)
    {
        $data = $this->kiemTraMaDonHang($request);
        $donHang = DonHang::find($data['ma_don_hang']);

        if (! $donHang || $donHang->trang_thai != 'giao_that_bai') {
            return $this->phanHoiLoi(
                'Chỉ giao lại được đơn đang giao thất bại.'
            );
        }

        $donHang->trang_thai = 'dang_giao';
        $donHang->save();

        return $this->phanHoiThanhCong('Đơn hàng đã được chuyển lại sang đang giao.');
    }
    // Nhan lai hang hoan va xu ly ton kho theo tinh trang thuc te.
    public function xacNhanNhanHangHoan(Request $request)
    {
        $data = $request->validate([
            'ma_don_hang' => 'required|integer|exists:don_hang,ma_don_hang',
            'tinh_trang_hang_hoan' => 'required|in:nguyen_ven,hu_hong',
            'ly_do_hang_hoan_hu' => 'required_if:tinh_trang_hang_hoan,hu_hong|nullable|string|max:3000',
            'minh_chung' => 'required_if:tinh_trang_hang_hoan,hu_hong|nullable|array|min:1',
            'minh_chung.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
        ]);

        $donHang = DonHang::with([
            'chiTietDonHangs.sanPham',
            'thanhToan',
        ])->find($data['ma_don_hang']);

        if (! $donHang || $donHang->trang_thai != 'dang_hoan_hang') {
            return $this->phanHoiLoi(
                'Chỉ nhận hàng khi đơn đang hoàn về cửa hàng.'
            );
        }

        try {
            DB::beginTransaction();

            if ($data['tinh_trang_hang_hoan'] == 'nguyen_ven') {
                $this->hoanTonKhoDonHang($donHang);
                $donHang->da_hoan_ton_kho = true;
                $donHang->hoan_ton_kho_luc = now();
            } else {
                $this->luuHangHoanHu(
                    $donHang,
                    trim($data['ly_do_hang_hoan_hu']),
                    $request->file('minh_chung', [])
                );
            }

            $donHang->trang_thai = 'da_hoan_ve_kho';
            $donHang->cua_hang_nhan_lai_luc = now();
            $donHang->tinh_trang_hang_hoan =
                $data['tinh_trang_hang_hoan'];
            $donHang->ly_do_hang_hoan_hu =
                isset($data['ly_do_hang_hoan_hu'])
                    ? trim($data['ly_do_hang_hoan_hu'])
                    : null;
            $donHang->save();

            if (
                $donHang->thanhToan
                && $donHang->thanhToan->phuong_thuc == 'paypal'
            ) {
                $donHang->thanhToan->trang_thai = 'da_thanh_toan';
                $donHang->thanhToan->save();
            }

            DB::commit();

            return $this->phanHoiThanhCong(
                'Đã nhận hàng hoàn và ghi nhận tình trạng hàng.'
            );
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->phanHoiLoi($exception->getMessage());
        }
    }

    // Xac nhan tien PayPal da duoc hoan cho khach.
    public function xacNhanHoanTienPayPal(Request $request)
    {
        $data = $this->kiemTraMaDonHang($request);
        $donHang = DonHang::with('thanhToan')->find($data['ma_don_hang']);

        if (
            ! $donHang
            || ! $donHang->thanhToan
            || $donHang->thanhToan->phuong_thuc != 'paypal'
            || $donHang->thanhToan->trang_thai != 'da_thanh_toan'
        ) {
            return $this->phanHoiLoi('Đơn này không cần hoàn tiền PayPal.');
        }

        if ($donHang->trang_thai != 'da_hoan_ve_kho') {
            return $this->phanHoiLoi('Chỉ hoàn tiền sau khi hàng đã hoàn về kho.');
        }

        $donHang->thanhToan->trang_thai = 'da_hoan_tien';
        $donHang->thanhToan->save();

        $donHang->trang_thai = 'da_huy';
        $donHang->nguoi_huy = 'quan_tri';
        if (! $donHang->ly_do_huy) {
            $donHang->ly_do_huy = 'Đơn giao thất bại đã hoàn về kho và đã hoàn tiền.';
        }
        $donHang->save();

        return $this->phanHoiThanhCong('Đã hoàn tiền PayPal và kết thúc đơn hàng.');
    }

    // Ket thuc don giao that bai sau khi hang da hoan ve kho.
    public function ketThucDonHoan(Request $request)
    {
        $data = $this->kiemTraMaDonHang($request);
        $donHang = DonHang::with('thanhToan')->find($data['ma_don_hang']);

        if (! $donHang || $donHang->trang_thai != 'da_hoan_ve_kho') {
            return $this->phanHoiLoi('Chỉ kết thúc được đơn đã hoàn về kho.');
        }

        if (
            $donHang->thanhToan
            && $donHang->thanhToan->phuong_thuc == 'paypal'
            && $donHang->thanhToan->trang_thai != 'da_hoan_tien'
        ) {
            return $this->phanHoiLoi('Đơn PayPal cần hoàn tiền trước khi hủy.');
        }

        $donHang->trang_thai = 'da_huy';
        $donHang->nguoi_huy = 'quan_tri';
        if (! $donHang->ly_do_huy) {
            $donHang->ly_do_huy = 'Đơn giao thất bại đã hoàn về kho.';
        }
        $donHang->save();

        return $this->phanHoiThanhCong('Đã kết thúc đơn giao thất bại.');
    }
    // Huy don chua giao va hoan lai ton kho.
    public function huyDonHang(Request $request)
    {
        $data = $request->validate([
            'ma_don_hang' => 'required|integer|exists:don_hang,ma_don_hang',
            'ly_do_huy' => 'required|string|max:1000',
        ]);

        $donHang = DonHang::with([
            'chiTietDonHangs.sanPham',
            'thanhToan',
        ])->find($data['ma_don_hang']);

        if (
            ! $donHang
            || ! in_array(
                $donHang->trang_thai,
                ['cho_xac_nhan', 'da_xac_nhan']
            )
        ) {
            return $this->phanHoiLoi(
                'Đơn đang giao phải xử lý bằng luồng giao thất bại.'
            );
        }

        try {
            DB::beginTransaction();

            $this->hoanTonKhoDonHang($donHang);
            $donHang->trang_thai = 'da_huy';
            $donHang->nguoi_huy = 'quan_tri';
            $donHang->ly_do_huy = trim($data['ly_do_huy']);
            $donHang->save();

            if (
                $donHang->thanhToan
                && $donHang->thanhToan->phuong_thuc == 'paypal'
            ) {
                $donHang->thanhToan->trang_thai = 'da_hoan_tien';
                $donHang->thanhToan->save();
            } elseif ($donHang->thanhToan) {
                $donHang->thanhToan->trang_thai = 'chua_thanh_toan';
                $donHang->thanhToan->save();
            }

            DB::commit();

            return $this->phanHoiThanhCong(
                'Đã hủy đơn hàng và hoàn lại tồn kho.'
            );
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->phanHoiLoi($exception->getMessage());
        }
    }

    // Kiem tra ma don hang tu yeu cau.
    private function kiemTraMaDonHang($request)
    {
        return $request->validate([
            'ma_don_hang' => 'required|integer|exists:don_hang,ma_don_hang',
        ]);
    }

    // Hoan ton kho cho tat ca san pham trong don.
    private function hoanTonKhoDonHang($donHang)
    {
        if ($donHang->da_hoan_ton_kho) {
            throw new Exception('Đơn hàng đã được hoàn tồn kho trước đó.');
        }

        foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
            $sanPham = SanPham::find($chiTietDonHang->ma_san_pham);

            if ($sanPham) {
                $sanPham->hoanTonKho(
                    $chiTietDonHang->so_luong,
                    $chiTietDonHang->phan_bo_ton_kho
                );
            }
        }

        $donHang->da_hoan_ton_kho = true;
        $donHang->hoan_ton_kho_luc = now();
        $donHang->save();
    }

    // Luu hang hoan bi hu va cac tep minh chung.
    private function luuHangHoanHu($donHang, $lyDo, $tepMinhChungs)
    {
        $minhChungs = $this->dichVuMinhChung->taiNhieuTepLen(
            $tepMinhChungs,
            config('cloudinary.folders.warehouse_damages')
        );

        foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
            $hangHu = HangHuKho::create([
                'ma_lo_hang_kho' => null,
                'ma_san_pham' => $chiTietDonHang->ma_san_pham,
                'ten_san_pham' => $chiTietDonHang->sanPham
                    ? $chiTietDonHang->sanPham->ten_hien_thi
                    : 'Sản phẩm đã xóa',
                'so_luong' => $chiTietDonHang->so_luong,
                'ly_do' => 'Hàng hoàn bị hư: '.$lyDo,
                'xay_ra_luc' => now(),
            ]);

            foreach ($minhChungs as $minhChung) {
                $this->luuMinhChungHangHu($hangHu, $minhChung);
            }
        }
    }

    // Luu mot tep minh chung cho hang hu.
    private function luuMinhChungHangHu($hangHu, $minhChung)
    {
        $hangHu->minhChungs()->create([
            'o_dia' => $this->layGiaTriMinhChung(
                $minhChung,
                'o_dia',
                'disk',
                'public'
            ),
            'duong_dan' => $this->layGiaTriMinhChung(
                $minhChung,
                'duong_dan',
                'path',
                ''
            ),
            'ten_goc' => $this->layGiaTriMinhChung(
                $minhChung,
                'ten_goc',
                'original_name',
                null
            ),
            'loai_mime' => $this->layGiaTriMinhChung(
                $minhChung,
                'loai_mime',
                'mime_type',
                null
            ),
            'loai_tep' => $this->layGiaTriMinhChung(
                $minhChung,
                'loai_tep',
                'media_type',
                'hinh_anh'
            ),
            'kich_thuoc' => $this->layGiaTriMinhChung(
                $minhChung,
                'kich_thuoc',
                'size',
                0
            ),
        ]);
    }

    // Lay gia tri moi hoac cu trong du lieu minh chung.
    private function layGiaTriMinhChung(
        $minhChung,
        $tenMoi,
        $tenCu,
        $macDinh
    ) {
        if (isset($minhChung[$tenMoi])) {
            return $minhChung[$tenMoi];
        }

        if (isset($minhChung[$tenCu])) {
            return $minhChung[$tenCu];
        }

        return $macDinh;
    }

    // Chuan bi ten nguoi dung, dia chi, thanh toan va san pham cho View.
    private function chuanBiDonHangHienThi($donHang)
    {
        $donHang->ten_khach_hang = 'Khách vãng lai';
        $donHang->email_khach_hang = '-';

        if ($donHang->nguoiDung) {
            $donHang->ten_khach_hang = $donHang->nguoiDung->ten;
            $donHang->email_khach_hang = $donHang->nguoiDung->email;
        }

        $diaChi = $donHang->layDiaChiGiaoHang();
        $donHang->ten_nguoi_nhan = $diaChi ? $diaChi->ho_ten : '-';
        $donHang->so_dien_thoai_nguoi_nhan = $diaChi ? $diaChi->so_dien_thoai : '-';
        $donHang->dia_chi_nguoi_nhan = $diaChi ? $diaChi->dia_chi : '-';
        $donHang->tinh_thanh_nguoi_nhan = $diaChi ? $diaChi->tinh_thanh : '-';

        $donHang->ten_phuong_thuc_thanh_toan = '-';
        $donHang->ten_trang_thai_thanh_toan = '-';
        $donHang->lop_trang_thai_thanh_toan = 'custom-badge badge badge-secondary';

        if ($donHang->thanhToan) {
            if ($donHang->thanhToan->phuong_thuc == 'paypal') {
                $donHang->ten_phuong_thuc_thanh_toan = 'PayPal';
            } else {
                $donHang->ten_phuong_thuc_thanh_toan = 'Thanh toán khi nhận hàng';
            }

            $cacTrangThai = [
                'chua_thanh_toan' => 'Chưa thanh toán',
                'da_thanh_toan' => 'Đã thanh toán',
                                'da_hoan_tien' => 'Đã hoàn tiền',
            ];

            $trangThaiThanhToan = $donHang->thanhToan->trang_thai;
            if (isset($cacTrangThai[$trangThaiThanhToan])) {
                $donHang->ten_trang_thai_thanh_toan = $cacTrangThai[$trangThaiThanhToan];
            } else {
                $donHang->ten_trang_thai_thanh_toan = $trangThaiThanhToan;
            }

            $donHang->lop_trang_thai_thanh_toan = $this->layLopTrangThaiThanhToan($trangThaiThanhToan);
        }

        foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
            if ($chiTietDonHang->sanPham) {
                $chiTietDonHang->ten_san_pham = $chiTietDonHang->sanPham->ten_hien_thi;
                $chiTietDonHang->hinh_anh_san_pham = $chiTietDonHang->sanPham->duong_dan_hinh_anh;
            } else {
                $chiTietDonHang->ten_san_pham = 'Sản phẩm đã xóa';
                $chiTietDonHang->hinh_anh_san_pham = asset('storage/uploads/products/default.png');
            }

            $chiTietDonHang->thanh_tien = $chiTietDonHang->gia * $chiTietDonHang->so_luong;
        }
    }
    // Lay lop mau hien thi trang thai thanh toan.
    private function layLopTrangThaiThanhToan($trangThai)
    {
        if ($trangThai == 'da_thanh_toan') {
            return 'custom-badge badge badge-success';
        }

        if ($trangThai == 'chua_thanh_toan') {
            return 'custom-badge badge badge-warning';
        }


        if ($trangThai == 'da_hoan_tien') {
            return 'custom-badge badge badge-info';
        }

        return 'custom-badge badge badge-secondary';
    }
    // Lay danh sach san pham trong yeu cau doi tra.
    private function laySanPhamDoiTra($donHang, $yeuCauDoiTra)
    {
        $ketQuas = [];

        if (! $yeuCauDoiTra || ! is_array($yeuCauDoiTra->san_pham)) {
            return $ketQuas;
        }

        foreach ($yeuCauDoiTra->san_pham as $sanPhamDoiTra) {
            $maChiTiet = isset($sanPhamDoiTra['ma_chi_tiet_don_hang'])
                ? $sanPhamDoiTra['ma_chi_tiet_don_hang']
                : null;

            foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
                if ($chiTietDonHang->ma_chi_tiet_don_hang == $maChiTiet) {
                    $ketQuas[] = [
                        'ten_san_pham' =>
                            $chiTietDonHang->ten_san_pham,
                        'so_luong' => isset($sanPhamDoiTra['so_luong'])
                            ? $sanPhamDoiTra['so_luong']
                            : 0,
                    ];
                    break;
                }
            }
        }

        return $ketQuas;
    }

    // Lay danh sach minh chung doi tra de hien thi.
    private function layMinhChungDoiTra($yeuCauDoiTra)
    {
        $ketQuas = [];

        if (! $yeuCauDoiTra || ! is_array($yeuCauDoiTra->minh_chung)) {
            return $ketQuas;
        }

        foreach ($yeuCauDoiTra->minh_chung as $minhChung) {
            $ketQuas[] = [
                'duong_dan' => $this->dichVuMinhChung->layDuongDan($minhChung),
                'ten_tep' => $this->layGiaTriMinhChung(
                    $minhChung,
                    'ten_goc',
                    'original_name',
                    'Minh chứng'
                ),
            ];
        }

        return $ketQuas;
    }

    // Dinh dang ngay gio de hien thi.
    private function dinhDangNgay($thoiGian)
    {
        return $thoiGian ? $thoiGian->format('d/m/Y H:i') : '-';
    }

    // Gui hoa don den email nguoi dung, loi mail khong lam huy xac nhan don.
    private function guiHoaDon($donHang)
    {
        if (! $donHang->nguoiDung || ! $donHang->nguoiDung->email) {
            return false;
        }

        try {
            Mail::to($donHang->nguoiDung->email)
                ->send(new HoaDonDonHangMail($donHang));

            return true;
        } catch (Exception $exception) {
            Log::warning('Không gửi được hóa đơn: '.$exception->getMessage());

            return false;
        }
    }

    // Tra phan hoi thanh cong cho JavaScript.
    private function phanHoiThanhCong($thongBao)
    {
        return response()->json([
            'trang_thai' => true,
            'thong_bao' => $thongBao,
        ]);
    }

    // Tra phan hoi loi cho JavaScript.
    private function phanHoiLoi($thongBao)
    {
        return response()->json([
            'trang_thai' => false,
            'thong_bao' => $thongBao,
        ], 422);
    }
}
