<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hóa đơn đơn hàng #{{ $order->id }}</title>
</head>
<body style="margin:0;background:#f4f6f8;font-family:Arial,sans-serif;color:#333;">
    @php
        $shippingAddress = $order->shippingAddress;
        $subtotal = (float) $order->subtotal;

        if ($subtotal <= 0) {
            foreach ($order->orderItems as $item) {
                $subtotal += $item->quantity * $item->price;
            }
        }

        $shippingFee = (float) $order->shipping_fee;
        if ($shippingFee <= 0) {
            $shippingFee = max(0, (float) $order->total_price - $subtotal + (float) $order->discount_amount);
        }

        $createdAt = '';
        if ($order->created_at) {
            $createdAt = $order->created_at->format('d/m/Y H:i');
        }

        $customerName = 'quý khách';
        if ($order->user) {
            $customerName = $order->user->name;
        } elseif ($shippingAddress && $shippingAddress->full_name) {
            $customerName = $shippingAddress->full_name;
        }

        $shippingName = '—';
        $shippingPhone = '—';
        $shippingStreet = '—';
        $shippingCity = '';

        if ($shippingAddress) {
            if ($shippingAddress->full_name) {
                $shippingName = $shippingAddress->full_name;
            }

            if ($shippingAddress->phone) {
                $shippingPhone = $shippingAddress->phone;
            }

            if ($shippingAddress->address) {
                $shippingStreet = $shippingAddress->address;
            }

            if ($shippingAddress->city) {
                $shippingCity = ', '.$shippingAddress->city;
            }
        }
    @endphp
    <div style="max-width:720px;margin:24px auto;background:#fff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
        <div style="padding:22px 28px;background:#2a9d8f;color:#fff;">
            <h1 style="margin:0 0 6px;font-size:24px;">Hóa đơn Veggie</h1>
            <div>Mã đơn hàng: <strong>#{{ $order->id }}</strong></div>
            <div>Ngày đặt: {{ $createdAt }}</div>
        </div>

        <div style="padding:24px 28px;">
            <p>Xin chào <strong>{{ $customerName }}</strong>,</p>
            <p>Cảm ơn bạn đã mua hàng. Dưới đây là thông tin đơn hàng của bạn:</p>

            <div style="padding:14px 16px;background:#f8fafc;border-radius:6px;margin:18px 0;">
                <strong>Thông tin nhận hàng</strong><br>
                {{ $shippingName }}<br>
                {{ $shippingPhone }}<br>
                {{ $shippingStreet }}{{ $shippingCity }}
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
                    @foreach($order->orderItems as $item)
                        @php
                            $productName = 'Sản phẩm';
                            if ($item->product) {
                                $productName = $item->product->display_name;
                            }
                        @endphp
                        <tr>
                            <td style="border-bottom:1px solid #eee;">{{ $productName }}</td>
                            <td style="border-bottom:1px solid #eee;text-align:center;">{{ $item->quantity }}</td>
                            <td style="border-bottom:1px solid #eee;text-align:right;">{{ number_format($item->quantity * $item->price, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table width="100%" cellpadding="5" cellspacing="0" style="margin-top:18px;border-collapse:collapse;">
                <tr><td>Tiền hàng</td><td style="text-align:right;">{{ number_format($subtotal, 0, ',', '.') }} đ</td></tr>
                @if((float) $order->discount_amount > 0)
                    <tr><td>Giảm giá @if($order->coupon_code) ({{ $order->coupon_code }}) @endif</td><td style="text-align:right;color:#d9534f;">-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</td></tr>
                @endif
                <tr><td>Phí vận chuyển</td><td style="text-align:right;">{{ number_format($shippingFee, 0, ',', '.') }} đ</td></tr>
                <tr style="font-size:18px;font-weight:bold;"><td style="padding-top:10px;">Tổng thanh toán</td><td style="padding-top:10px;text-align:right;color:#2a9d8f;">{{ number_format($order->total_price, 0, ',', '.') }} đ</td></tr>
            </table>

            <p style="margin-top:26px;">Trân trọng,<br><strong>Đội ngũ Veggie</strong></p>
        </div>
    </div>
</body>
</html>