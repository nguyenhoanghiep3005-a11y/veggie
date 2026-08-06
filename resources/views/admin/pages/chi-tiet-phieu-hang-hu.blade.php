@extends('layouts.admin')

@section('title', 'Chi tiết phiếu hàng hư')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Chi tiết phiếu hàng hư {{ $phieuHangHu->so_phieu }}</h3></div>
        <div class="title_right"><a href="{{ route('admin.phieu-hang-hu.danh-sach') }}" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Quay lại</a></div>
    </div>
    <div class="clearfix"></div>

    <div class="x_panel">
        <div class="x_title"><h2>Thông tin phiếu</h2><div class="clearfix"></div></div>
        <div class="x_content row">
            <div class="col-md-6">
                <p><strong>Nguồn:</strong>
                    @if ($phieuHangHu->ma_don_hang)
                        Hàng hoàn từ đơn hàng
                    @elseif ($phieuHangHu->ma_phieu_nhap)
                        Phiếu nhập hàng
                    @elseif ($phieuHangHu->ma_don_dat_nhap)
                        Đơn đặt nhập
                    @else
                        Điều chỉnh kho
                    @endif
                </p>
                <p><strong>Đơn đặt nhập:</strong> {{ $phieuHangHu->donDatNhap ? $phieuHangHu->donDatNhap->so_don : '-' }}</p>
                <p><strong>Phiếu nhập:</strong> {{ $phieuHangHu->phieuNhap ? $phieuHangHu->phieuNhap->so_phieu : '-' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Nhà cung cấp:</strong> {{ $phieuHangHu->nhaCungCap ? $phieuHangHu->nhaCungCap->ten : '-' }}</p>
                <p><strong>Lý do:</strong> {{ $phieuHangHu->ly_do }}</p>
                <p><strong>Thời điểm:</strong> {{ $phieuHangHu->xay_ra_luc ? $phieuHangHu->xay_ra_luc->format('d/m/Y H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    <div class="x_panel">
        <div class="x_title"><h2>Sản phẩm hư</h2><div class="clearfix"></div></div>
        <div class="x_content table-responsive">
            <table class="table table-bordered text-center">
                <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Ghi chú</th></tr></thead>
                <tbody>
                    @foreach($phieuHangHu->chiTietPhieuHangHus as $chiTiet)
                        <tr>
                            <td class="text-left"><strong>{{ $chiTiet->ten_san_pham_hien_thi }}</strong></td>
                            <td>{{ number_format($chiTiet->so_luong) }}</td>
                            <td>{{ $chiTiet->ghi_chu ? $chiTiet->ghi_chu : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($phieuHangHu->minhChungs->isNotEmpty())
        <div class="x_panel">
            <div class="x_title"><h2>Minh chứng</h2><div class="clearfix"></div></div>
            <div class="x_content row">
                @foreach($phieuHangHu->minhChungs as $minhChung)
                    <div class="col-md-3 mb-3">
                        <a href="{{ $minhChung->duong_dan_hien_thi }}" target="_blank">{{ $minhChung->ten_goc ? $minhChung->ten_goc : 'Xem minh chứng' }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
