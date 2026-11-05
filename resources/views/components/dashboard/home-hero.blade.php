@props([
    'role',
    'title',
    'description',
    'icon' => 'fas fa-gauge-high',
    'focusLabel' => 'Focus now',
    'focusTitle',
    'focusDescription' => null,
    'focusIcon' => 'fas fa-bolt',
])

<section {{ $attributes->class(['bk-dashboard-hero']) }} aria-labelledby="dashboard-home-title" data-dashboard-visual="modern">
    <div class="bk-dashboard-hero-decoration" aria-hidden="true">
        <span></span><span></span><span></span>
    </div>

    <div class="bk-dashboard-live" data-dashboard-live-status>
        <span class="bk-dashboard-live-presence" aria-hidden="true"></span>
        <strong>Live workspace</strong>
        <span class="bk-dashboard-live-divider" aria-hidden="true"></span>
        <span><i class="fas fa-clock" aria-hidden="true"></i> <span data-dashboard-live-time>{{ now('Africa/Lagos')->format('h:i A') }}</span> WAT</span>
        <span class="bk-dashboard-live-divider" aria-hidden="true"></span>
        <span data-dashboard-network-status role="status" aria-live="polite"><i class="fas fa-wifi" aria-hidden="true"></i> Checking connection</span>
        <span class="bk-dashboard-live-private"><i class="fas fa-lock" aria-hidden="true"></i> Private account view</span>
    </div>

    <div class="bk-dashboard-hero-main">
        <div class="min-w-0 max-w-3xl">
            <div class="bk-dashboard-hero-role">
                <span class="bk-dashboard-hero-role-icon" aria-hidden="true"><i class="{{ $icon }}"></i></span>
                <span>{{ $role }}</span>
                <span aria-hidden="true">·</span>
                <span>Learn. Build. Grow.</span>
            </div>
            <h1 id="dashboard-home-title" class="bk-dashboard-hero-title">{{ $title }}</h1>
            <p class="bk-dashboard-hero-description">{{ $description }}</p>
            @isset($actions)
                <div class="bk-dashboard-hero-actions">{{ $actions }}</div>
            @endisset
        </div>

        <aside class="bk-dashboard-hero-focus" aria-label="{{ $focusLabel }}">
            <div class="bk-dashboard-hero-focus-heading">
                <span aria-hidden="true"><i class="{{ $focusIcon }}"></i></span>
                <p class="bk-dashboard-hero-focus-label">{{ $focusLabel }}</p>
            </div>
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
