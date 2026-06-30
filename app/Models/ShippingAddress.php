<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address',
        'city',
        'default',
        'province_id',
        'district_id',
        'ward_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasGhnLocation()
    {
        return !empty($this->province_id) && !empty($this->district_id) && !empty($this->ward_id);
    }
}
