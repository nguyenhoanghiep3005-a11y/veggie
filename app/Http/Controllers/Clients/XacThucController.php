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
        $this->khoiPhucGioHangTaiKhoan($request, $nguoiDung->ma_nguoi_dung);
        toastr()->success('Đăng nhập thành công.');

        return redirect()->route('trang-chu');
    }

    // Dang xuat va lam moi phien dang nhap.
    public function dangXuat(Request $request)
    {
        $gioHang = $request->session()->get('gio_hang', []);
        $maNguoiDung = Auth::id();
        $cacGioHangTaiKhoan = $request->session()->get('gio_hang_tai_khoan', []);

        if ($maNguoiDung) {
            $cacGioHangTaiKhoan[(string) $maNguoiDung] = [
                'gio_hang' => $gioHang,
                'so_luong_gio_hang' => $this->tinhTongSoLuongGioHang($gioHang),
            ];
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->put('gio_hang_tai_khoan', $cacGioHangTaiKhoan);

        toastr()->success('Đăng xuất thành công.');

        return redirect()->route('dang-nhap.hien-thi');
    }

    // Khoi phuc dung gio hang cua tai khoan vua dang nhap.
    private function khoiPhucGioHangTaiKhoan(Request $request, $maNguoiDung)
    {
        $gioHangKhach = (array) $request->session()->get('gio_hang', []);
        $cacGioHangTaiKhoan = (array) $request->session()->get('gio_hang_tai_khoan', []);
        $khoaTaiKhoan = (string) $maNguoiDung;
        $gioHangTaiKhoan = [];

        if (isset($cacGioHangTaiKhoan[$khoaTaiKhoan]['gio_hang'])) {
            $gioHangTaiKhoan = (array) $cacGioHangTaiKhoan[$khoaTaiKhoan]['gio_hang'];
        }

        foreach ($gioHangKhach as $maSanPham => $dongGioHang) {
            if (! isset($gioHangTaiKhoan[$maSanPham])) {
                $gioHangTaiKhoan[$maSanPham] = $dongGioHang;
            }
        }

        $soLuongGioHang = $this->tinhTongSoLuongGioHang($gioHangTaiKhoan);
        $cacGioHangTaiKhoan[$khoaTaiKhoan] = [
            'gio_hang' => $gioHangTaiKhoan,
            'so_luong_gio_hang' => $soLuongGioHang,
        ];

        $request->session()->put('gio_hang_tai_khoan', $cacGioHangTaiKhoan);
        $request->session()->put('gio_hang', $gioHangTaiKhoan);
        $request->session()->put('so_luong_gio_hang', $soLuongGioHang);
    }

    // Tinh lai tong so luong de khong phu thuoc du lieu dem cu trong session.
    private function tinhTongSoLuongGioHang($gioHang)
    {
        $tongSoLuong = 0;

        foreach ((array) $gioHang as $dongGioHang) {
            if (is_array($dongGioHang) && isset($dongGioHang['so_luong'])) {
                $tongSoLuong += max(0, (int) $dongGioHang['so_luong']);
            }
        }
        return $tongSoLuong;
    }
}
