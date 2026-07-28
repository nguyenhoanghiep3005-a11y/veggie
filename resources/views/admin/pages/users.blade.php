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
                    @foreach ($users as $user)
                        @php
                            $statusLabels = [
                                'active' => 'Đang hoạt động',
                                'pending' => 'Chờ kích hoạt',
                                'banned' => 'Đã chặn',
                            ];

                            $statusText = $user->status;
                            if (isset($statusLabels[$user->status])) {
                                $statusText = $statusLabels[$user->status];
                            }

                            $address = '—';
                            if ($user->address) {
                                $address = $user->address;
                            }

                            $phoneNumber = '—';
                            if ($user->phone_number) {
                                $phoneNumber = $user->phone_number;
                            }
                        @endphp
                        <div class="col-md-4 col-sm-4 profile_details">
                            <div class="well profile_view">
                                <div class="col-sm-12">
                                    <h4 class="brief"><i>KHÁCH HÀNG</i></h4>
                                    <div class="left col-md-12 col-sm-12">
                                        <h2>{{ $user->name }}</h2>
                                        <p><strong>Email: </strong>{{ $user->email }}</p>
                                        <p><strong>Trạng thái: </strong><span class="user-status">{{ $statusText }}</span></p>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-building"></i> Địa chỉ: {{ $address }}</li>
                                            <li><i class="fa fa-phone"></i> SĐT: {{ $phoneNumber }}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="profile-bottom text-center">
                                    <div class="col-sm-4 emphasis"></div>
                                    <div class="col-sm-12 emphasis">
                                        @if ($user->status == 'banned')
                                            <button type="button" class="btn btn-success btn-sm changeStatus" data-userid="{{ $user->id }}" data-status="active" data-url="{{ route('admin.users.update-status') }}">
                                                <i class="fa fa-check"></i> Bỏ chặn
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-warning btn-sm changeStatus" data-userid="{{ $user->id }}" data-status="banned" data-url="{{ route('admin.users.update-status') }}">
                                                <i class="fa fa-ban"></i> Chặn
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection