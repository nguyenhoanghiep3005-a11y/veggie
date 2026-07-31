@extends('layouts.client')

@section('title','Sản phẩm')
@section('breadcrumb','Sản phẩm')
@section('content')
<!-- PRODUCT DETAILS AREA START -->
<div class="ltn__product-area ltn__product-gutter">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 order-lg-2 mb-120">
                <div class="ltn__shop-options">
                    <ul>
                        <li>
                            <div class="ltn__grid-list-tab-menu ">
                                <div class="nav">
                                    <a class="active show" data-bs-toggle="tab" href="#liton_product_grid"><i
                                            class="fas fa-th-large"></i></a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="short-by text-center">
                                <select id="sort-by" class="nice-select">
                                    <option value="mac_dinh">Sắp xếp mặc định</option>
                                    <option value="moi_nhat">Sắp xếp theo sản phẩm mới</option>
                                    <option value="gia_tang">Sắp xếp theo giá: Thấp đến Cao</option>
                                    <option value="gia_giam">Sắp xếp theo giá: Cao đến Thấp</option>
                                </select>
                            </div>
                        </li>

                    </ul>
                </div>
                <div class="tab-content">
                    <div id="loading-spinner">
                        <div class="loader"></div>
                    </div>
                    <div class="tab-pane fade active show" id="liton_product_grid">
                        @include('clients.components.luoi-san-pham',['sanPhams' => $sanPhams])
                    </div>
                </div>
                <div class="ltn__pagination-area text-center">
                    <div class="ltn__pagination">
                        {!! $sanPhams->links('clients.components.pagination.phan-trang') !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-4  mb-120">
                <aside class="sidebar ltn__shop-sidebar">
                    <!-- Category Widget -->
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
                    <!-- Price Filter Widget -->
                    <div class="widget ltn__price-filter-widget">
                        <h4 class="ltn__widget-title ltn__widget-title-border">Lọc theo giá</h4>
                        <div class="price_filter">
                            <div class="price_slider_amount">
                                <input type="submit" value="Giá:" />
                                <input type="text" class="so_tien" name="gia" placeholder="Add Your Price" />
                            </div>
                            <div class="slider-range"></div>
                        </div>
                    </div>
                    <!-- Top Rated Product Widget -->
                    <!-- Search Widget -->
                    <div class="widget ltn__search-widget">
                        <h4 class="ltn__widget-title ltn__widget-title-border">Tìm Kiếm</h4>
                        <form id="#" method="get" action="{{route('tim-kiem')}}">
                            <input type="text" name="keyword" value="" placeholder="Tìm kiếm..." />
                            <button type="submit">
                                <span><i class="icon-search"></i></span>
                            </button>
                        </form>
                    </div>
                    <!-- Banner Widget -->
                    <div class="widget ltn__banner-widget">
                        <a href="{{route('san-pham.danh-sach')}}"><img
                                src="{{asset('assets/clients/img/banner/banner-1.jpg')}}" alt="#"></a>
                    </div>

                </aside>
            </div>
        </div>
    </div>
</div>
<!-- PRODUCT DETAILS AREA END -->
@endsection