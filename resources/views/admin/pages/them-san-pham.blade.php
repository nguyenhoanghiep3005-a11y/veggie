@extends('layouts.admin')

@section('title','Thêm sản phẩm')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Thêm sản phẩm</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="x_panel">
        <div class="x_title">
            <h2>Thông tin sản phẩm mới</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
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

            <form action="{{ route('admin.san-pham.luu') }}" id="add-product" method="POST" class="form-horizontal form-label-left" enctype="multipart/form-data">
                @csrf

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Tên sản phẩm <span class="required">*</span></label>
                    <div class="col-md-6">
                        <input type="text" name="ten" class="form-control" value="{{ old('ten') }}" required>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Danh mục <span class="required">*</span></label>
                    <div class="col-md-6">
                        <select name="ma_danh_muc" class="form-control" required>
                            <option value="">Chọn danh mục</option>
                            @foreach($danhMucs as $danhMuc)
                                <option value="{{ $danhMuc->ma_danh_muc }}" @selected(old('ma_danh_muc') == $danhMuc->ma_danh_muc)>
                                    {{ $danhMuc->ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Mô tả <span class="required">*</span></label>
                    <div class="col-md-6">
                        <textarea name="mo_ta" class="form-control" rows="4" required>{{ old('mo_ta') }}</textarea>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Giá niêm yết <span class="required">*</span></label>
                    <div class="col-md-6">
                        <input type="number" name="gia" class="form-control" min="0" step="1000" value="{{ old('gia') }}" required>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Khối lượng/đơn vị <span class="required">*</span></label>
                    <div class="col-md-6">
                        <div class="row product-weight-fields">
                            <div class="col-7">
                                <input type="number" name="khoi_luong" class="form-control"
                                    min="0.001" max="9999999" step="0.001"
                                    value="{{ old('khoi_luong') }}" placeholder="Nhập khối lượng" required>
                            </div>
                            <div class="col-5">
                                <select name="don_vi" class="form-control" required>
                                    <option value="">Chọn đơn vị</option>
                                    <option value="g" @selected(old('don_vi') == 'g')>Gram (g)</option>
                                    <option value="kg" @selected(old('don_vi') == 'kg')>Kilogram (kg)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Hình ảnh</label>
                    <div class="col-md-6">
                        <label class="custom-file-upload" for="product-images">Chọn ảnh</label>
                        <input type="file" name="images[]" id="product-images" accept="image/*" multiple>
                        <div id="image-preview-container"></div>
                    </div>
                </div>
                <div class="ln_solid"></div>

                <div class="item form-group">
                    <div class="col-md-6 offset-md-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Thêm sản phẩm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
