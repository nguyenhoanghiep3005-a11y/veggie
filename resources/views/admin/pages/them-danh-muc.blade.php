@extends('layouts.admin')

@section('title','Quản Danh Mục')
@section('content')
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Tạo Danh Mục</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Thêm danh mục mới</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br />
                        <form action="{{ route('admin.danh-muc.luu') }}" id="add-category" method="POST" class="form-horizontal form-label-left"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align" for="category-name">Tên Danh
                                    Mục
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 ">
                                    <input type="text" id="category-name" name="ten" required="required"
                                    class="form-control ">
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align"
                                    for="category-description">Mô Tả
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 ">
                                    <input type="text" id="category-description" name="mo_ta"
                                    required="required" class="form-control">
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="col-form-label col-md-3 col-sm-3 label-align" for="category-image">Hình
                                    ảnh</label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <label class="custom-file-upload" style="display:inline-block; margin-top:10px; padding:6px 12px; cursor:pointer; background:#368ae3; color:#fff; border-radius:6px; font-weight:bold; text-align:center;" for="category-image"> Chọn Ảnh</label>
                                        <input type="file" name="hinh_anh" id="category-image" accept="image/*" style="display:none;">
                                        <img src="" alt="Ảnh xem trước" id="image-preview" class="image-preview" style="width:120px; height:120px; object-fit:cover; border-radius:8px; margin-top:10px; margin-bottom:10px; border:1px solid #ddd;">
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 offset-md-3">
                                        <button type="submit" class="btn btn-success">Thêm Danh Mục</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
    @endsection
