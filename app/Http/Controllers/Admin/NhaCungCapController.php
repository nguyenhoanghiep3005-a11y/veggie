<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhaCungCap;
use Illuminate\Http\Request;

class NhaCungCapController extends Controller
{
    // Hien thi danh sach nha cung cap.
    public function hienThiDanhSachNhaCungCap()
    {
        $nhaCungCaps = NhaCungCap::orderBy('ten')->paginate(10);

        foreach ($nhaCungCaps as $nhaCungCap) {
            $nhaCungCap->so_dien_thoai_hien_thi = '-';
            $nhaCungCap->mo_ta_hien_thi = '-';

            if ($nhaCungCap->so_dien_thoai) {
                $nhaCungCap->so_dien_thoai_hien_thi = $nhaCungCap->so_dien_thoai;
            }

            if ($nhaCungCap->mo_ta) {
                $nhaCungCap->mo_ta_hien_thi = $nhaCungCap->mo_ta;
            }
        }

        return view('admin.pages.nha-cung-cap', compact('nhaCungCaps'));
    }

    // Them nha cung cap moi.
    public function themNhaCungCap(Request $request)
    {
        $data = $this->kiemTraDuLieu($request);
        NhaCungCap::create($data);

        return back()->with('success', 'Thêm nhà cung cấp thành công.');
    }

    // Cap nhat thong tin nha cung cap.
    public function capNhatNhaCungCap(Request $request, NhaCungCap $nhaCungCap)
    {
        $data = $this->kiemTraDuLieu($request);
        $nhaCungCap->update($data);

        return back()->with('success', 'Cập nhật nhà cung cấp thành công.');
    }

    // Xoa nha cung cap neu chua phat sinh chung tu.
    public function xoaNhaCungCap(NhaCungCap $nhaCungCap)
    {
        if ($nhaCungCap->donDatNhaps()->exists()
            || $nhaCungCap->phieuNhaps()->exists()
            || $nhaCungCap->phieuHangHus()->exists()
            || $nhaCungCap->loHangKhos()->exists()) {
            return back()->with('error', 'Không thể xóa nhà cung cấp đã có chứng từ nhập hàng.');
        }

        $nhaCungCap->delete();

        return back()->with('success', 'Xóa nhà cung cấp thành công.');
    }

    // Kiem tra du lieu nha cung cap truoc khi luu.
    private function kiemTraDuLieu($request)
    {
        return $request->validate([
            'ten' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:50',
            'mo_ta' => 'nullable|string',
        ]);
    }
}