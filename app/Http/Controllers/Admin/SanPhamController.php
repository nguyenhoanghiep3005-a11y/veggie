<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\HinhAnhSanPham;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SanPhamController extends Controller
{
    // Hien thi form them san pham.
    public function hienThiFormThemSanPham()
    {
        $danhMucs = DanhMuc::orderBy('ten')->get();

        return view('admin.pages.them-san-pham', compact('danhMucs'));
    }

    // Luu san pham moi, ton kho ban dau bang 0.
    public function themSanPham(Request $request)
    {
        $this->chuanHoaTepHinhAnh($request);

        $data = $request->validate($this->quyTacThemSanPham(), $this->thongBaoKiemTra());
        $data['duong_dan'] = Str::slug($data['ten'].'-'.$data['don_vi']).'-'.time();

        $sanPham = SanPham::create($data);
        $this->luuHinhAnhSanPham($sanPham, $request);

        return redirect()->route('admin.san-pham.them')
            ->with('success', 'Thêm sản phẩm thành công. Tồn kho sẽ tăng khi nhập hàng.');
    }

    // Hien thi danh sach san pham.
    public function hienThiDanhSachSanPham()
    {
        $sanPhams = SanPham::with(['danhMuc', 'hinhAnhs', 'hinhAnhDauTien'])
            ->orderBy('ma_san_pham', 'desc')
            ->paginate(10);
        $danhMucs = DanhMuc::orderBy('ten')->get();

        foreach ($sanPhams as $sanPham) {
            $sanPham->ten_danh_muc = 'Chưa phân loại';

            if ($sanPham->danhMuc) {
                $sanPham->ten_danh_muc = $sanPham->danhMuc->ten;
            }
        }

        return view('admin.pages.san-pham', compact('sanPhams', 'danhMucs'));
    }

    // Cap nhat san pham bang Ajax.
    public function capNhatSanPham(Request $request)
    {
        $this->chuanHoaTepHinhAnh($request);

        $data = $request->validate([
            'ma_san_pham' => 'required|exists:san_pham,ma_san_pham',
        ] + $this->quyTacCapNhatSanPham(), $this->thongBaoKiemTra());

        $sanPham = SanPham::findOrFail($data['ma_san_pham']);
        unset($data['ma_san_pham']);

        $sanPham->fill($data);
        $sanPham->save();

        if ($request->hasFile('images')) {
            $this->xoaHinhAnhSanPham($sanPham);
            $this->luuHinhAnhSanPham($sanPham, $request);
        }

        $sanPham->load('danhMuc', 'hinhAnhs', 'hinhAnhDauTien');

        $tenDanhMuc = 'Chưa phân loại';
        if ($sanPham->danhMuc) {
            $tenDanhMuc = $sanPham->danhMuc->ten;
        }

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Cập nhật sản phẩm thành công.',
            'du_lieu' => [
                'ma_san_pham' => $sanPham->ma_san_pham,
                'ten' => $sanPham->ten,
                'ten_hien_thi' => $sanPham->ten_hien_thi,
                'duong_dan' => $sanPham->duong_dan,
                'ten_danh_muc' => $tenDanhMuc,
                'mo_ta' => $sanPham->mo_ta,
                'gia' => $sanPham->gia,
                'don_vi' => $sanPham->don_vi,
                'duong_dan_hinh_anh' => $sanPham->duong_dan_hinh_anh,
                'hinh_anhs' => $this->layDuongDanHinhAnh($sanPham),
            ],
        ]);
    }

    // Xoa san pham va cac hinh anh lien quan.
    public function xoaSanPham(Request $request)
    {
        $request->validate([
            'ma_san_pham' => 'required|exists:san_pham,ma_san_pham',
        ]);

        $sanPham = SanPham::with('hinhAnhs')->findOrFail($request->ma_san_pham);

        if ($sanPham->chiTietDonHangs()->exists()
            || $sanPham->chiTietDonDatNhaps()->exists()
            || $sanPham->chiTietPhieuNhaps()->exists()
            || $sanPham->loHangKhos()->exists()) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => 'Sản phẩm đã phát sinh dữ liệu nên không thể xóa.',
            ], 422);
        }

        $this->xoaHinhAnhSanPham($sanPham);
        $sanPham->delete();

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Xóa sản phẩm thành công.',
        ]);
    }

    // Tra ve quy tac kiem tra khi them san pham.
    private function quyTacThemSanPham()
    {
        return [
            'ten' => 'required|string|max:255',
            'ma_danh_muc' => 'required|exists:danh_muc,ma_danh_muc',
            'mo_ta' => 'required|string',
            'gia' => 'required|numeric|min:0',
            'don_vi' => 'required|in:g,kg',
            'images' => 'nullable|array',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,gif,webp',
        ];
    }

    // Tra ve quy tac kiem tra khi cap nhat san pham.
    private function quyTacCapNhatSanPham()
    {
        return $this->quyTacThemSanPham();
    }

    // Tra ve thong bao kiem tra tep hinh anh.
    private function thongBaoKiemTra()
    {
        return [
            'images.*.image' => 'Tệp đã chọn phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
        ];
    }

    // Loai bo tep hinh anh rong truoc khi kiem tra du lieu.
    private function chuanHoaTepHinhAnh($request)
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $tepHinhAnhs = [];

        foreach ($request->file('images') as $tepHinhAnh) {
            if ($tepHinhAnh && $tepHinhAnh->isValid()) {
                $tepHinhAnhs[] = $tepHinhAnh;
            }
        }

        if (count($tepHinhAnhs) == 0) {
            $request->files->remove('images');

            return;
        }

        $request->files->set('images', $tepHinhAnhs);
    }

    // Luu cac hinh anh cua san pham vao storage.
    private function luuHinhAnhSanPham($sanPham, $request)
    {
        if (! $request->hasFile('images')) {
            return;
        }

        foreach ($request->file('images') as $hinhAnh) {
            $tenHinhAnh = time().'_'.uniqid().'.'.$hinhAnh->getClientOriginalExtension();
            $duongDanHinhAnh = 'uploads/products/'.$tenHinhAnh;
            Storage::disk('public')->put($duongDanHinhAnh, file_get_contents($hinhAnh));

            HinhAnhSanPham::create([
                'ma_san_pham' => $sanPham->ma_san_pham,
                'hinh_anh' => $duongDanHinhAnh,
            ]);
        }
    }

    // Xoa hinh anh cu cua san pham.
    private function xoaHinhAnhSanPham($sanPham)
    {
        foreach ($sanPham->hinhAnhs as $hinhAnh) {
            Storage::disk('public')->delete($hinhAnh->hinh_anh);
        }

        HinhAnhSanPham::where('ma_san_pham', $sanPham->ma_san_pham)->delete();
    }

    // Lay danh sach duong dan hinh anh de tra ve Ajax.
    private function layDuongDanHinhAnh($sanPham)
    {
        $duongDanHinhAnhs = [];

        foreach ($sanPham->hinhAnhs as $hinhAnh) {
            $duongDanHinhAnhs[] = asset('storage/'.$hinhAnh->hinh_anh);
        }

        return $duongDanHinhAnhs;
    }
}