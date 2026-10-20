@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-content']) }}>
    {{ $value ?? $slot }}
</label>
