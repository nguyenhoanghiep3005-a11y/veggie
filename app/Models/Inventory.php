<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    const NEAR_EXPIRY_DAYS = 60;

    protected $fillable = [
        'product_id',
        'quantity_imported',
        'quantity_remaining',
        'quantity_damaged',
        'damaged_item_numbers',
        'imported_at',
        'expired_at',
        'condition',
        'adjusted_price',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sellingPrice()
    {
        if ($this->adjusted_price !== null && $this->adjusted_price > 0) {
            return $this->adjusted_price;
        }

        if ($this->product) {
            return $this->product->price;
        }

        return 0;
    }

    public function lotCode()
    {
        return 'SP' . str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }

    public function itemCode($number)
    {
        return $this->lotCode() . '-' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }

    public function conditionLabel()
    {
        if ($this->condition == 'fresh') {
            return 'Tươi mới';
        }

        if ($this->condition == 'near_expiry') {
            return 'Cận hạn';
        }

        if ($this->condition == 'expired') {
            return 'Hết hạn';
        }

        if ($this->condition == 'damaged') {
            return 'Hư hỏng';
        }

        if ($this->condition == 'sold_out') {
            return 'Đã bán hết';
        }

        return $this->condition;
    }

    public function conditionClass()
    {
        if ($this->condition == 'fresh') {
            return 'badge badge-success';
        }

        if ($this->condition == 'near_expiry') {
            return 'badge badge-warning';
        }

        if ($this->condition == 'expired' || $this->condition == 'damaged') {
            return 'badge badge-danger';
        }

        return 'badge badge-secondary';
    }

    public function maxUnsoldQuantity()
    {
        $maxUnsoldQuantity = $this->quantity_imported - $this->soldQuantity();

        if ($maxUnsoldQuantity < 0) {
            return 0;
        }

        return $maxUnsoldQuantity;
    }

    public function isDamagedItem($number)
    {
        return in_array((int) $number, $this->damagedItemNumbers());
    }

    public function isSoldItem($number)
    {
        return in_array((int) $number, $this->soldItemNumbers());
    }

    public function damagedItemNumbers()
    {
        if (!$this->damaged_item_numbers) {
            $damagedItemNumbers = [];
            $soldQuantity = $this->soldQuantity();

            for ($i = $this->quantity_imported; $i >= 1; $i--) {
                if (count($damagedItemNumbers) >= $this->quantity_damaged) {
                    break;
                }

                if ($i > $soldQuantity) {
                    $damagedItemNumbers[] = $i;
                }
            }

            sort($damagedItemNumbers);

            return $damagedItemNumbers;
        }

        $numbers = explode(',', $this->damaged_item_numbers);
        $damagedItemNumbers = [];

        foreach ($numbers as $number) {
            $number = (int) trim($number);

            if ($number > 0 && $number <= $this->quantity_imported && !in_array($number, $damagedItemNumbers)) {
                $damagedItemNumbers[] = $number;
            }
        }

        sort($damagedItemNumbers);

        return $damagedItemNumbers;
    }

    public function soldItemNumbers()
    {
        $soldQuantity = $this->soldQuantity();
        $damagedItemNumbers = $this->damagedItemNumbers();
        $soldItemNumbers = [];

        for ($i = 1; $i <= $this->quantity_imported; $i++) {
            if (count($soldItemNumbers) >= $soldQuantity) {
                break;
            }

            if (!in_array($i, $damagedItemNumbers)) {
                $soldItemNumbers[] = $i;
            }
        }

        return $soldItemNumbers;
    }

    public function setDamagedItemNumbers($numbers)
    {
        $damagedItemNumbers = [];

        foreach ($numbers as $number) {
            $number = (int) $number;

            if ($number > 0 && $number <= $this->quantity_imported && !in_array($number, $damagedItemNumbers)) {
                $damagedItemNumbers[] = $number;
            }
        }

        sort($damagedItemNumbers);

        $this->damaged_item_numbers = count($damagedItemNumbers) > 0 ? implode(',', $damagedItemNumbers) : null;
        $this->quantity_damaged = count($damagedItemNumbers);
    }

    public function isAvailable()
    {
        return $this->quantity_remaining > 0
            && $this->expired_at >= now()->toDateString()
            && !in_array($this->condition, ['expired', 'damaged', 'sold_out']);
    }

    public function isNearExpiry($days = null)
    {
        if (!$this->expired_at) {
            return false;
        }

        if ($days === null) {
            $days = self::NEAR_EXPIRY_DAYS;
        }

        return $this->expired_at >= now()->toDateString()
            && $this->expired_at <= now()->addDays($days)->toDateString();
    }

    public static function checkCondition($condition, $quantityRemaining, $expiredAt)
    {
        if ($condition == 'damaged' && $quantityRemaining <= 0) {
            return 'damaged';
        }

        if ($quantityRemaining <= 0) {
            return 'sold_out';
        }

        if ($expiredAt < now()->toDateString()) {
            return 'expired';
        }

        if ($expiredAt <= now()->addDays(self::NEAR_EXPIRY_DAYS)->toDateString()) {
            return 'near_expiry';
        }

        return 'fresh';
    }

    public function refreshCondition()
    {
        $this->condition = self::checkCondition(
            $this->condition,
            $this->quantity_remaining,
            $this->expired_at
        );
    }

    public function soldQuantity()
    {
        $soldQuantity = $this->quantity_imported - $this->quantity_remaining - $this->quantity_damaged;

        if ($soldQuantity < 0) {
            return 0;
        }

        return $soldQuantity;
    }

    public function unsoldQuantity()
    {
        return $this->quantity_remaining + $this->quantity_damaged;
    }
}
