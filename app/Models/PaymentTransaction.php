<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentTransactionStatus;
use Database\Factories\PaymentTransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ledger of every payment attempt (provider, reference, gateway response,
 * error) — the "journalisation" layer that makes future real-gateway
 * integrations debuggable. Purely additive: never read by existing order
 * logic, only written to alongside it.
 */
#[Fillable(['order_id', 'provider', 'status', 'reference', 'amount', 'response_payload', 'error_message'])]
class PaymentTransaction extends Model
{
    /** @use HasFactory<PaymentTransactionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'provider' => PaymentMethod::class,
            'status' => PaymentTransactionStatus::class,
            'amount' => 'decimal:2',
            'response_payload' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
