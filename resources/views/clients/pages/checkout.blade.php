@extends('layouts.client')

@section('title','Đặt hàng')
@section('breadcrumb','Đặt hàng')

@section('content')
<div class="ltn__checkout-area mb-105">
    <div class="container">
        @php
            $hasSavedAddress = Auth::check() && count($addresses) > 0;
            $selectedDeliveryType = old('delivery_type', $hasSavedAddress ? 'account' : 'new');
            $selectedAddressId = '';
            $selectedAddress = null;

            if ($selectedDeliveryType === 'account' && $defaultAddress) {
                $selectedAddressId = old('address_id', $defaultAddress->id);
            }

            if ($hasSavedAddress) {
                foreach ($addresses as $address) {
                    if ($address->id == (int) $selectedAddressId) {
                        $selectedAddress = $address;
                    }
                }

                if (! $selectedAddress) {
                    $selectedAddress = $defaultAddress;
                }
            }

            $selectedName = '';
            $selectedPhone = '';
            $selectedAddressText = '';
            $selectedCity = '';

            if ($selectedAddress) {
                $selectedName = $selectedAddress->full_name;
                $selectedPhone = $selectedAddress->phone;
                $selectedAddressText = $selectedAddress->address;
                $selectedCity = $selectedAddress->city;
            }

            $subtotalValue = isset($subtotal) ? $subtotal : 0;
            $shippingFeeValue = isset($shippingFee) ? $shippingFee : 0;
            $discountValue = isset($discount) ? $discount : 0;
            $totalPriceValue = isset($totalPrice) ? $totalPrice : 0;
            $couponCode = $coupon ? $coupon->code : '';
        @endphp

        {{-- Form lớn duy nhất bao bọc toàn bộ trang checkout để gửi tất cả dữ liệu --}}
        <form action="{{ route('checkout.placeOrder') }}" method="POST" id="checkout-order-form">
            @csrf

            {{-- Hidden fields cập nhật qua JS --}}
            <input type="hidden" name="delivery_type" id="checkout_delivery_type"
                value="{{ $selectedDeliveryType }}">
            <input type="hidden" name="address_id" id="checkout_address_id"
                value="{{ $selectedAddressId }}">

            <div class="row checkout-main-row">
                {{-- ============================================================
                     CỘT TRÁI: Thông tin giao hàng
                ============================================================ --}}
                <div class="col-lg-6 checkout-left-column">
                    <div class="ltn__checkout-inner">
                        <div class="ltn__checkout-single-content mt-50">
                            <h4 class="title-2">Thông tin giao hàng</h4>

                            {{-- ── 2 lựa chọn giao hàng ── --}}
                            <div class="delivery-type-select mb-30">
                                {{-- Option 1: Giao đến địa chỉ tài khoản (chỉ khi đã login và có địa chỉ) --}}
                                @if($hasSavedAddress)
                                <div class="delivery-option {{ $selectedDeliveryType === 'account' ? 'active' : '' }}" id="option-account" onclick="selectDeliveryType('account')">
                                    <input type="radio" name="delivery_type_ui" id="dt_account" value="account" {{ $selectedDeliveryType === 'account' ? 'checked' : '' }}>
                                    <label for="dt_account">
                                        <i class="fas fa-user-circle"></i>
                                        <strong>Giao đến thông tin tài khoản</strong>
                                        <span>Dùng địa chỉ đã lưu trong tài khoản</span>
                                    </label>
                                </div>
                                @endif

                                {{-- Option 2: Giao đến địa chỉ khác --}}
                                <div class="delivery-option {{ $selectedDeliveryType === 'new' ? 'active' : '' }}"
                                     id="option-new" onclick="selectDeliveryType('new')">
                                    <input type="radio" name="delivery_type_ui" id="dt_new" value="new"
                                        {{ $selectedDeliveryType === 'new' ? 'checked' : '' }}>
                                    <label for="dt_new">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <strong>Giao đến người nhận / địa chỉ khác</strong>
                                        <span>Đặt cho người thân hoặc điền địa chỉ mới</span>
                                    </label>
                                </div>
                            </div>

                            {{-- ── Nội dung: Địa chỉ tài khoản ── --}}
                            @if($hasSavedAddress)
                            <div id="section-account" class="ltn__checkout-single-content-info {{ $selectedDeliveryType === 'account' ? '' : 'd-none' }}">
                                <h6>Địa chỉ giao hàng đã lưu</h6>
                                <div class="select-address mb-20">
                                    <select name="address_id_ui" id="list_address" class="input-item checkout-address-select w-100">
                                        @foreach ($addresses as $address)
                                        <option value="{{ $address->id }}"
                                            data-ship-ready="{{ $address->hasGhnLocation() ? 1 : 0 }}"
                                            {{ $selectedAddress && $address->id == $selectedAddress->id ? 'selected' : '' }}>
                                            {{ $address->full_name }} - {{ $address->address }}, {{ $address->city }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Thông tin địa chỉ đang chọn dạng Card sạch đẹp, gọn gàng ── --}}
                                <div class="address-info-box checkout-address-info p-3 mb-20">
                                    <div class="mb-2"><strong>Người nhận:</strong> <span id="show_name_text">{{ $selectedName }}</span></div>
                                    <div class="mb-2"><strong>Số điện thoại:</strong> <span id="show_phone_text">{{ $selectedPhone }}</span></div>
                                    <div><strong>Địa chỉ giao hàng:</strong> <span id="show_address_text">{{ $selectedAddressText }}{{ $selectedCity ? ', '.$selectedCity : '' }}</span></div>

                                    {{-- Giữ input hidden để phục vụ logic JS --}}
                                    <input type="hidden" id="show_name" value="{{ $selectedName }}">
                                    <input type="hidden" id="show_phone" value="{{ $selectedPhone }}">
                                    <input type="hidden" id="show_address" value="{{ $selectedAddressText }}">
                                    <input type="hidden" id="show_city" value="{{ $selectedCity }}">
                                </div>
                            </div>
                            @endif

                            {{-- ── Nội dung: Địa chỉ nhập tay ── --}}
                            <div id="section-new" class="ltn__checkout-single-content-info {{ $selectedDeliveryType === 'new' ? '' : 'd-none' }}">
                                <h6>Thông tin người nhận</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-item input-item-name ltn__custom-icon">
                                            <input type="text" id="guest_name" name="guest_name"
                                                placeholder="Họ và tên người nhận *" value="{{ old('guest_name') }}"
                                                class="checkout-input">
                                            @if ($errors->has('guest_name'))<p class="text-danger small mt-1 checkout-error checkout-error-pulled">{{ $errors->first('guest_name') }}</p>@endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-item-phone ltn__custom-icon">
                                            <input type="text" id="guest_phone" name="guest_phone"
                                                placeholder="Số điện thoại *" value="{{ old('guest_phone') }}"
                                                class="checkout-input">
                                            @if ($errors->has('guest_phone'))<p class="text-danger small mt-1 checkout-error checkout-error-pulled">{{ $errors->first('guest_phone') }}</p>@endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-item">
                                            <input type="text" id="guest_address" name="guest_address"
                                                placeholder="Số nhà, tên đường *" value="{{ old('guest_address') }}"
                                                class="checkout-input checkout-input-wide">
                                            @if ($errors->has('guest_address'))<p class="text-danger small mt-1 checkout-error checkout-error-pulled-lg">{{ $errors->first('guest_address') }}</p>@endif
                                        </div>
                                    </div>
                                </div>

                                {{-- GHN cascade: Tỉnh / Quận / Phường --}}
                                <h6 class="mt-10 checkout-section-title">Khu vực giao nhận hàng</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="nice-select-wrapper">
                                            <select id="new_province_id" name="guest_province_id" class="nice-select w-100">
                                                <option value="">Tỉnh/thành *</option>
                                            </select>
                                            <input type="hidden" id="new_province_name" name="guest_province_name">
                                        </div>
                                        @if ($errors->has('guest_province_id'))<p class="text-danger small mt-1 checkout-error">{{ $errors->first('guest_province_id') }}</p>@endif
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="nice-select-wrapper">
                                            <select id="new_district_id" name="guest_district_id" class="nice-select w-100" disabled>
                                                <option value="">Quận/huyện *</option>
                                            </select>
                                            <input type="hidden" id="new_district_name" name="guest_district_name">
                                        </div>
                                        @if ($errors->has('guest_district_id'))<p class="text-danger small mt-1 checkout-error">{{ $errors->first('guest_district_id') }}</p>@endif
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="nice-select-wrapper">
                                            <select id="new_ward_id" name="guest_ward_id" class="nice-select w-100" disabled>
                                                <option value="">Phường/xã *</option>
                                            </select>
                                            <input type="hidden" id="new_ward_name" name="guest_ward_name">
                                        </div>
                                        @if ($errors->has('guest_ward_id'))<p class="text-danger small mt-1 checkout-error">{{ $errors->first('guest_ward_id') }}</p>@endif
                                    </div>
                                </div>

                            </div>
                        </div>{{-- end ltn__checkout-single-content --}}
                    </div>{{-- end ltn__checkout-inner --}}
                </div>{{-- end col-lg-7 --}}

                {{-- ============================================================
                     CỘT PHẢI: Tổng đơn hàng + Thanh toán
                ============================================================ --}}
                <div class="col-lg-6 mt-50 checkout-right-column">
                    <div class="shoping-cart-total" id="checkout-summary"
                        data-subtotal="{{ $subtotalValue }}"
                        data-shipping-fee="{{ $shippingFeeValue }}"
                        data-discount="{{ $discountValue }}"
                        data-total="{{ $totalPriceValue }}"
                        data-shipping-url="{{ route('checkout.shippingFee') }}"
                        data-shipping-url-guest="{{ route('checkout.shippingFeeGuest') }}">
                        <h4 class="title-2 text-center">Tổng sản phẩm</h4>
                        @if(Auth::check())
                            <div id="coupon-box" class="mb-3">
                                <button type="button" class="checkout-voucher-entry"
                                    data-bs-toggle="modal" data-bs-target="#checkoutVoucherModal">
                                    <span><span class="checkout-voucher-icon">🎟</span> Voucher</span>
                                    <span class="checkout-selected-coupon">
                                        {{ $coupon ? 'Đã chọn: '.$coupon->code : 'Chọn hoặc nhập mã' }}
                                    </span>
                                </button>
                            </div>

                            <div class="modal fade checkout-voucher-modal" id="checkoutVoucherModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Voucher</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="checkout-voucher-code-row">
                                                <label for="coupon_code">Mã Voucher</label>
                                                <div class="input-group">
                                                    <input type="text" id="coupon_code" class="form-control"
                                                        value="{{ $couponCode }}" placeholder="Nhập mã giảm giá">
                                                    <div class="input-group-append">
                                                        <button type="button" id="apply-coupon-btn" class="btn btn-success">
                                                            Áp dụng
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <h5 class="mt-4 mb-2">Voucher của bạn</h5>

                                            @if(count($claimedCoupons) > 0)
                                                <div class="checkout-voucher-list">
                                                    @foreach($claimedCoupons as $claimedCoupon)
                                                        @php
                                                            $canApplyCoupon = $claimedCoupon->canUse(Auth::id(), (float) $subtotalValue);
                                                            $selectedCoupon = false;
                                                            if ($coupon && $coupon->id == $claimedCoupon->id) {
                                                                $selectedCoupon = true;
                                                            }

                                                            $discountText = rtrim(rtrim(number_format($claimedCoupon->discount_percent, 2, '.', ''), '0'), '.');

                                                            if ((float) $claimedCoupon->minimum_order_amount > 0) {
                                                                $minimumText = 'Đơn tối thiểu '.number_format($claimedCoupon->minimum_order_amount, 0, ',', '.').' đ';
                                                            } else {
                                                                $minimumText = 'Không yêu cầu đơn tối thiểu';
                                                            }

                                                            if ($claimedCoupon->max_discount_amount) {
                                                                $maxText = 'Gi?m t?i ?a '.number_format($claimedCoupon->max_discount_amount, 0, ',', '.').' ?';
                                                            } else {
                                                                $maxText = 'Kh?ng gi?i h?n gi?m t?i ?a';
                                                            }

                                                            $expiresAtText = 'Kh?ng gi?i h?n';
                                                            if ($claimedCoupon->expires_at) {
                                                                $expiresAtText = $claimedCoupon->expires_at->format('d/m/Y H:i');
                                                            }
                                                        @endphp

                                                        <button type="button"
                                                            class="checkout-voucher-item {{ $selectedCoupon ? 'active' : '' }} {{ $canApplyCoupon ? '' : 'is-disabled' }}"
                                                            data-code="{{ $claimedCoupon->code }}"
                                                            {{ $canApplyCoupon ? '' : 'disabled' }}>
                                                            <span class="checkout-voucher-ticket">
                                                                <strong>{{ $discountText }}%</strong>
                                                            </span>
                                                            <span class="checkout-voucher-info">
                                                                <strong>{{ $claimedCoupon->code }}</strong>
                                                                <small>{{ $maxText }}</small>
                                                                <small>{{ $minimumText }}</small>
                                                                <small>HSD: {{ $expiresAtText }}</small>
                                                            </span>
                                                            <span class="checkout-voucher-action">
                                                                {{ $canApplyCoupon ? ($selectedCoupon ? 'Đã chọn' : 'Áp dụng') : 'Chưa đủ điều kiện' }}
                                                            </span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-info mb-0">
                                                    Tài khoản chưa có voucher.
                                                </div>
                                            @endif

                                            <small id="coupon-message" class="d-block mt-3"></small>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở lại</button>
                                            <button type="button" id="confirm-selected-coupon" class="btn btn-success">Đồng ý</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <table class="table">
                            <tbody>
                                @foreach ($cartItems as $item)
                                @php
                                    $product = null;
                                    $quantity = 1;

                                    if (is_object($item)) {
                                        if (isset($item->product)) {
                                            $product = $item->product;
                                        }

                                        if (isset($item->quantity)) {
                                            $quantity = $item->quantity;
                                        }
                                    } else {
                                        if (isset($item['product'])) {
                                            $product = $item['product'];
                                        }

                                        if (isset($item['quantity'])) {
                                            $quantity = $item['quantity'];
                                        }
                                    }

                                    $itemSubtotal = 0;
                                    if ($product) {
                                        $itemSubtotal = $product->calculatePriceByQuantity($quantity);
                                    }
                                @endphp
                                @if($product)
                                <tr>
                                    <td>
                                        {{ $product->display_name }}
                                        <strong>× {{ $quantity }}</strong>
                                    </td>
                                    <td>{{ number_format($itemSubtotal, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @endforeach
                                <tr>
                                    <td>Phí vận chuyển</td>
                                    <td class="shippingFee_Checkout">
                                        @if($defaultAddress && $defaultAddress->hasGhnLocation())
                                            {{ number_format($shippingFee, 0, ',', '.') }} đ
                                        @else
                                            Chưa tính
                                        @endif
                                    </td>
                                </tr>
                                <tr class="checkout-shipping-message @if(!$defaultAddress || !$defaultAddress->hasGhnLocation()) @else d-none @endif">
                                    <td colspan="2" class="checkout-shipping-alert">
                                        @if(!$defaultAddress || !$defaultAddress->hasGhnLocation())
                                            Vui lòng chọn hoặc nhập đầy đủ thông tin khu vực để tính phí ship.
                                        @endif
                                    </td>
                                </tr>
                                @if(Auth::check())
                                    <tr>
                                        <td>Giảm giá</td>
                                        <td class="discount_Checkout">{{ number_format($discountValue, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Tổng tiền</strong></td>
                                    <td><strong class="totalPrice_Checkout">{{ number_format($totalPrice, 0, ',', '.') }} đ</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- ── Phương thức thanh toán ── --}}
                    <div class="ltn__checkout-payment-method mt-50">
                        <h4 class="title-2 text-center checkout-payment-title">Phương thức thanh toán</h4>
                        <div id="checkout_payment" class="small-payment-methods">
                            @if(Auth::check())
                            <div class="card mb-2 checkout-payment-card checkout-cod-card {{ $selectedDeliveryType === 'new' ? 'd-none' : '' }}">
                                <h5 class="ltn__card-title checkout-payment-heading">
                                    <input type="radio" name="payment_method" value="cash" id="payment_cod"
                                        {{ $selectedDeliveryType === 'account' ? 'checked' : '' }} class="checkout-payment-radio">
                                    <label for="payment_cod" class="checkout-payment-label">
                                        Thanh toán khi nhận hàng
                                        <img src="{{ asset('assets/clients/img/icons/cash.png') }}" class="checkout-payment-icon checkout-payment-icon-cash">
                                    </label>
                                </h5>
                            </div>
                            @endif

                            <div class="card mb-2 checkout-payment-card">
                                <h5 class="collapsed ltn__card-title checkout-payment-heading">
                                    <input type="radio" name="payment_method" value="paypal"
                                        id="payment_paypal" {{ !Auth::check() || $selectedDeliveryType === 'new' ? 'checked' : '' }} class="checkout-payment-radio">
                                    <label for="payment_paypal" class="checkout-payment-label">
                                        PayPal
                                        <img src="{{ asset('assets/clients/img/icons/payment-3.png') }}" class="checkout-payment-icon checkout-payment-icon-paypal">
                                    </label>
                                </h5>
                            </div>
                        </div>

                        <div class="alert alert-info mt-2 checkout-paypal-only-alert {{ !Auth::check() || $selectedDeliveryType === 'new' ? '' : 'd-none' }}">
                            <i class="fas fa-lock"></i>
                            Khách vãng lai hoặc đơn giao đến địa chỉ khác chỉ thanh toán qua <strong>PayPal</strong>.
                            Thông tin người nhận chỉ lưu trong đơn hàng để giao, không lưu vào sổ địa chỉ tài khoản.
                        </div>

                        <div class="ltn__payment-note mt-15 mb-15 checkout-payment-note">
                        </div>

                        @if(Auth::check())
                        <button class="btn theme-btn-1 btn-effect-1 text-uppercase w-100 checkout-order-button" type="submit" id="order_button_cash">
                            Đặt hàng
                        </button>
                        @endif
                        <div id="paypal-button-container" class="mt-2"></div>
                    </div>
                </div>{{-- end col-lg-5 --}}
            </div>{{-- end row --}}
        </form>
    </div>
</div>

@endsection
