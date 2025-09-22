<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $invoiceId;

    public function __construct(int $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function handle(): void
    {
        try {
            $invoice = Invoice::with(['order.customer', 'order.orderItems'])->findOrFail($this->invoiceId);

            // Generate PDF content (placeholder implementation)
            $pdfContent = $this->generatePdfContent($invoice);

            // Store PDF file
            $filename = "invoices/{$invoice->number}.pdf";
            Storage::disk('public')->put($filename, $pdfContent);

            // Update invoice with PDF path
            $invoice->update([
                'pdf_path' => $filename,
            ]);

            Log::info('Invoice PDF generated successfully', [
                'invoice_id' => $this->invoiceId,
                'filename' => $filename,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate invoice PDF', [
                'error' => $e->getMessage(),
                'invoice_id' => $this->invoiceId,
            ]);
            throw $e;
        }
    }

    private function generatePdfContent(Invoice $invoice): string
    {
        // This is a placeholder implementation
        // In a real application, you would use a PDF library like DomPDF or TCPDF
        $content = "INVOICE #{$invoice->number}\n";
        $content .= "Date: {$invoice->issued_at->format('Y-m-d')}\n";
        $content .= "Customer: {$invoice->order->customer->full_name}\n";
        $content .= "Total: {$invoice->total_gross} PLN\n";
        $content .= "\nItems:\n";
        
        foreach ($invoice->order->orderItems as $item) {
            $content .= "- {$item->name} x{$item->qty} = {$item->price_gross} PLN\n";
        }

        return $content;
    }
}
