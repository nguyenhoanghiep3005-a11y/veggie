@extends('layouts.admin')

@section('title', 'Thêm đơn đặt nhập')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Thêm đơn đặt nhập</h3></div>
        <div class="title_right">
            <a href="{{ route('admin.don-dat-nhap.danh-sach') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $loi)
                <div>{{ $loi }}</div>
            @endforeach
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="x_panel">
        <div class="x_title">
            <h2>Số đơn: <strong>{{ $soDon }}</strong></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <form action="{{ route('admin.don-dat-nhap.luu') }}" method="POST" class="form-horizontal form-label-left">
                @csrf
                <div class="form-group row">
                    <label class="control-label col-md-3">Nhà cung cấp</label>
                    <div class="col-md-6">
                        <select name="ma_nha_cung_cap" class="form-control" required>
                            <option value="">-- Chọn nhà cung cấp --</option>
                            @foreach($nhaCungCaps as $nhaCungCap)
                                <option value="{{ $nhaCungCap->ma_nha_cung_cap }}" @selected(old('ma_nha_cung_cap') == $nhaCungCap->ma_nha_cung_cap)>
                                    {{ $nhaCungCap->ten }}{{ $nhaCungCap->so_dien_thoai ? ' - '.$nhaCungCap->so_dien_thoai : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-md-3">Ngày đặt</label>
                    <div class="col-md-6">
                        <input type="date" name="ngay_dat" class="form-control" value="{{ old('ngay_dat', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead><tr><th>Sản phẩm</th><th>Số lượng đặt</th><th>Thao tác</th></tr></thead>
                        <tbody id="purchase-order-items"></tbody>
                    </table>
                </div>

                <button type="button" id="btn-add-purchase-row" class="btn btn-info">
                    <i class="fa fa-plus"></i> Thêm sản phẩm
                </button>

                <select id="purchase-product-options" class="d-none">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($luaChonSanPhams as $luaChonSanPham)
                        <option value="{{ $luaChonSanPham['ma_san_pham'] }}">{{ $luaChonSanPham['ten_san_pham'] }}</option>
                    @endforeach
                </select>

                <div class="ln_solid"></div>
                <div class="text-right">
                    <a href="{{ route('admin.don-dat-nhap.danh-sach') }}" class="btn btn-default">Hủy</a>
                    <button type="submit" class="btn btn-success">Lưu đơn đặt nhập</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection