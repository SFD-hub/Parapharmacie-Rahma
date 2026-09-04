<?php

namespace App\Enums;

enum CurrencySymbolPosition: string
{
    case Before = 'before';
    case After = 'after';

    public function label(): string
    {
        return match ($this) {
            self::Before => 'Avant le montant',
            self::After => 'Après le montant',
        };
    }
}
