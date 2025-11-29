@extends('layouts.admin')

@section('title','Quản lý Đơn Hàng')
@section('content')
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Danh sách tất cả Đơn hàng</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Danh Sách Đơn hàng</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <p class="text-muted font-13 m-b-30">
                                        Trang quản lý cho phép Admin tạo mới, sửa và xóa các sản phẩm.
                                    </p>
                                    <table id="datatable-buttons" class="table table-striped table-bordered"
                                        style="width:100%; text-align: center;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>tài khoản</th>
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
                                            <tr>
                                                <td>{{$order->id}}</td>
                                                <td>{{$order->user->name}}</td>
                                                <td>
                                                    {{-- <a href="">{{optional($order->shippingAddress)->address ??
                                                        'Không có
                                                        địa chỉ' }}</a> --}}
                                                    <a href="" data-toggle="modal"
                                                        data-target="#addressShippingModal-{{$order->id}}">{{($order->shippingAddress->address)}}</a>
                                                </td>
                                                <td>{{number_format($order->total_price, 0, ',', '.')}} VND</td>
                                                <td class="order-status">
                                                    @if($order->status == 'pending')
                                                    <span class="custom-badge badge badge-warning">Đợi xác nhận</span>
                                                    @elseif($order->status == 'processing')
                                                    <span class="custom-badge badge badge-info">Đang giao</span>
                                                    @elseif($order->status == 'completed')
                                                    <span class="custom-badge badge badge-primary">Đã hoàn thành</span>
                                                    @elseif($order->status == 'canceled')
                                                    <span class="custom-badge badge badge-danger">Đã hủy</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->payment->status == 'pending')
                                                    <span class="custom-badge badge badge-danger">Chưa thanh toán</span>
                                                    @else
                                                    <span class="custom-badge badge badge-success">Đã thanh toán</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                                        data-target="#orderItemsModal-{{$order->id}}">Xem</button>
                                                </td>
                                                <td>
                                                    <!-- Split button -->
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-danger dropdown-toggle dropdown-toggle-split"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if($order->status == 'pending')
                                                            <a class="dropdown-item confirm-order" href="#"
                                                                data-id="{{$order->id}}">Xác nhận</a>
                                                            @endif
                                                            <a class="dropdown-item" target="_blank" href="{{route('admin.order-detail', ['id'=>$order->id])}}">Xem chi
                                                                tiết</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            @endforeach
                                        </tbody>
                                    </table>
                                    @foreach($orders as $order)
                                    <!-- Modal address -->
                                    <div class="modal fade" id="addressShippingModal-{{$order->id}}" tabindex="-1"
                                        aria-labelledby="addressShippingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addressShippingModalLabel">Thông tin
                                                        giao hàng

                                                    </h5>
                                                    <button type="button" class="btn-close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Người nhận:</strong>
                                                        {{$order->shippingAddress->full_name}}</p>
                                                    <p><strong>Địa chỉ:</strong> {{$order->shippingAddress->address}}
                                                    </p>
                                                    <p><strong>Thành phố:</strong>{{$order->shippingAddress->city}}</p>
                                                    <p><strong>Điện thoại:</strong> {{$order->shippingAddress->phone}}
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal OrderItems -->
                                    <div class="modal fade" id="orderItemsModal-{{$order->id}}" tabindex="-1"
                                        aria-labelledby="orderItemsModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderItemsModalLabel">Chi tiết hóa đơn

                                                    </h5>
                                                    <button type="button" class="btn-close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Tên Sản phẩm</th>
                                                                <th>Số lượng</th>
                                                                <th>Đơn Giá</th>
                                                                <th>Thành Tiền</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $index = 1; @endphp
                                                            @foreach($order->orderItems as $item)
                                                            <tr>
                                                                <td>{{$index++}}</td>
                                                                <td>{{$item->product->name}}</td>
                                                                <td>{{$item->quantity}}</td>
                                                                <td>{{number_format($item->price, 0, ',', '.')}} VND
                                                                </td>
                                                                <td>{{number_format($item->quantity * $item->price, 0,
                                                                    ',', '.')}} VND</td>
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
<!-- /page content -->
@endsection