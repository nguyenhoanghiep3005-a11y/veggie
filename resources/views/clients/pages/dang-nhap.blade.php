@extends('layouts.client')

@section('title','Đăng nhập')
@section('breadcrumb','Đăng nhập')
@section('content')
<div class="ltn__login-area pb-65">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area text-center">
                    <h1 class="section-title">Đăng Nhập <br>Vào Tài Khoản Của Bạn</h1>
                    <p>Chào mừng bạn quay lại với chúng tôi. <br>
                        Vui lòng đăng nhập để tiếp tục mua sắm và quản lý đơn hàng của bạn.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="account-login-inner">
                        <form action="{{ route('dang-nhap.xu-ly') }}" class="ltn__form-box contact-form-box" method="POST" id="login-form">
                            @csrf
                            <input type="email" name="email" placeholder="Email*" value="{{ old('email') }}" required>
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <input type="password" name="password" placeholder="Mật khẩu*" required>
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <div class="btn-wrapper mt-0">
                                <button class="theme-btn-1 btn btn-block" type="submit">ĐĂNG NHẬP</button>
                            </div>
                            <div class="go-to-btn mt-20">
                                <a href="{{route('quen-mat-khau.hien-thi')}}"><small>QUÊN MẬT KHẨU?</small></a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="account-create text-center pt-50">
                        <h4>BẠN CHƯA CÓ TÀI KHOẢN?</h4>
                        <p>Thêm sản phẩm vào danh sách yêu thích, nhận gợi ý cá nhân hóa, <br>
                            thanh toán nhanh hơn và theo dõi đơn hàng dễ dàng hơn bằng cách đăng ký ngay hôm nay.</p>
                            <div class="btn-wrapper">
                                <a href="{{route('dang-ky.hien-thi')}}" class="theme-btn-1 btn black-btn">TẠO TÀI KHOẢN</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
