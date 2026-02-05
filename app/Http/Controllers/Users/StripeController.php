<?php
namespace App\Http\Controllers\Users;

use Stripe\Stripe;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\StripeService;
use App\Http\Services\Global\WalletService;
use App\Http\Services\Global\InvoiceService;
use App\Enum\StatusOrderEnum;

class StripeController extends Controller
{
    protected $walletService;
    protected $stripeService;
    protected $invoiceService;
    public function __construct(WalletService $walletService, StripeService $stripeService, InvoiceService $invoiceService)
    {
        $this->walletService = $walletService;
        $this->stripeService = $stripeService;
        $this->invoiceService = $invoiceService;
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

            // Generate invoice download URL if order exists
            $invoiceDownloadUrl = null;
            if ($order) {
                // Update order status to confirmed after successful payment
                if ($order->status === StatusOrderEnum::PENDING) {
                    $order->update(['status' => StatusOrderEnum::CONFIRMED]);
                }

                // Generate invoice if not exists
                if (!$order->invoice) {
                    $this->invoiceService->generateInvoice($order);
                    $order->refresh();
                }

                // Get download URL if invoice exists
                if ($order->invoice) {
                    $invoiceDownloadUrl = $this->invoiceService->getDownloadUrl($order->invoice);
                }
            }

            return view('stripe.order-success', [
                'session' => $session,
                'order' => $order,
                'message' => 'Payment successful! Your order has been confirmed.',
                'invoiceDownloadUrl' => $invoiceDownloadUrl
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
