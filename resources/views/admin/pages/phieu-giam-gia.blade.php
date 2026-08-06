@extends('layouts.admin')

@section('title', 'Quản lý phiếu giảm giá')


@push('styles')
<style>
    /* CSS rieng trang phieu giam gia. */
    .admin-coupon-panel .x_content {
        padding-top: 18px;
    }

    .admin-coupon-form .row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .admin-coupon-column {
        padding-left: 18px;
        padding-right: 18px;
    }

    .admin-coupon-form .form-group {
        margin-bottom: 16px;
    }

    .admin-coupon-form label {
        color: #73879c;
        font-weight: 600;
    }

    .admin-coupon-form .form-control {
        height: 42px;
        border-radius: 2px;
    }

    .admin-coupon-form select[multiple] {
        height: 130px;
    }

    .admin-coupon-status {
        display: flex;
        align-items: center;
        min-height: 42px;
        margin-top: 4px;
    }

    .admin-coupon-customer-box {
        padding: 12px;
        border: 1px dashed #cfd8dc;
        border-radius: 4px;
        background: #fbfcfd;
    }

    .admin-coupon-submit {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
        padding-top: 16px;
        border-top: 1px solid #e5e5e5;
    }

    .admin-coupon-submit .btn {
        min-width: 110px;
    }
</style>
@endpush
@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Quản lý phiếu giảm giá</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="x_panel admin-coupon-panel">
        <div class="x_title">
            <h2>Thêm phiếu mới</h2>
            <div class="clearfix"></div>
        </div>

        <div class="x_content">
            <form method="POST" action="{{ route('admin.phieu-giam-gia.luu') }}" class="admin-coupon-form">
                @csrf

                <div class="row">
                    <div class="col-md-6 admin-coupon-column">
                        <div class="form-group">
                            <label>Mã giảm giá *</label>
                            <input name="ma_giam_gia" class="form-control" value="{{ old('ma_giam_gia') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Phần trăm giảm (%) *</label>
                            <input type="number" name="phan_tram_giam" class="form-control"
                            value="{{ old('phan_tram_giam') }}" min="0.01" max="100" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label>Thời hạn</label>
                            <input type="datetime-local" name="het_han_luc" class="form-control"
                            value="{{ old('het_han_luc') }}">
                        </div>

                        <div class="admin-coupon-status">
                            <label>
                                <input type="checkbox" name="dang_hoat_dong" value="1"
                                @if (old('dang_hoat_dong', 1)) checked @endif>
                                Đang hoạt động
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 admin-coupon-column">
                        <div class="form-group">
                            <label>Đơn tối thiểu</label>
                            <input type="number" name="gia_tri_don_toi_thieu" class="form-control"
                            value="{{ old('gia_tri_don_toi_thieu', 0) }}" min="0" step="1000">
                        </div>

                        <div class="form-group">
                            <label>Giảm tối đa</label>
                            <input type="number" name="so_tien_giam_toi_da" class="form-control"
                            value="{{ old('so_tien_giam_toi_da') }}" min="0" step="1000">
                        </div>

                        <div class="form-group">
                            <label>Giới hạn lượt dùng</label>
                            <input type="number" name="gioi_han_su_dung" class="form-control"
                            value="{{ old('gioi_han_su_dung') }}" min="1">
                        </div>

                        <div class="form-group">
                            <label>Phạm vi sử dụng</label>
                            <select name="loai_ap_dung" class="form-control js-coupon-apply-type">
                                <option value="tat_ca" @if (old('loai_ap_dung', 'tat_ca') == 'tat_ca') selected @endif>
                                    Tất cả khách hàng
                                </option>
                                <option value="khach_hang" @if (old('loai_ap_dung') == 'khach_hang') selected @endif>
                                    Tài khoản riêng
                                </option>
                            </select>
                        </div>

                        <div class="form-group admin-coupon-customer-box {{ old('loai_ap_dung', 'tat_ca') == 'khach_hang' ? '' : 'd-none' }}">
                            <label>Tài khoản được nhận phiếu</label>
                            <select name="ma_nguoi_dungs[]" class="form-control" multiple size="5">
                                @foreach ($khachHangs as $khachHang)
                                    <option value="{{ $khachHang->ma_nguoi_dung }}"
                                        @if (in_array($khachHang->ma_nguoi_dung, old('ma_nguoi_dungs', []))) selected @endif>
                                        {{ $khachHang->ten }} - {{ $khachHang->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="admin-coupon-submit">
                    <button class="btn btn-success">Thêm phiếu</button>
                </div>
            </form>
        </div>
    </div>

    <div class="x_panel">
        <div class="x_title">
            <h2>Danh sách phiếu giảm giá</h2>
            <div class="clearfix"></div>
        </div>

        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Giảm</th>
                        <th>Điều kiện</th>
                        <th>Phạm vi</th>
                        <th>Thời hạn</th>
                        <th>Đã dùng</th>
                        <th>Giới hạn</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($phieuGiamGias as $phieuGiamGia)
                        <tr>
                            <td><strong>{{ $phieuGiamGia->ma_giam_gia }}</strong></td>
                            <td>
                                {{ $phieuGiamGia->phan_tram_hien_thi }}%
                                @if ($phieuGiamGia->so_tien_giam_toi_da)
                                    <br>
                                    <small>
                                    Tối đa {{ number_format($phieuGiamGia->so_tien_giam_toi_da, 0, ',', '.') }} đ
                                </small>
                            @endif
                        </td>
                        <td>{{ $phieuGiamGia->dieu_kien_hien_thi }}</td>
                        <td>
                            @if ($phieuGiamGia->loai_ap_dung == 'khach_hang')
                                Tài khoản riêng
                                <br><small>{{ count($phieuGiamGia->ma_nguoi_dungs) }} khách</small>
                            @else
                                Tất cả khách hàng
                            @endif
                        </td>
                        <td>{{ $phieuGiamGia->thoi_han_hien_thi }}</td>
                        <td>{{ $phieuGiamGia->so_lan_da_dung }}</td>
                        <td>{{ $phieuGiamGia->gioi_han_hien_thi }}</td>
                        <td>
                            <span class="badge badge-{{ $phieuGiamGia->lop_trang_thai }}">
                                {{ $phieuGiamGia->ten_trang_thai }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#coupon-edit-{{ $phieuGiamGia->ma_phieu_giam_gia }}">
                                Sửa
                            </button>

                            <form method="POST"
                                action="{{ route('admin.phieu-giam-gia.xoa', $phieuGiamGia) }}"
                                class="d-inline"
                                onsubmit="return confirm('Xóa hoặc khóa phiếu này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">Chưa có phiếu giảm giá.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="text-center">{{ $phieuGiamGias->links() }}</div>
    </div>
</div>
</div>

@foreach ($phieuGiamGias as $phieuGiamGia)
    <div class="modal fade" id="coupon-edit-{{ $phieuGiamGia->ma_phieu_giam_gia }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST"
                    action="{{ route('admin.phieu-giam-gia.cap-nhat', $phieuGiamGia) }}"
                    class="admin-coupon-form">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h4>Sửa phiếu {{ $phieuGiamGia->ma_giam_gia }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 admin-coupon-column">
                                <div class="form-group">
                                    <label>Mã giảm giá</label>
                                    <input name="ma_giam_gia" class="form-control"
                                    value="{{ $phieuGiamGia->ma_giam_gia }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Phần trăm giảm (%)</label>
                                    <input type="number" name="phan_tram_giam" class="form-control"
                                    value="{{ $phieuGiamGia->phan_tram_giam }}"
                                    min="0.01" max="100" step="0.01" required>
                                </div>

                                <div class="form-group">
                                    <label>Thời hạn</label>
                                    <input type="datetime-local" name="het_han_luc" class="form-control"
                                    value="{{ $phieuGiamGia->thoi_han_form }}">
                                </div>

                                <div class="admin-coupon-status">
                                    <label>
                                        <input type="checkbox" name="dang_hoat_dong" value="1"
                                        @if ($phieuGiamGia->dang_hoat_dong) checked @endif>
                                        Đang hoạt động
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 admin-coupon-column">
                                <div class="form-group">
                                    <label>Đơn tối thiểu</label>
                                    <input type="number" name="gia_tri_don_toi_thieu" class="form-control"
                                    value="{{ $phieuGiamGia->gia_tri_don_toi_thieu }}" min="0" step="1000">
                                </div>

                                <div class="form-group">
                                    <label>Giảm tối đa</label>
                                    <input type="number" name="so_tien_giam_toi_da" class="form-control"
                                    value="{{ $phieuGiamGia->so_tien_giam_toi_da }}" min="0" step="1000">
                                </div>

                                <div class="form-group">
                                    <label>Giới hạn lượt dùng</label>
                                    <input type="number" name="gioi_han_su_dung" class="form-control"
                                    value="{{ $phieuGiamGia->gioi_han_su_dung }}" min="1">
                                </div>

                                <div class="form-group">
                                    <label>Phạm vi sử dụng</label>
                                    <select name="loai_ap_dung" class="form-control js-coupon-apply-type">
                                        <option value="tat_ca"
                                            @if ($phieuGiamGia->loai_ap_dung == 'tat_ca') selected @endif>
                                            Tất cả khách hàng
                                        </option>
                                        <option value="khach_hang"
                                            @if ($phieuGiamGia->loai_ap_dung == 'khach_hang') selected @endif>
                                            Tài khoản riêng
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group admin-coupon-customer-box {{ $phieuGiamGia->loai_ap_dung == 'khach_hang' ? '' : 'd-none' }}">
                                    <label>Tài khoản được nhận phiếu</label>
                                    <select name="ma_nguoi_dungs[]" class="form-control" multiple size="5">
                                        @foreach ($khachHangs as $khachHang)
                                            <option value="{{ $khachHang->ma_nguoi_dung }}"
                                                @if (in_array($khachHang->ma_nguoi_dung, $phieuGiamGia->ma_nguoi_dungs)) selected @endif>
                                                {{ $khachHang->ten }} - {{ $khachHang->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
