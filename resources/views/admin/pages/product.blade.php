@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Danh sách sản phẩm</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.product.add') }}" class="btn btn-success pull-right">
                <i class="fa fa-plus"></i> Thêm sản phẩm
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="x_panel">
        <div class="x_title">
            <h2>Sản phẩm bán tại cửa hàng</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <p class="text-muted font-13 m-b-30">
                Sản phẩm là mặt hàng bán ra tại cửa hàng. Mỗi lựa chọn khối lượng là một dòng sản phẩm riêng.
                Số lượng tồn, hạn sử dụng và giá khuyến mãi được quản lý ở phần Quản lý kho.
            </p>

            <div class="table-responsive">
                <table id="datatable-responsive" class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá niêm yết</th>
                            <th>Đơn vị</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            @php
                                $categoryName = 'Chưa phân loại';
                                if ($product->category) {
                                    $categoryName = $product->category->name;
                                }
                            @endphp
                            <tr id="product-row-{{ $product->id }}">
                                <td><img src="{{ $product->image_url }}" alt="{{ $product->display_name }}" class="image-product"></td>
                                <td class="text-left"><strong>{{ $product->display_name }}</strong></td>
                                <td>{{ $categoryName }}</td>
                                <td>{{ number_format((float) $product->price, 0, ',', '.') }} đ</td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalupdate-{{ $product->id }}">
                                        <i class="fa fa-edit"></i> Sửa
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-product" data-id="{{ $product->id }}">
                                        <i class="fa fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalupdate-{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Chỉnh sửa sản phẩm</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="update-product-{{ $product->id }}" action="{{ route('admin.product.update') }}" method="POST" class="form-horizontal form-label-left" enctype="multipart/form-data">
                                                @csrf

                                                <div class="item form-group">
                                                    <label class="col-form-label col-md-3 label-align">Tên sản phẩm <span class="required">*</span></label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                                    </div>
                                                </div>

                                                <div class="item form-group">
                                                    <label class="col-form-label col-md-3 label-align">Danh mục <span class="required">*</span></label>
                                                    <div class="col-md-8">
                                                        <select name="category_id" class="form-control" required>
                                                            <option value="">Chọn danh mục</option>
                                                            @foreach($categories as $category)
                                                                @if($product->category_id == $category->id)
                                                                    <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                                                                @else
                                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="item form-group">
                                                    <label class="col-form-label col-md-3 label-align">Mô tả <span class="required">*</span></label>
                                                    <div class="col-md-8">
                                                        <textarea name="description" class="form-control" rows="4" required>{{ $product->description }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="item form-group">
                                                    <label class="col-form-label col-md-3 label-align">Giá niêm yết <span class="required">*</span></label>
                                                    <div class="col-md-8">
                                                        <input type="number" name="price" class="form-control" min="0" step="1000" value="{{ (float) $product->price }}" required>
                                                    </div>
                                                </div>

                                                <div class="item form-group">
                                                    <label class="col-form-label col-md-3 label-align">Đơn vị/khối lượng <span class="required">*</span></label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="unit" class="form-control" value="{{ $product->unit }}" required>
                                                    </div>
                                                </div>

                                                <div class="item form-group">
                                                    <label class="col-form-label col-md-3 label-align">Hình ảnh</label>
                                                    <div class="col-md-8">
                                                        <label class="custom-file-upload" for="product-images-{{ $product->id }}">Chọn ảnh</label>
                                                        <input type="file" name="images[]" class="product-images" id="product-images-{{ $product->id }}" data-id="{{ $product->id }}" accept="image/*" multiple>
                                                        <div id="image-preview-container-{{ $product->id }}" class="image-preview-container image-preview-listproduct" data-id="{{ $product->id }}">
                                                            @foreach ($product->images as $image)
                                                                <img src="{{ asset('storage/'.$image->image) }}" alt="Ảnh sản phẩm" class="image-preview">
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Quay lại</button>
                                            <button type="button" class="btn btn-primary btn-update-submit-product" data-id="{{ $product->id }}">Cập nhật sản phẩm</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection