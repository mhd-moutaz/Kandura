<?php

namespace App\Http\Services\Users;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Design;
use App\Models\CouponUsage;
use App\Enum\StatusOrderEnum;
use App\Models\CardTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Users\CouponService;
use App\Http\Services\Global\WalletService;
use App\Http\Services\Global\InvoiceService;

class OrderService
{
    protected $walletService;
    protected $couponService;
    protected $stripeService;
    protected $invoiceService;


    public function __construct(WalletService $walletService, CouponService $couponService, StripeService $stripeService, InvoiceService $invoiceService)
    {
        $this->walletService = $walletService;
        $this->couponService = $couponService;
        $this->stripeService = $stripeService;
        $this->invoiceService = $invoiceService;
    }

    public function createOrder($data)
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $data['user_id'] = $user->id;
            $data['status'] = StatusOrderEnum::PENDING;
            $order = Order::create($data);
            return $order;
        });
    }
    public function getUserOrders()
    {
        return Order::where('user_id', Auth::id())
            ->with('orderItems', 'address')
            ->get();
    }



    public function confirmOrder(Order $order, $data)
    {
        if ($order->status === StatusOrderEnum::CONFIRMED) {
            throw new GeneralException('Order is already confirmed', 400);
        }

        if ($order->status === StatusOrderEnum::CANCELLED) {
            throw new GeneralException('Cannot confirm a cancelled order', 400);
        }

        if ($order->orderItems->isEmpty()) {
            throw new GeneralException('Order has no items', 400);
        }

        return DB::transaction(function () use ($order, $data) {
            $user = Auth::user();
            $payment_method = $data['payment_method'];

            switch ($payment_method) {
                case 'wallet':
                    return $this->processWalletPayment($order, $user);

                case 'card':
                    return $this->processCardPayment($order);

                case 'cash':
                    return $this->processCashPayment($order);

                default:
                    throw new GeneralException('Invalid payment method', 400);
            }
        });
    }

    private function processWalletPayment(Order $order, $user): Order
    {
        $finalAmount = $order->total;

        if (!$this->walletService->hasEnoughBalance($user, $order->total)) {
            throw new GeneralException('Insufficient wallet balance', 400);
        }

        $this->walletService->pay(
            $user,
            $finalAmount,
            "Payment for Order #{$order->id}" . ($order->coupon_id ? " (Coupon Applied)" : ""),
            [
                'order_id' => $order->id,
                'payment_method' => 'wallet',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'order_total' => $order->total,
                'discount_amount' => $order->discount_amount ?? 0,
                'coupon_code' => $order->coupon->code ?? null,
                'items_count' => $order->orderItems->count(),
            ]
        );

        // تقليل المخزون لجميع التصاميم
        $this->reduceStockForOrder($order);

        $order->update([
            'payment_method' => 'wallet',
            'status' => StatusOrderEnum::CONFIRMED,
        ]);
        // Record coupon usage if coupon was applied
        if ($order->coupon_id) {
            $this->couponService->recordCouponUsage($order->coupon, $order, $user);
        }

        // Generate invoice and get download URL
        $order = $order->fresh();
        $invoice = $this->invoiceService->generateInvoice($order);
        $order->invoice_download_url = $this->invoiceService->getDownloadUrl($invoice);

        return $order;
    }

    private function processCardPayment(Order $order): array
    {
        $order->update([
            'payment_method' => 'card',
        ]);

        // Create Stripe checkout session
        $session = $this->stripeService->createOrderCheckout($order->fresh());

        return [
            'order' => $order->fresh(),
            'payment_required' => true,
            'session_id' => $session->id,
            'checkout_url' => $session->url,
        ];
    }
    private function processCashPayment(Order $order): Order
    {
        $user = Auth::user();

        // تقليل المخزون لجميع التصاميم
        $this->reduceStockForOrder($order);

        $order->update([
            'payment_method' => 'cash',
            'status' => StatusOrderEnum::CONFIRMED,
        ]);

         // Record coupon usage if coupon was applied
        if ($order->coupon_id) {
            $this->couponService->recordCouponUsage($order->coupon, $order, $user);
        }

        // Generate invoice and get download URL
        $order = $order->fresh();
        $invoice = $this->invoiceService->generateInvoice($order);
        $order->invoice_download_url = $this->invoiceService->getDownloadUrl($invoice);

        return $order;
    }

    /**
     * تأكيد الطلب بعد الدفع بالبطاقة (يُستدعى من Webhook)
     */
    public function confirmOrderAfterCardPayment(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            if ($order->status === StatusOrderEnum::CONFIRMED) {
                throw new GeneralException('Order is already confirmed', 400);
            }

            if ($order->payment_method !== 'card') {
                throw new GeneralException('Order payment method is not card', 400);
            }

            // تقليل المخزون لجميع التصاميم
            $this->reduceStockForOrder($order);

            $order->update([
                'status' => StatusOrderEnum::CONFIRMED,
            ]);

            // Record coupon usage if coupon was applied
            if ($order->coupon_id) {
                $this->couponService->recordCouponUsage($order->coupon, $order, $order->user);
            }

            return $order->fresh();
        });
    }

    /**
     * تقليل المخزون لجميع التصاميم في الطلب
     * يتم استدعاؤه عند تأكيد الطلب
     */
    private function reduceStockForOrder(Order $order): void
    {
        foreach ($order->orderItems as $orderItem) {
            $design = Design::lockForUpdate()->find($orderItem->design_id);

            if (!$design) {
                throw new GeneralException(
                    "Design #{$orderItem->design_id} not found",
                    404
                );
            }

            // التحقق النهائي من الكمية قبل التقليل
            if ($design->quantity < $orderItem->quantity) {
                $designName = $design->getTranslation('name', app()->getLocale());
                throw new GeneralException(
                    "Insufficient stock for design: {$designName}. Available: {$design->quantity}, Required: {$orderItem->quantity}",
                    400
                );
            }

            $design->decrement('quantity', $orderItem->quantity);

            \Log::info('Stock reduced for design', [
                'design_id' => $design->id,
                'order_id' => $order->id,
                'quantity_reduced' => $orderItem->quantity,
                'remaining_quantity' => $design->fresh()->quantity
            ]);
        }
    }

    /**
     * استعادة المخزون لجميع التصاميم في الطلب
     * يتم استدعاؤه عند إلغاء الطلب
     */
    private function restoreStockForOrder(Order $order): void
    {
        foreach ($order->orderItems as $orderItem) {
            $design = Design::lockForUpdate()->find($orderItem->design_id);

            if ($design) {
                $design->increment('quantity', $orderItem->quantity);

                \Log::info('Stock restored for design', [
                    'design_id' => $design->id,
                    'order_id' => $order->id,
                    'quantity_restored' => $orderItem->quantity,
                    'new_quantity' => $design->fresh()->quantity
                ]);
            } else {
                \Log::warning('Design not found during stock restoration', [
                    'design_id' => $orderItem->design_id,
                    'order_id' => $order->id,
                    'quantity_to_restore' => $orderItem->quantity
                ]);
            }
        }
    }

    /**
     * إلغاء الطلب
     */
    public function cancelOrder(Order $order, $reason = null): Order
    {
        return DB::transaction(function () use ($order, $reason) {
            $user = Auth::user();

            // التحقق من إمكانية الإلغاء - يجب أن تكون الحالة CONFIRMED
            if ($order->status !== StatusOrderEnum::CONFIRMED) {
                throw new GeneralException('Only confirmed orders can be cancelled', 400);
            }

            if ($order->status === StatusOrderEnum::CANCELLED) {
                throw new GeneralException('Order is already cancelled', 400);
            }

            // التحقق من أن الطلب تم تأكيده خلال ساعة واحدة
            // نستخدم updated_at لأنه يتم تحديثه عند تأكيد الطلب
            $orderConfirmedAt = Carbon::parse($order->updated_at);
            $now = Carbon::now();
            $hoursSinceConfirmation = $orderConfirmedAt->diffInHours($now, false);

            if ($hoursSinceConfirmation >= 1) {
                throw new GeneralException('Orders can only be cancelled within 1 hour of confirmation', 400);
            }

            // معالجة الاسترداد بناءً على طريقة الدفع
            if ($order->payment_method === 'wallet') {
                // استرداد المبلغ إلى المحفظة
                $this->walletService->refund(
                    $user,
                    $order->total,
                    "Refund for cancelled Order #{$order->id}",
                    [
                        'order_id' => $order->id,
                        'original_payment_method' => 'wallet',
                        'cancellation_reason' => $reason,
                        'coupon_code' => $order->coupon->code ?? null,
                    ]
                );
            } elseif ($order->payment_method === 'card') {
                // استرداد المبلغ عبر Stripe
                $paymentIntentId = $this->getPaymentIntentForOrder($order);

                if (!$paymentIntentId) {
                    // تسجيل المشكلة في logs
                    Log::warning('Payment intent not found for card order cancellation', [
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'payment_method' => $order->payment_method,
                    ]);

                    throw new GeneralException(
                        'Payment intent not found for this order. The payment may not have been processed through Stripe yet, or the order may have been paid using a different method.',
                        400
                    );
                }

                // استدعاء Stripe لاسترداد المبلغ
                $this->stripeService->refundPayment(
                    $paymentIntentId,
                    $order->total,
                    'requested_by_customer',
                    [
                        'order_id' => $order->id,
                        'refund_amount' => $order->total,
                        'cancelled_at' => now()->toDateTimeString(),
                    ]
                );

                // تسجيل الاسترداد في card_transactions
                CardTransactions::create([
                    'user_id' => $user->id,
                    'amount' => -$order->total, // سالب للاسترداد
                    'description' => "Refund for cancelled Order #{$order->id}",
                    'metadata' => [
                        'order_id' => $order->id,
                        'payment_intent_id' => $paymentIntentId,
                        'type' => 'refund',
                        'reason' => $reason,
                        'cancelled_at' => now()->toDateTimeString(),
                    ]
                ]);
            } elseif ($order->payment_method === 'cash') {
                // الدفع نقدي - لا حاجة لاسترداد
            }

            // إرجاع الكوبون إلى المستخدم إذا كان مستخدماً
            if ($order->coupon_id) {
                $this->returnCouponToUser($order);
            }

            // استعادة المخزون لجميع التصاميم
            $this->restoreStockForOrder($order);

            // تحديث حالة الطلب
            $order->update([
                'status' => StatusOrderEnum::CANCELLED,
                'note' => ($order->note ? $order->note . ' | ' : '') . "Cancelled: " . ($reason ?? 'No reason provided'),
            ]);

            return $order->fresh();
        });
    }

    /**
     * الحصول على تفاصيل طلب معين
     */
    public function getOrderDetails($orderId)
    {
        $order = Order::with([
            'orderItems.design.designImages',
            'orderItems.measurement',
            'orderItems.designOptions',
            'address.city',
            'user',
            'coupon'
        ])->findOrFail($orderId);

        // التحقق من الصلاحية
        if ($order->user_id !== Auth::id()) {
            throw new GeneralException('Unauthorized to view this order', 403);
        }

        return $order;
    }

    /**
     * الحصول على الطلب المعلق (سلة التسوق)
     */
    public function getPendingOrder()
    {
        return Order::where('user_id', Auth::id())
            ->where('status', StatusOrderEnum::PENDING)
            ->with([
                'orderItems.design.designImages',
                'orderItems.measurement',
                'orderItems.designOptions',
                'coupon'
            ])
            ->first();
    }


    private function getPaymentIntentForOrder(Order $order): ?string
    {
        // البحث عن transaction ناجح لهذا الطلب
        $transaction = CardTransactions::where('user_id', $order->user_id)
            ->where(function ($query) use ($order) {
                // استخدام JSON operator المباشر
                $query->where('metadata->order_id', $order->id)
                      ->orWhereRaw("JSON_EXTRACT(metadata, '$.order_id') = ?", [$order->id]);
            })
            ->where(function ($query) {
                // البحث عن status = success
                $query->where('metadata->status', 'success')
                      ->orWhereRaw("JSON_EXTRACT(metadata, '$.status') = ?", ['success']);
            })
            ->whereNotNull('metadata->payment_intent_id')
            ->latest()
            ->first();

        if (!$transaction) {
            return null;
        }

        // محاولة استخراج payment_intent_id من metadata
        if (is_array($transaction->metadata)) {
            return $transaction->metadata['payment_intent_id'] ?? null;
        } elseif (is_string($transaction->metadata)) {
            $metadata = json_decode($transaction->metadata, true);
            return $metadata['payment_intent_id'] ?? null;
        }

        return null;
    }

    private function returnCouponToUser(Order $order): void
    {
        $couponUsage = CouponUsage::where('order_id', $order->id)->first();

        if ($couponUsage) {
            $couponId = $couponUsage->coupon_id;
            $couponUsage->delete();

            // Decrement the coupon's used_count
            $coupon = Coupon::find($couponId);
            if ($coupon && $coupon->used_count > 0) {
                $coupon->decrement('used_count');
            }
        }
    }

}
