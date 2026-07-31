<div class="ltn__product-tab-content-inner ltn__product-grid-view">
    <div class="row">
        @forelse ($sanPhams as $sanPham)
            <div class="col-xl-4 col-sm-6 col-6">
                <div class="ltn__product-item ltn__product-item-3 text-center">
                    <div class="product-img">
                        <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">
                            <img src="{{ $sanPham->duong_dan_hinh_anh }}" alt="{{ $sanPham->ten_hien_thi }}">
                        </a>
                        <div class="product-hover-action">
                            <ul>
                                <li><a href="javascript:void(0)" title="Xem nhanh" data-bs-toggle="modal" data-bs-target="#quick_view_modal-{{ $sanPham->ma_san_pham }}"><i class="far fa-eye"></i></a></li>
                                <li><a href="javascript:void(0)" title="Thêm vào giỏ hàng" class="add-to-cart-btn" data-id="{{ $sanPham->ma_san_pham }}"><i class="fas fa-shopping-cart"></i></a></li>
                                @auth
                                <li><a href="javascript:void(0)" title="Yêu thích" class="add-to-wishlist" data-id="{{ $sanPham->ma_san_pham }}"><i class="far fa-heart"></i></a></li>
                                @endauth
                            </ul>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-ratting">
                            <ul>
                                @for ($soSao = 1; $soSao <= 5; $soSao++)
                                    <li><i class="{{ $soSao <= $sanPham->so_sao_trung_binh ? 'fas fa-star' : 'far fa-star' }}"></i></li>
                                @endfor
                                <li class="review-total">({{ $sanPham->tong_danh_gia }} đánh giá)</li>
                            </ul>
                        </div>
                        <h2 class="product-title">
                            <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">{{ $sanPham->ten_hien_thi }}</a>
                        </h2>
                        <div class="product-card-bottom">
                            <div class="product-card-price">
                                @if ($sanPham->gia_hien_tai < $sanPham->gia)
                                    <del>{{ number_format($sanPham->gia, 0, ',', '.') }}<small class="product-price-symbol">đ</small></del>
                                @endif
                                <span>{{ number_format($sanPham->gia_hien_tai, 0, ',', '.') }}<small class="product-price-symbol">đ</small></span>
                            </div>
                            <div class="product-card-sold">{{ $sanPham->so_luong_da_ban }} đã bán</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">Không tìm thấy sản phẩm.</div>
        @endforelse
    </div>
</div>

@foreach ($sanPhams as $sanPham)
    @include('clients.components.modals.includes.noi-dung-modal')
@endforeach
