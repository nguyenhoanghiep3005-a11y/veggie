@extends('layouts.admin')

@section('title', 'Quản lý mã giảm giá')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Quản lý mã giảm giá</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="x_panel admin-coupon-panel">
        <div class="x_title">
            <h2>Thêm mã mới</h2>
            <div class="clearfix"></div>
        </div>

        <div class="x_content">
            <form method="POST" action="{{ route('admin.coupons.store') }}" class="admin-coupon-form">
                @csrf

                <div class="row">
                    <div class="col-md-6 admin-coupon-column">
                        <div class="form-group">
                            <label>Mã voucher *</label>
                            <input name="code" class="form-control" value="{{ old('code') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Phần trăm giảm (%) *</label>
                            <input type="number" name="discount_percent" class="form-control" value="{{ old('discount_percent') }}" min="0.01" max="100" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label>Thời hạn</label>
                            <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                        </div>

                        <div class="admin-coupon-status">
                            <label>
                                <input type="checkbox" name="is_active" value="1" checked>
                                Đang hoạt động
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 admin-coupon-column">
                        <div class="form-group">
                            <label>Đơn tối thiểu</label>
                            <input type="number" name="minimum_order_amount" class="form-control" value="{{ old('minimum_order_amount', 0) }}" min="0" step="1000">
                        </div>

                        <div class="form-group">
                            <label>Giảm tối đa</label>
                            <input type="number" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount') }}" min="0" step="1000">
                        </div>

                        <div class="form-group">
                            <label>Giới hạn lượt dùng</label>
                            <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}" min="1">
                        </div>

                        <div class="form-group">
                            <label>Phạm vi sử dụng</label>
                            <select name="apply_type" class="form-control js-coupon-apply-type">
                                <option value="all" @if(old('apply_type', 'all') == 'all') selected @endif>Tất cả khách hàng</option>
                                <option value="customer" @if(old('apply_type') == 'customer') selected @endif>Tài khoản riêng</option>
                            </select>
                        </div>

                        <div class="form-group admin-coupon-customer-box {{ old('apply_type', 'all') == 'customer' ? '' : 'd-none' }}">
                            <label>Tài khoản được nhận voucher</label>
                            <select name="customer_ids[]" class="form-control" multiple size="5">
                                @foreach($customers as $customer)
                                    @php
                                        $selectedCustomer = false;
                                        $oldCustomers = old('customer_ids', []);

                                        if (in_array($customer->id, $oldCustomers)) {
                                            $selectedCustomer = true;
                                        }
                                    @endphp
                                    <option value="{{ $customer->id }}" @if($selectedCustomer) selected @endif>
                                        {{ $customer->name }} - {{ $customer->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="admin-coupon-submit">
                    <button class="btn btn-success">Thêm mã</button>
                </div>
            </form>
        </div>
    </div>

    <div class="x_panel">
        <div class="x_title">
            <h2>Danh sách mã giảm giá</h2>
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
                    @if(count($coupons) > 0)
                        @foreach($coupons as $coupon)
                            @php
                                $assignedIds = [];
                                foreach ($coupon->users as $user) {
                                    $assignedIds[] = $user->id;
                                }

                                $discount = number_format($coupon->discount_percent, 2, '.', '');
                                $discount = rtrim($discount, '0');
                                $discount = rtrim($discount, '.');

                                $minimumOrder = 'Không yêu cầu';
                                if ($coupon->minimum_order_amount > 0) {
                                    $minimumOrder = 'Từ '.number_format($coupon->minimum_order_amount, 0, ',', '.').' đ';
                                }

                                $expiresAt = 'Không giới hạn';
                                $expiresAtForm = '';
                                if ($coupon->expires_at) {
                                    $expiresAt = $coupon->expires_at->format('d/m/Y H:i');
                                    $expiresAtForm = $coupon->expires_at->format('Y-m-d\TH:i');
                                }

                                $usageLimit = 'Không giới hạn';
                                if ($coupon->usage_limit) {
                                    $usageLimit = $coupon->usage_limit;
                                }

                                $statusClass = 'danger';
                                $statusText = 'Hết hiệu lực';
                                if ($coupon->isUsable()) {
                                    $statusClass = 'success';
                                    $statusText = 'Đang kích hoạt';
                                }
                            @endphp
                            <tr>
                                <td><strong>{{ $coupon->code }}</strong></td>
                                <td>
                                    {{ $discount }}%
                                    @if($coupon->max_discount_amount)
                                        <br><small>Tối đa {{ number_format($coupon->max_discount_amount, 0, ',', '.') }} đ</small>
                                    @endif
                                </td>
                                <td>{{ $minimumOrder }}</td>
                                <td>
                                    @if($coupon->apply_type == 'customer')
                                        Tài khoản riêng
                                        <br><small>{{ count($assignedIds) }} khách</small>
                                    @else
                                        Tất cả khách hàng
                                    @endif
                                </td>
                                <td>{{ $expiresAt }}</td>
                                <td>{{ $coupon->used_count }}</td>
                                <td>{{ $usageLimit }}</td>
                                <td>
                                    <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#coupon-edit-{{ $coupon->id }}">
                                        Sửa
                                    </button>

                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="d-inline" onsubmit="return confirm('Xóa hoặc khóa mã này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="coupon-edit-{{ $coupon->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="admin-coupon-form">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h4>Sửa mã {{ $coupon->code }}</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 admin-coupon-column">
                                                        <div class="form-group">
                                                            <label>Mã voucher</label>
                                                            <input name="code" class="form-control" value="{{ $coupon->code }}" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Phần trăm giảm (%)</label>
                                                            <input type="number" name="discount_percent" class="form-control" value="{{ $coupon->discount_percent }}" min="0.01" max="100" step="0.01" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Thời hạn</label>
                                                            <input type="datetime-local" name="expires_at" class="form-control" value="{{ $expiresAtForm }}">
                                                        </div>

                                                        <div class="admin-coupon-status">
                                                            <label>
                                                                <input type="checkbox" name="is_active" value="1" @if($coupon->is_active) checked @endif>
                                                                Đang hoạt động
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 admin-coupon-column">
                                                        <div class="form-group">
                                                            <label>Đơn tối thiểu</label>
                                                            <input type="number" name="minimum_order_amount" class="form-control" value="{{ $coupon->minimum_order_amount }}" min="0" step="1000">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Giảm tối đa</label>
                                                            <input type="number" name="max_discount_amount" class="form-control" value="{{ $coupon->max_discount_amount }}" min="0" step="1000">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Giới hạn lượt dùng</label>
                                                            <input type="number" name="usage_limit" class="form-control" value="{{ $coupon->usage_limit }}" min="1">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Phạm vi sử dụng</label>
                                                            <select name="apply_type" class="form-control js-coupon-apply-type">
                                                                <option value="all" @if($coupon->apply_type == 'all') selected @endif>Tất cả khách hàng</option>
                                                                <option value="customer" @if($coupon->apply_type == 'customer') selected @endif>Tài khoản riêng</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group admin-coupon-customer-box {{ $coupon->apply_type == 'customer' ? '' : 'd-none' }}">
                                                            <label>Tài khoản được nhận voucher</label>
                                                            <select name="customer_ids[]" class="form-control" multiple size="5">
                                                                @foreach($customers as $customer)
                                                                    @php
                                                                        $selectedCustomer = false;
                                                                        if (in_array($customer->id, $assignedIds)) {
                                                                            $selectedCustomer = true;
                                                                        }
                                                                    @endphp
                                                                    <option value="{{ $customer->id }}" @if($selectedCustomer) selected @endif>
                                                                        {{ $customer->name }} - {{ $customer->email }}
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
                    @else
                        <tr>
                            <td colspan="9">Chưa có mã giảm giá.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
