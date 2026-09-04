<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when an incoming payment provider webhook fails signature
 * verification or is otherwise malformed.
 */
class InvalidWebhookException extends RuntimeException
{
    public function __construct(?string $reason = null)
    {
        parent::__construct($reason ?? 'Signature de webhook invalide.');
    }
}
