<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageSlipItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'damage_slip_id',
        'product_id',
        'quantity',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function damageSlip()
    {
        return $this->belongsTo(DamageSlip::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
