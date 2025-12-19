<?php

namespace App\Http\Services\Users;

use App\Models\Order;
use App\Enum\StatusOrderEnum;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                        ]
                    );
                    break;

                case 'card':
                    break;

                case 'cash':
                    break;

                default:
                    throw new GeneralException('Invalid payment method');
            }

            $order->update([
                'payment_method' => $payment_method,
                'status' => StatusOrderEnum::CONFIRMED,
            ]);

            // يمكن إضافة حدث هنا إذا كنت تستخدم Events
            // event(new OrderConfirmed($order));
            return $order->fresh();
        });
    }
}
