<?php

namespace App\Http\Services\Admins;

use App\Models\Order;
use App\Enum\StatusOrderEnum;
use App\Http\Services\Global\InvoiceService;


class OrderService
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
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
            'orderItems.designOptions',
            'review'
        ])->findOrFail($orderId);
    }

    public function updateStatus(Order $order, $status)
    {
        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        // Auto-generate invoice when order is confirmed or completed
        if (
            ($status === StatusOrderEnum::CONFIRMED && $oldStatus !== StatusOrderEnum::CONFIRMED)
        ) {
            $this->invoiceService->generateInvoice($order);
        }

        return $order;
    }
}
