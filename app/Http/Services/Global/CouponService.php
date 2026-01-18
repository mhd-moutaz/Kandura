<?php

namespace App\Http\Services\Global;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Models\CouponUsage;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * Validate and apply coupon to order
     */
    public function validateAndApplyCoupon(string $code, Order $order, User $user): array
    {
        $coupon = Coupon::byCode($code)->first();

        if (!$coupon) {
            throw new GeneralException('Coupon not found', 404);
        }

        // Validate coupon
        $this->validateCoupon($coupon, $order, $user);

        // Calculate discount
        $discount = $coupon->calculateDiscount($order->total);

        return [
            'coupon' => $coupon,
            'discount' => $discount,
            'total_after_discount' => max(0, $order->total - $discount),
        ];
    }

    /**
     * Validate coupon against all rules
     */
    private function validateCoupon(Coupon $coupon, Order $order, User $user): void
    {
        // Check if coupon is valid
        if (!$coupon->isValid()) {
            throw new GeneralException('This coupon is not valid or has expired', 400);
        }

        // Check if user can use this coupon
        if (!$coupon->canBeUsedBy($user)) {
            throw new GeneralException('You have already used this coupon', 400);
        }

        // Check if applicable to order amount
        if (!$coupon->isApplicableToOrder($order->total)) {
            if ($coupon->min_order_amount) {
                throw new GeneralException(
                    "Minimum order amount for this coupon is $" . number_format($coupon->min_order_amount, 2),
                    400
                );
            }

            if ($coupon->discount_type === 'fixed') {
                throw new GeneralException(
                    "Order amount must be at least $" . number_format($coupon->discount_value, 2) . " to use this coupon",
                    400
                );
            }
        }
    }

    /**
     * Apply coupon to order (update order with coupon details)
     */
    public function applyCouponToOrder(Coupon $coupon, Order $order, float $discount): Order
    {
        return DB::transaction(function () use ($coupon, $order, $discount) {
            $order->update([
                'coupon_id' => $coupon->id,
                'discount_amount' => $discount,
                'total_before_discount' => $order->total,
                'total' => max(0, $order->total - $discount),
            ]);

            return $order->fresh();
        });
    }

    /**
     * Record coupon usage after order confirmation
     */
    public function recordCouponUsage(Coupon $coupon, Order $order, User $user): void
    {
        DB::transaction(function () use ($coupon, $order, $user) {
            // Create usage record
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'discount_applied' => $order->discount_amount,
            ]);

            // Increment coupon usage count
            $coupon->incrementUsage();
        });
    }

    /**
     * Remove coupon from order
     */
    public function removeCouponFromOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            if ($order->coupon_id) {
                $order->update([
                    'total' => $order->total_before_discount ?? $order->total,
                    'coupon_id' => null,
                    'discount_amount' => 0,
                    'total_before_discount' => null,
                ]);
            }

            return $order->fresh();
        });
    }

    /**
     * Get coupon by code
     */
    public function getCouponByCode(string $code): ?Coupon
    {
        return Coupon::byCode($code)->first();
    }

    /**
     * Check if coupon can be applied to order
     */
    public function canApplyCoupon(string $code, Order $order, User $user): bool
    {
        try {
            $this->validateAndApplyCoupon($code, $order, $user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
