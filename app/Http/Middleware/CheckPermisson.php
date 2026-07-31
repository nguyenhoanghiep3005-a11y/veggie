<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermisson
{
    // Kiểm tra admin đã đăng nhập và có quyền truy cập chức năng.
    public function handle(Request $request, Closure $next, $tenQuyen)
    {
        $nguoiDung = Auth::guard('admin')->user();

        if (! $nguoiDung) {
            return redirect()->route('admin.dang-nhap.hien-thi');
        }

        $vaiTro = $nguoiDung->vaiTro;

        if (! $vaiTro || ! $vaiTro->cacQuyen->contains('ten', $tenQuyen)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}