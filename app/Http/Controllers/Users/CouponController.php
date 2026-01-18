<?php

namespace App\Http\Controllers\Users;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\OrderService;
use App\Http\Requests\Users\ApplyCouponRequest;
use App\Http\Resources\Users\OrderResource;
use Illuminate\Support\Facades\Gate;

class CouponController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Apply coupon to order
     */
    public function apply(ApplyCouponRequest $request, Order $order)
    {
        Gate::authorize('update', $order);

        try {
            $order = $this->orderService->applyCoupon($order, $request->coupon_code);

            return $this->success(
                new OrderResource($order),
                'Coupon applied successfully! You saved $' . number_format($order->discount_amount, 2)
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * Remove coupon from order
     */
    public function remove(Order $order)
    {
        Gate::authorize('update', $order);

        try {
            $order = $this->orderService->removeCoupon($order);

            return $this->success(
                new OrderResource($order),
                'Coupon removed successfully'
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * Validate coupon without applying it
     */
    public function validate(ApplyCouponRequest $request, Order $order)
    {
        Gate::authorize('update', $order);

        try {
            $user = $request->user();
            $couponService = app(\App\Http\Services\Global\CouponService::class);

            $result = $couponService->validateAndApplyCoupon(
                $request->coupon_code,
                $order,
                $user
            );

            return $this->success([
                'valid' => true,
                'coupon_code' => $result['coupon']->code,
                'discount_type' => $result['coupon']->discount_type,
                'discount_value' => $result['coupon']->discount_value,
                'discount_amount' => $result['discount'],
                'total_before_discount' => $order->total,
                'total_after_discount' => $result['total_after_discount'],
            ], 'Coupon is valid');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => $e->getMessage()
            ], 200); // Return 200 with valid: false instead of error code
        }
    }
}
