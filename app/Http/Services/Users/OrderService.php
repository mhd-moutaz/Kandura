<?php

namespace App\Http\Services\Users;

use App\Models\Order;
use App\Enum\StatusOrderEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Global\WalletService;

class OrderService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
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

    public function confirmOrder(Order $order,  $data): Order
    {
        if ($order->status === StatusOrderEnum::CONFIRMED) {
            throw new GeneralException('Order is already confirmed');
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
                    if (!$this->walletService->hasEnoughBalance($user, $order->total)) {
                        throw new GeneralException('Insufficient wallet balance');
                    }
                    $this->walletService->pay(
                        $user,
                        $order->total,
                        "Payment for Order #{$order->id}",
                        [
                            'order_id' => $order->id,
                            'payment_method' => 'wallet',
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                            'user_email' => $user->email,
                        ]
                    );

                    $order->update([
                        'payment_method' => $payment_method,
                        'status' => StatusOrderEnum::CONFIRMED,
                    ]);
                    break;

                case 'card':
                    // الدفع بالبطاقة - سيتم التأكيد عبر Stripe Webhook
                    // فقط نحفظ طريقة الدفع وننتظر تأكيد Stripe
                    $order->update([
                        'payment_method' => $payment_method,
                        // الحالة تبقى pending حتى يتم التأكيد من Stripe
                    ]);

                    // إرجاع رسالة للمستخدم ليكمل الدفع عبر Stripe
                    return $order->fresh();

                case 'cash':
                    $order->update([
                        'payment_method' => $payment_method,
                        'status' => StatusOrderEnum::CONFIRMED,
                    ]);
                    break;

                default:
                    throw new GeneralException('Invalid payment method');
            }

            // يمكن إضافة حدث هنا إذا كنت تستخدم Events
            // event(new OrderConfirmed($order));
            return $order->fresh();
        });
    }
}
