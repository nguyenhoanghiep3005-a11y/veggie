<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\NguoiDung;
use App\Models\SanPham;
use App\Models\SanPhamYeuThich;

class TongQuanController extends Controller
{
    // Hien thi cac so lieu tong quan cua trang quan tri.
    public function hienThiTongQuan()
    {
        $trangThaiCoDoanhThu = [
            'hoan_thanh',
            
        ];

        $tongNguoiDung = NguoiDung::where('ma_vai_tro', 3)->count();
        $tongSanPham = SanPham::count();
        $tongDonHang = DonHang::count();
        $tongDoanhThu = DonHang::whereIn('trang_thai', $trangThaiCoDoanhThu)
            ->sum('tong_tien');

        $thongKeDoanhThuTuan = $this->layDoanhThuBayNgay($trangThaiCoDoanhThu);
        $thongKeDoanhThuThang = $this->layDoanhThuTheoThang($trangThaiCoDoanhThu);
        $thongKeNguoiDung = $this->layNguoiDungMoiTheoThang();
        $topSanPhamYeuThich = $this->layTopSanPhamYeuThich();
        $topSanPhamBanChay = $this->layTopSanPhamBanChay($trangThaiCoDoanhThu);

        $danhMucs = DanhMuc::with('sanPhams')->orderBy('ten')->get();
        $tenDanhMucs = [];
        $soLuongSanPhams = [];

        foreach ($danhMucs as $danhMuc) {
            $tenDanhMucs[] = $danhMuc->ten;
            $soLuongSanPhams[] = $danhMuc->sanPhams->count();
        }

        return view('admin.pages.tong-quan', compact(
            'tongNguoiDung',
            'tongSanPham',
            'tongDonHang',
            'tongDoanhThu',
            'danhMucs',
            'tenDanhMucs',
            'soLuongSanPhams',
            'thongKeDoanhThuTuan',
            'thongKeDoanhThuThang',
            'thongKeNguoiDung',
            'topSanPhamYeuThich',
            'topSanPhamBanChay'
        ));
    }

    // Lay doanh thu cua tung ngay trong 7 ngay gan nhat.
    private function layDoanhThuBayNgay($trangThaiCoDoanhThu)
    {
        $ngayBatDau = now()->startOfDay()->subDays(6);
        $ngayKetThuc = now()->endOfDay();
        $donHangs = DonHang::whereIn('trang_thai', $trangThaiCoDoanhThu)->get();

        $nhanNgay = [];
        $doanhThus = [];

        for ($soNgay = 0; $soNgay < 7; $soNgay++) {
            $ngay = $ngayBatDau->copy()->addDays($soNgay);
            $doanhThuTrongNgay = 0;

            foreach ($donHangs as $donHang) {
                $ngayHoanTat = $donHang->hoan_tat_luc ?: $donHang->updated_at;

                if ($ngayHoanTat->isSameDay($ngay)) {
                    $doanhThuTrongNgay += (float) $donHang->tong_tien;
                }
            }

            $nhanNgay[] = $ngay->format('d/m');
            $doanhThus[] = $doanhThuTrongNgay;
        }

        return [
            'nhan' => $nhanNgay,
            'doanh_thu' => $doanhThus,
        ];
    }

    // Lay doanh thu cua tung thang trong nam hien tai.
    private function layDoanhThuTheoThang($trangThaiCoDoanhThu)
    {
        $namHienTai = now()->year;
        $donHangs = DonHang::whereIn('trang_thai', $trangThaiCoDoanhThu)->get();

        $nhanThang = [];
        $doanhThus = [];

        for ($thang = 1; $thang <= 12; $thang++) {
            $doanhThuTrongThang = 0;

            foreach ($donHangs as $donHang) {
                $ngayHoanTat = $donHang->hoan_tat_luc ?: $donHang->updated_at;

                if ($ngayHoanTat->year == $namHienTai && $ngayHoanTat->month == $thang) {
                    $doanhThuTrongThang += (float) $donHang->tong_tien;
                }
            }

            $nhanThang[] = 'Tháng '.$thang;
            $doanhThus[] = $doanhThuTrongThang;
        }

        return [
            'nam' => $namHienTai,
            'nhan' => $nhanThang,
            'doanh_thu' => $doanhThus,
        ];
    }

    // Lay so khach hang dang ky moi trong 6 thang gan nhat.
    private function layNguoiDungMoiTheoThang()
    {
        $thangBatDau = now()->startOfMonth()->subMonths(5);
        $nguoiDungs = NguoiDung::where('ma_vai_tro', 3)
            ->where('created_at', '>=', $thangBatDau)
            ->get();

        $nhanThang = [];
        $soNguoiDungs = [];

        for ($soThang = 0; $soThang < 6; $soThang++) {
            $thang = $thangBatDau->copy()->addMonths($soThang);
            $soNguoiDungTrongThang = 0;

            foreach ($nguoiDungs as $nguoiDung) {
                if ($nguoiDung->created_at->year == $thang->year
                    && $nguoiDung->created_at->month == $thang->month) {
                    $soNguoiDungTrongThang++;
                }
            }

            $nhanThang[] = $thang->format('m/Y');
            $soNguoiDungs[] = $soNguoiDungTrongThang;
        }

        return [
            'nhan' => $nhanThang,
            'so_nguoi_dung' => $soNguoiDungs,
        ];
    }

    // Lay 5 san pham duoc khach hang them vao danh sach yeu thich nhieu nhat.
    private function layTopSanPhamYeuThich()
    {
        $sanPhamYeuThichs = SanPhamYeuThich::with('sanPham')->get();
        $thongKeSanPhams = [];

        foreach ($sanPhamYeuThichs as $sanPhamYeuThich) {
            if (! $sanPhamYeuThich->sanPham) {
                continue;
            }

            $maSanPham = $sanPhamYeuThich->ma_san_pham;

            if (! isset($thongKeSanPhams[$maSanPham])) {
                $thongKeSanPhams[$maSanPham] = [
                    'ten' => $sanPhamYeuThich->sanPham->ten_hien_thi,
                    'so_luot' => 0,
                ];
            }

            $thongKeSanPhams[$maSanPham]['so_luot']++;
        }

        $danhSachSanPhams = [];

        foreach ($thongKeSanPhams as $sanPham) {
            $danhSachSanPhams[] = $sanPham;
        }

        $thongKeSanPhams = $danhSachSanPhams;
        $tongSoSanPham = count($thongKeSanPhams);

        for ($viTri = 0; $viTri < $tongSoSanPham; $viTri++) {
            for ($viTriSoSanh = $viTri + 1; $viTriSoSanh < $tongSoSanPham; $viTriSoSanh++) {
                if ($thongKeSanPhams[$viTri]['so_luot'] < $thongKeSanPhams[$viTriSoSanh]['so_luot']) {
                    $sanPhamTam = $thongKeSanPhams[$viTri];
                    $thongKeSanPhams[$viTri] = $thongKeSanPhams[$viTriSoSanh];
                    $thongKeSanPhams[$viTriSoSanh] = $sanPhamTam;
                }
            }
        }

        $tenSanPhams = [];
        $soLuotYeuThichs = [];
        $soSanPhamDaLay = 0;

        foreach ($thongKeSanPhams as $sanPham) {
            if ($soSanPhamDaLay >= 5) {
                break;
            }

            $tenSanPhams[] = $sanPham['ten'];
            $soLuotYeuThichs[] = $sanPham['so_luot'];
            $soSanPhamDaLay++;
        }

        return [
            'nhan' => $tenSanPhams,
            'so_luot' => $soLuotYeuThichs,
        ];
    }

    // Lay 5 san pham co so luong ban thanh cong cao nhat.
    private function layTopSanPhamBanChay($trangThaiCoDoanhThu)
    {
        $donHangs = DonHang::with('chiTietDonHangs.sanPham')
            ->whereIn('trang_thai', $trangThaiCoDoanhThu)
            ->get();
        $thongKeSanPhams = [];

        foreach ($donHangs as $donHang) {
            foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
                if (! $chiTietDonHang->sanPham) {
                    continue;
                }

                $maSanPham = $chiTietDonHang->ma_san_pham;

                if (! isset($thongKeSanPhams[$maSanPham])) {
                    $thongKeSanPhams[$maSanPham] = [
                        'ten' => $chiTietDonHang->sanPham->ten_hien_thi,
                        'so_luong' => 0,
                        'doanh_thu' => 0,
                    ];
                }

                $thongKeSanPhams[$maSanPham]['so_luong'] += (int) $chiTietDonHang->so_luong;
                $thongKeSanPhams[$maSanPham]['doanh_thu'] +=
                    (float) $chiTietDonHang->gia * (int) $chiTietDonHang->so_luong;
            }
        }

        $danhSachSanPhams = [];

        foreach ($thongKeSanPhams as $sanPham) {
            $danhSachSanPhams[] = $sanPham;
        }

        $thongKeSanPhams = $danhSachSanPhams;
        $tongSoSanPham = count($thongKeSanPhams);

        for ($viTri = 0; $viTri < $tongSoSanPham; $viTri++) {
            for ($viTriSoSanh = $viTri + 1; $viTriSoSanh < $tongSoSanPham; $viTriSoSanh++) {
                if ($thongKeSanPhams[$viTri]['so_luong'] < $thongKeSanPhams[$viTriSoSanh]['so_luong']) {
                    $sanPhamTam = $thongKeSanPhams[$viTri];
                    $thongKeSanPhams[$viTri] = $thongKeSanPhams[$viTriSoSanh];
                    $thongKeSanPhams[$viTriSoSanh] = $sanPhamTam;
                }
            }
        }

        $topSanPhams = [];
        $soSanPhamDaLay = 0;

        foreach ($thongKeSanPhams as $sanPham) {
            if ($soSanPhamDaLay >= 5) {
                break;
            }

            $topSanPhams[] = $sanPham;
            $soSanPhamDaLay++;
        }

        return $topSanPhams;
    }
}