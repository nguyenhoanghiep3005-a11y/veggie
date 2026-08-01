@extends('layouts.client_home')

@section('title','Trang Chủ')

@section('content')

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
                        <a href="{{route('san-pham.chi-tiet',$sanPham->duong_dan)}}">
                            <img src="{{$sanPham->duong_dan_hinh_anh}}" alt="{{$sanPham->ten}}">
                        </a>
                        @if($sanPham->phan_tram_giam > 0)
                        <div class="product-badge">
                            <ul>
                                <li>-{{$sanPham->phan_tram_giam}}%</li>
                            </ul>
                        </div>
                        @endif
                        <div class="product-hover-action">
                            <ul>
                                <li>
                                    <a href="#" title="Xem nhanh" data-bs-toggle="modal"
                                        data-bs-target="#quick_view_modal-{{$sanPham->ma_san_pham}}">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" title="Thêm vào giỏ hàng" class="add-to-cart-btn"
                                        data-id="{{$sanPham->ma_san_pham}}">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                </li>
                                @auth
                                <li>
                                    <a href="javascript:void(0)" class="add-to-wishlist"
                                        data-id="{{ $sanPham->ma_san_pham }}">
                                        <i class="far fa-heart"></i>
                                    </a>
                                </li>
                                @endauth
                            </ul>
                        </div>
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
                                <li class="review-total">
                                    ({{ $sanPham->tong_danh_gia }} Đánh giá)
                                </li>
                            </ul>
                        </div>
                        <h2 class="product-title">
                            <a href="{{route('san-pham.chi-tiet',$sanPham->duong_dan)}}">{{$sanPham->ten}}</a>
                        </h2>
                        <div class="product-card-bottom">
                            <div class="product-card-price">
                                @if($sanPham->gia_khuyen_mai < $sanPham->gia)
                                <del>{{number_format($sanPham->gia,0,',','.')}}<small class="product-price-symbol">&#273;</small></del>
                                @endif
                                <span>{{number_format($sanPham->gia_khuyen_mai,0,',','.')}}<small class="product-price-symbol">&#273;</small></span>
                            </div>
                            <div class="product-card-sold">
                                {{ $sanPham->so_luong_da_ban }} đã bán
                            </div>
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
                        <a href="{{route('san-pham.chi-tiet',$sanPham->duong_dan)}}">
                            <img src="{{$sanPham->duong_dan_hinh_anh}}" alt="{{$sanPham->ten}}">
                        </a>
                        @if($sanPham->phan_tram_giam > 0)
                        <div class="product-badge">
                            <ul>
                                <li>-{{$sanPham->phan_tram_giam}}%</li>
                            </ul>
                        </div>
                        @endif
                        <div class="product-hover-action">
                            <ul>
                                <li>
                                    <a href="#" title="Xem nhanh" data-bs-toggle="modal"
                                        data-bs-target="#quick_view_modal-{{$sanPham->ma_san_pham}}">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" title="Thêm vào giỏ hàng" class="add-to-cart-btn"
                                        data-id="{{$sanPham->ma_san_pham}}">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                </li>
                                @auth
                                <li>
                                    <a href="javascript:void(0)" class="add-to-wishlist"
                                        data-id="{{ $sanPham->ma_san_pham }}">
                                        <i class="far fa-heart"></i>
                                    </a>
                                </li>
                                @endauth
                            </ul>
                        </div>
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
                                <li class="review-total">
                                    ({{ $sanPham->tong_danh_gia }} Đánh giá)
                                </li>
                            </ul>
                        </div>
                        <h2 class="product-title">
                            <a href="{{route('san-pham.chi-tiet',$sanPham->duong_dan)}}">{{$sanPham->ten}}</a>
                        </h2>
                        <div class="product-card-bottom">
                            <div class="product-card-price">
                                @if($sanPham->gia_hien_tai < $sanPham->gia)
                                <del>{{number_format($sanPham->gia,0,',','.')}}<small class="product-price-symbol">&#273;</small></del>
                                @endif
                                <span>{{number_format($sanPham->gia_hien_tai,0,',','.')}}<small class="product-price-symbol">&#273;</small></span>
                            </div>
                            <div class="product-card-sold">
                                {{ $sanPham->so_luong_da_ban }} đã bán
                            </div>
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


@foreach ($sanPhamModals as $sanPham)
@include('clients.components.modals.includes.noi-dung-modal')
@endforeach

@endsection
