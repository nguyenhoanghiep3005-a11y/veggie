@extends('layouts.admin')

@section('title', 'Phiếu nhập hàng')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Phiếu nhập hàng</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

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

    <div class="x_panel">
        <div class="x_title">
            <h2>Nhập từ phiếu đặt mua: <strong>{{ $purchaseOrder->code }}</strong></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-md-6"><p><strong>Nhà cung cấp:</strong> {{ $supplierName }}</p></div>
                <div class="col-md-6"><p><strong>Ngày đặt:</strong> {{ $orderedAt }}</p></div>
            </div>

            <form action="{{ route('admin.purchase-orders.import.process', $purchaseOrder->id) }}" method="POST" enctype="multipart/form-data" id="purchase-import-form">
                @csrf

                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng đặt mua</th>
                                <th>Số lượng nhận</th>
                                <th>Hàng lỗi/hư</th>
                                <th>Thực tế nhập</th>
                                <th>NSX/đóng gói</th>
                                <th>Hạn sử dụng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $index => $item)
                                @php
                                    $received = old("items.$index.quantity_received", $item->quantity_ordered);
                                    $rejected = old("items.$index.quantity_rejected", 0);
                                    $productName = 'Sản phẩm đã xóa';

                                    if ($item->product) {
                                        $productName = $item->product->display_name;
                                    }
                                @endphp
                                <tr class="js-import-row">
                                    <td class="text-left">
                                        <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                        <strong>{{ $productName }}</strong>
                                    </td>
                                    <td><input type="number" class="form-control text-center js-ordered" value="{{ $item->quantity_ordered }}" readonly></td>
                                    <td><input type="number" name="items[{{ $index }}][quantity_received]" class="form-control text-center js-received" min="0" max="{{ $item->quantity_ordered }}" value="{{ $received }}" required></td>
                                    <td><input type="number" name="items[{{ $index }}][quantity_rejected]" class="form-control text-center js-rejected" min="0" max="{{ $item->quantity_ordered }}" value="{{ $rejected }}" required></td>
                                    <td><input type="number" class="form-control text-center js-accepted" value="{{ max(0, $received - $rejected) }}" readonly></td>
                                    <td><input type="date" name="items[{{ $index }}][manufactured_at]" class="form-control js-manufactured" value="{{ old("items.$index.manufactured_at", date('Y-m-d')) }}"></td>
                                    <td><input type="date" name="items[{{ $index }}][expired_at]" class="form-control js-expired" value="{{ old("items.$index.expired_at", date('Y-m-d', strtotime('+365 days'))) }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group">
                    <label>Lý do hàng lỗi/hư <span id="defect-required-mark" class="text-danger d-none">*</span></label>
                    <textarea id="defect-description" name="defect_description" class="form-control" rows="3">{{ old('defect_description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Ảnh/video minh chứng hàng lỗi/hư <span id="defect-evidence-required-mark" class="text-danger d-none">*</span></label>
                    <input id="defect-evidence" type="file" name="evidence[]" class="form-control" accept="image/*,video/*" multiple>
                    <small class="text-muted">Bắt buộc khi có hàng lỗi/hư. Mỗi tệp tối đa 50MB.</small>
                </div>

                <div class="text-right">
                    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-default">Hủy</a>
                    <button type="submit" class="btn btn-success" id="btn-submit-import"><i class="fa fa-save"></i> Xác nhận nhập hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection