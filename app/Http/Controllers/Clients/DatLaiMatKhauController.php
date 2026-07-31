<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class DatLaiMatKhauController extends Controller
{
    // Hiển thị form đặt lại mật khẩu và gửi mã đặt lại sang View.
    public function hienThiFormDatLaiMatKhau(Request $request, $token)
    {
        $email = $request->query('email');

        return view('clients.pages.dat-lai-mat-khau', compact('token', 'email'));
    }

    // Kiểm tra thông tin và cập nhật mật khẩu mới cho người dùng.
    public function datLaiMatKhau(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nguoi_dung,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required',
        ], [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email này chưa được đăng ký trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'token.required' => 'Mã đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
        ]);

        $nguoiDung = NguoiDung::where('email', $data['email'])->first();

        if ($nguoiDung && Hash::check($data['password'], $nguoiDung->mat_khau)) {
            toastr()->error('Mật khẩu mới không được trùng với mật khẩu hiện tại.');

            return back()->withInput($request->only('email'));
        }

        $trangThai = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($nguoiDung, $matKhau) {
                $nguoiDung->mat_khau = Hash::make($matKhau);
                $nguoiDung->save();
            }
        );

        if ($trangThai == Password::PASSWORD_RESET) {
            toastr()->success('Mật khẩu đã được đặt lại thành công.');

            return redirect()->route('dang-nhap.hien-thi');
        }

        if ($trangThai == Password::INVALID_TOKEN) {
            toastr()->error('Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng gửi lại liên kết mới.');
        } elseif ($trangThai == Password::INVALID_USER) {
            toastr()->error('Email không khớp với liên kết đặt lại mật khẩu.');
        } else {
            toastr()->error('Đặt lại mật khẩu không thành công. Vui lòng thử lại.');
        }

        return back()->withInput($request->only('email'));
    }
}