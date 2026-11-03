@props(['title'])

<div role="status" aria-live="polite" {{ $attributes->class(['bk-panel p-6']) }}>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-content">{{ $title }} is temporarily unavailable</h2>
            <p class="mt-1 text-sm text-content-muted">The rest of your workspace is still available. No learning data was changed.</p>
        </div>
        <x-secondary-button wire:click="$refresh" wire:loading.attr="disabled">
            Try again
        </x-secondary-button>
    </div>
</div>
