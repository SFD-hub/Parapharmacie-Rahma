@props(['order', 'compact' => false])

@if ($order->invoice)
    <div class="flex flex-wrap items-center gap-2 {{ $compact ? 'justify-end text-xs' : 'text-sm' }}">
        <a href="{{ route('admin.invoices.download', $order->invoice) }}" class="font-medium text-primary-600 hover:text-primary-700">
            Télécharger
        </a>
        <a href="{{ route('admin.invoices.print', $order->invoice) }}" target="_blank" class="font-medium text-primary-600 hover:text-primary-700">
            Imprimer
        </a>
        <a href="{{ route('admin.invoices.whatsapp', $order->invoice) }}" target="_blank" class="font-medium text-emerald-600 hover:text-emerald-700">
            WhatsApp
        </a>
    </div>
@else
    <form method="POST" action="{{ route('admin.orders.invoice.store', $order) }}" class="{{ $compact ? 'text-right' : '' }}">
        @csrf
        <button type="submit" class="text-sm font-medium text-primary-600 hover:text-primary-700">
            Générer la facture
        </button>
    </form>
@endif
