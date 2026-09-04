<?php

namespace App\Enums;

/**
 * Payment methods selectable at checkout. No real payment gateway is
 * integrated yet — every method currently resolves to the manual gateway
 * (see App\Services\Payment). New gateways can be added by implementing
 * PaymentGatewayInterface and registering them in PaymentGatewayFactory.
 */
enum PaymentMethod: string
{
    case CashOnDelivery = 'cash_on_delivery';
    case Wave = 'wave';
    case OrangeMoney = 'orange_money';
    case CreditCard = 'credit_card';
    case PayPal = 'paypal';
    case Points = 'points';

    public function label(): string
    {
        return match ($this) {
            self::CashOnDelivery => 'Paiement à la livraison',
            self::Wave => 'Wave',
            self::OrangeMoney => 'Orange Money',
            self::CreditCard => 'Carte bancaire',
            self::PayPal => 'PayPal',
            self::Points => 'Points de fidélité',
        };
    }
}
