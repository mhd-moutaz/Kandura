<?php

namespace App\Http\Services\Users;

use Stripe\Stripe;
use App\Models\Order;
use Stripe\Checkout\Session;
use App\Exceptions\GeneralException;
use App\Models\CardTransactions;
use Illuminate\Support\Facades\Auth;

class StripeService
{
    public function createOrderCheckout($order)
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            throw new GeneralException('Unauthorized access to this order.', 403);
        }

        if ($order->status !== 'pending') {
            throw new GeneralException('Order is not in pending status.', 400);
        }

        try {
            Stripe::setApiKey(config('stripe.secret'));

            $lineItems = [];
            foreach ($order->orderItems as $item) {
                // Handle design name (could be string or array/JSON)
                $designName = $item->design->name;
                if (is_string($designName)) {
                    $nameArray = json_decode($designName, true);
                    $designName = $nameArray['en'] ?? $nameArray['ar'] ?? $designName;
                } elseif (is_array($designName)) {
                    $designName = $designName['en'] ?? $designName['ar'] ?? 'Design';
                }

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $designName,
                            'description' => 'Size: ' . $item->measurement->size,
                        ],
                        'unit_amount' => (int) round($item->unit_price * 100),
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            CardTransactions::create([
                'user_id' => $user->id,
                'amount' => $order->total,
                'description' => 'Initiated payment for Order #' . $order->id,
                'metadata' => [
                    'order_id' => $order->id,
                    'total_before_discount' => $order->total_before_discount ?? $order->total,
                    'discount_amount' => $order->discount_amount ?? 0,
                    'final_price' => $order->total,
                    'coupon_code' => $order->coupon->code ?? null,
                ],
            ]);

            // إعداد معلمات الجلسة
            $sessionParams = [
                'customer_email' => $user->email,
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.order.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.order.cancel'),
                'metadata' => [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'type' => 'order_payment',
                    'coupon_code' => $order->coupon->code ?? null,
                    'discount_amount' => $order->discount_amount ?? 0,
                ]
            ];

            // إضافة الخصم إذا كان موجوداً
            if ($order->coupon_id && $order->discount_amount > 0) {
                // إضافة ترويجي يدوي (بدون إنشاء كوبون في Stripe)
                $sessionParams['discounts'] = [
                    [
                        'coupon' => $this->createOrGetStripeCoupon($order)
                    ]
                ];
            }

            $session = Session::create($sessionParams);

            return $session;
        } catch (\Exception $e) {
            throw new GeneralException('Error creating Stripe checkout session: ' . $e->getMessage(), 500);
        }
    }
    /**
     * إنشاء أو استرجاع كوبون Stripe
     */
    private function createOrGetStripeCoupon($order)
    {
        try {
            $coupon = $order->coupon;

            // إذا كان لدينا معرف كوبون مخزن في قاعدة البيانات
            if ($coupon->stripe_coupon_id) {
                return $coupon->stripe_coupon_id;
            }

            // إنشاء كوبون جديد في Stripe
            $stripeCouponParams = [
                'duration' => 'once',
            ];

            if ($coupon->discount_type === 'percentage') {
                $stripeCouponParams['percent_off'] = (float) $coupon->discount_value;
            } else {
                $stripeCouponParams['amount_off'] = (int) round($coupon->discount_value * 100);
                $stripeCouponParams['currency'] = 'usd';
            }

            $stripeCoupon = \Stripe\Coupon::create($stripeCouponParams);

            // حفظ معرف الكوبون في قاعدة البيانات
            $coupon->update(['stripe_coupon_id' => $stripeCoupon->id]);

            return $stripeCoupon->id;

        } catch (\Exception $e) {
            throw new GeneralException('Error creating Stripe coupon: ' . $e->getMessage());
        }
    }

    /**
     * Process a refund for a Stripe payment
     *
     * @param string $paymentIntentId The Stripe payment intent ID
     * @param float $amount The amount to refund (in dollars)
     * @param string $reason Refund reason (default: 'requested_by_customer')
     * @param array $metadata Additional metadata for the refund
     * @return \Stripe\Refund
     * @throws GeneralException
     */
    public function refundPayment(string $paymentIntentId, float $amount, string $reason = 'requested_by_customer', array $metadata = [])
    {
        try {
            Stripe::setApiKey(config('stripe.secret'));

            // Create refund in Stripe
            $refund = \Stripe\Refund::create([
                'payment_intent' => $paymentIntentId,
                'amount' => (int) round($amount * 100), // Convert to cents
                'reason' => $reason,
                'metadata' => $metadata,
            ]);

            return $refund;

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            throw new GeneralException('Invalid refund request: ' . $e->getMessage(), 400);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new GeneralException('Stripe API error during refund: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            throw new GeneralException('Error processing refund: ' . $e->getMessage(), 500);
        }
    }

}
