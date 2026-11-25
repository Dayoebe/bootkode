@props(['field', 'label' => null])

<button
    type="button"
    wire:click="openAiAssist(@js($field))"
    {{ $attributes->class(['inline-flex min-h-10 items-center gap-1.5 rounded-lg px-2.5 py-2 text-xs font-semibold text-brand hover:bg-brand/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand/40 disabled:cursor-not-allowed disabled:opacity-60']) }}
    aria-label="{{ $label ?: 'Use AI Assist for '.str($field)->headline() }}"
    wire:loading.attr="disabled"
    wire:target="openAiAssist">
    <i class="fas fa-wand-magic-sparkles" aria-hidden="true"></i>
    <span>{{ $label ?: 'AI Assist' }}</span>
</button>
