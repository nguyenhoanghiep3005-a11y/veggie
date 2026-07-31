@extends('layouts.client')

@section('title', 'Tài khoản')
@section('breadcrumb', 'Tài khoản')

@section('content')
    @if ($errors->any())
        <div class="container">
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif
    <!-- Khu vực quản lý tài khoản khách hàng -->
    <div class="liton__wishlist-area pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__product-tab-area">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="ltn__tab-menu-list mb-50">
                                        <div class="nav">
                                            <a class="active show" data-bs-toggle="tab" href="#liton_tab_dashboard">Bảng điều khiển <i class="fas fa-home"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_orders">Đơn hàng <i class="fas fa-file-alt"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_address">Địa chỉ <i class="fas fa-map-marker-alt"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_account">Chi tiết tài khoản <i class="fas fa-user"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_password">Đổi mật khẩu <i class="fas fa-lock"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="liton_tab_dashboard">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Xin chào <strong>{{ $nguoiDung->email }}</strong> (<small><a href="{{ route('dang-xuat') }}">Đăng xuất</a></small>)</p>
                                                <p>Tại trang tài khoản, bạn có thể xem <span>đơn hàng gần đây</span>, quản lý <span>địa chỉ giao hàng</span>, và cập nhật <span>thông tin cá nhân hoặc mật khẩu</span>.</p>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="liton_tab_orders">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <div class="table-responsive account-orders-table">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Đơn hàng</th>
                                                                <th>Ngày đặt</th>
                                                                <th>Trạng thái</th>
                                                                <th>Tổng tiền</th>
                                                                <th>Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if ($donHangs->count() > 0)
                                                            @foreach ($donHangs as $donHang)
                                                                <tr>
                                                                    <td>#{{ $donHang->ma_don_hang }}</td>
                                                                    <td>{{ $donHang->created_at->format('d/m/Y') }}</td>
                                                                    <td>
                                                                        <span class="badge {{ $donHang->lopTrangThaiKhachHang() }}">
                                                                            {{ $donHang->tenTrangThai() }}
                                                                        </span>                                                                    </td>
                                                                    <td>{{ number_format($donHang->tong_tien, 0, ',', '.') }} đ</td>
                                                                    <td>
                                                                        <a href="{{ route('don-hang.chi-tiet', $donHang->ma_don_hang) }}" class="btn btn-sm btn-info">Xem chi tiết</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="5" class="text-center">Bạn chưa có đơn hàng nào.</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="liton_tab_address">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Các địa chỉ sau sẽ được sử dụng khi bạn thanh toán.</p>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead class="text-nowrap">
                                                            <tr>
                                                                <th>Tên người nhận</th>
                                                                <th>Địa chỉ </th>
                                                                <th>Số điện thoại</th>
                                                                <th>Mặc định</th>
                                                                <th>Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if ($diaChis->count() > 0)
                                                            @foreach ($diaChis as $diaChi)
                                                                <tr>
                                                                    <td>{{ $diaChi->ho_ten }}</td>
                                                                    <td>{{ $diaChi->dia_chi }}{{ $diaChi->tinh_thanh ? ', '.$diaChi->tinh_thanh : '' }}</td>
                                                                    <td>{{ $diaChi->so_dien_thoai }}</td>
                                                                    <td>
                                                                        @if ($diaChi->mac_dinh)
                                                                            <span class="badge bg-success">Mặc định</span>
                                                                        @else
                                                                            <form action="{{ route('tai-khoan.dia-chi.dat-mac-dinh', $diaChi->ma_dia_chi_giao_hang) }}" method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <button type="submit" class="btn btn-effect-1 btn-warning">Chọn</button>
                                                                            </form>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <form action="{{ route('tai-khoan.dia-chi.xoa', $diaChi->ma_dia_chi_giao_hang) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')">Xóa</button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="5" class="text-center">Bạn chưa có địa chỉ giao hàng.</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button class="btn theme-btn-1 btn-effect-1 mt-3" data-bs-toggle="modal" data-bs-target="#addAddressModal">Thêm địa chỉ mới</button>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content account-address-modal">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addAddressModalLabel">Thêm địa chỉ mới</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('tai-khoan.dia-chi.them') }}" method="POST" id="addAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="province_name" id="province_name">
                                                            <input type="hidden" name="district_name" id="district_name">
                                                            <input type="hidden" name="ward_name" id="ward_name">

                                                            <div class="mb-3">
                                                                <label for="ho_ten" class="form-label">Tên người nhận</label>
                                                                <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                                                <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="dia_chi" class="form-label">Địa chỉ cụ thể</label>
                                                                <input type="text" class="form-control" id="dia_chi" name="dia_chi" placeholder="Số nhà, tên đường" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="ma_tinh" class="form-label">Tỉnh/thành</label>
                                                                <div class="nice-select-wrapper">
                                                                    <select class="nice-select w-100" name="ma_tinh" id="ma_tinh">
                                                                        <option value="">--Chọn tỉnh/thành--</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="ma_huyen" class="form-label">Quận/huyện</label>
                                                                <div class="nice-select-wrapper">
                                                                    <select class="nice-select w-100" name="ma_huyen" id="ma_huyen" disabled>
                                                                        <option value="">--Chọn quận/huyện--</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="ma_xa" class="form-label">Phường/xã</label>
                                                                <div class="nice-select-wrapper">
                                                                    <select class="nice-select w-100" name="ma_xa" id="ma_xa" disabled>
                                                                        <option value="">--Chọn phường/xã--</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 form-check">
                                                                <input type="checkbox" class="form-check-input" id="mac_dinh" name="mac_dinh">
                                                                <label for="mac_dinh" class="form-label">Đặt làm địa chỉ mặc định</label>
                                                            </div>
                                                            <button type="submit" class="btn theme-btn-1 btn-effect-1 mt-3">Lưu địa chỉ</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="liton_tab_account">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <div class="ltn__form-box">
                                                    <form action="{{ route('tai-khoan.cap-nhat') }}" method="POST" id="update-account">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row mb-50">
                                                            <div class="col-md-6">
                                                                <label for="ltn__name">Họ và tên:</label>
                                                                <input type="text" name="ltn__name" id="ltn__name" value="{{ $nguoiDung->ten }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn__so_dien_thoai">Số điện thoại:</label>
                                                                <input type="number" name="ltn__so_dien_thoai" id="ltn__so_dien_thoai" value="{{ $nguoiDung->so_dien_thoai }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn__email">Email (không được thay đổi)</label>
                                                                <input type="text" name="ltn__email" id="ltn__email" value="{{ $nguoiDung->email }}" readonly>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn__address">Địa chỉ:</label>
                                                                <input type="text" name="ltn__address" id="ltn__address" value="{{ $nguoiDung->dia_chi }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="btn-wrapper">
                                                            <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase">Cập nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="liton_tab_password">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <div class="ltn__form-box">
                                                    <form action="{{ route('tai-khoan.doi-mat-khau') }}" method="POST" id="change-password-form">
                                                        @csrf
                                                        <fieldset>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label>Mật khẩu hiện tại:</label>
                                                                    <input type="password" name="current_password" required>
                                                                    <label>Mật khẩu mới:</label>
                                                                    <input type="password" name="new_password" required>
                                                                    <label>Nhập lại mật khẩu mới:</label>
                                                                    <input type="password" name="confirm_new_password" autocomplete="new-password" required>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="btn-wrapper">
                                                            <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase">Đổi mật khẩu</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
