<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'min_order_amount',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where('end_date', '>=', $now)
            ->whereColumn('used_count', '<', 'usage_limit');
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }

    // Methods
    public function isValid(): bool
    {
        $now = Carbon::now();

        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check start date
        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }

        // Check end date
        if ($this->end_date->lt($now)) {
            return false;
        }

        // Check usage limit
        if ($this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canBeUsedBy(User $user): bool
    {
        return !$this->usages()->where('user_id', $user->id)->exists();
    }

    public function isApplicableToOrder(float $orderTotal): bool
    {
        // Check minimum order amount
        if ($this->min_order_amount && $orderTotal < $this->min_order_amount) {
            return false;
        }

        // For fixed discounts, order must be >= discount value
        if ($this->discount_type === 'fixed' && $orderTotal < $this->discount_value) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->discount_type === 'percentage') {
            return round(($orderTotal * $this->discount_value) / 100, 2);
        }

        return $this->discount_value;
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    // Mutators
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }
}
