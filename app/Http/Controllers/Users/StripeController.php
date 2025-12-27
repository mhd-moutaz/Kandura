<?php
namespace App\Http\Controllers\Users;

use Stripe\Stripe;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use App\Http\Services\Global\WalletService;

class StripeController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * إنشاء جلسة دفع Stripe لشحن المحفظة
     */
    public function createWalletCheckout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
        ]);

        $user = $request->user();
        $amount = $request->amount;

        try {
            Stripe::setApiKey(config('stripe.secret'));

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
                        'unit_amount' => $amount * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.wallet.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.wallet.cancel'),
                'metadata' => [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'wallet_recharge',
                ]
            ]);

            return $this->success([
                'session_id' => $session->id,
                'checkout_url' => $session->url
            ], 'Checkout session created successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating checkout session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء جلسة دفع Stripe للطلب
     */
    public function createOrderCheckout(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = $request->user();
        $order = \App\Models\Order::findOrFail($request->order_id);

        // التحقق من أن الطلب يخص المستخدم
        if ($order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // التحقق من حالة الطلب
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order is not in pending status'
            ], 400);
        }

        try {
            Stripe::setApiKey(config('stripe.secret'));

            // إنشاء line items من عناصر الطلب
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

            return $this->success([
                'session_id' => $session->id,
                'checkout_url' => $session->url
            ], 'Checkout session created successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating checkout session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * صفحة نجاح شحن المحفظة
     */
    public function walletSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->success(null, 'Invalid session');
        }

        return $this->success([
            'session_id' => $sessionId,
            'message' => 'Payment successful! Your wallet will be updated shortly.'
        ], 'Payment completed successfully');
    }

    /**
     * صفحة إلغاء شحن المحفظة
     */
    public function walletCancel()
    {
        return $this->success(null, 'Payment cancelled');
    }

    /**
     * صفحة نجاح دفع الطلب
     */
    public function orderSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->success(null, 'Invalid session');
        }

        return $this->success([
            'session_id' => $sessionId,
            'message' => 'Payment successful! Your order will be confirmed shortly.'
        ], 'Payment completed successfully');
    }

    /**
     * صفحة إلغاء دفع الطلب
     */
    public function orderCancel()
    {
        return $this->success(null, 'Payment cancelled');
    }
}
