<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    use HasFactory;

    public const APPLY_ALL = 'all';
    public const APPLY_CUSTOMER = 'customer';

    protected $fillable = [
        'code',
        'discount_percent',
        'minimum_order_amount',
        'max_discount_amount',
        'expires_at',
        'usage_limit',
        'used_count',
        'apply_type',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
    ];

    // Lấy danh sách đơn hàng đã dùng mã giảm giá này.
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Lấy danh sách tài khoản được nhận voucher này.
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')
            ->withPivot(['claimed_at', 'used_at'])
            ->withTimestamps();
    }

    // Kiểm tra voucher có dùng được cho tài khoản và tổng tiền hiện tại không.
    public function canUse($userId, $subtotal = 0)
    {
        $error = $this->validateForUser($userId, $subtotal);

        if ($error) {
            return false;
        }

        return true;
    }

    // Trả về lỗi nếu voucher không dùng được, hợp lệ thì trả về null.
    public function validateForUser($userId, $subtotal = 0)
    {
        if (! $this->isUsable()) {
            return 'Mã giảm giá không còn hiệu lực hoặc đã hết lượt sử dụng.';
        }

        if ($this->apply_type != self::APPLY_ALL) {
            if (! $userId) {
                return 'Mã giảm giá này chỉ áp dụng cho khách hàng được chỉ định.';
            }

            $isAssigned = DB::table('coupon_user')
                ->where('coupon_id', $this->id)
                ->where('user_id', $userId)
                ->exists();

            if (! $isAssigned) {
                return 'Mã giảm giá này chỉ áp dụng cho khách hàng được chỉ định.';
            }
        }

        if ($userId) {
            $hasUsed = DB::table('coupon_user')
                ->where('coupon_id', $this->id)
                ->where('user_id', $userId)
                ->whereNotNull('used_at')
                ->exists();

            if ($hasUsed) {
                return 'Bạn đã sử dụng mã giảm giá này.';
            }
        }

        if ($subtotal > 0 && $subtotal < $this->minimum_order_amount) {
            return 'Đơn hàng cần tối thiểu '.number_format($this->minimum_order_amount, 0, ',', '.').' đ để dùng mã này.';
        }

        return null;
    }

    // Tính số tiền được giảm theo phần trăm và mức giảm tối đa.
    public function discountAmount($subtotal)
    {
        if ($subtotal < $this->minimum_order_amount) {
            return 0;
        }

        $discount = round($subtotal * ($this->discount_percent / 100), 2);

        if ($this->max_discount_amount != null && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        if ($discount > $subtotal) {
            return $subtotal;
        }

        return $discount;
    }

    // Kiểm tra voucher còn bật, chưa hết hạn và chưa hết lượt dùng.
    public function isUsable()
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit != null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    // Lọc các voucher còn hiệu lực để dùng trong checkout.
    public function scopeUsable($query)
    {
        return $query
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }
}
