@extends('layouts.admin')

@section('title', 'Chi tiết phiếu đặt mua')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Chi tiết phiếu đặt mua {{ $purchaseOrder->code }}</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Danh sách phiếu
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @php
        $supplierName = 'Không rõ';
        if ($purchaseOrder->supplier) {
            $supplierName = $purchaseOrder->supplier->name;
        }

        $orderedAt = '—';
        if ($purchaseOrder->ordered_at) {
            $orderedAt = $purchaseOrder->ordered_at->format('d/m/Y');
        }

        $receivedAt = 'Chưa nhập';
        if ($purchaseOrder->received_at) {
            $receivedAt = $purchaseOrder->received_at->format('d/m/Y H:i');
        }
    @endphp

    <div class="x_panel">
        <div class="x_title">
            <h2>Thông tin phiếu đặt mua</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nhà cung cấp:</strong> {{ $supplierName }}</p>
                    <p><strong>Ngày đặt:</strong> {{ $orderedAt }}</p>
                    <p><strong>Ngày nhập:</strong> {{ $receivedAt }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Trạng thái:</strong> <span class="{{ $purchaseOrder->statusClass() }}">{{ $purchaseOrder->statusLabel() }}</span></p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng đặt mua</th>
                            <th>Số lượng nhận</th>
                            <th>Hàng lỗi/hư</th>
                            <th>Thực tế nhập</th>
                            <th>Hạn sử dụng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                            @php
                                $productName = 'Sản phẩm đã xóa';
                                if ($item->product) {
                                    $productName = $item->product->display_name;
                                }

                                $expiredAt = '—';
                                if ($item->expired_at) {
                                    $expiredAt = $item->expired_at->format('d/m/Y');
                                }
                            @endphp
                            <tr>
                                <td class="text-left"><strong>{{ $productName }}</strong></td>
                                <td>{{ number_format($item->quantity_ordered) }}</td>
                                <td>{{ number_format($item->quantity_received) }}</td>
                                <td class="text-danger">{{ number_format($item->quantity_rejected) }}</td>
                                <td class="text-success">{{ number_format($item->quantity_imported) }}</td>
                                <td>{{ $expiredAt }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($purchaseOrder->importReceipts->isNotEmpty())
                <h4>Phiếu nhập hàng</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Ngày nhập</th>
                            <th>Số lượng nhập</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->importReceipts as $receipt)
                            @php
                                $receiptDate = '—';
                                if ($receipt->received_at) {
                                    $receiptDate = $receipt->received_at->format('d/m/Y H:i');
                                }
                            @endphp
                            <tr>
                                <td><strong>{{ $receipt->code }}</strong></td>
                                <td>{{ $receiptDate }}</td>
                                <td>{{ number_format($receipt->totalQuantity()) }}</td>
                                <td>
                                    <a href="{{ route('admin.import-receipts.show', $receipt) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> Xem phiếu nhập
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($purchaseOrder->damageSlips->isNotEmpty())
                <h4>Phiếu hủy hàng lỗi/hư</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Lý do</th>
                            <th>Số lượng hủy</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->damageSlips as $damageSlip)
                            <tr>
                                <td><strong>{{ $damageSlip->code }}</strong></td>
                                <td>{{ $damageSlip->reason }}</td>
                                <td>{{ number_format($damageSlip->totalQuantity()) }}</td>
                                <td>
                                    <a href="{{ route('admin.damage-slips.show', $damageSlip) }}" class="btn btn-danger btn-sm">
                                        <i class="fa fa-eye"></i> Xem phiếu hủy
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection