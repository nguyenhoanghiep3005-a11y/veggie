<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Mail\KichHoatTaiKhoanMail;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;

class XacThucController extends Controller
{
    // Hien thi form dang ky tai khoan.
    public function hienThiFormDangKy()
    {
        return view('clients.pages.dang-ky');
    }

    // Dang ky tai khoan khach hang va gui email kich hoat.
    public function dangKy(Request $request)
    {
        $data = $request->validate([
            'ten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'confirmPassword' => 'required|same:password',
            'checkbox1' => 'accepted',
            'checkbox2' => 'accepted',  
        ], [
            'ten.required' => 'Tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'confirmPassword.required' => 'Vui lòng xác nhận mật khẩu.',
            'confirmPassword.same' => 'Mật khẩu xác nhận không khớp.',
            'checkbox1.accepted' => 'Bạn phải đồng ý với chính sách xử lý dữ liệu.',
            'checkbox2.accepted' => 'Bạn phải đồng ý với chính sách bảo mật.',
        ]);

        $nguoiDungDaCo = NguoiDung::where('email', $data['email'])->first();

        if ($nguoiDungDaCo) {
            if ($nguoiDungDaCo->dangChoKichHoat()) {
                toastr()->error('Email đã được đăng ký và đang chờ kích hoạt.');
            } else {
                toastr()->error('Email này đã được sử dụng.');
            }

            return redirect()->route('dang-ky.hien-thi')->withInput();
        }

        $maKichHoat = Str::random(64);
        $nguoiDung = NguoiDung::create([
            'ten' => $data['ten'],
            'email' => $data['email'],
            'mat_khau' => Hash::make($data['password']),
            'trang_thai' => 'cho_kich_hoat',
            'ma_vai_tro' => 3,
            'ma_kich_hoat' => $maKichHoat,
        ]);

        $this->guiEmailKichHoat($nguoiDung, $maKichHoat);

        toastr()->success('Đăng ký thành công. Vui lòng kiểm tra email để kích hoạt tài khoản.');

        return redirect()->route('dang-nhap.hien-thi');
    }

    // Gui email kich hoat sau khi trinh duyet da nhan phan hoi.
    private function guiEmailKichHoat($nguoiDung, $maKichHoat)
    {
        app()->terminating(function () use ($nguoiDung, $maKichHoat) {
            try {
                Mail::to($nguoiDung->email)
                    ->send(new KichHoatTaiKhoanMail($maKichHoat, $nguoiDung));
            } catch (Exception $exception) {
                Log::warning('Khong gui duoc email kich hoat: '.$exception->getMessage());
            }
        });
    }
    // Kich hoat tai khoan bang ma duoc gui qua email.
    public function kichHoatTaiKhoan($maKichHoat)
    {
        $nguoiDung = NguoiDung::where('ma_kich_hoat', $maKichHoat)->first();

        if (! $nguoiDung) {
            toastr()->error('Mã kích hoạt không hợp lệ hoặc đã được sử dụng.');

            return redirect()->route('dang-nhap.hien-thi');
        }

        $nguoiDung->trang_thai = 'hoat_dong';
        $nguoiDung->ma_kich_hoat = null;
        $nguoiDung->save();

        toastr()->success('Kích hoạt tài khoản thành công.');

        return redirect()->route('dang-nhap.hien-thi');
    }

    // Hien thi form dang nhap tai khoan.
    public function hienThiFormDangNhap()
    {
        return view('clients.pages.dang-nhap');
    }

    // Dang nhap tai khoan khach hang dang hoat dong.
    public function dangNhap(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $dangNhapThanhCong = Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
            'trang_thai' => 'hoat_dong',
        ]);

        if (! $dangNhapThanhCong) {
            toastr()->error('Thông tin đăng nhập không chính xác hoặc tài khoản chưa kích hoạt.');

            return back()->withInput($request->only('email'));
        }

        $nguoiDung = Auth::user();

        if (! $nguoiDung->vaiTro || $nguoiDung->vaiTro->ten != 'khach_hang') {
            Auth::logout();
            toastr()->warning('Bạn không có quyền đăng nhập ở trang khách hàng.');

            return back()->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        toastr()->success('Đăng nhập thành công.');

        return redirect()->route('trang-chu');
    }

    // Dang xuat va lam moi phien dang nhap.
    public function dangXuat(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        toastr()->success('Đăng xuất thành công.');

        return redirect()->route('dang-nhap.hien-thi');
    }
}