<div x-data="{
    menuSearch: '',
    menuMatches(text) { return !this.menuSearch.trim() || text.includes(this.menuSearch.trim().toLowerCase()); },
    resetMenuSearch() { this.menuSearch = ''; }
}" x-on:livewire:navigated.window="resetMenuSearch()" @keydown.escape.window="resetMenuSearch()">
    <aside id="workspace-sidebar" class="bk-sidebar" :data-collapsed="sidebarCollapsed.toString()"
        :class="{ 'is-open': sidebarOpen }" aria-label="Workspace" wire:ignore.self>
        <div class="bk-sidebar-brand">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3" aria-label="BootKode dashboard" wire:navigate>
                <span class="relative h-10 w-10 shrink-0 overflow-hidden"><img src="{{ asset('img/logo.png') }}" alt="" class="absolute left-1/2 top-1/2 w-24 max-w-none -translate-x-1/2 -translate-y-1/2"></span>
                <span class="sidebar-expanded-content min-w-0">
                    <span class="block font-display text-base font-bold text-content">BootKode</span>
                    <span class="block text-xs text-content-muted">Learn. Build. Grow.</span>
                </span>
            </a>
            <button type="button" @click="sidebarOpen = false" class="bk-icon-button lg:hidden" aria-label="Close navigation"><i class="fas fa-xmark" aria-hidden="true"></i></button>
        </div>
        <div class="sidebar-expanded-content px-4 pb-3">
            <label class="relative block">
                <span class="sr-only">Search navigation</span>
                <i class="fas fa-search pointer-events-none absolute left-3 top-4 text-xs text-content-muted" aria-hidden="true"></i>
                <input type="search" x-ref="menuSearchInput" x-model.debounce.100ms="menuSearch" @search="menuSearch = $event.target.value"
                    class="bk-input pl-9 text-sm" placeholder="Find a tool…" aria-label="Search navigation">
            </label>
        </div>
        <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-3 pb-4" aria-label="Workspace navigation">
            @foreach ($menuItems as $item)
                @php
                    $itemLinkId = $item['link_id'] ?? str($item['label'])->slug()->toString();
                    $hasActiveChild = collect($item['children'] ?? [])->contains(fn ($child) => $activeLink === ($child['link_id'] ?? str($child['label'])->slug()->toString()));
                    $isExpanded = $activeLink === $itemLinkId || $hasActiveChild;
                    $searchText = strtolower($item['label'] . ' ' . collect($item['children'] ?? [])->pluck('label')->implode(' '));
                    $sectionId = 'nav-section-' . $loop->index;
                @endphp
                <div class="menu-item" x-data="{ expanded: @js($isExpanded) }" x-show="menuMatches(@js($searchText))">
                    @if (!empty($item['children']))
                        <button type="button" class="bk-nav-link {{ $isExpanded ? 'is-active' : '' }}"
                            @click="if (sidebarCollapsed && window.innerWidth >= 1024) { toggleSidebarCollapse(); expanded = true } else { expanded = !expanded }"
                            :aria-expanded="(expanded || !!menuSearch.trim()).toString()" aria-controls="{{ $sectionId }}" title="{{ $item['label'] }}" aria-label="{{ $item['label'] }}">
                            <i class="{{ $item['icon'] }} bk-nav-icon" aria-hidden="true"></i>
                            <span class="sidebar-expanded-content min-w-0 flex-1 text-left">{{ $item['label'] }}</span>
                            <i class="sidebar-expanded-content fas fa-chevron-down text-[10px]" :class="{ 'rotate-180': expanded }" aria-hidden="true"></i>
                        </button>
                        <ul id="{{ $sectionId }}" x-show="(expanded || menuSearch.trim()) && (!sidebarCollapsed || window.innerWidth < 1024)" x-collapse class="ml-5 mt-1 space-y-1 border-l border-line pl-3">
                            @foreach ($item['children'] as $child)
                                @php
                                    $childLinkId = $child['link_id'] ?? str($child['label'])->slug()->toString();
                                    $isActiveChild = $activeLink === $childLinkId;
                                @endphp
                                <li><a href="{{ $child['route_name'] === '#' ? '#' : route($child['route_name']) }}"
                                    @click="resetMenuSearch(); sidebarOpen = false" class="bk-nav-link {{ $isActiveChild ? 'is-active' : '' }}"
                                    @if($isActiveChild) aria-current="page" @endif wire:navigate>
                                    <span class="min-w-0 flex-1">{{ $child['label'] }}</span>
                                    @if(($child['badge_count'] ?? 0) > 0)<span class="bk-count">{{ min(99, $child['badge_count']) }}</span>@endif
                                </a></li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $item['route_name'] === '#' ? '#' : route($item['route_name']) }}"
                            @click="resetMenuSearch(); sidebarOpen = false" class="bk-nav-link {{ $activeLink === $itemLinkId ? 'is-active' : '' }}"
                            title="{{ $item['label'] }}" aria-label="{{ $item['label'] }}" @if($activeLink === $itemLinkId) aria-current="page" @endif wire:navigate>
                            <i class="{{ $item['icon'] }} bk-nav-icon" aria-hidden="true"></i>
                            <span class="sidebar-expanded-content min-w-0 flex-1">{{ $item['label'] }}</span>
                            @if(($item['badge_count'] ?? 0) > 0)<span class="sidebar-expanded-content bk-count">{{ min(99, $item['badge_count']) }}</span>@endif
                        </a>
                    @endif
                </div>
            @endforeach
            <p x-show="menuSearch.trim() && !Array.from($el.querySelectorAll('.menu-item')).some(item => item.style.display !== 'none')" x-cloak class="px-3 py-4 text-sm text-content-muted" role="status">No tools match your search.</p>
        </nav>
        <div class="shrink-0 border-t border-line p-3">
            <a href="{{ route('help.support') }}" class="bk-nav-link" aria-label="Help and support" title="Help and support" wire:navigate>
                <i class="fas fa-circle-question bk-nav-icon" aria-hidden="true"></i><span class="sidebar-expanded-content">Help & support</span>
            </a>
            <button type="button" @click="toggleSidebarCollapse()" class="bk-nav-link hidden w-full lg:flex" :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'" :aria-expanded="(!sidebarCollapsed).toString()" aria-controls="workspace-sidebar" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <i class="fas bk-nav-icon" :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'" aria-hidden="true"></i><span class="sidebar-expanded-content">Collapse navigation</span>
            </button>
        </div>
    </aside>
</div>
