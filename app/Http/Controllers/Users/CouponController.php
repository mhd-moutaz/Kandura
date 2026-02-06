<?php

namespace App\Http\Controllers\Users;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\OrderService;
use App\Http\Requests\Users\ApplyCouponRequest;
use App\Http\Resources\Users\OrderResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Services\Users\CouponService;
class CouponController extends Controller
{

    protected $couponService;
    public function __construct(CouponService $couponService)
    {

        $this->couponService = $couponService;
    }

    /**
     * Apply coupon to order
     */
    public function apply(ApplyCouponRequest $request, Order $order)
    {
        Gate::authorize('update', $order);

        try {
            $order = $this->couponService->applyCoupon($order, $request->coupon_code);
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
            $order = $this->couponService->removeCouponFromOrder($order);

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

   
}
