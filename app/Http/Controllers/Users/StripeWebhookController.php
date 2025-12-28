<?php
namespace App\Http\Controllers\Users;

use Stripe\Webhook;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Services\Global\WalletService;
use App\Http\Services\Users\OrderService;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected $walletService;
    protected $orderService;

    public function __construct(WalletService $walletService, OrderService $orderService)
    {
        $this->walletService = $walletService;
        $this->orderService = $orderService;
    }

    /**
     * معالجة Webhook من Stripe
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('stripe.webhook_secret')
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // معالجة الأحداث المختلفة
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled webhook event', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * معالجة إتمام الدفع
     */
    private function handleCheckoutCompleted($session)
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

            $order = Order::find($orderId);
            if (!$order) {
                throw new \Exception('Order not found');
            }

            // تأكيد الطلب
            $order->update([
                'status' => 'confirmed',
                'payment_method' => 'card',
            ]);

            DB::commit();

            Log::info('Order payment completed successfully', [
                'order_id' => $orderId,
                'session_id' => $session->id,
                'amount' => $session->amount_total / 100
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order payment failed', [
                'error' => $e->getMessage(),
                'session_id' => $session->id
            ]);
        }
    }

    /**
     * معالجة نجاح الدفع
     */
    private function handlePaymentSucceeded($paymentIntent)
    {
        Log::info('Payment succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount / 100,
            'currency' => $paymentIntent->currency
        ]);
    }

    /**
     * معالجة فشل الدفع
     */
    private function handlePaymentFailed($paymentIntent)
    {
        Log::error('Payment failed', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount / 100,
            'currency' => $paymentIntent->currency,
            'failure_message' => $paymentIntent->last_payment_error->message ?? 'Unknown error'
        ]);
    }
}
