@extends('layouts.admin')

@section('title', 'Nhập kho')

@section('content')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left"><h3>Nhập kho</h3></div>
        <div class="title_right">
            <a href="{{ route('admin.don-dat-nhap.danh-sach') }}" class="btn btn-default pull-right">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="clearfix"></div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $loi)
                <div>{{ $loi }}</div>
            @endforeach
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="x_panel">
        <div class="x_title">
            <h2>Nhập từ đơn: <strong>{{ $donDatNhap->so_don }}</strong></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-md-6"><p><strong>Nhà cung cấp:</strong> {{ $donDatNhap->ten_nha_cung_cap_hien_thi }}</p></div>
                <div class="col-md-6"><p><strong>Ngày đặt:</strong> {{ $donDatNhap->ngay_dat_hien_thi }}</p></div>
            </div>

            <form action="{{ route('admin.don-dat-nhap.xu-ly-nhap-kho', $donDatNhap->ma_don_dat_nhap) }}" method="POST" enctype="multipart/form-data" id="purchase-import-form">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th><th>Đã đặt</th><th>Đã nhận</th><th>Từ chối</th>
                                <th>Nhập kho</th><th>Ngày sản xuất</th><th>Hạn sử dụng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donDatNhap->chiTietDonDatNhaps as $viTri => $chiTiet)
                                <tr>
                                    <td class="text-left">
                                        <input type="hidden" name="chi_tiets[{{ $viTri }}][ma_chi_tiet_don_dat_nhap]" value="{{ $chiTiet->ma_chi_tiet_don_dat_nhap }}">
                                        <strong>{{ $chiTiet->ten_san_pham_hien_thi }}</strong>
                                    </td>
                                    <td><input type="number" class="form-control text-center js-ordered" value="{{ $chiTiet->so_luong_dat }}" readonly></td>
                                    <td><input type="number" name="chi_tiets[{{ $viTri }}][so_luong_nhan]" class="form-control text-center js-received" min="0" max="{{ $chiTiet->so_luong_dat }}" value="{{ $chiTiet->so_luong_nhan_mac_dinh }}" required></td>
                                    <td><input type="number" name="chi_tiets[{{ $viTri }}][so_luong_tu_choi]" class="form-control text-center js-rejected" min="0" max="{{ $chiTiet->so_luong_dat }}" value="{{ $chiTiet->so_luong_tu_choi_mac_dinh }}" required></td>
                                    <td><input type="number" class="form-control text-center js-accepted" value="{{ $chiTiet->so_luong_nhap_mac_dinh }}" readonly></td>
                                    <td><input type="date" name="chi_tiets[{{ $viTri }}][ngay_san_xuat]" class="form-control js-manufactured" value="{{ $chiTiet->ngay_san_xuat_mac_dinh }}"></td>
                                    <td><input type="date" name="chi_tiets[{{ $viTri }}][han_su_dung]" class="form-control js-expired" value="{{ $chiTiet->han_su_dung_mac_dinh }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group">
                    <label>Mô tả hàng lỗi <span id="defect-required-mark" class="text-danger d-none">*</span></label>
                    <textarea id="defect-description" name="mo_ta_hang_loi" class="form-control" rows="3">{{ old('mo_ta_hang_loi') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Ảnh hoặc video minh chứng <span id="defect-evidence-required-mark" class="text-danger d-none">*</span></label>
                    <input id="defect-evidence" type="file" name="minh_chungs[]" class="form-control" accept="image/*,video/*" multiple>
                </div>

                <div class="text-right">
                    <a href="{{ route('admin.don-dat-nhap.danh-sach') }}" class="btn btn-default">Hủy</a>
                    <button type="submit" id="purchase-import-submit" class="btn btn-success">Xác nhận nhập kho</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection