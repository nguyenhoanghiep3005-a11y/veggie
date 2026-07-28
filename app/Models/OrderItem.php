<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'stock_allocations'];

    protected $casts = [
        'stock_allocations' => 'array',
    ];

    // Lấy sản phẩm của dòng đơn hàng.
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Lấy đơn hàng chứa dòng sản phẩm này.
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
