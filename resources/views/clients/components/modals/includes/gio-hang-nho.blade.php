<div class="ltn__utilize-menu-head">
    <span class="ltn__utilize-menu-title">Giỏ hàng</span>
    <button class="ltn__utilize-close">x</button>
</div>

<div class="mini-cart-product-area ltn__scrollbar">
    @if (count($sanPhamGioHangs) > 0)
        <ul class="mini-cart-list">
            @foreach ($sanPhamGioHangs as $sanPhamGioHang)
                <li class="mini-cart-item d-flex mb-2 align-items-center" data-product-id="{{ $sanPhamGioHang['ma_san_pham'] }}">
                    <img src="{{ $sanPhamGioHang['hinh_anh'] }}" class="mini-cart-item-image" alt="{{ $sanPhamGioHang['ten'] }}">

                    <div class="mini-cart-item-info flex-grow-1">
                        <h6>{{ $sanPhamGioHang['ten'] }}</h6>
                        <span class="mini-cart-item-total">{{ number_format($sanPhamGioHang['tam_tinh'], 0, ',', '.') }}<small>đ</small></span>

                        <div class="mini-cart-quantity">
                            <button type="button" class="mini-cart-qty-btn" data-action="decrease" data-product-id="{{ $sanPhamGioHang['ma_san_pham'] }}">-</button>
                            <input type="text"
                            class="mini-cart-qty-input"
                            value="{{ $sanPhamGioHang['so_luong'] }}"
                            readonly
                            data-product-id="{{ $sanPhamGioHang['ma_san_pham'] }}"
                            data-max="{{ $sanPhamGioHang['ton_kho'] }}">
                            <button type="button" class="mini-cart-qty-btn" data-action="increase" data-product-id="{{ $sanPhamGioHang['ma_san_pham'] }}">+</button>
                        </div>
                    </div>

                    <button type="button" class="remove-from-cart-btn mini-cart-remove-button" data-product-id="{{ $sanPhamGioHang['ma_san_pham'] }}" title="Xóa sản phẩm">
                        <i class="fa fa-times"></i>
                    </button>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-center p-3 mb-0">Giỏ hàng trống</p>
    @endif
</div>

<div class="mini-cart-footer">
    <div class="mini-cart-sub-total">
        <h5>Tổng tiền: <span class="cart-total">{{ number_format($tongTienGioHang, 0, ',', '.') }}<small>đ</small></span></h5>
    </div>
    <div class="btn-wrapper ">
        <a href="{{ route('thanh-toan.hien-thi') }}" class="theme-btn-2 btn btn-effect-2">Thanh toán</a>
    </div>
</div>
