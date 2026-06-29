@extends('layouts.client')

@section('title','Về Nông Sản Khô')
@section('breadcrumb','Về Nông Sản Khô')

@section('content')
<!-- ABOUT US AREA START -->
        <div class="ltn__about-us-area pt-120--- pb-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 align-self-center">
                        <div class="about-us-img-wrap about-img-left">
                            <img src="{{asset('assets/clients/img/others/6.png')}}" alt="About Us Image">
                        </div>
                    </div>
                    <div class="col-lg-6 align-self-center">
                        <div class="about-us-info-wrap">
                            <div class="section-title-area ltn__section-title-2">
                                <h1 class="section-title">Cửa hàng Nông Sản Khô uy tín</h1>
                                <p>Chúng tôi cam kết mang đến các sản phẩm nông sản khô chất lượng, an toàn và dễ sử dụng.</p>
                            </div>
                            <p>Nông Sản Khô tập trung cung cấp thực phẩm khô, gia vị, gạo và hạt dinh dưỡng được chọn lọc từ nguồn cung uy tín, giúp khách hàng mua sắm thuận tiện và an tâm hơn.</p>
                            <div class="about-author-info d-flex">
                                <div class="author-name-designation  align-self-center">
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ABOUT US AREA END -->

        <!-- FEATURE AREA START ( Feature - 6) -->
        <div class="ltn__feature-area section-bg-1 pt-115 pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-area ltn__section-title-2 text-center">
                            <h1 class="section-title">Tại Sao Chọn Nông Sản Khô<span>.</span></h1>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="ltn__feature-item ltn__feature-item-7">
                            <div class="ltn__feature-icon-title">
                                <div class="ltn__feature-icon">
                                    <span><img src="{{asset('assets/clients/img/icons/icon-img/21.png')}}" alt="#"></span>
                                </div>
                                <h3><a href="service-details.html">Danh Mục Đa Dạng</a></h3>
                            </div>
                            <div class="ltn__feature-info">
                                <p>Cung cấp nhiều nhóm sản phẩm như thực phẩm khô, gia vị, gạo và hạt dinh dưỡng.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="ltn__feature-item ltn__feature-item-7">
                            <div class="ltn__feature-icon-title">
                                <div class="ltn__feature-icon">
                                    <span><img src="{{asset('assets/clients/img/icons/icon-img/22.png')}}" alt="#"></span>
                                </div>
                                <h3><a href="service-details.html">Sản Phẩm Chọn Lọc</a></h3>
                            </div>
                            <div class="ltn__feature-info">
                                <p>Mỗi sản phẩm được chọn lọc kỹ, đóng gói cẩn thận và phù hợp với nhu cầu sử dụng hằng ngày.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="ltn__feature-item ltn__feature-item-7">
                            <div class="ltn__feature-icon-title">
                                <div class="ltn__feature-icon">
                                    <span><img src="{{asset('assets/clients/img/icons/icon-img/23.png')}}" alt="#"></span>
                                </div>
                                <h3><a href="service-details.html">Bảo Quản An Toàn</a></h3>
                            </div>
                            <div class="ltn__feature-info">
                                <p>Cam kết sản phẩm rõ nguồn gốc, bảo quản đúng cách và thông tin minh bạch.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FEATURE AREA END -->
@endsection

