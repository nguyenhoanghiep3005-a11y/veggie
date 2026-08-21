<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\YeuCauDoiTra;
use Exception;
use Illuminate\Support\Facades\DB;

class DoiTraController extends Controller
{
    // Duyet yeu cau doi tra dang cho xu ly.
    public function duyetYeuCau($maYeuCauDoiTra)
    {
        $yeuCauDoiTra = YeuCauDoiTra::with('donHang')->find($maYeuCauDoiTra);

        if (
            ! $yeuCauDoiTra
            || ! $yeuCauDoiTra->donHang
            || $yeuCauDoiTra->trang_thai != 'cho_duyet'
            || $yeuCauDoiTra->donHang->trang_thai != 'hoan_thanh'
        ) {
            return $this->phanHoiLoi('Yêu cầu không còn ở trạng thái chờ duyệt.');
        }

        $yeuCauDoiTra->trang_thai = 'da_duyet';
        $yeuCauDoiTra->duyet_luc = now();
        $yeuCauDoiTra->save();

        return $this->phanHoiThanhCong('Đã duyệt yêu cầu đổi trả.');
    }

    // Nhan hang loi va chuan bi san pham thay the.
    public function nhanHangDoiTra($maYeuCauDoiTra)
    {
        $yeuCauDoiTra = YeuCauDoiTra::with([
            'donHang.chiTietDonHangs.sanPham',
            'chiTiets.chiTietDonHang.sanPham',
        ])->find($maYeuCauDoiTra);

        if (
            ! $yeuCauDoiTra
            || ! $yeuCauDoiTra->donHang
            || $yeuCauDoiTra->trang_thai != 'da_duyet'
            || $yeuCauDoiTra->donHang->trang_thai != 'hoan_thanh'
        ) {
            return $this->phanHoiLoi('Yêu cầu chưa sẵn sàng nhận hàng lỗi.');
        }

        try {
            DB::beginTransaction();

            if ($yeuCauDoiTra->chiTiets->isEmpty()) {
                throw new Exception('Yêu cầu không có sản phẩm đổi trả.');
            }

            foreach ($yeuCauDoiTra->chiTiets as $chiTietYeuCau) {
                $chiTietDonHang = $chiTietYeuCau->chiTietDonHang;
                $sanPham = $chiTietDonHang
                    ? $chiTietDonHang->sanPham
                    : null;
                $soLuong = (int) $chiTietYeuCau->so_luong;

                if (! $sanPham || $sanPham->soLuongCoTheBan() < $soLuong) {
                    throw new Exception(
                        'Không đủ tồn kho để chuẩn bị sản phẩm đổi.'
                    );
                }

                $sanPham->truTonKho($soLuong);
                $chiTietYeuCau->da_xuat_hang_doi = true;
                $chiTietYeuCau->save();
            }
            $yeuCauDoiTra->trang_thai = 'dang_xu_ly';
            $yeuCauDoiTra->nhan_hang_luc = now();
            $yeuCauDoiTra->save();

            DB::commit();

            return $this->phanHoiThanhCong('Đã nhận hàng lỗi. Shop đang kiểm tra và chuẩn bị sản phẩm thay thế.');
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->phanHoiLoi($exception->getMessage());
        }
    }

    // Chuyen sang giao san pham thay the cho khach.
    public function giaoHangDoi($maYeuCauDoiTra)
    {
        $yeuCauDoiTra = YeuCauDoiTra::with('donHang')->find($maYeuCauDoiTra);

        if (
            ! $yeuCauDoiTra
            || ! $yeuCauDoiTra->donHang
            || $yeuCauDoiTra->trang_thai != 'dang_xu_ly'
            || $yeuCauDoiTra->donHang->trang_thai != 'hoan_thanh'
        ) {
            return $this->phanHoiLoi('Yêu cầu chưa ở trạng thái đang xử lý đổi trả.');
        }

        $yeuCauDoiTra->trang_thai = 'dang_giao_hang_doi';
        $yeuCauDoiTra->save();

        return $this->phanHoiThanhCong('Đã chuyển sang đang giao hàng đổi.');
    }

    // Hoan tat yeu cau sau khi khach da nhan san pham doi.
    public function hoanTatDoiTra($maYeuCauDoiTra)
    {
        $yeuCauDoiTra = YeuCauDoiTra::with('donHang')->find($maYeuCauDoiTra);

        if (
            ! $yeuCauDoiTra
            || ! $yeuCauDoiTra->donHang
            || $yeuCauDoiTra->trang_thai != 'dang_giao_hang_doi'
            || $yeuCauDoiTra->donHang->trang_thai != 'hoan_thanh'
        ) {
            return $this->phanHoiLoi('Yêu cầu chưa ở trạng thái đang giao hàng đổi.');
        }

        $yeuCauDoiTra->trang_thai = 'hoan_tat';
        $yeuCauDoiTra->save();

        return $this->phanHoiThanhCong('Đã hoàn tất đổi trả.');
    }

    // Tra phan hoi thanh cong cho JavaScript.
    private function phanHoiThanhCong($thongBao)
    {
        return response()->json([
            'trang_thai' => true,
            'thong_bao' => $thongBao,
        ]);
    }

    // Tra phan hoi loi cho JavaScript.
    private function phanHoiLoi($thongBao)
    {
        return response()->json([
            'trang_thai' => false,
            'thong_bao' => $thongBao,
        ], 422);
    }
}
