@props([
    'source',
    'record',
    'file' => '0',
    'label' => 'View file',
    'mode' => 'page',
    'download' => true,
])

@php
    $parameters = [(string) $source, (string) $record, (string) $file];
    $viewerUrl = route('files.view', $parameters + ['return' => request()->getRequestUri(), 'embed' => $mode === 'overlay' ? 1 : 0]);
@endphp

<span {{ $attributes->class(['inline-flex flex-wrap items-center gap-2']) }}>
    <a href="{{ $viewerUrl }}"
        @if($mode === 'overlay') data-file-preview-overlay @endif
        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-action px-4 py-2 text-sm font-semibold text-action-foreground shadow-sm transition hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-action focus-visible:ring-offset-2">
        <i class="fas fa-eye" aria-hidden="true"></i>
        <span>{{ $label }}</span>
    </a>
    @if($download)
        <a href="{{ route('files.download', $parameters) }}"
            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-line bg-surface px-4 py-2 text-sm font-semibold text-content transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-action focus-visible:ring-offset-2">
            <i class="fas fa-download" aria-hidden="true"></i>
            <span>Download</span>
        </a>
    @endif
</span>
