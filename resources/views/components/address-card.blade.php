@props(['address'])

<p class="mt-2 text-sm text-gray-600">
    {{ $address->first_name }} {{ $address->last_name }}<br>
    {{ $address->address_line1 }}<br>
    @if ($address->address_line2)
        {{ $address->address_line2 }}<br>
    @endif
    {{ $address->postal_code }} {{ $address->city }}<br>
    {{ $address->country }}
    @if ($address->phone)
        <br>{{ $address->phone }}
    @endif
</p>
