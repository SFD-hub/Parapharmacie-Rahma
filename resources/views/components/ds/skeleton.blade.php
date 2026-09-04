@props(['rounded' => 'rounded-md'])

<div {{ $attributes->merge(['class' => "animate-pulse bg-gray-200 {$rounded}"]) }}></div>
