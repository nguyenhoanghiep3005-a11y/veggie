@extends('layouts.admin')

@section('title', 'Chi tiết đơn đặt nhập')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Chi tiết đơn {{ $donDatNhap->so_don }}</h3></div>
        <div class="title_right">
            <a href="{{ route('admin.don-dat-nhap.danh-sach') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="x_panel">
            <div class="x_title"><h2>Thông tin đơn đặt nhập</h2><div class="clearfix"></div></div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nhà cung cấp:</strong> {{ $donDatNhap->ten_nha_cung_cap_hien_thi }}</p>
                        <p><strong>Ngày đặt:</strong> {{ $donDatNhap->ngay_dat_hien_thi }}</p>
                        <p><strong>Ngày nhập:</strong> {{ $donDatNhap->ngay_nhap_hien_thi }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Trạng thái:</strong>
                            @if ($donDatNhap->trang_thai == 'cho_nhap_hang')
                                <span class="badge badge-warning">Chờ nhập hàng</span>
                            @elseif ($donDatNhap->trang_thai == 'da_nhap_hang')
                                <span class="badge badge-success">Đã nhập hàng</span>
                            @else
                                <span class="badge badge-secondary">{{ $donDatNhap->trang_thai }}</span>
                            @endif
                        </p>
                        <p><strong>Ghi chú:</strong> {{ $donDatNhap->ghi_chu ? $donDatNhap->ghi_chu : '-' }}</p>
                        <p><strong>Mô tả hàng lỗi:</strong> {{ $donDatNhap->mo_ta_hang_loi ? $donDatNhap->mo_ta_hang_loi : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title"><h2>Sản phẩm đặt nhập</h2><div class="clearfix"></div></div>
            <div class="x_content table-responsive">
                <table class="table table-bordered text-center">
                    <thead><tr><th>Sản phẩm</th><th>Đã đặt</th><th>Đã nhận</th><th>Từ chối</th><th>Đã nhập</th><th>Hạn sử dụng</th></tr></thead>
                    <tbody>
                        @foreach($donDatNhap->chiTietDonDatNhaps as $chiTiet)
                            <tr>
                                <td class="text-left"><strong>{{ $chiTiet->ten_san_pham_hien_thi }}</strong></td>
                                <td>{{ number_format($chiTiet->so_luong_dat) }}</td>
                                <td>{{ number_format($chiTiet->so_luong_nhan) }}</td>
                                <td class="text-danger">{{ number_format($chiTiet->so_luong_tu_choi) }}</td>
                                <td class="text-success">{{ number_format($chiTiet->so_luong_da_nhap) }}</td>
                                <td>{{ $chiTiet->han_su_dung_hien_thi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($donDatNhap->phieuNhaps->isNotEmpty())
            <div class="x_panel">
                <div class="x_title"><h2>Phiếu nhập</h2><div class="clearfix"></div></div>
                <div class="x_content table-responsive">
                    <table class="table table-bordered text-center">
                        <thead><tr><th>Số phiếu</th><th>Ngày nhập</th><th>Tổng số lượng</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            @foreach($donDatNhap->phieuNhaps as $phieuNhap)
                                <tr>
                                    <td><strong>{{ $phieuNhap->so_phieu }}</strong></td>
                                    <td>{{ $phieuNhap->ngay_nhap_hien_thi }}</td>
                                    <td>{{ number_format($phieuNhap->tongSoLuong()) }}</td>
                                    <td><a href="{{ route('admin.phieu-nhap.chi-tiet', $phieuNhap) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> Xem</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($donDatNhap->phieuHangHus->isNotEmpty())
            <div class="x_panel">
                <div class="x_title"><h2>Phiếu hàng hư</h2><div class="clearfix"></div></div>
                <div class="x_content table-responsive">
                    <table class="table table-bordered text-center">
                        <thead><tr><th>Số phiếu</th><th>Lý do</th><th>Số lượng</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            @foreach($donDatNhap->phieuHangHus as $phieuHangHu)
                                <tr>
                                    <td><strong>{{ $phieuHangHu->so_phieu }}</strong></td>
                                    <td>{{ $phieuHangHu->ly_do }}</td>
                                    <td>{{ number_format($phieuHangHu->tongSoLuong()) }}</td>
                                    <td><a href="{{ route('admin.phieu-hang-hu.chi-tiet', $phieuHangHu) }}" class="btn btn-danger btn-sm"><i class="fa fa-eye"></i> Xem</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
