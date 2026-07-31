<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;

class NguoiDungController extends Controller
{
    // Hien thi danh sach tai khoan khach hang.
    public function hienThiDanhSachNguoiDung()
    {
        $nguoiDungs = NguoiDung::where('ma_vai_tro', 3)
            ->orderBy('ma_nguoi_dung', 'desc')
            ->paginate(9);

        foreach ($nguoiDungs as $nguoiDung) {
            $this->chuanBiDuLieuHienThi($nguoiDung);
        }

        return view('admin.pages.nguoi-dung', compact('nguoiDungs'));
    }

    // Chan hoac bo chan tai khoan khach hang.
    public function capNhatTrangThaiNguoiDung(Request $request)
    {
        $data = $request->validate([
            'ma_nguoi_dung' => 'required|exists:nguoi_dung,ma_nguoi_dung',
            'trang_thai' => 'required|in:hoat_dong,bi_khoa',
        ]);

        $nguoiDung = NguoiDung::where('ma_nguoi_dung', $data['ma_nguoi_dung'])
            ->where('ma_vai_tro', 3)
            ->first();

        if (! $nguoiDung) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Không tìm thấy người dùng.',
            ], 404);
        }

        $nguoiDung->trang_thai = $data['trang_thai'];
        $nguoiDung->save();

        $thongBao = 'Đã bỏ chặn tài khoản khách hàng.';
        if ($nguoiDung->trang_thai == 'bi_khoa') {
            $thongBao = 'Đã chặn tài khoản khách hàng.';
        }

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => $thongBao,
            'du_lieu' => [
                'trang_thai' => $nguoiDung->trang_thai,
            ],
        ]);
    }

    // Chuan bi thong tin hien thi cua tai khoan cho View.
    private function chuanBiDuLieuHienThi($nguoiDung)
    {
        $cacTrangThai = [
            'hoat_dong' => 'Đang hoạt động',
            'cho_kich_hoat' => 'Chờ kích hoạt',
            'bi_khoa' => 'Đã chặn',
        ];

        $nguoiDung->ten_trang_thai = $nguoiDung->trang_thai;
        if (isset($cacTrangThai[$nguoiDung->trang_thai])) {
            $nguoiDung->ten_trang_thai = $cacTrangThai[$nguoiDung->trang_thai];
        }

        $nguoiDung->dia_chi_hien_thi = '-';
        if ($nguoiDung->dia_chi) {
            $nguoiDung->dia_chi_hien_thi = $nguoiDung->dia_chi;
        }

        $nguoiDung->so_dien_thoai_hien_thi = '-';
        if ($nguoiDung->so_dien_thoai) {
            $nguoiDung->so_dien_thoai_hien_thi = $nguoiDung->so_dien_thoai;
        }
    }
}