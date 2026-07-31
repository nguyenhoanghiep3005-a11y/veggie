@extends('layouts.admin')

@section('title', 'Hàng hư')
@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Hàng hư</h3></div>
        <div class="title_right">
            <a href="{{ route('admin.kho-hang.danh-sach') }}" class="btn btn-default pull-right">Quay lại kho</a>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="x_panel">
        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Lý do</th>
                        <th>Minh chứng</th>
                        <th>Ngày ghi nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hangHuKhos as $hangHuKho)
                        <tr>
                            <td>{{ $hangHuKho->ten_san_pham }}</td>
                            <td>{{ $hangHuKho->so_luong }}</td>
                            <td>{{ $hangHuKho->ly_do }}</td>
                            <td>
                                @forelse ($hangHuKho->minh_chung_hien_thi as $minhChung)
                                    <a href="{{ $minhChung['duong_dan'] }}" target="_blank" class="btn btn-default btn-xs">
                                        Xem {{ $minhChung['loai_tep'] == 'video' ? 'video' : 'ảnh' }} {{ $loop->iteration }}
                                    </a>
                                @empty
                                    Không có
                                @endforelse
                            </td>
                            <td>{{ $hangHuKho->xay_ra_luc_hien_thi }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Chưa có hàng hư.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{ $hangHuKhos->links() }}
        </div>
    </div>
</div>
@endsection
