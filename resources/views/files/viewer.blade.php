<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <title>{{ $preview->name }} — {{ config('app.name', 'BootKode') }}</title>
    @vite(['resources/css/app.css', 'resources/js/file-viewer.js'])
</head>
<body class="h-full min-w-[320px] overflow-hidden bg-slate-100 font-sans text-content antialiased">
    <a class="bk-skip-link" href="#file-viewer-stage">Skip to document</a>
    <div id="bootkode-file-viewer" class="bk-file-viewer" data-kind="{{ $preview->kind }}">
        <header class="bk-file-viewer-header">
            <div class="flex min-w-0 flex-1 items-center gap-3">
                @if($metadata['embedded'])
                    <button type="button" data-viewer-action="close" class="bk-file-viewer-icon" aria-label="Close preview">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                @else
                    <a href="{{ $metadata['returnUrl'] }}" class="bk-file-viewer-icon" aria-label="Return to previous page">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </a>
                @endif
                <div class="min-w-0">
                    <h1 class="truncate font-display text-sm font-semibold text-white sm:text-base">{{ $preview->name }}</h1>
                    <p class="truncate text-xs text-slate-300">
                        {{ strtoupper($preview->extension ?: 'File') }} · {{ \Illuminate\Support\Number::fileSize($preview->size, precision: 1) }}
                    </p>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <button type="button" data-viewer-action="fullscreen" class="bk-file-viewer-icon" aria-label="Enter full screen">
                    <i class="fas fa-expand" aria-hidden="true"></i>
                </button>
                <a href="{{ $metadata['downloadUrl'] }}" class="bk-file-viewer-icon" aria-label="Download original file">
                    <i class="fas fa-download" aria-hidden="true"></i>
                </a>
            </div>
        </header>

        @if($preview->previewable)
            <div class="bk-file-viewer-toolbar" aria-label="Document controls">
                <div class="bk-file-viewer-toolbar-group" data-control="pagination" hidden>
                    <button type="button" data-viewer-action="previous" class="bk-file-viewer-control" aria-label="Previous page or slide"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
                    <label class="flex items-center gap-2 text-sm" for="viewer-page">
                        <span class="sr-only">Page or slide</span>
                        <input id="viewer-page" data-viewer-page type="number" min="1" value="1" class="bk-file-viewer-page-input">
                        <span aria-hidden="true">/</span><span data-viewer-page-count>1</span>
                    </label>
                    <button type="button" data-viewer-action="next" class="bk-file-viewer-control" aria-label="Next page or slide"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                </div>
                <div class="bk-file-viewer-toolbar-group" data-control="zoom" hidden>
                    <button type="button" data-viewer-action="zoom-out" class="bk-file-viewer-control" aria-label="Zoom out"><i class="fas fa-minus" aria-hidden="true"></i></button>
                    <span data-viewer-zoom class="min-w-12 text-center text-xs font-semibold">100%</span>
                    <button type="button" data-viewer-action="zoom-in" class="bk-file-viewer-control" aria-label="Zoom in"><i class="fas fa-plus" aria-hidden="true"></i></button>
                    <button type="button" data-viewer-action="fit-width" class="bk-file-viewer-control px-3 text-xs font-semibold">Fit width</button>
                    <button type="button" data-viewer-action="fit-page" class="bk-file-viewer-control px-3 text-xs font-semibold">Fit page</button>
                </div>
                <form data-viewer-search class="bk-file-viewer-search" role="search" hidden>
                    <input id="viewer-search-input" type="search" maxlength="120" placeholder="Search document" aria-label="Search document" class="bk-file-viewer-search-input">
                    <button class="bk-file-viewer-control px-3 text-xs font-semibold" type="submit">Find</button>
                </form>
            </div>
        @endif

        <main id="file-viewer-stage" tabindex="-1" class="bk-file-viewer-stage">
            @if($preview->previewable)
                <div data-viewer-loading class="bk-file-viewer-state" role="status">
                    <span class="bk-file-viewer-spinner" aria-hidden="true"></span>
                    <p>Preparing {{ strtolower($metadata['extension']) }} preview…</p>
                </div>
                <div data-viewer-error class="bk-file-viewer-state" role="alert" hidden>
                    <i class="fas fa-triangle-exclamation text-2xl text-amber-600" aria-hidden="true"></i>
                    <h2 class="font-display text-lg font-semibold">Preview unavailable</h2>
                    <p data-viewer-error-message class="max-w-xl text-sm text-content-muted">The preview could not be loaded.</p>
                    <a href="{{ $metadata['downloadUrl'] }}" class="bk-button bk-button-primary">Download original</a>
                </div>
                <div data-viewer-surface class="bk-file-viewer-surface" hidden></div>
                <p data-viewer-status class="sr-only" role="status" aria-live="polite"></p>
            @else
                <section class="bk-file-viewer-state" role="status">
                    <i class="fas fa-file-circle-exclamation text-3xl text-amber-600" aria-hidden="true"></i>
                    <h2 class="font-display text-xl font-semibold">This format needs the original file</h2>
                    <p class="max-w-xl text-sm leading-6 text-content-muted">{{ $preview->unavailableReason }}</p>
                    <a href="{{ $metadata['downloadUrl'] }}" class="bk-button bk-button-primary">
                        <i class="fas fa-download" aria-hidden="true"></i> Download original
                    </a>
                </section>
            @endif
        </main>
    </div>
    <script id="file-viewer-metadata" type="application/json">{!! json_encode($metadata, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) !!}</script>
</body>
</html>
