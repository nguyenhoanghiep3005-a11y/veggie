<div class="ltn__product-tab-content-inner ltn__product-grid-view">
    <div class="row">
        @foreach ($products as $product)
        <div class="col-xl-4 col-sm-6 col-6">
            <div class="ltn__product-item ltn__product-item-3 text-center">
                <div class="product-img">
                    <a href="{{route('product.detail',$product->slug)}}">
                        <img src="{{$product->image_url}}" alt="{{$product->name}}"></a>
                    <div class="product-hover-action">
                        <ul>
                            <li>
                                <a href="javescript:void(0)" title="Xem nhanh" data-bs-toggle="modal"
                                    data-bs-target="#quick_view_modal-{{$product->id}}">
                                    <i class="far fa-eye"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javescript:void(0)" title="Thêm vào giỏ hàng" class="add-to-cart-btn"
                                    data-id="{{$product->id}}">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javescript:void(0)" title="Yêu thích" class="add-to-wishlist"
                                    data-id="{{$product->id}}">
                                    <i class="far fa-heart"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="product-info">
                    <div class="product-ratting">
                        <ul>
                            @php
                            $avgRating = $product->reviews->avg('rating') ?? 0; // điểm trung bình
                            $totalReviews = $product->reviews->count(); // tổng số đánh giá
                            @endphp

                            <div class="product-ratting">
                                <ul>
                                    @for ($i = 1; $i <= 5; $i++) <li>
                                        <a href="javascript:void(0)">
                                            <i class="{{ $i <= $avgRating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                        </a>
                                        </li>
                                        @endfor

                                        <li class="review-total">
                                            ({{$totalReviews}} Đánh giá)
                                        </li>
                                </ul>
                            </div>
                        </ul>
                    </div>
                    <h2 class="product-title"><a
                            href="{{route('product.detail',$product->slug)}}">{{$product->name}}</a></h2>
                    <div class="product-price">
                        <span>{{number_format($product->current_price , 0 , ',',".")}}VND</span>

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
