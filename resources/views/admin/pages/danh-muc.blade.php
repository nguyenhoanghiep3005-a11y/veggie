@extends('layouts.admin')

@section('title','Quản Danh Mục')

@push('styles')
<style>
    /* CSS bat buoc cho Datatables tu sinh ra, khong gan truc tiep vao the HTML duoc. */
    table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child:before,
    table.dataTable td.control,
    table.dataTable th.control,
    table.dataTable td.dtr-control,
    table.dataTable th.dtr-control {
        display: none !important;
    }

    #datatable-buttons td:empty,
    #datatable-buttons th:empty {
        display: none !important;
        visibility: collapse !important;
        width: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
</style>
@endpush
@section('content')
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Danh sách tất cả Danh Mục</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Danh Sách Danh Mục</h2>
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
                                    <table id="datatable-responsive" class="table table-striped table-bordered admin-table-centered" style="width:100%; text-align:center;">
                                        <thead>
                                            <tr>
                                                <th>Hình ảnh</th>
                                                <th>Tên danh mục</th>
                                                <th>Slug</th>
                                                <th>Mô tả</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach ($danhMucs as $danhMuc)
                                                <tr id="category-row-{{$danhMuc->ma_danh_muc}}">
                                                    <td><img src="{{ $danhMuc->duong_dan_hinh_anh }}"
                                                        alt="{{$danhMuc->ten}}" class="image-category" style="width:80px; height:80px; object-fit:cover; border-radius:8px;"></td>
                                                        <td>{{$danhMuc->ten}}</td>
                                                        <td>{{$danhMuc->duong_dan}}</td>
                                                        <td>{{$danhMuc->mo_ta}}</td>
                                                        <td>
                                                            <a class="btn btn-app btn-update-category" data-toggle="modal"
                                                            data-target="#modalupdate-{{$danhMuc->ma_danh_muc}}"> <i
                                                            class="fa fa-edit"></i>Chỉnh
                                                            sữa</a>
                                                        </td>
                                                        <td> <a class="btn btn-app btn-delete-category"
                                                            data-id="{{$danhMuc->ma_danh_muc}}"> <i class="fa fa-close"></i>Xóa</a>
                                                        </td>
                                                    </tr>

                                                    <div class="modal fade" id="modalupdate-{{$danhMuc->ma_danh_muc}}" tabindex="-1"
                                                        aria-labelledby="categoryModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="categoryModalLabel">Chỉnh sửa
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-dismiss="modal"
                                                                        aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form id="update-category" method="POST"
                                                                        class="form-horizontal form-label-left"
                                                                        enctype="multipart/form-data">
                                                                        @csrf

                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="category-name">Tên Danh
                                                                                Mục
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text" id="category-name"
                                                                                name="ten" required="required"
                                                                                class="form-control "
                                                                                value="{{$danhMuc->ten}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="category-description">Mô Tả
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text" id="category-description"
                                                                                name="mo_ta" required="required"
                                                                                class="form-control"
                                                                                value="{{$danhMuc->mo_ta}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="category-image">Hình
                                                                                ảnh</label>
                                                                                <div class="col-md-6 col-sm-6 ">
                                                                                    <img src="{{ $danhMuc->duong_dan_hinh_anh }}"
                                                                                    alt="{{$danhMuc->ten}}"
                                                                                    id="image-preview-{{$danhMuc->ma_danh_muc}}"
                                                                                    class="image-preview" style="width:120px; height:120px; object-fit:cover; border-radius:8px; margin-top:10px; margin-bottom:10px; border:1px solid #ddd;">
                                                                                    <label class="custom-file-upload" style="display:inline-block; margin-top:10px; padding:6px 12px; cursor:pointer; background:#368ae3; color:#fff; border-radius:6px; font-weight:bold; text-align:center;"
                                                                                        for="category-image-{{$danhMuc->ma_danh_muc}}"> Chọn
                                                                                        Ảnh</label>
                                                                                        <input type="file" name="hinh_anh"
                                                                                        class="category-image"
                                                                                        id="category-image-{{$danhMuc->ma_danh_muc}}" style="display:none;"
                                                                                        data-id="{{$danhMuc->ma_danh_muc}}"
                                                                                        accept="image/*">
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">Quay lại</button>
                                                                                <button type="button"
                                                                                    class="btn btn-primary btn-update-submit-category"
                                                                                    data-id="{{$danhMuc->ma_danh_muc}}">Chỉnh sửa</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        <div class="text-center">{{ $danhMucs->links() }}</div>
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
