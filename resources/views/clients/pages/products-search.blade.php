@extends('layouts.client')

@section('title','Tìm kiếm')
@section('breadcrumb','Tìm kiếm')
@section('content')

<!-- PRODUCT DETAILS AREA START -->
<div class="ltn__product-area ltn__product-gutter mb-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="ltn__shop-options">
                    <ul class="search-sort-right">
                        <li>
                            <div class="short-by text-center">
                                <select id="sort-by" class="nice-select">
                                    <option value="default">Sắp xếp mặc định</option>
                                    <option value="latest">Sắp xếp theo sản phẩm mới</option>
                                    <option value="price_asc">Sắp xếp theo giá: Thấp ến Cao</option>
                                    <option value="price_desc">Sắp xếp theo giá: Cao ến Thấp</option>
                                </select>
                            </div>
                        </li>

                    </ul>
                </div>
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
                                                <img src="{{ $product->image_url }}" alt="{{ $product->display_name }}">
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
                                                $soldQuantity = $product->sold_quantity ?? 0;
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
                                                        ({{ $totalReviews }} ánh giá)
                                                    </li>
                                                </ul>
                                            </div>

                                            <h2 class="product-title">
                                                <a href="{{ route('product.detail', $product->slug) }}">
                                                    {{ $product->display_name }}
                                                </a>
                                            </h2>

                                            <div class="product-card-bottom">
                                                <div class="product-card-price">
                                                    @if($product->current_price < $product->price)
                                                    <del>{{ number_format($product->price, 0, ',', '.') }}<small class="product-price-symbol">&#273;</small></del>
                                                    @endif
                                                    <span>{{ number_format($product->current_price, 0, ',', '.') }}<small class="product-price-symbol">&#273;</small></span>
                                                </div>
                                                <div class="product-card-sold">
                                                    {{ $soldQuantity }} &#273;&#227; b&#225;n
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                </div>
                <div class="ltn__pagination-area text-center">
                    <div class="ltn__pagination">
                        {!! $products->links('clients.components.pagination.pagination_custom') !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- PRODUCT DETAILS AREA END -->


@foreach ($products as $product)
@include('clients.components.modals.includes.include-modals')
@endforeach
@endsection
