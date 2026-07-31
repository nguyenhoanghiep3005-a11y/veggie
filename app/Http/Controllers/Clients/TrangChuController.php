<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\DanhMuc;
use App\Models\SanPham;

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
        ])->where('trang_thai', 'con_hang')
            ->where('ton_kho', '>', 0)
            ->whereHas('loHangKhos', function ($query) {
                $query->where('so_luong_con', '>', 0)
                    ->where('gia_khuyen_mai', '>', 0)
                    ->where(function ($query) {
                        $query->whereNull('han_su_dung')
                            ->orWhereDate('han_su_dung', '>=', today());
                    });
            })
            ->orderBy('ma_san_pham', 'desc')
            ->paginate(12, ['*'], 'khuyen_mai');

        foreach ($sanPhamKhuyenMais as $sanPham) {
            $this->chuanBiSanPhamTrangChu($sanPham, $soLuongDaBans);
        }

        $danhMucBanChays = $this->layDanhMucBanChay($danhMucs);
        $sanPhamModals = $this->laySanPhamModal(
            $sanPhamKhuyenMais,
            $danhMucBanChays
        );

        return view('clients.pages.home', compact(
            'danhMucs',
            'sanPhamKhuyenMais',
            'danhMucBanChays',
            'sanPhamModals'
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

    // Lay cac danh muc co san pham ban chay khong trong dot khuyen mai.
    private function layDanhMucBanChay($danhMucs)
    {
        $danhMucBanChays = [];
        $viTriTab = 0;

        foreach ($danhMucs as $danhMuc) {
            $sanPhamBanChays = [];

            foreach ($danhMuc->sanPhams as $sanPham) {
                if ($sanPham->so_luong_da_ban <= 0 || $sanPham->dang_khuyen_mai) {
                    continue;
                }

                $sanPhamBanChays[] = $sanPham;
            }

            if (count($sanPhamBanChays) == 0) {
                continue;
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

            $danhMuc->san_pham_ban_chays = $sanPhamBanChays;
            $danhMuc->lop_tab = '';
            $danhMuc->lop_noi_dung = '';

            if ($viTriTab == 0) {
                $danhMuc->lop_tab = 'active show';
                $danhMuc->lop_noi_dung = 'active show';
            }

            $danhMucBanChays[] = $danhMuc;
            $viTriTab++;
        }

        return $danhMucBanChays;
    }

    // Gom san pham de tao modal xem nhanh mot lan o cuoi trang.
    private function laySanPhamModal($sanPhamKhuyenMais, $danhMucBanChays)
    {
        $sanPhamModals = [];
        $maSanPhamDaThems = [];

        foreach ($sanPhamKhuyenMais as $sanPham) {
            $sanPhamModals[] = $sanPham;
            $maSanPhamDaThems[$sanPham->ma_san_pham] = true;
        }

        foreach ($danhMucBanChays as $danhMuc) {
            foreach ($danhMuc->san_pham_ban_chays as $sanPham) {
                if (isset($maSanPhamDaThems[$sanPham->ma_san_pham])) {
                    continue;
                }

                $sanPhamModals[] = $sanPham;
                $maSanPhamDaThems[$sanPham->ma_san_pham] = true;
            }
        }

        return $sanPhamModals;
    }
}