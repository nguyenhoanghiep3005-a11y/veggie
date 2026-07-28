<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'supplier_id',
        'status',
        'note',
        'ordered_at',
        'received_at',
        'defect_description',
        'supplier_reported_at',
    ];

    protected $casts = [
        'ordered_at' => 'date',
        'received_at' => 'datetime',
        'supplier_reported_at' => 'datetime',
    ];

    // Lấy nhà cung cấp của phiếu đặt mua.
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Lấy danh sách sản phẩm trong phiếu đặt mua.
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Lấy danh sách phiếu nhập hàng của phiếu đặt mua.
    public function importReceipts()
    {
        return $this->hasMany(ImportReceipt::class);
    }

    // Lấy danh sách phiếu ghi nhận hàng lỗi.
    public function damageSlips()
    {
        return $this->hasMany(DamageSlip::class);
    }

    // Tạo mã phiếu đặt mua tự động.
    public static function generateCode()
    {
        return 'PO-'.str_pad(((int) self::max('id')) + 1, 4, '0', STR_PAD_LEFT);
    }

    // Tính tổng số lượng đã đặt.
    public function orderedQuantity()
    {
        return (int) $this->items->sum('quantity_ordered');
    }

    // Tính tổng số lượng đã nhập kho.
    public function importedQuantity()
    {
        return (int) $this->items->sum('quantity_imported');
    }

    // Tính tổng số lượng hàng lỗi/bị từ chối.
    public function rejectedQuantity()
    {
        return (int) $this->items->sum('quantity_rejected');
    }

    // Lấy tên trạng thái để hiển thị ngoài giao diện.
    public function statusLabel()
    {
        if ($this->status == 'pending') {
            return 'Chờ nhập hàng';
        }

        if ($this->status == 'completed') {
            return 'Đã nhập hàng';
        }

        return $this->status;
    }

    // Lấy class màu cho trạng thái phiếu đặt mua.
    public function statusClass()
    {
        if ($this->status == 'pending') {
            return 'badge badge-warning';
        }

        if ($this->status == 'completed') {
            return 'badge badge-success';
        }

        return 'badge badge-secondary';
    }
}