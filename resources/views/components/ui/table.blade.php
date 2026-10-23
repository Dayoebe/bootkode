@props(['label' => 'Data table'])
<div {{ $attributes->class(['bk-table-region']) }} role="region" aria-label="{{ $label }}" tabindex="0">
    {{ $slot }}
</div>
