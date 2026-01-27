<?php

namespace App\Http\Services\Admins;

use App\Models\Order;
use App\Enum\StatusOrderEnum;
use App\Http\Services\Global\InvoiceService;
use App\Mail\InvoiceCreated;
use Illuminate\Support\Facades\Mail;

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
            'orderItems.designOptions'
        ])->findOrFail($orderId);
    }

    public function updateStatus(Order $order, $status)
    {
        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        // Auto-generate invoice when order is completed
        if ($status === StatusOrderEnum::COMPLETED && $oldStatus !== StatusOrderEnum::COMPLETED) {
            $this->invoiceService->generateInvoice($order);
        }

        return $order;
    }
}
