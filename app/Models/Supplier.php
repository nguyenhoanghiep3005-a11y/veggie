<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'description',
    ];

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function importReceipts()
    {
        return $this->hasMany(ImportReceipt::class);
    }

    public function damageSlips()
    {
        return $this->hasMany(DamageSlip::class);
    }
}
