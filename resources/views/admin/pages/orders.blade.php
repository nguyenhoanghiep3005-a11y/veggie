@extends('layouts.admin')

@section('title','Quản lý Đơn Hàng')
@section('content')
<div class="right_col admin-orders-page" role="main"
    data-confirm-url="{{ route('admin.order.confirm') }}"
    data-ship-url="{{ route('admin.order.ship') }}"
    data-cancel-url="{{ route('admin.order.cancel') }}">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Danh sách tất cả đơn hàng</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Danh sách đơn hàng</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <p class="text-muted font-13 m-b-30">
                                        Theo dõi đơn hàng và trạng thái xử lý hàng lỗi ngay trên chính đơn đó.
                                    </p>
                                    <table id="datatable-responsive" class="table table-striped table-bordered admin-table-centered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tài khoản</th>
                                                <th>Thông tin người đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái đơn hàng</th>
                                                <th>Trạng thái thanh toán</th>
                                                <th>Chi tiết đơn hàng</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                @php
                                                    $shippingAddress = $order->shippingAddress;
                                                    $customerName = 'Khách vãng lai';
                                                    $addressText = 'Không có địa chỉ';
                                                    $shippingName = '—';
                                                    $shippingStreet = '—';
                                                    $shippingCity = '—';
                                                    $shippingPhone = '—';
                                                    $paymentStatus = '';

                                                    if ($order->user) {
                                                        $customerName = $order->user->name;
                                                    } elseif ($shippingAddress && $shippingAddress->full_name) {
                                                        $customerName = $shippingAddress->full_name . ' (Khách)';
                                                    }

                                                    if ($shippingAddress) {
                                                        if ($shippingAddress->address) {
                                                            $addressText = $shippingAddress->address;
                                                        }
                                                        $shippingName = $shippingAddress->full_name;
                                                        $shippingStreet = $shippingAddress->address;
                                                        $shippingCity = $shippingAddress->city;
                                                        $shippingPhone = $shippingAddress->phone;
                                                    }

                                                    if ($order->payment) {
                                                        $paymentStatus = $order->payment->status;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $order->id }}</td>
                                                    <td>{{ $customerName }}</td>
                                                    <td>
                                                        <a href="" data-toggle="modal" data-target="#addressShippingModal-{{ $order->id }}">
                                                            {{ $addressText }}
                                                        </a>
                                                    </td>
                                                    <td>{{ number_format($order->total_price, 0, ',', '.') }} <small>đ</small></td>
                                                    <td class="order-status">
                                                        @include('admin.pages.partials.order_status_badge', ['status' => $order->status, 'order' => $order])
                                                    </td>
                                                    <td>
                                                        @if($paymentStatus == 'pending')
                                                            <span class="custom-badge badge badge-danger">Chưa thanh toán</span>
                                                        @elseif($paymentStatus == 'completed')
                                                            <span class="custom-badge badge badge-success">Đã thanh toán</span>
                                                        @else
                                                            <span class="custom-badge badge badge-secondary">Chưa có thanh toán</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#orderItemsModal-{{ $order->id }}">Xem</button>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                            <div class="dropdown-menu">
                                                                @if($order->status == 'pending')
                                                                    <a class="dropdown-item confirm-order" href="#" data-id="{{ $order->id }}">Xác nhận</a>
                                                                @endif
                                                                @if($order->status == 'confirmed')
                                                                    <a class="dropdown-item ship-order" href="#" data-id="{{ $order->id }}">Giao hàng</a>
                                                                @endif
                                                                @if(in_array($order->status, ['pending', 'confirmed', 'shipping'], true))
                                                                    <a class="dropdown-item cancel-order text-danger" href="#" data-id="{{ $order->id }}">Hủy đơn</a>
                                                                @endif
                                                                <a class="dropdown-item" target="_blank" href="{{ route('admin.order-detail', ['id' => $order->id]) }}">Xem chi tiết</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    @foreach($orders as $order)
                                        @php
                                            $shippingAddress = $order->shippingAddress;
                                            $shippingName = '—';
                                            $shippingStreet = '—';
                                            $shippingCity = '—';
                                            $shippingPhone = '—';

                                            if ($shippingAddress) {
                                                $shippingName = $shippingAddress->full_name;
                                                $shippingStreet = $shippingAddress->address;
                                                $shippingCity = $shippingAddress->city;
                                                $shippingPhone = $shippingAddress->phone;
                                            }
                                        @endphp
                                        <div class="modal fade" id="addressShippingModal-{{ $order->id }}" tabindex="-1" aria-labelledby="addressShippingModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addressShippingModalLabel">Thông tin giao hàng</h5>
                                                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Người nhận:</strong> {{ $shippingName }}</p>
                                                        <p><strong>Địa chỉ:</strong> {{ $shippingStreet }}</p>
                                                        <p><strong>Thành phố:</strong> {{ $shippingCity }}</p>
                                                        <p><strong>Điện thoại:</strong> {{ $shippingPhone }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="orderItemsModal-{{ $order->id }}" tabindex="-1" aria-labelledby="orderItemsModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="orderItemsModalLabel">Chi tiết hóa đơn</h5>
                                                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Tên sản phẩm</th>
                                                                    <th>Số lượng</th>
                                                                    <th>Đơn giá</th>
                                                                    <th>Thành tiền</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php $index = 1; @endphp
                                                                @foreach($order->orderItems as $item)
                                                                    @php
                                                                        $productName = 'Sản phẩm đã bị xóa';
                                                                        if ($item->product) {
                                                                            $productName = $item->product->name;
                                                                        }
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $index++ }}</td>
                                                                        <td>{{ $productName }}</td>
                                                                        <td>{{ $item->quantity }}</td>
                                                                        <td>{{ number_format($item->price, 0, ',', '.') }} <small>đ</small></td>
                                                                        <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }} <small>đ</small></td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrderModalLabel">Hủy đơn hàng</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancel-order-id">
                <div class="form-group">
                    <label for="cancel-reason"><strong>Lý do hủy đơn hàng</strong> <span class="text-danger">*</span></label>
                    <textarea id="cancel-reason" class="form-control" rows="3" placeholder="Nhập lý do hủy đơn hàng..." required></textarea>
                    <small class="text-danger d-none" id="cancel-reason-error">Vui lòng nhập lý do hủy đơn hàng.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" id="confirm-cancel-order">Xác nhận h?y</button>
            </div>
        </div>
    </div>
</div>
@endsection
