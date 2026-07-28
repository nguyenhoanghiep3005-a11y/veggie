<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'purchase_order_id',
        'supplier_id',
        'received_at',
        'note',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    // Lấy phiếu đặt mua liên quan.
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Lấy nhà cung cấp của phiếu nhập.
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Lấy danh sách sản phẩm đã nhập.
    public function items()
    {
        return $this->hasMany(ImportReceiptItem::class);
    }

    // Lấy danh sách tồn kho được tạo từ phiếu nhập.
    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }

    // Tạo mã phiếu nhập tự động.
    public static function generateCode()
    {
        return 'IR-'.str_pad(((int) self::max('id')) + 1, 4, '0', STR_PAD_LEFT);
    }

    // Tính tổng số lượng sản phẩm trong phiếu nhập.
    public function totalQuantity()
    {
        return (int) $this->items->sum('quantity');
    }
}