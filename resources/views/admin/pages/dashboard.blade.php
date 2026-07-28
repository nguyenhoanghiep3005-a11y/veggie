@extends('layouts.admin')

@section('title','Dashboard')
@section('content')
<!-- page content -->
<div class="right_col" role="main">
    <!-- top tiles -->
    <div class="row dashboard-top-row">
        <div class="tile_count">
            <div class="col-md-2 col-sm-4  tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> Tổng số người dùng</span>
                <div class="count">{{$user->count()}}</div>
            </div>
            <div class="col-md-2 col-sm-4  tile_stats_count">
                <span class="count_top"><i class="fa fa-bar-chart"></i>Tổng số lượng sản phẩm</span>
                <div class="count">{{$products->count()}}</div>
            </div>
            <div class="col-md-2 col-sm-4  tile_stats_count">
                <span class="count_top"><i class="fa fa-shopping-cart"></i> Tổng số lượng đơn hàng</span>
                <div class="count">{{$orders->count()}}</div>
            </div>
            <div class="col-md-2 col-sm-4  tile_stats_count dashboard-revenue-tile">
                <span class="count_top"><i class="fa fa-money"></i>Tổng doanh thu</span>
                <div class="count">{{number_format($orders->sum('total_price'),0,0)}}</div>
            </div>
        </div>
    </div>
    <!-- /top tiles -->
    <div class="row">
        <div class="col-md-4 col-sm-4 ">
            <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                    <h2>Danh mục</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="dashboard-category-table">
                        <tr>
                            <th class="dashboard-chart-heading">
                                <p>Top 5</p>
                            </th>
                            <th>
                                <div class="col-lg-7 col-md-7 col-sm-7 ">
                                    <p class="">Danh mục</p>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5 ">
                                    <p class="">Sản phẩm</p>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <canvas class="canvasDoughnutCategory" height="140" width="140"
                                    data-labels='@json($categories->pluck('name'))'
                                    data-counts='@json($categories->map(fn($category)=>$category->products->count()))'
                                    class="dashboard-doughnut"></canvas>
                            </td>
                            <td>
                                <table class="tile_info">
                                    @foreach ($categories as $index => $category)
                                    <tr>
                                        <td>
                                            <p><i class="fa fa-square dashboard-color-{{ $index % 5 }}"></i>{{$category->name}}</p>
                                        </td>
                                        <td>{{$category->products->count()}}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /page content -->
@endsection
