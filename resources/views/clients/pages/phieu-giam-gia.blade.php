@extends('layouts.client')

@section('title', 'Phiếu giảm giá')
@section('breadcrumb', 'Phiếu giảm giá')

@section('content')
<style>
    /* CSS rieng trang phieu giam gia: sua card voucher tai day. */
    .client-voucher-title {
        margin-bottom: 16px;
    }

    .client-voucher-title .section-title {
        font-size: 26px;
    }

    .client-voucher-card {
        display: flex;
        border: 1px solid #e5e5e5;
        background: #fff;
        box-shadow: 0 4px 12px rgba(7, 28, 31, 0.04);
    }

    .client-voucher-left {
        display: flex;
        flex: 0 0 76px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #90d5ca;
        color: #fff;
        text-align: center;
    }

    .client-voucher-left span,
    .client-voucher-left strong {
        font-size: 18px;
    }

    .client-voucher-left strong {
        line-height: 1.2;
    }

    .client-voucher-left small {
        font-size: 11px;
    }

    .client-voucher-right {
        flex: 1;
        padding: 8px 10px;
    }

    .client-voucher-card h3 {
        margin: 2px 0 6px;
        color: #80b500;
        font-size: 16px;
        font-weight: 800;
    }

    .client-voucher-card ul {
        margin: 0 0 6px;
        padding-left: 15px;
    }

    .client-voucher-card p,
    .client-voucher-card li {
        margin-bottom: 2px;
        font-size: 11px;
        line-height: 1.35;
    }

    .client-voucher-card .btn {
        padding: 6px 10px;
        font-size: 11px;
        line-height: 1.2;
    }

    .client-voucher-label {
        display: inline-block;
        color: #ee4d2d;
        font-size: 11px;
        font-weight: 700;
    }
</style>
<div class="ltn__product-area ltn__product-gutter mb-120">
    <div class="container">
        <div class="section-title-area ltn__section-title-2 text-center client-voucher-title">
            <h1 class="section-title">Phiếu giảm giá</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            @forelse($phieuGiamGias as $phieuGiamGia)
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="client-voucher-card">
                        <div class="client-voucher-left">
                            <span>GIẢM</span>
                            <strong>{{ $phieuGiamGia->phan_tram_giam_hien_thi }}%</strong>
                            <small>Veggie</small>
                        </div>

                        <div class="client-voucher-right">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="client-voucher-label">Mã giảm giá</span>
                                    <h3>{{ $phieuGiamGia->ma_giam_gia }}</h3>
                                    <small>
                                        Giảm {{ $phieuGiamGia->phan_tram_giam_hien_thi }}%
                                        · Tối đa {{ $phieuGiamGia->giam_toi_da_hien_thi }}
                                    </small>
                                </div>

                                @if($phieuGiamGia->da_su_dung)
                                    <span class="badge badge-secondary align-self-start">Đã dùng</span>
                                @elseif($phieuGiamGia->da_nhan)
                                    <span class="badge badge-success align-self-start">Đã nhận</span>
                                @else
                                    <span class="badge badge-warning align-self-start">Có thể nhận</span>
                                @endif
                            </div>

                            <ul>
                                <li>Đơn từ: {{ $phieuGiamGia->don_toi_thieu_hien_thi }}</li>
                                <li>Hạn dùng: {{ $phieuGiamGia->ngay_het_han_hien_thi }}</li>
                            </ul>

                            @auth
                                @if($phieuGiamGia->da_su_dung)
                                    <button type="button" class="btn btn-secondary w-100" disabled>Đã sử dụng</button>
                                @elseif($phieuGiamGia->da_nhan)
                                    <a href="{{ route('thanh-toan.hien-thi') }}" class="theme-btn-1 btn btn-effect-1 w-100 text-center">
                                        Dùng ngay
                                    </a>
                                @else
                                    <form action="{{ route('phieu-giam-gia.nhan', $phieuGiamGia) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="theme-btn-1 btn btn-effect-1 w-100">
                                            Nhận phiếu giảm giá
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('dang-nhap.hien-thi') }}" class="theme-btn-1 btn btn-effect-1 w-100 text-center">
                                    Đăng nhập để nhận
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">Hiện chưa có phiếu giảm giá khả dụng.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
