<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_receipt_id',
        'purchase_order_item_id',
        'product_id',
        'quantity',
        'manufactured_at',
        'expired_at',
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'manufactured_at' => 'date',
        'expired_at'     => 'date',
    ];

    public function receipt()
    {
        return $this->belongsTo(ImportReceipt::class, 'import_receipt_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouseStock()
    {
        return $this->hasOne(WarehouseStock::class);
    }
}
