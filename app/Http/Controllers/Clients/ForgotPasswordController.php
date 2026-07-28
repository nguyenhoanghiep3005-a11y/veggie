<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        // Hiển thị form nhập email để lấy lại mật khẩu.
        return view('clients.pages.forgot-password');
    }

    public function sendResetlink(Request $request)
    {
        // Kiểm tra email có tồn tại trong hệ thống không.
        $request->validate(
            [
                'email' => 'required|email|exists:users,email',
            ],
            [
                'email.required' => 'Email là bắt buộc',
                'email.email' => 'Email không hợp lệ',
                'email.exists' => 'Email không tồn tại trong hệ thống',

            ]
        );

        // Gửi link đặt lại mật khẩu qua email.
        $status = Password::sendResetLink($request->only('email'));
        if ($status === Password::RESET_LINK_SENT) {
            // Gửi mail thành công thì quay lại form và báo thành công.
            toastr()->success('Liên kết đặt lại mật khẩu đã được gửi đến email của bạn');

            return back();
        }

        // Gửi mail thất bại thì trả lỗi về form.
        toastr()->error('Không thể gửi lại email đặt lại mật khẩu');

        return back()->withErrors(['email' => __($status)]);
    }
}
