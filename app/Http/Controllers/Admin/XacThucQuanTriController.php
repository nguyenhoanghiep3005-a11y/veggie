<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class XacThucQuanTriController extends Controller
{
    // Hien thi form dang nhap quan tri.
    public function hienThiFormDangNhap()
    {
        return view('admin.pages.dang-nhap');
    }

    // Dang nhap va kiem tra vai tro quan tri.
    public function dangNhap(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('admin')->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ])) {
            $nguoiDung = Auth::guard('admin')->user();
            $tenVaiTro = '';

            if ($nguoiDung && $nguoiDung->vaiTro) {
                $tenVaiTro = $nguoiDung->vaiTro->ten;
            }

            if (in_array($tenVaiTro, ['quan_tri', 'staff'])) {
                $request->session()->regenerate();
                toastr()->success('Đăng nhập quản trị thành công.');

                return redirect()->route('admin.tong-quan');
            }

            Auth::guard('admin')->logout();
            toastr()->error('Bạn không có quyền truy cập trang quản trị.');

            return back()->withInput($request->only('email'));
        }

        toastr()->error('Email hoặc mật khẩu không chính xác.');

        return back()->withInput($request->only('email'));
    }

    // Dang xuat va lam moi phien dang nhap.
    public function dangXuat(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.dang-nhap.hien-thi')
            ->with('success', 'Bạn đã đăng xuất thành công.');
    }
}