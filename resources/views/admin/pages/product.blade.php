@extends('layouts.admin')

@section('title','Quản Sản Phẩm')
@section('content')
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Danh sách tất cả Sản Phẩm</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Danh Sách Sản Phẩm</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <p class="text-muted font-13 m-b-30">
                                        Trang quản lý danh mục cho phép Admin tạo mới, sửa và xóa các danh mục sản phẩm.
                                    </p>
                                    <table id="datatable-buttons" class="table table-striped table-bordered"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Hình ảnh</th>
                                                <th>Tên sản phẩm</th>
                                                <th>Danh mục</th>
                                                <th>Slug</th>
                                                <th>Mô tả</th>
                                                <th>Số lượng</th>
                                                <th>Giá</th>
                                                <th>Đơn vị</th>
                                                <th>Trạng thái</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>


                                        <tbody>

                                            @foreach ($products as $product)
                                            <tr id="product-row-{{$product->id}}">
                                                <td><img src="{{ $product->firstImage ? asset('storage/' . $product->firstImage->image) : asset('storage/uploads/products/product_default.png') }}"
                                                        alt="{{ $product->name }}" class="image-product"></td>
                                                <td>{{$product->name}}</td>
                                                <td>{{$product->category->name}}</td>
                                                <td>{{$product->slug}}</td>
                                                <td>{{$product->description}}</td>
                                                <td>{{$product->stock}}</td>
                                                <td>{{number_format($product->price, 0, ',', '.')}} VND</td>
                                                <td>{{$product->unit}}</td>
                                                <td>{{$product->status =='int_stock' ? 'Còn hàng' : 'Hết hàng'}}</td>

                                                <td>
                                                    <a class="btn btn-app btn-update-product" data-toggle="modal"
                                                        data-target="#modalupdate-{{$product->id}}"> <i
                                                            class="fa fa-edit"></i>Chỉnh
                                                        sữa</a>
                                                </td>
                                                <td> <a class="btn btn-app btn-delete-product"
                                                        data-id="{{$product->id}}"> <i class="fa fa-close"></i>Xóa</a>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="modalupdate-{{$product->id}}" tabindex="-1"
                                                aria-labelledby="productModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="productModalLabel">Chỉnh sửa
                                                            </h5>
                                                            <button type="button" class="btn-close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="update-product" method="POST"
                                                                class="form-horizontal form-label-left"
                                                                enctype="multipart/form-data">
                                                                @csrf

                                                                <div class="item form-group">
                                                                    <label
                                                                        class="col-form-label col-md-3 col-sm-3 label-align"
                                                                        for="product-name">Tên Sản Phẩm
                                                                        <span class="required">*</span>
                                                                    </label>
                                                                    <div class="col-md-6 col-sm-6 ">
                                                                        <input type="text" id="product-name" name="name"
                                                                            required class="form-control "
                                                                            value="{{$product->name}}">
                                                                    </div>
                                                                </div>

                                                                <div class="item form-group">
                                                                    <label
                                                                        class="col-form-label col-md-3 col-sm-3 label-align"
                                                                        for="product-name">Danh Mục
                                                                        <span class="required">*</span>
                                                                    </label>
                                                                    <div class="col-md-6 col-sm-6 ">
                                                                        <select name="category_id" id="category_id"
                                                                            class="form-control">
                                                                            <option value="">Chọn danh mục</option>
                                                                            @foreach($categories as $category)
                                                                            <option value="{{ $category->id }}" {{
                                                                                $product->category_id == $category->id ?
                                                                                'selected' : '' }}>
                                                                                {{ $category->name }}
                                                                            </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="item form-group">
                                                                    <label
                                                                        class="col-form-label col-md-3 col-sm-3 label-align"
                                                                        for="product-description">Mô Tả
                                                                        <span class="required">*</span>
                                                                    </label>
                                                                    <div class="col-md-6 col-sm-6 ">
                                                                        <input type="text" id="product-description"
                                                                            name="description" required
                                                                            class="form-control"
                                                                            value="{{$product->description}}">
                                                                    </div>
                                                                </div>
                                                                <div class="item form-group">
                                                                    <label
                                                                        class="col-form-label col-md-3 col-sm-3 label-align"
                                                                        for="product-description">Giá
                                                                        <span class="required">*</span>
                                                                    </label>
                                                                    <div class="col-md-6 col-sm-6 ">
                                                                        <input type="number" id="product-price"
                                                                            name="price" required class="form-control"
                                                                            value="{{$product->price}}">
                                                                    </div>
                                                                </div>
                                                                <div class="item form-group">
                                                                    <label
                                                                        class="col-form-label col-md-3 col-sm-3 label-align"
                                                                        for="product-description">Số lượng
                                                                        <span class="required">*</span>
                                                                    </label>
                                                                    <div class="col-md-6 col-sm-6 ">
                                                                        <input type="number" id="product-stock"
                                                                            name="stock" required class="form-control"
                                                                            value="{{$product->stock}}">
                                                                    </div>
                                                                </div>
                                                                <div class="item form-group">
                                                                    <label
                                                                        class="col-form-label col-md-3 col-sm-3 label-align"
                                                                        for="product-description">Đơn vị
                                                                        <span class="required">*</span>
                                                                    </label>
                                                                    <div class="col-md-6 col-sm-6 ">
                                                                        <input type="number" id="product-unit"
                                                                            name="unit" required class="form-control"
                                                                            value="{{$product->unit}}">
                                                                    </div>
                                                                </div>
                                                                <div class="item form-group">
                                                                    <label
                                                                        class="col-form-label col-md-3 col-sm-3 label-align"
                                                                        for="product-image">Hình
                                                                        ảnh</label>
                                                                    <div class="col-md-6 col-sm-6 ">
                                                                        <img src="{{asset('storage/'. $product->image)}}"
                                                                            alt="{{$product->name}}"
                                                                            id="image-preview-{{$product->id}}"
                                                                            class="image-preview">
                                                                        <label class="custom-file-upload"
                                                                            for="product-image-{{$product->id}}">
                                                                            Chọn
                                                                            Ảnh</label>
                                                                        <input type="file" name="image"
                                                                            class="product-image"
                                                                            id="product-image-{{$product->id}}"
                                                                            data-id="{{$product->id}}" accept="image/*">
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Quay lại</button>
                                                            <button type="button"
                                                                class="btn btn-primary btn-update-submit-product"
                                                                data-id="{{$product->id}}">Chỉnh sửa</button>
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
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->
@endsection