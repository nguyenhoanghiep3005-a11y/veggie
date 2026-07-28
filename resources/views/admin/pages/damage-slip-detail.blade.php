@extends('layouts.admin')

@section('title', 'Chi tiết phiếu hàng hư, lỗi')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Chi tiết phiếu hư, lỗi {{ $damageSlip->code }}</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.damage-slips.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Danh sách phiếu hư, lỗi
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @php
        $purchaseOrderCode = '—';
        if ($damageSlip->purchaseOrder) {
            $purchaseOrderCode = $damageSlip->purchaseOrder->code;
        }

        $importReceiptCode = '—';
        if ($damageSlip->importReceipt) {
            $importReceiptCode = $damageSlip->importReceipt->code;
        }

        $occurredAt = '—';
        if ($damageSlip->occurred_at) {
            $occurredAt = $damageSlip->occurred_at->format('d/m/Y H:i');
        }

        $supplierName = '—';
        if ($damageSlip->supplier) {
            $supplierName = $damageSlip->supplier->name;
        }
    @endphp

    <div class="x_panel">
        <div class="x_title">
            <h2>Thông tin phiếu hư, lỗi</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nguồn:</strong> {{ $damageSlip->sourceLabel() }}</p>
                    <p><strong>Phiếu đặt mua:</strong> {{ $purchaseOrderCode }}</p>
                    <p><strong>Phiếu nhập hàng:</strong> {{ $importReceiptCode }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Ngày ghi nhận:</strong> {{ $occurredAt }}</p>
                    <p><strong>Nhà cung cấp:</strong> {{ $supplierName }}</p>
                </div>
            </div>
            <p><strong>Lý do:</strong> {{ $damageSlip->reason }}</p>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng hủy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($damageSlip->items as $item)
                            @php
                                $productName = 'Sản phẩm đã xóa';
                                if ($item->product) {
                                    $productName = $item->product->display_name;
                                }
                            @endphp
                            <tr>
                                <td class="text-left"><strong>{{ $productName }}</strong></td>
                                <td>{{ number_format($item->quantity) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($damageSlip->mediaFiles->isNotEmpty())
                <h4>Ảnh/video minh chứng</h4>
                <div class="row">
                    @foreach($damageSlip->mediaFiles as $media)
                        @php
                            $mediaUrl = app(\App\Services\CloudinaryService::class)->mediaUrl($media);
                            $mediaName = basename($media->path);
                            if ($media->original_name) {
                                $mediaName = $media->original_name;
                            }
                        @endphp
                        <div class="col-md-3">
                            <a href="{{ $mediaUrl }}" target="_blank" class="btn btn-default btn-xs btn-block">
                                <i class="fa fa-external-link"></i> Xem minh chứng {{ $loop->iteration }}
                            </a>
                            <small class="text-muted">{{ $mediaName }}</small>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection