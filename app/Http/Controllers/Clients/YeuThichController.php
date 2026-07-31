<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\SanPhamYeuThich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YeuThichController extends Controller
{
    // Hien thi danh sach san pham yeu thich cua nguoi dung.
    public function hienThiDanhSachYeuThich()
    {
        $sanPhamYeuThichs = SanPhamYeuThich::with(
            'sanPham.hinhAnhDauTien'
        )->where('ma_nguoi_dung', Auth::id())
            ->whereHas('sanPham')
            ->orderBy('ma_san_pham_yeu_thich', 'desc')
            ->paginate(10);

        return view(
            'clients.pages.yeu-thich',
            compact('sanPhamYeuThichs')
        );
    }

    // Them san pham vao danh sach yeu thich.
    public function themSanPhamYeuThich(Request $request)
    {
        $data = $request->validate([
            'ma_san_pham' => 'required|integer|exists:san_pham,ma_san_pham',
        ]);

        $sanPhamYeuThich = SanPhamYeuThich::where(
            'ma_nguoi_dung',
            Auth::id()
        )->where('ma_san_pham', $data['ma_san_pham'])->first();

        if (! $sanPhamYeuThich) {
            $sanPhamYeuThich = new SanPhamYeuThich();
            $sanPhamYeuThich->ma_nguoi_dung = Auth::id();
            $sanPhamYeuThich->ma_san_pham = $data['ma_san_pham'];
            $sanPhamYeuThich->save();
        }

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Đã thêm sản phẩm vào danh sách yêu thích.',
        ]);
    }

    // Xoa san pham khoi danh sach yeu thich.
    public function xoaSanPhamYeuThich(Request $request)
    {
        $data = $request->validate([
            'ma_san_pham' => 'required|integer|exists:san_pham,ma_san_pham',
        ]);

        SanPhamYeuThich::where(
            'ma_nguoi_dung',
            Auth::id()
        )->where('ma_san_pham', $data['ma_san_pham'])->delete();

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Đã xóa sản phẩm khỏi danh sách yêu thích.',
        ]);
    }
}
