<div class="ltn__product-tab-content-inner ltn__product-grid-view">
    <div class="row">
        @forelse ($sanPhams as $sanPham)
            <div class="{{ !empty($kichThuocNho) ? 'col-xl-3 col-lg-3 col-sm-6 col-6' : 'col-xl-4 col-sm-6 col-6' }}">
                <div class="ltn__product-item ltn__product-item-3 text-center">
                    <div class="product-img">
                        <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">
                            <img
                            src="{{ $sanPham->duong_dan_hinh_anh }}"
                            alt="{{ $sanPham->ten_hien_thi }}">
                        </a>
                    </div>

                    <div class="product-info">
                        <div class="product-ratting">
                            <ul>
                                @for ($soSao = 1; $soSao <= 5; $soSao++)
                                    <li>
                                        <i class="{{ $soSao <= $sanPham->so_sao_trung_binh ? 'fas fa-star' : 'far fa-star' }}"></i>
                                    </li>
                                @endfor
                                <li class="review-total">
                                    ({{ $sanPham->tong_danh_gia }} đánh giá)
                                </li>
                            </ul>
                        </div>

                        <h2 class="product-title">
                            <a href="{{ route('san-pham.chi-tiet', $sanPham->duong_dan) }}">
                                {{ $sanPham->ten_hien_thi }}
                            </a>
                        </h2>

                        <div
                        class="product-card-bottom"
                        style="display:flex !important; align-items:flex-end !important; justify-content:space-between !important; gap:10px !important; margin-top:10px !important; text-align:left !important;">
                        <div
                        class="product-card-price"
                        style="display:flex !important; flex-direction:column !important; align-items:flex-start !important; line-height:1.3 !important;">
                        @if ($sanPham->gia_hien_tai < $sanPham->gia)
                            <del style="color:#999 !important; font-size:16px !important; font-weight:400 !important;">
                                {{ number_format($sanPham->gia, 0, ',', '.') }}<small class="product-price-symbol"
                                 style="margin-left:2px !important; font-size:55% !important; font-weight:400 !important; vertical-align:baseline !important; text-decoration:underline !important;">&#273;</small>
                            </del>
                        @endif
                        <span style="color:#80B500 !important; font-size:20px !important; font-weight:600 !important;">
                            {{ number_format($sanPham->gia_hien_tai, 0, ',', '.') }}<small class="product-price-symbol"
                            style="margin-left:2px !important; font-size:55% !important; font-weight:400 !important; vertical-align:baseline !important; text-decoration:underline !important;">&#273;</small>
                        </span>
                    </div>

                    <div
                    class="product-card-sold"
                    style="color:#111 !important; font-size:14px !important; white-space:nowrap !important;">
                    {{ $sanPham->so_luong_da_ban }} đã bán
                </div>
            </div>

            <div
            class="product-card-actions"
            style="display:flex !important; align-items:center !important; justify-content:center !important; gap:10px !important; margin-top:14px !important; width:100% !important;">
            @auth
                <a
                href="javascript:void(0)"
                class="product-wishlist-btn add-to-wishlist"
                data-id="{{ $sanPham->ma_san_pham }}"
                title="Yêu thích"
                style="width:34px !important; height:42px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; color:#80B500 !important; font-size:24px !important; line-height:1 !important;">
                <i class="far fa-heart"></i>
            </a>
        @endauth

        <a
        href="javascript:void(0)"
        class="product-buy-now-btn add-to-cart-btn buy-now-btn"
        data-id="{{ $sanPham->ma_san_pham }}"
        style="height:42px !important; flex:1 1 auto !important; max-width:178px !important; min-width:0 !important; padding:0 16px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; background:#80B500 !important; border:1px solid #80B500 !important; color:#fff !important; font-size:15px !important; font-weight:600 !important; line-height:1 !important;">
        Mua ngay
    </a>

    <a
    href="javascript:void(0)"
    class="product-cart-btn add-to-cart-btn"
    data-id="{{ $sanPham->ma_san_pham }}"
    title="Thêm vào giỏ hàng"
    style="width:50px !important; height:42px !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; background:#80B500 !important; border:1px solid #80B500 !important; color:#fff !important; font-size:20px !important; line-height:1 !important;">
    <i class="fas fa-shopping-cart"></i>
</a>
</div>
</div>
</div>
</div>
@empty
    <div class="col-12 text-center">
        Không tìm thấy sản phẩm.
    </div>
@endforelse
</div>
</div>
