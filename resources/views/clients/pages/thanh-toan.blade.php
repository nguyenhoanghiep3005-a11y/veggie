@extends('layouts.client')

@section('title', 'Đặt hàng')
@section('breadcrumb', 'Đặt hàng')

@section('content')
<style>
    /* Chi giu CSS bat buoc cho trang thai JS va nice-select do plugin tu tao. */
    .delivery-option.active {
        border-color: #5cb85c !important;
        background: #f5fff5 !important;
    }

    .delivery-option input[type="radio"] {
        display: none;
    }

    .delivery-option label {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 3px;
        margin: 0;
    }

    .delivery-option label i {
        font-size: 20px;
        color: #5cb85c;
        margin-bottom: 4px;
    }

    .delivery-option label span {
        font-size: 12px;
        color: #888;
        font-weight: 400;
    }

    .select-address .nice-select,
    .nice-select-wrapper .nice-select {
        width: 100% !important;
        float: none !important;
    }

    .select-address .nice-select {
        display: block !important;
        height: auto !important;
        line-height: 1.5 !important;
        padding: 12px 30px 12px 15px !important;
    }

    .select-address .nice-select .current,
    #khu-vuc-dia-chi-moi .nice-select .current {
        display: block !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .select-address .nice-select .list,
    .nice-select-wrapper .nice-select .list {
        width: 100% !important;
        max-height: 250px !important;
        overflow-y: auto !important;
    }

    .select-address .nice-select .option {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .nice-select-wrapper .nice-select {
        height: 50px !important;
        line-height: 48px !important;
        border: 1px solid #e5e5e5 !important;
        border-radius: 5px !important;
        padding-left: 15px !important;
        padding-right: 30px !important;
        background: #fff !important;
    }

    .nice-select-wrapper .nice-select .current {
        font-size: 14px !important;
        color: #444 !important;
    }

    .nice-select-wrapper .nice-select .list {
        border-radius: 5px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1) !important;
    }

    #khu-vuc-dia-chi-moi .nice-select {
        min-width: 0;
    }

    #khu-vuc-dia-chi-moi .nice-select .current {
        width: calc(100% - 18px);
    }

    .checkout-voucher-item.active {
        border-color: #80b500 !important;
        box-shadow: 0 0 0 1px #80b500 inset !important;
    }

    .checkout-voucher-item.is-disabled {
        cursor: not-allowed;
        opacity: .35;
        filter: grayscale(1);
    }

    .checkout-voucher-item.active .checkout-voucher-radio {
        border-color: #80b500 !important;
        background: #80b500 !important;
        color: #fff !important;
    }
    .checkout-address-empty {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .checkout-address-empty-content {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        flex: 1 1 280px;
    }

    .checkout-address-empty-content i {
        margin-top: 4px;
    }

    .checkout-address-empty-content span {
        display: block;
        margin-top: 2px;
    }

    .checkout-address-empty-button {
        white-space: nowrap;
    }
</style>
<div class="ltn__checkout-area mb-105">
    <div class="container">
        <form action="{{ route('thanh-toan.dat-hang') }}" method="POST" id="form-thanh-toan">
            @csrf
            <div class="row checkout-main-row">
                <div class="col-lg-6 checkout-left-column">
                    <div class="ltn__checkout-inner">
                        <div class="ltn__checkout-single-content mt-50" style="margin-top:35px !important;">
                            <h4 class="title-2">Thông tin giao hàng</h4>
                            @if (Auth::check() && ! $coDiaChiDaLuu)
                                <div class="alert alert-warning checkout-address-empty mb-25" role="alert">
                                    <div class="checkout-address-empty-content">
                                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                        <div>
                                            <strong>Bạn chưa có địa chỉ giao hàng đã lưu.</strong>
                                            <span>Thêm địa chỉ vào tài khoản để chọn nhanh khi đặt hàng.</span>
                                        </div>
                                    </div>
                                    <a
                                        href="{{ route('tai-khoan.hien-thi', ['tab' => 'dia-chi', 'them_dia_chi' => 1]) }}"
                                        class="btn theme-btn-1 btn-effect-1 checkout-address-empty-button"
                                    >
                                        <i class="fas fa-plus" aria-hidden="true"></i>
                                        Thêm địa chỉ
                                    </a>
                                </div>
                            @endif

                            <div class="delivery-type-select mb-30" 
                            style="display:flex; gap:12px; flex-wrap:wrap;">
                                @if (Auth::check() && $coDiaChiDaLuu)
                                    <div
                                    id="lua-chon-tai-khoan"
                                    class="delivery-option {{ $loaiGiaoHangDaChon == 'tai_khoan' ? 'active' : '' }}"
                                    style="flex:1; min-width:220px; border:2px solid #e0e0e0; border-radius:8px; padding:14px 16px; cursor:pointer; transition:border-color .2s, background .2s; background:#fff;"
                                    onclick="chonLoaiGiaoHang('tai_khoan')"
                                    >
                                    <input
                                    type="radio"
                                    name="loai_giao_hang"
                                    id="giao-den-tai-khoan"
                                    value="tai_khoan"
                                    {{ $loaiGiaoHangDaChon == 'tai_khoan' ? 'checked' : '' }}
                                    >
                                    <label for="giao-den-tai-khoan">
                                        <i class="fas fa-user-circle"></i>
                                        <strong>Giao đến thông tin tài khoản</strong>
                                        <span>Dùng địa chỉ đã lưu trong tài khoản</span>
                                    </label>
                                </div>
                            @endif

                            <div
                            id="lua-chon-dia-chi-moi"
                            class="delivery-option {{ $loaiGiaoHangDaChon == 'dia_chi_moi' ? 'active' : '' }}"
                            style="flex:1; min-width:220px; border:2px solid #e0e0e0; border-radius:8px; padding:14px 16px; cursor:pointer; transition:border-color .2s, background .2s; background:#fff;"
                            onclick="chonLoaiGiaoHang('dia_chi_moi')"
                            >
                            <input
                            type="radio"
                            name="loai_giao_hang"
                            id="giao-den-dia-chi-moi"
                            value="dia_chi_moi"
                            {{ $loaiGiaoHangDaChon == 'dia_chi_moi' ? 'checked' : '' }}
                            >
                            <label for="giao-den-dia-chi-moi">
                                <i class="fas fa-map-marker-alt"></i>
                                <strong>Giao đến người nhận / địa chỉ khác</strong>
                                <span>Đặt cho người thân hoặc điền địa chỉ mới</span>
                            </label>
                        </div>
                    </div>

                    <div
                    id="khu-vuc-dia-chi-da-luu"
                    class="ltn__checkout-single-content-info {{ Auth::check() && $coDiaChiDaLuu && $loaiGiaoHangDaChon == 'tai_khoan' ? '' : 'd-none' }}"
                    >
                    <h6>Địa chỉ giao hàng đã lưu</h6>

                    <div class="select-address mb-20" style="display:flex; justify-content:space-between; align-items:center; margin:10px 0;">
                        <select
                        id="danh_sach_dia_chi"
                        name="ma_dia_chi_giao_hang"
                        class="input-item checkout-address-select w-100"
                        >
                        @foreach ($diaChis as $diaChi)
                            <option
                            value="{{ $diaChi->ma_dia_chi_giao_hang }}"
                            {{ $diaChiDaChon && $diaChiDaChon->ma_dia_chi_giao_hang == $diaChi->ma_dia_chi_giao_hang ? 'selected' : '' }}
                            >
                            {{ $diaChi->ho_ten }} - {{ $diaChi->dia_chi }}, {{ $diaChi->tinh_thanh }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="address-info-box checkout-address-info p-3 mb-20">
                <div class="mb-2">
                    <strong>Người nhận:</strong>
                    <span id="ten-nguoi-nhan-hien-thi">{{ $tenNguoiNhan }}</span>
                </div>
                <div class="mb-2">
                    <strong>Số điện thoại:</strong>
                    <span id="so-dien-thoai-hien-thi">{{ $soDienThoaiNguoiNhan }}</span>
                </div>
                <div>
                    <strong>Địa chỉ giao hàng:</strong>
                    <span id="dia-chi-hien-thi">
                        {{ $diaChiNguoiNhan }}{{ $tinhThanhNguoiNhan ? ', '.$tinhThanhNguoiNhan : '' }}
                    </span>
                </div>
            </div>
        </div>

        <div
        id="khu-vuc-dia-chi-moi"
        class="ltn__checkout-single-content-info {{ ! Auth::check() || ! $coDiaChiDaLuu || $loaiGiaoHangDaChon == 'dia_chi_moi' ? '' : 'd-none' }}"
        >
        <h6>Thông tin người nhận</h6>

        <div class="row">
            <div class="col-md-6">
                <div class="input-item input-item-name ltn__custom-icon">
                    <input
                    type="text"
                    id="ho_ten_nguoi_nhan"
                    name="ho_ten_nguoi_nhan"
                    placeholder="Họ và tên người nhận *"
                    class="checkout-input"
                    >
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-item input-item-phone ltn__custom-icon">
                    <input
                    type="text"
                    id="so_dien_thoai_nguoi_nhan"
                    name="so_dien_thoai_nguoi_nhan"
                    inputmode="numeric"
                    maxlength="11"
                    placeholder="Số điện thoại *"
                    class="checkout-input"
                    >
                </div>
            </div>
        </div>

        <div class="input-item">
            <input
            type="text"
            id="dia_chi_nguoi_nhan"
            name="dia_chi_nguoi_nhan"
            placeholder="Số nhà, tên đường *"
            class="checkout-input checkout-input-wide"
            >
        </div>
        <div class="input-item input-item-email ltn__custom-icon">
            <input
            type="email"
            id="email_nhan_hoa_don"
            name="email_nhan_hoa_don"
            placeholder="Gmail nhận hóa đơn *"
            class="checkout-input checkout-input-wide"
            >
        </div>

        <h6 class="mt-10 checkout-section-title">Khu vực giao nhận hàng</h6>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="nice-select-wrapper">
                    <select id="ma_tinh_moi" name="ma_tinh" class="nice-select w-100">
                        <option value="">Tỉnh/thành *</option>
                    </select>
                    <input type="hidden" id="ten_tinh_moi" name="ten_tinh">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="nice-select-wrapper">
                    <select id="ma_huyen_moi" name="ma_huyen" class="nice-select w-100" disabled>
                        <option value="">Quận/huyện *</option>
                    </select>
                    <input type="hidden" id="ten_huyen_moi" name="ten_huyen">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="nice-select-wrapper">
                    <select id="ma_xa_moi" name="ma_xa" class="nice-select w-100" disabled>
                        <option value="">Phường/xã *</option>
                    </select>
                    <input type="hidden" id="ten_xa_moi" name="ten_xa">
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<div class="col-lg-6 mt-50 checkout-right-column">
    <div
    id="tong-ket-thanh-toan"
    class="shoping-cart-total" style="margin-top:35px !important;"
    data-tam-tinh="{{ $tamTinh }}"
    data-phi-van-chuyen="{{ $phiVanChuyen }}"
    data-so-tien-giam="{{ $soTienGiam }}"
    data-tong-tien="{{ $tongTien }}"
    data-san-sang="{{ $phiVanChuyen > 0 ? 1 : 0 }}"
    >
    <h4 class="title-2 text-center">Tổng sản phẩm</h4>

    @if (Auth::check())
        <button
        type="button"
        class="checkout-voucher-entry mb-3" style="display:flex; align-items:center; justify-content:space-between; width:100%; padding:14px 16px; border:1px solid #eeeeee; background:#fff; color:#071c1f; font-weight:700; text-align:left;"
        data-bs-toggle="modal"
        data-bs-target="#modal-phieu-giam-gia"
        >
        <span><i class="fas fa-ticket-alt"></i> Voucher</span>
        <span class="checkout-selected-coupon" style="color:#007bff; font-weight:500;">
            {{ $phieuGiamGia ? 'Đã chọn: '.$phieuGiamGia->ma_giam_gia : 'Chọn hoặc nhập mã' }}
        </span>
    </button>
@endif

<table class="table">
    <tbody>
        @foreach ($sanPhamGioHangs as $sanPhamGioHang)
            <tr>
                <td>
                    {{ $sanPhamGioHang['ten'] }}
                    <strong>× {{ $sanPhamGioHang['so_luong'] }}</strong>
                </td>
                <td>
                    {{ number_format($sanPhamGioHang['tam_tinh'], 0, ',', '.') }}<small>đ</small>
                </td>
            </tr>
        @endforeach

        <tr>
            <td>Phí vận chuyển</td>
            <td class="phi-van-chuyen-thanh-toan">
                {{ $phiVanChuyen > 0 ? number_format($phiVanChuyen, 0, ',', '.').'đ' : 'Chưa tính' }}
            </td>
        </tr>
        <tr class="thong-bao-phi-van-chuyen {{ $phiVanChuyen > 0 ? 'd-none' : '' }}" style="color:#dc3545; font-weight:600;">
            <td colspan="2">
                Vui lòng chọn hoặc nhập đầy đủ khu vực để tính phí vận chuyển.
            </td>
        </tr>

        @if (Auth::check())
            <tr>
                <td>Giảm giá</td>
                <td class="so-tien-giam-thanh-toan">
                    {{ number_format($soTienGiam, 0, ',', '.') }}<small>đ</small>
                </td>
            </tr>
        @endif

        <tr>
            <td><strong>Tổng tiền</strong></td>
            <td>
                <strong class="tong-tien-thanh-toan">
                    {{ number_format($tongTien, 0, ',', '.') }}<small>đ</small>
                </strong>
            </td>
        </tr>
    </tbody>
</table>
</div>

<div class="ltn__checkout-payment-method mt-50" style="margin-top:35px !important;">
    <h4 class="title-2 text-center checkout-payment-title" style="font-size:18px; margin-bottom:15px;">Phương thức thanh toán</h4>

    <div id="checkout_payment" class="small-payment-methods">
        <div class="card mb-2 checkout-payment-card checkout-cod-card {{ $loaiGiaoHangDaChon == 'tai_khoan' && Auth::check() ? '' : 'd-none' }}" style="border:1px solid #e0e0e0; border-radius:4px;">
            <h5 class="ltn__card-title checkout-payment-heading" style="margin:0; padding:10px 15px; font-size:14px;">
                <input
                type="radio"
                name="phuong_thuc"
                value="tien_mat"
                id="thanh-toan-tien-mat"
                class="checkout-payment-radio" style="margin-right:8px;"
                {{ $loaiGiaoHangDaChon == 'tai_khoan' && Auth::check() ? 'checked' : '' }}
                >
                <label for="thanh-toan-tien-mat" class="checkout-payment-label" style="display:inline-flex; align-items:center; justify-content:space-between; width:90%; cursor:pointer; margin:0; font-weight:600;">
                    Thanh toán khi nhận hàng
                    <img
                    src="{{ asset('assets/clients/img/icons/cash.png') }}"
                    alt="COD"
                    class="checkout-payment-icon checkout-payment-icon-cash" style="max-height:20px;"
                    >
                </label>
            </h5>
        </div>

        <div class="card mb-2 checkout-payment-card" style="border:1px solid #e0e0e0; border-radius:4px;">
            <h5 class="collapsed ltn__card-title checkout-payment-heading" style="margin:0; padding:10px 15px; font-size:14px;">
                <input
                type="radio"
                name="phuong_thuc"
                value="paypal"
                id="thanh-toan-paypal"
                class="checkout-payment-radio" style="margin-right:8px;"
                {{ $loaiGiaoHangDaChon == 'dia_chi_moi' || ! Auth::check() ? 'checked' : '' }}
                >
                <label for="thanh-toan-paypal" class="checkout-payment-label" style="display:inline-flex; align-items:center; justify-content:space-between; width:90%; cursor:pointer; margin:0; font-weight:600;">
                    PayPal
                    <img
                    src="{{ asset('assets/clients/img/icons/payment-3.png') }}"
                    alt="PayPal"
                    class="checkout-payment-icon checkout-payment-icon-paypal" style="max-height:18px;"
                    >
                </label>
            </h5>
        </div>

        <div class="alert alert-info mt-2 checkout-paypal-only-alert {{ $loaiGiaoHangDaChon == 'dia_chi_moi' || ! Auth::check() ? '' : 'd-none' }}">
            <i class="fas fa-lock"></i>
            Giao đến người nhận hoặc địa chỉ khác chỉ thanh toán trước bằng <strong>PayPal</strong>.
            Đơn hàng này không lưu vào lịch sử đơn của tài khoản.
        </div>
    </div>

    <button
    type="submit"
    id="nut-dat-hang"
    class="btn theme-btn-1 btn-effect-1 text-uppercase w-100 checkout-order-button" style="padding:12px; font-size:14px; font-weight:bold;"
    >
    Đặt hàng
</button>

<div id="paypal-button-container" class="mt-2 d-none"></div>
</div>
</div>
</div>
</form>
</div>
</div>

@if (Auth::check())
    <div class="modal fade checkout-voucher-modal" id="modal-phieu-giam-gia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered checkout-voucher-dialog" style="width:calc(100% - 30px); max-width:720px;">
            <div class="modal-content">
                <div class="modal-header" style="padding:16px 18px;">
                    <h5 class="modal-title">Voucher</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="padding:16px 18px;">
                    <div class="input-group">
                        <input
                        type="text"
                        id="ma_giam_gia"
                        class="form-control" style="height:46px;"
                        value="{{ $maGiamGia }}"
                        placeholder="Nhập mã giảm giá"
                        >
                        <div class="input-group-append">
                            <button type="button" id="nut-ap-dung-phieu" class="btn btn-success" style="height:46px;">
                                Áp dụng
                            </button>
                        </div>
                    </div>

                    <div class="checkout-voucher-list mt-4" style="max-height:300px; overflow-y:auto;">
                        @forelse ($phieuDaNhans as $phieuDaNhan)
                            <button
                            type="button"
                            class="checkout-voucher-item {{ $phieuDaNhan->dang_duoc_chon ? 'active' : '' }} {{ $phieuDaNhan->co_the_ap_dung ? '' : 'is-disabled' }}" style="display:flex; align-items:stretch; width:100%; min-height:80px; margin-bottom:8px; padding:0; border:1px solid #e5e5e5; background:#fff; text-align:left;"
                            data-ma-giam-gia="{{ $phieuDaNhan->ma_giam_gia }}"
                            aria-pressed="{{ $phieuDaNhan->dang_duoc_chon ? 'true' : 'false' }}"
                            {{ $phieuDaNhan->co_the_ap_dung ? '' : 'disabled' }}
                            >
                            <span class="checkout-voucher-ticket" style="display:flex; flex:0 0 105px; flex-direction:column; align-items:center; justify-content:center; background:#90d5ca; color:#fff;">
                                <strong style="font-size:22px;">{{ $phieuDaNhan->phan_tram_giam_hien_thi }}%</strong>
                            </span>
                            <span class="checkout-voucher-info" style="flex:1; padding:10px 12px;">
                                <strong>{{ $phieuDaNhan->ma_giam_gia }}</strong>
                                <small style="display:block; color:#777;">{{ $phieuDaNhan->giam_toi_da_hien_thi }}</small>
                                <small style="display:block; color:#777;">{{ $phieuDaNhan->don_toi_thieu_hien_thi }}</small>
                                <small style="display:block; color:#777;">Hạn dùng: {{ $phieuDaNhan->het_han_hien_thi }}</small>
                            </span>
                            <span class="checkout-voucher-radio" style="display:flex; flex:0 0 24px; width:24px; height:24px; margin:auto 14px; align-items:center; justify-content:center; border:2px solid #b8b8b8; border-radius:50%; color:transparent;" aria-hidden="true">
                                <i class="fas fa-check"></i>
                            </span>
                        </button>
                    @empty
                        <div class="alert alert-info mb-0">Tài khoản chưa có voucher.</div>
                    @endforelse
                </div>
                <small id="thong-bao-phieu" class="d-block mt-3"></small>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
