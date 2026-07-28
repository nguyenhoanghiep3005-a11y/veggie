@extends('layouts.admin')

@section('title', 'Phiếu hàng hư, lỗi')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Phiếu hàng hư, lỗi</h3>
        </div>
    </div>
    <div class="clearfix"></div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="x_panel">
        <div class="x_title">
            <h2>Danh sách sản phẩm hư hỏng/lỗi đã ghi nhận</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Sản phẩm hư/lỗi</th>
                        <th>Số lượng hư/lỗi</th>
                        <th>Mô tả</th>
                        <th>Ngày ghi nhận</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($damageSlips) > 0)
                        @foreach($damageSlips as $damageSlip)
                            @php
                                $productSummary = $damageSlip->productSummary();
                                if (! $productSummary) {
                                    $productSummary = '—';
                                }

                                $occurredAt = '—';
                                if ($damageSlip->occurred_at) {
                                    $occurredAt = $damageSlip->occurred_at->format('d/m/Y H:i');
                                }
                            @endphp
                            <tr>
                                <td><strong>{{ $damageSlip->code }}</strong></td>
                                <td class="text-left">{{ $productSummary }}</td>
                                <td>{{ number_format($damageSlip->totalQuantity()) }}</td>
                                <td class="text-left">{{ \Illuminate\Support\Str::limit($damageSlip->reason, 80) }}</td>
                                <td>{{ $occurredAt }}</td>
                                <td>
                                    <a href="{{ route('admin.damage-slips.show', $damageSlip) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-muted">Chưa có phiếu hủy hàng.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="text-center">{{ $damageSlips->links() }}</div>
        </div>
    </div>
</div>
@endsection