<?php

namespace App\Http\Services\Users;

use App\Models\Order;
use App\Enum\StatusOrderEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Users\CouponService;
use App\Http\Services\Global\WalletService;

class OrderService
{
    protected $walletService;
    protected $couponService;
    protected $stripeService;


    public function __construct(WalletService $walletService, CouponService $couponService, StripeService $stripeService)
    {
        $this->walletService = $walletService;
        $this->couponService = $couponService;
        $this->stripeService = $stripeService;
    }

    public function createOrder($data)
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $data['user_id'] = $user->id;
            $data['status'] = StatusOrderEnum::PENDING;
            $order = Order::create($data);
            return $order;
        });
    }
    public function getUserOrders()
    {
        return Order::where('user_id', Auth::id())
            ->with('orderItems', 'address')
            ->get();
    }
    public function applyCoupon(Order $order, string $couponCode): Order
    {
        if ($order->status !== StatusOrderEnum::PENDING) {
            throw new GeneralException('Can only apply coupons to pending orders', 400);
        }

        $user = Auth::user();

        // Remove existing coupon if any
        if ($order->coupon_id) {
            $order = $this->couponService->removeCouponFromOrder($order);
        }

        // Validate and calculate discount
        $couponData = $this->couponService->validateAndApplyCoupon($couponCode, $order, $user);

        // Apply coupon to order
        $order = $this->couponService->applyCouponToOrder(
            $couponData['coupon'],
            $order,
            $couponData['discount']
        );

        return $order->fresh(['coupon', 'orderItems']);
    }
    /**
     * Remove coupon from order
     */
    public function removeCoupon(Order $order): Order
    {
        if ($order->status !== StatusOrderEnum::PENDING) {
            throw new GeneralException('Can only remove coupons from pending orders', 400);
        }

        return $this->couponService->removeCouponFromOrder($order);
    }

    public function confirmOrder(Order $order, $data)
    {
        if ($order->status === StatusOrderEnum::CONFIRMED) {
            throw new GeneralException('Order is already confirmed', 400);
        }

        if ($order->status === StatusOrderEnum::CANCELLED) {
            throw new GeneralException('Cannot confirm a cancelled order', 400);
        }

        if ($order->orderItems->isEmpty()) {
            throw new GeneralException('Order has no items', 400);
        }

        return DB::transaction(function () use ($order, $data) {
            $user = Auth::user();
            $payment_method = $data['payment_method'];

            switch ($payment_method) {
                case 'wallet':
                    return $this->processWalletPayment($order, $user);

                case 'card':
                    return $this->processCardPayment($order);

                case 'cash':
                    return $this->processCashPayment($order);

                default:
                    throw new GeneralException('Invalid payment method', 400);
            }
        });
    }

    private function processWalletPayment(Order $order, $user): Order
    {
        $finalAmount = $order->total;

        if (!$this->walletService->hasEnoughBalance($user, $order->total)) {
            throw new GeneralException('Insufficient wallet balance', 400);
        }

        $this->walletService->pay(
            $user,
            $finalAmount,
            "Payment for Order #{$order->id}" . ($order->coupon_id ? " (Coupon Applied)" : ""),
            [
                'order_id' => $order->id,
                'payment_method' => 'wallet',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'order_total' => $order->total,
                'discount_amount' => $order->discount_amount ?? 0,
                'coupon_code' => $order->coupon->code ?? null,
                'items_count' => $order->orderItems->count(),
            ]
        );

        $order->update([
            'payment_method' => 'wallet',
            'status' => StatusOrderEnum::CONFIRMED,
        ]);
        // Record coupon usage if coupon was applied
        if ($order->coupon_id) {
            $this->couponService->recordCouponUsage($order->coupon, $order, $user);
        }

        return $order->fresh();
    }

    private function processCardPayment(Order $order): array
    {
        $order->update([
            'payment_method' => 'card',
        ]);

        // Create Stripe checkout session
        $session = $this->stripeService->createOrderCheckout($order->fresh());

        return [
            'order' => $order->fresh(),
            'payment_required' => true,
            'session_id' => $session->id,
            'checkout_url' => $session->url,
        ];
    }
    private function processCashPayment(Order $order): Order
    {
        $user = Auth::user();

        $order->update([
            'payment_method' => 'cash',
            'status' => StatusOrderEnum::CONFIRMED,
        ]);

         // Record coupon usage if coupon was applied
        if ($order->coupon_id) {
            $this->couponService->recordCouponUsage($order->coupon, $order, $user);
        }

        return $order->fresh();
    }

    /**
     * تأكيد الطلب بعد الدفع بالبطاقة (يُستدعى من Webhook)
     */
    public function confirmOrderAfterCardPayment(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            if ($order->status === StatusOrderEnum::CONFIRMED) {
                throw new GeneralException('Order is already confirmed', 400);
            }

            if ($order->payment_method !== 'card') {
                throw new GeneralException('Order payment method is not card', 400);
            }

            $order->update([
                'status' => StatusOrderEnum::CONFIRMED,
            ]);

            // Record coupon usage if coupon was applied
            if ($order->coupon_id) {
                $this->couponService->recordCouponUsage($order->coupon, $order, $order->user);
            }

            return $order->fresh();
        });
    }

    /**
     * إلغاء الطلب
     */
    public function cancelOrder(Order $order, $reason = null): Order
    {
        return DB::transaction(function () use ($order, $reason) {
            $user = Auth::user();

            // التحقق من إمكانية الإلغاء
            if ($order->status === StatusOrderEnum::COMPLETED) {
                throw new GeneralException('Cannot cancel a completed order', 400);
            }

            if ($order->status === StatusOrderEnum::CANCELLED) {
                throw new GeneralException('Order is already cancelled', 400);
            }

            // إذا كان الطلب مؤكد والدفع من المحفظة - نرجع المبلغ
            if ($order->status === StatusOrderEnum::CONFIRMED && $order->payment_method === 'wallet') {
                $this->walletService->refund(
                    $user,
                    $order->total,
                    "Refund for cancelled Order #{$order->id}",
                    [
                        'order_id' => $order->id,
                        'original_payment_method' => 'wallet',
                        'cancellation_reason' => $reason,
                        'coupon_code' => $order->coupon->code ?? null,
                    ]
                );
            }

            // تحديث حالة الطلب
            $order->update([
                'status' => StatusOrderEnum::CANCELLED,
                'note' => ($order->note ? $order->note . ' | ' : '') . "Cancelled: " . ($reason ?? 'No reason provided'),
            ]);

            return $order->fresh();
        });
    }

    /**
     * الحصول على تفاصيل طلب معين
     */
    public function getOrderDetails($orderId)
    {
        $order = Order::with([
            'orderItems.design.designImages',
            'orderItems.measurement',
            'orderItems.designOptions',
            'address.city',
            'user',
            'coupon'
        ])->findOrFail($orderId);

        // التحقق من الصلاحية
        if ($order->user_id !== Auth::id()) {
            throw new GeneralException('Unauthorized to view this order', 403);
        }

        return $order;
    }

    /**
     * الحصول على الطلب المعلق (سلة التسوق)
     */
    public function getPendingOrder()
    {
        return Order::where('user_id', Auth::id())
            ->where('status', StatusOrderEnum::PENDING)
            ->with([
                'orderItems.design.designImages',
                'orderItems.measurement',
                'orderItems.designOptions',
                'coupon'
            ])
            ->first();
    }

    /**
     * تحديث عنوان الطلب
     */
    public function updateOrderAddress(Order $order, $addressId): Order
    {
        return DB::transaction(function () use ($order, $addressId) {
            // التحقق من الحالة
            if ($order->status !== StatusOrderEnum::PENDING) {
                throw new GeneralException('Cannot update address of a non-pending order', 400);
            }

            // التحقق من العنوان
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($addressId);

            $order->update([
                'address_id' => $address->id,
            ]);

            return $order->fresh();
        });
    }



}
