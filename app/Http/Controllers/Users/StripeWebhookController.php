<?php
namespace App\Http\Controllers\Users;

use Stripe\Webhook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\WalletService;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
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
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // معالجة الحدث
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $userId = $session->metadata->user_id ?? null;
            $amount = $session->metadata->amount ?? null;

            if ($userId && $amount) {
                $user = User::find($userId);

                if ($user) {
                    // إضافة الرصيد للمحفظة
                    $this->walletService->deposit(
                        $user,
                        (float) $amount,
                        'Stripe payment - Session: ' . $session->id,
                        [
                            'stripe_session_id' => $session->id,
                            'stripe_payment_intent' => $session->payment_intent,
                            'payment_status' => $session->payment_status,
                        ]
                    );

                    Log::info('Wallet recharged successfully', [
                        'user_id' => $userId,
                        'amount' => $amount,
                        'session_id' => $session->id
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
