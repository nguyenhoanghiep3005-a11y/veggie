@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@push('styles')
<style>
    /* CSS bat buoc cho Datatables tu sinh ra, khong gan truc tiep vao the HTML duoc. */
    table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child:before,
    table.dataTable td.control,
    table.dataTable th.control,
    table.dataTable td.dtr-control,
    table.dataTable th.dtr-control {
        display: none !important;
    }

    #datatable-buttons td:empty,
    #datatable-buttons th:empty {
        display: none !important;
        visibility: collapse !important;
        width: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* CSS rieng cho nhan trang thai. */
    .custom-badge,
    .label {
        display: inline-block;
        min-width: 90px;
        padding: 8px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.3;
        text-align: center;
        white-space: normal;
    }

    .badge-success,
    .label-success {
        background-color: #28a745 !important;
        color: #fff !important;
    }

    .badge-warning,
    .label-warning {
        background-color: #ffc107 !important;
        color: #111 !important;
    }

    .badge-danger,
    .label-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
    }

    .badge-info,
    .label-info {
        background-color: #17a2b8 !important;
        color: #fff !important;
    }

    .badge-primary,
    .label-primary {
        background-color: #007bff !important;
        color: #fff !important;
    }

    .badge-secondary,
    .label-default,
    .label-secondary {
        background-color: #6c757d !important;
        color: #fff !important;
    }

    /* CSS rieng trang don hang. */
    .admin-orders-page .dropdown-menu {
        min-width: 150px;
    }

    .admin-orders-page .dropdown-item {
        display: block;
        padding: 7px 14px;
        color: #333;
    }

    .admin-orders-page .dropdown-item:hover {
        background-color: #f5f5f5;
        text-decoration: none;
    }
</style>
@endpush

@section('content')
<div class="right_col admin-orders-page" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Danh sách tất cả đơn hàng</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div id="thong-bao-don-hang"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Danh sách đơn hàng</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <p class="text-muted font-13 m-b-30">
                                        Theo dõi đơn hàng, trạng thái thanh toán và xử lý giao hàng ngay trên danh sách.
                                    </p>

                                    <form method="GET" action="{{ route('admin.don-hang.danh-sach') }}" class="form-inline mb-3">
                                        <label for="trang_thai" class="mr-2"><strong>Trạng thái đơn</strong></label>
                                        <select name="trang_thai" id="trang_thai" class="form-control mr-2" onchange="this.form.submit()">
                                            <option value="tat_ca" {{ $trangThaiDaChon == 'tat_ca' ? 'selected' : '' }}>Tất cả trạng thái</option>
                                            @foreach ($cacTrangThaiDonHang as $giaTri => $tenTrangThai)
                                                <option value="{{ $giaTri }}" {{ $trangThaiDaChon == $giaTri ? 'selected' : '' }}>{{ $tenTrangThai }}</option>
                                            @endforeach
                                        </select>
                                        @if ($trangThaiDaChon != 'tat_ca')
                                            <a href="{{ route('admin.don-hang.danh-sach') }}" class="btn btn-secondary">Bỏ lọc</a>
                                        @endif
                                    </form>

                                    <table id="datatable-responsive" class="table table-striped table-bordered admin-table-centered" style="width:100%; text-align:center;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tài khoản</th>
                                                <th>Thông tin người đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái đơn hàng</th>
                                                <th>Trạng thái thanh toán</th>
                                                <th>Chi tiết đơn hàng</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($donHangs as $donHang)
                                                <tr id="don-hang-{{ $donHang->ma_don_hang }}">
                                                    <td>{{ $donHang->ma_don_hang }}</td>
                                                    <td>{{ $donHang->ten_khach_hang }}</td>
                                                    <td>
                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#diaChiGiaoHangModal-{{ $donHang->ma_don_hang }}">
                                                        {{ $donHang->dia_chi_nguoi_nhan }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ number_format($donHang->tong_tien, 0, ',', '.') }}
                                                    <small>đ</small>
                                                </td>
                                                <td class="order-status">
                                                    @if ($donHang->yeuCauDoiTra)
                                                        @if ($donHang->yeuCauDoiTra->trang_thai == 'cho_duyet')
                                                            <span class="custom-badge badge badge-warning">Chờ duyệt yêu cầu</span>
                                                        @elseif ($donHang->yeuCauDoiTra->trang_thai == 'da_duyet')
                                                            <span class="custom-badge badge badge-info">Đã duyệt yêu cầu</span>
                                                        @elseif ($donHang->yeuCauDoiTra->trang_thai == 'dang_xu_ly')
                                                            <span class="custom-badge badge badge-info">Đang xử lý đổi trả</span>
                                                        @elseif ($donHang->yeuCauDoiTra->trang_thai == 'dang_giao_hang_doi')
                                                            <span class="custom-badge badge badge-primary">Đang giao hàng đổi</span>
                                                        @elseif ($donHang->yeuCauDoiTra->trang_thai == 'hoan_tat')
                                                            <span class="custom-badge badge badge-success">Hoàn tất đổi trả</span>
                                                        @else
                                                            <span class="custom-badge badge badge-secondary">-</span>
                                                        @endif
                                                    @else
                                                        @if ($donHang->trang_thai == 'cho_xac_nhan')
                                                            <span class="custom-badge badge badge-warning">Chờ xác nhận</span>
                                                        @elseif ($donHang->trang_thai == 'da_xac_nhan')
                                                            <span class="custom-badge badge badge-primary">Đã xác nhận</span>
                                                        @elseif ($donHang->trang_thai == 'dang_giao')
                                                            <span class="custom-badge badge badge-info">Đang giao hàng</span>
                                                        @elseif ($donHang->trang_thai == 'dang_hoan_hang')
                                                            <span class="custom-badge badge badge-info">Đang hoàn hàng</span>
                                                        @elseif ($donHang->trang_thai == 'hoan_thanh')
                                                            <span class="custom-badge badge badge-success">Hoàn thành</span>
                                                        @elseif ($donHang->trang_thai == 'da_hoan_ve_kho')
                                                            <span class="custom-badge badge badge-success">Đã hoàn về kho</span>
                                                        @elseif ($donHang->trang_thai == 'giao_that_bai')
                                                            <span class="custom-badge badge badge-danger">Giao hàng thất bại</span>
                                                        @elseif ($donHang->trang_thai == 'da_huy' && $donHang->nguoi_huy == 'quan_tri')
                                                            <span class="custom-badge badge badge-danger">Đã hủy bởi Shop</span>
                                                        @elseif ($donHang->trang_thai == 'da_huy')
                                                            <span class="custom-badge badge badge-danger">Đã hủy</span>
                                                        @else
                                                            <span class="custom-badge badge badge-secondary">{{ $donHang->trang_thai }}</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="{{ $donHang->lop_trang_thai_thanh_toan }}">
                                                        {{ $donHang->ten_trang_thai_thanh_toan }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#chiTietDonHangModal-{{ $donHang->ma_don_hang }}">
                                                        Xem
                                                    </button>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                        <div class="dropdown-menu">
                                                            @if ($donHang->trang_thai == 'cho_xac_nhan')
                                                                <a class="dropdown-item nut-xac-nhan-don" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Xác nhận</a>
                                                                <a class="dropdown-item nut-mo-huy-don text-danger" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Hủy đơn</a>
                                                            @endif

                                                            @if ($donHang->trang_thai == 'da_xac_nhan')
                                                                <a class="dropdown-item nut-giao-don" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Giao hàng</a>
                                                                <a class="dropdown-item nut-mo-huy-don text-danger" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Hủy đơn</a>
                                                            @endif

                                                            @if ($donHang->trang_thai == 'dang_giao')
                                                                <a class="dropdown-item nut-hoan-tat-don" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Đã giao</a>
                                                                <a class="dropdown-item nut-mo-giao-that-bai text-danger" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Giao thất bại</a>
                                                            @endif

                                                            @if ($donHang->coTheGiaoLai())
                                                                <a class="dropdown-item nut-giao-lai" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Giao lại</a>
                                                            @endif

                                                            @if ($donHang->coTheHoanVeCuaHang())
                                                                <a class="dropdown-item nut-hoan-ve" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Hoàn về cửa hàng</a>
                                                            @endif

                                                            @if ($donHang->trang_thai == 'dang_hoan_hang')
                                                                <a class="dropdown-item nut-mo-nhan-hang-hoan" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Nhận hàng hoàn</a>
                                                            @endif

                                                            @php
                                                            $coTheHoanTienPaypal = $donHang->thanhToan
                                                            && $donHang->thanhToan->phuong_thuc == 'paypal'
                                                            && $donHang->thanhToan->trang_thai == 'da_thanh_toan'
                                                            && (
                                                            $donHang->trang_thai == 'da_hoan_ve_kho'
                                                            || ($donHang->trang_thai == 'da_huy' && $donHang->nguoi_huy == 'khach_hang')
                                                            );
                                                            @endphp

                                                            @if ($coTheHoanTienPaypal)
                                                                <a class="dropdown-item nut-hoan-tien-paypal" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Đã hoàn tiền PayPal</a>
                                                            @elseif ($donHang->trang_thai == 'da_hoan_ve_kho')
                                                                <a class="dropdown-item nut-ket-thuc-don-hoan text-danger" href="#" data-ma-don-hang="{{ $donHang->ma_don_hang }}">Kết thúc đơn hoàn</a>
                                                            @endif

                                                            <a class="dropdown-item" target="_blank" href="{{ route('admin.don-hang.chi-tiet', $donHang->ma_don_hang) }}">Xem chi tiết</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @foreach ($donHangs as $donHang)
                                    <div class="modal fade" id="diaChiGiaoHangModal-{{ $donHang->ma_don_hang }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Thông tin giao hàng</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Người nhận:</strong> {{ $donHang->ten_nguoi_nhan }}</p>
                                                    <p><strong>Địa chỉ:</strong> {{ $donHang->dia_chi_nguoi_nhan }}</p>
                                                    <p><strong>Tỉnh thành:</strong> {{ $donHang->tinh_thanh_nguoi_nhan }}</p>
                                                    <p><strong>Điện thoại:</strong> {{ $donHang->so_dien_thoai_nguoi_nhan }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="chiTietDonHangModal-{{ $donHang->ma_don_hang }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Chi tiết hóa đơn</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Tên sản phẩm</th>
                                                                <th>Số lượng</th>
                                                                <th>Đơn giá</th>
                                                                <th>Thành tiền</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($donHang->chiTietDonHangs as $chiTietDonHang)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $chiTietDonHang->ten_san_pham }}</td>
                                                                    <td>{{ $chiTietDonHang->so_luong }}</td>
                                                                    <td>{{ number_format($chiTietDonHang->gia, 0, ',', '.') }} <small>đ</small></td>
                                                                    <td>{{ number_format($chiTietDonHang->thanh_tien, 0, ',', '.') }} <small>đ</small></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="modal-huy-don" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hủy đơn hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ma-don-hang-huy">
                <div class="form-group">
                    <label><strong>Lý do hủy đơn hàng</strong> <span class="text-danger">*</span></label>
                    <textarea id="ly-do-huy-don" class="form-control" rows="3" placeholder="Nhập lý do hủy đơn hàng"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" id="nut-xac-nhan-huy-don">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-giao-that-bai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Giao hàng thất bại</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ma-don-giao-that-bai">
                <div class="form-group">
                    <label><strong>Lý do giao thất bại</strong> <span class="text-danger">*</span></label>
                    <textarea id="ly-do-giao-that-bai" class="form-control" rows="3" placeholder="Khách từ chối nhận hoặc không liên hệ được"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" id="nut-xac-nhan-giao-that-bai">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-nhan-hang-hoan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nhận hàng hoàn</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ma-don-nhan-hang-hoan">
                <div class="form-group">
                    <label><strong>Tình trạng hàng</strong></label>
                    <select id="tinh-trang-hang-hoan" class="form-control">
                        <option value="nguyen_ven">Nguyên vẹn, nhập lại kho</option>
                        <option value="hu_hong">Hư hỏng, không nhập lại kho</option>
                    </select>
                </div>
                <div id="khu-vuc-hang-hoan-hu" class="d-none mt-3">
                    <textarea id="ly-do-hang-hoan-hu" class="form-control mb-2" rows="3" placeholder="Mô tả tình trạng hư hỏng"></textarea>
                    <input type="file" id="minh-chung-hang-hoan" class="form-control" accept="image/*,video/*" multiple>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-warning" id="nut-xac-nhan-nhan-hang-hoan">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
@endsection
