@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Quản lý nhà cung cấp</h3>
        </div>
        <div class="title_right">
            <button class="btn btn-success pull-right" data-toggle="modal" data-target="#supplier-create">
                <i class="fa fa-plus"></i> Thêm nhà cung cấp
                </button>
            </div>
        </div>
        <div class="clearfix"></div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="x_panel">
            <div class="x_content table-responsive">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên nhà cung cấp</th>
                            <th>Điện thoại</th>
                            <th>Mô tả</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($nhaCungCaps) > 0)
                            @foreach($nhaCungCaps as $nhaCungCap)
                                <tr>
                                    <td>{{ $nhaCungCap->ma_nha_cung_cap }}</td>
                                    <td>{{ $nhaCungCap->ten }}</td>
                                    <td>{{ $nhaCungCap->so_dien_thoai_hien_thi }}</td>
                                    <td class="text-left">{{ $nhaCungCap->mo_ta_hien_thi }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#supplier-edit-{{ $nhaCungCap->ma_nha_cung_cap }}">
                                            <i class="fa fa-edit"></i> Sửa
                                            </button>
                                            <form method="POST" action="{{ route('admin.nha-cung-cap.xoa', $nhaCungCap) }}" class="d-inline" onsubmit="return confirm('Xóa nhà cung cấp này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5">Chưa có nhà cung cấp.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="text-center">{{ $nhaCungCaps->links() }}</div>
                </div>
            </div>

            @foreach($nhaCungCaps as $nhaCungCap)
                <div class="modal fade" id="supplier-edit-{{ $nhaCungCap->ma_nha_cung_cap }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.nha-cung-cap.cap-nhat', $nhaCungCap) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h4>Chỉnh sửa nhà cung cấp</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <label>Tên nhà cung cấp</label>
                                    <input name="ten" class="form-control" value="{{ $nhaCungCap->ten }}" required>
                                    <label>Điện thoại</label>
                                    <input name="so_dien_thoai" class="form-control" value="{{ $nhaCungCap->so_dien_thoai }}">
                                    <label>Mô tả</label>
                                    <textarea name="mo_ta" class="form-control" rows="3">{{ $nhaCungCap->mo_ta }}</textarea>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary">Lưu thay đổi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="modal fade" id="supplier-create" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.nha-cung-cap.luu') }}">
                        @csrf
                        <div class="modal-header">
                            <h4>Thêm nhà cung cấp</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <label>Tên nhà cung cấp</label>
                            <input name="ten" class="form-control" required>
                            <label>Điện thoại</label>
                            <input name="so_dien_thoai" class="form-control">
                            <label>Mô tả</label>
                            <textarea name="mo_ta" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success">Thêm nhà cung cấp</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
