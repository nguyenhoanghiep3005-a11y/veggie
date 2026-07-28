@extends('layouts.admin')

@section('title','Tạo phiếu đặt mua')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Tạo phiếu đặt mua</h3>
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

    <div class="x_panel">
        <div class="x_title">
            <h2>Mã phiếu: <strong>{{ $code }}</strong></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form action="{{ route('admin.purchase-orders.store') }}" method="POST" class="form-horizontal form-label-left">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3">Nhà cung cấp <span class="required">*</span></label>
                            <div class="col-md-9">
                                <select name="supplier_id" class="form-control" required>
                                    <option value="">— Chọn nhà cung cấp —</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                            {{ $supplier->name }}{{ $supplier->phone ? ' - '.$supplier->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-3">Ngày đặt <span class="required">*</span></label>
                            <div class="col-md-9">
                                <input type="date" name="ordered_at" class="form-control" value="{{ old('ordered_at', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <h4>Danh sách sản phẩm đặt mua</h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="purchase-order-items-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng đặt mua</th>
                                <th>Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="purchase-order-items"></tbody>
                    </table>
                    <template id="purchase-product-options">
                        <option value="">— Chọn sản phẩm —</option>
                        @foreach($productOptions as $productOption)
                            <option value="{{ $productOption['id'] }}">{{ $productOption['name'] }}</option>
                        @endforeach
                    </template>
                </div>

                <button type="button" class="btn btn-warning btn-sm" id="btn-add-purchase-row">
                    <i class="fa fa-plus"></i> Thêm sản phẩm
                </button>

                <div class="ln_solid"></div>

                <div class="form-group text-right">
                    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-default">Hủy</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Lưu phiếu đặt mua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
