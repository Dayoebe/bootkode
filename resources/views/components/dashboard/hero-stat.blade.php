@props(['label', 'value', 'context' => null, 'icon' => 'fas fa-circle'])

<div class="bk-dashboard-hero-stat">
    <span class="bk-dashboard-hero-stat-icon" aria-hidden="true"><i class="{{ $icon }}"></i></span>
    <span class="min-w-0">
        <span class="bk-dashboard-hero-stat-label">{{ $label }}</span>
        <span class="bk-dashboard-hero-stat-value">{{ $value }}</span>
        @if($context)<span class="bk-dashboard-hero-stat-context">{{ $context }}</span>@endif
    </span>
</div>
