<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageSlipMedia extends Model
{
    use HasFactory;

    protected $table = 'damage_slip_media';

    protected $fillable = [
        'damage_slip_id',
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

    public function damageSlip()
    {
        return $this->belongsTo(DamageSlip::class);
    }
}
