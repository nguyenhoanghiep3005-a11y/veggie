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
                                        <a href="{{route('products.index')}}"
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
                                        <a href="{{route('about')}}"
                                            class="theme-btn-1 btn btn-effect-1 text-uppercase">
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

@if($promotionProducts->count() > 0)
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
        <div class="row ltn__tab-product-slider-one-active slick-arrow-1 home-product-slider {{$promotionProducts->count() > 4 ? 'has-many-products' : ''}}">
            @foreach ($promotionProducts as $product)
            @php
                $salePrice = $product->promotion_price ?? $product->current_price;
                $discountPercent = 0;
                if ($product->price > 0 && $salePrice < $product->price) {
                    $discountPercent = round((($product->price - $salePrice) / $product->price) * 100);
                }
            @endphp
            <div class="col-lg-12">
                <div class="ltn__product-item ltn__product-item-3 text-center">
                    <div class="product-img">
                        <a href="{{route('product.detail',$product->slug)}}">
                            <img src="{{$product->image_url}}" alt="{{$product->name}}">
                        </a>
                        @if($discountPercent > 0)
                        <div class="product-badge">
                            <ul>
                                <li>-{{$discountPercent}}%</li>
                            </ul>
                        </div>
                        @endif
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
                                    <a href="javascript:void(0)" class="add-to-wishlist"
                                        data-id="{{ $product->id }}">
                                        <i class="far fa-heart"></i>
                                    </a>
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
                                @for ($i = 1; $i <= 5; $i++)
                                <li>
                                    <a href="javascript:void(0)">
                                        <i class="{{ $i <= $avgRating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                    </a>
                                </li>
                                @endfor
                                <li class="review-total">
                                    ({{ $totalReviews }} Đánh giá)
                                </li>
                            </ul>
                        </div>
                        <h2 class="product-title">
                            <a href="{{route('product.detail',$product->slug)}}">{{$product->name}}</a>
                        </h2>
                        <div class="product-price">
                            @if($salePrice < $product->price)
                            <del class="d-block">{{number_format($product->price,0,',','.')}} VNĐ</del>
                            @endif
                            <span>{{number_format($salePrice,0,',','.')}} VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @foreach ($promotionProducts as $product)
        @include('clients.components.modals.includes.include-modals')
        @endforeach
    </div>
</div>
<!-- PROMOTION PRODUCT AREA END -->
@endif


@if($bestSellerCategories->count() > 0)
<!-- PRODUCT TAB AREA START (product-item-3) -->
<div class="ltn__product-tab-area ltn__product-gutter pt-115 pb-70 home-best-seller-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2 text-center">
                    <h1 class="section-title">Sản Phẩm Bán Chạy </h1>
                </div>
                <div class="ltn__tab-menu ltn__tab-menu-2 ltn__tab-menu-top-right-- text-uppercase text-center">
                    <div class="nav">
                        @foreach ($bestSellerCategories as $index => $category)
                        <a class="{{$index ==0? 'active show' : ''}}" data-bs-toggle="tab"
                            href="#tab-{{$category->id}}">{{$category->name}}</a>
                        @endforeach
                    </div>
                </div>
                <div class="tab-content">
                    @foreach ($bestSellerCategories as $index => $category)
                    <div class="tab-pane fade {{$index == 0? 'active show' : ''}}" id="tab-{{$category->id}}">
                        <div class="ltn__product-tab-content-inner">
                            <div class="row ltn__tab-product-slider-one-active slick-arrow-1 home-product-slider {{$category->products->count() > 4 ? 'has-many-products' : ''}}">
                                @foreach ($category->products as $product)
                                <!-- ltn__product-item -->
                                <div class="col-lg-12">
                                    <div class="ltn__product-item ltn__product-item-3 text-center">
                                        <div class="product-img">
                                            <a href="{{route('product.detail',$product->slug)}}"><img src="{{$product->image_url}}" alt="{{$product->name}}"></a>
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
                                                        <a href="javascript:void(0)" class="add-to-wishlist"
                                                            data-id="{{ $product->id }}">
                                                            <i class="far fa-heart"></i>
                                                        </a>

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
                                                    href="{{route('product.detail',$product->slug)}}">{{$product->name}}</a>
                                            </h2>
                                            <div class="product-price">
                                                <span>{{number_format($product->current_price,0,',','.')}} VNĐ</span>
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
            @foreach ($categories as $category)
            <div class="col-12">
                <div class="ltn__category-item ltn__category-item-3 text-center">
                    <div class="ltn__category-item-img">
                        <a href="shop.html">
                            <img src="{{asset('storage/'.$category->image)}}" alt="{{$category->name}}">
                        </a>
                    </div>
                    <div class="ltn__category-item-name">
                        <h5><a href="{{route('products.index')}}">{{$category->name}}</a></h5>
                        <h6>{{$category->products->count()}} Sản phẩm</h6>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- CATEGORY AREA END -->


<!-- COUNTER UP AREA START -->
<div class="ltn__counterup-area bg-image bg-overlay-theme-black-80 pt-115 pb-70"
    data-bg="{{ asset('assets/clients/img/bg/5.jpg') }}">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/2.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">733</span><span class="counterUp-icon">+</span></h1>
                    <h6>Khách hàng hài lòng</h6>
                </div>
            </div>
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
            <div class="col-md-3 col-sm-6 align-self-center">
                <div class="ltn__counterup-item-3 text-color-white text-center">
                    <div class="counter-icon">
                        <img src="{{asset('assets/clients/img/icons/icon-img/4.png')}}" alt="#">
                    </div>
                    <h1><span class="counter">100</span><span class="counterUp-icon">+</span></h1>
                    <h6>Giải thưởng & chứng nhận</h6>
                </div>
            </div>
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
@endsection
