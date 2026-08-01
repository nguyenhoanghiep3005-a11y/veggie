<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonDatNhap;
use App\Models\ChiTietPhieuNhap;
use App\Models\DonDatNhap;
use App\Models\LoHangKho;
use App\Models\NhaCungCap;
use App\Models\PhieuHangHu;
use App\Models\PhieuNhap;
use App\Models\SanPham;
use App\Services\MinhChungService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NhapKhoController extends Controller
{
    protected $dichVuLuuTru;

    // Gán dịch vụ lưu ảnh và video minh chứng hàng hư.
    public function __construct(MinhChungService $dichVuLuuTru)
    {
        $this->dichVuLuuTru = $dichVuLuuTru;
    }

    // Hiển thị danh sách đơn đặt nhập.
    public function hienThiDanhSachDonDatNhap()
    {
        $donDatNhaps = DonDatNhap::with(['nhaCungCap', 'chiTietDonDatNhaps.sanPham'])
            ->orderBy('ma_don_dat_nhap', 'desc')
            ->paginate(15);

        foreach ($donDatNhaps as $donDatNhap) {
            $this->chuanBiDonDatNhapDeHienThi($donDatNhap);
        }

        return view('admin.pages.don-dat-nhap', compact('donDatNhaps'));
    }

    // Hiển thị form thêm đơn đặt nhập.
    public function hienThiFormThemDonDatNhap()
    {
        $nhaCungCaps = NhaCungCap::orderBy('ten')->get();
        $sanPhams = SanPham::orderBy('ten')->orderBy('don_vi')->get();
        $luaChonSanPhams = [];
        $soDon = DonDatNhap::taoSoDon();

        foreach ($sanPhams as $sanPham) {
            $luaChonSanPhams[] = [
                'ma_san_pham' => $sanPham->ma_san_pham,
                'ten_san_pham' => $sanPham->ten_hien_thi,
            ];
        }

        return view(
            'admin.pages.them-don-dat-nhap',
            compact('nhaCungCaps', 'luaChonSanPhams', 'soDon')
        );
    }

    // Kiểm tra và lưu đơn đặt nhập mới.
    public function themDonDatNhap(Request $request)
    {
        $data = $request->validate([
            'ma_nha_cung_cap' => 'required|integer|exists:nha_cung_cap,ma_nha_cung_cap',
            'ngay_dat' => 'required|date',
            'chi_tiets' => 'required|array|min:1',
            'chi_tiets.*.ma_san_pham' => 'required|integer|exists:san_pham,ma_san_pham',
            'chi_tiets.*.so_luong_dat' => 'required|integer|min:1',
        ], [
            'ma_nha_cung_cap.required' => 'Vui lòng chọn nhà cung cấp.',
            'chi_tiets.required' => 'Vui lòng thêm ít nhất một sản phẩm.',
        ]);

        if ($this->coSanPhamTrung($data['chi_tiets'])) {
            return back()->withInput()->with('error', 'Mỗi sản phẩm chỉ được xuất hiện một lần.');
        }

        try {
            DB::beginTransaction();

            $donDatNhap = DonDatNhap::create([
                'so_don' => DonDatNhap::taoSoDon(),
                'ma_nha_cung_cap' => $data['ma_nha_cung_cap'],
                'trang_thai' => 'cho_nhap_hang',
                'ghi_chu' => null,
                'ngay_dat' => $data['ngay_dat'],
            ]);

            foreach ($data['chi_tiets'] as $chiTiet) {
                ChiTietDonDatNhap::create([
                    'ma_don_dat_nhap' => $donDatNhap->ma_don_dat_nhap,
                    'ma_san_pham' => $chiTiet['ma_san_pham'],
                    'so_luong_dat' => (int) $chiTiet['so_luong_dat'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.don-dat-nhap.danh-sach')
                ->with('success', 'Đã tạo đơn đặt nhập '.$donDatNhap->so_don.'.');
        } catch (Exception $exception) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Không thể tạo đơn đặt nhập: '.$exception->getMessage());
        }
    }

    // Hiển thị chi tiết đơn đặt nhập.
    public function hienThiChiTietDonDatNhap($maDonDatNhap)
    {
        $donDatNhap = DonDatNhap::with([
            'nhaCungCap',
            'chiTietDonDatNhaps.sanPham',
            'phieuNhaps.chiTietPhieuNhaps.sanPham',
            'phieuHangHus.chiTietPhieuHangHus.sanPham',
            'phieuHangHus.minhChungs',
        ])->findOrFail($maDonDatNhap);

        $this->chuanBiDonDatNhapDeHienThi($donDatNhap);
        $this->chuanBiChiTietDonDatNhap($donDatNhap);

        return view('admin.pages.chi-tiet-don-dat-nhap', compact('donDatNhap'));
    }

    // Hiển thị form nhận hàng của đơn đặt nhập.
    public function hienThiFormNhapKho($maDonDatNhap)
    {
        $donDatNhap = DonDatNhap::with(['nhaCungCap', 'chiTietDonDatNhaps.sanPham'])
            ->findOrFail($maDonDatNhap);

        if ($donDatNhap->trang_thai != 'cho_nhap_hang') {
            return redirect()->route('admin.don-dat-nhap.danh-sach')
                ->with('error', 'Đơn đặt nhập này đã được xử lý.');
        }

        $this->chuanBiDonDatNhapDeHienThi($donDatNhap);
        $this->chuanBiFormNhapKho($donDatNhap);

        return view('admin.pages.nhap-kho', compact('donDatNhap'));
    }

    // Nhận hàng, tạo phiếu nhập, cộng tồn kho và ghi nhận hàng hư.
    public function nhapKho(Request $request, $maDonDatNhap)
    {
        $data = $request->validate([
            'mo_ta_hang_loi' => 'nullable|string|max:3000',
            'minh_chungs' => 'nullable|array',
            'minh_chungs.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
            'chi_tiets' => 'required|array|min:1',
            'chi_tiets.*.ma_chi_tiet_don_dat_nhap' => 'required|integer|exists:chi_tiet_don_dat_nhap,ma_chi_tiet_don_dat_nhap',
            'chi_tiets.*.so_luong_nhan' => 'required|integer|min:0',
            'chi_tiets.*.so_luong_tu_choi' => 'required|integer|min:0',
            'chi_tiets.*.ngay_san_xuat' => 'nullable|date',
            'chi_tiets.*.han_su_dung' => 'nullable|date',
        ]);

        $donDatNhap = DonDatNhap::with('chiTietDonDatNhaps.sanPham')->findOrFail($maDonDatNhap);
        $lois = $this->kiemTraChiTietNhapKho($donDatNhap, $data['chi_tiets']);
        $tongSoLuongTuChoi = $this->tinhTongSoLuongTuChoi($data['chi_tiets']);
        $tepMinhChungs = $this->layTepMinhChung($request);

        if ($tongSoLuongTuChoi > 0 && trim((string) $data['mo_ta_hang_loi']) == '') {
            $lois['mo_ta_hang_loi'] = 'Vui lòng nhập lý do hàng bị từ chối.';
        }

        if ($tongSoLuongTuChoi > 0 && count($tepMinhChungs) == 0) {
            $lois['minh_chungs'] = 'Hàng bị từ chối phải có ảnh hoặc video minh chứng.';
        }

        if (count($lois) > 0) {
            return back()->withInput()->withErrors($lois);
        }

        $duongDanDaLuu = [];

        try {
            DB::beginTransaction();

            $donDatNhap = DonDatNhap::with('chiTietDonDatNhaps.sanPham')
                ->findOrFail($maDonDatNhap);

            if ($donDatNhap->trang_thai != 'cho_nhap_hang') {
                throw new Exception('Đơn đặt nhập đã được xử lý.');
            }

            $phieuNhap = $this->taoPhieuNhap($donDatNhap);
            $hangBiTuChois = $this->luuChiTietNhapKho(
                $donDatNhap,
                $phieuNhap,
                $data['chi_tiets']
            );
            $minhChungs = [];

            if ($tongSoLuongTuChoi > 0) {
                $minhChungs = $this->dichVuLuuTru->taiNhieuTepLen(
                    $tepMinhChungs,
                    config('cloudinary.folders.damage_slips'),
                    $duongDanDaLuu
                );
            }

            if (count($hangBiTuChois) > 0) {
                $phieuHangHu = $this->taoPhieuHangHu(
                    $donDatNhap,
                    $phieuNhap,
                    $data['mo_ta_hang_loi']
                );

                foreach ($hangBiTuChois as $hangBiTuChoi) {
                    $phieuHangHu->chiTietPhieuHangHus()->create($hangBiTuChoi);
                }

                $this->luuMinhChungPhieuHangHu($phieuHangHu, $minhChungs);
            }

            $this->hoanTatDonDatNhap(
                $donDatNhap,
                $tongSoLuongTuChoi,
                $data['mo_ta_hang_loi']
            );

            DB::commit();

            return redirect()->route('admin.phieu-nhap.chi-tiet', $phieuNhap)
                ->with('success', 'Đã nhập hàng và cập nhật tồn kho.');
        } catch (Exception $exception) {
            DB::rollBack();

            foreach ($duongDanDaLuu as $duongDan) {
                Storage::disk('public')->delete($duongDan);
            }

            return back()->withInput()->with('error', 'Không thể nhập hàng: '.$exception->getMessage());
        }
    }

    // Hiển thị danh sách phiếu nhập.
    public function hienThiDanhSachPhieuNhap()
    {
        $phieuNhaps = PhieuNhap::with(['nhaCungCap', 'donDatNhap', 'chiTietPhieuNhaps.sanPham'])
            ->orderBy('ma_phieu_nhap', 'desc')
            ->paginate(15);

        foreach ($phieuNhaps as $phieuNhap) {
            $this->chuanBiPhieuNhapDeHienThi($phieuNhap);
        }

        return view('admin.pages.phieu-nhap', compact('phieuNhaps'));
    }

    // Hiển thị chi tiết phiếu nhập.
    public function hienThiChiTietPhieuNhap(PhieuNhap $phieuNhap)
    {
        $phieuNhap->load(['nhaCungCap', 'donDatNhap', 'chiTietPhieuNhaps.sanPham']);
        $this->chuanBiPhieuNhapDeHienThi($phieuNhap);

        foreach ($phieuNhap->chiTietPhieuNhaps as $chiTiet) {
            $chiTiet->ten_san_pham_hien_thi = 'Sản phẩm đã xóa';
            if ($chiTiet->sanPham) {
                $chiTiet->ten_san_pham_hien_thi = $chiTiet->sanPham->ten_hien_thi;
            }

            $chiTiet->han_su_dung_hien_thi = '-';
            if ($chiTiet->han_su_dung) {
                $chiTiet->han_su_dung_hien_thi = $chiTiet->han_su_dung->format('d/m/Y');
            }
        }

        return view('admin.pages.chi-tiet-phieu-nhap', compact('phieuNhap'));
    }

    // Hiển thị danh sách phiếu hàng hư.
    public function hienThiDanhSachPhieuHangHu()
    {
        $phieuHangHus = PhieuHangHu::with([
            'nhaCungCap',
            'donDatNhap',
            'phieuNhap',
            'chiTietPhieuHangHus.sanPham',
        ])->orderBy('ma_phieu_hang_hu', 'desc')->paginate(15);

        return view('admin.pages.phieu-hang-hu', compact('phieuHangHus'));
    }

    // Hiển thị chi tiết phiếu hàng hư.
    public function hienThiChiTietPhieuHangHu(PhieuHangHu $phieuHangHu)
    {
        $phieuHangHu->load([
            'nhaCungCap',
            'donDatNhap',
            'phieuNhap',
            'donHang',
            'chiTietPhieuHangHus.sanPham',
            'minhChungs',
        ]);

        foreach ($phieuHangHu->chiTietPhieuHangHus as $chiTiet) {
            $chiTiet->ten_san_pham_hien_thi = 'Sản phẩm đã xóa';
            if ($chiTiet->sanPham) {
                $chiTiet->ten_san_pham_hien_thi = $chiTiet->sanPham->ten_hien_thi;
            }
        }

        return view('admin.pages.chi-tiet-phieu-hang-hu', compact('phieuHangHu'));
    }

    // Xóa đơn đặt nhập chưa phát sinh phiếu nhập.
    public function xoaDonDatNhap($maDonDatNhap)
    {
        $donDatNhap = DonDatNhap::with('phieuNhaps')->findOrFail($maDonDatNhap);

        if ($donDatNhap->phieuNhaps()->exists()) {
            return back()->with('error', 'Không thể xóa đơn đã có phiếu nhập.');
        }

        $donDatNhap->delete();

        return redirect()->route('admin.don-dat-nhap.danh-sach')
            ->with('success', 'Đã xóa đơn đặt nhập.');
    }

    // Kiểm tra sản phẩm có bị chọn trùng trong đơn đặt nhập.
    private function coSanPhamTrung($chiTiets)
    {
        $maSanPhams = [];

        foreach ($chiTiets as $chiTiet) {
            $maSanPham = (int) $chiTiet['ma_san_pham'];
            if (in_array($maSanPham, $maSanPhams)) {
                return true;
            }

            $maSanPhams[] = $maSanPham;
        }

        return false;
    }

    // Lấy các tệp minh chứng hợp lệ từ request.
    private function layTepMinhChung($request)
    {
        $tepMinhChungs = [];

        foreach ($request->file('minh_chungs', []) as $tepMinhChung) {
            if ($tepMinhChung) {
                $tepMinhChungs[] = $tepMinhChung;
            }
        }

        return $tepMinhChungs;
    }

    // Tính tổng số lượng hàng bị từ chối.
    private function tinhTongSoLuongTuChoi($chiTiets)
    {
        $tongSoLuong = 0;

        foreach ($chiTiets as $chiTiet) {
            $tongSoLuong += (int) $chiTiet['so_luong_tu_choi'];
        }

        return $tongSoLuong;
    }

    // Kiểm tra từng dòng nhận hàng trước khi lưu.
    private function kiemTraChiTietNhapKho($donDatNhap, $chiTietNhapKhos)
    {
        $lois = [];
        $maGuiLens = $this->layMaChiTietTuForm($chiTietNhapKhos);
        $maCanCos = $this->layMaChiTietDonDatNhap($donDatNhap);
        sort($maGuiLens);
        sort($maCanCos);

        if ($maGuiLens != $maCanCos) {
            return ['chi_tiets' => 'Danh sách nhận hàng phải đúng với đơn đặt nhập.'];
        }

        foreach ($chiTietNhapKhos as $viTri => $data) {
            $chiTiet = $this->timChiTietDonDatNhap(
                $donDatNhap,
                (int) $data['ma_chi_tiet_don_dat_nhap']
            );
            $soLuongNhan = (int) $data['so_luong_nhan'];
            $soLuongTuChoi = (int) $data['so_luong_tu_choi'];
            $soLuongNhap = $soLuongNhan - $soLuongTuChoi;
            $ngaySanXuat = '';
            if (isset($data['ngay_san_xuat'])) {
                $ngaySanXuat = $data['ngay_san_xuat'];
            }

            $hanSuDung = '';
            if (isset($data['han_su_dung'])) {
                $hanSuDung = $data['han_su_dung'];
            }
            $tienToLoi = 'chi_tiets.'.$viTri.'.';

            if (! $chiTiet) {
                $lois[$tienToLoi.'ma_chi_tiet_don_dat_nhap'] = 'Sản phẩm không thuộc đơn đặt nhập.';
                continue;
            }

            if ($soLuongNhan > $chiTiet->so_luong_dat) {
                $lois[$tienToLoi.'so_luong_nhan'] = 'Số nhận không được lớn hơn số đã đặt.';
            }

            if ($soLuongTuChoi > $soLuongNhan) {
                $lois[$tienToLoi.'so_luong_tu_choi'] = 'Số từ chối không được lớn hơn số nhận.';
            }

            if ($soLuongNhap > 0 && $ngaySanXuat == '') {
                $lois[$tienToLoi.'ngay_san_xuat'] = 'Vui lòng nhập ngày sản xuất.';
            }

            if ($soLuongNhap > 0 && $hanSuDung == '') {
                $lois[$tienToLoi.'han_su_dung'] = 'Vui lòng nhập hạn sử dụng.';
            }

            if ($soLuongNhap > 0 && $hanSuDung != '' && $hanSuDung < now()->toDateString()) {
                $lois[$tienToLoi.'han_su_dung'] = 'Hàng hết hạn không được nhập vào kho bán.';
            }

            if ($soLuongNhap > 0 && $ngaySanXuat != '' && $hanSuDung != '' && $hanSuDung < $ngaySanXuat) {
                $lois[$tienToLoi.'han_su_dung'] = 'Hạn sử dụng phải sau ngày sản xuất.';
            }
        }

        return $lois;
    }

    // Lấy mã chi tiết đơn đặt nhập từ form.
    private function layMaChiTietTuForm($chiTietNhapKhos)
    {
        $maChiTiets = [];

        foreach ($chiTietNhapKhos as $chiTietNhapKho) {
            $maChiTiets[] = (int) $chiTietNhapKho['ma_chi_tiet_don_dat_nhap'];
        }

        return $maChiTiets;
    }

    // Lấy mã các dòng thuộc đơn đặt nhập.
    private function layMaChiTietDonDatNhap($donDatNhap)
    {
        $maChiTiets = [];

        foreach ($donDatNhap->chiTietDonDatNhaps as $chiTiet) {
            $maChiTiets[] = (int) $chiTiet->ma_chi_tiet_don_dat_nhap;
        }

        return $maChiTiets;
    }

    // Tìm một dòng chi tiết trong đơn đặt nhập.
    private function timChiTietDonDatNhap($donDatNhap, $maChiTietDonDatNhap)
    {
        foreach ($donDatNhap->chiTietDonDatNhaps as $chiTiet) {
            if ($chiTiet->ma_chi_tiet_don_dat_nhap == $maChiTietDonDatNhap) {
                return $chiTiet;
            }
        }

        return null;
    }

    // Tạo phiếu nhập từ đơn đặt nhập.
    private function taoPhieuNhap($donDatNhap)
    {
        return PhieuNhap::create([
            'so_phieu' => PhieuNhap::taoSoPhieu(),
            'ma_don_dat_nhap' => $donDatNhap->ma_don_dat_nhap,
            'ma_nha_cung_cap' => $donDatNhap->ma_nha_cung_cap,
            'nhan_hang_luc' => now(),
            'ghi_chu' => null,
        ]);
    }

    // Lưu chi tiết nhận hàng, tạo lô kho và trả các dòng bị từ chối.
    private function luuChiTietNhapKho($donDatNhap, $phieuNhap, $chiTietNhapKhos)
    {
        $hangBiTuChois = [];

        foreach ($chiTietNhapKhos as $data) {
            $chiTiet = $this->timChiTietDonDatNhap(
                $donDatNhap,
                (int) $data['ma_chi_tiet_don_dat_nhap']
            );
            if (! $chiTiet) {
                continue;
            }

            $soLuongNhan = (int) $data['so_luong_nhan'];
            $soLuongTuChoi = (int) $data['so_luong_tu_choi'];
            $soLuongNhap = $soLuongNhan - $soLuongTuChoi;
            $ngaySanXuat = null;
            $hanSuDung = null;

            if ($soLuongNhap > 0) {
                $ngaySanXuat = $data['ngay_san_xuat'];
                $hanSuDung = $data['han_su_dung'];
            }

            $chiTiet->update([
                'so_luong_nhan' => $soLuongNhan,
                'so_luong_tu_choi' => $soLuongTuChoi,
                'so_luong_da_nhap' => $soLuongNhap,
                'ngay_san_xuat' => $ngaySanXuat,
                'han_su_dung' => $hanSuDung,
            ]);

            if ($soLuongNhap > 0) {
                $chiTietPhieuNhap = ChiTietPhieuNhap::create([
                    'ma_phieu_nhap' => $phieuNhap->ma_phieu_nhap,
                    'ma_chi_tiet_don_dat_nhap' => $chiTiet->ma_chi_tiet_don_dat_nhap,
                    'ma_san_pham' => $chiTiet->ma_san_pham,
                    'so_luong' => $soLuongNhap,
                    'ngay_san_xuat' => $ngaySanXuat,
                    'han_su_dung' => $hanSuDung,
                ]);

                LoHangKho::create([
                    'ma_chi_tiet_phieu_nhap' => $chiTietPhieuNhap->ma_chi_tiet_phieu_nhap,
                    'ma_phieu_nhap' => $phieuNhap->ma_phieu_nhap,
                    'ma_san_pham' => $chiTiet->ma_san_pham,
                    'ma_nha_cung_cap' => $donDatNhap->ma_nha_cung_cap,
                    'so_luong_nhap' => $soLuongNhap,
                    'so_luong_con' => $soLuongNhap,
                    'ngay_san_xuat' => $ngaySanXuat,
                    'han_su_dung' => $hanSuDung,
                ]);
            }

            if ($soLuongTuChoi > 0) {
                $hangBiTuChois[] = [
                    'ma_san_pham' => $chiTiet->ma_san_pham,
                    'so_luong' => $soLuongTuChoi,
                    'ghi_chu' => 'Hàng bị từ chối khi nhập từ đơn '.$donDatNhap->so_don,
                ];
            }
        }

        return $hangBiTuChois;
    }

    // Tạo phiếu ghi nhận hàng hư khi nhập kho.
    private function taoPhieuHangHu($donDatNhap, $phieuNhap, $lyDo)
    {
        return PhieuHangHu::create([
            'so_phieu' => PhieuHangHu::taoSoPhieu(),
            'ma_don_dat_nhap' => $donDatNhap->ma_don_dat_nhap,
            'ma_phieu_nhap' => $phieuNhap->ma_phieu_nhap,
            'ma_nha_cung_cap' => $donDatNhap->ma_nha_cung_cap,
            'ly_do' => $lyDo,
            'xay_ra_luc' => now(),
        ]);
    }

    // Cập nhật đơn đặt nhập sau khi nhận hàng xong.
    private function hoanTatDonDatNhap($donDatNhap, $tongSoLuongTuChoi, $moTaHangLoi)
    {
        $moTaHangLoiCanLuu = null;
        $thoiGianBaoNhaCungCap = null;

        if ($tongSoLuongTuChoi > 0) {
            $moTaHangLoiCanLuu = $moTaHangLoi;
            $thoiGianBaoNhaCungCap = now();
        }

        $donDatNhap->update([
            'trang_thai' => 'da_nhap_hang',
            'nhan_hang_luc' => now(),
            'mo_ta_hang_loi' => $moTaHangLoiCanLuu,
            'bao_nha_cung_cap_luc' => $thoiGianBaoNhaCungCap,
        ]);
    }
// Lưu thông tin các tệp minh chứng vào phiếu hàng hư.
    private function luuMinhChungPhieuHangHu($phieuHangHu, $minhChungs)
    {
        foreach ($minhChungs as $minhChung) {
            $oDia = 'public';
            $duongDan = '';
            $tenGoc = null;
            $loaiMime = null;
            $loaiTep = 'hinh_anh';
            $kichThuoc = 0;

            if (isset($minhChung['o_dia'])) {
                $oDia = $minhChung['o_dia'];
            }

            if (isset($minhChung['duong_dan'])) {
                $duongDan = $minhChung['duong_dan'];
            }

            if (isset($minhChung['ten_goc'])) {
                $tenGoc = $minhChung['ten_goc'];
            }

            if (isset($minhChung['loai_mime'])) {
                $loaiMime = $minhChung['loai_mime'];
            }

            if (isset($minhChung['loai_tep'])) {
                $loaiTep = $minhChung['loai_tep'];
            }

            if (isset($minhChung['kich_thuoc'])) {
                $kichThuoc = $minhChung['kich_thuoc'];
            }

            $phieuHangHu->minhChungs()->create([
                'o_dia' => $oDia,
                'duong_dan' => $duongDan,
                'ten_goc' => $tenGoc,
                'loai_mime' => $loaiMime,
                'loai_tep' => $loaiTep,
                'kich_thuoc' => $kichThuoc,
            ]);
        }
    }

    // Chuẩn bị thông tin chung của đơn đặt nhập để View chỉ hiển thị.
    private function chuanBiDonDatNhapDeHienThi($donDatNhap)
    {
        $donDatNhap->ten_nha_cung_cap_hien_thi = 'Không rõ';
        if ($donDatNhap->nhaCungCap) {
            $donDatNhap->ten_nha_cung_cap_hien_thi = $donDatNhap->nhaCungCap->ten;
        }

        $donDatNhap->ngay_dat_hien_thi = '-';
        if ($donDatNhap->ngay_dat) {
            $donDatNhap->ngay_dat_hien_thi = $donDatNhap->ngay_dat->format('d/m/Y');
        }

        $donDatNhap->ngay_nhap_hien_thi = 'Chưa nhập';
        if ($donDatNhap->nhan_hang_luc) {
            $donDatNhap->ngay_nhap_hien_thi = $donDatNhap->nhan_hang_luc->format('d/m/Y H:i');
        }
    }

    // Chuẩn bị các dòng và phiếu con của chi tiết đơn đặt nhập.
    private function chuanBiChiTietDonDatNhap($donDatNhap)
    {
        foreach ($donDatNhap->chiTietDonDatNhaps as $chiTiet) {
            $chiTiet->ten_san_pham_hien_thi = 'Sản phẩm đã xóa';
            if ($chiTiet->sanPham) {
                $chiTiet->ten_san_pham_hien_thi = $chiTiet->sanPham->ten_hien_thi;
            }

            $chiTiet->han_su_dung_hien_thi = '-';
            if ($chiTiet->han_su_dung) {
                $chiTiet->han_su_dung_hien_thi = $chiTiet->han_su_dung->format('d/m/Y');
            }
        }

        foreach ($donDatNhap->phieuNhaps as $phieuNhap) {
            $this->chuanBiPhieuNhapDeHienThi($phieuNhap);
        }
    }

    // Chuẩn bị giá trị mặc định cho từng dòng trong form nhập kho.
    private function chuanBiFormNhapKho($donDatNhap)
    {
        foreach ($donDatNhap->chiTietDonDatNhaps as $viTri => $chiTiet) {
            $tienTo = 'chi_tiets.'.$viTri.'.';
            $chiTiet->ten_san_pham_hien_thi = 'Sản phẩm đã xóa';
            if ($chiTiet->sanPham) {
                $chiTiet->ten_san_pham_hien_thi = $chiTiet->sanPham->ten_hien_thi;
            }
            $chiTiet->so_luong_nhan_mac_dinh = old($tienTo.'so_luong_nhan', $chiTiet->so_luong_dat);
            $chiTiet->so_luong_tu_choi_mac_dinh = old($tienTo.'so_luong_tu_choi', 0);
            $chiTiet->so_luong_nhap_mac_dinh =
                $chiTiet->so_luong_nhan_mac_dinh - $chiTiet->so_luong_tu_choi_mac_dinh;

            if ($chiTiet->so_luong_nhap_mac_dinh < 0) {
                $chiTiet->so_luong_nhap_mac_dinh = 0;
            }
            $chiTiet->ngay_san_xuat_mac_dinh = old($tienTo.'ngay_san_xuat', date('Y-m-d'));
            $chiTiet->han_su_dung_mac_dinh = old($tienTo.'han_su_dung', date('Y-m-d', strtotime('+365 days')));
        }
    }

    // Chuẩn bị thông tin phiếu nhập để View chỉ hiển thị.
    private function chuanBiPhieuNhapDeHienThi($phieuNhap)
    {
        $phieuNhap->ngay_nhap_hien_thi = '-';
        if ($phieuNhap->nhan_hang_luc) {
            $phieuNhap->ngay_nhap_hien_thi = $phieuNhap->nhan_hang_luc->format('d/m/Y H:i');
        }

        $phieuNhap->ten_nha_cung_cap_hien_thi = 'Không rõ';
        if ($phieuNhap->nhaCungCap) {
            $phieuNhap->ten_nha_cung_cap_hien_thi = $phieuNhap->nhaCungCap->ten;
        }
    }
}