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
                                        100% Rau Củ Quả Tươi Sạch
                                    </h6>
                                    <h1 class="slide-title animated">Trải nghiệm <br> hương vị tự nhiên <br> từ nông
                                        trại</h1>
                                    <div class="slide-brief animated">
                                        <p>Cam kết cung cấp nông sản tươi ngon, an toàn, được thu hoạch mỗi ngày
                                            từ những nông trại Việt Nam chất lượng cao.</p>
                                    </div>
                                    <div class="btn-wrapper animated">
                                        <a href="{{route('products.index')}}" class="theme-btn-1 btn btn-effect-1 text-uppercase">
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

                                    <h1 class="slide-title animated">Thực Phẩm Hữu Cơ <br> Vì Sức Khỏe Gia Đình Bạn</h1>
                                    <div class="slide-brief animated">
                                        <p>Chọn lựa kỹ càng từ nguồn nông sản sạch, không chất bảo quản,
                                            đảm bảo hương vị tự nhiên và dinh dưỡng tốt nhất.</p>
                                    </div>
                                    <div class="btn-wrapper animated">
                                        <a href="{{route('about')}}" class="theme-btn-1 btn btn-effect-1 text-uppercase">
                                            Khám Phá Ngay
                                        </a>
                                        <a href="{{route('service')}}" class="btn btn-transparent btn-effect-3">
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

<!-- BANNER AREA START -->
<div class="ltn__banner-area mt-120 mb-90">
    <div class="container">
        <div class="row ltn__custom-gutter--- justify-content-center">
            <div class="col-lg-6 col-md-6">
                <div class="ltn__banner-item">
                    <div class="ltn__banner-img">
                        <a href="shop.html"><img src="{{asset('assets/clients/img/banner/13.png')}}"
                                alt="Banner Image"></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ltn__banner-item">
                            <div class="ltn__banner-img">
                                <a href="shop.html"><img src="{{asset('assets/clients/img/banner/14.png')}}"
                                        alt="Banner Image"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="ltn__banner-item">
                            <div class="ltn__banner-img">
                                <a href="shop.html"><img src="{{asset('assets/clients/img/banner/15.png')}}"
                                        alt="Banner Image"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- BANNER AREA END -->
<!-- CATEGORY AREA START -->
<div class="ltn__category-area section-bg-1-- ltn__primary-bg before-bg-1 bg-image bg-overlay-theme-black-5--0 pt-115 pb-90"
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
            @foreach ($categories as $category)
            <div class="col-12">
                <div class="ltn__category-item ltn__category-item-3 text-center">
                    <div class="ltn__category-item-img">
                        <a href="shop.html">
                            <img src="{{asset('storage/'.$category->image)}}" alt="{{$category->name}}">
                        </a>
                    </div>
                    <div class="ltn__category-item-name">
                        <h5><a href="shop.html">{{$category->name}}</a></h5>
                        <h6>{{$category->products->count()}} Sản phẩm</h6>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- CATEGORY AREA END -->

<!-- PRODUCT TAB AREA START (product-item-3) -->
<div class="ltn__product-tab-area ltn__product-gutter pt-115 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2 text-center">
                    <h1 class="section-title">Sản phẩm</h1>
                </div>
                <div class="ltn__tab-menu ltn__tab-menu-2 ltn__tab-menu-top-right-- text-uppercase text-center">
                    <div class="nav">
                        @foreach ($categories as $index => $category)
                        <a class="{{$index ==0? 'active show' : ''}}" data-bs-toggle="tab"
                            href="#tab-{{$category->id}}">{{$category->name}}</a>
                        @endforeach
                    </div>
                </div>
                <div class="tab-content">
                    @foreach ($categories as $index => $category)
                    <div class="tab-pane fade {{$index == 0? 'active show' : ''}}" id="tab-{{$category->id}}">
                        <div class="ltn__product-tab-content-inner">
                            <div class="row ltn__tab-product-slider-one-active slick-arrow-1">
                                @foreach ($category->products as $product)
                                <!-- ltn__product-item -->
                                <div class="col-lg-12">
                                    <div class="ltn__product-item ltn__product-item-3 text-center">
                                        <div class="product-img">
                                            <a href="#"><img src="{{$product->image_url}}" alt="{{$product->name}}"></a>

                                            <div class="product-hover-action">
                                                <ul>
                                                    <li>
                                                        <a href="#" title="Xem nhanh" data-bs-toggle="modal"
                                                            data-bs-target="#quick_view_modal-{{$product->id}}">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" title="Thêm vào giỏ hàng" class="add-to-cart-btn"
                                                            data-id="{{$product->id}}">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" title="Yêu thích" data-bs-toggle="modal"
                                                            data-bs-target="#liton_wishlist_modal-{{$product->id}}">
                                                            <i class="far fa-heart"></i></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="product-info">
                                            <div class="product-ratting">
                                                <ul>
                                                    @php
                                                    $avgRating = $product->reviews->avg('rating') ?? 0;
                                                    $totalReviews = $product->reviews->count();
                                                    @endphp

                                                    @for ($i = 1; $i <= 5; $i++) <li>
                                                        <a href="javascript:void(0)">
                                                            <i
                                                                class="{{ $i <= $avgRating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                        </a>
                                                        </li>
                                                        @endfor

                                                        <li class="review-total">
                                                            ({{ $totalReviews }} Đánh giá)
                                                        </li>
                                                </ul>
                                            </div>
                                            <h2 class="product-title"><a
                                                    href="product-details.html">{{$product->name}}</a></h2>
                                            <div class="product-price">
                                                <span>{{number_format($product->price,0,',','.')}} VNĐ</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @endforeach
                            </div>
                            @foreach ($category->products as $product)
                            @include('clients.components.modals.includes.include-modals')

                            @endforeach
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
<!-- PRODUCT TAB AREA END -->

<!-- COUNTER UP AREA START -->
<div class="ltn__counterup-area bg-image bg-overlay-theme-black-80 pt-115 pb-70"
    data-bg="{{ asset('assets/clients/img/bg/5.jpg') }}">
    <div class="container">
        <div class="row">
            <!-- Khách hàng hài lòng -->
            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/2.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">733</span><span class="counterUp-icon">+</span></h1>
                    <h6>Khách hàng hài lòng</h6>
                </div>
            </div>

            <!-- Cốc cà phê tượng trưng cho sự phục vụ tận tâm -->
            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/3.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">33</span><span class="counterUp-letter">K</span><span
                            class="counterUp-icon">+</span></h1>
                    <h6>Đơn hàng đã giao</h6>
                </div>
            </div>

            <!-- Giải thưởng hoặc chứng nhận -->
            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/4.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">100</span><span class="counterUp-icon">+</span></h1>
                    <h6>Giải thưởng & chứng nhận</h6>
                </div>
            </div>

            <!-- Khu vực phân phối -->
            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/5.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">21</span><span class="counterUp-icon">+</span></h1>
                    <h6>Tỉnh thành phân phối</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- COUNTER UP AREA END -->
<!-- CALL TO ACTION START (call-to-action-4) -->
<div class="ltn__call-to-action-area ltn__call-to-action-4 bg-image pt-115 pb-120"
    data-bg="{{asset('assets/clients/img/bg/6.jpg')}}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="call-to-action-inner call-to-action-inner-4 text-center">
                    <div class="section-title-area ltn__section-title-2">

                        <h1 class="section-title white-color">Liên hệ ngay: 0388536385</h1>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="ltn__call-to-4-img-1">
        <img src="{{asset('assets/clients/img/bg/12.png')}}" alt="#">
    </div>

</div>
<!-- CALL TO ACTION END -->
@endsection