@props(['brand'])

<a href="{{ route('shop.index', ['brands' => [$brand->slug]]) }}" {{ $attributes->class(['group flex shrink-0 items-center justify-center p-6 transition duration-300']) }}>
    @if ($brand->logo)
        <img
            src="{{ $brand->logo }}"
            alt="{{ $brand->name }}"
            class="max-h-full max-w-full object-contain grayscale transition duration-300 group-hover:scale-105 group-hover:grayscale-0"
        >
    @else
        <span class="text-lg font-bold uppercase tracking-wide text-gray-400 transition group-hover:text-primary-600">
            {{ $brand->name }}
        </span>
    @endif
</a>
