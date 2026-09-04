<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a payment gateway is selected (via config) but cannot be used
 * — typically because its API credentials are not configured yet.
 */
class GatewayUnavailableException extends RuntimeException
{
    public function __construct(public readonly string $provider, ?string $reason = null)
    {
        $message = "La passerelle de paiement « {$provider} » n'est pas disponible.";

        parent::__construct($reason ? "{$message} {$reason}" : $message);
    }
}
