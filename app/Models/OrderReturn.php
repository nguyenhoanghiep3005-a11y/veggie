<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    public const TYPE_DAMAGED = 'damaged';

    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'reason',
        'description',
        'items',
        'media',
        'requested_at',
        'approved_at',
        'received_at',
        'completed_at',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'items' => 'array',
        'media' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    // Lấy đơn hàng có yêu cầu đổi/trả.
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Hiển thị loại yêu cầu đổi/trả.
    public function typeLabel()
    {
        if ($this->type == self::TYPE_DAMAGED) {
            return 'Đổi/trả do hàng hư hỏng, lỗi';
        }

        return '—';
    }

    // Hiển thị trạng thái xử lý yêu cầu đổi/trả.
    public function statusLabel()
    {
        $status = null;
        if ($this->order) {
            $status = $this->order->status;
        }

        if ($status == 'return_requested') {
            return 'Ch? duy?t';
        }

        if ($status == 'return_pickup') {
            return 'Chờ nhận hàng lỗi';
        }

        if ($status == 'replacement_shipping') {
            return 'Đang giao sản phẩm đổi';
        }

        if ($status == 'replacement_completed') {
            return 'Hoàn tất yêu cầu đổi';
        }

        return '—';
    }

    // Class badge trạng thái đổi/trả.
    public function statusClass()
    {
        $status = null;
        if ($this->order) {
            $status = $this->order->status;
        }

        if ($status == 'return_requested') {
            return 'custom-badge badge badge-warning';
        }

        if ($status == 'return_pickup' || $status == 'replacement_shipping') {
            return 'custom-badge badge badge-info';
        }

        if ($status == 'replacement_completed') {
            return 'custom-badge badge badge-success';
        }

        return 'custom-badge badge badge-secondary';
    }

    // Tính tổng số lượng sản phẩm khách yêu cầu đổi/trả.
    public function totalQuantity()
    {
        $total = 0;
        $items = $this->items;

        if (! is_array($items)) {
            $items = [];
        }

        foreach ($items as $item) {
            if (isset($item['quantity'])) {
                $total += (int) $item['quantity'];
            }
        }

        return $total;
    }
}
