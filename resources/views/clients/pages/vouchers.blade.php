@extends('layouts.client')

@section('title', 'Voucher')
@section('breadcrumb', 'Voucher')

@section('content')
<div class="ltn__product-area ltn__product-gutter mb-120">
    <div class="container">
        <div class="section-title-area ltn__section-title-2 text-center client-voucher-title">
            <h1 class="section-title">Voucher</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            @if(count($coupons) > 0)
                @foreach($coupons as $coupon)
                    @php
                        $claimed = false;
                        $used = false;

                        if (in_array($coupon->id, $claimedIds, true)) {
                            $claimed = true;
                        }

                        if (in_array($coupon->id, $usedIds, true)) {
                            $used = true;
                        }

                        $discount = number_format($coupon->discount_percent, 2, '.', '');
                        $discount = rtrim($discount, '0');
                        $discount = rtrim($discount, '.');

                        $minimumOrder = 'Không yêu cầu';
                        if ($coupon->minimum_order_amount > 0) {
                            $minimumOrder = number_format($coupon->minimum_order_amount, 0, ',', '.').' đ';
                        }

                        $maxDiscount = 'Không giới hạn';
                        if ($coupon->max_discount_amount) {
                            $maxDiscount = number_format($coupon->max_discount_amount, 0, ',', '.').' đ';
                        }

                        $expiresAt = 'Không giới hạn';
                        if ($coupon->expires_at) {
                            $expiresAt = $coupon->expires_at->format('d/m/Y');
                        }
                    @endphp

                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="client-voucher-card">
                            <div class="client-voucher-left">
                                <span>SALE</span>
                                <strong>{{ $discount }}%</strong>
                                <small>Veggie</small>
                            </div>

                            <div class="client-voucher-right">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span class="client-voucher-label">Mã voucher</span>
                                        <h3>{{ $coupon->code }}</h3>
                                        <small>Giảm {{ $discount }}% · Tối đa {{ $maxDiscount }}</small>
                                    </div>

                                    @if($used)
                                        <span class="badge badge-secondary align-self-start">Đã dùng</span>
                                    @elseif($claimed)
                                        <span class="badge badge-success align-self-start">Đã thu thập</span>
                                    @else
                                        <span class="badge badge-warning align-self-start">Có thể lấy</span>
                                    @endif
                                </div>

                                <ul>
                                    <li>Đơn từ: {{ $minimumOrder }}</li>
                                    <li>HSD: {{ $expiresAt }}</li>
                                </ul>

                                @auth
                                    @if($used)
                                        <button type="button" class="btn btn-secondary w-100" disabled>Đã sử dụng</button>
                                    @elseif($claimed)
                                        <a href="{{ route('checkout') }}" class="theme-btn-1 btn btn-effect-1 w-100 text-center">
                                            Dùng ngay
                                        </a>
                                    @else
                                        <form action="{{ route('vouchers.claim', $coupon) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="theme-btn-1 btn btn-effect-1 w-100">
                                                Lấy voucher
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="theme-btn-1 btn btn-effect-1 w-100 text-center">
                                        Đăng nhập để lấy
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-info text-center">Hiện chưa có voucher khả dụng.</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
