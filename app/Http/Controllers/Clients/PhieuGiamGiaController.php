<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\PhieuGiamGia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhieuGiamGiaController extends Controller
{
    // Hiển thị các phiếu giảm giá người dùng có thể nhận hoặc đã nhận.
    public function hienThiDanhSachPhieuGiamGia()
    {
        $nguoiDung = Auth::user();
        $maPhieuDaNhan = [];
        $maPhieuDaSuDung = [];

        if ($nguoiDung) {
            $phieuDaNhans = $nguoiDung->phieuGiamGias()->get();

            foreach ($phieuDaNhans as $phieuGiamGia) {
                if ($phieuGiamGia->pivot->ngay_su_dung == null) {
                    $maPhieuDaNhan[] = $phieuGiamGia->ma_phieu_giam_gia;
                } else {
                    $maPhieuDaSuDung[] = $phieuGiamGia->ma_phieu_giam_gia;
                }
            }
        }

        $tatCaPhieuGiamGia = PhieuGiamGia::where('dang_hoat_dong', true)
            ->orderBy('ma_phieu_giam_gia', 'desc')
            ->get();
        $phieuGiamGias = [];

        foreach ($tatCaPhieuGiamGia as $phieuGiamGia) {
            if (! $phieuGiamGia->conHieuLuc()) {
                continue;
            }

            if ($phieuGiamGia->loai_ap_dung == 'khach_hang') {
                if (! $nguoiDung) {
                    continue;
                }

                $duocGan = DB::table('nguoi_dung_phieu_giam_gia')
                    ->where('ma_phieu_giam_gia', $phieuGiamGia->ma_phieu_giam_gia)
                    ->where('ma_nguoi_dung', $nguoiDung->ma_nguoi_dung)
                    ->exists();

                if (! $duocGan) {
                    continue;
                }
            }

            $this->chuanBiThongTinHienThi($phieuGiamGia, $maPhieuDaNhan, $maPhieuDaSuDung);
            $phieuGiamGias[] = $phieuGiamGia;
        }

        return view('clients.pages.phieu-giam-gia', compact('phieuGiamGias'));
    }

    // Ghi nhận phiếu giảm giá người dùng đã chọn vào tài khoản.
    public function nhanPhieuGiamGia(PhieuGiamGia $phieuGiamGia)
    {
        $loi = $phieuGiamGia->kiemTraDieuKienSuDung(Auth::id());

        if ($loi) {
            return back()->with('error', $loi);
        }

        $daNhan = DB::table('nguoi_dung_phieu_giam_gia')
            ->where('ma_nguoi_dung', Auth::id())
            ->where('ma_phieu_giam_gia', $phieuGiamGia->ma_phieu_giam_gia)
            ->exists();

        if (! $daNhan) {
            DB::table('nguoi_dung_phieu_giam_gia')->insert([
                'ma_nguoi_dung' => Auth::id(),
                'ma_phieu_giam_gia' => $phieuGiamGia->ma_phieu_giam_gia,
                'ngay_nhan' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Đã nhận mã '.$phieuGiamGia->ma_giam_gia.'.');
    }

    // Chuẩn bị trạng thái và nội dung hiển thị của một phiếu giảm giá.
    private function chuanBiThongTinHienThi($phieuGiamGia, $maPhieuDaNhan, $maPhieuDaSuDung)
    {
        $phieuGiamGia->da_nhan = in_array($phieuGiamGia->ma_phieu_giam_gia, $maPhieuDaNhan);
        $phieuGiamGia->da_su_dung = in_array($phieuGiamGia->ma_phieu_giam_gia, $maPhieuDaSuDung);

        $phanTramGiam = number_format($phieuGiamGia->phan_tram_giam, 2, '.', '');
        $phanTramGiam = rtrim($phanTramGiam, '0');
        $phieuGiamGia->phan_tram_giam_hien_thi = rtrim($phanTramGiam, '.');

        $phieuGiamGia->don_toi_thieu_hien_thi = 'Không yêu cầu';
        if ($phieuGiamGia->gia_tri_don_toi_thieu > 0) {
            $phieuGiamGia->don_toi_thieu_hien_thi = number_format($phieuGiamGia->gia_tri_don_toi_thieu, 0, ',', '.').' đ';
        }

        $phieuGiamGia->giam_toi_da_hien_thi = 'Không giới hạn';
        if ($phieuGiamGia->so_tien_giam_toi_da != null) {
            $phieuGiamGia->giam_toi_da_hien_thi = number_format($phieuGiamGia->so_tien_giam_toi_da, 0, ',', '.').' đ';
        }

        $phieuGiamGia->ngay_het_han_hien_thi = 'Không giới hạn';
        if ($phieuGiamGia->het_han_luc != null) {
            $phieuGiamGia->ngay_het_han_hien_thi = $phieuGiamGia->het_han_luc->format('d/m/Y');
        }
    }
}