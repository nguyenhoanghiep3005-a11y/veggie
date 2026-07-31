<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Exception;

class QuenMatKhauController extends Controller
{
    // Hien thi form nhap email quen mat khau.
    public function hienThiFormQuenMatKhau()
    {
        return view('clients.pages.quen-mat-khau');
    }

    // Gui lien ket dat lai mat khau den email da dang ky.
    public function guiLienKetDatLaiMatKhau(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:nguoi_dung,email',
        ], [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
        ]);

        try {
            $trangThai = Password::sendResetLink([
                'email' => $data['email'],
            ]);
        } catch (Exception $exception) {
            toastr()->error('Không thể gửi email đặt lại mật khẩu.');

            return back()->withInput();
        }

        if ($trangThai == Password::RESET_LINK_SENT) {
            toastr()->success('Liên kết đặt lại mật khẩu đã được gửi đến email của bạn.');

            return back();
        }

        toastr()->error('Không thể gửi email đặt lại mật khẩu.');

        return back()->withInput();
    }
}