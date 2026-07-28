@extends('layouts.admin')

@section('title', 'Quản lý kho')

@section('content')
<div class="right_col admin-warehouses-page" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Quản lý kho</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.warehouses.damages') }}" class="btn btn-warning pull-right m-l-5">
                <i class="fa fa-exclamation-triangle"></i> Hàng hư hỏng/lỗi
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-list"></i> Danh sách sản phẩm
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="x_panel">
        <div class="x_title">
            <h2>Danh sách hàng tồn kho</h2>
            <div class="clearfix"></div>
        </div>

        <div class="x_content">
            <p class="text-muted">
                Hệ thống bán theo nguyên tắc hàng có hạn sử dụng ngắn hơn sẽ xuất trước.
                Hàng còn từ {{ \App\Models\Product::NEAR_EXPIRY_DAYS }} ngày trở xuống tự chuyển sang trạng thái <strong>Cận hạn</strong>.
            </p>

            <div class="btn-group m-b-15">
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-default' }}">Tất cả</a>
                <a href="{{ route('admin.warehouses.index', ['status' => 'fresh']) }}" class="btn btn-sm {{ in_array($status, ['fresh', 'normal'], true) ? 'btn-success' : 'btn-default' }}">Tươi mới</a>
                <a href="{{ route('admin.warehouses.index', ['status' => 'near']) }}" class="btn btn-sm {{ $status === 'near' ? 'btn-warning' : 'btn-default' }}">Cận hạn</a>
                <a href="{{ route('admin.warehouses.index', ['status' => 'expired']) }}" class="btn btn-sm {{ $status === 'expired' ? 'btn-danger' : 'btn-default' }}">Hết hạn</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center warehouse-table">
                    <thead>
                        <tr>
                            <th>NCC</th>
                            <th>Sản phẩm</th>
                            <th>SL</th>
                            <th>NSX</th>
                            <th>HSD</th>
                            <th>Trạng thái</th>
                            <th>Giá</th>
                            <th>Điều chỉnh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($stocks) > 0)
                            @foreach($stocks as $stock)
                                @php
                                    $supplierName = '—';
                                    if ($stock->supplier) {
                                        $supplierName = $stock->supplier->name;
                                    }

                                    $productName = 'Sản phẩm đã xóa';
                                    if ($stock->product) {
                                        $productName = $stock->product->display_name;
                                    }

                                    $manufacturedAt = '—';
                                    if ($stock->manufactured_at) {
                                        $manufacturedAt = $stock->manufactured_at->format('d/m/Y');
                                    }

                                    $expiredAt = '—';
                                    if ($stock->expired_at) {
                                        $expiredAt = $stock->expired_at->format('d/m/Y');
                                    }
                                @endphp

                                <tr>
                                    <td class="warehouse-supplier"><strong>{{ $supplierName }}</strong></td>
                                    <td class="text-left warehouse-product-name"><strong>{{ $productName }}</strong></td>
                                    <td class="warehouse-quantity"><strong>{{ number_format($stock->quantity_remaining) }}</strong> | {{ number_format($stock->quantity) }}</td>
                                    <td>{{ $manufacturedAt }}</td>
                                    <td>{{ $expiredAt }}</td>
                                    <td><span class="warehouse-status-badge {{ $stock->expiryBadgeClass() }}">{{ $stock->expiryLabel() }}</span></td>
                                    <td class="warehouse-price">
                                        @if($stock->product && $stock->hasPromotion())
                                            <del>{{ number_format((float) $stock->product->price, 0, ',', '.') }} đ</del>
                                            <strong>{{ number_format((float) $stock->sale_price, 0, ',', '.') }} đ</strong>
                                        @elseif($stock->product)
                                            <span>{{ number_format((float) $stock->product->price, 0, ',', '.') }} đ</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="warehouse-action">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#stock-adjust-{{ $stock->id }}">Điều chỉnh</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center text-muted">Chưa có hàng tồn phù hợp.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="text-center">{{ $stocks->links() }}</div>
        </div>
    </div>

    @foreach($stocks as $stock)
        @php
            $isExpired = $stock->isExpired();

            $receiptCode = '#'.$stock->id;
            if ($stock->receipt) {
                $receiptCode = $stock->receipt->code;
            }

            $productName = 'Sản phẩm đã xóa';
            if ($stock->product) {
                $productName = $stock->product->display_name;
            }
        @endphp

        <div class="modal fade" id="stock-adjust-{{ $stock->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Điều chỉnh kho {{ $receiptCode }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="warehouse-stock-summary">
                            <strong>{{ $productName }}</strong>
                            — Còn {{ number_format($stock->quantity_remaining) }} | Nhập {{ number_format($stock->quantity) }}
                            — <span class="warehouse-status-badge {{ $stock->expiryBadgeClass() }}">{{ $stock->expiryLabel() }}</span>
                        </p>

                        <div class="row">
                            <div class="col-md-6">
                                <h4>Giảm giá hàng cận hạn</h4>

                                @if($stock->product && ! $isExpired)
                                    <form action="{{ route('admin.warehouses.adjust', $stock) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="promotion">

                                        <div class="form-group">
                                            <label>Giá niêm yết</label>
                                            <input type="text" class="form-control" value="{{ number_format((float) $stock->product->price, 0, ',', '.') }} đ" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Giá điều chỉnh</label>
                                            <input type="number" name="sale_price" class="form-control" min="0" step="1000" max="{{ max(0, (float) $stock->product->price - 1000) }}" value="{{ $stock->sale_price ? (float) $stock->sale_price : '' }}">
                                        </div>

                                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Lưu giảm giá</button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">Hàng hết hạn hoặc không có sản phẩm nên không được bán giảm giá.</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <h4>Ghi nhận hư hỏng/lỗi</h4>

                                <form action="{{ route('admin.warehouses.adjust', $stock) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="action" value="damage">

                                    <div class="form-group">
                                        <label>Số lượng hư/lỗi</label>
                                        <input type="number" name="damage_quantity" class="form-control" min="1" max="{{ $stock->quantity_remaining }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Lý do</label>
                                        <textarea name="damage_reason" class="form-control" rows="3"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Ảnh/video minh chứng <span class="text-danger">*</span></label>
                                        <input type="file" name="evidence[]" class="form-control" accept="image/*,video/*" multiple required>
                                        <small class="text-muted">Chọn ít nhất một ảnh hoặc video thể hiện tình trạng hàng hư/lỗi. Mỗi file tối đa 50MB.</small>
                                    </div>

                                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Ghi nhận hủy hàng</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection