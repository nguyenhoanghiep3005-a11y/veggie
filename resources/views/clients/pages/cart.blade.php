@extends('layouts.client')

@section('title', 'Giỏ hàng')
@section('breadcrumb', 'Giỏ hàng')

@section('content')
<!-- SHOPING CART AREA START -->
<div class="liton__shoping-cart-area mb-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="shoping-cart-inner">
                    <div class="shoping-cart-table table-responsive">
                        <table class="table">
                            <tbody>
                                @if (!empty($cartItems) && count($cartItems) > 0)
                                    @foreach ($cartItems as $item)
                                        <tr>
                                            <td class="cart-product-remove">
                                                <button type="button" class="remove-from-cart" data-id="{{ $item['product_id'] }}">x</button>
                                            </td>
                                            <td class="cart-product-image">
                                                <a href="javascript:void(0)">
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                                </a>
                                            </td>
                                            <td class="cart-product-info">
                                                <h5>{{ $item['name'] }}</h5>
                                            </td>
                                            <td class="cart-product-price">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                                            <td class="cart-product-quantity">
                                                <div class="cart-plus-minus">
                                                    <input type="text"
                                                        value="{{ $item['quantity'] }}"
                                                        name="qtybutton"
                                                        class="cart-plus-minus-box"
                                                        readonly
                                                        data-max="{{ $item['stock'] }}"
                                                        data-id="{{ $item['product_id'] }}">
                                                </div>
                                            </td>
                                            <td class="cart-product-subtotal">{{ number_format($item['subtotal'], 0, ',', '.') }}đ</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">Giỏ hàng của bạn đang trống</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if (!empty($cartItems) && count($cartItems) > 0)
                        <div class="shoping-cart-total mt-50">
                            <h4>Tổng giỏ hàng</h4>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Tổng tiền hàng</td>
                                        <td><span class="cart-total">{{ number_format($cartTotal, 0, ',', '.') }}đ</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="btn-wrapper text-right text-end">
                                <a href="{{ route('checkout') }}" class="theme-btn-1 btn btn-effect-1">Thanh toán</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- SHOPING CART AREA END -->
@endsection
