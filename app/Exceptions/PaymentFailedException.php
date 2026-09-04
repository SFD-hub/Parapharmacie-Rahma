<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a payment gateway explicitly reports that a charge failed
 * (as opposed to being unavailable or misconfigured).
 */
class PaymentFailedException extends RuntimeException
{
    public function __construct(public readonly ?string $reference = null, ?string $reason = null)
    {
        parent::__construct($reason ?? 'Le paiement a échoué.');
    }
}
