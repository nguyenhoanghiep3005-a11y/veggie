@extends('layouts.client')

@section('title', 'Tài khoản')
@section('breadcrumb', 'Tài Khoản')

@section('content')
    <!-- WISHLIST AREA START -->
    <div class="liton__wishlist-area pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- PRODUCT TAB AREA START -->
                    <div class="ltn__product-tab-area">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="ltn__tab-menu-list mb-50">
                                        <div class="nav">
                                            <a class="active show" data-bs-toggle="tab" href="#liton_tab_dashboard">Bản điều
                                                khiển <i class="fas fa-home"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_orders">Đơn hàng <i
                                                    class="fas fa-file-alt"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_address">Địa chỉ <i
                                                    class="fas fa-map-marker-alt"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_account">Chi tiết tài khoản <i
                                                    class="fas fa-user"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_password">Đổi mật khẩu <i
                                                    class="fas fa-user"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="liton_tab_dashboard">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Hello <strong>{{ $user->email }}</strong> (not
                                                    <strong>{{ $user->email }}</strong>?
                                                    <small><a href="{{ route('logout') }}">Đăng xuất</a></small> )
                                                </p>
                                                <p>Tại trang tài khoản, bạn có thể xem <span>các đơn hàng gần đây</span>,
                                                    quản lý <span>địa chỉ giao hàng và thanh toán</span>, và <span>cập nhật
                                                        mật khẩu hoặc thông tin cá nhân</span>.</p>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_orders">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <div class="table-responsive"
                                                    style="overflow-x: auto; overflow-y: scroll; max-height: 400px;">
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
                                                            @foreach ($orders as $order)
                                                                <tr>
                                                                    <td>#{{ $order->id }}</td>
                                                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                                    <td>
                                                                        @if ($order->status == 'pending')
                                                                            <span class="badge bg-warning">Chờ xác
                                                                                nhận</span>
                                                                        @elseif($order->status == 'processing')
                                                                            <span class="badge bg-primary">Đang xử lý</span>
                                                                        @elseif($order->status == 'completed')
                                                                            <span class="badge bg-success">Hoàn thành</span>
                                                                        @elseif($order->status == 'canceled')
                                                                            <span class="badge bg-danger">Đã hủy</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ number_format($order->total_price, 0, ',', '.') }}
                                                                        đ
                                                                    </td>
                                                                    <td><a href="{{ route('order.show', $order->id) }}"
                                                                            class="btn btn-sm btn-info">Xem chi tiết</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="liton_tab_address">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Các địa chỉ sau sẽ được sử dụng mặc định trên trang thanh toán.</p>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead style="white-space: nowrap;">
                                                            <tr>
                                                                <th>Tên người nhận</th>
                                                                <th>Địa chỉ</th>
                                                                <th>Thành phố</th>
                                                                <th>Số điện thoại</th>
                                                                <th>Mặc định</th>
                                                                <th>Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($addresses as $address)
                                                                <tr>
                                                                    <td>{{ $address->full_name }}</td>
                                                                    <td>{{ $address->address }}</td>
                                                                    <td>{{ $address->city }}</td>
                                                                    <td>{{ $address->phone }}</td>
                                                                    <td>
                                                                        @if ($address->default)
                                                                            <span class="badge bg-success">Mặc định</span>
                                                                        @else
                                                                            <form
                                                                                action="{{ route('account.addresses.update', $address->id) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <button
                                                                                    class="btn btn-effect-1 btn-warning">Chọn</button>
                                                                            </form>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <form
                                                                            action="{{ route('account.addresses.delete', $address->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-danger"
                                                                                onclick="return confirm('Bạn có chắc muốn xóa')">Xóa</button>
                                                                        </form>

                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button class="btn theme-btn-1 btn-effect-1 mt-3" data-bs-toggle="modal"
                                                    data-bs-target="#addAddressModal">Thêm địa chỉ mới</button>
                                            </div>
                                        </div>
                                        <!-- Modal -->
                                        <div class="modal fade" id="addAddressModal" tabindex="-1"
                                            aria-labelledby="addAddressModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content" style="padding: 5px 10px ">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addAddressModalLabel">Thêm địa chỉ mới
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('account.addresses.add') }}" method="POST"
                                                            id="addAddressForm">
                                                            @csrf
                                                            <input type="hidden" name="province_name" id="province_name">
                                                            <input type="hidden" name="district_name" id="district_name">
                                                            <input type="hidden" name="ward_name" id="ward_name">

                                                            <div class="mb-3">
                                                                <label for="full_name" class="form-label">Tên người
                                                                    dùng</label>
                                                                <input type="text" class="form-control" id="full_name"
                                                                    name="full_name" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="address" class="form-label">Địa chỉ cụ thể (Số nhà, tên đường)</label>
                                                                <input type="text" class="form-control" id="address"
                                                                    name="address" required>
                                                            </div>
                                                            <!-- <div class="mb-3">
                                                                <label for="city" class="form-label">Thành phố</label>
                                                                <input type="text" class="form-control" id="city"
                                                                    name="city" required>
                                                            </div> -->
                                                            <div class="mb-3">
                                                                <label for="city" class="form-label">Chọn tỉnh/thành
                                                                    (GHN)</label>
                                                                <br>
                                                                <select class="form-control" name="province_id"
                                                                    id="province_id">
                                                                    <option value="">--Chọn tỉnh/thành--</option>
                                                                </select>
                                                                <br>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="city" class="form-label">Chọn quận/huyện
                                                                    (GHN)</label>
                                                                <br>
                                                                <select class="form-control" name="district_id"
                                                                    id="district_id">
                                                                    <option value="">--Chọn quận/huyện--</option>
                                                                </select>
                                                                <br>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="city" class="form-label">Chọn phường/xã
                                                                    (GHN)</label>
                                                                <br>
                                                                <select class="form-control" name="ward_id"
                                                                    id="ward_id">
                                                                    <option value="">--Chọn phường/xã--</option>
                                                                </select>
                                                                <br>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="phone" class="form-label">Số điện
                                                                    thoại</label>
                                                                <input type="text" class="form-control" id="phone"
                                                                    name="phone" required>
                                                            </div>
                                                            <div class="mb-3 form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="default" name="default" >
                                                                <label for="default" class="form-label">Đặt làm địa chỉ
                                                                    mặt
                                                                    định</label>
                                                            </div>
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 mt-3">lưu địa
                                                                chỉ</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_account">
                                            <div class="ltn__myaccount-tab-content-inner">

                                                <div class="ltn__form-box">
                                                    <form action="{{ route('account.update') }}" method="POST"
                                                        id="update-account" enctype="multipart/form-data">
                                                        @method('PUT')
                                                        <div class="row mb-50">
                                                            <div class="col-md-12 text-center mb-3">
                                                                <div class="profile-pic-container">
                                                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                                                        alt="Avata" id="preview-image"
                                                                        class="profile-pic">
                                                                    <input type="file" name="avatar" id="avatar"
                                                                        accept="image/*" class="d-none">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label for="ltn__name">Họ và tên:</label>
                                                                <input type="text" name="ltn__name" id="ltn__name"
                                                                    value="{{ $user->name }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn__phone_number">Số điện thoại:</label>
                                                                <input type="number" name="ltn__phone_number"
                                                                    id="ltn__phone_number"
                                                                    value="{{ $user->phone_number }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn__email">Email (Không được thay
                                                                    đổi email)</label>
                                                                <input type="text" name="ltn__email" id="ltn__email"
                                                                    value="{{ $user->email }}" readonly>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn__address">Địa chỉ:</label>
                                                                <input type="text" name="ltn__address"
                                                                    id="ltn__address" value="{{ $user->address }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="btn-wrapper">
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 text-uppercase">Cập
                                                                nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_password">
                                            <div class="ltn__myaccount-tab-content-inner">

                                                <div class="ltn__form-box">
                                                    <form action="{{ route('account.change-password') }}" method="POST"
                                                        id="change-password-form">
                                                        <fieldset>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label>Mật khẩu hiện tại:</label>
                                                                    <input type="password" name="current_password"
                                                                        required>
                                                                    <label>Mật khẩu mới:</label>
                                                                    <input type="password" name="new_password" required>
                                                                    <label>Nhập lại mật khẩu mới:</label>
                                                                    <input type="password" name="confirm_new_password"
                                                                        autocomplete="new-password" required>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div class="btn-wrapper">
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 text-uppercase">Đổi mật
                                                                khẩu</button>
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
                    <!-- PRODUCT TAB AREA END -->
                </div>
            </div>
        </div>
    </div>
    <!-- WISHLIST AREA START -->

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            });
        </script>
    @endif
@endsection
