@extends('layouts.admin')

@section('title', 'Phiếu đặt mua')

@section('content')
<div class="right_col admin-purchase-orders-page" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Phiếu đặt mua</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-success pull-right">
                <i class="fa fa-plus"></i> Tạo phiếu đặt mua
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="x_panel">
        <div class="x_title">
            <h2>Danh sách phiếu đặt mua từ nhà cung cấp</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center purchase-orders-table">
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày đặt</th>
                        <th>Số mặt hàng</th>
                        <th>Đã nhập</th>
                        <th>Hàng lỗi</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($orders) > 0)
                        @foreach($orders as $purchaseOrder)
                            @php
                                $supplierName = 'Không rõ';
                                if ($purchaseOrder->supplier) {
                                    $supplierName = $purchaseOrder->supplier->name;
                                }

                                $orderedAt = '—';
                                if ($purchaseOrder->ordered_at) {
                                    $orderedAt = $purchaseOrder->ordered_at->format('d/m/Y');
                                }
                            @endphp

                            <tr>
                                <td><strong>{{ $purchaseOrder->code }}</strong></td>
                                <td>{{ $supplierName }}</td>
                                <td>{{ $orderedAt }}</td>
                                <td>{{ $purchaseOrder->items->count() }}</td>
                                <td>{{ number_format($purchaseOrder->importedQuantity()) }}</td>
                                <td>{{ number_format($purchaseOrder->rejectedQuantity()) }}</td>
                                <td><span class="{{ $purchaseOrder->statusClass() }}">{{ $purchaseOrder->statusLabel() }}</span></td>
                                <td class="purchase-order-actions">
                                    <div class="admin-action-group">
                                        @if($purchaseOrder->status === 'pending')
                                            <a href="{{ route('admin.purchase-orders.import.form', $purchaseOrder->id) }}" class="btn btn-success btn-sm action-btn">
                                                <i class="fa fa-sign-in"></i> Nhập hàng
                                            </a>

                                            <form action="{{ route('admin.purchase-orders.destroy', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Xóa phiếu đặt mua này?');">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm action-btn">
                                                    <i class="fa fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.purchase-orders.show', $purchaseOrder->id) }}" class="btn btn-primary btn-sm action-btn">
                                                <i class="fa fa-eye"></i> Xem
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-muted">Chưa có phiếu đặt mua.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="text-center">{{ $orders->links() }}</div>
        </div>
    </div>
</div>
@endsection