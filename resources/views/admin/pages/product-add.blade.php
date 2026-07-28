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

            <form action="{{ route('admin.product.store') }}" id="add-product" method="POST" class="form-horizontal form-label-left" enctype="multipart/form-data">
                @csrf

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Tên sản phẩm <span class="required">*</span></label>
                    <div class="col-md-6">
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Danh mục <span class="required">*</span></label>
                    <div class="col-md-6">
                        <select name="category_id" class="form-control" required>
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Mô tả <span class="required">*</span></label>
                    <div class="col-md-6">
                        <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Giá niêm yết <span class="required">*</span></label>
                    <div class="col-md-6">
                        <input type="number" name="price" class="form-control" min="0" step="1000" value="{{ old('price') }}" required>
                    </div>
                </div>

                <div class="item form-group">
                    <label class="col-form-label col-md-3 label-align">Đơn vị/khối lượng <span class="required">*</span></label>
                    <div class="col-md-6">
                        <input type="text" name="unit" class="form-control" value="{{ old('unit') }}" placeholder="VD: 100g, 250g, 500g" required>
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
