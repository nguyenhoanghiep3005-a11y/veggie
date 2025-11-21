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
                                    <div class="single-large-img">
                                        @foreach ($product->images as $image)
                                            <a href="{{asset('storage/' . $image->image)}}"
                                               data-rel="lightcase:myCollection">
                                                <img src="{{asset('storage/' . $image->image)}}" alt="{{$product->name}}">
                                            </a>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="ltn__shop-details-small-img slick-arrow-2">
                                    @foreach ($product->images as $image)
                                        <div class="single-small-img">
                                            <img src="{{asset('storage/' . $image->image)}}" alt="{{$product->name}}">
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
                                                <input type="text" value="1" class="cart-plus-minus-box" readonly data-max="{{$product->stock}}">
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

                            </div>
                        </div>

                    </div>
                </div>

                {{-- MÔ TẢ – ĐÁNH GIÁ --}}
                <div class="ltn__shop-details-tab-inner ltn__shop-details-tab-inner-2">
                    ...
                </div>

            </div>
        </div>
    </div>
</div>

{{-- SẢN PHẨM TƯƠNG TỰ --}}
<div class="ltn__product-slider-area ltn__product-gutter pb-70">
    <div class="container">

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
