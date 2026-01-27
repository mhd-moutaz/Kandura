<?php

namespace App\Http\Services\Global;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
class InvoiceService
{
    /**
     * Generate invoice for an order
     */
    public function generateInvoice(Order $order): Invoice
    {
        // Check if invoice already exists
        if ($order->invoice) {
            return $order->invoice;
        }

        // Generate unique invoice number
        $invoiceNumber = $this->generateInvoiceNumber();
        // Load order relationships for invoice
        $order1=$order->load(['user', 'address', 'orderItems.design', 'orderItems.measurement', 'coupon']);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.template', ['order' => $order1, 'invoiceNumber' => $invoiceNumber]);
        Log::info("Generating invoice PDF for Order ID: {$order1->id} with Invoice Number: {$invoiceNumber}");

        // Create filename
        $filename = "invoice_{$invoiceNumber}.pdf";
        $path = "invoices/{$filename}";

        // Store PDF in private storage
        Storage::put($path, $pdf->output());

        // Create invoice record
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'order_id' => $order1->id,
            'total' => $order1->total,
            'pdf_url' => $path,
        ]);

        return $invoice;
    }

    /**
     * Generate unique invoice number with format INV-{year}-{increment}
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');

        // Get the last invoice number for this year
        $lastInvoice = Invoice::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract increment from last invoice number
            $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
            $increment = $lastNumber + 1;
        } else {
            $increment = 1;
        }

        // Format: INV-2026-00001
        return sprintf('INV-%s-%05d', $year, $increment);
    }

    /**
     * Get signed URL for downloading invoice PDF
     */
    public function getDownloadUrl(Invoice $invoice): string
    {
        return Storage::temporaryUrl(
            $invoice->pdf_url,
            now()->addMinutes(30)
        );
    }
}
