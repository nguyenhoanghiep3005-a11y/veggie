@extends('layouts.admin')

@section('title', 'Phiếu nhập')

@section('content')
<div class="right_col" role="main">
    <div class="page-title"><div class="title_left"><h3>Phiếu nhập</h3></div></div>
    <div class="clearfix"></div>

    <div class="x_panel">
        <div class="x_title"><h2>Danh sách phiếu nhập</h2><div class="clearfix"></div></div>
        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead><tr><th>Số phiếu</th><th>Đơn đặt nhập</th><th>Nhà cung cấp</th><th>Ngày nhập</th><th>Số mặt hàng</th><th>Thao tác</th></tr></thead>
                <tbody>
                    @forelse($phieuNhaps as $phieuNhap)
                        <tr>
                            <td><strong>{{ $phieuNhap->so_phieu }}</strong></td>
                            <td>{{ $phieuNhap->donDatNhap ? $phieuNhap->donDatNhap->so_don : '-' }}</td>
                            <td>{{ $phieuNhap->ten_nha_cung_cap_hien_thi }}</td>
                            <td>{{ $phieuNhap->ngay_nhap_hien_thi }}</td>
                            <td>{{ $phieuNhap->chiTietPhieuNhaps->count() }}</td>
                            <td><a href="{{ route('admin.phieu-nhap.chi-tiet', $phieuNhap) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> Xem</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">Chưa có phiếu nhập.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-center">{{ $phieuNhaps->links() }}</div>
        </div>
    </div>
</div>
@endsection
