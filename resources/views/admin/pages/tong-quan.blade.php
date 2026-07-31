@extends('layouts.admin')

@section('title', 'Tổng quan')

@section('content')
<div class="right_col admin-dashboard" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Tổng quan</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="row dashboard-top-row">
        <div class="tile_count">
            <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> Tổng số người dùng</span>
                <div class="count">{{ $tongNguoiDung }}</div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count">
                <span class="count_top"><i class="fa fa-cubes"></i> Tổng số sản phẩm</span>
                <div class="count">{{ $tongSanPham }}</div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count">
                <span class="count_top"><i class="fa fa-shopping-cart"></i> Tổng số đơn hàng</span>
                <div class="count">{{ $tongDonHang }}</div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 tile_stats_count dashboard-revenue-tile">
                <span class="count_top"><i class="fa fa-money"></i> Tổng doanh thu</span>
                <div class="count green dashboard-money">
                    {{ number_format($tongDoanhThu, 0, ',', '.') }}<small>đ</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row dashboard-panel-row">
        <div class="col-md-8 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Doanh thu theo tháng <small>Năm {{ $thongKeDoanhThuThang['nam'] }}</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link" title="Thu gọn"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="dashboard-chart dashboard-chart-large">
                        <canvas id="bieu-do-doanh-thu-thang"
                            data-nhan="{{ json_encode($thongKeDoanhThuThang['nhan']) }}"
                            data-gia-tri="{{ json_encode($thongKeDoanhThuThang['doanh_thu']) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Doanh thu 7 ngày gần nhất</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link" title="Thu gọn"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="dashboard-chart dashboard-chart-large">
                        <canvas id="bieu-do-doanh-thu-tuan"
                            data-nhan="{{ json_encode($thongKeDoanhThuTuan['nhan']) }}"
                            data-gia-tri="{{ json_encode($thongKeDoanhThuTuan['doanh_thu']) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row dashboard-panel-row">
        <div class="col-md-6 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Người dùng đăng ký mới <small>6 tháng gần nhất</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link" title="Thu gọn"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="dashboard-chart">
                        <canvas id="bieu-do-nguoi-dung"
                            data-nhan="{{ json_encode($thongKeNguoiDung['nhan']) }}"
                            data-gia-tri="{{ json_encode($thongKeNguoiDung['so_nguoi_dung']) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Top sản phẩm được yêu thích</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link" title="Thu gọn"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    @if (count($topSanPhamYeuThich['nhan']) > 0)
                        <div class="dashboard-chart">
                            <canvas id="bieu-do-san-pham-yeu-thich"
                                data-nhan="{{ json_encode($topSanPhamYeuThich['nhan']) }}"
                                data-gia-tri="{{ json_encode($topSanPhamYeuThich['so_luot']) }}"></canvas>
                        </div>
                    @else
                        <div class="dashboard-empty">Chưa có sản phẩm nào được thêm vào yêu thích.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row dashboard-panel-row">
        <div class="col-md-7 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Sản phẩm bán chạy</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link" title="Thu gọn"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped dashboard-product-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Đã bán</th>
                                    <th class="text-right">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topSanPhamBanChay as $sanPham)
                                    <tr>
                                        <td>{{ $sanPham['ten'] }}</td>
                                        <td class="text-center">{{ $sanPham['so_luong'] }}</td>
                                        <td class="text-right">
                                            {{ number_format($sanPham['doanh_thu'], 0, ',', '.') }}đ
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Chưa có sản phẩm bán thành công.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Sản phẩm theo danh mục</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link" title="Thu gọn"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="dashboard-category-content">
                        <canvas class="canvasDoughnutCategory dashboard-doughnut"
                            height="150"
                            width="150"
                            data-labels="{{ json_encode($tenDanhMucs) }}"
                            data-counts="{{ json_encode($soLuongSanPhams) }}"></canvas>

                        <div class="dashboard-category-list">
                            <table class="tile_info">
                                @foreach ($danhMucs as $viTri => $danhMuc)
                                    <tr>
                                        <td>
                                            <p>
                                                <i class="fa fa-square dashboard-color-{{ $viTri % 5 }}"></i>
                                                {{ $danhMuc->ten }}
                                            </p>
                                        </td>
                                        <td>{{ $danhMuc->sanPhams->count() }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection