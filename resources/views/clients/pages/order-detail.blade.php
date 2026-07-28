@extends('layouts.client')

@section('title', 'Chi tiết đơn hàng')
@section('breadcrumb', 'Chi tiết đơn hàng')

@section('content')
<div class="liton__shoping-cart-area mb-120">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h3 class="mb-1">Chi tiết đơn hàng #{{ $order->id }}</h3>
                <div class="text-muted">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <span class="badge {{ $order->clientStatusClass() }}">
                {{ $order->clientStatusLabel() }}
            </span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($returnRequest)
            @php
                $received = in_array($order->status, ['replacement_shipping', 'replacement_completed'], true);
                $finished = $order->status == 'replacement_completed';
                $requestedAt = '—';

                if ($returnRequest->requested_at) {
                    $requestedAt = $returnRequest->requested_at->format('d/m/Y H:i');
                }
            @endphp
            <div class="card border-warning mb-4">
                <div class="card-body">
                    <h4>{{ $returnRequest->typeLabel() }}</h4>
                    <p class="text-muted">Gửi lúc {{ $requestedAt }}</p>
                    <p><strong>Trạng thái:</strong> {{ $order->clientStatusLabel() }}</p>
                    <p><strong>Nội dung:</strong> {{ $returnRequest->description ? $returnRequest->description : '—' }}</p>

                    <h5>Sản phẩm đã gửi yêu cầu xử lý</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đã mua</th>
                                    <th>Số lượng lỗi/hư</th>
                                    <th>Trạng thái hàng đổi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $orderItem)
                                    @php
                                        $requestedItem = null;
                                        $requestedQuantity = 0;
                                        $productName = 'Sản phẩm đã xóa';

                                        if (isset($returnItems[$orderItem->id])) {
                                            $requestedItem = $returnItems[$orderItem->id];
                                        }

                                        if ($requestedItem && isset($requestedItem['quantity'])) {
                                            $requestedQuantity = (int) $requestedItem['quantity'];
                                        }

                                        if ($orderItem->product) {
                                            $productName = $orderItem->product->display_name;
                                        }
                                    @endphp
                                    @if($requestedItem)
                                        <tr>
                                            <td>{{ $productName }}</td>
                                            <td>{{ $orderItem->quantity }}</td>
                                            <td>{{ $requestedQuantity }}</td>
                                            <td>
                                                @if(!empty($requestedItem['replacement_allocations']))
                                                    Đã xuất sản phẩm đổi cho khách
                                                @else
                                                    Chưa xuất sản phẩm đổi
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(!empty($returnRequest->media))
                        <h5>Ảnh/video minh chứng đã gửi</h5>
                        <div class="row mb-3">
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
                                <div class="col-md-3 mb-2">
                                    <a href="{{ $mediaUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100 mt-1">
                                        Xem minh chứng {{ $loop->iteration }}
                                    </a>
                                    <small class="text-muted">{{ $mediaName }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="row text-center">
                        <div class="col-md-3 mb-2"><div class="p-2 bg-success text-white">1. Đã gửi yêu cầu</div></div>
                        <div class="col-md-3 mb-2">
                            <div class="p-2 {{ $order->status == 'return_requested' ? 'bg-warning text-dark' : 'bg-success text-white' }}">
                                2. {{ $order->status == 'return_requested' ? 'Chờ duyệt' : 'Đã duyệt' }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-2 {{ $received ? 'bg-success text-white' : ($order->status == 'return_pickup' ? 'bg-warning text-dark' : 'bg-light') }}">
                                3. {{ $received ? 'Shop đã nhận hàng lỗi' : 'Chờ shop nhận hàng lỗi' }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="p-2 {{ $finished ? 'bg-success text-white' : ($order->status == 'replacement_shipping' ? 'bg-warning text-dark' : 'bg-light') }}">
                                4. {{ $finished ? 'Hoàn tất yêu cầu đổi' : 'Đang giao sản phẩm đổi' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h4>Sản phẩm trong đơn hàng</h4>
                <div class="table-responsive">
                    <table class="table align-middle">
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
                            @foreach($order->orderItems as $item)
                                @php
                                    $productImage = asset('storage/uploads/products/default.png');
                                    $productName = 'Sản phẩm đã xóa';

                                    if ($item->product) {
                                        $productImage = $item->product->image_url;
                                        $productName = $item->product->display_name;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <img src="{{ $productImage }}" width="60" height="60" alt="Sản phẩm">
                                    </td>
                                    <td>{{ $productName }}</td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }} <small>đ</small></td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} <small>đ</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <h5>Thông tin giao hàng</h5>
                        <p class="mb-1">{{ $shippingInfo['name'] }} · {{ $shippingInfo['phone'] }}</p>
                        <p>{{ $shippingInfo['address'] }}, {{ $shippingInfo['city'] }}</p>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between"><span>Tiền hàng</span><strong>{{ number_format($order->subtotal, 0, ',', '.') }} <small>đ</small></strong></div>
                        <div class="d-flex justify-content-between"><span>{{ $order->shippingFeeLabel() }}</span><strong>{{ number_format($order->shipping_fee, 0, ',', '.') }} <small>đ</small></strong></div>
                        @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between"><span>Giám gi?</span><strong>-{{ number_format($order->discount_amount, 0, ',', '.') }} <small>đ</small></strong></div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>{{ $order->totalLabel() }}</span>
                            <strong class="text-danger">{{ number_format($order->total_price, 0, ',', '.') }} <small>đ</small></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($order->status == 'pending')
            <div class="card border-danger mb-4">
                <div class="card-body">
                    <h5 class="text-danger">Hủy đơn hàng</h5>
                    <form action="{{ route('order.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này? Số lượng sản phẩm sẽ được hoàn lại.');">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="cancel_reason"><strong>Lý do hủy đơn hàng</strong> <span class="text-danger">*</span></label>
                            <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="3" placeholder="VD: Đặt nhầm sản phẩm, muốn đổi địa chỉ..." required>{{ old('cancel_reason') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm">Hủy đơn hàng</button>
                    </form>
                </div>
            </div>
        @endif

        @if($order->status == 'canceled' && $order->cancel_reason)
            <div class="alert alert-danger mb-4">
                <strong><i class="fa fa-info-circle"></i> Thông tin hủy đơn:</strong><br>
                <strong>Người hủy:</strong> {{ $order->canceled_by == 'admin' ? 'Quản trị viên' : 'Bạn' }}<br>
                <strong>Lý do:</strong> {{ $order->cancel_reason }}
            </div>
        @endif

        @if($order->status == 'completed' && ! $returnRequest && $canRequestReturn)
            <button type="button" id="show-return-request-form" class="btn btn-warning mt-4">
                <i class="fa fa-refresh"></i> Gửi yêu cầu hàng lỗi/hư
            </button>

            <div id="return-request-form" class="card border-warning mt-4 return-request-panel {{ $errors->any() ? 'is-open' : '' }}">
                <div class="card-body">
                    <h4>Tạo yêu cầu xử lý hàng lỗi/hư</h4>
                    <p class="text-muted">Chỉ dùng khi sản phẩm bị hư hỏng, lỗi, móp, mốc hoặc sai quy cách. Hạn gửi yêu cầu: {{ $order->returnDeadlineLabel() }}.</p>

                    <form action="{{ route('order.return-request.store', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <h5>1. Chọn sản phẩm lỗi/hư</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đã mua</th>
                                        <th>Số lượng lỗi/hư</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                        @php
                                            $productName = 'Sản phẩm đã xóa';

                                            if ($item->product) {
                                                $productName = $item->product->display_name;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $productName }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                <input type="number" name="items[{{ $item->id }}][quantity]" class="form-control" min="0" max="{{ $item->quantity }}" value="{{ old('items.'.$item->id.'.quantity', 0) }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <label>Mô tả tình trạng lỗi/hư <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="VD: sản phẩm bị mốc, rách bao bì, dập nát...">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Ảnh/video minh chứng <span class="text-danger">*</span></label>
                            <input type="file" name="evidence[]" class="form-control" accept="image/*,video/*" multiple required>
                            <small class="text-muted">Vui lòng gửi ít nhất một ảnh/video rõ tình trạng sản phẩm.</small>
                        </div>

                        <button type="submit" class="btn btn-warning">Gửi yêu cầu</button>
                    </form>
                </div>
            </div>
        @elseif($order->status == 'completed' && ! $returnRequest)
            <div class="alert alert-info mt-4">
                Đơn hàng chỉ được đổi/trả trong vòng 3 ngày kể từ khi nhận hàng.
                Hạn gửi yêu cầu của đơn này: {{ $order->returnDeadlineLabel() }}.
            </div>
        @endif

        @if($order->status == 'completed' && ! $returnRequest)
            <h4 class="mt-4">Đánh giá sản phẩm</h4>
            @foreach($order->orderItems as $item)
                @if($item->product)
                    <a href="{{ route('product.detail', $item->product->slug) }}" class="btn theme-btn-1 btn-effect-1 mr-2">
                        Đánh giá {{ $item->product->display_name }}
                    </a>
                @endif
            @endforeach
        @endif
    </div>
</div>

@endsection
