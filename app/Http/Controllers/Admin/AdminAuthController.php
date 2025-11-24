<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Flasher\Toastr\Prime\toastr;

class AdminAuthController extends Controller
{
    public function showLoginForm(Request $request)
    {

        return view('admin.pages.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::guard('admin')->user();
            if (in_array($user->role->name, ['admin', 'staff'])) {
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
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success','Bạn đã đăng xuất thành công.');
    }
}
