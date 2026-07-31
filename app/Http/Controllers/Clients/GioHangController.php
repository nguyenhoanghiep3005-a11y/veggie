<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Services\GioHangService;
use Exception;
use Illuminate\Http\Request;

class GioHangController extends Controller
{
    private $gioHang;

    // Khoi tao dich vu xu ly gio hang.
    public function __construct(GioHangService $gioHang)
    {
        $this->gioHang = $gioHang;
    }

    // Them san pham vao gio hang.
    public function themVaoGioHang(Request $request)
    {
        $data = $request->validate([
            'ma_san_pham' => 'required|integer|exists:san_pham,ma_san_pham',
            'so_luong' => 'nullable|integer|min:1',
        ]);

        $sanPham = SanPham::find($data['ma_san_pham']);
        $soLuong = isset($data['so_luong']) ? (int) $data['so_luong'] : 1;

        try {
            $this->gioHang->themSanPham($sanPham, $soLuong);
        } catch (Exception $exception) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Đã thêm sản phẩm vào giỏ hàng.',
            'so_luong_gio_hang' => $this->gioHang->demSoLuongSanPham(),
        ]);
    }

    // Hien thi noi dung gio hang nho tren thanh dau trang.
    public function hienThiGioHangNho()
    {
        $sanPhamGioHangs = $this->gioHang->laySanPhamGioHang();
        $tongTienGioHang = $this->gioHang->tinhTongTien();

        return response()->json([
            'trang_thai' => true,
            'noi_dung' => view(
                'clients.components.modals.includes.gio-hang-nho',
                compact('sanPhamGioHangs', 'tongTienGioHang')
            )->render(),
            'so_luong_gio_hang' => $this->gioHang->demSoLuongSanPham(),
            'tong_tien' => number_format($tongTienGioHang, 0, ',', '.'),
        ]);
    }

    // Hien thi trang gio hang lon.
    public function hienThiGioHang()
    {
        $sanPhamGioHangs = $this->gioHang->laySanPhamGioHang();
        $tongTienGioHang = $this->gioHang->tinhTongTien();

        return view(
            'clients.pages.gio-hang',
            compact('sanPhamGioHangs', 'tongTienGioHang')
        );
    }

    // Cap nhat so luong san pham trong gio hang.
    public function capNhatGioHang(Request $request)
    {
        $data = $request->validate([
            'ma_san_pham' => 'required|integer',
            'so_luong' => 'required|integer|min:1',
        ]);

        try {
            $this->gioHang->capNhatSoLuong(
                (int) $data['ma_san_pham'],
                (int) $data['so_luong']
            );
        } catch (Exception $exception) {
            return response()->json([
                'trang_thai' => false,
                'thong_bao' => $exception->getMessage(),
            ], 422);
        }

        $sanPhamCanTim = null;
        $sanPhamGioHangs = $this->gioHang->laySanPhamGioHang();

        foreach ($sanPhamGioHangs as $sanPhamGioHang) {
            if ($sanPhamGioHang['ma_san_pham'] == $data['ma_san_pham']) {
                $sanPhamCanTim = $sanPhamGioHang;
                break;
            }
        }

        return response()->json([
            'trang_thai' => true,
            'so_luong' => $sanPhamCanTim ? $sanPhamCanTim['so_luong'] : (int) $data['so_luong'],
            'tam_tinh' => number_format($sanPhamCanTim ? $sanPhamCanTim['tam_tinh'] : 0, 0, ',', '.'),
            'tong_tien' => number_format($this->gioHang->tinhTongTien(), 0, ',', '.'),
            'so_luong_gio_hang' => $this->gioHang->demSoLuongSanPham(),
        ]);
    }

    // Xoa san pham khoi gio hang nho.
    public function xoaKhoiGioHangNho(Request $request)
    {
        return $this->xoaSanPhamKhoiGioHang($request);
    }

    // Xoa san pham khoi trang gio hang.
    public function xoaKhoiGioHang(Request $request)
    {
        return $this->xoaSanPhamKhoiGioHang($request);
    }

    // Xu ly chung viec xoa mot san pham khoi gio hang.
    private function xoaSanPhamKhoiGioHang(Request $request)
    {
        $data = $request->validate([
            'ma_san_pham' => 'required|integer',
        ]);

        $this->gioHang->xoaSanPham((int) $data['ma_san_pham']);

        return response()->json([
            'trang_thai' => true,
            'thong_bao' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'tong_tien' => number_format($this->gioHang->tinhTongTien(), 0, ',', '.'),
            'so_luong_gio_hang' => $this->gioHang->demSoLuongSanPham(),
        ]);
    }
}
