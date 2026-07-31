@extends('layouts.client')

@section('title', 'Giỏ hàng')
@section('breadcrumb', 'Giỏ hàng')

@section('content')
<div class="liton__shoping-cart-area mb-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="shoping-cart-inner">
                    <div class="shoping-cart-table table-responsive">
                        <table class="table">
                            <tbody>
                                @forelse ($sanPhamGioHangs as $sanPhamGioHang)
                                    <tr>
                                        <td class="cart-product-remove">
                                            <button type="button" class="remove-from-cart" data-id="{{ $sanPhamGioHang['ma_san_pham'] }}">x</button>
                                        </td>
                                        <td class="cart-product-image">
                                            <img src="{{ $sanPhamGioHang['hinh_anh'] }}" alt="{{ $sanPhamGioHang['ten'] }}">
                                        </td>
                                        <td class="cart-product-info">
                                            <h5>{{ $sanPhamGioHang['ten'] }}</h5>
                                        </td>
                                        <td class="cart-product-price">
                                            {{ number_format($sanPhamGioHang['gia'], 0, ',', '.') }}<small>đ</small>
                                        </td>
                                        <td class="cart-product-quantity">
                                            <div class="cart-plus-minus">
                                                <button type="button" class="dec qtybutton">-</button>
                                                <input type="text"
                                                    value="{{ $sanPhamGioHang['so_luong'] }}"
                                                    class="cart-plus-minus-box"
                                                    readonly
                                                    data-max="{{ $sanPhamGioHang['ton_kho'] }}"
                                                    data-id="{{ $sanPhamGioHang['ma_san_pham'] }}">
                                                <button type="button" class="inc qtybutton">+</button>
                                            </div>
                                        </td>
                                        <td class="cart-product-subtotal">
                                            {{ number_format($sanPhamGioHang['tam_tinh'], 0, ',', '.') }}<small>đ</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Giỏ hàng của bạn đang trống</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (count($sanPhamGioHangs) > 0)
                        <div class="shoping-cart-total mt-50">
                            <h4>Tổng giỏ hàng</h4>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Tổng tiền hàng</td>
                                        <td><span class="cart-total">{{ number_format($tongTienGioHang, 0, ',', '.') }}<small>đ</small></span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="btn-wrapper text-right text-end">
                                <a href="{{ route('thanh-toan.hien-thi') }}" class="theme-btn-1 btn btn-effect-1">Thanh toán</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection