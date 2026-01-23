<?php
namespace App\Http\Controllers\Users;

use Stripe\Webhook;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\CardTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\OrderService;
use App\Http\Services\Global\WalletService;
use App\Http\Services\Users\StripeWebhookService;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{

    protected $orderService;
    protected $stripeService;

    public function __construct(OrderService $orderService, StripeWebhookService $stripeService)
    {
        $this->orderService = $orderService;
        $this->stripeService = $stripeService;
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
                $this->stripeService->handleCheckoutCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->stripeService->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->stripeService->handlePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled webhook event', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }
}
