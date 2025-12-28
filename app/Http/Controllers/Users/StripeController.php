<?php
namespace App\Http\Controllers\Users;

use Stripe\Stripe;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\StripeService;
use App\Http\Services\Global\WalletService;

class StripeController extends Controller
{
    protected $walletService;
    protected $stripeService;
    public function __construct(WalletService $walletService, StripeService $stripeService)
    {
        $this->walletService = $walletService;
        $this->stripeService = $stripeService;
    }
    public function createOrderCheckout(Order $order)
    {
        $session = $this->stripeService->createOrderCheckout($order);
        return $this->success([
            'session_id' => $session->id,
            'checkout_url' => $session->url
        ], 'Checkout session created successfully');
    }

    public function orderSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->success(null, 'Invalid session');
        }

        try {
            Stripe::setApiKey(config('stripe.secret'));
            $session = Session::retrieve($sessionId);

            $orderId = $session->metadata->order_id ?? null;
            $order = Order::find($orderId);

            return view('stripe.order-success', [
                'session' => $session,
                'order' => $order,
                'message' => 'Payment successful! Your order has been confirmed.'
            ]);
        } catch (\Exception $e) {
            return view('stripe.order-failed', [
                'message' => 'Unable to verify payment'
            ]);
        }
    }

    public function orderCancel()
    {
        return $this->success(null, 'Payment cancelled');
    }
}
