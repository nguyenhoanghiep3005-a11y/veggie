@extends('layouts.client')

@section('title', 'Tìm kiếm')
@section('breadcrumb', 'Tìm kiếm')

@section('content')
<style>
    /* CSS rieng trang ket qua tim kiem. */
    .category-filter.active {
        color: var(--client-primary);
        font-weight: bold;
        text-decoration: underline;
    }
</style>
<div class="ltn__product-area ltn__product-gutter">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 order-lg-2 mb-120">
                <h3>Kết quả tìm kiếm cho "{{ $tuKhoa }}"</h3>

                @include('clients.components.luoi-san-pham', ['sanPhams' => $sanPhams])

                <div class="ltn__pagination-area text-center">
                    <div class="ltn__pagination">
                        {{ $sanPhams->links('clients.components.pagination.phan-trang') }}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-120">
                <aside class="sidebar ltn__shop-sidebar">
                    <div class="widget ltn__menu-widget">
                        <h4 class="ltn__widget-title ltn__widget-title-border">Danh mục</h4>
                        <ul>
                            @foreach ($danhMucs as $danhMuc)
                                <li>
                                    <a href="{{ route('san-pham.danh-sach', ['ma_danh_muc' => $danhMuc->ma_danh_muc]) }}"
                                        class="category-filter {{ (int) $maDanhMucDaChon === (int) $danhMuc->ma_danh_muc ? 'active' : '' }}"
                                        data-id="{{ $danhMuc->ma_danh_muc }}">
                                        {{ $danhMuc->ten }}
                                        <span><i class="fas fa-long-arrow-alt-right"></i></span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="widget ltn__search-widget">
                        <h4 class="ltn__widget-title ltn__widget-title-border">Tìm Kiếm</h4>
                        <form id="#" method="get" action="{{ route('tim-kiem') }}">
                            <input type="text" name="keyword" value="{{ $tuKhoa }}" placeholder="Tìm kiếm...">
                            <button type="submit">
                                <span><i class="icon-search"></i></span>
                            </button>
                        </form>
                    </div>

                    <div class="widget ltn__banner-widget">
                        <a href="{{ route('san-pham.danh-sach') }}">
                            <img src="{{ asset('assets/clients/img/banner/banner-1.jpg') }}" alt="#">
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection
