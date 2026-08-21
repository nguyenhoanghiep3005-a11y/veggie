<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\YeuCauDoiTra;
use App\Services\MinhChungService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DonHangController extends Controller
{
    private $dichVuMinhChung;

    // Khoi tao dich vu luu anh va video minh chung.
    public function __construct(MinhChungService $dichVuMinhChung)
    {
        $this->dichVuMinhChung = $dichVuMinhChung;
    }

    // Hien thi chi tiet mot don hang cua nguoi dung.
    public function hienThiChiTietDonHang($maDonHang)
    {
        $donHang = DonHang::with([
            'chiTietDonHangs.sanPham.hinhAnhs',
            'nguoiDung',
            'diaChiGiaoHang',
            'thanhToan',
            'yeuCauDoiTra.chiTiets',
            'yeuCauDoiTra.minhChungs',
        ])->where('ma_nguoi_dung', Auth::id())
            ->where('ma_don_hang', $maDonHang)
            ->firstOrFail();

        $this->chuanBiChiTietDonHang($donHang);

        $yeuCauDoiTra = $donHang->yeuCauDoiTra;
        $sanPhamDoiTras = $this->laySanPhamDoiTra($donHang, $yeuCauDoiTra);
        $minhChungDoiTras = $this->layMinhChungDoiTra($yeuCauDoiTra);
        $thongTinGiaoHang = $this->layThongTinGiaoHang($donHang);
        $coTheYeuCauDoiTra = $donHang->conHanDoiTra();
        $daNhanHangDoiTra = $yeuCauDoiTra && in_array(
            $yeuCauDoiTra->trang_thai,
            ['dang_xu_ly', 'dang_giao_hang_doi', 'hoan_tat']
        );
        $daHoanTatDoiTra = $yeuCauDoiTra && $yeuCauDoiTra->trang_thai == 'hoan_tat';
        $thoiGianYeuCau = '-';
        if ($yeuCauDoiTra && $yeuCauDoiTra->yeu_cau_luc) {
            $thoiGianYeuCau = $yeuCauDoiTra->yeu_cau_luc->format('d/m/Y H:i');
        }

        return view('clients.pages.chi-tiet-don-hang', compact(
            'donHang',
            'yeuCauDoiTra',
            'sanPhamDoiTras',
            'minhChungDoiTras',
            'thongTinGiaoHang',
            'coTheYeuCauDoiTra',
            'daNhanHangDoiTra',
            'daHoanTatDoiTra',
            'thoiGianYeuCau'
        ));
    }

    // Gui yeu cau doi tra kem so luong va minh chung hang loi.
    public function guiYeuCauDoiTra(Request $request, $maDonHang)
    {
        $donHang = DonHang::with([
            'chiTietDonHangs.sanPham',
            'yeuCauDoiTra.chiTiets',
            'yeuCauDoiTra.minhChungs',
        ])->where('ma_don_hang', $maDonHang)
            ->where('ma_nguoi_dung', Auth::id())
            ->firstOrFail();

        if (
            $donHang->trang_thai != 'hoan_thanh'
            || $donHang->yeuCauDoiTra
            || ! $donHang->conHanDoiTra()
        ) {
            toastr()->error('Đơn hàng không còn đủ điều kiện đổi trả.');

            return back();
        }

        $data = $request->validate([
            'mo_ta' => 'required|string|max:3000',
            'san_pham' => 'required|array',
            'san_pham.*.so_luong' => 'required|integer|min:0',
            'minh_chung' => 'required|array|min:1',
            'minh_chung.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
        ]);

        try {
            $sanPhamsYeuCau = $this->kiemTraSanPhamDoiTra(
                $donHang,
                $data['san_pham']
            );
        } catch (Exception $exception) {
            toastr()->error($exception->getMessage());

            return back()->withInput();
        }

        if (count($sanPhamsYeuCau) == 0) {
            toastr()->error('Vui lòng chọn ít nhất một sản phẩm đổi trả.');

            return back()->withInput();
        }

        $duongDanDaLuu = [];

        try {
            DB::beginTransaction();

            $tepMinhChungs = [];
            foreach ($request->file('minh_chung', []) as $tepMinhChung) {
                if ($tepMinhChung) {
                    $tepMinhChungs[] = $tepMinhChung;
                }
            }

            $minhChungs = $this->dichVuMinhChung->taiNhieuTepLen(
                $tepMinhChungs,
                config('cloudinary.folders.order_returns'),
                $duongDanDaLuu
            );

            $yeuCauDoiTra = YeuCauDoiTra::create([
                'ma_don_hang' => $donHang->ma_don_hang,
                'loai' => 'hang_loi',
                'mo_ta' => trim($data['mo_ta']),
                'trang_thai' => 'cho_duyet',
                'yeu_cau_luc' => now(),
            ]);
            $yeuCauDoiTra->chiTiets()->createMany($sanPhamsYeuCau);
            $yeuCauDoiTra->minhChungs()->createMany($minhChungs);

            DB::commit();
            toastr()->success('Đã gửi yêu cầu đổi trả.');

            return back();
        } catch (Exception $exception) {
            DB::rollBack();

            foreach ($duongDanDaLuu as $duongDan) {
                Storage::disk('public')->delete($duongDan);
            }

            toastr()->error($exception->getMessage());

            return back()->withInput();
        }
    }

    // Huy don dang cho xac nhan va hoan lai ton kho.
    public function huyDonHang(Request $request, $maDonHang)
    {
        $data = $request->validate([
            'ly_do_huy' => 'required|string|max:1000',
        ]);

        $donHang = DonHang::with([
            'chiTietDonHangs.sanPham',
            'thanhToan',
        ])
            ->where('ma_don_hang', $maDonHang)
            ->where('ma_nguoi_dung', Auth::id())
            ->where('trang_thai', 'cho_xac_nhan')
            ->first();

        if (! $donHang) {
            toastr()->error('Không thể hủy đơn ở trạng thái hiện tại.');

            return back();
        }

        try {
            DB::beginTransaction();

            foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
                $sanPham = SanPham::find($chiTietDonHang->ma_san_pham);

                if ($sanPham) {
                    $sanPham->hoanTonKho(
                        $chiTietDonHang->so_luong,
                        $chiTietDonHang->layPhanBoTonKhoDeHoan()
                    );
                }
            }

            $donHang->trang_thai = 'da_huy';
            $donHang->nguoi_huy = 'khach_hang';
            $donHang->ly_do_huy = trim($data['ly_do_huy']);
            $donHang->da_hoan_ton_kho = true;
            $donHang->hoan_ton_kho_luc = now();
            $donHang->save();

            DB::commit();
            toastr()->success('Đã hủy đơn hàng và hoàn lại tồn kho.');
        } catch (Exception $exception) {
            DB::rollBack();
            toastr()->error('Không thể hủy đơn hàng.');
        }

        return back();
    }

    // Kiem tra san pham doi tra co thuoc don va khong vuot so luong da mua.
    private function kiemTraSanPhamDoiTra($donHang, $sanPhamDaChons)
    {
        $sanPhamsYeuCau = [];

        foreach ($sanPhamDaChons as $maChiTietDonHang => $sanPhamDaChon) {
            $soLuong = isset($sanPhamDaChon['so_luong'])
                ? (int) $sanPhamDaChon['so_luong']
                : 0;

            if ($soLuong <= 0) {
                continue;
            }

            $chiTietCanTim = null;

            foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
                if (
                    $chiTietDonHang->ma_chi_tiet_don_hang
                    == $maChiTietDonHang
                ) {
                    $chiTietCanTim = $chiTietDonHang;
                    break;
                }
            }

            if (! $chiTietCanTim) {
                throw new Exception('Sản phẩm đổi trả không thuộc đơn hàng.');
            }

            if ($soLuong > $chiTietCanTim->so_luong) {
                throw new Exception('Số lượng đổi trả vượt quá số lượng đã mua.');
            }

            $sanPhamsYeuCau[] = [
                'ma_chi_tiet_don_hang' => $chiTietCanTim->ma_chi_tiet_don_hang,
                'ma_san_pham' => $chiTietCanTim->ma_san_pham,
                'so_luong' => $soLuong,
            ];
        }

        return $sanPhamsYeuCau;
    }

    // Chuan bi ten, anh va thanh tien cho tung dong don hang.
    private function chuanBiChiTietDonHang($donHang)
    {
        $donHang->ten_phuong_thuc_thanh_toan = 'COD';

        if ($donHang->thanhToan) {
            $donHang->ten_phuong_thuc_thanh_toan =
                $donHang->thanhToan->phuong_thuc == 'paypal' ? 'PayPal' : 'COD';
        }

        foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
            $chiTietDonHang->ten_san_pham = 'Sản phẩm đã xóa';
            $chiTietDonHang->hinh_anh_san_pham = asset(
                'storage/uploads/products/default.png'
            );

            if ($chiTietDonHang->sanPham) {
                $chiTietDonHang->ten_san_pham =
                    $chiTietDonHang->sanPham->ten_hien_thi;
                $chiTietDonHang->hinh_anh_san_pham =
                    $chiTietDonHang->sanPham->duong_dan_hinh_anh;
            }

            $chiTietDonHang->thanh_tien =
                $chiTietDonHang->gia * $chiTietDonHang->so_luong;
        }
    }

    // Lay cac san pham da duoc gui trong yeu cau doi tra.
    private function laySanPhamDoiTra($donHang, $yeuCauDoiTra)
    {
        $ketQuas = [];

        if (! $yeuCauDoiTra || ! is_array($yeuCauDoiTra->san_pham)) {
            return $ketQuas;
        }

        foreach ($yeuCauDoiTra->san_pham as $sanPhamDoiTra) {
            $maChiTiet = null;

            if (isset($sanPhamDoiTra['ma_chi_tiet_don_hang'])) {
                $maChiTiet = $sanPhamDoiTra['ma_chi_tiet_don_hang'];
            }

            $chiTietCanTim = null;

            foreach ($donHang->chiTietDonHangs as $chiTietDonHang) {
                if ($chiTietDonHang->ma_chi_tiet_don_hang == $maChiTiet) {
                    $chiTietCanTim = $chiTietDonHang;
                    break;
                }
            }

            if (! $chiTietCanTim) {
                continue;
            }

            $soLuongDoiTra = 0;
            if (isset($sanPhamDoiTra['so_luong'])) {
                $soLuongDoiTra = $sanPhamDoiTra['so_luong'];
            }

            $daXuatHangDoi = false;
            if (isset($sanPhamDoiTra['phan_bo_hang_doi']) && count($sanPhamDoiTra['phan_bo_hang_doi']) > 0) {
                $daXuatHangDoi = true;
            }

            $ketQuas[] = [
                'ten_san_pham' => $chiTietCanTim->ten_san_pham,
                'so_luong_da_mua' => $chiTietCanTim->so_luong,
                'so_luong_doi_tra' => $soLuongDoiTra,
                'da_xuat_hang_doi' => $daXuatHangDoi,
            ];
        }

        return $ketQuas;
    }

    // Lay duong dan va ten cac tep minh chung doi tra.
    private function layMinhChungDoiTra($yeuCauDoiTra)
    {
        $ketQuas = [];

        if (! $yeuCauDoiTra || ! is_array($yeuCauDoiTra->minh_chung)) {
            return $ketQuas;
        }

        foreach ($yeuCauDoiTra->minh_chung as $minhChung) {
            $tenTep = 'Minh chứng';

            if (isset($minhChung['ten_goc'])) {
                $tenTep = $minhChung['ten_goc'];
            } elseif (isset($minhChung['original_name'])) {
                $tenTep = $minhChung['original_name'];
            }

            $ketQuas[] = [
                'duong_dan' => $this->dichVuMinhChung->layDuongDan($minhChung),
                'ten_tep' => $tenTep,
            ];
        }

        return $ketQuas;
    }

    // Lay dia chi giao hang tu ban chup da luu cung don hang.
    private function layThongTinGiaoHang($donHang)
    {
        $diaChi = $donHang->layDiaChiGiaoHang();

        $thongTinGiaoHang = [
            'ten' => '-',
            'so_dien_thoai' => '-',
            'dia_chi' => '-',
            'tinh_thanh' => '-',
        ];

        if ($diaChi) {
            $thongTinGiaoHang['ten'] = $diaChi->ho_ten;
            $thongTinGiaoHang['so_dien_thoai'] = $diaChi->so_dien_thoai;
            $thongTinGiaoHang['dia_chi'] = $diaChi->dia_chi;
            $thongTinGiaoHang['tinh_thanh'] = $diaChi->tinh_thanh;
        }

        return $thongTinGiaoHang;
    }
}
