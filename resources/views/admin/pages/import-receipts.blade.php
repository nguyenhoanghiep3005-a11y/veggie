@extends('layouts.admin')

@section('title', 'Phiếu nhập hàng')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Phiếu nhập hàng</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="x_panel">
        <div class="x_title">
            <h2>Danh sách phiếu nhập hàng</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Phiếu đặt mua</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>Số mặt hàng</th>
                        <th>Tổng số lượng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($receipts) > 0)
                        @foreach($receipts as $receipt)
                            @php
                                $purchaseOrderCode = '—';
                                if ($receipt->purchaseOrder) {
                                    $purchaseOrderCode = $receipt->purchaseOrder->code;
                                }

                                $supplierName = 'Không rõ';
                                if ($receipt->supplier) {
                                    $supplierName = $receipt->supplier->name;
                                }

                                $receivedAt = '—';
                                if ($receipt->received_at) {
                                    $receivedAt = $receipt->received_at->format('d/m/Y H:i');
                                }
                            @endphp
                            <tr>
                                <td><strong>{{ $receipt->code }}</strong></td>
                                <td>{{ $purchaseOrderCode }}</td>
                                <td>{{ $supplierName }}</td>
                                <td>{{ $receivedAt }}</td>
                                <td>{{ $receipt->items->count() }}</td>
                                <td>{{ number_format($receipt->totalQuantity()) }}</td>
                                <td>
                                    <a href="{{ route('admin.import-receipts.show', $receipt) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-muted">Chưa có phiếu nhập hàng.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="text-center">{{ $receipts->links() }}</div>
        </div>
    </div>
</div>
@endsection