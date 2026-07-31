@extends('layouts.client')

@section('title','Đăng ký')
@section('breadcrumb','Đăng ký')
@section('content')
<div class="ltn__login-area pb-110">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area text-center">
                    <h1 class="section-title">Đăng Ký <br>Tài Khoản Của Bạn</h1>
                    <p>Hãy tạo tài khoản để bắt đầu trải nghiệm mua sắm tiện lợi cùng chúng tôi. <br>
                        Mọi thông tin của bạn sẽ được bảo mật tuyệt đối.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="account-login-inner">
                    <form action="{{ route('dang-ky.xu-ly') }}" class="ltn__form-box contact-form-box" method="POST" id="register-form">
                        @csrf
                        <input type="text" name="ten" placeholder="Họ và Tên" value="{{ old('ten') }}" required>
                        @error('ten')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <input type="email" name="email" placeholder="Email*" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <input type="password" name="password" placeholder="Mật khẩu*" required>
                        @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <input type="password" name="confirmPassword" placeholder="Xác nhận mật khẩu*" required>
                        @error('confirmPassword')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <label class="checkbox-inline">
                            <input type="checkbox" name="checkbox1" value="1" {{ old('checkbox1') ? 'checked' : '' }} required>
                            Tôi đồng ý cho phép hệ thống xử lý dữ liệu cá nhân của tôi để gửi thông tin khuyến mãi
                            theo chính sách bảo mật.
                        </label>
                        @error('checkbox1')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <label class="checkbox-inline">
                            <input type="checkbox" name="checkbox2" value="1" {{ old('checkbox2') ? 'checked' : '' }} required>
                            Bằng cách nhấn “Tạo tài khoản”, tôi đồng ý với chính sách bảo mật.
                        </label>
                        @error('checkbox2')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="btn-wrapper">
                            <button class="theme-btn-1 btn reverse-color btn-block" type="submit">TẠO TÀI KHOẢN</button>
                        </div>
                    </form>
                    <div class="by-agree text-center">
                        <p>Khi tạo tài khoản, bạn đồng ý với</p>
                        <p><a href="#">ĐIỀU KHOẢN SỬ DỤNG &nbsp; &nbsp; | &nbsp; &nbsp; CHÍNH SÁCH BẢO MẬT</a></p>
                        <div class="go-to-btn mt-50">
                            <a href="{{route('dang-nhap.hien-thi')}}">ĐÃ CÓ TÀI KHOẢN ? ĐĂNG NHẬP NGAY</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        
@endsection
