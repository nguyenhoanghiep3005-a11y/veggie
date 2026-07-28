<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseDamageMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_damage_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'media_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function warehouseDamage()
    {
        return $this->belongsTo(WarehouseDamage::class);
    }
}
