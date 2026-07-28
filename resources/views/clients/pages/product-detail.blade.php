@extends('layouts.client')
@section('title','Chi tiết sản phẩm')
@section('breadcrumb','Chi tiết sản phẩm')

@section('content')
@php
    $sellableStock = $product->sellableStock();
    $avgRating = $product->reviews->avg('rating');
    if (! $avgRating) {
        $avgRating = 0;
    }
    $totalReviews = $product->reviews->count();
    $soldQuantity = 0;
    if (isset($product->sold_quantity)) {
        $soldQuantity = $product->sold_quantity;
    }
@endphp
<div class="ltn__shop-details-area pb-85">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">

                {{-- CHI TIẾT SẢN PHẨM --}}
                <div class="ltn__shop-details-inner mb-60 product-detail-wrapper" data-current-slug="{{ $product->slug }}">
                    <div class="row">
                        {{-- ẢNH SẢN PHẨM --}}
                        <div class="col-md-6">
                            <div class="ltn__shop-details-img-gallery">
                                <div class="ltn__shop-details-large-img">
                                    @if (count($product->detail_image_urls) > 0)
                                        @foreach ($product->detail_image_urls as $imgUrl)
                                            <div class="single-large-img">
                                                <a href="{{ $imgUrl }}"
                                                    data-rel="lightcase:myCollection">
                                                    <img src="{{ $imgUrl }}" alt="{{ $product->display_name }}" class="product-detail-image">
                                                </a>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="single-large-img">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->display_name }}" class="product-detail-image">
                                        </div>
                                    @endif
                                </div>
                                <div class="ltn__shop-details-small-img slick-arrow-2">
                                    @if (count($product->detail_image_urls) > 0)
                                        @foreach ($product->detail_image_urls as $imgUrl)
                                            <div class="single-small-img">
                                                <img src="{{ $imgUrl }}" alt="{{ $product->display_name }}" class="product-detail-image">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="single-small-img">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->display_name }}" class="product-detail-image">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- THÔNG TIN --}}
                        <div class="col-md-6">
                            <div class="modal-product-info shop-details-info pl-0">
                                <h3 class="product-detail-name">{{$product->display_name}}</h3>

                                <div class="product-detail-rating-line">
                                    <span class="product-detail-rating-number">{{ number_format($avgRating, 1) }}</span>
                                    <span class="product-detail-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $avgRating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                        @endfor
                                    </span>
                                    <span class="product-detail-review-count">{{ $totalReviews }} Đánh giá</span>
                                    <span>Đã bán {{ $soldQuantity }}</span>
                                </div>

                                <div class="product-price product-detail-price-box">
                                    @if($product->current_price < $product->price)
                                    <del class="product-detail-old-price">{{number_format($product->price,0, ',', '.')}}<small class="product-price-symbol">đ</small></del>
                                    @endif
                                    <span class="product-detail-price">{{number_format($product->current_price,0, ',', '.')}}<small class="product-price-symbol">đ</small></span>
                                </div>

                                <div class="product-variant-box mt-3">
                                    <h6>Đơn vị</h6>

                                    @if (count($variantProducts) > 1)
                                        <div class="product-variant-list">
                                            @foreach ($variantProducts as $variant)
                                                @php
                                                    $variantStock = $variant->sellableStock();
                                                @endphp
                                                <a href="{{ route('product.detail', $variant->slug) }}"
                                                    data-variant-url="{{ route('product.variant', $variant->slug) }}"
                                                    class="product-variant-option {{ $variant->is_current_variant ? 'active' : '' }} {{ $variantStock <= 0 ? 'out-of-stock' : '' }}">
                                                    <span>{{ $variant->variant_label }}</span>
                                                    <small>{{ $variantStock > 0 ? 'Còn ' . $variantStock : 'Hết hàng' }}</small>
                                                    {{-- <small>
                                                        {{ $variant->stock > 0 ? 'Còn ' . $variant->stock : 'Hết hàng' }}
                                                    </small> --}}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="product-variant-list">
                                            <span class="product-variant-option active">
                                                <span>{{ $product->variant_label }}</span>
                                                <small>{{ $sellableStock > 0 ? 'Còn ' . $sellableStock : 'Hết hàng' }}</small>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ltn__product-details-menu-2">
                                    <ul>
                                        <li>
                                            <div class="cart-plus-minus">
                                                <input type="text" value="1" class="cart-plus-minus-box" readonly
                                                    data-max="{{$sellableStock}}">
                                            </div>
                                        </li>
                                        <li>
                                            <span class="product-detail-cart-action">
                                                    @if ($sellableStock > 0)
                                                <a href="javascript:void(0)" class="theme-btn-1 btn btn-effect-1 add-to-cart-btn"
                                                    data-id="{{$product->id}}">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <span>Thêm vào giỏ hàng</span>
                                                </a>
                                            @else
                                                <span class="theme-btn-1 btn btn-effect-1 product-action-disabled">
                                                    Hết hàng
                                                </span>
                                            @endif
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="ltn__product-details-menu-3">
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0)" class="product-detail-wishlist add-to-wishlist" title="Yêu thích" data-id="{{ $product->id }}">
                                                <i class="far fa-heart"></i>
                                                <span>Yêu thích</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <hr>
                                <div class="ltn__social-media">
                                    <ul>
                                        <li>Share:</li>
                                        <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                        </li>
                                        <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a>
                                        </li>
                                        <li><a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <hr>
                                <div class="ltn__safe-checkout">
                                    <h5>Có thể thanh toán</h5>
                                    <img src="{{asset('assets/clients/img/icons/payment-2.png')}}" alt="Payment Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- MÔ TẢ – ĐÁNH GIÁ --}}
                <div class="ltn__shop-details-tab-inner ltn__shop-details-tab-inner-2">
                    <div class="ltn__shop-details-tab-menu">
                        <div class="nav">
                            <a class="active show" data-bs-toggle="tab" href="#liton_tab_details_1_1">Mô tả</a>
                            <a data-bs-toggle="tab" href="#liton_tab_details_1_2" class="">Đánh giá</a>
                        </div>
                    </div>
                    <div class="tab-content">
                        <!-- MÔ TẢ -->
                        <div class="tab-pane fade active show" id="liton_tab_details_1_1">
                            <div class="ltn__shop-details-tab-content-inner">
                                <h4 class="title-2">Thông tin chi tiết sản phẩm</h4>

                                <div class="product-detail-description">
                                    <p class="product-description-text">{{ $product->description_text }}</p>

                                    <ul class="product-detail-facts">
                                        <li>
                                            <strong>Bảo quản:</strong>
                                            <span class="product-storage-text">{{ $product->storage_text }}</span>
                                        </li>
                                        <li>
                                            <strong>Thương hiệu:</strong>
                                            <span class="product-brand-text">{{ $product->brand_text }}</span>
                                        </li>
                                        <li>
                                            <strong>Sản xuất:</strong>
                                            <span class="product-manufacture-text">{{ $product->manufacture_text }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- ĐÁNH GIÁ -->
                        <div class="tab-pane fade" id="liton_tab_details_1_2">
                            <div class="ltn__shop-details-tab-content-inner">
                                <h4 class="title-2">Đánh giá của khách hàng</h4>
                                <!-- DANH SÁCH BÌNH LUẬN -->
                                <div class="ltn__comment-area mb-30">
                                    <div class="ltn__comment-inner" id="product-review-list">
                                        @include('clients.components.modals.includes.review-list',
                                        ['product'=>$product])
                                    </div>
                                </div>
                                <!-- FORM THÊM ĐÁNH GIÁ -->
                                <div class="ltn__comment-reply-area ltn__form-box mb-30">
                                    <form id="review-form" data-product-id={{$product->id}}>
                                        <h4 class="title-2">Thêm ánh giá</h4>
                                        <div class="mb-30">
                                            <div class="add-a-review">
                                                <h6>Chọn số sao:</h6>
                                                <div class="product-ratting">
                                                    <ul>
                                                        @for ($i = 1; $i <= 5; $i++) <li>
                                                            <a href="javascript:void(0)" class="rating-star"
                                                                data-value="{{$i}}">
                                                                <i class="far fa-star"></i>
                                                            </a></li>
                                                            @endfor
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="rating" id="rating-value" value="0">
                                        <div class="input-item input-item-textarea ltn__custom-icon">
                                            <textarea placeholder="Nhập nội dung ánh giá..."
                                                id="review-content"></textarea>
                                        </div>
                                        <div class="btn-wrapper">
                                            <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit">
                                                Gửi ánh giá
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
{{-- SẢN PHẨM TƯƠNG TỰ --}}
<div class="ltn__product-slider-area ltn__product-gutter pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2">
                    <h6 class="section-subtitle ltn__secondary-color"></h6>
                    <h1 class="section-title">Sản phẩm tương tự<span></span></h1>
                </div>
            </div>
        </div>
        <div class="row ltn__related-product-slider-one-active slick-arrow-1">
            @foreach ($relatedProducts as $relatedProduct)
            <div class="col-lg-12">
                <div class="ltn__product-item ltn__product-item-3 text-center">

                    <div class="product-img">
                        <a href="{{route('product.detail', $relatedProduct->slug)}}">
                            <img src="{{$relatedProduct->image_url}}" alt="{{$relatedProduct->display_name}}">
                        </a>

                        <div class="product-hover-action">
                            <ul>

                                {{-- QUICK VIEW --}}
                                <li>
                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#quick_view_modal-{{$relatedProduct->id}}">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </li>

                                {{-- ADD TO CART --}}
                                <li>
                                    <a href="javascript:void(0)" class="add-to-cart-btn"
                                        data-id="{{$relatedProduct->id}}">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                </li>

                                {{-- WISHLIST --}}
                                <li>
                                    <a href="javascript:void(0)" class="add-to-wishlist" data-id="{{ $relatedProduct->id }}" title="Yêu thích">
                                        <i class="far fa-heart"></i>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>

                    <div class="product-info">
                        <h2 class="product-title">
                            <a href="{{route('product.detail', $relatedProduct->slug)}}">{{$relatedProduct->display_name}}</a>
                        </h2>
                        <div class="product-card-bottom">
                            <div class="product-card-price">
                                @if($relatedProduct->current_price < $relatedProduct->price)
                                <del>{{number_format($relatedProduct->price,0,',','.')}}<small class="product-price-symbol">đ</small></del>
                                @endif
                                <span>{{number_format($relatedProduct->current_price,0,',','.')}}<small class="product-price-symbol">đ</small></span>
                            </div>
                            <div class="product-card-sold">
                                {{ isset($relatedProduct->sold_quantity) ? $relatedProduct->sold_quantity : 0 }} đã bán
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        {{-- MODAL CHO SẢN PHẨM TƯƠNG TỰ --}}
        <div id="product-detail-modal-container">
            @include('clients.components.modals.includes.include-modals', ['product' => $product])
        </div>

        @foreach ($relatedProducts as $relatedProduct)
        @include('clients.components.modals.includes.include-modals', ['product' => $relatedProduct])
        @endforeach

    </div>
</div>
@endsection
