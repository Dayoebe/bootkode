@props([
    'role',
    'title',
    'description',
    'icon' => 'fas fa-gauge-high',
    'focusLabel' => 'Focus now',
    'focusTitle',
    'focusDescription' => null,
])

<section {{ $attributes->class(['bk-dashboard-hero']) }} aria-labelledby="dashboard-home-title">
    <div class="bk-dashboard-hero-main">
        <div class="min-w-0 max-w-3xl">
            <div class="bk-dashboard-hero-role">
                <span class="bk-dashboard-hero-role-icon" aria-hidden="true"><i class="{{ $icon }}"></i></span>
                <span>{{ $role }}</span>
                <span aria-hidden="true">·</span>
                <span>{{ now()->format('D, j M') }}</span>
            </div>
            <h1 id="dashboard-home-title" class="bk-dashboard-hero-title">{{ $title }}</h1>
            <p class="bk-dashboard-hero-description">{{ $description }}</p>
            @isset($actions)
                <div class="bk-dashboard-hero-actions">{{ $actions }}</div>
            @endisset
        </div>

        <aside class="bk-dashboard-hero-focus" aria-label="{{ $focusLabel }}">
            <p class="bk-dashboard-hero-focus-label">{{ $focusLabel }}</p>
            <p class="bk-dashboard-hero-focus-title">{{ $focusTitle }}</p>
            @if($focusDescription)<p class="bk-dashboard-hero-focus-description">{{ $focusDescription }}</p>@endif
            @isset($focusAction)<div class="mt-4">{{ $focusAction }}</div>@endisset
        </aside>
    </div>

    @isset($highlights)
        <div class="bk-dashboard-hero-highlights">{{ $highlights }}</div>
    @endisset
</section>

@isset($tools)
    <section class="bk-dashboard-toolbar" aria-label="Dashboard filters and tools">
        {{ $tools }}
    </section>
@endisset
