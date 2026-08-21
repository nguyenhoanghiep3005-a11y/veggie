@extends('layouts.client_home')

@section('title','Trang Chủ')

@section('content')
<style>
    /* CSS rieng trang chu: sua khoang cach cac section va slider san pham tai day. */
    .home-promotion-section {
    padding-top: 45px !important;
    padding-bottom: 10px !important;
    }

    .home-best-seller-section {
    padding-top: 20px !important;
    padding-bottom: 25px !important;
    }

    .home-best-seller-section .section-title-area {
    margin-bottom: 15px;
    }

    .home-best-seller-section .ltn__tab-menu {
    margin-bottom: 20px;
    }

    .home-best-seller-section .ltn__tab-menu .nav {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0 28px;
    }

    .home-best-seller-section .ltn__tab-menu-2 a {
    padding: 12px 16px;
    margin-right: 0;
    }

    .home-best-seller-section .ltn__tab-menu-2 a::before {
    right: -14px;
    }

    .home-category-section {
    padding-top: 35px !important;
    padding-bottom: 45px !important;
    }

    .home-category-section .section-title-area {
    margin-bottom: 25px;
    }

    .home-product-slider.has-many-products.slick-arrow-1 .slick-arrow {
    opacity: 1;
    visibility: visible;
    }

    .home-product-slider.has-many-products.slick-arrow-1 .slick-prev {
    left: -45px;
    }

    .home-product-slider.has-many-products.slick-arrow-1 .slick-next {
    right: -45px;
    }

    .home-product-slider .ltn__product-item {
    margin-bottom: 18px !important;
    }

    .home-product-slider .slick-slide {
    height: auto !important;
    padding-bottom: 0 !important;
    }

    .home-product-slider .slick-track {
    display: flex !important;
    align-items: flex-start !important;
    }

    .home-product-slider .slick-list {
    margin-bottom: 0 !important;
    }

    .home-product-slider .product-info {
    padding-bottom: 10px !important;
    }

    .home-product-slider .product-title {
    margin-bottom: 8px !important;
    }

    .home-product-slider .product-ratting {
    margin-bottom: 6px;
    }

    .home-promotion-section + .home-best-seller-section {
    margin-top: 18px !important;
    }

    .home-best-seller-section .section-title-area {
    margin-top: 0 !important;
    margin-bottom: 18px !important;
    }

</style>

<!-- SLIDER AREA START (slider-3) -->
<div class="ltn__slider-area ltn__slider-3 section-bg-1">
    <div class="ltn__slide-one-active slick-slide-arrow-1 slick-slide-dots-1">
        <!-- Slide 1 -->
        <div class="ltn__slide-item ltn__slide-item-2 ltn__slide-item-3 ltn__slide-item-3-normal bg-image"
            data-bg="{{ asset('assets/clients/img/slider/13.jpg') }}">
            <div class="ltn__slide-item-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 align-self-center">
                            <div class="slide-item-info">
                                <div class="slide-item-info-inner ltn__slide-animation">
                                    <h6 class="slide-sub-title animated">
                                        <img src="{{ asset('assets/clients/img/icons/icon-img/1.png')}}" alt="#">
                                        100% Nông Sản Khô Chọn Lọc
                                    </h6>
                                    <h1 class="slide-title animated">Khám phá <br> hương vị mộc mạc <br> từ Nông Sản Khô</h1>
                                    <div class="slide-brief animated">
                                        <p>Cam kết cung cấp nông sản khô chất lượng, được chọn lọc kỹ
                                            từ nguồn cung uy tín tại Việt Nam.</p>
                                        </div>
                                        <div class="btn-wrapper animated">
                                            <a href="{{route('san-pham.danh-sach')}}"
                                                class="theme-btn-1 btn btn-effect-1 text-uppercase">
                                                Mua Ngay
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="ltn__slide-item ltn__slide-item-2 ltn__slide-item-3 ltn__slide-item-3-normal bg-image"
                data-bg="{{ asset('assets/clients/img/slider/14.jpg') }}">
                <div class="ltn__slide-item-inner text-right text-end">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 align-self-center">
                                <div class="slide-item-info">
                                    <div class="slide-item-info-inner ltn__slide-animation">
                                        <h1 class="slide-title animated">Nông Sản Khô Chất Lượng <br> Cho Bữa Ăn Gia Đình</h1>
                                        <div class="slide-brief animated">
                                            <p>Tuyển chọn các loại thực phẩm khô, gia vị, gạo và hạt dinh dưỡng,
                                                đảm bảo an toàn, tiện lợi và dễ bảo quản.</p>
                                            </div>
                                            <div class="btn-wrapper animated">
                                                <a href="{{route('lien-he')}}"
                                                    class="theme-btn-1 btn btn-effect-1 text-uppercase">
                                                    Khám Phá Ngay
                                                </a>
                                                <a href="{{route('lien-he')}}" class="btn btn-transparent btn-effect-3">
                                                    Tìm Hiểu Thêm
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- SLIDER AREA END -->

        @auth
            @if(count($sanPhamCaNhans) > 0)
                <!-- PERSONAL PRODUCT AREA START -->
                <div class="ltn__product-slider-area ltn__product-gutter pt-115 pb-70 home-personal-section">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="section-title-area ltn__section-title-2 text-center">
                                    <h1 class="section-title">Sản Phẩm Cá Nhân</h1>
                                </div>
                            </div>
                        </div>
                        <div class="row ltn__tab-product-slider-one-active slick-arrow-1 home-product-slider">
                            @foreach ($sanPhamCaNhans as $sanPham)
                                <div class="col-lg-12">
                                    <div class="ltn__product-item ltn__product-item-3 text-center">
                                        <div class="product-img">
                                            <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">
                                                <img src="{{ $sanPham->duong_dan_hinh_anh }}" alt="{{ $sanPham->ten_hien_thi }}">
                                            </a>
                                            @if($sanPham->phan_tram_giam > 0)
                                                <div class="product-badge">
                                                    <ul>
                                                        <li>-{{ $sanPham->phan_tram_giam }}%</li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <div class="product-ratting">
                                                <ul>
                                                    @for ($soSao = 1; $soSao <= 5; $soSao++)
                                                        <li>
                                                            <a href="javascript:void(0)">
                                                                <i class="{{ $soSao <= $sanPham->so_sao_trung_binh ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                            </a>
                                                        </li>
                                                    @endfor
                                                    <li class="review-total">({{ $sanPham->tong_danh_gia }} Đánh giá)</li>
                                                </ul>
                                            </div>
                                            <h2 class="product-title">
                                                <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">{{ $sanPham->ten_hien_thi }}</a>
                                            </h2>
                                            <div class="product-card-bottom" style="display:flex !important; align-items:flex-end !important; justify-content:space-between !important; gap:10px !important; margin-top:10px !important; text-align:left !important;">
                                                <div class="product-card-price" style="display:flex !important; flex-direction:column !important; align-items:flex-start !important; line-height:1.3 !important;">
                                                    @if($sanPham->gia_hien_tai < $sanPham->gia)
                                                        <del style="color:#999 !important; font-size:16px !important; font-weight:200 !important;">
                                                            {{ number_format($sanPham->gia, 0, ',', '.') }}<small class="product-price-symbol" style="margin-left:2px !important; font-size:55% !important; font-weight:400 !important; vertical-align:baseline !important; text-decoration:underline !important;">&#273;</small></del>
                                                    @endif
                                                    <span style="color:#80B500 !important; font-size:20px !important; font-weight:600 !important;">
                                                        {{ number_format($sanPham->gia_hien_tai, 0, ',', '.') }}<small class="product-price-symbol" style="margin-left:2px !important; font-size:55% !important; font-weight:400 !important; vertical-align:baseline !important; text-decoration:underline !important;">&#273;</small></span>
                                                </div>
                                                <div class="product-card-sold" style="color:#111 !important; font-size:14px !important; white-space:nowrap !important;">
                                                    {{ $sanPham->so_luong_da_ban }} đã bán
                                                </div>
                                            </div>
                                            <div class="product-card-actions" style="display:flex !important; align-items:center !important; justify-content:center !important; gap:10px !important; margin-top:14px !important; width:100% !important;">
                                                @auth
                                                    <a href="javascript:void(0)" class="product-wishlist-btn add-to-wishlist" data-id="{{ $sanPham->ma_san_pham }}" title="Yêu thích" style="width:34px !important; height:42px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; color:#80B500 !important; font-size:24px !important; line-height:1 !important;">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                @endauth
                                                <a href="javascript:void(0)" class="product-buy-now-btn add-to-cart-btn buy-now-btn" 
                                                data-id="{{ $sanPham->ma_san_pham }}"
                                                     style="height:42px !important; flex:1 1 auto !important; max-width:178px !important; min-width:0 !important; padding:0 16px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; background:#80B500 !important; border:1px solid #80B500 !important; color:#fff !important; font-size:15px !important; font-weight:600 !important; line-height:1 !important;">Mua ngay</a>
                                                <a href="javascript:void(0)" class="product-cart-btn add-to-cart-btn"
                                                 data-id="{{ $sanPham->ma_san_pham }}" title="Thêm vào giỏ hàng" 
                                                    style="width:50px !important; height:42px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; background:#80B500 !important; border:1px solid #80B500 !important; color:#fff !important; font-size:20px !important; line-height:1 !important;">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- PERSONAL PRODUCT AREA END -->
            @endif
        @endauth
        @if(count($sanPhamKhuyenMais) > 0)
            <!-- PROMOTION PRODUCT AREA START -->
            <div class="ltn__product-slider-area ltn__product-gutter pt-115 pb-70 home-promotion-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-title-area ltn__section-title-2 text-center">
                                <h1 class="section-title">Sản Phẩm Khuyến Mãi</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row ltn__tab-product-slider-one-active slick-arrow-1 home-product-slider">
                        @foreach ($sanPhamKhuyenMais as $sanPham)
                            <div class="col-lg-12">
                                <div class="ltn__product-item ltn__product-item-3 text-center">
                                    <div class="product-img">
                                        <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">
                                            <img src="{{ $sanPham->duong_dan_hinh_anh }}" alt="{{ $sanPham->ten_hien_thi }}">
                                        </a>
                                        @if($sanPham->phan_tram_giam > 0)
                                            <div class="product-badge">
                                                <ul>
                                                    <li>-{{ $sanPham->phan_tram_giam }}%</li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="product-info">
                                        <div class="product-ratting">
                                            <ul>
                                                @for ($soSao = 1; $soSao <= 5; $soSao++)
                                                    <li>
                                                        <a href="javascript:void(0)">
                                                            <i class="{{ $soSao <= $sanPham->so_sao_trung_binh ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                        </a>
                                                    </li>
                                                @endfor
                                                <li class="review-total">({{ $sanPham->tong_danh_gia }} Đánh giá)</li>
                                            </ul>
                                        </div>
                                        <h2 class="product-title">
                                            <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">{{ $sanPham->ten_hien_thi }}</a>
                                        </h2>
                                        <div class="product-card-bottom" style="display:flex !important; align-items:flex-end !important; justify-content:space-between !important; gap:10px !important; margin-top:10px !important; text-align:left !important;">
                                            <div class="product-card-price" style="display:flex !important; flex-direction:column !important; align-items:flex-start !important; line-height:1.3 !important;">
                                                @if($sanPham->gia_hien_tai < $sanPham->gia)
                                                    <del style="color:#999 !important; font-size:16px !important; font-weight:200 !important;">
                                                        {{ number_format($sanPham->gia, 0, ',', '.') }}<small class="product-price-symbol" style="margin-left:2px !important; font-size:55% !important; font-weight:400 !important; vertical-align:baseline !important; text-decoration:underline !important;">&#273;</small></del>
                                                @endif
                                                <span style="color:#80B500 !important; font-size:20px !important; font-weight:600 !important;">
                                                    {{ number_format($sanPham->gia_hien_tai, 0, ',', '.') }}<small class="product-price-symbol" style="margin-left:2px !important; font-size:55% !important; font-weight:400 !important; vertical-align:baseline !important; text-decoration:underline !important;">&#273;</small></span>
                                            </div>
                                            <div class="product-card-sold" style="color:#111 !important; font-size:14px !important; white-space:nowrap !important;">
                                                {{ $sanPham->so_luong_da_ban }} đã bán
                                            </div>
                                        </div>
                                        <div class="product-card-actions" style="display:flex !important; align-items:center !important; justify-content:center !important; gap:10px !important; margin-top:14px !important; width:100% !important;">
                                            @auth
                                                <a href="javascript:void(0)" class="product-wishlist-btn add-to-wishlist" data-id="{{ $sanPham->ma_san_pham }}" title="Yêu thích" style="width:34px !important; height:42px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; color:#80B500 !important; font-size:24px !important; line-height:1 !important;">
                                                    <i class="far fa-heart"></i>
                                                </a>
                                            @endauth
                                            <a href="javascript:void(0)" class="product-buy-now-btn add-to-cart-btn buy-now-btn" data-id="{{ $sanPham->ma_san_pham }}" 
                                                style="height:42px !important; flex:1 1 auto !important; max-width:178px !important; min-width:0 !important; padding:0 16px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; background:#80B500 !important; border:1px solid #80B500 !important; color:#fff !important; font-size:15px !important; font-weight:600 !important; line-height:1 !important;">Mua ngay</a>
                                            <a href="javascript:void(0)" class="product-cart-btn add-to-cart-btn" data-id="{{ $sanPham->ma_san_pham }}" title="Thêm vào giỏ hàng"
                                                 style="width:50px !important; height:42px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; background:#80B500 !important; border:1px solid #80B500 !important; color:#fff !important; font-size:20px !important; line-height:1 !important;">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center">{{ $sanPhamKhuyenMais->links() }}</div>
                </div>
            </div>
            <!-- PROMOTION PRODUCT AREA END -->
        @endif
        @if(count($sanPhamBanChays) > 0)
            <!-- BEST SELLER PRODUCT AREA START -->
            <div class="ltn__product-slider-area ltn__product-gutter pt-115 pb-70 home-best-seller-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-title-area ltn__section-title-2 text-center">
                                <h1 class="section-title">Sản Phẩm Bán Chạy</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row ltn__tab-product-slider-one-active slick-arrow-1 home-product-slider">
                        @foreach ($sanPhamBanChays as $sanPham)
                            <div class="col-lg-12">
                                <div class="ltn__product-item ltn__product-item-3 text-center">
                                    <div class="product-img">
                                        <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">
                                            <img src="{{ $sanPham->duong_dan_hinh_anh }}" alt="{{ $sanPham->ten_hien_thi }}">
                                        </a>
                                        @if($sanPham->phan_tram_giam > 0)
                                            <div class="product-badge">
                                                <ul>
                                                    <li>-{{ $sanPham->phan_tram_giam }}%</li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="product-info">
                                        <div class="product-ratting">
                                            <ul>
                                                @for ($soSao = 1; $soSao <= 5; $soSao++)
                                                    <li>
                                                        <a href="javascript:void(0)">
                                                            <i class="{{ $soSao <= $sanPham->so_sao_trung_binh ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                        </a>
                                                    </li>
                                                @endfor
                                                <li class="review-total">({{ $sanPham->tong_danh_gia }} Đánh giá)</li>
                                            </ul>
                                        </div>
                                        <h2 class="product-title">
                                            <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">{{ $sanPham->ten_hien_thi }}</a>
                                        </h2>
                                        <div class="product-card-bottom" style="display:flex !important; align-items:flex-end !important; justify-content:space-between !important; gap:10px !important; margin-top:10px !important; text-align:left !important;">
                                            <div class="product-card-price" style="display:flex !important; flex-direction:column !important; align-items:flex-start !important; line-height:1.3 !important;">
                                                @if($sanPham->gia_hien_tai < $sanPham->gia)
                                                    <del style="color:#999 !important; font-size:16px !important; font-weight:200 !important;">
                                                        {{ number_format($sanPham->gia, 0, ',', '.') }}<small class="product-price-symbol" style="margin-left:2px !important; font-size:55% !important; font-weight:400 !important; vertical-align:baseline !important; text-decoration:underline !important;">&#273;</small></del>
                                                @endif
                                                <span style="color:#80B500 !important; font-size:20px !important; font-weight:600 !important;">
                                                    {{ number_format($sanPham->gia_hien_tai, 0, ',', '.') }}<small class="product-price-symbol" style="margin-left:2px !important; font-size:55% !important; font-weight:400 !important; vertical-align:baseline !important; text-decoration:underline !important;">&#273;</small></span>
                                            </div>
                                            <div class="product-card-sold" style="color:#111 !important; font-size:14px !important; white-space:nowrap !important;">
                                                {{ $sanPham->so_luong_da_ban }} đã bán
                                            </div>
                                        </div>
                                        <div class="product-card-actions" style="display:flex !important; align-items:center !important; justify-content:center !important; gap:10px !important; margin-top:14px !important; width:100% !important;">
                                            @auth
                                                <a href="javascript:void(0)" class="product-wishlist-btn add-to-wishlist"
                                                 data-id="{{ $sanPham->ma_san_pham }}" title="Yêu thích" style="width:34px !important; height:42px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; color:#80B500 !important; font-size:24px !important; line-height:1 !important;">
                                                    <i class="far fa-heart"></i>
                                                </a>
                                            @endauth
                                            <a href="javascript:void(0)" class="product-buy-now-btn add-to-cart-btn buy-now-btn"
                                             data-id="{{ $sanPham->ma_san_pham }}"
                                                 style="height:42px !important; flex:1 1 auto !important; max-width:178px !important; min-width:0 !important; padding:0 16px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; background:#80B500 !important; border:1px solid #80B500 !important; color:#fff !important; font-size:15px !important; font-weight:600 !important; line-height:1 !important;">
                                                 Mua ngay</a>
                                            <a href="javascript:void(0)" class="product-cart-btn add-to-cart-btn"
                                             data-id="{{ $sanPham->ma_san_pham }}" title="Thêm vào giỏ hàng"
                                                style="width:50px !important; height:42px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; background:#80B500 !important; border:1px solid #80B500 !important; color:#fff !important; font-size:20px !important; line-height:1 !important;">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- BEST SELLER PRODUCT AREA END -->
        @endif
        <!-- CATEGORY AREA START -->
        <div class="ltn__category-area section-bg-1-- ltn__primary-bg before-bg-1 bg-image bg-overlay-theme-black-5--0 pt-115 pb-90 home-category-section"
            data-bg="{{asset('assets/clients/img/bg/5.jpg')}}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-area ltn__section-title-2 text-center">
                            <h1 class="section-title white-color">Danh mục</h1>
                        </div>
                    </div>
                </div>
                <div class="row ltn__category-slider-active slick-arrow-1">
                    @foreach ($danhMucs as $danhMuc)
                        <div class="col-12">
                            <div class="ltn__category-item ltn__category-item-3 text-center">
                                <div class="ltn__category-item-img">
                                    <a href="{{ route('san-pham.danh-sach', ['ma_danh_muc' => $danhMuc->ma_danh_muc]) }}">
                                        <img src="{{$danhMuc->duong_dan_hinh_anh}}" alt="{{$danhMuc->ten}}">
                                    </a>
                                </div>
                                <div class="ltn__category-item-name">
                                    <h5><a href="{{ route('san-pham.danh-sach', ['ma_danh_muc' => $danhMuc->ma_danh_muc]) }}">{{ $danhMuc->ten }}</a></h5>
                                    <h6>{{count($danhMuc->sanPhams)}} Sản phẩm</h6>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- CATEGORY AREA END -->


    @endsection
