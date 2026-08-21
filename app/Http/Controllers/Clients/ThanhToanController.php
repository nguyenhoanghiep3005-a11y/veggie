<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Mail\HoaDonDonHangMail;
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
use Illuminate\Support\Facades\Mail;

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

        if (count($sanPhamGioHangs) == 0) {
            toastr()->error('Giỏ hàng đang trống.');

            return redirect()->route('trang-chu');
        }

        $diaChis = $this->layDanhSachDiaChiNguoiDung($nguoiDung);

        $diaChiMacDinh = $this->layDiaChiMacDinh($diaChis);
        $coDiaChiDaLuu = count($diaChis) > 0;

        $loaiGiaoHangMacDinh = 'dia_chi_moi';
        if ($coDiaChiDaLuu) {
            $loaiGiaoHangMacDinh = 'tai_khoan';
        }

        $maDiaChiMacDinh = '';
        if ($diaChiMacDinh) {
            $maDiaChiMacDinh = $diaChiMacDinh->ma_dia_chi_giao_hang;
        }

        $loaiGiaoHangDaChon = $loaiGiaoHangMacDinh;
        $maDiaChiDaChon = $maDiaChiMacDinh;
        $diaChiDaChon = $diaChiMacDinh;

        $tenNguoiNhan = '';
        $soDienThoaiNguoiNhan = '';
        $diaChiNguoiNhan = '';
        $tinhThanhNguoiNhan = '';

        if ($diaChiDaChon) {
            $tenNguoiNhan = $diaChiDaChon->ho_ten;
            $soDienThoaiNguoiNhan = $diaChiDaChon->so_dien_thoai;
            $diaChiNguoiNhan = $diaChiDaChon->dia_chi;
            $tinhThanhNguoiNhan = $diaChiDaChon->tinh_thanh;
        }

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
        $maGiamGia = '';

        if ($phieuGiamGia) {
            $maGiamGia = $phieuGiamGia->ma_giam_gia;
        }

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
            'ma_tinh' => 'nullable',
            'ma_huyen' => 'nullable',
            'ma_xa' => 'nullable',
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
        if (! Auth::check()) {
            toastr()->error('Vui lòng đăng nhập để thanh toán khi nhận hàng.');

            return redirect()->route('thanh-toan.hien-thi')->withInput();
        }

        if ($request->input('loai_giao_hang') != 'tai_khoan') {
            toastr()->error('Địa chỉ khác chỉ được thanh toán trước bằng PayPal.');

            return redirect()->route('thanh-toan.hien-thi')->withInput();
        }

        $data = $request->validate([
            'loai_giao_hang' => 'required|in:tai_khoan',
            'ma_dia_chi_giao_hang' => 'required|integer|exists:dia_chi_giao_hang,ma_dia_chi_giao_hang',
            'phuong_thuc' => 'required|in:tien_mat',
        ]);

        try {
            DB::beginTransaction();

            $sanPhamGioHangs = $this->gioHang->laySanPhamGioHang();

            if (count($sanPhamGioHangs) == 0) {
                throw new Exception('Giỏ hàng đang trống.');
            }

            $diaChi = DiaChiGiaoHang::where('ma_dia_chi_giao_hang', $data['ma_dia_chi_giao_hang'])
                ->where('ma_nguoi_dung', Auth::id())
                ->first();

            if (! $diaChi || ! $diaChi->coDiaChiGhn()) {
                throw new Exception('Địa chỉ giao hàng chưa đầy đủ.');
            }
            $diaChiDonHang = $this->taoDiaChiChoDonHang($diaChi);

            $donHang = DonHang::create([
                'ma_nguoi_dung' => Auth::id(),
                'ma_dia_chi_giao_hang' => $diaChiDonHang->ma_dia_chi_giao_hang,
                'tam_tinh' => 0,
                'phi_van_chuyen' => 0,
                'so_tien_giam' => 0,
                'tong_tien' => 0,
                'trang_thai' => 'cho_xac_nhan',
            ]);

            $tamTinh = 0;

            foreach ($sanPhamGioHangs as $sanPhamGioHang) {
                $sanPham = SanPham::find($sanPhamGioHang['ma_san_pham']);
                $soLuong = (int) $sanPhamGioHang['so_luong'];

                if (! $sanPham) {
                    throw new Exception('Có sản phẩm không còn tồn tại.');
                }

                if ($soLuong > $sanPham->soLuongCoTheBan()) {
                    throw new Exception('Sản phẩm "'.$sanPham->ten_hien_thi.'" không đủ hàng.');
                }

                $gia = (float) $sanPham->gia_hien_tai;
                $phanBoTonKhos = $sanPham->truTonKho($soLuong);

                foreach ($phanBoTonKhos as $phanBoTonKho) {
                    ChiTietDonHang::create([
                        'ma_don_hang' => $donHang->ma_don_hang,
                        'ma_san_pham' => $sanPham->ma_san_pham,
                        'ma_lo_hang_kho' => $phanBoTonKho['ma_lo_hang_kho'],
                        'so_luong' => $phanBoTonKho['so_luong'],
                        'gia' => $gia,
                    ]);
                }

                $tamTinh += $gia * $soLuong;
            }

            $phieuGiamGia = $this->layPhieuGiamGiaTrongSession($tamTinh);
            $soTienGiam = 0;

            if ($phieuGiamGia) {
                $soTienGiam = $phieuGiamGia->tinhSoTienGiam($tamTinh);
            }

            $phiVanChuyen = $this->phiVanChuyenGhn->tinhPhiVanChuyen($diaChi, $sanPhamGioHangs);
            $tongTien = $tamTinh + $phiVanChuyen - $soTienGiam;

            if ($tongTien < 0) {
                $tongTien = 0;
            }

            $donHang->tam_tinh = $tamTinh;
            $donHang->phi_van_chuyen = $phiVanChuyen;
            $donHang->so_tien_giam = $soTienGiam;
            $donHang->tong_tien = $tongTien;

            if ($phieuGiamGia) {
                $donHang->ma_phieu_giam_gia = $phieuGiamGia->ma_phieu_giam_gia;
                $donHang->ma_giam_gia = $phieuGiamGia->ma_giam_gia;
            }

            $donHang->save();

            ThanhToan::create([
                'ma_don_hang' => $donHang->ma_don_hang,
                'phuong_thuc' => 'tien_mat',
                'ma_giao_dich' => null,
                'trang_thai' => 'chua_thanh_toan',
                'thanh_toan_luc' => null,
                'so_tien' => $tongTien,
            ]);

            if ($phieuGiamGia) {
                $phieuGiamGia->so_lan_da_dung = (int) $phieuGiamGia->so_lan_da_dung + 1;
                $phieuGiamGia->save();

                DB::table('nguoi_dung_phieu_giam_gia')
                    ->where('ma_nguoi_dung', Auth::id())
                    ->where('ma_phieu_giam_gia', $phieuGiamGia->ma_phieu_giam_gia)
                    ->update(['ngay_su_dung' => now()]);
            }

            $this->gioHang->xoaGioHang();
            session()->forget('phieu_giam_gia_thanh_toan');

            DB::commit();

            toastr()->success('Đặt hàng thành công.');

            return redirect()->route('tai-khoan.hien-thi');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Lỗi đặt hàng COD: '.$exception->getMessage());
            toastr()->error($exception->getMessage());

            return redirect()->route('thanh-toan.hien-thi')->withInput();
        }
    }

    // Luu don sau khi PayPal da xac nhan thanh toan thanh cong.
    public function datHangPayPal(Request $request)
    {
        $quyTac = [
            'loai_giao_hang' => 'required|in:tai_khoan,dia_chi_moi',
            'phuong_thuc' => 'required|in:paypal',
            'ma_giao_dich' => 'required|string|max:191',
        ];

        if ($request->input('loai_giao_hang') == 'tai_khoan' && Auth::check()) {
            $quyTac['ma_dia_chi_giao_hang'] =
                'required|integer|exists:dia_chi_giao_hang,ma_dia_chi_giao_hang';
        } else {
            $quyTac['ho_ten_nguoi_nhan'] = 'required|string|min:2|max:100';
            $quyTac['so_dien_thoai_nguoi_nhan'] = 'required|regex:/^0[0-9]{9,10}$/';
            $quyTac['dia_chi_nguoi_nhan'] = 'required|string|min:5|max:191';
            $quyTac['email_nhan_hoa_don'] = 'required|email|max:255';
            $quyTac['ma_tinh'] = 'required';
            $quyTac['ma_huyen'] = 'required';
            $quyTac['ma_xa'] = 'required';
            $quyTac['ten_tinh'] = 'required|string';
            $quyTac['ten_huyen'] = 'required|string';
            $quyTac['ten_xa'] = 'required|string';
        }

        $data = $request->validate($quyTac);

        try {
            DB::beginTransaction();

            $sanPhamGioHangs = $this->gioHang->laySanPhamGioHang();

            if (count($sanPhamGioHangs) == 0) {
                throw new Exception('Giỏ hàng đang trống.');
            }

            $maNguoiDung = null;

            if ($data['loai_giao_hang'] == 'tai_khoan' && Auth::check()) {
                $diaChi = DiaChiGiaoHang::where('ma_dia_chi_giao_hang', $data['ma_dia_chi_giao_hang'])
                    ->where('ma_nguoi_dung', Auth::id())
                    ->first();

                if (! $diaChi) {
                    throw new Exception('Địa chỉ giao hàng không hợp lệ.');
                }

                $maNguoiDung = Auth::id();
            } else {
                $diaChi = new DiaChiGiaoHang;
                $diaChi->ho_ten = $data['ho_ten_nguoi_nhan'];
                $diaChi->so_dien_thoai = $data['so_dien_thoai_nguoi_nhan'];
                $diaChi->dia_chi = $data['dia_chi_nguoi_nhan'];
                $diaChi->tinh_thanh = $data['ten_xa'].', '.$data['ten_huyen'].', '.$data['ten_tinh'];
                $diaChi->ma_tinh = $data['ma_tinh'];
                $diaChi->ma_huyen = $data['ma_huyen'];
                $diaChi->ma_xa = $data['ma_xa'];
            }

            if (! $diaChi || ! $diaChi->coDiaChiGhn()) {
                throw new Exception('Địa chỉ giao hàng chưa đầy đủ.');
            }
            $diaChiDonHang = $this->taoDiaChiChoDonHang($diaChi);

            $donHang = DonHang::create([
                'ma_nguoi_dung' => $maNguoiDung,
                'ma_dia_chi_giao_hang' => $diaChiDonHang->ma_dia_chi_giao_hang,
                'tam_tinh' => 0,
                'phi_van_chuyen' => 0,
                'so_tien_giam' => 0,
                'tong_tien' => 0,
                'trang_thai' => 'cho_xac_nhan',
            ]);

            $tamTinh = 0;

            foreach ($sanPhamGioHangs as $sanPhamGioHang) {
                $sanPham = SanPham::find($sanPhamGioHang['ma_san_pham']);
                $soLuong = (int) $sanPhamGioHang['so_luong'];

                if (! $sanPham) {
                    throw new Exception('Có sản phẩm không còn tồn tại.');
                }

                if ($soLuong > $sanPham->soLuongCoTheBan()) {
                    throw new Exception('Sản phẩm "'.$sanPham->ten_hien_thi.'" không đủ hàng.');
                }

                $gia = (float) $sanPham->gia_hien_tai;
                $phanBoTonKhos = $sanPham->truTonKho($soLuong);

                foreach ($phanBoTonKhos as $phanBoTonKho) {
                    ChiTietDonHang::create([
                        'ma_don_hang' => $donHang->ma_don_hang,
                        'ma_san_pham' => $sanPham->ma_san_pham,
                        'ma_lo_hang_kho' => $phanBoTonKho['ma_lo_hang_kho'],
                        'so_luong' => $phanBoTonKho['so_luong'],
                        'gia' => $gia,
                    ]);
                }

                $tamTinh += $gia * $soLuong;
            }

            $phieuGiamGia = $this->layPhieuGiamGiaTrongSession($tamTinh);
            $soTienGiam = 0;

            if ($phieuGiamGia) {
                $soTienGiam = $phieuGiamGia->tinhSoTienGiam($tamTinh);
            }

            $phiVanChuyen = $this->phiVanChuyenGhn->tinhPhiVanChuyen($diaChi, $sanPhamGioHangs);
            $tongTien = $tamTinh + $phiVanChuyen - $soTienGiam;

            if ($tongTien < 0) {
                $tongTien = 0;
            }

            $donHang->tam_tinh = $tamTinh;
            $donHang->phi_van_chuyen = $phiVanChuyen;
            $donHang->so_tien_giam = $soTienGiam;
            $donHang->tong_tien = $tongTien;

            if ($phieuGiamGia) {
                $donHang->ma_phieu_giam_gia = $phieuGiamGia->ma_phieu_giam_gia;
                $donHang->ma_giam_gia = $phieuGiamGia->ma_giam_gia;
            }

            $donHang->save();

            ThanhToan::create([
                'ma_don_hang' => $donHang->ma_don_hang,
                'phuong_thuc' => 'paypal',
                'ma_giao_dich' => $data['ma_giao_dich'],
                'trang_thai' => 'da_thanh_toan',
                'thanh_toan_luc' => now(),
                'so_tien' => $tongTien,
            ]);

            if ($phieuGiamGia && Auth::check()) {
                $phieuGiamGia->so_lan_da_dung = (int) $phieuGiamGia->so_lan_da_dung + 1;
                $phieuGiamGia->save();

                DB::table('nguoi_dung_phieu_giam_gia')
                    ->where('ma_nguoi_dung', Auth::id())
                    ->where('ma_phieu_giam_gia', $phieuGiamGia->ma_phieu_giam_gia)
                    ->update(['ngay_su_dung' => now()]);
            }

            $this->gioHang->xoaGioHang();
            session()->forget('phieu_giam_gia_thanh_toan');

            DB::commit();

            $this->guiHoaDonDenEmailNhanHoaDon($donHang, $data['email_nhan_hoa_don'] ?? null);

            $duongDanChuyen = route('trang-chu');
            if ($donHang->ma_nguoi_dung) {
                $duongDanChuyen = route('tai-khoan.hien-thi');
            }

            return response()->json([
                'trang_thai' => true,
                'thong_bao' => 'Thanh toán thành công.',
                'duong_dan_chuyen' => $duongDanChuyen,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Lỗi đặt hàng PayPal: '.$exception->getMessage());

            return response()->json([
                'trang_thai' => false,
                'thong_bao' => $exception->getMessage(),
            ], 422);
        }
    }

    // Lay danh sach dia chi da luu cua nguoi dung.
    private function layDanhSachDiaChiNguoiDung($nguoiDung)
    {
        if (! $nguoiDung) {
            return [];
        }

        return DiaChiGiaoHang::where(
            'ma_nguoi_dung',
            $nguoiDung->ma_nguoi_dung
        )->get();
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
        $tongTien = $tamTinh + $phiVanChuyen - $soTienGiam;

        if ($tongTien < 0) {
            $tongTien = 0;
        }

        return [
            'tam_tinh' => $tamTinh,
            'phi_van_chuyen' => $phiVanChuyen,
            'so_tien_giam' => $soTienGiam,
            'tong_tien' => $tongTien,
        ];
    }

    // Lay dia chi da luu hoac dia chi moi tu du lieu yeu cau.

    private function layDiaChiTinhPhiTuRequest($data)
    {
        $coMaDiaChi = isset($data['ma_dia_chi_giao_hang'])
            && $data['ma_dia_chi_giao_hang'] != '';

        if ($coMaDiaChi && Auth::check()) {
            return $this->timDiaChiNguoiDung(
                Auth::id(),
                $data['ma_dia_chi_giao_hang']
            );
        }

        $coTinh = isset($data['ma_tinh']) && $data['ma_tinh'] != '';
        $coHuyen = isset($data['ma_huyen']) && $data['ma_huyen'] != '';
        $coXa = isset($data['ma_xa']) && $data['ma_xa'] != '';

        if ($coTinh && $coHuyen && $coXa) {
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

    // Tao ban ghi dia chi rieng de don hang chi luu khoa ngoai.
    private function taoDiaChiChoDonHang($diaChi)
    {
        return DiaChiGiaoHang::create([
            'ma_nguoi_dung' => null,
            'ho_ten' => $diaChi->ho_ten,
            'so_dien_thoai' => $diaChi->so_dien_thoai,
            'dia_chi' => $diaChi->dia_chi,
            'tinh_thanh' => $diaChi->tinh_thanh,
            'ma_tinh' => $diaChi->ma_tinh,
            'ma_huyen' => $diaChi->ma_huyen,
            'ma_xa' => $diaChi->ma_xa,
            'mac_dinh' => false,
        ]);
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

            $phieuDaNhan->dang_duoc_chon = false;
            if (
                $phieuDangChon
                && $phieuDangChon->ma_phieu_giam_gia
                    == $phieuDaNhan->ma_phieu_giam_gia
            ) {
                $phieuDaNhan->dang_duoc_chon = true;
            }

            $phieuDaNhan->phan_tram_giam_hien_thi =
                number_format($phieuDaNhan->phan_tram_giam, 0);
            $phieuDaNhan->don_toi_thieu_hien_thi =
                'Đơn tối thiểu '
                .number_format($phieuDaNhan->gia_tri_don_toi_thieu, 0, ',', '.')
                .' đ';

            $phieuDaNhan->giam_toi_da_hien_thi = 'Không giới hạn mức giảm';
            if ($phieuDaNhan->so_tien_giam_toi_da) {
                $phieuDaNhan->giam_toi_da_hien_thi =
                    'Giảm tối đa '
                    .number_format($phieuDaNhan->so_tien_giam_toi_da, 0, ',', '.')
                    .' đ';
            }

            $phieuDaNhan->het_han_hien_thi = 'Không giới hạn';
            if ($phieuDaNhan->het_han_luc) {
                $phieuDaNhan->het_han_hien_thi =
                    $phieuDaNhan->het_han_luc->format('d/m/Y H:i');
            }

            $phieuDaNhans[] = $phieuDaNhan;
        }

        return $phieuDaNhans;
    }

    // Gui hoa don den email khach nhap khi chon dia chi giao hang khac.
    private function guiHoaDonDenEmailNhanHoaDon($donHang, $emailNhanHoaDon)
    {
        if (! $emailNhanHoaDon) {
            return false;
        }

        app()->terminating(function () use ($donHang, $emailNhanHoaDon) {
            try {
                Mail::to($emailNhanHoaDon)
                    ->send(new HoaDonDonHangMail($donHang));
            } catch (Exception $exception) {
                Log::warning('Khong gui duoc hoa don den email nhan hoa don: '.$exception->getMessage());
            }
        });

        return true;
    }
}
