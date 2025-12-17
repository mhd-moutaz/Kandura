<?php

namespace App\Http\Services\Admins;

use App\Models\Order;

class OrderService
{
    public function index(array $filters)
    {
        return Order::with(['user', 'address.city', 'orderItems.design', 'orderItems.measurement'])
            ->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();
    }

    public function show($orderId)
    {
        return Order::with([
            'user',
            'address.city',
            'orderItems.design.designImages',
            'orderItems.measurement',
            'orderItems.designOptions'
        ])->findOrFail($orderId);
    }

    public function updateStatus(Order $order, string $status)
    {
        $order->update(['status' => $status]);
        return $order;
    }
}
