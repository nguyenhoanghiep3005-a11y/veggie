@extends('layouts.admin')

@section('title', 'Quản lý kho')


@push('styles')
<style>
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
    }</style>
@endpush

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Quản lý kho</h3></div>
    </div>
    <div class="clearfix"></div>

    <div class="x_panel">
        <div class="x_content">
            <div class="btn-group m-b-15">
                <a href="{{ route('admin.kho-hang.danh-sach') }}" class="btn btn-sm {{ $trangThai == 'tat_ca' ? 'btn-primary' : 'btn-default' }}">Tất cả</a>
                <a href="{{ route('admin.kho-hang.danh-sach', ['trang_thai' => 'tuoi_moi']) }}" class="btn btn-sm {{ $trangThai == 'tuoi_moi' ? 'btn-success' : 'btn-default' }}">Tươi mới</a>
                <a href="{{ route('admin.kho-hang.danh-sach', ['trang_thai' => 'can_han']) }}" class="btn btn-sm {{ $trangThai == 'can_han' ? 'btn-warning' : 'btn-default' }}">Cận hạn</a>
                <a href="{{ route('admin.kho-hang.danh-sach', ['trang_thai' => 'het_han']) }}" class="btn btn-sm {{ $trangThai == 'het_han' ? 'btn-danger' : 'btn-default' }}">Hết hạn</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Nhà cung cấp</th>
                            <th>Sản phẩm</th>
                            <th>Còn/Nhập</th>
                            <th>Ngày sản xuất</th>
                            <th>Hạn sử dụng</th>
                            <th>Trạng thái</th>
                            <th>Giá</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loHangKhos as $loHangKho)
                            <tr>
                                <td>{{ $loHangKho->ten_nha_cung_cap }}</td>
                                <td>{{ $loHangKho->ten_san_pham }}</td>
                                <td>{{ $loHangKho->so_luong_con }}/{{ $loHangKho->so_luong_nhap }}</td>
                                <td>{{ $loHangKho->ngay_san_xuat_hien_thi }}</td>
                                <td>{{ $loHangKho->han_su_dung_hien_thi }}</td>
                                <td>
                                    @if ($loHangKho->han_su_dung == null)
                                        <span class="label label-default">Chưa có hạn sử dụng</span>
                                    @elseif ($loHangKho->daHetHan())
                                        <span class="label label-danger">Hết hạn</span>
                                    @elseif ($loHangKho->sapHetHan())
                                        <span class="label label-warning">Cận hạn</span>
                                    @else
                                        <span class="label label-success">Còn mới</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($loHangKho->sanPham && $loHangKho->coKhuyenMai())
                                        <del>{{ number_format($loHangKho->sanPham->gia, 0, ',', '.') }}<small>đ</small></del>
                                        <strong>{{ number_format($loHangKho->gia_khuyen_mai, 0, ',', '.') }}<small>đ</small></strong>
                                    @elseif ($loHangKho->sanPham)
                                        {{ number_format($loHangKho->sanPham->gia, 0, ',', '.') }}<small>đ</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#dieu-chinh-lo-{{ $loHangKho->ma_lo_hang_kho }}">Điều chỉnh</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8">Không có lô hàng phù hợp.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $loHangKhos->links() }}
            </div>
        </div>

        @foreach ($loHangKhos as $loHangKho)
            <div class="modal fade" id="dieu-chinh-lo-{{ $loHangKho->ma_lo_hang_kho }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg"><div class="modal-content">
                    <div class="modal-header">
                        <h4>{{ $loHangKho->so_phieu_nhap }} - {{ $loHangKho->ten_san_pham }}</h4>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Giá khuyến mãi</h4>
                                <form action="{{ route('admin.kho-hang.dieu-chinh', $loHangKho) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="loai_dieu_chinh" value="khuyen_mai">
                                    <label>Giá gốc sản phẩm</label>
                                    <input type="text" class="form-control mb-2"
                                    value="{{ number_format($loHangKho->sanPham->gia, 0, ',', '.') }} đ" readonly>
                                    <label>Giá khuyến mãi</label>
                                    <input type="number" name="gia_khuyen_mai" class="form-control mb-3"
                                    min="0" step="1000" value="{{ $loHangKho->gia_khuyen_mai }}">
                                    <button class="btn btn-success" type="submit">Lưu giá</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h4>Ghi nhận hàng hư</h4>
                                <form action="{{ route('admin.kho-hang.dieu-chinh', $loHangKho) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="loai_dieu_chinh" value="hang_hu">
                                    <input type="number" name="so_luong_hu" class="form-control mb-2"
                                    min="1" max="{{ $loHangKho->so_luong_con }}" placeholder="Số lượng hư">
                                    <textarea name="ly_do_hu" class="form-control mb-2" rows="3" placeholder="Lý do"></textarea>
                                    <input type="file" name="minh_chung[]" class="form-control mb-2"
                                    accept="image/*,video/*" multiple required>
                                    <button class="btn btn-danger" type="submit">Ghi nhận hàng hư</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div></div>
            </div>
        @endforeach
    </div>
    @endsection
