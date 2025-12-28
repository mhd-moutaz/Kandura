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
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item->design->name['en'] ?? $item->design->name['ar'],
                            'description' => 'Size: ' . $item->measurement->size,
                        ],
                        'unit_amount' => $item->unit_price * 100,
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
                    'total_price' => $order->total,
                ],
            ]);
            $session = Session::create([
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
                ]
            ]);
            return $session;
        } catch (\Exception $e) {
            throw new GeneralException('Error creating Stripe checkout session: ' . $e->getMessage(), 500);
        }
    }


}
