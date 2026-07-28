@extends('layouts.admin')

@section('title', 'Chi tiết phiếu nhập hàng')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Chi tiết phiếu nhập hàng {{ $receipt->code }}</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.import-receipts.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Danh sách phiếu nhập
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

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

    <div class="x_panel">
        <div class="x_title">
            <h2>Thông tin phiếu nhập</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Phiếu đặt mua:</strong> {{ $purchaseOrderCode }}</p>
                    <p><strong>Nhà cung cấp:</strong> {{ $supplierName }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Ngày nhập:</strong> {{ $receivedAt }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng nhập</th>
                            <th>NSX/đóng gói</th>
                            <th>Hạn sử dụng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipt->items as $item)
                            @php
                                $productName = 'Sản phẩm đã xóa';
                                if ($item->product) {
                                    $productName = $item->product->display_name;
                                }

                                $manufacturedAt = '—';
                                if ($item->manufactured_at) {
                                    $manufacturedAt = $item->manufactured_at->format('d/m/Y');
                                }

                                $expiredAt = '—';
                                if ($item->expired_at) {
                                    $expiredAt = $item->expired_at->format('d/m/Y');
                                }
                            @endphp
                            <tr>
                                <td class="text-left"><strong>{{ $productName }}</strong></td>
                                <td>{{ number_format($item->quantity) }}</td>
                                <td>{{ $manufacturedAt }}</td>
                                <td>{{ $expiredAt }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection