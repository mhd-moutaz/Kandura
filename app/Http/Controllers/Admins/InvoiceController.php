<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Services\Global\InvoiceService;
use App\Http\Resources\InvoiceResource;
use App\Models\Order;
use Illuminate\Http\Request;
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
     * Get invoice for an order (admin)
     */
    public function show(Order $order)
    {
        if (!$order->invoice) {
            return response()->json([
                'message' => 'Invoice not found for this order'
            ], 404);
        }

        $downloadUrl = $this->invoiceService->getDownloadUrl($order->invoice);

        return response()->json([
            'invoice_number' => $order->invoice->invoice_number,
            'order_id' => $order->id,
            'total' => $order->invoice->total,
            'download_url' => $downloadUrl,
            'created_at' => $order->invoice->created_at,
        ]);
    }

    /**
     * Regenerate invoice for an order (admin)
     */
    public function regenerate(Order $order)
    {
        // Delete existing invoice if present
        if ($order->invoice) {
            $order->invoice->delete();
        }

        $invoice = $this->invoiceService->generateInvoice($order);

        return response()->json([
            'message' => 'Invoice regenerated successfully',
            'invoice_number' => $invoice->invoice_number,
        ]);
    }
}
