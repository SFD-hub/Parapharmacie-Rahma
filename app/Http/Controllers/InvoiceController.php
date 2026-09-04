<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer-facing invoice download, reached only via the signed link shared
 * through WhatsApp — no login required, the signature is the authorization.
 */
class InvoiceController extends Controller
{
    public function download(Invoice $invoice, InvoiceService $invoices): Response
    {
        return $invoices->renderPdf($invoice)->download("{$invoice->invoice_number}.pdf");
    }
}
