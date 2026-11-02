@props(['title', 'description' => null, 'eyebrow' => null])

<header {{ $attributes->class(['bk-page-header mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between']) }}>
    <div class="min-w-0 max-w-3xl">
        @if($eyebrow)<p class="bk-eyebrow mb-2">{{ $eyebrow }}</p>@endif
        <h1 class="font-display text-2xl font-bold tracking-tight text-content sm:text-3xl">{{ $title }}</h1>
        @if($description)<p class="mt-3 text-content-muted">{{ $description }}</p>@endif
    </div>
    @isset($actions)<div class="bk-page-header-actions flex shrink-0 flex-wrap gap-2">{{ $actions }}</div>@endisset
</header>
