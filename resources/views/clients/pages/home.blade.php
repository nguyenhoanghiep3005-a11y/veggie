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
                                        <p>Cam kết cung cấp nông sản khô chất lượng, ược chọn lọc kỹ
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
                                            ảm bảo an toàn, tiện lợi và dễ bảo quản.</p>
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

@if(count($promotionProducts) > 0)
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
            @foreach ($promotionProducts as $product)
            <div class="col-lg-12">
                <div class="ltn__product-item ltn__product-item-3 text-center">
                    <div class="product-img">
                        <a href="{{route('product.detail',$product->slug)}}">
                            <img src="{{$product->image_url}}" alt="{{$product->name}}">
                        </a>
                        @if($product->home_discount_percent > 0)
                        <div class="product-badge">
                            <ul>
                                <li>-{{$product->home_discount_percent}}%</li>
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
                                @for ($i = 1; $i <= 5; $i++)
                                <li>
                                    <a href="javascript:void(0)">
                                        <i class="{{ $i <= $product->home_avg_rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                    </a>
                                </li>
                                @endfor
                                <li class="review-total">
                                    ({{ $product->home_total_reviews }} Đánh giá)
                                </li>
                            </ul>
                        </div>
                        <h2 class="product-title">
                            <a href="{{route('product.detail',$product->slug)}}">{{$product->name}}</a>
                        </h2>
                        <div class="product-card-bottom">
                            <div class="product-card-price">
                                @if($product->home_sale_price < $product->price)
                                <del>{{number_format($product->price,0,',','.')}}<small class="product-price-symbol">&#273;</small></del>
                                @endif
                                <span>{{number_format($product->home_sale_price,0,',','.')}}<small class="product-price-symbol">&#273;</small></span>
                            </div>
                            <div class="product-card-sold">
                                {{ $product->sold_quantity ?? 0 }} &#273;&#227; b&#225;n
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- PROMOTION PRODUCT AREA END -->
@endif


@if(count($bestSellerCategories) > 0)
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
                        @foreach ($bestSellerCategories as $category)
                        <a class="{{$category->home_tab_class}}" data-bs-toggle="tab"
                            href="#tab-{{$category->id}}">{{$category->name}}</a>
                        @endforeach
                    </div>
                </div>
                <div class="tab-content">
                    @foreach ($bestSellerCategories as $category)
                    <div class="tab-pane fade {{$category->home_content_class}}" id="tab-{{$category->id}}">
                        <div class="ltn__product-tab-content-inner">
                            <div class="row ltn__tab-product-slider-one-active slick-arrow-1 home-product-slider">
                                @foreach ($category->home_products as $product)
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
                                                    @for ($i = 1; $i <= 5; $i++) <li>
                                                        <a href="javascript:void(0)">
                                                            <i
                                                                class="{{ $i <= $product->home_avg_rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                        </a>
                                                        </li>
                                                        @endfor

                                                        <li class="review-total">
                                                            ({{ $product->home_total_reviews }} Đánh giá)
                                                        </li>
                                                </ul>
                                            </div>
                                            <h2 class="product-title"><a
                                                    href="{{route('product.detail',$product->slug)}}">{{$product->name}}</a>
                                            </h2>
                                            <div class="product-card-bottom">
                                                <div class="product-card-price">
                                                    @if($product->current_price < $product->price)
                                                    <del>{{number_format($product->price,0,',','.')}}<small class="product-price-symbol">&#273;</small></del>
                                                    @endif
                                                    <span>{{number_format($product->current_price,0,',','.')}}<small class="product-price-symbol">&#273;</small></span>
                                                </div>
                                                <div class="product-card-sold">
                                                    {{ $product->sold_quantity ?? 0 }} &#273;&#227; b&#225;n
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @endforeach
                            </div>
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
                        <a href="{{ route('products.index', ['category_id' => $category->id]) }}">
                            <img src="{{$category->image_url}}" alt="{{$category->name}}">
                        </a>
                    </div>
                    <div class="ltn__category-item-name">
                        <h5><a href="{{ route('products.index', ['category_id' => $category->id]) }}">{{ $category->name }}</a></h5>
                        <h6>{{count($category->products)}} Sản phẩm</h6>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- CATEGORY AREA END -->


@foreach ($homeModalProducts as $product)
@include('clients.components.modals.includes.include-modals')
@endforeach

@endsection
