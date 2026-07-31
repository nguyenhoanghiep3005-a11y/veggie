@extends('layouts.admin')

@section('title', 'Đơn đặt nhập')

@section('content')
<div class="right_col admin-purchase-orders-page" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Đơn đặt nhập</h3></div>
        <div class="title_right">
            <a href="{{ route('admin.don-dat-nhap.them') }}" class="btn btn-success pull-right">
                <i class="fa fa-plus"></i> Tạo đơn đặt nhập
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="x_panel">
        <div class="x_title">
            <h2>Danh sách đơn đặt nhập từ nhà cung cấp</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center purchase-orders-table">
                <thead>
                    <tr>
                        <th>Số đơn</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày đặt</th>
                        <th>Số mặt hàng</th>
                        <th>Đã nhập</th>
                        <th>Hàng lỗi</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donDatNhaps as $donDatNhap)
                        <tr>
                            <td><strong>{{ $donDatNhap->so_don }}</strong></td>
                            <td>{{ $donDatNhap->ten_nha_cung_cap_hien_thi }}</td>
                            <td>{{ $donDatNhap->ngay_dat_hien_thi }}</td>
                            <td>{{ $donDatNhap->chiTietDonDatNhaps->count() }}</td>
                            <td>{{ number_format($donDatNhap->tongSoLuongDaNhap()) }}</td>
                            <td>{{ number_format($donDatNhap->tongSoLuongTuChoi()) }}</td>
                            <td><span class="{{ $donDatNhap->lopTrangThai() }}">{{ $donDatNhap->tenTrangThai() }}</span></td>
                            <td class="purchase-order-actions">
                                <div class="admin-action-group">
                                    @if($donDatNhap->trang_thai == 'cho_nhap_hang')
                                        <a href="{{ route('admin.don-dat-nhap.nhap-kho', $donDatNhap->ma_don_dat_nhap) }}" class="btn btn-success btn-sm action-btn">
                                            <i class="fa fa-sign-in"></i> Nhập hàng
                                        </a>
                                        <form action="{{ route('admin.don-dat-nhap.xoa', $donDatNhap->ma_don_dat_nhap) }}" method="POST" onsubmit="return confirm('Xóa đơn đặt nhập này?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm action-btn">
                                                <i class="fa fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.don-dat-nhap.chi-tiet', $donDatNhap->ma_don_dat_nhap) }}" class="btn btn-primary btn-sm action-btn">
                                            <i class="fa fa-eye"></i> Xem
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted">Chưa có đơn đặt nhập.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="text-center">{{ $donDatNhaps->links() }}</div>
        </div>
    </div>
</div>
@endsection