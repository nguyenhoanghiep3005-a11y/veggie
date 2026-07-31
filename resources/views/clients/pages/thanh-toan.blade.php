@extends('layouts.client')

@section('title', 'Đặt hàng')
@section('breadcrumb', 'Đặt hàng')

@section('content')
<div class="ltn__checkout-area mb-105">
    <div class="container">
        <form action="{{ route('thanh-toan.dat-hang') }}" method="POST" id="form-thanh-toan">
            @csrf
            <input type="hidden" name="loai_giao_hang" id="loai_giao_hang" value="{{ $loaiGiaoHangDaChon }}">
            <input type="hidden" name="ma_dia_chi_giao_hang" id="ma_dia_chi_giao_hang" value="{{ $maDiaChiDaChon }}">

            <div class="row checkout-main-row">
                <div class="col-lg-6 checkout-left-column">
                    <div class="ltn__checkout-inner">
                        <div class="ltn__checkout-single-content mt-50">
                            <h4 class="title-2">Thông tin giao hàng</h4>

                            <div class="delivery-type-select mb-30">
                                @if (Auth::check() && $coDiaChiDaLuu)
                                    <div class="delivery-option {{ $loaiGiaoHangDaChon == 'tai_khoan' ? 'active' : '' }}"
                                        id="lua-chon-tai-khoan"
                                        onclick="chonLoaiGiaoHang('tai_khoan')">
                                        <input type="radio"
                                            name="loai_giao_hang_hien_thi"
                                            id="giao-den-tai-khoan"
                                            value="tai_khoan"
                                            {{ $loaiGiaoHangDaChon == 'tai_khoan' ? 'checked' : '' }}>
                                        <label for="giao-den-tai-khoan">
                                            <i class="fas fa-user-circle"></i>
                                            <strong>Giao đến thông tin tài khoản</strong>
                                            <span>Dùng địa chỉ đã lưu trong tài khoản</span>
                                        </label>
                                    </div>
                                @endif

                                <div class="delivery-option {{ $loaiGiaoHangDaChon == 'dia_chi_moi' ? 'active' : '' }}"
                                    id="lua-chon-dia-chi-moi"
                                    onclick="chonLoaiGiaoHang('dia_chi_moi')">
                                    <input type="radio"
                                        name="loai_giao_hang_hien_thi"
                                        id="giao-den-dia-chi-moi"
                                        value="dia_chi_moi"
                                        {{ $loaiGiaoHangDaChon == 'dia_chi_moi' ? 'checked' : '' }}>
                                    <label for="giao-den-dia-chi-moi">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <strong>Giao đến người nhận / địa chỉ khác</strong>
                                        <span>Đặt cho người thân hoặc điền địa chỉ mới</span>
                                    </label>
                                </div>
                            </div>

                            <div id="khu-vuc-dia-chi-da-luu"
                                class="ltn__checkout-single-content-info {{ Auth::check() && $coDiaChiDaLuu && $loaiGiaoHangDaChon == 'tai_khoan' ? '' : 'd-none' }}">
                                <h6>Địa chỉ giao hàng đã lưu</h6>
                                <div class="select-address mb-20">
                                    <select id="danh_sach_dia_chi" class="input-item checkout-address-select w-100">
                                        @foreach ($diaChis as $diaChi)
                                            <option value="{{ $diaChi->ma_dia_chi_giao_hang }}"
                                                {{ $diaChiDaChon && $diaChiDaChon->ma_dia_chi_giao_hang == $diaChi->ma_dia_chi_giao_hang ? 'selected' : '' }}>
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
                                        <span id="dia-chi-hien-thi">{{ $diaChiNguoiNhan }}{{ $tinhThanhNguoiNhan ? ', '.$tinhThanhNguoiNhan : '' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div id="khu-vuc-dia-chi-moi"
                                class="ltn__checkout-single-content-info {{ !Auth::check() || ! $coDiaChiDaLuu || $loaiGiaoHangDaChon == 'dia_chi_moi' ? '' : 'd-none' }}">
                                <h6>Thông tin người nhận</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-item input-item-name ltn__custom-icon">
                                            <input type="text"
                                                id="ho_ten_nguoi_nhan"
                                                name="ho_ten_nguoi_nhan"
                                                value="{{ old('ho_ten_nguoi_nhan') }}"
                                                placeholder="Họ và tên người nhận *"
                                                class="checkout-input">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-item-phone ltn__custom-icon">
                                            <input type="text"
                                                id="so_dien_thoai_nguoi_nhan"
                                                name="so_dien_thoai_nguoi_nhan"
                                                value="{{ old('so_dien_thoai_nguoi_nhan') }}"
                                                placeholder="Số điện thoại *"
                                                class="checkout-input">
                                        </div>
                                    </div>
                                </div>

                                <div class="input-item">
                                    <input type="text"
                                        id="dia_chi_nguoi_nhan"
                                        name="dia_chi_nguoi_nhan"
                                        value="{{ old('dia_chi_nguoi_nhan') }}"
                                        placeholder="Số nhà, tên đường *"
                                        class="checkout-input checkout-input-wide">
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
                    <div class="shoping-cart-total"
                        id="tong-ket-thanh-toan"
                        data-tam-tinh="{{ $tamTinh }}"
                        data-phi-van-chuyen="{{ $phiVanChuyen }}"
                        data-so-tien-giam="{{ $soTienGiam }}"
                        data-tong-tien="{{ $tongTien }}"
                        data-san-sang="{{ $phiVanChuyen > 0 ? 1 : 0 }}"
                        data-duong-dan-dia-chi="{{ route('thanh-toan.dia-chi') }}"
                        data-duong-dan-phi-van-chuyen="{{ route('thanh-toan.phi-van-chuyen') }}"
                        data-duong-dan-phieu-giam-gia="{{ route('thanh-toan.ap-dung-phieu-giam-gia') }}"
                        data-duong-dan-paypal="{{ route('thanh-toan.paypal') }}">
                        <h4 class="title-2 text-center">Tổng sản phẩm</h4>

                        @if (Auth::check())
                            <button type="button"
                                class="checkout-voucher-entry mb-3"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-phieu-giam-gia">
                                <span><i class="fas fa-ticket-alt"></i> Voucher</span>
                                <span class="checkout-selected-coupon">
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
                                        <td>{{ number_format($sanPhamGioHang['tam_tinh'], 0, ',', '.') }}<small>đ</small></td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>Phí vận chuyển</td>
                                    <td class="phi-van-chuyen-thanh-toan">
                                        {{ $phiVanChuyen > 0 ? number_format($phiVanChuyen, 0, ',', '.').'đ' : 'Chưa tính' }}
                                    </td>
                                </tr>
                                <tr class="thong-bao-phi-van-chuyen {{ $phiVanChuyen > 0 ? 'd-none' : '' }}">
                                    <td colspan="2">Vui lòng chọn hoặc nhập đầy đủ khu vực để tính phí vận chuyển.</td>
                                </tr>
                                @if (Auth::check())
                                    <tr>
                                        <td>Giảm giá</td>
                                        <td class="so-tien-giam-thanh-toan">{{ number_format($soTienGiam, 0, ',', '.') }}<small>đ</small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Tổng tiền</strong></td>
                                    <td><strong class="tong-tien-thanh-toan">{{ number_format($tongTien, 0, ',', '.') }}<small>đ</small></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="ltn__checkout-payment-method mt-50">
                        <h4 class="title-2 text-center checkout-payment-title">Phương thức thanh toán</h4>
                        <div id="checkout_payment" class="small-payment-methods">
                            <div class="card mb-2 checkout-payment-card checkout-cod-card {{ $loaiGiaoHangDaChon == 'tai_khoan' && Auth::check() ? '' : 'd-none' }}">
                                <h5 class="ltn__card-title checkout-payment-heading">
                                    <input type="radio"
                                        name="phuong_thuc"
                                        value="tien_mat"
                                        id="thanh-toan-tien-mat"
                                        class="checkout-payment-radio"
                                        {{ $loaiGiaoHangDaChon == 'tai_khoan' && Auth::check() ? 'checked' : '' }}>
                                    <label for="thanh-toan-tien-mat" class="checkout-payment-label">
                                        Thanh toán khi nhận hàng
                                        <img src="{{ asset('assets/clients/img/icons/cash.png') }}"
                                            alt="COD"
                                            class="checkout-payment-icon checkout-payment-icon-cash">
                                    </label>
                                </h5>
                            </div>

                            <div class="card mb-2 checkout-payment-card">
                                <h5 class="collapsed ltn__card-title checkout-payment-heading">
                                    <input type="radio"
                                        name="phuong_thuc"
                                        value="paypal"
                                        id="thanh-toan-paypal"
                                        class="checkout-payment-radio"
                                        {{ $loaiGiaoHangDaChon == 'dia_chi_moi' || ! Auth::check() ? 'checked' : '' }}>
                                    <label for="thanh-toan-paypal" class="checkout-payment-label">
                                        PayPal
                                        <img src="{{ asset('assets/clients/img/icons/payment-3.png') }}"
                                            alt="PayPal"
                                            class="checkout-payment-icon checkout-payment-icon-paypal">
                                    </label>
                                </h5>
                            </div>
                        <div class="alert alert-info mt-2 checkout-paypal-only-alert {{ $loaiGiaoHangDaChon == 'dia_chi_moi' || ! Auth::check() ? '' : 'd-none' }}">
                            <i class="fas fa-lock"></i>
                            Giao đến người nhận hoặc địa chỉ khác chỉ thanh toán trước bằng <strong>PayPal</strong>.
                            Đơn hàng này không lưu vào lịch sử đơn của tài khoản.
                        </div>

                        </div>

                        <button class="btn theme-btn-1 btn-effect-1 text-uppercase w-100 checkout-order-button"
                            type="submit"
                            id="nut-dat-hang">
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
        <div class="modal-dialog modal-dialog-centered checkout-voucher-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Voucher</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input type="text" id="ma_giam_gia" class="form-control"
                            value="{{ $maGiamGia }}"
                            placeholder="Nhập mã giảm giá">
                        <div class="input-group-append">
                            <button type="button" id="nut-ap-dung-phieu" class="btn btn-success">Áp dụng</button>
                        </div>
                    </div>

                    <div class="checkout-voucher-list mt-4">
                        @forelse ($phieuDaNhans as $phieuDaNhan)
                            <button type="button"
                                class="checkout-voucher-item {{ $phieuDaNhan->dang_duoc_chon ? 'active' : '' }} {{ $phieuDaNhan->co_the_ap_dung ? '' : 'is-disabled' }}"
                                data-ma-giam-gia="{{ $phieuDaNhan->ma_giam_gia }}"
                                aria-pressed="{{ $phieuDaNhan->dang_duoc_chon ? 'true' : 'false' }}"
                                {{ $phieuDaNhan->co_the_ap_dung ? '' : 'disabled' }}>
                                <span class="checkout-voucher-ticket">
                                    <strong>{{ $phieuDaNhan->phan_tram_giam_hien_thi }}%</strong>
                                </span>
                                <span class="checkout-voucher-info">
                                    <strong>{{ $phieuDaNhan->ma_giam_gia }}</strong>
                                    <small>{{ $phieuDaNhan->giam_toi_da_hien_thi }}</small>
                                    <small>{{ $phieuDaNhan->don_toi_thieu_hien_thi }}</small>
                                    <small>Hạn dùng: {{ $phieuDaNhan->het_han_hien_thi }}</small>
                                </span>
                                <span class="checkout-voucher-radio" aria-hidden="true">
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