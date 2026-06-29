<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'description',
        'price',
        'status',
        'unit',
        'average_rating',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function availableInventories()
    {
        return $this->hasMany(Inventory::class)
            ->where('quantity_remaining', '>', 0)
            ->whereDate('expired_at', '>=', now()->toDateString())
            ->whereNotIn('condition', ['expired', 'damaged', 'sold_out'])
            ->orderBy('expired_at')
            ->orderBy('id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function cartItem()
    {
        return $this->hasMany(CartItem::class);
    }

    public function firstImage()
    {
        return $this->hasOne(ProductImage::class)->orderBy('id', 'ASC');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getStockAttribute()
    {
        if ($this->relationLoaded('inventories')) {
            $totalQuantity = 0;

            foreach ($this->inventories as $inventory) {
                if ($inventory->isAvailable()) {
                    $totalQuantity += $inventory->quantity_remaining;
                }
            }

            return $totalQuantity;
        }

        return $this->availableInventories()->sum('quantity_remaining');
    }

    public function getCurrentPriceAttribute()
    {
        $inventory = $this->availableInventories()->first();

        if ($inventory) {
            return $inventory->sellingPrice();
        }

        return $this->price;
    }

    public function calculatePriceByQuantity($quantity)
    {
        $quantityNeed = (int)$quantity;
        $totalPrice = 0;

        if ($quantityNeed <= 0) {
            return 0;
        }

        $inventories = $this->availableInventories()->get();

        foreach ($inventories as $inventory) {
            if ($quantityNeed <= 0) {
                break;
            }

            $quantitySell = $quantityNeed;
            if ($inventory->quantity_remaining < $quantitySell) {
                $quantitySell = $inventory->quantity_remaining;
            }

            $totalPrice += $inventory->sellingPrice() * $quantitySell;
            $quantityNeed -= $quantitySell;
        }

        return $totalPrice;
    }

    public function getImageUrlAttribute()
    {
        if ($this->firstImage && $this->firstImage->image) {
            return asset('storage/' . $this->firstImage->image);
        }

        return asset('storage/uploads/products/default.png');
    }
}
