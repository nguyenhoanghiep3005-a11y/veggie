@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

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
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Danh sách sản phẩm</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.san-pham.them') }}" class="btn btn-success pull-right">
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
                    @foreach ($sanPhams as $sanPham)

                        <tr id="product-row-{{ $sanPham->ma_san_pham }}">
                            <td><img src="{{ $sanPham->duong_dan_hinh_anh }}" alt="{{ $sanPham->ten_hien_thi }}" class="image-product" style="width:80px; height:80px; object-fit:cover; border-radius:8px;"></td>
                            <td class="text-left"><strong>{{ $sanPham->ten_hien_thi }}</strong></td>
                            <td>{{ $sanPham->ten_danh_muc }}</td>
                            <td>{{ number_format((float) $sanPham->gia, 0, ',', '.') }} đ</td>
                            <td>{{ $sanPham->don_vi }}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalupdate-{{ $sanPham->ma_san_pham }}">
                                    <i class="fa fa-edit"></i> Sửa
                                </button>
                                <button type="button" class="btn btn-danger btn-sm btn-delete-product" data-id="{{ $sanPham->ma_san_pham }}">
                                    <i class="fa fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalupdate-{{ $sanPham->ma_san_pham }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Chỉnh sửa sản phẩm</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="update-product-{{ $sanPham->ma_san_pham }}" action="{{ route('admin.san-pham.cap-nhat') }}" method="POST" class="form-horizontal form-label-left" enctype="multipart/form-data">
                                            @csrf

                                            <div class="item form-group">
                                                <label class="col-form-label col-md-3 label-align">Tên sản phẩm <span class="required">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="text" name="ten" class="form-control" value="{{ $sanPham->ten }}" required>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="col-form-label col-md-3 label-align">Danh mục <span class="required">*</span></label>
                                                <div class="col-md-8">
                                                    <select name="ma_danh_muc" class="form-control" required>
                                                        <option value="">Chọn danh mục</option>
                                                        @foreach($danhMucs as $danhMuc)
                                                            @if($sanPham->ma_danh_muc == $danhMuc->ma_danh_muc)
                                                                <option value="{{ $danhMuc->ma_danh_muc }}" selected>{{ $danhMuc->ten }}</option>
                                                            @else
                                                                <option value="{{ $danhMuc->ma_danh_muc }}">{{ $danhMuc->ten }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="col-form-label col-md-3 label-align">Mô tả <span class="required">*</span></label>
                                                <div class="col-md-8">
                                                    <textarea name="mo_ta" class="form-control" rows="4" required>{{ $sanPham->mo_ta }}</textarea>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="col-form-label col-md-3 label-align">Giá niêm yết <span class="required">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="number" name="gia" class="form-control" min="0" step="1000" value="{{ (float) $sanPham->gia }}" required>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="col-form-label col-md-3 label-align">Đơn vị/khối lượng <span class="required">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="text" name="don_vi" class="form-control" value="{{ $sanPham->don_vi }}" required>
                                                </div>
                                            </div>

                                            <div class="item form-group">
                                                <label class="col-form-label col-md-3 label-align">Hình ảnh</label>
                                                <div class="col-md-8">
                                                    <label class="custom-file-upload"
                                                     style="display:inline-block; margin-top:10px; padding:6px 12px; cursor:pointer; background:#368ae3; color:#fff; border-radius:6px; font-weight:bold; text-align:center;" 
                                                     for="product-images-{{ $sanPham->ma_san_pham }}">Chọn ảnh</label>
                                                    <input type="file" name="images[]" class="product-images" id="product-images-{{ $sanPham->ma_san_pham }}"
                                                     style="display:none;" 
                                                     data-id="{{ $sanPham->ma_san_pham }}" accept="image/*" multiple>
                                                    <div id="image-preview-container-{{ $sanPham->ma_san_pham }}" class="image-preview-container image-preview-listproduct" style="display:grid; grid-template-columns:repeat(2, 1fr); width:100%; margin-top:10px; gap:10px;" data-id="{{ $sanPham->ma_san_pham }}">
                                                        @foreach ($sanPham->hinhAnhs as $hinhAnh)
                                                            <img src="{{ asset('storage/'.$hinhAnh->hinh_anh) }}" alt="Ảnh sản phẩm" class="image-preview" style="width:120px; height:120px; object-fit:cover; border-radius:8px; margin-top:10px; margin-bottom:10px; border:1px solid #ddd;">
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Quay lại</button>
                                        <button type="button" class="btn btn-primary btn-update-submit-product" data-id="{{ $sanPham->ma_san_pham }}">Cập nhật sản phẩm</button>
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
