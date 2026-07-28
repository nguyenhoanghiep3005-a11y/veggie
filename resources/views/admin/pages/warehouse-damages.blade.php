@extends('layouts.admin')

@section('title', 'Hàng hủy')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Hàng hủy</h3></div>
        <div class="title_right">
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Danh sách hàng trong kho
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="x_panel">
        <div class="x_title">
            <h2>Danh sách hàng hủy trong kho</h2>
            <div class="clearfix"></div>
        </div>

        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng hư/lỗi</th>
                        <th>Lý do</th>
                        <th>Minh chứng</th>
                        <th>Ngày ghi nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($damages) > 0)
                        @foreach($damages as $damage)
                            @php
                                $occurredAt = '—';
                                if ($damage->occurred_at) {
                                    $occurredAt = $damage->occurred_at->format('d/m/Y H:i');
                                }
                            @endphp
                            <tr>
                                <td class="text-left"><strong>{{ $damage->product_name }}</strong></td>
                                <td>{{ number_format($damage->quantity) }}</td>
                                <td class="text-left">{{ $damage->reason }}</td>
                                <td>
                                    @if(count($damage->mediaFiles) > 0)
                                        @foreach($damage->mediaFiles as $media)
                                            @php
                                                $mediaUrl = app(\App\Services\CloudinaryService::class)->mediaUrl($media);
                                                $mediaType = 'ảnh';
                                                if ($media->media_type === 'video') {
                                                    $mediaType = 'video';
                                                }
                                            @endphp
                                            <a href="{{ $mediaUrl }}" target="_blank" class="btn btn-default btn-xs">
                                                <i class="fa fa-external-link"></i> Xem {{ $mediaType }} {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </td>
                                <td>{{ $occurredAt }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-muted">Chưa ghi nhận hàng hư hỏng/lỗi trong kho.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="text-center">{{ $damages->links() }}</div>
        </div>
    </div>
</div>
@endsection