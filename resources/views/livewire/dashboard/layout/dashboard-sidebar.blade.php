<div
    x-data="{
        menuSearch: '',
        menuMatches(text) {
            const term = this.menuSearch.trim().toLowerCase();

            return term === '' || text.includes(term);
        },
        resetMenuSearch() {
            this.menuSearch = '';
        }
    }"
    x-on:livewire:navigated.window="resetMenuSearch()"
    x-on:keydown.escape.window="resetMenuSearch()"
>
    @php
        $user = Auth::user();
        $userInitials = collect(explode(' ', $user?->name ?? 'User'))
            ->filter()
            ->map(fn ($part) => substr($part, 0, 1))
            ->take(2)
            ->implode('') ?: 'U';
    @endphp

    <aside
        class="fixed bottom-0 left-0 top-0 z-50 flex w-72 flex-col overflow-hidden border-r border-white/10 bg-slate-950 text-white shadow-2xl shadow-slate-950/30 transition-transform duration-300 ease-out lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        wire:ignore.self
    >
        <x-icon-field class="opacity-[0.08]" />

        <div class="relative border-b border-white/10 px-5 py-5">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3" aria-label="BootKode Home">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[8px] bg-white text-slate-950 shadow-lg shadow-black/20">
                        <i class="fas fa-code text-sm"></i>
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-lg font-black">BootKode</span>
                        <span class="block truncate text-[11px] font-extrabold uppercase text-teal-200">Academy</span>
                    </span>
                </a>
                <button
                    @click="sidebarOpen = false"
                    class="grid h-10 w-10 place-items-center rounded-[8px] text-slate-300 transition hover:bg-white/10 hover:text-white lg:hidden"
                    aria-label="Close sidebar"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mt-5 rounded-[8px] border border-white/10 bg-white/[0.06] p-3">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-[8px] border border-white/15 bg-teal-500 text-sm font-black text-white">
                        {{ $userInitials }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-black">{{ $user?->name ?? 'Guest' }}</p>
                        <p class="truncate text-xs font-semibold text-slate-400">{{ ucfirst($user?->getRoleNames()->first() ?? 'User') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative px-4 py-4">
            <label class="relative block">
                <span class="sr-only">Search menu</span>
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500"></i>
                <input
                    x-ref="menuSearchInput"
                    x-model.debounce.100ms="menuSearch"
                    @search="menuSearch = $event.target.value"
                    type="search"
                    placeholder="Search menu"
                    class="h-11 w-full rounded-[8px] border border-white/10 bg-white/[0.06] pl-9 pr-10 text-sm font-semibold text-white placeholder:text-slate-500 outline-none transition focus:border-teal-300 focus:bg-white/[0.09] focus:ring-4 focus:ring-teal-300/10"
                    aria-label="Search navigation"
                >
                <button
                    type="button"
                    x-show="menuSearch.trim() !== ''"
                    x-cloak
                    @click="resetMenuSearch(); $refs.menuSearchInput.focus()"
                    class="absolute right-2 top-1/2 grid h-7 w-7 -translate-y-1/2 place-items-center rounded-[8px] text-slate-400 transition hover:bg-white/10 hover:text-white"
                    aria-label="Clear menu search"
                >
                    <i class="fas fa-times text-xs"></i>
                </button>
            </label>
        </div>

        <nav
            class="flex-1 space-y-1 overflow-y-auto px-4 pb-5"
            role="navigation"
        >
            @foreach ($menuItems as $item)
                @php
                    $itemLinkId = $item['link_id'] ?? str($item['label'])->slug()->toString();
                    $hasActiveChild = collect($item['children'] ?? [])->contains(fn ($child) => $activeLink === ($child['link_id'] ?? str($child['label'])->slug()->toString()));
                    $isExpanded = $activeLink === $itemLinkId || $hasActiveChild;
                    $searchText = strtolower($item['label'] . ' ' . collect($item['children'] ?? [])->pluck('label')->implode(' '));
                @endphp

                <div
                    x-data="{ expanded: @js($isExpanded) }"
                    x-show="menuMatches(@js($searchText))"
                    x-cloak
                    class="menu-item"
                >
                    @if(isset($item['children']) && !empty($item['children']))
                        <button
                            @click="expanded = !expanded"
                            class="group flex w-full items-center justify-between gap-3 rounded-[8px] px-3 py-2.5 text-left transition {{ $isExpanded ? 'bg-white text-slate-950 shadow-lg shadow-black/10' : 'text-slate-300 hover:bg-white/[0.08] hover:text-white' }}"
                            :aria-expanded="expanded.toString()"
                        >
                            <span class="flex min-w-0 items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[8px] {{ $isExpanded ? 'bg-slate-950 text-white' : 'bg-white/[0.08] text-teal-200 group-hover:bg-white/[0.12]' }}">
                                    <i class="{{ $item['icon'] }} text-sm"></i>
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-black">{{ $item['label'] }}</span>
                                    <span class="{{ $isExpanded ? 'text-slate-500' : 'text-slate-500 group-hover:text-slate-400' }} block text-[11px] font-semibold">
                                        {{ count($item['children']) }} tools
                                    </span>
                                </span>
                            </span>
                            <i class="fas fa-chevron-down shrink-0 text-[11px] transition-transform" :class="{ 'rotate-180': expanded }"></i>
                        </button>

                        <ul
                            x-show="expanded"
                            x-collapse
                            class="ml-4 mt-1 space-y-1 border-l border-white/10 pl-3"
                            role="menu"
                        >
                            @foreach ($item['children'] as $child)
                                @php
                                    $childLinkId = $child['link_id'] ?? str($child['label'])->slug()->toString();
                                    $isActiveChild = $activeLink === $childLinkId;
                                @endphp
                                <li role="menuitem">
                                    <a
                                        href="{{ $child['route_name'] === '#' ? '#' : route($child['route_name']) }}"
                                        @click="resetMenuSearch(); if (window.innerWidth < 1024) { sidebarOpen = false }"
                                        class="group flex items-center gap-3 rounded-[8px] px-3 py-2.5 transition {{ $isActiveChild ? 'bg-teal-300 text-slate-950 shadow-sm' : 'text-slate-400 hover:bg-white/[0.08] hover:text-white' }}"
                                        wire:navigate
                                    >
                                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-[8px] {{ $isActiveChild ? 'bg-slate-950/10 text-slate-950' : 'bg-white/[0.06] text-slate-500 group-hover:text-teal-200' }}">
                                            <i class="{{ $child['icon'] }} text-xs"></i>
                                        </span>
                                        <span class="min-w-0 flex-1 truncate text-sm font-bold">{{ $child['label'] }}</span>
                                        @if (($child['badge_count'] ?? 0) > 0)
                                            <span class="rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-black leading-none text-white">
                                                {{ $child['badge_count'] > 9 ? '9+' : $child['badge_count'] }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        @php
                            $isActiveItem = $activeLink === $itemLinkId;
                        @endphp
                        <a
                            href="{{ $item['route_name'] === '#' ? '#' : route($item['route_name']) }}"
                            @click="resetMenuSearch(); if (window.innerWidth < 1024) { sidebarOpen = false }"
                            class="group flex items-center gap-3 rounded-[8px] px-3 py-2.5 transition {{ $isActiveItem ? 'bg-white text-slate-950 shadow-lg shadow-black/10' : 'text-slate-300 hover:bg-white/[0.08] hover:text-white' }}"
                            wire:navigate
                        >
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[8px] {{ $isActiveItem ? 'bg-slate-950 text-white' : 'bg-white/[0.08] text-teal-200 group-hover:bg-white/[0.12]' }}">
                                <i class="{{ $item['icon'] }} text-sm"></i>
                            </span>
                            <span class="min-w-0 flex-1 truncate text-sm font-black">{{ $item['label'] }}</span>
                            @if (($item['badge_count'] ?? 0) > 0)
                                <span class="rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-black leading-none text-white">
                                    {{ $item['badge_count'] > 9 ? '9+' : $item['badge_count'] }}
                                </span>
                            @endif
                        </a>
                    @endif
                </div>
            @endforeach
        </nav>

        <div class="relative border-t border-white/10 p-4">
            <div class="flex items-center justify-between gap-3 rounded-[8px] bg-white/[0.06] p-3">
                <div class="min-w-0">
                    <p class="text-xs font-black uppercase text-slate-500">Theme</p>
                    <p class="text-sm font-bold text-slate-200">Workspace tone</p>
                </div>
                <x-sidebar-theme-selector />
            </div>
        </div>
    </aside>

    <style>
        aside::-webkit-scrollbar {
            width: 4px;
        }

        aside::-webkit-scrollbar-track {
            background: transparent;
        }

        aside::-webkit-scrollbar-thumb {
            background: rgb(148 163 184 / 0.35);
            border-radius: 999px;
        }

        aside::-webkit-scrollbar-thumb:hover {
            background: rgb(148 163 184 / 0.55);
        }

        button:focus-visible,
        a:focus-visible {
            outline: 2px solid rgb(94 234 212);
            outline-offset: 2px;
        }
    </style>
</div>
