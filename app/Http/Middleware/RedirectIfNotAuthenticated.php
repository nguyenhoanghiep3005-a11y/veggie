<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAuthenticated
{
    // Kiem tra dang nhap truoc khi cho truy cap trang duoc bao ve.
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            if (! Auth::guard('admin')->check()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'thong_bao' => 'Vui lòng đăng nhập quản trị để tiếp tục.',
                    ], 401);
                }

                toastr()->error('Vui lòng đăng nhập để vào trang quản trị.');

                return redirect()->route('admin.dang-nhap.hien-thi');
            }
        } elseif (! Auth::guard('web')->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'thong_bao' => 'Vui lòng đăng nhập để thực hiện chức năng này.',
                ], 401);
            }

            toastr()->error('Vui lòng đăng nhập để thực hiện chức năng này.');

            return redirect()->route('dang-nhap.hien-thi');
        }

        return $next($request);
    }
}