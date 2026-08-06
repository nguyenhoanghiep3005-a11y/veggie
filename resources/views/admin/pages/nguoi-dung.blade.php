@extends('layouts.admin')

@section('title', 'Quản lý người dùng')
@section('content')
<div class="right_col admin-users-page" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left"><h3>Quản lý người dùng</h3></div>
        </div>

        <div class="clearfix"></div>
        <div class="x_panel">
            <div class="x_content">
                <div class="row">
                    @forelse ($nguoiDungs as $nguoiDung)
                        <div class="col-md-4 col-sm-4 profile_details">
                            <div class="well profile_view">
                                <div class="col-sm-12">
                                    <h4 class="brief"><i>KHÁCH HÀNG</i></h4>
                                    <div class="left col-md-12 col-sm-12">
                                        <h2>{{ $nguoiDung->ten }}</h2>
                                        <p><strong>Email: </strong>{{ $nguoiDung->email }}</p>
                                        <p>
                                            <strong>Trạng thái: </strong>
                                            <span class="user-status">{{ $nguoiDung->ten_trang_thai }}</span>
                                        </p>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-building"></i> Địa chỉ: {{ $nguoiDung->dia_chi_hien_thi }}</li>
                                            <li><i class="fa fa-phone"></i> SĐT: {{ $nguoiDung->so_dien_thoai_hien_thi }}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="profile-bottom text-center">
                                    <div class="col-sm-4 emphasis"></div>
                                    <div class="col-sm-12 emphasis">
                                        @if ($nguoiDung->trang_thai == 'bi_khoa')
                                            <button type="button" class="btn btn-success btn-sm changeStatus"
                                                data-userid="{{ $nguoiDung->ma_nguoi_dung }}"
                                                data-status="hoat_dong">
                                                <i class="fa fa-check"></i> Bỏ chặn
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-warning btn-sm changeStatus"
                                                    data-userid="{{ $nguoiDung->ma_nguoi_dung }}"
                                                    data-status="bi_khoa">
                                                    <i class="fa fa-ban"></i> Chặn
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-md-12">Chưa có tài khoản khách hàng.</div>
                            @endforelse
                        </div>

                        <div class="text-center">{{ $nguoiDungs->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
