@php
    $statusMap = [
        'pending' => ['class' => 'badge-warning', 'label' => 'Chờ xác nhận'],
        'confirmed' => ['class' => 'badge-primary', 'label' => 'Đã xác nhận'],
        'shipping' => ['class' => 'badge-info', 'label' => 'Đang giao'],
        'return_requested' => ['class' => 'badge-warning', 'label' => 'Chờ duyệt hàng lỗi'],
        'return_pickup' => ['class' => 'badge-info', 'label' => 'Chờ nhận hàng lỗi'],
        'replacement_shipping' => ['class' => 'badge-info', 'label' => 'Đang giao sản phẩm đổi'],
        'replacement_completed' => ['class' => 'badge-success', 'label' => 'Hoàn tất yêu cầu đổi'],
        'completed' => ['class' => 'badge-success', 'label' => 'Đã giao'],
        'canceled' => ['class' => 'badge-danger', 'label' => 'Đã hủy đơn hàng'],
    ];

    $info = ['class' => 'badge-secondary', 'label' => $status];
    if (isset($statusMap[$status])) {
        $info = $statusMap[$status];
    }

    if (isset($order)) {
        $info['label'] = $order->adminStatusLabel();
    }
@endphp
<span class="custom-badge badge {{ $info['class'] }}"
    @if($status === 'canceled' && isset($order) && $order->cancel_reason)
        title="Lý do: {{ $order->cancel_reason }}"
        data-toggle="tooltip"
    @endif
>{{ $info['label'] }}</span>