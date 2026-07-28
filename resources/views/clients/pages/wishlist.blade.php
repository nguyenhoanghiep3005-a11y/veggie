@extends('layouts.client')

@section('title','Yêu thích')
@section('breadcrumb','Yêu thích')
@section('content')
<div class="liton__shoping-cart-area mb-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="shoping-cart-inner">
                    <div class="shoping-cart-table table-responsive">
                        <table class="table">
                            <tbody>
                                @forelse ($wishlist as $item)
                                    @php($product = $item->product)
                                    <tr class="wishlist-row">
                                        <td class="wishlist-product-remove" data-id="{{ $product->id }}" role="button" title="Xóa sản phẩm">
                                            x
                                        </td>
                                        <td class="cart-product-image">
                                            <a href="{{ route('product.detail', $product->slug) }}">
                                                <img src="{{ $product->image_url }}" alt="{{ $product->display_name }}">
                                            </a>
                                        </td>
                                        <td class="wishlist-product-info">
                                            <h5>
                                                <a href="{{ route('product.detail', $product->slug) }}">
                                                    {{ $product->display_name }}
                                                </a>
                                            </h5>
                                        </td>
                                        <td class="cart-product-price">
                                            {{ number_format($product->current_price, 0, ',', '.') }}đ
                                        </td>
                                        <td class="wishlist-product-stock">
                                            @if ($product->sellableStock() > 0)
                                                <span class="badge bg-success">Còn hàng</span>
                                            @else
                                                <span class="badge bg-danger">Hết hàng</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('product.detail', $product->slug) }}" class="submit-button-1" title="Xem sản phẩm">
                                                <span>Xem sản phẩm</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="wishlist-empty-row">
                                        <td colspan="6" class="text-center">Danh sách yêu thích của bạn đang trống.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection