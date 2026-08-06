@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $donHang->ma_don_hang)


@push('styles')
<style>
    /* CSS rieng cho nhan trang thai. */
    .custom-badge,
    .label {
        display: inline-block;
        min-width: 90px;
        padding: 8px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.3;
        text-align: center;
        white-space: normal;
    }

    .badge-success,
    .label-success {
        background-color: #28a745 !important;
        color: #fff !important;
    }

    .badge-warning,
    .label-warning {
        background-color: #ffc107 !important;
        color: #111 !important;
    }

    .badge-danger,
    .label-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
    }

    .badge-info,
    .label-info {
        background-color: #17a2b8 !important;
        color: #fff !important;
    }

    .badge-primary,
    .label-primary {
        background-color: #007bff !important;
        color: #fff !important;
    }

    .badge-secondary,
    .label-default,
    .label-secondary {
        background-color: #6c757d !important;
        color: #fff !important;
    }</style>
@endpush

@section('content')
<div class="right_col admin-order-detail-page" id="trang-chi-tiet-don-hang" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Chi tiết đơn hàng #{{ $donHang->ma_don_hang }}</h3>
        </div>
        <div class="title_right">
            <a href="{{ route('admin.don-hang.danh-sach') }}" class="btn btn-default pull-right">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>
<div class="clearfix"></div>
<div id="thong-bao-don-hang"></div>

<div class="x_panel">
    <div class="x_title">
        <h2>Thông tin đơn hàng</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <section class="content invoice">
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    <strong>Từ cửa hàng</strong>
                    <address>
                    <strong>HIEP SHOP</strong><br>
                    Tân Phú, Hồ Chí Minh, VN<br>
                    Điện thoại: 0388536385<br>
                    Email: nguyenhoanghiep@gmail.com
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <strong>Người nhận</strong>
                <address>
                <strong>{{ $donHang->ten_nguoi_nhan }}</strong><br>
                {{ $donHang->dia_chi_nguoi_nhan }}<br>
                {{ $donHang->tinh_thanh_nguoi_nhan }}<br>
                Điện thoại: {{ $donHang->so_dien_thoai_nguoi_nhan }}
            </address>
        </div>
        <div class="col-sm-4 invoice-col">
            <b>Mã đơn: #{{ $donHang->ma_don_hang }}</b><br>
            <b>Ngày tạo:</b> {{ $donHang->created_at->format('d/m/Y H:i') }}<br>
            <b>Tài khoản:</b> {{ $donHang->ten_khach_hang }}<br>
            <b>Email:</b> {{ $donHang->email_khach_hang }}<br>
            <b>Trạng thái:</b>
            @if ($yeuCauDoiTra)
                @if ($yeuCauDoiTra->trang_thai == 'cho_duyet')
                    <span class="custom-badge badge badge-warning">Chờ duyệt yêu cầu</span>
                @elseif ($yeuCauDoiTra->trang_thai == 'da_duyet')
                    <span class="custom-badge badge badge-info">Đã duyệt yêu cầu</span>
                @elseif ($yeuCauDoiTra->trang_thai == 'dang_xu_ly')
                    <span class="custom-badge badge badge-info">Đang xử lý đổi trả</span>
                @elseif ($yeuCauDoiTra->trang_thai == 'dang_giao_hang_doi')
                    <span class="custom-badge badge badge-primary">Đang giao hàng đổi</span>
                @elseif ($yeuCauDoiTra->trang_thai == 'hoan_tat')
                    <span class="custom-badge badge badge-success">Hoàn tất đổi trả</span>
                @else
                    <span class="custom-badge badge badge-secondary">-</span>
                @endif
            @else
                @if ($donHang->trang_thai == 'cho_xac_nhan')
                    <span class="custom-badge badge badge-warning">Chờ xác nhận</span>
                @elseif ($donHang->trang_thai == 'da_xac_nhan')
                    <span class="custom-badge badge badge-primary">Đã xác nhận</span>
                @elseif ($donHang->trang_thai == 'dang_giao')
                    <span class="custom-badge badge badge-info">Đang giao hàng</span>
                @elseif ($donHang->trang_thai == 'dang_hoan_hang')
                    <span class="custom-badge badge badge-info">Đang hoàn hàng</span>
                @elseif ($donHang->trang_thai == 'hoan_thanh')
                    <span class="custom-badge badge badge-success">Hoàn thành</span>
                @elseif ($donHang->trang_thai == 'da_hoan_ve_kho')
                    <span class="custom-badge badge badge-success">Đã hoàn về kho</span>
                @elseif ($donHang->trang_thai == 'giao_that_bai')
                    <span class="custom-badge badge badge-danger">Giao hàng thất bại</span>
                @elseif ($donHang->trang_thai == 'da_huy' && $donHang->nguoi_huy == 'quan_tri')
                    <span class="custom-badge badge badge-danger">Đã hủy bởi Shop</span>
                @elseif ($donHang->trang_thai == 'da_huy')
                    <span class="custom-badge badge badge-danger">Đã hủy</span>
                @else
                    <span class="custom-badge badge badge-secondary">{{ $donHang->trang_thai }}</span>
                @endif
            @endif
        </div>
    </div>

    <hr>

    @if ($yeuCauDoiTra)
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-refresh"></i> Thông tin đổi trả</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-8">
                        <p>
                            <strong>Loại:</strong>
                            @if ($yeuCauDoiTra->loai == 'hang_loi')
                                Đổi trả do hàng hư hỏng hoặc bị lỗi
                            @else
                                -
                            @endif
                        </p>
                        <p>
                            <strong>Trạng thái:</strong>
                            @if ($yeuCauDoiTra->trang_thai == 'cho_duyet')
                                <span class="custom-badge badge badge-warning">Chờ duyệt yêu cầu</span>
                            @elseif ($yeuCauDoiTra->trang_thai == 'da_duyet')
                                <span class="custom-badge badge badge-info">Đã duyệt yêu cầu</span>
                            @elseif ($yeuCauDoiTra->trang_thai == 'dang_xu_ly')
                                <span class="custom-badge badge badge-info">Đang xử lý đổi trả</span>
                            @elseif ($yeuCauDoiTra->trang_thai == 'dang_giao_hang_doi')
                                <span class="custom-badge badge badge-primary">Đang giao hàng đổi</span>
                            @elseif ($yeuCauDoiTra->trang_thai == 'hoan_tat')
                                <span class="custom-badge badge badge-success">Hoàn tất đổi trả</span>
                            @else
                                <span class="custom-badge badge badge-secondary">-</span>
                            @endif
                        </p>
                        <p><strong>Mô tả:</strong> {{ $yeuCauDoiTra->mo_ta }}</p>
                        <p><strong>Ngày gửi yêu cầu:</strong> {{ $ngayYeuCau }}</p>
                        <p><strong>Ngày duyệt:</strong> {{ $ngayDuyet }}</p>
                        <p><strong>Ngày nhận hàng:</strong> {{ $ngayNhanHang }}</p>
                    </div>
                    <div class="col-md-4">
                        @if ($yeuCauDoiTra->trang_thai == 'cho_duyet')
                            <button type="button" class="btn btn-primary btn-block nut-duyet-doi-tra" data-ma-yeu-cau="{{ $yeuCauDoiTra->ma_yeu_cau_doi_tra }}">
                                <i class="fa fa-check"></i> Duy&#7879;t y&#234;u c&#7847;u
                            </button>
                        @elseif ($yeuCauDoiTra->trang_thai == 'da_duyet')
                            <button type="button" class="btn btn-success btn-block nut-nhan-doi-tra" data-ma-yeu-cau="{{ $yeuCauDoiTra->ma_yeu_cau_doi_tra }}">
                                <i class="fa fa-inbox"></i> X&#225;c nh&#7853;n &#273;&#227; nh&#7853;n h&#224;ng l&#7895;i
                            </button>
                        @elseif ($yeuCauDoiTra->trang_thai == 'dang_xu_ly')
                            <button type="button" class="btn btn-primary btn-block nut-giao-hang-doi" data-ma-yeu-cau="{{ $yeuCauDoiTra->ma_yeu_cau_doi_tra }}">
                                <i class="fa fa-truck"></i> B&#7855;t &#273;&#7847;u giao h&#224;ng &#273;&#7893;i
                            </button>
                        @elseif ($yeuCauDoiTra->trang_thai == 'dang_giao_hang_doi')
                            <button type="button" class="btn btn-success btn-block nut-hoan-tat-doi-tra" data-ma-yeu-cau="{{ $yeuCauDoiTra->ma_yeu_cau_doi_tra }}">
                                <i class="fa fa-check-circle"></i> Ho&#224;n t&#7845;t &#273;&#7893;i tr&#7843;
                            </button>
                        @endif
                    </div>
                </div>

                <div class="row text-center return-flow-admin" style="margin: 20px 0 10px;">
                    <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 10px;"><div class="well well-sm" style="margin-bottom: 0; background: #5cb85c; color: #fff;">1. &#272;&#227; g&#7917;i</div></div>
                    <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 10px;"><div class="well well-sm" style="margin-bottom: 0; {{ $yeuCauDoiTra->trang_thai == 'cho_duyet' ? 'background: #f0ad4e; color: #fff;' : 'background: #5cb85c; color: #fff;' }}">2. &#272;&#227; duy&#7879;t</div></div>
                    <div class="col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 10px;"><div class="well well-sm" style="margin-bottom: 0; {{ in_array($yeuCauDoiTra->trang_thai, ['dang_xu_ly', 'dang_giao_hang_doi', 'hoan_tat']) ? 'background: #5cb85c; color: #fff;' : '' }}">3. &#272;&#227; nh&#7853;n h&#224;ng l&#7895;i</div></div>
                    <div class="col-md-3 col-sm-6 col-xs-6" style="margin-bottom: 10px;"><div class="well well-sm" style="margin-bottom: 0; {{ in_array($yeuCauDoiTra->trang_thai, ['dang_xu_ly', 'dang_giao_hang_doi', 'hoan_tat']) ? 'background: #5bc0de; color: #fff;' : '' }}">4. &#272;ang x&#7917; l&#253; &#273;&#7893;i tr&#7843;</div></div>
                    <div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 10px;"><div class="well well-sm" style="margin-bottom: 0; {{ $yeuCauDoiTra->trang_thai == 'dang_giao_hang_doi' ? 'background: #337ab7; color: #fff;' : ($yeuCauDoiTra->trang_thai == 'hoan_tat' ? 'background: #5cb85c; color: #fff;' : '') }}">5. &#272;ang giao h&#224;ng &#273;&#7893;i / Ho&#224;n t&#7845;t</div></div>
                </div>
                <h4>Sản phẩm đổi trả</h4>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sanPhamDoiTras as $sanPhamDoiTra)
                            <tr>
                                <td>{{ $sanPhamDoiTra['ten_san_pham'] }}</td>
                                <td>{{ $sanPhamDoiTra['so_luong'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h4>Minh chứng của khách</h4>
                @if (count($minhChungDoiTras) > 0)
                    <div class="row">
                        @foreach ($minhChungDoiTras as $minhChungDoiTra)
                            <div class="col-md-3">
                                <a href="{{ $minhChungDoiTra['duong_dan'] }}" target="_blank" class="btn btn-default btn-xs btn-block">
                                <i class="fa fa-external-link"></i> Xem minh chứng {{ $loop->iteration }}
                            </a>
                            <small class="text-muted">{{ $minhChungDoiTra['ten_tep'] }}</small>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-danger">Yêu cầu chưa có minh chứng.</p>
            @endif
        </div>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($donHang->chiTietDonHangs as $chiTietDonHang)
                <tr>
                    <td><img src="{{ $chiTietDonHang->hinh_anh_san_pham }}" width="50" height="50" alt="Sản phẩm"></td>
                    <td>{{ $chiTietDonHang->ten_san_pham }}</td>
                    <td>{{ number_format($chiTietDonHang->gia, 0, ',', '.') }} <small>đ</small></td>
                    <td>{{ $chiTietDonHang->so_luong }}</td>
                    <td>{{ number_format($chiTietDonHang->thanh_tien, 0, ',', '.') }} <small>đ</small></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-md-6">
        <p class="lead">Phương thức thanh toán</p>
        <p><span class="{{ $donHang->lop_trang_thai_thanh_toan }}">{{ $donHang->ten_phuong_thuc_thanh_toan }}</span></p>
        <p><span class="{{ $donHang->lop_trang_thai_thanh_toan }}">{{ $donHang->ten_trang_thai_thanh_toan }}</span></p>
    </div>
    <div class="col-md-6">
        <table class="table">
            <tbody>
                <tr>
                    <th>Tiền hàng:</th>
                    <td>{{ number_format($donHang->tam_tinh, 0, ',', '.') }} <small>đ</small></td>
                </tr>
                <tr>
                    <th>Phí vận chuyển:</th>
                    <td>{{ number_format($donHang->phi_van_chuyen, 0, ',', '.') }} <small>đ</small></td>
                </tr>
                @if ($donHang->so_tien_giam > 0)
                    <tr>
                        <th>Giảm giá:</th>
                        <td>-{{ number_format($donHang->so_tien_giam, 0, ',', '.') }} <small>đ</small></td>
                    </tr>
                @endif
                <tr>
                    <th>Tổng tiền:</th>
                    <td><strong>{{ number_format($donHang->tong_tien, 0, ',', '.') }} <small>đ</small></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row no-print">
    <div class="col-md-12">
        <button class="btn btn-default" onclick="window.print();">
            <i class="fa fa-print"></i> In hóa đơn
        </button>

        @if ($donHang->trang_thai == 'cho_xac_nhan')
            <button class="btn btn-primary pull-right nut-xac-nhan-don" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-check"></i> Xác nhận đơn hàng
            </button>
            <button class="btn btn-danger pull-right nut-mo-huy-don" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-times"></i> Hủy đơn hàng
            </button>
        @elseif ($donHang->trang_thai == 'da_xac_nhan')
            <button class="btn btn-info pull-right nut-giao-don" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-truck"></i> Giao hàng
            </button>
            <button class="btn btn-danger pull-right nut-mo-huy-don" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-times"></i> Hủy đơn hàng
            </button>
        @elseif ($donHang->trang_thai == 'dang_giao')
            <button class="btn btn-success pull-right nut-hoan-tat-don" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-flag"></i> Đánh dấu giao thành công
            </button>
            <button class="btn btn-danger pull-right nut-mo-giao-that-bai" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-times"></i> Giao thất bại
            </button>
        @elseif ($donHang->trang_thai == 'giao_that_bai')
            @if ($donHang->coTheGiaoLai())
                <button class="btn btn-primary pull-right nut-giao-lai" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                    <i class="fa fa-truck"></i> Giao lại
                </button>
            @endif
            @if ($donHang->coTheHoanVeCuaHang())
                <button class="btn btn-warning pull-right nut-hoan-ve" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                    <i class="fa fa-undo"></i> Hoàn về cửa hàng
                </button>
            @endif
        @elseif ($donHang->trang_thai == 'dang_hoan_hang')
            <button class="btn btn-warning pull-right nut-mo-nhan-hang-hoan" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-inbox"></i> Nhận hàng hoàn
            </button>
        @endif

        @php
        $coTheHoanTienPaypal = $donHang->thanhToan
        && $donHang->thanhToan->phuong_thuc == 'paypal'
        && $donHang->thanhToan->trang_thai == 'da_thanh_toan'
        && (
        $donHang->trang_thai == 'da_hoan_ve_kho'
        || ($donHang->trang_thai == 'da_huy' && $donHang->nguoi_huy == 'khach_hang')
        );
        @endphp

        @if ($coTheHoanTienPaypal)
            <button class="btn btn-warning pull-right nut-hoan-tien-paypal" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-money"></i> Đã hoàn tiền PayPal
            </button>
        @elseif ($donHang->trang_thai == 'da_hoan_ve_kho')
            <button class="btn btn-danger pull-right nut-ket-thuc-don-hoan" data-ma-don-hang="{{ $donHang->ma_don_hang }}">
                <i class="fa fa-times"></i> Kết thúc đơn hoàn
            </button>
        @endif
    </div>
</div>

@if ($donHang->ly_do_huy)
    <div class="alert alert-danger mt-3">
        <strong><i class="fa fa-info-circle"></i> Lý do hủy:</strong> {{ $donHang->ly_do_huy }}
    </div>
@endif

@if ($donHang->ly_do_giao_that_bai)
    <div class="alert alert-warning mt-3">
        <strong><i class="fa fa-info-circle"></i> Lý do giao thất bại:</strong> {{ $donHang->ly_do_giao_that_bai }}
    </div>
@endif
</section>
</div>
</div>
</div>

<div class="modal fade" id="modal-huy-don" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Hủy đơn hàng</h5></div>
            <div class="modal-body">
                <input type="hidden" id="ma-don-hang-huy">
                <textarea id="ly-do-huy-don" class="form-control" rows="3" placeholder="Nhập lý do hủy"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button class="btn btn-danger" id="nut-xac-nhan-huy-don">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-giao-that-bai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Giao hàng thất bại</h5></div>
            <div class="modal-body">
                <input type="hidden" id="ma-don-giao-that-bai">
                <textarea id="ly-do-giao-that-bai" class="form-control" rows="3" placeholder="Nhập lý do giao thất bại"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button class="btn btn-danger" id="nut-xac-nhan-giao-that-bai">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-nhan-hang-hoan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Nhận hàng hoàn</h5></div>
            <div class="modal-body">
                <input type="hidden" id="ma-don-nhan-hang-hoan">
                <label>Tình trạng hàng</label>
                <select id="tinh-trang-hang-hoan" class="form-control">
                    <option value="nguyen_ven">Nguyên vẹn, nhập lại kho</option>
                    <option value="hu_hong">Hư hỏng, không nhập lại kho</option>
                </select>
                <div id="khu-vuc-hang-hoan-hu" class="d-none mt-3">
                    <textarea id="ly-do-hang-hoan-hu" class="form-control mb-2" rows="3" placeholder="Mô tả tình trạng hư hỏng"></textarea>
                    <input type="file" id="minh-chung-hang-hoan" class="form-control" accept="image/*,video/*" multiple>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button class="btn btn-warning" id="nut-xac-nhan-nhan-hang-hoan">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
@endsection
