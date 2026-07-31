@extends('layouts.client')

@section('title','Liên hệ')
@section('breadcrumb','Liên hệ')

@section('content')
<div class="ltn__contact-address-area mb-90">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                    <div class="ltn__contact-address-icon">
                        <img src="{{ asset('assets/clients/img/icons/10.png') }}" alt="Email">
                    </div>
                    <h3>Email</h3>
                    <p>nguyenhoanghiep3005@gmail.com</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                    <div class="ltn__contact-address-icon">
                        <img src="{{ asset('assets/clients/img/icons/11.png') }}" alt="Điện thoại">
                    </div>
                    <h3>Điện thoại</h3>
                    <p>0388536385</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ltn__contact-address-item ltn__contact-address-item-3 box-shadow">
                    <div class="ltn__contact-address-icon">
                        <img src="{{ asset('assets/clients/img/icons/12.png') }}" alt="Địa chỉ">
                    </div>
                    <h3>Địa chỉ</h3>
                    <p>Tân Phú, Hồ Chí Minh, Việt Nam</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ltn__contact-message-area mb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="ltn__form-box contact-form-box box-shadow white-bg text-center">
                    <h4 class="title-2">Tư vấn mua hàng</h4>
                    <p>
                        Chức năng lưu liên hệ đã được bỏ khỏi hệ thống. Nếu khách cần tư vấn,
                        vui lòng gọi trực tiếp hoặc gửi email cho cửa hàng theo thông tin bên trên.
                    </p>
                    <div class="btn-wrapper mt-3">
                        <a class="btn theme-btn-1 btn-effect-1 text-uppercase" href="tel:0388536385">Gọi tư vấn</a>
                        <a class="btn btn-effect-1 text-uppercase" href="mailto:nguyenhoanghiep3005@gmail.com">Gửi email</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="google-map mb-120">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.3964851520236!2d106.63804877371079!3d10.780914589368098!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ea55213f095%3A0x65d98568c398fc18!2zNDU3IMSQLiBLw6puaCBUw6JuIEjDs2EsIFTDom4gUGjDuiwgSOG7kyBDaMOtIE1pbmggNzAwMDAwLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1785489097323!5m2!1svi!2s"width="100%" height="100%" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
</div>
@endsection
