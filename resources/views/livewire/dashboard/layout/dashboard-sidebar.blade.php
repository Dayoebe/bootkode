<div x-data="{
    menuSearch: '',
    menuMatches(text) { return !this.menuSearch.trim() || text.includes(this.menuSearch.trim().toLowerCase()); },
    hasMenuMatches() {
        if (!this.menuSearch.trim()) return true;
        return Array.from(this.$root.querySelectorAll('[data-menu-search]'))
            .some((item) => this.menuMatches(item.dataset.menuSearch));
    },
    resetMenuSearch() { this.menuSearch = ''; }
}" x-on:livewire:navigated.window="resetMenuSearch()" @keydown.escape.window="resetMenuSearch()">
    <aside id="workspace-sidebar" class="bk-sidebar" :data-collapsed="sidebarCollapsed.toString()"
        :class="{ 'is-open': sidebarOpen }" aria-label="Workspace" wire:ignore.self>
        <div class="bk-sidebar-brand">
            <a href="{{ route('dashboard') }}" class="bk-sidebar-logo" aria-label="BootKode dashboard" wire:navigate>
                <span class="bk-sidebar-brand-mark"><img src="{{ asset('img/logo.png') }}" alt="" class="absolute left-1/2 top-1/2 w-24 max-w-none -translate-x-1/2 -translate-y-1/2"></span>
                <span class="sidebar-expanded-content min-w-0">
                    <span class="bk-sidebar-wordmark block font-display text-base font-bold">BootKode</span>
                    <span class="bk-sidebar-tagline block text-[11px] font-semibold">Learn. Build. Grow.</span>
                    <span class="bk-sidebar-role">{{ str($user?->getRoleNames()->first() ?? 'Member')->replace('_', ' ')->title() }} workspace</span>
                </span>
            </a>
            <button id="workspace-sidebar-close" type="button" @click="closeSidebar()" class="bk-sidebar-close lg:hidden" aria-label="Close navigation"><i class="fas fa-xmark" aria-hidden="true"></i></button>
        </div>
        <div class="sidebar-expanded-content bk-sidebar-search-wrap">
            <label class="relative block">
                <span class="sr-only">Search navigation</span>
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                <input type="search" x-ref="menuSearchInput" x-model.debounce.100ms="menuSearch" @search="menuSearch = $event.target.value"
                    class="bk-sidebar-search" placeholder="Find a tool…" aria-label="Search navigation">
            </label>
        </div>
        <p class="sidebar-expanded-content bk-sidebar-section-label">Navigation</p>
        <nav class="bk-sidebar-scroll min-h-0 flex-1 space-y-1 overflow-y-auto overscroll-contain px-3 pb-4" aria-label="Workspace navigation">
            @foreach ($menuItems as $item)
                @php
                    $itemLinkId = $item['link_id'] ?? str($item['label'])->slug()->toString();
                    $hasActiveChild = collect($item['children'] ?? [])->contains(fn ($child) => $activeLink === ($child['link_id'] ?? str($child['label'])->slug()->toString()));
                    $isExpanded = $activeLink === $itemLinkId || $hasActiveChild;
                    $searchText = strtolower($item['label'] . ' ' . collect($item['children'] ?? [])->pluck('label')->implode(' '));
                    $sectionId = 'nav-section-' . $loop->index;
                @endphp
                <div class="menu-item" data-menu-search="{{ $searchText }}" x-data="{ expanded: @js($isExpanded) }" x-show="menuMatches(@js($searchText))">
                    @if (!empty($item['children']))
                        <button type="button" class="bk-nav-link {{ $isExpanded ? 'is-active' : '' }}"
                            @click="if (sidebarCollapsed && window.innerWidth >= 1024) { toggleSidebarCollapse(); expanded = true } else { expanded = !expanded }"
                            :aria-expanded="(expanded || !!menuSearch.trim()).toString()" aria-controls="{{ $sectionId }}" title="{{ $item['label'] }}" aria-label="{{ $item['label'] }}">
                            <i class="{{ $item['icon'] }} bk-nav-icon" aria-hidden="true"></i>
                            <span class="sidebar-expanded-content min-w-0 flex-1 text-left">{{ $item['label'] }}</span>
                            <i class="sidebar-expanded-content fas fa-chevron-down text-[10px]" :class="{ 'rotate-180': expanded }" aria-hidden="true"></i>
                        </button>
                        <ul id="{{ $sectionId }}" x-cloak x-show="(expanded || menuSearch.trim()) && (!sidebarCollapsed || window.innerWidth < 1024)" x-collapse class="bk-nav-children space-y-1">
                            @foreach ($item['children'] as $child)
                                @php
                                    $childLinkId = $child['link_id'] ?? str($child['label'])->slug()->toString();
                                    $isActiveChild = $activeLink === $childLinkId;
                                @endphp
                                <li><a href="{{ $child['route_name'] === '#' ? '#' : route($child['route_name']) }}"
                                    @click="resetMenuSearch(); if (window.innerWidth < 1024) closeSidebar(false)" class="bk-nav-link bk-nav-child-link {{ $isActiveChild ? 'is-active' : '' }}"
                                    @if($isActiveChild) aria-current="page" @endif wire:navigate>
                                    <span class="min-w-0 flex-1">{{ $child['label'] }}</span>
                                    @if(($child['badge_count'] ?? 0) > 0)<span class="bk-count">{{ min(99, $child['badge_count']) }}</span>@endif
                                </a></li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $item['route_name'] === '#' ? '#' : route($item['route_name']) }}"
                            @click="resetMenuSearch(); if (window.innerWidth < 1024) closeSidebar(false)" class="bk-nav-link {{ $activeLink === $itemLinkId ? 'is-active' : '' }}"
                            title="{{ $item['label'] }}" aria-label="{{ $item['label'] }}" @if($activeLink === $itemLinkId) aria-current="page" @endif wire:navigate>
                            <i class="{{ $item['icon'] }} bk-nav-icon" aria-hidden="true"></i>
                            <span class="sidebar-expanded-content min-w-0 flex-1">{{ $item['label'] }}</span>
                            @if(($item['badge_count'] ?? 0) > 0)<span class="sidebar-expanded-content bk-count">{{ min(99, $item['badge_count']) }}</span>@endif
                        </a>
                    @endif
                </div>
            @endforeach
            <p x-show="menuSearch.trim() && !hasMenuMatches()" x-cloak class="bk-sidebar-empty" role="status">No tools match your search.</p>
        </nav>
        <div class="bk-sidebar-footer">
            <a href="{{ route('help.support') }}" class="bk-nav-link" aria-label="Help and support" title="Help and support" wire:navigate>
                <i class="fas fa-circle-question bk-nav-icon" aria-hidden="true"></i><span class="sidebar-expanded-content">Help & support</span>
            </a>
            <button type="button" @click="toggleSidebarCollapse()" class="bk-nav-link hidden w-full lg:flex" :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'" :aria-expanded="(!sidebarCollapsed).toString()" aria-controls="workspace-sidebar" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <i class="fas bk-nav-icon" :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'" aria-hidden="true"></i><span class="sidebar-expanded-content">Collapse navigation</span>
            </button>
        </div>
    </aside>
</div>
