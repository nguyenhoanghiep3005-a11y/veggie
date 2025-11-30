<div class="ltn__utilize-menu-head">
    <span class="ltn__utilize-menu-title">Giỏ hàng</span>
    <button class="ltn__utilize-close">×</button>
</div>
<div class="mini-cart-product-area ltn__scrollbar">
    @php $total = 0; @endphp

    @if(!empty($cartItems) && count($cartItems) > 0)
    <ul class="mini-cart-list">
        @foreach($cartItems as $item)

        @php
        // Trường hợp đăng nhập: $item là model CartItem
        // Trường hợp chưa đăng nhập: $item là stdClass
        $product = $item->product ?? null;
        $quantity = $item->quantity ?? 1;
        @endphp

        @if($product)
        @php
        $price = $product->price ?? 0;
        $name = $product->name ?? 'Sản phẩm';
        $image = $product->image_url
        ? asset( $product->image_url)
        : asset('storage/uploads/products/product_default.png');
        $subtotal = $price * $quantity;
        $total += $subtotal;
        @endphp
        <li class="mini-cart-item d-flex mb-2 align-items-center">
            <img src="{{ $image }}" style="width:60px;height:60px;object-fit:cover;margin-right:10px;">
            <div class="flex-grow-1">
                <h6>{{ $name }}</h6>
                <span>{{ number_format($price, 0, ',', '.') }}đ x {{ $quantity }}</span>
            </div>
            {{-- NÚT XÓA SẢN PHẨM --}}
            <button class="remove-from-cart-btn" data-product-id="{{ $product->id }}"
                style="background: none; border: none; color: #ff3c3c; font-size: 18px; line-height: 1;">
                <i class="fa fa-times"></i>
            </button>
        </li>
        @endif

        @endforeach
    </ul>
    @else
    <p class="text-center p-3 mb-0">Giỏ hàng trống</p>
    @endif

</div>
<div class="mini-cart-footer">
    <div class="mini-cart-sub-total">
        <h5>Tổng tiền: <span>{{number_format($total, 0, ',', '.')}} VNĐ</span></h5>
    </div>
    <div class="btn-wrapper">
        {{-- Sửa link tạm thời từ {{ route('cart.index') }} sang route products.index hoặc route cart thực tế --}}
        <a href="{{ route('cart.index') }}" class="theme-btn-1 btn btn-effect-1">Xem giỏ hàng</a> 
        <a href="{{ route('cart.index') }}" class="theme-btn-2 btn btn-effect-2">Thanh toán</a>
    </div>
</div>