@extends('layouts.client')

@section('title', 'Yêu thích')
@section('breadcrumb', 'Yêu thích')

@section('content')
<div class="liton__shoping-cart-area mb-120">
    <div class="container">
        <div class="shoping-cart-inner">
            <div class="shoping-cart-table table-responsive">
                <table class="table">
                    <tbody>
                        @forelse($sanPhamYeuThichs as $sanPhamYeuThich)
                            <tr class="wishlist-row">
                                <td class="wishlist-product-remove" data-id="{{ $sanPhamYeuThich->sanPham->ma_san_pham }}" role="button" title="Xóa sản phẩm">x</td>
                                <td class="cart-product-image">
                                    <a href="{{ route('san-pham.chi-tiet', $sanPhamYeuThich->sanPham->duong_dan) }}">
                                        <img src="{{ $sanPhamYeuThich->sanPham->duong_dan_hinh_anh }}" alt="{{ $sanPhamYeuThich->sanPham->ten_hien_thi }}">
                                    </a>
                                </td>
                                <td class="wishlist-product-info">
                                    <h5><a href="{{ route('san-pham.chi-tiet', $sanPhamYeuThich->sanPham->duong_dan) }}">{{ $sanPhamYeuThich->sanPham->ten_hien_thi }}</a></h5>
                                </td>
                                <td class="cart-product-price">{{ number_format($sanPhamYeuThich->sanPham->gia_hien_tai, 0, ',', '.') }}đ</td>
                                <td class="wishlist-product-stock">
                                    @if($sanPhamYeuThich->sanPham->soLuongCoTheBan() > 0)
                                        <span class="badge bg-success">Còn hàng</span>
                                    @else
                                        <span class="badge bg-danger">Hết hàng</span>
                                    @endif
                                </td>
                                <td><a href="{{ route('san-pham.chi-tiet', $sanPhamYeuThich->sanPham->duong_dan) }}" class="submit-button-1">Xem sản phẩm</a></td>
                            </tr>
                        @empty
                            <tr class="wishlist-empty-row"><td colspan="6" class="text-center">Danh sách yêu thích đang trống.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection