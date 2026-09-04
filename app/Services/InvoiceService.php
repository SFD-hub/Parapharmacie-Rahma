<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Simple invoicing on top of the existing Order model: one invoice per
 * order, generated on demand (never edited afterwards), rendered as a PDF
 * via the already-installed DomPDF package. No separate line-item storage —
 * an invoice is a formatted snapshot of its order, and OrderItem already
 * immutably records product name/sku/price/quantity at order time.
 */
class InvoiceService
{
    public function __construct(
        private readonly InvoiceNumberService $numbers,
        private readonly GeneralSettingsService $settings,
        private readonly ImageOptimizerService $images,
    ) {}

    /**
     * Idempotent: returns the existing invoice for the order if one was
     * already generated, otherwise creates it. Guarantees a single invoice
     * per order both here and via the invoices.order_id unique constraint.
     */
    public function generateForOrder(Order $order, ?Admin $admin = null): Invoice
    {
        return DB::transaction(function () use ($order, $admin) {
            $existing = $order->invoice()->first();

            if ($existing) {
                return $existing;
            }

            return Invoice::create([
                'order_id' => $order->id,
                'admin_id' => $admin?->id,
                'invoice_number' => $this->numbers->generate(),
            ]);
        });
    }

    public function renderPdf(Invoice $invoice): PdfDocument
    {
        $invoice->loadMissing(['order.items', 'order.address', 'order.user']);

        $shop = $this->settings->all();

        return Pdf::loadView('admin.invoices.pdf', [
            'invoice' => $invoice,
            'order' => $invoice->order,
            'shop' => $shop,
            // DomPDF reads local files directly; resolving the stored public
            // URL back to a filesystem path avoids relying on remote HTTP
            // fetches (disabled by default) to embed the logo.
            'logoPath' => $this->images->localPath($shop['shop_logo']),
            'money' => fn (float $amount) => $this->settings->formatPrice($amount),
        ])->setPaper('a4');
    }

    /**
     * Builds the wa.me link with a pre-filled message pointing to a
     * permanently signed (non-expiring) public download URL, so the
     * invoice stays reachable to the customer whenever they open it.
     */
    public function whatsAppUrl(Invoice $invoice): string
    {
        $invoice->loadMissing('order.address');

        $phone = preg_replace('/\D+/', '', $invoice->order->address?->phone ?? '');
        $downloadUrl = URL::signedRoute('invoices.download', ['invoice' => $invoice]);
        $shopName = $this->settings->shopName();

        $message = "Bonjour,\n\nVotre commande a été validée.\n\nVous trouverez votre facture ici :\n{$downloadUrl}\n\nMerci pour votre confiance.\n\n{$shopName}.";

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }
}
