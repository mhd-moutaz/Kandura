<?php

namespace App\Http\Services\Users;


use App\Models\Order;
use App\Models\CardTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class StripeWebhookService
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * معالجة إتمام الدفع
     */
    public function handleCheckoutCompleted($session)
    {
        $type = $session->metadata->type ?? null;
        if ($type === 'order_payment') {
            $this->handleOrderPayment($session);
        }
    }



    /**
     * معالجة دفع الطلب
     */
    private function handleOrderPayment($session)
    {
        DB::beginTransaction();
        try {
            $orderId = $session->metadata->order_id ?? null;

            if (!$orderId) {
                throw new \Exception('Missing order_id in metadata');
            }

            $order = Order::with('coupon', 'user')->find($orderId);
            if (!$order) {
                throw new \Exception('Order not found');
            }

            CardTransactions::create([
                'user_id' => $order->user_id,
                'amount' => $session->amount_total / 100, // من cents إلى dollars
                'description' => 'Successful payment for Order #' . $orderId,
                'metadata' => [
                    'order_id' => $orderId,
                    'session_id' => $session->id,
                    'payment_intent_id' => $session->payment_intent ?? null,
                    'payment_status' => $session->payment_status,
                    'customer_email' => $session->customer_email,
                    'coupon_code' => $order->coupon->code ?? null,
                    'discount_amount' => $order->discount_amount ?? 0,
                    'status' => 'success',
                    'timestamp' => now()->toDateTimeString(),
                ]
            ]);

            // تأكيد الطلب
            $this->orderService->confirmOrderAfterCardPayment($order);

            DB::commit();

            Log::info('Order payment completed successfully', [
                'order_id' => $orderId,
                'session_id' => $session->id,
                'amount' => $session->amount_total / 100
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($order)) {
                CardTransactions::create([
                    'user_id' => $order->user_id,
                    'amount' => $session->amount_total / 100,
                    'description' => 'Failed payment processing for Order #' . $orderId,
                    'metadata' => [
                        'order_id' => $orderId,
                        'session_id' => $session->id,
                        'error' => $e->getMessage(),
                        'status' => 'error',
                        'timestamp' => now()->toDateTimeString(),
                    ]
                ]);
            }

            Log::error('Order payment failed', [
                'error' => $e->getMessage(),
                'session_id' => $session->id
            ]);
        }
    }

    /**
     * معالجة نجاح الدفع
     */
    public function handlePaymentSucceeded($paymentIntent)
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;

            if (!$orderId) {
                Log::warning('Payment succeeded but no order_id in metadata', [
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return;
            }

            $order = Order::find($orderId);
            if (!$order) {
                Log::error('Order not found for payment success', [
                    'order_id' => $orderId,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return;
            }

            // ✅ تسجيل نجاح الدفع
            CardTransactions::create([
                'user_id' => $order->user_id,
                'amount' => $paymentIntent->amount / 100,
                'description' => 'Payment intent succeeded for Order #' . $orderId,
                'metadata' => [
                    'order_id' => $orderId,
                    'payment_intent_id' => $paymentIntent->id,
                    'payment_method' => $paymentIntent->payment_method ?? null,
                    'currency' => $paymentIntent->currency,
                    'status' => 'success',
                    'timestamp' => now()->toDateTimeString(),
                ]
            ]);

            Log::info('Payment succeeded', [
                'payment_intent_id' => $paymentIntent->id,
                'order_id' => $orderId,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling payment success', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent->id
            ]);
        }
    }

    /**
     * معالجة فشل الدفع
     */
    public function handlePaymentFailed($paymentIntent)
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;
            $failureMessage = $paymentIntent->last_payment_error->message ?? 'Unknown error';

            if (!$orderId) {
                Log::error('Payment failed but no order_id', [
                    'payment_intent_id' => $paymentIntent->id,
                    'failure_message' => $failureMessage
                ]);
                return;
            }

            $order = Order::find($orderId);
            if (!$order) {
                Log::error('Order not found for payment failure', [
                    'order_id' => $orderId,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return;
            }

            // ✅ تسجيل فشل الدفع في card_transactions
            CardTransactions::create([
                'user_id' => $order->user_id,
                'amount' => $paymentIntent->amount / 100,
                'description' => 'Payment failed for Order #' . $orderId,
                'metadata' => [
                    'order_id' => $orderId,
                    'payment_intent_id' => $paymentIntent->id,
                    'failure_code' => $paymentIntent->last_payment_error->code ?? null,
                    'failure_message' => $failureMessage,
                    'decline_code' => $paymentIntent->last_payment_error->decline_code ?? null,
                    'currency' => $paymentIntent->currency,
                    'status' => 'failed',
                    'timestamp' => now()->toDateTimeString(),
                ]
            ]);


            Log::error('Payment failed', [
                'payment_intent_id' => $paymentIntent->id,
                'order_id' => $orderId,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'failure_message' => $failureMessage,
                'failure_code' => $paymentIntent->last_payment_error->code ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling payment failure', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent->id
            ]);
        }
    }

}
