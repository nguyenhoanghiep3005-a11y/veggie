<?php

namespace App\Services;

use App\Models\SanPham;
use Exception;

class GioHangService
{
    // Chuan hoa du lieu gio hang ve dung cau truc dang su dung.
    private function chuanHoaGioHang()
    {
        $gioHang = session('gio_hang', []);
        $gioHangDaChuanHoa = [];

        foreach ((array) $gioHang as $khoaSanPham => $dongGioHang) {
            if (! is_array($dongGioHang)) {
                continue;
            }

            $maSanPham = (int) $khoaSanPham;
            if (isset($dongGioHang['ma_san_pham'])) {
                $maSanPham = (int) $dongGioHang['ma_san_pham'];
            }

            $soLuong = 0;
            if (isset($dongGioHang['so_luong'])) {
                $soLuong = (int) $dongGioHang['so_luong'];
            }

            if ($maSanPham <= 0 || $soLuong <= 0) {
                continue;
            }

            $gioHangDaChuanHoa[(string) $maSanPham] = [
                'ma_san_pham' => $maSanPham,
                'so_luong' => $soLuong,
            ];
        }

        $this->luuGioHang($gioHangDaChuanHoa);

        return $gioHangDaChuanHoa;
    }

    // Luu gio hang va tong so luong dung chung cho header.
    private function luuGioHang($gioHang)
    {
        $tongSoLuong = 0;

        foreach ($gioHang as $dongGioHang) {
            $tongSoLuong += (int) $dongGioHang['so_luong'];
        }

        session()->put('gio_hang', $gioHang);
        session()->put('so_luong_gio_hang', $tongSoLuong);
    }

    // Lay danh sach san pham dang luu trong gio hang.
    public function laySanPhamGioHang()
    {
        $gioHang = $this->chuanHoaGioHang();
        $sanPhamGioHangs = [];

        if (count($gioHang) == 0) {
            return $sanPhamGioHangs;
        }

        foreach ($gioHang as $maSanPham => $dongGioHang) {
            $sanPham = SanPham::find($maSanPham);

            if (! $sanPham) {
                continue;
            }

            $soLuong = (int) $dongGioHang['so_luong'];
            $donGia = (float) $sanPham->gia_hien_tai;
            $tamTinh = $donGia * $soLuong;

            $sanPhamGioHangs[] = [
                'ma_san_pham' => $sanPham->ma_san_pham,
                'san_pham' => $sanPham,
                'ten' => $sanPham->ten_hien_thi,
                'hinh_anh' => $sanPham->duong_dan_hinh_anh,
                'so_luong' => $soLuong,
                'gia' => $donGia,
                'tam_tinh' => $tamTinh,
                'ton_kho' => $sanPham->soLuongCoTheBan(),
            ];
        }

        return $sanPhamGioHangs;
    }

    // Dem tong so luong san pham trong gio hang.
    public function demSoLuongSanPham()
    {
        $tongSoLuong = 0;
        $gioHang = $this->chuanHoaGioHang();

        foreach ($gioHang as $dongGioHang) {
            $tongSoLuong += (int) $dongGioHang['so_luong'];
        }

        return $tongSoLuong;
    }

    // Tinh tong tien cua tat ca san pham trong gio hang.
    public function tinhTongTien()
    {
        $tongTien = 0;
        $sanPhamGioHangs = $this->laySanPhamGioHang();

        foreach ($sanPhamGioHangs as $sanPhamGioHang) {
            $tongTien += $sanPhamGioHang['tam_tinh'];
        }

        return $tongTien;
    }

    // Them san pham va so luong vao gio hang.
    public function themSanPham(SanPham $sanPham, $soLuong)
    {
        $gioHang = $this->chuanHoaGioHang();
        $maSanPham = (string) $sanPham->ma_san_pham;
        $soLuongCu = 0;

        if (isset($gioHang[$maSanPham])) {
            $soLuongCu = (int) $gioHang[$maSanPham]['so_luong'];
        }

        $soLuongThem = (int) $soLuong;
        if ($soLuongThem < 1) {
            $soLuongThem = 1;
        }

        $soLuongMoi = $soLuongCu + $soLuongThem;

        if ($soLuongMoi > $sanPham->soLuongCoTheBan()) {
            throw new Exception('Số lượng vượt quá số lượng sản phẩm đang có thể bán.');
        }

        $gioHang[$maSanPham] = [
            'ma_san_pham' => $sanPham->ma_san_pham,
            'so_luong' => $soLuongMoi,
        ];

        $this->luuGioHang($gioHang);
    }

    // Cap nhat so luong cua mot san pham trong gio hang.
    public function capNhatSoLuong($maSanPham, $soLuong)
    {
        $sanPham = SanPham::find($maSanPham);
        $gioHang = $this->chuanHoaGioHang();
        $khoaSanPham = (string) $maSanPham;

        if (! $sanPham || ! isset($gioHang[$khoaSanPham])) {
            throw new Exception('Sản phẩm không tồn tại trong giỏ hàng.');
        }

        if ($soLuong > $sanPham->soLuongCoTheBan()) {
            throw new Exception('Số lượng vượt quá số lượng sản phẩm đang có thể bán.');
        }

        $soLuongCapNhat = (int) $soLuong;
        if ($soLuongCapNhat < 1) {
            $soLuongCapNhat = 1;
        }

        $gioHang[$khoaSanPham] = [
            'ma_san_pham' => (int) $maSanPham,
            'so_luong' => $soLuongCapNhat,
        ];

        $this->luuGioHang($gioHang);
    }

    // Xoa mot san pham khoi gio hang.
    public function xoaSanPham($maSanPham)
    {
        $gioHang = $this->chuanHoaGioHang();
        $khoaSanPham = (string) $maSanPham;

        if (isset($gioHang[$khoaSanPham])) {
            unset($gioHang[$khoaSanPham]);
        }

        $this->luuGioHang($gioHang);
    }

    // Xoa toan bo gio hang sau khi dat hang thanh cong.
    public function xoaGioHang()
    {
        session()->forget('gio_hang');
        session()->forget('so_luong_gio_hang');
    }
}