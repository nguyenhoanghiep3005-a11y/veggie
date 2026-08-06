@extends('layouts.admin')

@section('title', 'Chi tiết phiếu nhập')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Chi tiết phiếu nhập {{ $phieuNhap->so_phieu }}</h3></div>
        <div class="title_right"><a href="{{ route('admin.phieu-nhap.danh-sach') }}" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Quay lại</a></div>
    </div>
    <div class="clearfix"></div>

    <div class="x_panel">
        <div class="x_title"><h2>Thông tin phiếu nhập</h2><div class="clearfix"></div></div>
        <div class="x_content row">
            <div class="col-md-6">
                <p><strong>Đơn đặt nhập:</strong> {{ $phieuNhap->donDatNhap ? $phieuNhap->donDatNhap->so_don : '-' }}</p>
                <p><strong>Nhà cung cấp:</strong> {{ $phieuNhap->ten_nha_cung_cap_hien_thi }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Ngày nhập:</strong> {{ $phieuNhap->ngay_nhap_hien_thi }}</p>
                <p><strong>Ghi chú:</strong> {{ $phieuNhap->ghi_chu ? $phieuNhap->ghi_chu : '-' }}</p>
            </div>
        </div>
    </div>

    <div class="x_panel">
        <div class="x_title"><h2>Sản phẩm đã nhập</h2><div class="clearfix"></div></div>
        <div class="x_content table-responsive">
            <table class="table table-bordered text-center">
                <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Ngày sản xuất</th><th>Hạn sử dụng</th></tr></thead>
                <tbody>
                    @foreach($phieuNhap->chiTietPhieuNhaps as $chiTiet)
                        <tr>
                            <td class="text-left"><strong>{{ $chiTiet->ten_san_pham_hien_thi }}</strong></td>
                            <td>{{ number_format($chiTiet->so_luong) }}</td>
                            <td>{{ $chiTiet->ngay_san_xuat ? $chiTiet->ngay_san_xuat->format('d/m/Y') : '-' }}</td>
                            <td>{{ $chiTiet->han_su_dung_hien_thi }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
