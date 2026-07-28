@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
@php
    $returnRequest = $order->returnRequest;
    $returnItems = [];

    if ($returnRequest && is_array($returnRequest->items)) {
        foreach ($returnRequest->items as $returnItem) {
            if (isset($returnItem['order_item_id'])) {
                $returnItems[$returnItem['order_item_id']] = $returnItem;
            }
        }
    }

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

    $userName = 'Khách vãng lai';
    $userEmail = '—';
    if ($order->user) {
        $userName = $order->user->name;
        $userEmail = $order->user->email;
    }

    $paymentMethod = '';
    $paymentStatus = '';
    if ($order->payment) {
        $paymentMethod = $order->payment->payment_method;
        $paymentStatus = $order->payment->status;
    }
@endphp

<div class="right_col admin-order-detail-page" role="main"
     data-confirm-url="{{ route('admin.order.confirm') }}"
     data-ship-url="{{ route('admin.order.ship') }}"
     data-complete-url="{{ route('admin.order.updateStatus') }}"
     data-cancel-url="{{ route('admin.order.cancel') }}"
     data-return-approve-url="{{ route('admin.order-returns.approve', ['orderReturn' => '__ID__']) }}"
     data-return-receive-url="{{ route('admin.order-returns.receive', ['orderReturn' => '__ID__']) }}"
     data-return-complete-url="{{ route('admin.order-returns.complete', ['orderReturn' => '__ID__']) }}">
    <div class="page-title">
        <div class="title_left">
            <h3>Chi tiết đơn hàng #{{ $order->id }}</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    <div id="alert-box"></div>

    <div class="x_panel">
        <div class="x_title">
            <h2>Thông tin đơn hàng</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <section class="content invoice">
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <strong>Từ cửa hàng</strong>
                        <address>
                            <strong>HIEP SHOP</strong><br>
                            Tân Phú, Hồ Chí Minh, VN<br>
                            Phone: 0388536385<br>
                            Email: nguyenhoanghiep@gmail.com
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <strong>Người nhận</strong>
                        <address>
                            <strong>{{ $shippingName }}</strong><br>
                            {{ $shippingStreet }}<br>
                            {{ $shippingCity }}<br>
                            Phone: {{ $shippingPhone }}
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>Mã đơn: #{{ $order->id }}</b><br>
                        <b>Ngày tạo:</b> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                        <b>Tài khoản:</b> {{ $userName }}<br>
                        <b>Email:</b> {{ $userEmail }}<br>
                        <b>Trạng thái:</b>
                        @include('admin.pages.partials.order_status_badge', ['status' => $order->status, 'order' => $order])
                    </div>
                </div>

                <hr>

                @if($returnRequest)
                    @php
                        $requestedAt = '—';
                        $approvedAt = '—';
                        $receivedAt = '—';

                        if ($returnRequest->requested_at) {
                            $requestedAt = $returnRequest->requested_at->format('d/m/Y H:i');
                        }
                        if ($returnRequest->approved_at) {
                            $approvedAt = $returnRequest->approved_at->format('d/m/Y H:i');
                        }
                        if ($returnRequest->received_at) {
                            $receivedAt = $returnRequest->received_at->format('d/m/Y H:i');
                        }
                    @endphp
                    <div class="x_panel">
                        <div class="x_title">
                            <h2><i class="fa fa-refresh"></i> Thông tin đổi/trả hàng lỗi trong đơn</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-md-8">
                                    <p>
                                        <strong>Trạng thái xử lý:</strong>
                                        <span class="{{ $returnRequest->statusClass() }}">
                                            {{ $returnRequest->statusLabel() }}
                                        </span>
                                    </p>
                                    <p><strong>Khách mô tả lỗi:</strong> {{ $returnRequest->description ? $returnRequest->description : '—' }}</p>
                                    <p><strong>Ngày gửi yêu cầu:</strong> {{ $requestedAt }}</p>
                                    <p><strong>Ngày duyệt:</strong> {{ $approvedAt }}</p>
                                    <p><strong>Ngày shop nhận hàng lỗi:</strong> {{ $receivedAt }}</p>
                                </div>
                                <div class="col-md-4">
                                    @if($order->status == 'return_requested')
                                        <button class="btn btn-primary btn-block approve-return-btn" data-id="{{ $returnRequest->id }}">
                                            <i class="fa fa-check"></i> Duyệt yêu cầu
                                        </button>
                                    @elseif($order->status == 'return_pickup')
                                        <div class="alert alert-info">
                                            Khi xác nhận đã nhận hàng lỗi, hệ thống sẽ trừ tồn sản phẩm mới để giao đổi cho khách.
                                        </div>
                                        <button class="btn btn-success btn-block receive-return-btn" data-id="{{ $returnRequest->id }}">
                                            <i class="fa fa-inbox"></i> Xác nhận đã nhận hàng lỗi
                                        </button>
                                    @elseif($order->status == 'replacement_shipping')
                                        <button class="btn btn-success btn-block complete-return-btn" data-id="{{ $returnRequest->id }}">
                                            <i class="fa fa-check-circle"></i> Hoàn tất yêu cầu đổi
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <h4>Sản phẩm khách báo lỗi/hư</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Ảnh</th>
                                            <th>Sản phẩm</th>
                                            <th>Đã mua</th>
                                            <th>Số lượng lỗi/hư</th>
                                            <th>Giao sản phẩm đổi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderItems as $orderItem)
                                            @php
                                                $requestedItem = null;
                                                $requestedQuantity = 0;
                                                $productImage = asset('storage/uploads/products/default.png');
                                                $productName = 'Sản phẩm đã xóa';

                                                if (isset($returnItems[$orderItem->id])) {
                                                    $requestedItem = $returnItems[$orderItem->id];
                                                }
                                                if ($requestedItem && isset($requestedItem['quantity'])) {
                                                    $requestedQuantity = (int) $requestedItem['quantity'];
                                                }
                                                if ($orderItem->product) {
                                                    $productImage = $orderItem->product->image_url;
                                                    $productName = $orderItem->product->display_name;
                                                }
                                            @endphp
                                            @if($requestedItem)
                                                <tr>
                                                    <td><img src="{{ $productImage }}" width="55" height="55" alt="Sản phẩm"></td>
                                                    <td class="text-left">{{ $productName }}</td>
                                                    <td>{{ $orderItem->quantity }}</td>
                                                    <td>{{ $requestedQuantity }}</td>
                                                    <td>
                                                        @if(!empty($requestedItem['replacement_allocations']))
                                                            <span class="label label-success">Đã trừ tồn để giao đổi</span>
                                                        @else
                                                            <span class="label label-default">Chưa xuất hàng đổi</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <h4>Ảnh/video minh chứng của khách</h4>
                            @if(!empty($returnRequest->media))
                                <div class="row">
                                    @foreach($returnRequest->media as $media)
                                        @php
                                            $mediaUrl = app(\App\Services\CloudinaryService::class)->mediaUrl($media);
                                            $mediaName = '';

                                            if (isset($media['original_name'])) {
                                                $mediaName = $media['original_name'];
                                            } elseif (isset($media['path'])) {
                                                $mediaName = basename($media['path']);
                                            }
                                        @endphp
                                        <div class="col-md-3 order-return-media">
                                            <a href="{{ $mediaUrl }}" target="_blank" class="btn btn-default btn-xs btn-block">
                                                <i class="fa fa-external-link"></i> Xem minh chứng {{ $loop->iteration }}
                                            </a>
                                            <small class="text-muted">{{ $mediaName }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-danger">Yêu cầu chưa có minh chứng.</p>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                @php
                                    $product = $item->product;
                                    $productImage = asset('storage/uploads/products/default.png');
                                    $productName = 'Sản phẩm đã xóa';
                                    $itemTotal = $item->quantity * $item->price;

                                    if ($product) {
                                        $productImage = $product->image_url;
                                        $productName = $product->display_name;
                                    }
                                @endphp
                                <tr>
                                    <td><img src="{{ $productImage }}" width="50" height="50" alt="Sản phẩm"></td>
                                    <td>{{ $productName }}</td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }} <small>đ</small></td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($itemTotal, 0, ',', '.') }} <small>đ</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="lead">Phương thức thanh toán</p>
                        @if($paymentMethod == 'paypal')
                            <p><span class="label label-success">Đã thanh toán qua PayPal</span></p>
                        @else
                            <p><span class="label label-warning">Thanh toán khi nhận hàng (COD)</span></p>
                            <p>
                                @if($paymentStatus == 'completed')
                                    <span class="label label-success">Đã thanh toán</span>
                                @else
                                    <span class="label label-danger">Chưa thanh toán</span>
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Tiền hàng:</th>
                                    <td>{{ number_format($order->subtotal, 0, ',', '.') }} <small>đ</small></td>
                                </tr>
                                <tr>
                                    <th>{{ $order->shippingFeeLabel() }}:</th>
                                    <td>{{ number_format($order->shipping_fee, 0, ',', '.') }} <small>đ</small></td>
                                </tr>
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <th>Giám gi?:</th>
                                        <td>-{{ number_format($order->discount_amount, 0, ',', '.') }} <small>đ</small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>{{ $order->totalLabel() }}:</th>
                                    <td><strong>{{ number_format($order->total_price, 0, ',', '.') }} <small>đ</small></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row no-print">
                    <div class="col-md-12">
                        <button class="btn btn-default" onclick="window.print();">
                            <i class="fa fa-print"></i> In hóa đơn
                        </button>

                        @if($order->status == 'pending')
                            <button class="btn btn-primary pull-right confirm-order-btn" data-id="{{ $order->id }}">
                                <i class="fa fa-check"></i> Xác nhận đơn hàng
                            </button>
                            <button class="btn btn-danger pull-right cancel-order-btn" data-id="{{ $order->id }}">
                                <i class="fa fa-times"></i> Hủy đơn hàng
                            </button>
                        @elseif($order->status == 'confirmed')
                            <button class="btn btn-info pull-right ship-order-btn" data-id="{{ $order->id }}">
                                <i class="fa fa-truck"></i> Giao hàng
                            </button>
                            <button class="btn btn-danger pull-right cancel-order-btn" data-id="{{ $order->id }}">
                                <i class="fa fa-times"></i> Hủy đơn hàng
                            </button>
                        @elseif($order->status == 'shipping')
                            <button class="btn btn-success pull-right complete-order-btn" data-id="{{ $order->id }}">
                                <i class="fa fa-flag"></i> Đánh dấu giao thành công
                            </button>
                            <button class="btn btn-danger pull-right cancel-order-btn" data-id="{{ $order->id }}">
                                <i class="fa fa-times"></i> Hủy đơn hàng
                            </button>
                        @elseif($order->status == 'canceled')
                            <button class="btn btn-danger" disabled>
                                <i class="fa fa-info"></i> Đơn hàng đã hủy
                            </button>
                        @endif
                    </div>
                </div>

                @if($order->status == 'canceled' && $order->cancel_reason)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <strong><i class="fa fa-info-circle"></i> Thông tin hủy đơn:</strong><br>
                                <strong>Người hủy:</strong> {{ $order->canceled_by == 'admin' ? 'Quản trị viên' : 'Khách hàng' }}<br>
                                <strong>Lý do:</strong> {{ $order->cancel_reason }}
                            </div>
                        </div>
                    </div>
                @endif

            </section>
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
                <button type="button" class="btn btn-danger" id="confirm-cancel-order">Xác nhận hủy</button>
            </div>
        </div>
    </div>
</div>

@endsection
