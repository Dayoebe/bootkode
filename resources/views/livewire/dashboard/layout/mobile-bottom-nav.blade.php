@php
    $user = auth()->user();
    $items = app(\App\View\Components\MobileBottomNav::class)->items($user);
@endphp
<nav class="bk-bottom-nav lg:hidden" aria-label="Mobile dashboard navigation">
    @foreach($items as $item)
        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs(...$item['active']) ? 'is-active' : '' }}"
            @if(request()->routeIs(...$item['active'])) aria-current="page" @endif wire:navigate>
            <i class="{{ $item['icon'] }}" aria-hidden="true"></i><span>{{ $item['label'] }}</span>
        </a>
    @endforeach
    <button type="button" @click="toggleSidebar()" :aria-expanded="sidebarOpen.toString()" aria-controls="dashboard-navigation">
        <i class="fas fa-bars" aria-hidden="true"></i><span>Menu</span>
    </button>
</nav>
