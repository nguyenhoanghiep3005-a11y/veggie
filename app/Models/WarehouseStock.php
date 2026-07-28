<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_receipt_item_id',
        'import_receipt_id',
        'product_id',
        'supplier_id',
        'quantity',
        'quantity_remaining',
        'sale_price',
        'manufactured_at',
        'expired_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_remaining' => 'integer',
        'sale_price' => 'decimal:2',
        'manufactured_at' => 'date',
        'expired_at' => 'date',
    ];

    // Lấy dòng chi tiết phiếu nhập tạo ra tồn kho này.
    public function importReceiptItem()
    {
        return $this->belongsTo(ImportReceiptItem::class);
    }

    // Lấy phiếu nhập liên quan.
    public function receipt()
    {
        return $this->belongsTo(ImportReceipt::class, 'import_receipt_id');
    }

    // Lấy sản phẩm trong tồn kho.
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Lấy nhà cung cấp của lô hàng.
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Kiểm tra lô hàng đã hết hạn chưa.
    public function isExpired()
    {
        if (! $this->expired_at) {
            return false;
        }

        return $this->expired_at->lt(today());
    }

    // Kiểm tra lô hàng có gần hết hạn không.
    public function isNearExpiry()
    {
        if ($this->quantity_remaining <= 0) {
            return false;
        }

        if (! $this->expired_at) {
            return false;
        }

        if (! $this->expired_at->greaterThanOrEqualTo(today())) {
            return false;
        }

        return today()->diffInDays($this->expired_at) <= Product::NEAR_EXPIRY_DAYS;
    }

    // Kiểm tra lô hàng còn bán bình thường không.
    public function isFresh()
    {
        if ($this->quantity_remaining <= 0) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        if ($this->isNearExpiry()) {
            return false;
        }

        return true;
    }

    // Kiểm tra lô hàng có giá khuyến mãi không.
    public function hasPromotion()
    {
        $price = 0;
        if ($this->product) {
            $price = (float) $this->product->price;
        }

        $salePrice = 0;
        if ($this->sale_price) {
            $salePrice = (float) $this->sale_price;
        }

        return $salePrice > 0 && $price > 0 && $salePrice < $price;
    }

    // Lấy chữ trạng thái hạn sử dụng.
    public function expiryLabel()
    {
        if (! $this->expired_at) {
            return 'Chưa có HSD';
        }

        if ($this->isExpired()) {
            return 'Hết hạn';
        }

        if ($this->isNearExpiry()) {
            return 'Cận hạn';
        }

        return 'Tươi mới';
    }

    // Lấy class màu cho trạng thái hạn sử dụng.
    public function expiryBadgeClass()
    {
        if (! $this->expired_at) {
            return 'label-default';
        }

        if ($this->isExpired()) {
            return 'label-danger';
        }

        if ($this->isNearExpiry()) {
            return 'label-warning';
        }

        return 'label-success';
    }
}