<div class="ltn__product-tab-content-inner ltn__product-grid-view">
    <div class="row">
        @foreach ($products as $product)
        <div class="col-xl-4 col-sm-6 col-6">
            <div class="ltn__product-item ltn__product-item-3 text-center">
                <div class="product-img">
                    <a href="{{route('product.detail',$product->slug)}}">
                        <img src="{{$product->image_url}}" alt="{{$product->display_name}}"></a>
                    <div class="product-hover-action">
                        <ul>
                            <li>
                                <a href="javascript:void(0)" title="Xem nhanh" data-bs-toggle="modal"
                                    data-bs-target="#quick_view_modal-{{$product->id}}">
                                    <i class="far fa-eye"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" title="Thêm vào giỏ hàng" class="add-to-cart-btn"
                                    data-id="{{$product->id}}">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" title="Yêu thích" class="add-to-wishlist"
                                    data-id="{{$product->id}}">
                                    <i class="far fa-heart"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="product-info">
                    @php
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

                    <div class="product-ratting">
                        <ul>
                            @for ($i = 1; $i <= 5; $i++)
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="{{ $i <= $avgRating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                </a>
                            </li>
                            @endfor
                            <li class="review-total">({{$totalReviews}} Đánh giá)</li>
                        </ul>
                    </div>
                    <h2 class="product-title"><a
                            href="{{route('product.detail',$product->slug)}}">{{$product->display_name}}</a></h2>
                    <div class="product-card-bottom">
                        <div class="product-card-price">
                            @if($product->current_price < $product->price)
                            <del>{{number_format($product->price, 0, ',', '.')}}<small class="product-price-symbol">đ</small></del>
                            @endif
                            <span>{{number_format($product->current_price, 0, ',', '.')}}<small class="product-price-symbol">đ</small></span>
                        </div>
                        <div class="product-card-sold">
                            {{ $soldQuantity }} đã bán
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@foreach ($products as $product)
@include('clients.components.modals.includes.include-modals')
@endforeach
