<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\DiaChiGiaoHang;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TaiKhoanController extends Controller
{
    // Hien thi thong tin tai khoan, dia chi va don hang cua nguoi dung.
    public function hienThiTaiKhoan()
    {
        $nguoiDung = Auth::user();
        $diaChis = DiaChiGiaoHang::where(
            'ma_nguoi_dung',
            $nguoiDung->ma_nguoi_dung
        )->get();
        $donHangs = DonHang::where(
            'ma_nguoi_dung',
            $nguoiDung->ma_nguoi_dung
        )->orderBy('created_at', 'desc')->paginate(10);

        return view(
            'clients.pages.tai-khoan',
            compact('nguoiDung', 'diaChis', 'donHangs')
        );
    }

    // Cap nhat thong tin ca nhan cua nguoi dung.
    public function capNhatTaiKhoan(Request $request)
    {
        $data = $request->validate([
            'ltn__name' => 'required|string|max:255',
            'ltn__so_dien_thoai' => 'nullable|regex:/^[0-9]{10,11}$/',
            'ltn__address' => 'nullable|string|max:255',
        ]);

        $nguoiDung = Auth::user();
        $nguoiDung->ten = $data['ltn__name'];
        $nguoiDung->so_dien_thoai = $data['ltn__so_dien_thoai'];
        $nguoiDung->dia_chi = $data['ltn__address'];
        $nguoiDung->save();

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Cập nhật thông tin thành công.',
        ]);
    }

    // Kiem tra mat khau hien tai va luu mat khau moi.
    public function doiMatKhau(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_new_password' => 'required|same:new_password',
        ]);

        $nguoiDung = Auth::user();

        if (! Hash::check($data['current_password'], $nguoiDung->mat_khau)) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Mật khẩu hiện tại không đúng.',
            ], 422);
        }

        if (Hash::check($data['new_password'], $nguoiDung->mat_khau)) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại.',
            ], 422);
        }

        $nguoiDung->mat_khau = Hash::make($data['new_password']);
        $nguoiDung->save();

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Đổi mật khẩu thành công.',
        ]);
    }

    // Them dia chi giao hang moi cho nguoi dung.
    public function themDiaChi(Request $request)
    {
        $data = $request->validate([
            'ho_ten' => 'required|string|min:3|max:255',
            'so_dien_thoai' => 'required|regex:/^[0-9]{10,11}$/',
            'dia_chi' => 'required|string|min:5|max:255',
            'ma_tinh' => 'required|integer',
            'ma_huyen' => 'required|integer',
            'ma_xa' => 'required|string|max:50',
            'province_name' => 'required|string|max:100',
            'district_name' => 'required|string|max:100',
            'ward_name' => 'required|string|max:100',
            'mac_dinh' => 'nullable',
        ]);

        $laDiaChiDauTien = ! DiaChiGiaoHang::where(
            'ma_nguoi_dung',
            Auth::id()
        )->exists();
        $datLamMacDinh = isset($data['mac_dinh']) || $laDiaChiDauTien;

        if ($datLamMacDinh) {
            DiaChiGiaoHang::where(
                'ma_nguoi_dung',
                Auth::id()
            )->update(['mac_dinh' => 0]);
        }

        DiaChiGiaoHang::create([
            'ma_nguoi_dung' => Auth::id(),
            'ho_ten' => trim($data['ho_ten']),
            'so_dien_thoai' => trim($data['so_dien_thoai']),
            'dia_chi' => trim($data['dia_chi']),
            'tinh_thanh' => $data['ward_name'] . ', ' . $data['district_name'] . ', ' . $data['province_name'],
            'ma_tinh' => $data['ma_tinh'],
            'ma_huyen' => $data['ma_huyen'],
            'ma_xa' => $data['ma_xa'],
            'mac_dinh' => $datLamMacDinh ? 1 : 0,
        ]);

        toastr()->success('Đã thêm địa chỉ giao hàng.');

        return back();
    }

    // Nguoi dung chọn 1 dc lam dia chi mac dinh.
    public function datDiaChiMacDinh($maDiaChi)
    {
        $diaChi = DiaChiGiaoHang::where(
            'ma_dia_chi_giao_hang',
            $maDiaChi
        )->where('ma_nguoi_dung', Auth::id())->firstOrFail();

        DiaChiGiaoHang::where(
            'ma_nguoi_dung',
            Auth::id()
        )->update(['mac_dinh' => 0]);

        $diaChi->mac_dinh = 1;
        $diaChi->save();

        toastr()->success('Đã cập nhật địa chỉ mặc định.');

        return back();
    }

    // Xoa dia chi va chon lai dia chi mac dinh neu can.
    public function xoaDiaChi($maDiaChi)
    {
        $diaChi = DiaChiGiaoHang::where(
            'ma_dia_chi_giao_hang',
            $maDiaChi
        )->where('ma_nguoi_dung', Auth::id())->firstOrFail();

        $laDiaChiMacDinh = $diaChi->mac_dinh;
        $diaChi->delete();

        if ($laDiaChiMacDinh) {
            $diaChiMoi = DiaChiGiaoHang::where(
                'ma_nguoi_dung',
                Auth::id()
            )->first();

            if ($diaChiMoi) {
                $diaChiMoi->mac_dinh = 1;
                $diaChiMoi->save();
            }
        }

        toastr()->success('Đã xóa địa chỉ.');

        return back();
    }
}
