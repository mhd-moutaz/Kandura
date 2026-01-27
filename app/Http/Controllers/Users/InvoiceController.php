<?php

namespace App\Http\Controllers\Users;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Services\Global\InvoiceService;
use App\Http\Resources\InvoiceResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Get invoice for an order
     */
    public function show(Order $order)
    {
        // $this->authorize('view', $order);

        // Check if invoice exists
        if (!$order->invoice) {
            throw new GeneralException('Invoice not found for this order', 404);
        }

        $downloadUrl = $this->invoiceService->getDownloadUrl($order->invoice);

        return $this->success('Invoice retrieved successfully', new InvoiceResource($order->invoice, $downloadUrl));
    }
}
