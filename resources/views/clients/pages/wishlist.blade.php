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
                                <tr>
                                    <td class="wishlist-product-remove" data-id="{{$item->product->id}}">
                                        x
                                    </td>
                                    <td class="cart-product-image">
                                        <a href="{{route('product.detail', $item->product->slug)}}">
                                            <img src="{{ $item->product->image_url}}" alt="Sản phẩm"></a>
                                    </td>
                                    <td class="wishlist-product-info">
                                        <h5><a href="{{route('product.detail', $item->product->slug)}}">{{$item->product->name}}
                                        </h5>
                                    </td>
                                    </td>
                                    <td class="{{route('product.detail', $item->product->slug)}}">
                                        {{number_format($item->product->current_price , 0 , ',',".")}}đ</td>
                                    <td class="wishlist-product-stock">
                                        {{$item->product->stock > 0 ? "Còn hàng" : "Hết hàng"}}
                                    </td>
                                    <td>
                                        <a href="{{route('product.detail', $item->product->slug)}}" class="submit-button-1 " title="Thêm vào giỏ hàng">
                                            <span>Thêm vào giỏ hàng</span>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Danh sách yêu thích của bạn đang trống</td>
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
