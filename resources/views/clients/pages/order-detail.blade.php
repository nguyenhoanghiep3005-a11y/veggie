@extends('layouts.client')

@section('title','Chi tiết đơn hàng')
@section('breadcrumb','Chi tiết đơn hàng')
@section('content')
<div class="liton__shoping-cart-area mb-120">
    <div class="container mt-4">
        <h3>Chi tiết đơn hàng #{{$order->id}}</h3>
        <p>Ngày đặt:{{$order->created_at->format('d/m/Y')}} </p>
        <p>Trạng thái:
            @if ($order->status == 'pending')
            <span class="badge bg-warning">Chờ xác nhận</span>
            @elseif($order->status == 'processing')
            <span class="badge bg-primary">Đang xử lý</span>

            @elseif($order->status == 'completed')
            <span class="badge bg-success">Hoàn thành</span>

            @elseif($order->status == 'canceled')
            <span class="badge bg-danger">Đã hủy</span>
            @endif
        </p>
        <p>Phương thức thanh toán:
            @if ($order->payment && $order->payment->payment_method == 'cash')
            <span class="badge bg-primary">Thanh toán khi nhận hàng</span>
            @elseif($order->payment && $order->payment->payment_method == 'paypal')
            <span class="badge bg-primary">Thanh toán bằng Paypal</span>
            @else
            <span class="badge bg-danger">Chưa xác định</span>
            @endif
        </p>
        <p>Tổng tiền:
            {{number_format($order->total_price, 0, ',', '.')}} VNĐ

        </p>
        <h4 class="mt-4">Sản phẩm trong đơn hàng</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                <tr>
                    <td><img src="{{asset('storage/'. $item->product->image)}}" width="50"></td>
                    <td>{{$item->product->name}}</td>
                    <td>{{number_format($item->price, 0, ',', '.')}} đ</td>
                    <td>{{$item->quantity}}</td>
                    <td>{{number_format($item->price * $item->quantity, 0, ',', '.')}} đ
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <h4 class="mt-4">Thông tin giao hàng</h4>
        <p>Người nhận: {{$order->shippingAddress->full_name}}</p>
        <p>Địa chỉ:{{$order->shippingAddress->address}} </p>
        <p>Thành phố:{{$order->shippingAddress->city}} </p>
        <p>Số điện thoại: {{$order->shippingAddress->phone}}</p>

        @if ($order->status == 'pending')
        <form action="" method="POST" onsubmit="return confirm('Bạn có chắc chắn mún hủy đơn hàng này?');">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm mt-3">Hủy đơn hàng</button>
        </form>

        @endif
        @if ($order->status == 'completed')
        <h4 class="mt-4">Đánh giá sản phẩm</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                <tr>
                    <td>{{$item->product->name}}</td>
                    <td>
                        <a href="{{route('product.detail', $item->product->slug)}}"
                            class="btn theme-btn-1 btn-effect-1">Đánh giá</a>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection