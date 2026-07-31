<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Http\Request;

class TimKiemController extends Controller
{
    // Tim san pham theo ten va hien thi ket qua co phan trang.
    public function timKiem(Request $request)
    {
        $tuKhoa = trim((string) $request->input('keyword'));

        if ($tuKhoa == '') {
            toastr()->error('Vui lòng nhập từ khóa tìm kiếm.');

            return back();
        }

        $danhMucs = DanhMuc::with('sanPhams')->get();
        $maDanhMucDaChon = 0;

        $sanPhams = SanPham::with([
            'hinhAnhDauTien',
            'hinhAnhs',
            'danhGias',
            'chiTietDonHangs.donHang',
        ])->where('trang_thai', 'con_hang')
            ->where('ton_kho', '>', 0)
            ->where('ten', 'like', '%'.$tuKhoa.'%')
            ->orderBy('ma_san_pham', 'desc')
            ->paginate(9)
            ->appends(['keyword' => $tuKhoa]);

        foreach ($sanPhams as $sanPham) {
            $soSaoTrungBinh = $sanPham->danhGias->avg('so_sao');
            $sanPham->so_sao_trung_binh = $soSaoTrungBinh
                ? round($soSaoTrungBinh, 1)
                : 0;
            $sanPham->tong_danh_gia = $sanPham->danhGias->count();
            $sanPham->so_luong_da_ban = $sanPham->soLuongDaBan();
        }

        return view(
            'clients.pages.ket-qua-tim-kiem',
            compact('danhMucs', 'maDanhMucDaChon', 'sanPhams', 'tuKhoa')
        );
    }
}
