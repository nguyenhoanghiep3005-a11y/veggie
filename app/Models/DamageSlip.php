<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'purchase_order_id',
        'import_receipt_id',
        'supplier_id',
        'reason',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    // Lấy phiếu đặt mua liên quan.
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Lấy phiếu nhập hàng liên quan.
    public function importReceipt()
    {
        return $this->belongsTo(ImportReceipt::class);
    }

    // Lấy nhà cung cấp của hàng lỗi.
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Lấy danh sách sản phẩm lỗi.
    public function items()
    {
        return $this->hasMany(DamageSlipItem::class);
    }

    // Lấy danh sách hình ảnh minh chứng hàng lỗi.
    public function mediaFiles()
    {
        return $this->hasMany(DamageSlipMedia::class);
    }

    // Tạo mã phiếu hàng lỗi tự động.
    public static function generateCode()
    {
        return 'DS-'.str_pad(((int) self::max('id')) + 1, 4, '0', STR_PAD_LEFT);
    }

    // Lấy nguồn phát sinh phiếu hàng lỗi để hiển thị.
    public function sourceLabel()
    {
        if ($this->import_receipt_id) {
            return 'Phiếu nhập hàng';
        }

        if ($this->purchase_order_id) {
            return 'Phiếu đặt mua';
        }

        return 'Điều chỉnh kho';
    }

    // Tính tổng số lượng hàng lỗi.
    public function totalQuantity()
    {
        return (int) $this->items->sum('quantity');
    }

    // Gom tên sản phẩm lỗi để hiển thị trong danh sách.
    public function productSummary()
    {
        $names = [];

        foreach ($this->items as $item) {
            $name = 'Sản phẩm đã xóa';

            if ($item->product) {
                $name = $item->product->display_name;
            }

            $names[] = $name;
        }

        return implode(', ', $names);
    }
}