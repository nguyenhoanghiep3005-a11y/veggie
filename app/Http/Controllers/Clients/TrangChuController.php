<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\SanPhamYeuThich;
use Illuminate\Support\Facades\Auth;

class TrangChuController extends Controller
{
    // Hien thi danh muc, san pham khuyen mai va san pham ban chay.
    public function hienThiTrangChu()
    {
        $soLuongDaBans = $this->laySoLuongDaBanTheoSanPham();

        $danhMucs = DanhMuc::with([
            'sanPhams.hinhAnhDauTien',
            'sanPhams.danhGias',
            'sanPhams.loHangKhos',
        ])->orderBy('ten')->get();

        foreach ($danhMucs as $danhMuc) {
            foreach ($danhMuc->sanPhams as $sanPham) {
                $this->chuanBiSanPhamTrangChu($sanPham, $soLuongDaBans);
            }
        }

        $sanPhamKhuyenMais = SanPham::with([
            'hinhAnhDauTien',
            'danhGias',
            'loHangKhos',
        ])->whereHas('loHangKhos', function ($query) {
            $query->where('so_luong_con', '>', 0)
                ->where('gia_khuyen_mai', '>', 0)
                ->whereDate('han_su_dung', '>=', today());
        })
            ->orderBy('ma_san_pham', 'desc')
            ->paginate(12, ['*'], 'khuyen_mai');

        foreach ($sanPhamKhuyenMais as $sanPham) {
            $this->chuanBiSanPhamTrangChu($sanPham, $soLuongDaBans);
        }

        $sanPhamBanChays = $this->laySanPhamBanChay($danhMucs);
        $sanPhamCaNhans = [];

        if (Auth::check()) {
            $maSanPhamCaNhans = [];
            $maSanPhamDaCo = [];

            $sanPhamYeuThichs = SanPhamYeuThich::where('ma_nguoi_dung', Auth::id())
                ->orderBy('ma_san_pham_yeu_thich', 'desc')
                ->get();

            foreach ($sanPhamYeuThichs as $sanPhamYeuThich) {
                if (! isset($maSanPhamDaCo[$sanPhamYeuThich->ma_san_pham])) {
                    $maSanPhamCaNhans[] = $sanPhamYeuThich->ma_san_pham;
                    $maSanPhamDaCo[$sanPhamYeuThich->ma_san_pham] = true;
                }
            }

            $donHangs = DonHang::with('chiTietDonHangs')
                ->where('ma_nguoi_dung', Auth::id())
                ->where('trang_thai', 'hoan_thanh')
                ->orderBy('ma_don_hang', 'desc')
                ->get();

            foreach ($donHangs as $donHang) {
                foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
                    if (! isset($maSanPhamDaCo[$chiTietDonHang->ma_san_pham])) {
                        $maSanPhamCaNhans[] = $chiTietDonHang->ma_san_pham;
                        $maSanPhamDaCo[$chiTietDonHang->ma_san_pham] = true;
                    }
                }
            }

            foreach ($maSanPhamCaNhans as $maSanPham) {
                if (count($sanPhamCaNhans) >= 12) {
                    break;
                }

                $sanPham = SanPham::with([
                    'hinhAnhDauTien',
                    'danhGias',
                    'loHangKhos',
                ])->find($maSanPham);

                if (! $sanPham || $sanPham->soLuongCoTheBan() <= 0) {
                    continue;
                }

                $this->chuanBiSanPhamTrangChu($sanPham, $soLuongDaBans);
                $sanPhamCaNhans[] = $sanPham;
            }
        }

        return view('clients.pages.home', compact(
            'danhMucs',
            'sanPhamKhuyenMais',
            'sanPhamBanChays',
            'sanPhamCaNhans'
        ));
    }

    // Tinh so luong da ban cua tung san pham tu cac don da giao.
    private function laySoLuongDaBanTheoSanPham()
    {
        $soLuongDaBans = [];
        $chiTietDonHangs = ChiTietDonHang::with('donHang')->get();
        $trangThaiDaBan = [
            'hoan_thanh',
        ];

        foreach ($chiTietDonHangs as $chiTietDonHang) {
            if (! $chiTietDonHang->donHang) {
                continue;
            }

            if (! in_array($chiTietDonHang->donHang->trang_thai, $trangThaiDaBan)) {
                continue;
            }

            if (! isset($soLuongDaBans[$chiTietDonHang->ma_san_pham])) {
                $soLuongDaBans[$chiTietDonHang->ma_san_pham] = 0;
            }

            $soLuongDaBans[$chiTietDonHang->ma_san_pham] +=
                (int) $chiTietDonHang->so_luong;
        }

        return $soLuongDaBans;
    }

    // Chuan bi gia, danh gia va so luong da ban cho san pham.
    private function chuanBiSanPhamTrangChu($sanPham, $soLuongDaBans)
    {
        $tongSoSao = 0;

        foreach ($sanPham->danhGias as $danhGia) {
            $tongSoSao += $danhGia->so_sao;
        }

        $sanPham->tong_danh_gia = $sanPham->danhGias->count();
        $sanPham->so_sao_trung_binh = 0;

        if ($sanPham->tong_danh_gia > 0) {
            $sanPham->so_sao_trung_binh =
                $tongSoSao / $sanPham->tong_danh_gia;
        }

        $sanPham->so_luong_da_ban = 0;
        if (isset($soLuongDaBans[$sanPham->ma_san_pham])) {
            $sanPham->so_luong_da_ban =
                $soLuongDaBans[$sanPham->ma_san_pham];
        }

        $sanPham->gia_khuyen_mai = $sanPham->gia_hien_tai;
        $sanPham->phan_tram_giam = 0;

        if ($sanPham->gia > 0 && $sanPham->gia_khuyen_mai < $sanPham->gia) {
            $sanPham->phan_tram_giam = round(
                (($sanPham->gia - $sanPham->gia_khuyen_mai) / $sanPham->gia)
                * 100
            );
        }
    }

    // Lay danh sach san pham ban chay khong trong dot khuyen mai.
    private function laySanPhamBanChay($danhMucs)
    {
        $sanPhamBanChays = [];

        foreach ($danhMucs as $danhMuc) {
            foreach ($danhMuc->sanPhams as $sanPham) {
                if ($sanPham->so_luong_da_ban <= 0 || $sanPham->dang_khuyen_mai || $sanPham->soLuongCoTheBan() <= 0) {
                    continue;
                }

                $sanPhamBanChays[] = $sanPham;
            }
        }

        usort($sanPhamBanChays, function ($sanPhamTruoc, $sanPhamSau) {
            if ($sanPhamSau->so_luong_da_ban > $sanPhamTruoc->so_luong_da_ban) {
                return 1;
            }

            if ($sanPhamSau->so_luong_da_ban < $sanPhamTruoc->so_luong_da_ban) {
                return -1;
            }

            return 0;
        });

        return array_slice($sanPhamBanChays, 0, 12);
    }

}