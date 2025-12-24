<?php
namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Global\WalletService;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * إنشاء جلسة دفع Stripe
     */
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
        ]);

        $user = $request->user();
        $amount = $request->amount;

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = Session::create([
                'customer_email' => $user->email,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Wallet Recharge',
                            'description' => 'Add funds to your wallet',
                        ],
                        'unit_amount' => $amount * 100, // تحويل إلى سنت
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.checkoutSuccess') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),
                'metadata' => [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'wallet_recharge',
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout session created successfully',
                'data' => [
                    'session_id' => $session->id,
                    'checkout_url' => $session->url
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating checkout session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * صفحة النجاح
     */
    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('wallet.index')->with('error', 'Invalid session');
        }

        // يمكنك إضافة منطق إضافي هنا
        return view('users.wallet.success', compact('sessionId'));
    }

    /**
     * صفحة الإلغاء
     */
    public function cancel()
    {
        return view('users.wallet.cancel');
    }
}

