<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Flasher\Toastr\Prime\toastr;

class AdminAuthController extends Controller
{
    // Hiển thị form đăng nhập admin.
    public function showLoginForm(Request $request)
    {
        return view('admin.pages.login');
    }

    // Xử lý đăng nhập admin và kiểm tra quyền admin/staff.
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::guard('admin')->user();
            $roleName = '';

            if ($user && $user->role) {
                $roleName = $user->role->name;
            }

            if (in_array($roleName, ['admin', 'staff'], true)) {
                $request->session()->regenerate();
                toastr()->success('Đăng nhập admin thành công');

                return redirect()->route('admin.dashboard');
            }

            Auth::guard('admin')->logout();
            toastr()->error('Bạn không có quyền truy cập admin');
        }

        toastr()->error('Email hoặc mật khẩu không chính xác');

        return back();
    }

    // Đăng xuất admin và làm mới session.
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
