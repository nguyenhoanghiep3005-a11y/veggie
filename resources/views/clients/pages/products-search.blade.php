@extends('layouts.client')

@section('title','Tìm kiếm')
@section('breadcrumb','Tìm kiếm')
@section('content')

<!-- PRODUCT DETAILS AREA START -->
<div class="ltn__product-area ltn__product-gutter mb-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="liton_product_grid">

                        <div class="ltn__product-tab-content-inner ltn__product-grid-view">
                            <div class="row">

                                @foreach ($products as $product)
                                <div class="col-xl-3 col-lg-4 col-sm-6 col-6">

                                    <div class="ltn__product-item ltn__product-item-3 text-center">

                                        <!-- ẢNH -->
                                        <div class="product-img">
                                            <a href="{{ route('product.detail', $product->slug) }}">
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                            </a>

                                            <div class="product-hover-action">
                                                <ul>
                                                    <!-- Quick View -->
                                                    <li>
                                                        <a href="javascript:void(0)" title="Xem nhanh"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#quick_view_modal-{{ $product->id }}">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                    </li>

                                                    <!-- Add to Cart -->
                                                    <li>
                                                        <a href="javascript:void(0)" title="Thêm vào giỏ hàng"
                                                            class="add-to-cart-btn"
                                                            data-id="{{ $product->id }}">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </a>
                                                    </li>

                                                    <!-- Wishlist -->
                                                    <li>
                                                        <a href="javascript:void(0)" title="Yêu thích"
                                                            class="add-to-wishlist"
                                                            data-id="{{ $product->id }}">
                                                            <i class="far fa-heart"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- THÔNG TIN -->
                                        <div class="product-info">

                                            <!-- ⭐ RATING -->
                                            @php
                                                $avgRating = $product->reviews->avg('rating') ?? 0;
                                                $totalReviews = $product->reviews->count();
                                            @endphp

                                            <div class="product-ratting">
                                                <ul>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <li>
                                                            <a href="javascript:void(0)">
                                                                <i class="{{ $i <= $avgRating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                            </a>
                                                        </li>
                                                    @endfor

                                                    <li class="review-total">
                                                        ({{ $totalReviews }} đánh giá)
                                                    </li>
                                                </ul>
                                            </div>

                                            <h2 class="product-title">
                                                <a href="{{ route('product.detail', $product->slug) }}">
                                                    {{ $product->name }}
                                                </a>
                                            </h2>

                                            <div class="product-price">
                                                <span>{{ number_format($product->current_price, 0, ',', '.') }} VND</span>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- PRODUCT DETAILS AREA END -->

@endsection
