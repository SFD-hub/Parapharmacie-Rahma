<?php

namespace App\Exceptions;

use App\Models\Product;
use RuntimeException;

class ProductExpiredException extends RuntimeException
{
    public function __construct(public readonly Product $product)
    {
        parent::__construct("Le produit « {$product->name} » a expiré et ne peut plus être vendu.");
    }
}
