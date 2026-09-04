<?php

namespace App\Services;

use App\Models\Invoice;

/**
 * Generates strictly sequential, gap-free invoice numbers per calendar year
 * (e.g. FAC-2026-000001). Must be called from within a DB transaction: the
 * row lock taken on the last invoice of the year is what serializes
 * concurrent generation attempts and guarantees no duplicate number is ever
 * handed out (the invoices.invoice_number unique constraint is the final
 * safety net).
 */
class InvoiceNumberService
{
    public function generate(): string
    {
        $year = now()->year;

        $lastNumber = Invoice::query()
            ->whereYear('created_at', $year)
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('invoice_number');

        $sequence = $lastNumber ? ((int) substr($lastNumber, -6)) + 1 : 1;

        return sprintf('FAC-%d-%06d', $year, $sequence);
    }
}
