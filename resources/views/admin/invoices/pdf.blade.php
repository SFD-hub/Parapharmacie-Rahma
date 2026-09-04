<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 24px 32px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header td {
            vertical-align: top;
        }

        .shop-name {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }

        .shop-meta {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.5;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            color: #16a34a;
            text-align: right;
        }

        .invoice-meta {
            text-align: right;
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }

        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .client-block {
            width: 100%;
            margin-bottom: 24px;
        }

        .client-block td {
            vertical-align: top;
            width: 50%;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.items thead th {
            background: #f3f4f6;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.items tbody td {
            padding: 8px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
        }

        table.items .align-right {
            text-align: right;
        }

        table.totals {
            width: 260px;
            margin-left: auto;
            border-collapse: collapse;
        }

        table.totals td {
            padding: 4px 0;
            font-size: 12px;
        }

        table.totals .align-right {
            text-align: right;
        }

        table.totals .total-row td {
            border-top: 2px solid #111827;
            font-weight: bold;
            font-size: 14px;
            padding-top: 8px;
        }

        .payment-block {
            margin-top: 24px;
            font-size: 12px;
        }

        .payment-block td {
            padding: 2px 0;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 55%;">
                @if ($logoPath)
                    <img src="{{ $logoPath }}" alt="{{ $shop['shop_name'] }}" style="height: 36px; max-width: 120px; object-fit: contain;">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="36" height="36">
                        <g fill="#16a34a">
                            <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(0 20 20)" />
                            <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(72 20 20)" />
                            <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(144 20 20)" />
                            <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(216 20 20)" />
                            <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(288 20 20)" />
                        </g>
                        <circle cx="20" cy="20" r="3.5" fill="white" />
                    </svg>
                @endif
                <div class="shop-name">{{ $shop['shop_name'] }}</div>
                @if ($shop['shop_slogan'])
                    <div class="shop-meta">{{ $shop['shop_slogan'] }}</div>
                @endif
                <div class="shop-meta">
                    @if ($shop['shop_address'] || $shop['shop_city'] || $shop['shop_country'])
                        {{ trim(collect([$shop['shop_address'], $shop['shop_city'], $shop['shop_country']])->filter()->join(', ')) }}<br>
                    @endif
                    @if ($shop['shop_phone'])
                        Tél : {{ $shop['shop_phone'] }}<br>
                    @endif
                    @if ($shop['shop_email'])
                        {{ $shop['shop_email'] }}
                    @endif
                </div>
            </td>
            <td style="width: 45%;">
                <div class="invoice-title">Facture</div>
                <div class="invoice-meta">
                    N° {{ $invoice->invoice_number }}<br>
                    Date : {{ $invoice->created_at->translatedFormat('d M Y') }}<br>
                    Commande : {{ $order->order_number }}
                </div>
            </td>
        </tr>
    </table>

    <table class="client-block">
        <tr>
            <td>
                <div class="section-title">Facturé à</div>
                {{ $order->user?->name }}<br>
                @if ($order->user?->email)
                    {{ $order->user->email }}<br>
                @endif
                @if ($order->address)
                    {{ $order->address->address_line1 }}<br>
                    @if ($order->address->address_line2)
                        {{ $order->address->address_line2 }}<br>
                    @endif
                    {{ $order->address->postal_code }} {{ $order->address->city }}<br>
                    {{ $order->address->country }}<br>
                    Tél : {{ $order->address->phone }}
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Produit</th>
                <th class="align-right">Quantité</th>
                <th class="align-right">Prix unitaire</th>
                <th class="align-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="align-right">{{ $item->quantity }}</td>
                    <td class="align-right">{{ $money($item->unit_price) }}</td>
                    <td class="align-right">{{ $money($item->total_price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $totalDiscount = (float) $order->discount + (float) $order->pack_discount + (float) $order->points_discount;
    @endphp

    <table class="totals">
        <tr>
            <td>Sous-total</td>
            <td class="align-right">{{ $money($order->subtotal) }}</td>
        </tr>
        @if ($totalDiscount > 0)
            <tr>
                <td>Remise</td>
                <td class="align-right">-{{ $money($totalDiscount) }}</td>
            </tr>
        @endif
        <tr>
            <td>Livraison</td>
            <td class="align-right">{{ $order->shipping_cost > 0 ? $money($order->shipping_cost) : 'Offerte' }}</td>
        </tr>
        <tr class="total-row">
            <td>Total</td>
            <td class="align-right">{{ $money($order->total) }}</td>
        </tr>
    </table>

    <table class="payment-block">
        <tr>
            <td><strong>Mode de paiement :</strong></td>
            <td>{{ $order->payment_method?->label() }}</td>
        </tr>
        <tr>
            <td><strong>Statut du paiement :</strong></td>
            <td>{{ $order->payment_status->label() }}</td>
        </tr>
    </table>

    <div class="footer">
        Merci pour votre confiance — {{ $shop['shop_name'] }}
    </div>
</body>
</html>
