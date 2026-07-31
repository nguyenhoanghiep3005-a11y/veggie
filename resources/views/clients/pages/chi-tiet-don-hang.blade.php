@extends('layouts.client')

@section('title', 'Chi tiết đơn hàng')
@section('breadcrumb', 'Chi tiết đơn hàng')

@section('content')
<div class="liton__shoping-cart-area mb-120">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h3 class="mb-1">Chi tiết đơn hàng #{{ $donHang->ma_don_hang }}</h3>
                <div class="text-muted">Ngày đặt: {{ $donHang->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <span class="badge {{ $donHang->lopTrangThaiKhachHang() }}">
                {{ $donHang->tenTrangThai() }}
            </span>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif

        @if (in_array($donHang->trang_thai, ['giao_that_bai', 'dang_hoan_hang', 'da_hoan_ve_kho']))
            <div class="alert alert-warning">
                <strong>{{ $donHang->tenTrangThai() }}</strong>
                @if ($donHang->ly_do_giao_that_bai)
                    <div>Lý do: {{ $donHang->ly_do_giao_that_bai }}</div>
                @endif
                @if ($donHang->trang_thai == 'da_hoan_ve_kho')
                    <div>
                        Tình trạng hàng hoàn:
                        {{ $donHang->tinh_trang_hang_hoan == 'nguyen_ven' ? 'Nguyên vẹn' : 'Hư hỏng' }}
                    </div>
                @endif
            </div>
        @endif

        @if ($yeuCauDoiTra)
            <div class="card border-warning mb-4">
                <div class="card-body">
                    <h4>{{ $yeuCauDoiTra->tenLoai() }}</h4>
                    <p class="text-muted">Gửi lúc {{ $thoiGianYeuCau }}</p>
                    <p><strong>Trạng thái:</strong> {{ $yeuCauDoiTra->tenTrangThai() }}</p>
                    <p><strong>Nội dung:</strong> {{ $yeuCauDoiTra->mo_ta }}</p>

                    <h5>Sản phẩm đã gửi yêu cầu xử lý</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đã mua</th>
                                    <th>Đổi trả</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sanPhamDoiTras as $sanPhamDoiTra)
                                    <tr>
                                        <td>{{ $sanPhamDoiTra['ten_san_pham'] }}</td>
                                        <td>{{ $sanPhamDoiTra['so_luong_da_mua'] }}</td>
                                        <td>{{ $sanPhamDoiTra['so_luong_doi_tra'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if (count($minhChungDoiTras) > 0)
                        <h5>Minh chứng đã gửi</h5>
                        <div class="row">
                            @foreach ($minhChungDoiTras as $minhChungDoiTra)
                                <div class="col-md-3 mb-2">
                                    <a href="{{ $minhChungDoiTra['duong_dan'] }}" target="_blank"
                                        class="btn btn-outline-secondary btn-sm d-inline-block">
                                        Xem minh chứng {{ $loop->iteration }}
                                    </a>
                                    <small class="text-muted d-block text-truncate mt-1" title="{{ $minhChungDoiTra['ten_tep'] }}">{{ $minhChungDoiTra['ten_tep'] }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="row text-center mt-3">
                        <div class="col-md-3 mb-2"><div class="p-2 bg-success text-white">1. Đã gửi yêu cầu</div></div>
                        <div class="col-md-3 mb-2"><div class="p-2 {{ $yeuCauDoiTra->trang_thai == 'cho_duyet' ? 'bg-warning' : 'bg-success text-white' }}">2. Duyệt yêu cầu</div></div>
                        <div class="col-md-3 mb-2"><div class="p-2 {{ $daNhanHangDoiTra ? 'bg-success text-white' : 'bg-light' }}">3. Nhận hàng lỗi</div></div>
                        <div class="col-md-3 mb-2"><div class="p-2 {{ $daHoanTatDoiTra ? 'bg-success text-white' : 'bg-light' }}">4. Hoàn tất đổi trả</div></div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h4>Sản phẩm trong đơn hàng</h4>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($donHang->chiTietDonHangs as $chiTietDonHang)
                                <tr>
                                    <td><img src="{{ $chiTietDonHang->hinh_anh_san_pham }}" width="60" height="60" alt="Sản phẩm"></td>
                                    <td>{{ $chiTietDonHang->ten_san_pham }}</td>
                                    <td>{{ number_format($chiTietDonHang->gia, 0, ',', '.') }}<small>đ</small></td>
                                    <td>{{ $chiTietDonHang->so_luong }}</td>
                                    <td>{{ number_format($chiTietDonHang->thanh_tien, 0, ',', '.') }}<small>đ</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <h5>Thông tin giao hàng</h5>
                        <p class="mb-1">{{ $thongTinGiaoHang['ten'] }} - {{ $thongTinGiaoHang['so_dien_thoai'] }}</p>
                        <p>{{ $thongTinGiaoHang['dia_chi'] }}, {{ $thongTinGiaoHang['tinh_thanh'] }}</p>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between"><span>Tiền hàng</span><strong>{{ number_format($donHang->tam_tinh, 0, ',', '.') }}<small>đ</small></strong></div>
                        <div class="d-flex justify-content-between"><span>Phí vận chuyển</span><strong>{{ number_format($donHang->phi_van_chuyen, 0, ',', '.') }}<small>đ</small></strong></div>
                        @if ($donHang->so_tien_giam > 0)
                            <div class="d-flex justify-content-between"><span>Giảm giá</span><strong>-{{ number_format($donHang->so_tien_giam, 0, ',', '.') }}<small>đ</small></strong></div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>{{ $donHang->tenTongTien() }}</span>
                            <strong class="text-danger">{{ number_format($donHang->tong_tien, 0, ',', '.') }}<small>đ</small></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($donHang->trang_thai == 'cho_xac_nhan')
            <div class="card border-danger mb-4">
                <div class="card-body">
                    <h5 class="text-danger">Hủy đơn hàng</h5>
                    <form action="{{ route('don-hang.huy', $donHang->ma_don_hang) }}" method="POST">
                        @csrf
                        <textarea name="ly_do_huy" class="form-control mb-3" rows="3"
                            placeholder="Nhập lý do hủy đơn" required>{{ old('ly_do_huy') }}</textarea>
                        <button type="submit" class="btn btn-danger btn-sm">Hủy đơn hàng</button>
                    </form>
                </div>
            </div>
        @endif

        @if ($donHang->trang_thai == 'da_huy' && $donHang->ly_do_huy)
            <div class="alert alert-danger mb-4">
                <strong>Thông tin hủy đơn</strong><br>
                Người hủy: {{ $donHang->nguoi_huy == 'quan_tri' ? 'Quản trị viên' : 'Khách hàng' }}<br>
                Lý do: {{ $donHang->ly_do_huy }}
            </div>
        @endif

        @if ($donHang->trang_thai == 'hoan_thanh' && ! $yeuCauDoiTra && $coTheYeuCauDoiTra)
            <button type="button" id="show-return-request-form" class="btn btn-warning mt-4">
                Gửi yêu cầu đổi trả
            </button>

            <div id="return-request-form"
                class="card border-warning mt-4 return-request-panel {{ $errors->any() ? 'is-open' : '' }}">
                <div class="card-body">
                    <h4>Yêu cầu xử lý hàng lỗi hoặc hư hỏng</h4>
                    <p>Hạn gửi yêu cầu: {{ $donHang->tenHanDoiTra() }}</p>

                    <form action="{{ route('don-hang.gui-yeu-cau-doi-tra', $donHang->ma_don_hang) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đã mua</th>
                                        <th>Số lượng đổi trả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($donHang->chiTietDonHangs as $chiTietDonHang)
                                        <tr>
                                            <td>{{ $chiTietDonHang->ten_san_pham }}</td>
                                            <td>{{ $chiTietDonHang->so_luong }}</td>
                                            <td>
                                                <input type="number"
                                                    name="san_pham[{{ $chiTietDonHang->ma_chi_tiet_don_hang }}][so_luong]"
                                                    class="form-control"
                                                    min="0"
                                                    max="{{ $chiTietDonHang->so_luong }}"
                                                    value="{{ old('san_pham.'.$chiTietDonHang->ma_chi_tiet_don_hang.'.so_luong', 0) }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <textarea name="mo_ta" class="form-control mb-3" rows="3"
                            placeholder="Mô tả tình trạng hàng lỗi hoặc hư hỏng" required>{{ old('mo_ta') }}</textarea>

                        <input type="file" name="minh_chung[]" class="form-control mb-2"
                            accept="image/*,video/*" multiple required>
                        <small class="text-muted">Cần ít nhất một ảnh hoặc video rõ tình trạng sản phẩm.</small>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning">Gửi yêu cầu</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif ($donHang->trang_thai == 'hoan_thanh' && ! $yeuCauDoiTra)
            <div class="alert alert-info mt-4">
                Đơn hàng chỉ được gửi yêu cầu đổi trả trong vòng 3 ngày sau khi nhận.
            </div>
        @endif

        @if (
            ($donHang->trang_thai == 'hoan_thanh' && ! $yeuCauDoiTra)
            
        )
            <h4 class="mt-4">Đánh giá sản phẩm</h4>
            <div class="order-review-actions">
                @foreach ($donHang->chiTietDonHangs as $chiTietDonHang)
                    @if ($chiTietDonHang->sanPham)
                        <a href="{{ route('san-pham.chi-tiet', $chiTietDonHang->sanPham->duong_dan) }}"
                            class="btn theme-btn-1 btn-effect-1 order-review-button">
                            Đánh giá {{ $chiTietDonHang->ten_san_pham }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
