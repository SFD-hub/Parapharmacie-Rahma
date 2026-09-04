<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function store(Order $order, InvoiceService $invoices): RedirectResponse
    {
        $invoices->generateForOrder($order, auth('admin')->user());

        return back()->with('success', 'Facture générée avec succès.');
    }

    public function download(Invoice $invoice, InvoiceService $invoices): Response
    {
        return $invoices->renderPdf($invoice)->download("{$invoice->invoice_number}.pdf");
    }

    public function print(Invoice $invoice, InvoiceService $invoices): Response
    {
        return $invoices->renderPdf($invoice)->stream("{$invoice->invoice_number}.pdf");
    }

    public function whatsapp(Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        return redirect()->away($invoices->whatsAppUrl($invoice));
    }
}
