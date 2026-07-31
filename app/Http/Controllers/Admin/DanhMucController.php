<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DanhMucController extends Controller
{
    // Hien thi form them danh muc.
    public function hienThiFormThemDanhMuc()
    {
        return view('admin.pages.them-danh-muc');
    }

    // Luu danh muc moi.
    public function themDanhMuc(Request $request)
    {
        $data = $request->validate([
            'ten' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        $data['duong_dan'] = Str::slug($data['ten']);
        $data['hinh_anh'] = $this->luuHinhAnhDanhMuc($request);

        DanhMuc::create($data);

        return redirect()->route('admin.danh-muc.them')
            ->with('success', 'Thêm danh mục thành công.');
    }

    // Hien thi danh sach danh muc.
    public function hienThiDanhSachDanhMuc()
    {
        $danhMucs = DanhMuc::orderBy('ma_danh_muc', 'desc')->paginate(10);

        return view('admin.pages.danh-muc', compact('danhMucs'));
    }

    // Cap nhat thong tin danh muc bang Ajax.
    public function capNhatDanhMuc(Request $request)
    {
        $data = $request->validate([
            'ma_danh_muc' => 'required|integer|exists:danh_muc,ma_danh_muc',
            'ten' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        $danhMuc = DanhMuc::findOrFail($data['ma_danh_muc']);
        $danhMuc->ten = $data['ten'];
        $danhMuc->duong_dan = Str::slug($data['ten']);
        $danhMuc->mo_ta = $data['mo_ta'];

        if ($request->hasFile('hinh_anh')) {
            $this->xoaHinhAnhDanhMuc($danhMuc->hinh_anh);
            $danhMuc->hinh_anh = $this->luuHinhAnhDanhMuc($request);
        }

        $danhMuc->save();

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Cập nhật danh mục thành công.',
            'du_lieu' => [
                'ma_danh_muc' => $danhMuc->ma_danh_muc,
                'ten' => $danhMuc->ten,
                'duong_dan' => $danhMuc->duong_dan,
                'mo_ta' => $danhMuc->mo_ta,
                'duong_dan_hinh_anh' => $danhMuc->duong_dan_hinh_anh,
            ],
        ]);
    }

    // Xoa danh muc neu chua co san pham.
    public function xoaDanhMuc(Request $request)
    {
        $data = $request->validate([
            'ma_danh_muc' => 'required|integer|exists:danh_muc,ma_danh_muc',
        ]);

        $danhMuc = DanhMuc::findOrFail($data['ma_danh_muc']);

        if ($danhMuc->sanPhams()->exists()) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Danh mục đang có sản phẩm nên không thể xóa.',
            ], 422);
        }

        $this->xoaHinhAnhDanhMuc($danhMuc->hinh_anh);
        $danhMuc->delete();

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Xóa danh mục thành công.',
        ]);
    }

    // Luu hinh anh danh muc va tra ve duong dan.
    private function luuHinhAnhDanhMuc($request)
    {
        if (! $request->hasFile('hinh_anh')) {
            return null;
        }

        $hinhAnh = $request->file('hinh_anh');
        $tenHinhAnh = now()->timestamp.'_'.uniqid().'.'.$hinhAnh->getClientOriginalExtension();

        return $hinhAnh->storeAs('uploads/categories', $tenHinhAnh, 'public');
    }

    // Xoa hinh anh cu cua danh muc.
    private function xoaHinhAnhDanhMuc($duongDanHinhAnh)
    {
        if ($duongDanHinhAnh) {
            Storage::disk('public')->delete($duongDanHinhAnh);
        }
    }
}