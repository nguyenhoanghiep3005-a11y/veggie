<div class="ltn__utilize-menu-head">
    <span class="ltn__utilize-menu-title">Giỏ hàng</span>
    <button class="ltn__utilize-close">x</button>
</div>

<div class="mini-cart-product-area ltn__scrollbar">
    @if (!empty($cartItems) && count($cartItems) > 0)
        <ul class="mini-cart-list">
            @foreach ($cartItems as $item)
                <li class="mini-cart-item d-flex mb-2 align-items-center" data-product-id="{{ $item->product_id }}">
                    <img src="{{ $item->image }}" class="mini-cart-item-image" alt="{{ $item->name }}">

                    <div class="mini-cart-item-info flex-grow-1">
                        <h6>{{ $item->name }}</h6>
                        <span class="mini-cart-item-total">{{ number_format($item->subtotal, 0, ',', '.') }}đ</span>

                        <div class="mini-cart-quantity">
                            <button type="button" class="mini-cart-qty-btn" data-action="decrease" data-product-id="{{ $item->product_id }}">-</button>
                            <input type="text"
                                class="mini-cart-qty-input"
                                value="{{ $item->quantity }}"
                                readonly
                                data-product-id="{{ $item->product_id }}"
                                data-max="{{ $item->stock }}">
                            <button type="button" class="mini-cart-qty-btn" data-action="increase" data-product-id="{{ $item->product_id }}">+</button>
                        </div>
                    </div>

                    <button type="button" class="remove-from-cart-btn mini-cart-remove-button" data-product-id="{{ $item->product_id }}" title="Xóa sản phẩm">
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
        <h5>Tổng tiền: <span>{{ number_format($cartTotal ?? 0, 0, ',', '.') }}đ</span></h5>
    </div>
    <div class="btn-wrapper">
        <a href="{{ route('cart.index') }}" class="theme-btn-1 btn btn-effect-1">Xem giỏ hàng</a>
        <a href="{{ route('checkout') }}" class="theme-btn-2 btn btn-effect-2">Thanh toán</a>
    </div>
</div>
