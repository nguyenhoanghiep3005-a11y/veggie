<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseDamage extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_stock_id',
        'product_id',
        'product_name',
        'quantity',
        'reason',
        'occurred_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'occurred_at' => 'datetime',
    ];

    public function warehouseStock()
    {
        return $this->belongsTo(WarehouseStock::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function mediaFiles()
    {
        return $this->hasMany(WarehouseDamageMedia::class);
    }
}
