<!doctype html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <title>Hóa đơn đơn hàng #{{ $donHang->ma_don_hang }}</title>
    </head>
    <body style="margin:0;background:#f4f6f8;font-family:Arial,sans-serif;color:#333;">
        <div style="max-width:720px;margin:24px auto;background:#fff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
            <div style="padding:22px 28px;background:#2a9d8f;color:#fff;">
                <h1 style="margin:0 0 6px;font-size:24px;">Hóa đơn Veggie</h1>
                <div>Mã đơn hàng: <strong>#{{ $donHang->ma_don_hang }}</strong></div>
                <div>Ngày đặt: {{ $ngayDatHang }}</div>
            </div>

            <div style="padding:24px 28px;">
                <p>Xin chào <strong>{{ $tenKhachHang }}</strong>,</p>
                <p>Cảm ơn bạn đã mua hàng. Dưới đây là thông tin đơn hàng của bạn:</p>

                <div style="padding:14px 16px;background:#f8fafc;border-radius:6px;margin:18px 0;">
                    <strong>Thông tin nhận hàng</strong><br>
                        {{ $tenNguoiNhan }}<br>
                        {{ $soDienThoaiNguoiNhan }}<br>
                        {{ $diaChiNguoiNhan }}{{ $tinhThanhNguoiNhan }}
                    </div>

                    <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
                        <thead>
                            <tr style="background:#f1f5f9;text-align:left;">
                                <th style="border-bottom:1px solid #ddd;">Sản phẩm</th>
                                <th style="border-bottom:1px solid #ddd;text-align:center;">SL</th>
                                <th style="border-bottom:1px solid #ddd;text-align:right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($donHang->chiTietDonHangs as $chiTietDonHang)
                                <tr>
                                    <td style="border-bottom:1px solid #eee;">{{ $chiTietDonHang->ten_san_pham }}</td>
                                    <td style="border-bottom:1px solid #eee;text-align:center;">{{ $chiTietDonHang->so_luong }}</td>
                                    <td style="border-bottom:1px solid #eee;text-align:right;">
                                        {{ number_format($chiTietDonHang->thanh_tien, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table width="100%" cellpadding="5" cellspacing="0" style="margin-top:18px;border-collapse:collapse;">
                        <tr>
                            <td>Tiền hàng</td>
                            <td style="text-align:right;">{{ number_format($tamTinh, 0, ',', '.') }} đ</td>
                        </tr>
                        @if ((float) $donHang->so_tien_giam > 0)
                            <tr>
                                <td>
                                    Giảm giá
                                    @if ($donHang->ma_giam_gia)
                                        ({{ $donHang->ma_giam_gia }})
                                    @endif
                                </td>
                                <td style="text-align:right;color:#d9534f;">
                                    -{{ number_format($donHang->so_tien_giam, 0, ',', '.') }} đ
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>Phí vận chuyển</td>
                            <td style="text-align:right;">{{ number_format($phiVanChuyen, 0, ',', '.') }} đ</td>
                        </tr>
                        <tr style="font-size:18px;font-weight:bold;">
                            <td style="padding-top:10px;">Tổng thanh toán</td>
                            <td style="padding-top:10px;text-align:right;color:#2a9d8f;">
                                {{ number_format($donHang->tong_tien, 0, ',', '.') }} đ
                            </td>
                        </tr>
                    </table>

                    <p style="margin-top:26px;">Trân trọng,<br><strong>Đội ngũ HoangHiep</strong></p>
                </div>
            </div>
        </body>
    </html>
