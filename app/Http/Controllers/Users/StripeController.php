<?php
namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\StripeService;
use App\Http\Services\Global\WalletService;
use App\Models\Order;

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

        return $this->success([
            'session_id' => $sessionId,
            'message' => 'Payment successful! Your order will be confirmed shortly.'
        ], 'Payment completed successfully');
    }

    public function orderCancel()
    {
        return $this->success(null, 'Payment cancelled');
    }
}
