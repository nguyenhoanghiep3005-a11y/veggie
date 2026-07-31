<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\DiaChiGiaoHang;
use App\Models\DonHang;
use App\Models\PhieuGiamGia;
use App\Models\SanPham;
use App\Models\ThanhToan;
use App\Services\GioHangService;
use App\Services\PhiVanChuyenGhnService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThanhToanController extends Controller
{
    private $phiVanChuyenGhn;
    private $gioHang;

    // Khoi tao dich vu gio hang va tinh phi van chuyen.
    public function __construct(
        PhiVanChuyenGhnService $phiVanChuyenGhn,
        GioHangService $gioHang
    ) {
        $this->phiVanChuyenGhn = $phiVanChuyenGhn;
        $this->gioHang = $gioHang;
    }

    // Hien thi trang thanh toan cung dia chi, gio hang va phieu giam gia.
    public function hienThiThanhToan()
    {
        $nguoiDung = Auth::user();
        $sanPhamGioHangs = $this->gioHang->laySanPhamGioHang();

        if (empty($sanPhamGioHangs)) {
            toastr()->error('Giỏ hàng đang trống.');

            return redirect()->route('gio-hang.hien-thi');
        }

        $diaChis = [];
        if ($nguoiDung) {
            $diaChis = DiaChiGiaoHang::where(
                'ma_nguoi_dung',
                $nguoiDung->ma_nguoi_dung
            )->get();
        }

        $diaChiMacDinh = $this->layDiaChiMacDinh($diaChis);
        $coDiaChiDaLuu = count($diaChis) > 0;
        $loaiGiaoHangDaChon = old(
            'loai_giao_hang',
            $coDiaChiDaLuu ? 'tai_khoan' : 'dia_chi_moi'
        );
        $maDiaChiDaChon = old(
            'ma_dia_chi_giao_hang',
            $diaChiMacDinh ? $diaChiMacDinh->ma_dia_chi_giao_hang : ''
        );
        $diaChiDaChon = $this->timDiaChiTrongDanhSach($diaChis, $maDiaChiDaChon);

        if (! $diaChiDaChon) {
            $diaChiDaChon = $diaChiMacDinh;
        }

        $tenNguoiNhan = $diaChiDaChon ? $diaChiDaChon->ho_ten : '';
        $soDienThoaiNguoiNhan = $diaChiDaChon ? $diaChiDaChon->so_dien_thoai : '';
        $diaChiNguoiNhan = $diaChiDaChon ? $diaChiDaChon->dia_chi : '';
        $tinhThanhNguoiNhan = $diaChiDaChon ? $diaChiDaChon->tinh_thanh : '';

        $tamTinh = $this->gioHang->tinhTongTien();
        $phieuGiamGia = $this->layPhieuGiamGiaTrongSession($tamTinh);
        $cacKhoanTien = $this->tinhCacKhoanTien(
            $sanPhamGioHangs,
            null,
            $phieuGiamGia
        );

        $phiVanChuyen = $cacKhoanTien['phi_van_chuyen'];
        $soTienGiam = $cacKhoanTien['so_tien_giam'];
        $tongTien = $cacKhoanTien['tong_tien'];
        $maGiamGia = $phieuGiamGia ? $phieuGiamGia->ma_giam_gia : '';
        $phieuDaNhans = $this->layPhieuGiamGiaDaNhan(
            $nguoiDung,
            $tamTinh,
            $phieuGiamGia
        );

        return view('clients.pages.thanh-toan', compact(
            'diaChis',
            'diaChiMacDinh',
            'diaChiDaChon',
            'coDiaChiDaLuu',
            'loaiGiaoHangDaChon',
            'maDiaChiDaChon',
            'tenNguoiNhan',
            'soDienThoaiNguoiNhan',
            'diaChiNguoiNhan',
            'tinhThanhNguoiNhan',
            'sanPhamGioHangs',
            'tamTinh',
            'phiVanChuyen',
            'soTienGiam',
            'tongTien',
            'phieuGiamGia',
            'maGiamGia',
            'phieuDaNhans'
        ));
    }

    // Lay mot dia chi da luu cua nguoi dung dang dang nhap.
    public function layDiaChi(Request $request)
    {
        $data = $request->validate([
            'ma_dia_chi_giao_hang' => 'required|integer|exists:dia_chi_giao_hang,ma_dia_chi_giao_hang',
        ]);

        $diaChi = $this->timDiaChiNguoiDung(
            Auth::id(),
            $data['ma_dia_chi_giao_hang']
        );

        if (! $diaChi) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Không tìm thấy địa chỉ giao hàng.',
            ], 404);
        }

        return response()->json([
            'trang_thai' => true,
            'du_lieu' => [
                'ma_dia_chi_giao_hang' => $diaChi->ma_dia_chi_giao_hang,
                'ho_ten' => $diaChi->ho_ten,
                'so_dien_thoai' => $diaChi->so_dien_thoai,
                'dia_chi' => $diaChi->dia_chi,
                'tinh_thanh' => $diaChi->tinh_thanh,
                'co_dia_chi_ghn' => $diaChi->coDiaChiGhn(),
            ],
        ]);
    }

    // Kiem tra va luu phieu giam gia dang ap dung vao session.
    public function apDungMaGiamGia(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Vui lòng đăng nhập để sử dụng phiếu giảm giá.',
            ], 403);
        }

        $data = $request->validate([
            'ma_giam_gia' => 'required|string|max:50',
            'ma_dia_chi_giao_hang' => 'nullable|integer',
            'ma_tinh' => 'nullable',
            'ma_huyen' => 'nullable',
            'ma_xa' => 'nullable',
        ]);

        $phieuGiamGia = PhieuGiamGia::where(
            'ma_giam_gia',
            trim($data['ma_giam_gia'])
        )->first();

        $tamTinh = $this->gioHang->tinhTongTien();

        if (! $phieuGiamGia) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Mã giảm giá không tồn tại.',
            ], 422);
        }

        $thongBaoLoi = $phieuGiamGia->kiemTraDieuKienSuDung(
            Auth::id(),
            $tamTinh
        );

        if ($thongBaoLoi) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => $thongBaoLoi,
            ], 422);
        }

        session()->put(
            'phieu_giam_gia_thanh_toan',
            $phieuGiamGia->ma_phieu_giam_gia
        );

        $diaChi = $this->layDiaChiTinhPhiTuRequest($data);
        $cacKhoanTien = $this->tinhCacKhoanTien(
            $this->gioHang->laySanPhamGioHang(),
            $diaChi,
            $phieuGiamGia
        );

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Đã áp dụng mã '.$phieuGiamGia->ma_giam_gia.'.',
            'ma_giam_gia' => $phieuGiamGia->ma_giam_gia,
            'du_lieu' => $cacKhoanTien,
        ]);
    }

    // Tinh lai phi van chuyen va tong tien theo dia chi dang chon.
    public function tinhPhiVanChuyen(Request $request)
    {
        $data = $request->validate([
            'ma_dia_chi_giao_hang' => 'nullable|integer',
            'ma_tinh' => 'required_without:ma_dia_chi_giao_hang',
            'ma_huyen' => 'required_without:ma_dia_chi_giao_hang',
            'ma_xa' => 'required_without:ma_dia_chi_giao_hang',
        ]);

        $diaChi = $this->layDiaChiTinhPhiTuRequest($data);

        if (! $diaChi || ! $diaChi->coDiaChiGhn()) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Vui lòng chọn đầy đủ tỉnh, quận và phường.',
            ], 422);
        }

        $tamTinh = $this->gioHang->tinhTongTien();
        $phieuGiamGia = $this->layPhieuGiamGiaTrongSession($tamTinh);

        try {
            $cacKhoanTien = $this->tinhCacKhoanTien(
                $this->gioHang->laySanPhamGioHang(),
                $diaChi,
                $phieuGiamGia
            );
        } catch (Exception $exception) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'trang_thai' => true,
            'du_lieu' => $cacKhoanTien,
        ]);
    }

    // Dat don thanh toan khi nhan hang.
    public function datHang(Request $request)
    {
        if (! Auth::check() || $request->input('loai_giao_hang') != 'tai_khoan') {
            toastr()->error('Địa chỉ khác chỉ được thanh toán trước bằng PayPal.');

            return redirect()->route('thanh-toan.hien-thi')->withInput();
        }

        $data = $this->kiemTraDuLieuDatHang($request, false);

        try {
            $donHang = $this->luuDonHang($data, 'tien_mat');
        } catch (Exception $exception) {
            Log::error('Lỗi đặt hàng COD: '.$exception->getMessage());
            toastr()->error($exception->getMessage());

            return redirect()->route('thanh-toan.hien-thi')->withInput();
        }

        $this->xoaDuLieuSauKhiDatHang();
        toastr()->success('Đặt hàng thành công.');

        if ($donHang->ma_nguoi_dung) {
            return redirect()->route('tai-khoan.hien-thi');
        }

        return redirect()->route('trang-chu');
    }

    // Luu don sau khi PayPal da xac nhan thanh toan thanh cong.
    public function datHangPayPal(Request $request)
    {
        $data = $this->kiemTraDuLieuDatHang($request, true);

        try {
            $donHang = $this->luuDonHang(
                $data,
                'paypal',
                $data['ma_giao_dich']
            );
        } catch (Exception $exception) {
            Log::error('Lỗi đặt hàng PayPal: '.$exception->getMessage());

            return response()->json([
                'trang_thai' => false,
                'thong_bao' => $exception->getMessage(),
            ], 422);
        }

        $this->xoaDuLieuSauKhiDatHang();

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Thanh toán thành công.',
            'duong_dan_chuyen' => $donHang->ma_nguoi_dung
                ? route('tai-khoan.hien-thi')
                : route('trang-chu'),
        ]);
    }

    // Kiem tra cac truong bat buoc khi dat hang.
    private function kiemTraDuLieuDatHang(Request $request, $laPayPal)
    {
        $quyTac = [
            'loai_giao_hang' => 'required|in:tai_khoan,dia_chi_moi',
            'phuong_thuc' => 'required|in:tien_mat,paypal',
        ];

        if ($laPayPal) {
            $quyTac['ma_giao_dich'] = 'required|string|max:191';
        }

        if ($request->input('loai_giao_hang') == 'tai_khoan' && Auth::check()) {
            $quyTac['ma_dia_chi_giao_hang'] =
                'required|integer|exists:dia_chi_giao_hang,ma_dia_chi_giao_hang';
        } else {
            $quyTac['ho_ten_nguoi_nhan'] = 'required|string|min:2|max:100';
            $quyTac['so_dien_thoai_nguoi_nhan'] = 'required|regex:/^[0-9]{10,11}$/';
            $quyTac['dia_chi_nguoi_nhan'] = 'required|string|min:5|max:191';
            $quyTac['ma_tinh'] = 'required';
            $quyTac['ma_huyen'] = 'required';
            $quyTac['ma_xa'] = 'required';
            $quyTac['ten_tinh'] = 'required|string';
            $quyTac['ten_huyen'] = 'required|string';
            $quyTac['ten_xa'] = 'required|string';
        }

        return $request->validate($quyTac);
    }

    // Luu don hang, chi tiet don, thanh toan va cap nhat ton kho.
    private function luuDonHang($data, $phuongThuc, $maGiaoDich = null)
    {
        $sanPhamGioHangs = $this->gioHang->laySanPhamGioHang();

        if (empty($sanPhamGioHangs)) {
            throw new Exception('Giỏ hàng đang trống.');
        }

        $thongTinDiaChi = $this->taoThongTinDiaChi($data);
        $diaChi = $thongTinDiaChi['dia_chi'];

        if (! $diaChi || ! $diaChi->coDiaChiGhn()) {
            throw new Exception('Địa chỉ giao hàng chưa đầy đủ.');
        }

        DB::beginTransaction();

        try {
            $donHang = DonHang::create([
                'ma_nguoi_dung' => $data['loai_giao_hang'] == 'tai_khoan'
                    ? Auth::id()
                    : null,
                'ma_dia_chi_giao_hang' => $thongTinDiaChi['ma_dia_chi_giao_hang'],
                'du_lieu_dia_chi_giao_hang' => $thongTinDiaChi['du_lieu_dia_chi'],
                'tam_tinh' => 0,
                'phi_van_chuyen' => 0,
                'so_tien_giam' => 0,
                'tong_tien' => 0,
                'trang_thai' => 'cho_xac_nhan',
            ]);

            $tamTinh = $this->taoChiTietDonHang($donHang, $sanPhamGioHangs);
            $phieuGiamGia = $this->layPhieuGiamGiaTrongSession($tamTinh);
            $soTienGiam = 0;

            if ($phieuGiamGia) {
                $soTienGiam = $phieuGiamGia->tinhSoTienGiam($tamTinh);
            }

            $phiVanChuyen = $this->phiVanChuyenGhn->tinhPhiVanChuyen(
                $diaChi,
                $sanPhamGioHangs
            );

            $donHang->tam_tinh = $tamTinh;
            $donHang->phi_van_chuyen = $phiVanChuyen;
            $donHang->so_tien_giam = $soTienGiam;
            $donHang->tong_tien = max(
                0,
                $tamTinh + $phiVanChuyen - $soTienGiam
            );

            if ($phieuGiamGia) {
                $donHang->ma_phieu_giam_gia = $phieuGiamGia->ma_phieu_giam_gia;
                $donHang->ma_giam_gia = $phieuGiamGia->ma_giam_gia;
            }

            $donHang->save();

            ThanhToan::create([
                'ma_don_hang' => $donHang->ma_don_hang,
                'phuong_thuc' => $phuongThuc,
                'ma_giao_dich' => $maGiaoDich,
                'trang_thai' => $phuongThuc == 'paypal'
                    ? 'da_thanh_toan'
                    : 'chua_thanh_toan',
                'thanh_toan_luc' => $phuongThuc == 'paypal' ? now() : null,
                'so_tien' => $donHang->tong_tien,
            ]);

            $this->ghiNhanSuDungPhieuGiamGia($phieuGiamGia);

            DB::commit();

            return $donHang;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    // Tao cac dong chi tiet don hang va tru ton tung san pham.
    private function taoChiTietDonHang($donHang, $sanPhamGioHangs)
    {
        $tamTinh = 0;

        foreach ($sanPhamGioHangs as $sanPhamGioHang) {
            $sanPham = SanPham::find($sanPhamGioHang['ma_san_pham']);
            $soLuong = (int) $sanPhamGioHang['so_luong'];

            if (! $sanPham) {
                throw new Exception('Có sản phẩm không còn tồn tại.');
            }

            if ($sanPham->soLuongCoTheBan() < $soLuong) {
                throw new Exception(
                    'Sản phẩm "'.$sanPham->ten_hien_thi.'" không đủ hàng.'
                );
            }

            $gia = $sanPham->layDonGiaTheoSoLuong($soLuong);
            $phanBoTonKho = $sanPham->truTonKho($soLuong);

            ChiTietDonHang::create([
                'ma_don_hang' => $donHang->ma_don_hang,
                'ma_san_pham' => $sanPham->ma_san_pham,
                'so_luong' => $soLuong,
                'gia' => $gia,
                'phan_bo_ton_kho' => $phanBoTonKho,
            ]);

            $tamTinh += $gia * $soLuong;
        }

        return $tamTinh;
    }

    // Tao doi tuong dia chi va du lieu dia chi luu kem don hang.
    private function taoThongTinDiaChi($data)
    {
        if ($data['loai_giao_hang'] == 'tai_khoan' && Auth::check()) {
            $diaChi = $this->timDiaChiNguoiDung(
                Auth::id(),
                $data['ma_dia_chi_giao_hang']
            );

            if (! $diaChi) {
                throw new Exception('Địa chỉ giao hàng không hợp lệ.');
            }

            return [
                'dia_chi' => $diaChi,
                'ma_dia_chi_giao_hang' => $diaChi->ma_dia_chi_giao_hang,
                'du_lieu_dia_chi' => $this->taoMangDiaChi($diaChi),
            ];
        }

        $diaChi = new DiaChiGiaoHang([
            'ho_ten' => $data['ho_ten_nguoi_nhan'],
            'so_dien_thoai' => $data['so_dien_thoai_nguoi_nhan'],
            'dia_chi' => $data['dia_chi_nguoi_nhan'],
            'tinh_thanh' => $data['ten_xa'].', '.$data['ten_huyen'].', '.$data['ten_tinh'],
            'ma_tinh' => $data['ma_tinh'],
            'ma_huyen' => $data['ma_huyen'],
            'ma_xa' => $data['ma_xa'],
        ]);

        return [
            'dia_chi' => $diaChi,
            'ma_dia_chi_giao_hang' => null,
            'du_lieu_dia_chi' => $this->taoMangDiaChi($diaChi),
        ];
    }

    // Chuyen doi tuong dia chi thanh mang de luu cung don hang.
    private function taoMangDiaChi($diaChi)
    {
        return [
            'ho_ten' => $diaChi->ho_ten,
            'so_dien_thoai' => $diaChi->so_dien_thoai,
            'dia_chi' => $diaChi->dia_chi,
            'tinh_thanh' => $diaChi->tinh_thanh,
            'ma_tinh' => $diaChi->ma_tinh,
            'ma_huyen' => $diaChi->ma_huyen,
            'ma_xa' => $diaChi->ma_xa,
        ];
    }

    // Tinh tam tinh, phi van chuyen, tien giam va tong thanh toan.
    private function tinhCacKhoanTien(
        $sanPhamGioHangs,
        $diaChi,
        $phieuGiamGia
    ) {
        $tamTinh = 0;

        foreach ($sanPhamGioHangs as $sanPhamGioHang) {
            $tamTinh += $sanPhamGioHang['tam_tinh'];
        }

        $phiVanChuyen = 0;
        if ($diaChi && $diaChi->coDiaChiGhn()) {
            $phiVanChuyen = $this->phiVanChuyenGhn->tinhPhiVanChuyen(
                $diaChi,
                $sanPhamGioHangs
            );
        }

        $soTienGiam = 0;
        if ($phieuGiamGia) {
            $soTienGiam = $phieuGiamGia->tinhSoTienGiam($tamTinh);
        }

        return [
            'tam_tinh' => $tamTinh,
            'phi_van_chuyen' => $phiVanChuyen,
            'so_tien_giam' => $soTienGiam,
            'tong_tien' => max(
                0,
                $tamTinh + $phiVanChuyen - $soTienGiam
            ),
        ];
    }

    // Lay dia chi da luu hoac dia chi moi tu du lieu yeu cau.
    private function layDiaChiTinhPhiTuRequest($data)
    {
        if (! empty($data['ma_dia_chi_giao_hang']) && Auth::check()) {
            return $this->timDiaChiNguoiDung(
                Auth::id(),
                $data['ma_dia_chi_giao_hang']
            );
        }

        if (
            ! empty($data['ma_tinh'])
            && ! empty($data['ma_huyen'])
            && ! empty($data['ma_xa'])
        ) {
            return new DiaChiGiaoHang([
                'ma_tinh' => $data['ma_tinh'],
                'ma_huyen' => $data['ma_huyen'],
                'ma_xa' => $data['ma_xa'],
            ]);
        }

        return null;
    }

    // Tim dia chi thuoc dung nguoi dung dang dang nhap.
    private function timDiaChiNguoiDung($maNguoiDung, $maDiaChi)
    {
        if (! $maNguoiDung || ! $maDiaChi) {
            return null;
        }

        return DiaChiGiaoHang::where(
            'ma_dia_chi_giao_hang',
            $maDiaChi
        )->where('ma_nguoi_dung', $maNguoiDung)->first();
    }

    // Tim dia chi theo ma trong danh sach da lay.
    private function timDiaChiTrongDanhSach($diaChis, $maDiaChi)
    {
        foreach ($diaChis as $diaChi) {
            if ($diaChi->ma_dia_chi_giao_hang == $maDiaChi) {
                return $diaChi;
            }
        }

        return null;
    }

    // Lay dia chi mac dinh, neu khong co thi lay dia chi dau tien.
    private function layDiaChiMacDinh($diaChis)
    {
        $diaChiDauTien = null;

        foreach ($diaChis as $diaChi) {
            if (! $diaChiDauTien) {
                $diaChiDauTien = $diaChi;
            }

            if ($diaChi->mac_dinh) {
                return $diaChi;
            }
        }

        return $diaChiDauTien;
    }

    // Lay phieu giam gia trong session neu phieu van con su dung duoc.
    private function layPhieuGiamGiaTrongSession($tamTinh)
    {
        if (! Auth::check()) {
            return null;
        }

        $maPhieuGiamGia = session('phieu_giam_gia_thanh_toan');

        if (! $maPhieuGiamGia) {
            return null;
        }

        $phieuGiamGia = PhieuGiamGia::find($maPhieuGiamGia);

        if (
            ! $phieuGiamGia
            || $phieuGiamGia->kiemTraDieuKienSuDung(Auth::id(), $tamTinh)
        ) {
            session()->forget('phieu_giam_gia_thanh_toan');

            return null;
        }

        return $phieuGiamGia;
    }

    // Lay va dinh dang danh sach phieu giam gia nguoi dung da nhan.
    private function layPhieuGiamGiaDaNhan(
        $nguoiDung,
        $tamTinh,
        $phieuDangChon
    ) {
        $phieuDaNhans = [];

        if (! $nguoiDung) {
            return $phieuDaNhans;
        }

        $cacPhieu = $nguoiDung->phieuGiamGias()->get();

        foreach ($cacPhieu as $phieuDaNhan) {
            if ($phieuDaNhan->pivot->ngay_su_dung) {
                continue;
            }

            $phieuDaNhan->co_the_ap_dung = $phieuDaNhan->coTheSuDung(
                $nguoiDung->ma_nguoi_dung,
                $tamTinh
            );
            $phieuDaNhan->dang_duoc_chon = $phieuDangChon
                && $phieuDangChon->ma_phieu_giam_gia
                    == $phieuDaNhan->ma_phieu_giam_gia;
            $phieuDaNhan->phan_tram_giam_hien_thi =
                number_format($phieuDaNhan->phan_tram_giam, 0);
            $phieuDaNhan->don_toi_thieu_hien_thi =
                'Đơn tối thiểu '
                .number_format($phieuDaNhan->gia_tri_don_toi_thieu, 0, ',', '.')
                .' đ';
            $phieuDaNhan->giam_toi_da_hien_thi =
                $phieuDaNhan->so_tien_giam_toi_da
                    ? 'Giảm tối đa '
                        .number_format($phieuDaNhan->so_tien_giam_toi_da, 0, ',', '.')
                        .' đ'
                    : 'Không giới hạn mức giảm';
            $phieuDaNhan->het_han_hien_thi =
                $phieuDaNhan->het_han_luc
                    ? $phieuDaNhan->het_han_luc->format('d/m/Y H:i')
                    : 'Không giới hạn';

            $phieuDaNhans[] = $phieuDaNhan;
        }

        return $phieuDaNhans;
    }

    // Cap nhat so luot va danh dau phieu giam gia da duoc su dung.
    private function ghiNhanSuDungPhieuGiamGia($phieuGiamGia)
    {
        if (! $phieuGiamGia || ! Auth::check()) {
            return;
        }

        $phieuGiamGia->so_lan_da_dung =
            (int) $phieuGiamGia->so_lan_da_dung + 1;
        $phieuGiamGia->save();

        Auth::user()->phieuGiamGias()->updateExistingPivot(
            $phieuGiamGia->ma_phieu_giam_gia,
            ['ngay_su_dung' => now()]
        );
    }

    // Xoa gio hang va phieu giam gia sau khi dat hang thanh cong.
    private function xoaDuLieuSauKhiDatHang()
    {
        $this->gioHang->xoaGioHang();
        session()->forget('phieu_giam_gia_thanh_toan');
    }
}
