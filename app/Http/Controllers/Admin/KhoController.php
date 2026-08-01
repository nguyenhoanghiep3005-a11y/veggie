<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HangHuKho;
use App\Models\LoHangKho;
use App\Models\SanPham;
use App\Services\MinhChungService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhoController extends Controller
{
    private $dichVuMinhChung;

    // Khoi tao dich vu luu minh chung hang hu.
    public function __construct(MinhChungService $dichVuMinhChung)
    {
        $this->dichVuMinhChung = $dichVuMinhChung;
    }

    // Hien thi cac lo hang con ton trong kho.
    public function hienThiDanhSachKho(Request $request)
    {
        $trangThai = $request->input('trang_thai', 'tat_ca');
        $query = LoHangKho::with(['sanPham.danhMuc', 'phieuNhap', 'nhaCungCap'])
            ->where('so_luong_con', '>', 0);

        if ($trangThai == 'can_han') {
            $query->whereDate('han_su_dung', '>=', today())
                ->whereDate('han_su_dung', '<=', today()->addDays(SanPham::SO_NGAY_CAN_HAN));
        } elseif ($trangThai == 'het_han') {
            $query->whereDate('han_su_dung', '<', today());
        } elseif ($trangThai == 'tuoi_moi') {
            $query->whereDate('han_su_dung', '>', today()->addDays(SanPham::SO_NGAY_CAN_HAN));
        }

        $loHangKhos = $query->orderBy('han_su_dung')
            ->orderBy('ma_lo_hang_kho', 'desc')
            ->paginate(20)
            ->appends(['trang_thai' => $trangThai]);

        foreach ($loHangKhos as $loHangKho) {
            $this->chuanBiLoHang($loHangKho);
        }

        return view('admin.pages.kho-hang', compact('loHangKhos', 'trangThai'));
    }

    // Hien thi hang hu va duong dan minh chung.
    public function hienThiDanhSachHangHu()
    {
        $hangHuKhos = HangHuKho::with(['sanPham', 'minhChungs'])
            ->orderBy('xay_ra_luc', 'desc')
            ->orderBy('ma_hang_hu_kho', 'desc')
            ->paginate(20);

        foreach ($hangHuKhos as $hangHuKho) {
            $hangHuKho->xay_ra_luc_hien_thi = '-';
            if ($hangHuKho->xay_ra_luc) {
                $hangHuKho->xay_ra_luc_hien_thi = $hangHuKho->xay_ra_luc->format('d/m/Y H:i');
            }
            $minhChungHienThis = [];

            foreach ($hangHuKho->minhChungs as $minhChung) {
                $minhChungHienThis[] = [
                    'duong_dan' => $this->dichVuMinhChung->layDuongDan($minhChung),
                    'loai_tep' => $minhChung->loai_tep,
                ];
            }

            $hangHuKho->minh_chung_hien_thi = $minhChungHienThis;
        }

        return view('admin.pages.hang-hu-kho', compact('hangHuKhos'));
    }

    // Cap nhat gia khuyen mai hoac ghi nhan hang hu.
    public function dieuChinhLoHang(Request $request, LoHangKho $loHangKho)
    {
        $data = $request->validate([
            'loai_dieu_chinh' => 'required|in:khuyen_mai,hang_hu',
            'gia_khuyen_mai' => 'nullable|numeric|min:0',
            'so_luong_hu' => 'nullable|integer|min:1',
            'ly_do_hu' => 'nullable|string|max:3000',
            'minh_chung' => 'nullable|array',
            'minh_chung.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
        ]);

        if ($data['loai_dieu_chinh'] == 'khuyen_mai') {
            return $this->capNhatGiaKhuyenMai($loHangKho, $data);
        }

        return $this->ghiNhanHangHu($loHangKho, $data, $request->file('minh_chung', []));
    }

    // Cap nhat gia khuyen mai cua lo hang con han.
    private function capNhatGiaKhuyenMai($loHangKho, $data)
    {
        $loHangKho->load('sanPham');
        $giaKhuyenMai = null;

        if (isset($data['gia_khuyen_mai']) && $data['gia_khuyen_mai'] != '') {
            $giaKhuyenMai = (float) $data['gia_khuyen_mai'];
        }

        if (! $loHangKho->sanPham || $loHangKho->daHetHan()) {
            toastr()->error('Lô hàng không đủ điều kiện khuyến mãi.');
            return back();
        }

        if ($giaKhuyenMai != null && $giaKhuyenMai >= $loHangKho->sanPham->gia) {
            toastr()->error('Giá khuyến mãi phải nhỏ hơn giá gốc.');
            return back()->withInput();
        }

        $loHangKho->gia_khuyen_mai = $giaKhuyenMai;
        $loHangKho->save();
        toastr()->success('Đã cập nhật giá khuyến mãi.');

        return back();
    }

    // Tru kho va luu hang hu kem minh chung.
    private function ghiNhanHangHu($loHangKho, $data, $tepMinhChungs)
    {
        $soLuongHu = 0;
        if (isset($data['so_luong_hu'])) {
            $soLuongHu = (int) $data['so_luong_hu'];
        }

        $lyDoHu = '';
        if (isset($data['ly_do_hu'])) {
            $lyDoHu = trim($data['ly_do_hu']);
        }

        if ($soLuongHu <= 0 || $lyDoHu == '' || count($tepMinhChungs) == 0) {
            toastr()->error('Hàng hư phải có số lượng, lý do và minh chứng.');
            return back()->withInput();
        }

        try {
            DB::beginTransaction();
            $loHangKho->load('sanPham');
            $sanPham = $loHangKho->sanPham;

            if (! $sanPham || $soLuongHu > $loHangKho->so_luong_con || $soLuongHu > $sanPham->ton_kho) {
                throw new Exception('Số lượng hàng hư không hợp lệ.');
            }

            $loHangKho->so_luong_con -= $soLuongHu;
            $loHangKho->save();
            $sanPham->ton_kho -= $soLuongHu;
            $sanPham->capNhatTrangThaiTonKho();
            $sanPham->save();

            $hangHuKho = HangHuKho::create([
                'ma_lo_hang_kho' => $loHangKho->ma_lo_hang_kho,
                'ma_san_pham' => $sanPham->ma_san_pham,
                'ten_san_pham' => $sanPham->ten_hien_thi,
                'so_luong' => $soLuongHu,
                'ly_do' => $lyDoHu,
                'xay_ra_luc' => now(),
            ]);

            $minhChungs = $this->dichVuMinhChung->taiNhieuTepLen(
                $tepMinhChungs,
                config('cloudinary.folders.warehouse_damages')
            );

            foreach ($minhChungs as $minhChung) {
                $hangHuKho->minhChungs()->create([
                    'o_dia' => $minhChung['o_dia'],
                    'duong_dan' => $minhChung['duong_dan'],
                    'ten_goc' => $minhChung['ten_goc'],
                    'loai_mime' => $minhChung['loai_mime'],
                    'loai_tep' => $minhChung['loai_tep'],
                    'kich_thuoc' => $minhChung['kich_thuoc'],
                ]);
            }

            DB::commit();
            toastr()->success('Đã ghi nhận hàng hư.');

            return redirect()->route('admin.kho-hang.hang-hu');
        } catch (Exception $exception) {
            DB::rollBack();
            toastr()->error($exception->getMessage());

            return back()->withInput();
        }
    }

    // Chuan bi cac gia tri hien thi cho lo hang.
    private function chuanBiLoHang($loHangKho)
    {
        $loHangKho->ten_nha_cung_cap = '-';
        if ($loHangKho->nhaCungCap) {
            $loHangKho->ten_nha_cung_cap = $loHangKho->nhaCungCap->ten;
        }

        $loHangKho->ten_san_pham = 'Sản phẩm đã xóa';
        if ($loHangKho->sanPham) {
            $loHangKho->ten_san_pham = $loHangKho->sanPham->ten_hien_thi;
        }

        $loHangKho->ngay_san_xuat_hien_thi = '-';
        if ($loHangKho->ngay_san_xuat) {
            $loHangKho->ngay_san_xuat_hien_thi = $loHangKho->ngay_san_xuat->format('d/m/Y');
        }

        $loHangKho->han_su_dung_hien_thi = '-';
        if ($loHangKho->han_su_dung) {
            $loHangKho->han_su_dung_hien_thi = $loHangKho->han_su_dung->format('d/m/Y');
        }

        $loHangKho->so_phieu_nhap = '#'.$loHangKho->ma_lo_hang_kho;
        if ($loHangKho->phieuNhap) {
            $loHangKho->so_phieu_nhap = $loHangKho->phieuNhap->so_phieu;
        }
    }
}
