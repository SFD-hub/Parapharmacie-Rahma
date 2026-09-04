@props(['order'])

@if ($order->status === \App\Enums\OrderStatus::Pending)
    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="{{ \App\Enums\OrderStatus::Confirmed->value }}">
        <x-ds.button type="submit" size="sm" class="!bg-emerald-600 hover:!bg-emerald-700">
            Valider
        </x-ds.button>
    </form>
@endif
