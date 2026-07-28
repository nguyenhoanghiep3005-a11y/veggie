<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Mail\ActivationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function Flasher\Toastr\Prime\toastr;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        // Hiển thị form đăng ký tài khoản khách hàng.
        return view('clients.pages.register');
    }

    public function register(Request $request)
    {
        // Kiểm tra dữ liệu đăng ký gửi từ form.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'confirmPassword' => 'required|same:password',
            'checkbox1' => 'accepted',
            'checkbox2' => 'accepted',
        ], [
            'name.required' => 'Tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Trường mật khẩu phải có ít nhất 6 ký tự',
            'confirmPassword.required' => 'Vui lòng xác nhận mật khẩu',
            'confirmPassword.same' => 'Mật khẩu xác nhận không khớp',
            'checkbox1.accepted' => 'Bạn phải đồng ý với chính sách xử lý dữ liệu',
            'checkbox2.accepted' => 'Bạn phải đồng ý với chính sách bảo mật',
        ]);

        // Kiểm tra email đã tồn tại trong hệ thống chưa.
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            if ($existingUser->isPending()) {
                // Email đã đăng ký nhưng tài khoản chưa kích hoạt.
                toastr()->error('Email đã được đăng ký và đang đợi kích hoạt');

                return redirect()->route('register')->withInput();
            }

            // Email đã thuộc về một tài khoản đang hoạt động.
            toastr()->error('Email này đã được sử dụng');

            return redirect()->route('register')->withInput();
        }

        // Tạo tài khoản mới ở trạng thái pending và sinh token kích hoạt.
        $token = Str::random(64);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'role_id' => 3,
            'activation_token' => $token,
        ]);

        // Gửi email kích hoạt tài khoản cho khách hàng.
        Mail::to($user->email)->send(new ActivationMail($token, $user));
        toastr()->success('Đăng ký tài khoản thành công, Vui lòng kiểm tra email của bạn để kích hoạt tài khoản.');

        return redirect()->route('login');
    }

    public function activate($token)
    {
        // Tìm tài khoản theo token được gửi trong email kích hoạt.
        $user = User::where('activation_token', $token)->first();
        if ($user) {
            // Token hợp lệ: kích hoạt tài khoản và xóa token.
            $user->status = 'active';
            $user->activation_token = null;
            $user->save();
            toastr()->success('Kích hoạt tài khoản thành công');

            return redirect()->route('login');
        }
        toastr()->error('token không hợp lệ hoặc đã hết hạn.');

        return redirect()->back();
    }

    public function showLoginForm()
    {
        // Hiển thị form đăng nhập khách hàng.
        return view('clients.pages.login');
    }

    public function login(Request $request)
    {
        // Kiểm tra dữ liệu đăng nhập gửi từ form.
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        // Đăng nhập chỉ cho tài khoản đã kích hoạt.
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 'active'])) {
            $user = Auth::user();

            if ($user->role?->name === 'customer') {
                // Khách hàng đăng nhập thành công thì tạo lại session.
                $request->session()->regenerate();
                toastr()->success('Đăng nhập thành công');

                return redirect()->route('home');
            } else {
                // Không cho tài khoản admin/staff đăng nhập ở giao diện khách hàng.
                Auth::logout();
                toastr()->warning('Bạn không có quyền truy cập tài khoản này.');

                return redirect()->back();
            }
        }
        toastr()->error('Thông tin đăng nhập không chính xác hoặc tài khoản chưa kích hoạt.');

        return redirect()->back();
    }

    public function logout()
    {
        // Đăng xuất khách hàng và làm mới session/token bảo mật.
        Auth::logout();
        session()->invalidate(); // hủy session
        session()->regenerateToken(); // tạo CSRF token mới
        toastr()->success('Đăng xuất thành công');

        return redirect()->route('login');
    }
}
