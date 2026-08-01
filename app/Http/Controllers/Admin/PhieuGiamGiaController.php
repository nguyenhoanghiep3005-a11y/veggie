<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\PhieuGiamGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PhieuGiamGiaController extends Controller
{
    // Hien thi danh sach phieu giam gia va form them moi.
    public function hienThiDanhSachPhieuGiamGia()
    {
        $phieuGiamGias = PhieuGiamGia::with('nguoiDungs')
            ->orderBy('ma_phieu_giam_gia', 'desc')
            ->paginate(10);

        foreach ($phieuGiamGias as $phieuGiamGia) {
            $this->chuanBiDuLieuHienThi($phieuGiamGia);
        }

        $khachHangs = NguoiDung::where('ma_vai_tro', 3)
            ->where('trang_thai', 'hoat_dong')
            ->orderBy('ten')
            ->get();

        return view('admin.pages.phieu-giam-gia', compact('phieuGiamGias', 'khachHangs'));
    }

    // Luu phieu giam gia moi.
    public function themPhieuGiamGia(Request $request)
    {
        $data = $this->kiemTraDuLieuThem($request);
        $maNguoiDungs = $data['ma_nguoi_dungs'];
        unset($data['ma_nguoi_dungs']);

        DB::transaction(function () use ($data, $maNguoiDungs) {
            $phieuGiamGia = PhieuGiamGia::create($data);
            $this->dongBoKhachHang($phieuGiamGia, $maNguoiDungs);
        });

        return back()->with('success', 'Thêm phiếu giảm giá thành công.');
    }

    // Cap nhat phieu giam gia da co.
    public function capNhatPhieuGiamGia(Request $request, PhieuGiamGia $phieuGiamGia)
    {
        $data = $this->kiemTraDuLieuCapNhat($request, $phieuGiamGia);
        $maNguoiDungs = $data['ma_nguoi_dungs'];
        unset($data['ma_nguoi_dungs']);

        DB::transaction(function () use ($phieuGiamGia, $data, $maNguoiDungs) {
            $phieuGiamGia->update($data);
            $this->dongBoKhachHang($phieuGiamGia, $maNguoiDungs);
        });

        return back()->with('success', 'Cập nhật phiếu giảm giá thành công.');
    }

    // Xoa phieu giam gia hoac khoa lai neu da phat sinh don hang.
    public function xoaPhieuGiamGia(PhieuGiamGia $phieuGiamGia)
    {
        if ($phieuGiamGia->donHangs()->exists()) {
            $phieuGiamGia->dang_hoat_dong = false;
            $phieuGiamGia->save();

            return back()->with('success', 'Phiếu đã được khóa vì đã phát sinh đơn hàng.');
        }

        $phieuGiamGia->delete();

        return back()->with('success', 'Xóa phiếu giảm giá thành công.');
    }

    // Kiem tra du lieu khi them phieu giam gia.
    private function kiemTraDuLieuThem($request)
    {
        $data = $request->validate([
            'ma_giam_gia' => 'required|string|max:50|unique:phieu_giam_gia,ma_giam_gia',
            'phan_tram_giam' => 'required|numeric|min:0.01|max:100',
            'gia_tri_don_toi_thieu' => 'nullable|numeric|min:0',
            'so_tien_giam_toi_da' => 'nullable|numeric|min:0',
            'het_han_luc' => 'nullable|date',
            'gioi_han_su_dung' => 'nullable|integer|min:1',
            'loai_ap_dung' => 'required|in:tat_ca,khach_hang',
            'ma_nguoi_dungs' => 'nullable|array',
            'ma_nguoi_dungs.*' => 'integer|exists:nguoi_dung,ma_nguoi_dung',
            'dang_hoat_dong' => 'nullable|boolean',
        ]);

        return $this->chuanBiDuLieuLuu($request, $data);
    }

    // Kiem tra du lieu khi cap nhat phieu giam gia.
    private function kiemTraDuLieuCapNhat($request, $phieuGiamGia)
    {
        $data = $request->validate([
            'ma_giam_gia' => 'required|string|max:50|unique:phieu_giam_gia,ma_giam_gia,'
                .$phieuGiamGia->ma_phieu_giam_gia.',ma_phieu_giam_gia',
            'phan_tram_giam' => 'required|numeric|min:0.01|max:100',
            'gia_tri_don_toi_thieu' => 'nullable|numeric|min:0',
            'so_tien_giam_toi_da' => 'nullable|numeric|min:0',
            'het_han_luc' => 'nullable|date',
            'gioi_han_su_dung' => 'nullable|integer|min:1',
            'loai_ap_dung' => 'required|in:tat_ca,khach_hang',
            'ma_nguoi_dungs' => 'nullable|array',
            'ma_nguoi_dungs.*' => 'integer|exists:nguoi_dung,ma_nguoi_dung',
            'dang_hoat_dong' => 'nullable|boolean',
        ]);

        return $this->chuanBiDuLieuLuu($request, $data);
    }

    // Chuan hoa du lieu truoc khi luu phieu giam gia.
    private function chuanBiDuLieuLuu($request, $data)
    {
        $data['ma_giam_gia'] = strtoupper(trim($data['ma_giam_gia']));

        if (! isset($data['gia_tri_don_toi_thieu']) || $data['gia_tri_don_toi_thieu'] == '') {
            $data['gia_tri_don_toi_thieu'] = 0;
        }

        if (! isset($data['so_tien_giam_toi_da']) || $data['so_tien_giam_toi_da'] == '') {
            $data['so_tien_giam_toi_da'] = null;
        }

        if (! isset($data['gioi_han_su_dung']) || $data['gioi_han_su_dung'] == '') {
            $data['gioi_han_su_dung'] = null;
        }

        $data['dang_hoat_dong'] = $request->boolean('dang_hoat_dong');

        if (! isset($data['ma_nguoi_dungs']) || count($data['ma_nguoi_dungs']) == 0) {
            $data['ma_nguoi_dungs'] = [];
        }

        if ($data['loai_ap_dung'] == PhieuGiamGia::AP_DUNG_KHACH_HANG
            && count($data['ma_nguoi_dungs']) == 0) {
            throw ValidationException::withMessages([
                'ma_nguoi_dungs' => 'Vui lòng chọn ít nhất một khách hàng.',
            ]);
        }

        return $data;
    }

    // Dong bo danh sach khach hang duoc nhan phieu giam gia rieng.
    private function dongBoKhachHang($phieuGiamGia, $maNguoiDungs)
    {
        DB::table('nguoi_dung_phieu_giam_gia')
            ->where('ma_phieu_giam_gia', $phieuGiamGia->ma_phieu_giam_gia)
            ->whereNull('ngay_su_dung')
            ->delete();

        if ($phieuGiamGia->loai_ap_dung != PhieuGiamGia::AP_DUNG_KHACH_HANG) {
            return;
        }

        $khachHangs = NguoiDung::whereIn('ma_nguoi_dung', $maNguoiDungs)
            ->where('ma_vai_tro', 3)
            ->get();

        foreach ($khachHangs as $khachHang) {
            $daDuocGan = DB::table('nguoi_dung_phieu_giam_gia')
                ->where('ma_phieu_giam_gia', $phieuGiamGia->ma_phieu_giam_gia)
                ->where('ma_nguoi_dung', $khachHang->ma_nguoi_dung)
                ->exists();

            if (! $daDuocGan) {
                DB::table('nguoi_dung_phieu_giam_gia')->insert([
                    'ma_phieu_giam_gia' => $phieuGiamGia->ma_phieu_giam_gia,
                    'ma_nguoi_dung' => $khachHang->ma_nguoi_dung,
                    'ngay_nhan' => now(),
                    'ngay_su_dung' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    // Chuan bi cac gia tri hien thi de View khong phai xu ly du lieu.
    private function chuanBiDuLieuHienThi($phieuGiamGia)
    {
        $maNguoiDungs = [];

        foreach ($phieuGiamGia->nguoiDungs as $nguoiDung) {
            $maNguoiDungs[] = $nguoiDung->ma_nguoi_dung;
        }

        $phieuGiamGia->ma_nguoi_dungs = $maNguoiDungs;

        $phanTram = number_format($phieuGiamGia->phan_tram_giam, 2, '.', '');
        $phieuGiamGia->phan_tram_hien_thi = rtrim(rtrim($phanTram, '0'), '.');

        $phieuGiamGia->dieu_kien_hien_thi = 'Không yêu cầu';
        if ($phieuGiamGia->gia_tri_don_toi_thieu > 0) {
            $phieuGiamGia->dieu_kien_hien_thi =
                'Từ '.number_format($phieuGiamGia->gia_tri_don_toi_thieu, 0, ',', '.').' đ';
        }

        $phieuGiamGia->thoi_han_hien_thi = 'Không giới hạn';
        $phieuGiamGia->thoi_han_form = '';

        if ($phieuGiamGia->het_han_luc) {
            $phieuGiamGia->thoi_han_hien_thi = $phieuGiamGia->het_han_luc->format('d/m/Y H:i');
            $phieuGiamGia->thoi_han_form = $phieuGiamGia->het_han_luc->format('Y-m-d\TH:i');
        }

        $phieuGiamGia->gioi_han_hien_thi = 'Không giới hạn';
        if ($phieuGiamGia->gioi_han_su_dung) {
            $phieuGiamGia->gioi_han_hien_thi = $phieuGiamGia->gioi_han_su_dung;
        }

        $phieuGiamGia->lop_trang_thai = 'danger';
        $phieuGiamGia->ten_trang_thai = 'Hết hiệu lực';

        if ($phieuGiamGia->conHieuLuc()) {
            $phieuGiamGia->lop_trang_thai = 'success';
            $phieuGiamGia->ten_trang_thai = 'Đang kích hoạt';
        }
    }
}