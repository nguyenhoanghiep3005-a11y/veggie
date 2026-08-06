@extends('layouts.admin')

@section('title', 'Phiếu hàng hư')

@section('content')
<div class="right_col" role="main">
    <div class="page-title"><div class="title_left"><h3>Phiếu hàng hư</h3></div></div>
    <div class="clearfix"></div>

    <div class="x_panel">
        <div class="x_title"><h2>Danh sách phiếu hàng hư</h2><div class="clearfix"></div></div>
        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead><tr><th>Số phiếu</th><th>Nguồn</th><th>Nhà cung cấp</th><th>Sản phẩm</th><th>Số lượng</th><th>Thao tác</th></tr></thead>
                <tbody>
                    @forelse($phieuHangHus as $phieuHangHu)
                        <tr>
                            <td><strong>{{ $phieuHangHu->so_phieu }}</strong></td>
                            <td>
                                @if ($phieuHangHu->ma_don_hang)
                                    Hàng hoàn từ đơn hàng
                                @elseif ($phieuHangHu->ma_phieu_nhap)
                                    Phiếu nhập hàng
                                @elseif ($phieuHangHu->ma_don_dat_nhap)
                                    Đơn đặt nhập
                                @else
                                    Điều chỉnh kho
                                @endif
                            </td>
                            <td>{{ $phieuHangHu->nhaCungCap ? $phieuHangHu->nhaCungCap->ten : '-' }}</td>
                            <td class="text-left">{{ $phieuHangHu->tomTatSanPham() }}</td>
                            <td>{{ number_format($phieuHangHu->tongSoLuong()) }}</td>
                            <td><a href="{{ route('admin.phieu-hang-hu.chi-tiet', $phieuHangHu) }}" class="btn btn-danger btn-sm"><i class="fa fa-eye"></i> Xem</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">Chưa có phiếu hàng hư.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-center">{{ $phieuHangHus->links() }}</div>
        </div>
    </div>
</div>
@endsection
