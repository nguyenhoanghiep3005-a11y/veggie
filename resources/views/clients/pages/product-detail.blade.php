@extends('layouts.client')
@section('title','Chi tiết sản phẩm')
@section('breadcrumb','Chi tiết sản phẩm')

@section('content')
<div class="ltn__shop-details-area pb-85">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">

                {{-- CHI TIẾT SẢN PHẨM --}}
                <div class="ltn__shop-details-inner mb-60">
                    <div class="row">
                        {{-- ẢNH SẢN PHẨM --}}
                        <div class="col-md-6">
                            <div class="ltn__shop-details-img-gallery">
                                <div class="ltn__shop-details-large-img">
                                     @foreach ($product->images as $image)
                                    <div class="single-large-img">
                                        <a href="{{asset('storage/' . $image->image)}}"
                                            data-rel="lightcase:myCollection">
                                            <img src="{{asset('storage/' . $image->image)}}" alt="{{$product->name}}">
                                        </a>
                                    </div>
                                         @endforeach
                                </div>
                                <div class="ltn__shop-details-small-img slick-arrow-2">
                                    @foreach ($product->images as $image)
                                    <div class="single-small-img">
                                        <img src="{{asset('storage/'. $image->image)}}" alt="{{$product->name}}">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- THÔNG TIN --}}
                        <div class="col-md-6">
                            <div class="modal-product-info shop-details-info pl-0">
                                <h3>{{$product->name}}</h3>
                                <div class="product-price">
                                    <span>{{number_format($product->price,0, ',', '.')}} VNĐ</span>
                                </div>
                                <div class="ltn__product-details-menu-2">
                                    <ul>
                                        <li>
                                            <div class="cart-plus-minus">
                                                <input type="text" value="1" class="cart-plus-minus-box" readonly
                                                    data-max="{{$product->stock}}">
                                            </div>
                                        </li>
                                        <li>
                                            <a href="#" class="theme-btn-1 btn btn-effect-1 add-to-cart-btn"
                                                data-id="{{$product->id}}">
                                                <i class="fas fa-shopping-cart"></i>
                                                <span>Thêm vào giỏ hàng</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="ltn__product-details-menu-3">
                                    <ul>
                                        <li>
                                            <a href="#" class="" title="Wishlist" data-bs-toggle="modal"
                                                data-bs-target="#liton_wishlist_modal">
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
                                <p>
                                    ngon
                                </p>
                            </div>
                        </div>
                        <!-- ĐÁNH GIÁ -->
                        <div class="tab-pane fade" id="liton_tab_details_1_2">
                            <div class="ltn__shop-details-tab-content-inner">
                                <h4 class="title-2">Đánh giá của khách hàng</h4>
                                <!-- DANH SÁCH BÌNH LUẬN -->
                                <div class="ltn__comment-area mb-30">
                                    <div class="ltn__comment-inner">
                                        @include('clients.components.modals.includes.review-list',
                                        ['product'=>$product])
                                    </div>
                                </div>
                                <!-- FORM THÊM ĐÁNH GIÁ -->
                                <div class="ltn__comment-reply-area ltn__form-box mb-30">
                                    <form id="review-form" data-product-id={{$product->id}}>
                                        <h4 class="title-2">Thêm đánh giá</h4>
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
                                            <textarea placeholder="Nhập nội dung đánh giá..."
                                                id="review-content"></textarea>
                                        </div>
                                        <div class="btn-wrapper">
                                            <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit">
                                                Gửi đánh giá
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
                            <img src="{{$relatedProduct->image_url}}" alt="{{$relatedProduct->name}}">
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
                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#liton_wishlist_modal-{{$relatedProduct->id}}">
                                        <i class="far fa-heart"></i>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>

                    <div class="product-info">
                        <h2 class="product-title">
                            <a href="{{route('product.detail', $relatedProduct->slug)}}">{{$relatedProduct->name}}</a>
                        </h2>
                        <div class="product-price">
                            <span>{{number_format($relatedProduct->price , 0 , ',',".")}} VND</span>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        {{-- MODAL CHO SẢN PHẨM TƯƠNG TỰ --}}
        @foreach ($relatedProducts as $relatedProduct)
        @include('clients.components.modals.includes.include-modals', ['product' => $relatedProduct])
        @endforeach

    </div>
</div>
@endsection