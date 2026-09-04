@props(['name', 'value' => null])

<input id="{{ $name }}" type="hidden" name="{{ $name }}" value="{{ old($name, $value) }}">
<trix-editor input="{{ $name }}"></trix-editor>
