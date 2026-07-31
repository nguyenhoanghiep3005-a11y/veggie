@extends('layouts.client')

@section('title', 'Chi tiết sản phẩm')
@section('breadcrumb', 'Chi tiết sản phẩm')

@section('content')
<div class="ltn__shop-details-area pb-85">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="ltn__shop-details-img-gallery">
                    <div class="ltn__shop-details-large-img">
                        @foreach ($sanPham->cac_hinh_anh_chi_tiet as $duongDanHinhAnh)
                            <div class="single-large-img">
                                <img src="{{ $duongDanHinhAnh }}" alt="{{ $sanPham->ten_hien_thi }}" class="product-detail-image">
                            </div>
                        @endforeach
                    </div>
                    <div class="ltn__shop-details-small-img slick-arrow-2">
                        @foreach ($sanPham->cac_hinh_anh_chi_tiet as $duongDanHinhAnh)
                            <div class="single-small-img">
                                <img src="{{ $duongDanHinhAnh }}" alt="{{ $sanPham->ten_hien_thi }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="modal-product-info shop-details-info pl-0">
                    <h3 class="product-detail-name">{{ $sanPham->ten_hien_thi }}</h3>

                    <div class="product-detail-rating-line">
                        <span class="product-detail-rating-number">{{ number_format($sanPham->so_sao_trung_binh, 1) }}</span>
                        <span class="product-ratting">
                            @for ($soSao = 1; $soSao <= 5; $soSao++)
                                <i class="{{ $soSao <= $sanPham->so_sao_trung_binh ? 'fas fa-star' : 'far fa-star' }}"></i>
                            @endfor
                        </span>
                        <span class="product-detail-review-count">{{ $sanPham->tong_danh_gia }} đánh giá</span>
                        <span class="product-detail-sold">Đã bán {{ $sanPham->so_luong_da_ban }}</span>
                    </div>

                    <div class="product-price product-detail-price-wrap">
                        @if ($sanPham->gia_hien_tai < $sanPham->gia)
                            <del class="product-detail-old-price">{{ number_format($sanPham->gia, 0, ',', '.') }}<small class="product-price-symbol">đ</small></del>
                        @else
                            <del class="product-detail-old-price" style="display:none"></del>
                        @endif
                        <span class="product-detail-price">{{ number_format($sanPham->gia_hien_tai, 0, ',', '.') }}<small class="product-price-symbol">đ</small></span>
                    </div>
                    @if (count($bienTheSanPhams) > 0)
                        <div class="product-variant-selector mt-3">
                            <strong>Đơn vị</strong>
                            <div class="product-variant-list product-variant-options mt-2">
                                @foreach ($bienTheSanPhams as $bienTheSanPham)
                                    <a href="{{ route('san-pham.chi-tiet', $bienTheSanPham->duong_dan) }}"
                                        data-variant-url="{{ route('san-pham.bien-the', $bienTheSanPham->duong_dan) }}"
                                        class="product-variant-option {{ $bienTheSanPham->dang_duoc_chon ? 'active' : '' }} {{ $bienTheSanPham->soLuongCoTheBan() <= 0 ? 'out-of-stock' : '' }}">
                                        <span>{{ $bienTheSanPham->ten_bien_the }}</span>
                                        <small>
                                            {{ $bienTheSanPham->soLuongCoTheBan() > 0 ? 'Còn '.$bienTheSanPham->soLuongCoTheBan() : 'Hết hàng' }}
                                        </small>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="ltn__product-details-menu-2 mt-20">
                        <ul>
                            <li>
                                <div class="cart-plus-minus">
                                    <div class="dec qtybutton">-</div>
                                    <input type="text" value="{{ $sanPham->soLuongCoTheBan() > 0 ? 1 : 0 }}"
                                        class="cart-plus-minus-box" readonly data-max="{{ $sanPham->soLuongCoTheBan() }}">
                                    <div class="inc qtybutton">+</div>
                                </div>
                            </li>
                            <li class="product-detail-cart-action">
                                @if ($sanPham->soLuongCoTheBan() > 0)
                                    <a href="javascript:void(0)" class="theme-btn-1 btn btn-effect-1 add-to-cart-btn"
                                        data-id="{{ $sanPham->ma_san_pham }}">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>Thêm vào giỏ hàng</span>
                                    </a>
                                @else
                                    <span class="theme-btn-1 btn btn-effect-1 product-action-disabled">Hết hàng</span>
                                @endif
                            </li>
                            @auth
                            <li>
                                <a href="javascript:void(0)" class="product-detail-wishlist add-to-wishlist"
                                    title="Yêu thích" data-id="{{ $sanPham->ma_san_pham }}">
                                    <i class="far fa-heart"></i>
                                </a>
                            </li>
                            @endauth
                        </ul>
                    </div>

                    <hr>
                    <div class="ltn__safe-checkout">
                        <h5>Có thể thanh toán</h5>
                        <img src="{{ asset('assets/clients/img/icons/payment-2.png') }}" alt="Phương thức thanh toán">
                    </div>

                </div>
            </div>
        </div>

        <div class="ltn__shop-details-tab-inner ltn__shop-details-tab-inner-2 mt-60">
            <div class="ltn__shop-details-tab-menu">
                <div class="nav">
                    <a class="active show" data-bs-toggle="tab" href="#tab-mo-ta-san-pham">Mô tả</a>
                    <a data-bs-toggle="tab" href="#tab-danh-gia-san-pham">Đánh giá</a>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade active show" id="tab-mo-ta-san-pham">
                    <div class="ltn__shop-details-tab-content-inner">
                        <h4 class="title-2">Thông tin chi tiết sản phẩm</h4>
                        <div class="product-detail-facts">
                            <p class="product-description-text">{{ $sanPham->mo_ta_hien_thi }}</p>
                            <ul>
                                <li class="product-manufacture-row {{ $sanPham->san_xuat_hien_thi == 'Đang cập nhật' ? 'd-none' : '' }}">
                                    <strong>Nơi sản xuất:</strong>
                                    <span class="product-manufacture-text">{{ $sanPham->san_xuat_hien_thi }}</span>
                                </li>
                                <li class="product-brand-row {{ $sanPham->thuong_hieu_hien_thi == 'Đang cập nhật' ? 'd-none' : '' }}">
                                    <strong>Thương hiệu:</strong>
                                    <span class="product-brand-text">{{ $sanPham->thuong_hieu_hien_thi }}</span>
                                </li>
                                <li class="product-storage-row {{ $sanPham->bao_quan_hien_thi == 'Đang cập nhật' ? 'd-none' : '' }}">
                                    <strong>Bảo quản:</strong>
                                    <span class="product-storage-text">{{ $sanPham->bao_quan_hien_thi }}</span>
                                </li>
                                <li class="product-use-row {{ $sanPham->cach_dung_hien_thi == 'Đang cập nhật' ? 'd-none' : '' }}">
                                    <strong>Cách dùng:</strong>
                                    <span class="product-use-text">{{ $sanPham->cach_dung_hien_thi }}</span>
                                </li>
                                <li class="product-ingredients-row {{ $sanPham->thanh_phan_hien_thi == 'Đang cập nhật' ? 'd-none' : '' }}">
                                    <strong>Thành phần:</strong>
                                    <span class="product-ingredients-text">{{ $sanPham->thanh_phan_hien_thi }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-danh-gia-san-pham">
                    <div class="ltn__shop-details-tab-content-inner">
                        <div class="ltn__comment-area mb-30">
                            <h4 class="title-2">Đánh giá sản phẩm</h4>
                            <div class="ltn__comment-inner" id="product-review-list">
                                @include('clients.components.modals.includes.danh-sach-danh-gia', ['sanPham' => $sanPham])
                            </div>
                        </div>

                        @if (Auth::check())
                            <form id="review-form" data-product-id="{{ $sanPham->ma_san_pham }}">
                                <div class="product-ratting mb-3">
                                    @for ($soSao = 1; $soSao <= 5; $soSao++)
                                        <a href="javascript:void(0)" class="so_sao-star" data-value="{{ $soSao }}">
                                            <i class="far fa-star"></i>
                                        </a>
                                    @endfor
                                </div>
                                <input type="hidden" name="so_sao" id="rating-value" value="0">
                                <textarea name="binh_luan" id="review-content" placeholder="Nhập đánh giá của bạn"></textarea>
                                <button type="submit" class="btn theme-btn-1 btn-effect-1 mt-3">Gửi đánh giá</button>
                            </form>
                        @else
                            <p><a href="{{ route('dang-nhap.hien-thi') }}">Đăng nhập</a> để đánh giá sản phẩm.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="ltn__product-area mt-80">
            <h3>Sản phẩm liên quan</h3>
            @include('clients.components.luoi-san-pham', ['sanPhams' => $sanPhamLienQuans])
            <div class="ltn__pagination-area text-center">
                {{ $sanPhamLienQuans->links('clients.components.pagination.phan-trang') }}
            </div>
        </div>
    </div>
</div>

@include('clients.components.modals.them-gio-hang', ['sanPham' => $sanPham])
@include('clients.components.modals.yeu-thich', ['sanPham' => $sanPham])
@endsection
