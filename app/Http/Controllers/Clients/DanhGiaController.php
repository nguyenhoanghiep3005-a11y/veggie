<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DanhGiaController extends Controller
{
    // Hien thi lai danh sach danh gia cua san pham.
    public function hienThiDanhSachDanhGia(SanPham $sanPham)
    {
        $sanPham->load('danhGias.nguoiDung');

        return view(
            'clients.components.modals.includes.danh-sach-danh-gia',
            compact('sanPham')
        )->render();
    }

    // Luu danh gia moi cua nguoi dung.
    public function themDanhGia(Request $request)
    {
        $data = $request->validate([
            'ma_san_pham' => 'required|integer|exists:san_pham,ma_san_pham',
            'so_sao' => 'required|integer|min:1|max:5',
            'binh_luan' => 'nullable|string|max:1000',
        ]);

        $danhGia = new DanhGia();
        $danhGia->ma_nguoi_dung = Auth::id();
        $danhGia->ma_san_pham = $data['ma_san_pham'];
        $danhGia->so_sao = $data['so_sao'];
        $danhGia->binh_luan = isset($data['binh_luan'])
            ? trim($data['binh_luan'])
            : null;
        $danhGia->save();

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Đã gửi đánh giá.',
        ]);
    }
}
