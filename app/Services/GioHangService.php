<?php

namespace App\Services;

use App\Models\SanPham;
use Exception;

class GioHangService
{
    // Lay danh sach san pham dang luu trong gio hang.
    public function laySanPhamGioHang()
    {
        $gioHang = $this->chuanHoaGioHang();
        $sanPhamGioHangs = [];

        if (empty($gioHang)) {
            return $sanPhamGioHangs;
        }

        $maSanPhams = array_keys($gioHang);
        $sanPhams = SanPham::whereIn('ma_san_pham', $maSanPhams)->get();

        foreach ($gioHang as $maSanPham => $dongGioHang) {
            $sanPhamCanTim = null;

            foreach ($sanPhams as $sanPham) {
                if ($sanPham->ma_san_pham == $maSanPham) {
                    $sanPhamCanTim = $sanPham;
                    break;
                }
            }

            if (! $sanPhamCanTim) {
                continue;
            }

            $soLuong = (int) $dongGioHang['so_luong'];
            $donGia = (float) $sanPhamCanTim->gia_hien_tai;
            $tamTinh = $donGia * $soLuong;

            $sanPhamGioHangs[] = [
                'ma_san_pham' => $sanPhamCanTim->ma_san_pham,
                'san_pham' => $sanPhamCanTim,
                'ten' => $sanPhamCanTim->ten_hien_thi,
                'hinh_anh' => $sanPhamCanTim->duong_dan_hinh_anh,
                'so_luong' => $soLuong,
                'gia' => $donGia,
                'tam_tinh' => $tamTinh,
                'ton_kho' => $sanPhamCanTim->soLuongCoTheBan(),
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

        $soLuongMoi = $soLuongCu + max(1, (int) $soLuong);

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

        $gioHang[$khoaSanPham] = [
            'ma_san_pham' => (int) $maSanPham,
            'so_luong' => max(1, (int) $soLuong),
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
    // Chuan hoa du lieu gio hang ve mot cau truc duy nhat.
    private function chuanHoaGioHang()
    {
        $gioHang = session('gio_hang', []);
        $gioHangDaChuanHoa = [];

        foreach ((array) $gioHang as $khoaSanPham => $dongGioHang) {
            if (is_array($dongGioHang)) {
                $maSanPham = isset($dongGioHang['ma_san_pham'])
                    ? (int) $dongGioHang['ma_san_pham']
                    : (int) $khoaSanPham;
                $soLuong = isset($dongGioHang['so_luong'])
                    ? (int) $dongGioHang['so_luong']
                    : 0;
            } else {
                $maSanPham = (int) $khoaSanPham;
                $soLuong = (int) $dongGioHang;
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
}
