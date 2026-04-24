<div>
    <!-- Desktop Sidebar -->
    <aside
        class="bg-themed-secondary w-64 fixed left-0 top-0 bottom-0 overflow-y-auto overflow-x-hidden transition-transform duration-300 ease-in-out shadow-xl lg:translate-x-0 z-50"
        :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
        x-show="sidebarOpen || window.innerWidth >= 1024"
        wire:ignore.self>

        <!-- Logo/Header -->
        <div class="sticky top-0 bg-themed-secondary z-10 p-4 border-b border-themed-primary shadow-sm transition-colors duration-300">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center space-x-3" aria-label="BootKode Home">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-400 dark:to-purple-500 rounded-lg flex items-center justify-center transition-colors duration-300">
                        <i class="fas fa-code text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-themed-primary transition-colors duration-300">BootKode</span>
                </a>
                <!-- Close button for mobile -->
                <button @click="sidebarOpen = false"
                    class="lg:hidden p-2 rounded-lg hover:bg-themed-tertiary transition-colors"
                    aria-label="Close sidebar">
                    <i class="fas fa-times text-themed-tertiary"></i>
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="p-4">
            <div class="relative">
                <input type="text" 
                    placeholder="Search menu..." 
                    x-data="{ searchTerm: '' }" 
                    x-model="searchTerm"
                    @input="$dispatch('menu-search', { term: searchTerm })"
                    class="w-full p-3 pl-10 rounded-xl bg-themed-tertiary text-themed-primary placeholder-themed-tertiary focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 border border-transparent focus:border-blue-200 dark:focus:border-blue-800"
                    aria-label="Search navigation">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-themed-tertiary"></i>
            </div>
        </div>

        <!-- Menu Items -->
        <nav class="px-4 pb-6 space-y-1 overflow-visible" role="navigation" x-data="{ searchTerm: '' }"
            @menu-search.window="searchTerm = $event.detail.term">
            @foreach ($menuItems as $index => $item)
                <div x-data="{ 
                    expanded: '{{ $activeLink }}' === '{{ $item['link_id'] ?? str()->slug($item['label']) }}',
                    visible: true
                }" 
                x-show="visible"
                x-effect="visible = searchTerm === '' || '{{ strtolower($item['label']) }}'.includes(searchTerm.toLowerCase())"
                class="menu-item">

                    @if(isset($item['children']) && !empty($item['children']))
                        <!-- Parent Menu Item with Children -->
                        <button @click="expanded = !expanded"
                            class="flex items-center justify-between w-full p-3 rounded-xl hover:bg-themed-tertiary transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 group relative"
                            :class="{ 'bg-blue-50 dark:bg-blue-900/20': expanded }" 
                            aria-expanded="expanded">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-themed-tertiary group-hover:bg-accent-themed-primary group-hover:bg-opacity-10 transition-colors duration-300 flex-shrink-0">
                                    <i class="{{ $item['icon'] }} text-themed-secondary text-sm"></i>
                                </div>
                                <span class="font-medium text-themed-primary transition-colors duration-300 truncate">{{ $item['label'] }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-themed-tertiary transition-transform duration-200 text-sm flex-shrink-0 ml-2"
                                :class="{ 'rotate-180': expanded }"></i>
                        </button>

                        <!-- Sub Menu Items -->
                        <ul x-show="expanded" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95" 
                            class="ml-6 space-y-1 mt-2 pb-2 relative z-10 overflow-visible" 
                            role="menu">
                            @foreach ($item['children'] as $child)
                                <li role="menuitem" class="overflow-visible">
                                    <a href="{{ $child['route_name'] === '#' ? '#' : route($child['route_name']) }}"
                                        @click="if (window.innerWidth < 1024) { sidebarOpen = false }"
                                        class="flex items-center space-x-3 p-2.5 rounded-lg transition-all duration-200 group {{ $activeLink === ($child['link_id'] ?? str()->slug($child['label'])) ? 'bg-opacity-20' : 'hover:bg-opacity-10' }}"
                                        :style="{ backgroundColor: '{{ $activeLink === ($child['link_id'] ?? str()->slug($child['label'])) ? 'rgba(var(--accent-primary), 0.2)' : 'transparent' }}' }"
                                        wire:navigate>
                                        <div class="w-6 h-6 flex items-center justify-center flex-shrink-0">
                                            <i class="{{ $child['icon'] }} text-xs {{ $activeLink === ($child['link_id'] ?? str()->slug($child['label'])) ? 'accent-themed-primary' : 'text-themed-tertiary group-hover:accent-themed-primary' }} transition-colors duration-300"></i>
                                        </div>
                                        <span class="flex-1 text-sm font-medium transition-colors duration-300 {{ $activeLink === ($child['link_id'] ?? str()->slug($child['label'])) ? 'accent-themed-primary' : 'text-themed-secondary' }}">{{ $child['label'] }}</span>
                                        @if (($child['badge_count'] ?? 0) > 0)
                                            <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-semibold leading-none text-white">
                                                {{ $child['badge_count'] > 9 ? '9+' : $child['badge_count'] }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <!-- Single Menu Item -->
                        <a href="{{ $item['route_name'] === '#' ? '#' : route($item['route_name']) }}"
                            @click="if (window.innerWidth < 1024) { sidebarOpen = false }"
                            class="flex items-center space-x-3 p-3 rounded-xl hover:bg-themed-tertiary transition-all duration-200 group {{ $activeLink === ($item['link_id'] ?? str()->slug($item['label'])) ? 'bg-accent-themed-primary bg-opacity-20 accent-themed-primary' : 'text-themed-secondary' }}"
                            wire:navigate>
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ $activeLink === ($item['link_id'] ?? str()->slug($item['label'])) ? 'bg-accent-themed-primary bg-opacity-30' : 'bg-themed-tertiary group-hover:bg-accent-themed-primary group-hover:bg-opacity-10' }} transition-colors duration-300 flex-shrink-0">
                                <i class="{{ $item['icon'] }} text-sm {{ $activeLink === ($item['link_id'] ?? str()->slug($item['label'])) ? 'accent-themed-primary' : 'text-themed-secondary' }} transition-colors duration-300"></i>
                            </div>
                            <span class="flex-1 font-medium transition-colors duration-300 truncate">{{ $item['label'] }}</span>
                            @if (($item['badge_count'] ?? 0) > 0)
                                <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-semibold leading-none text-white">
                                    {{ $item['badge_count'] > 9 ? '9+' : $item['badge_count'] }}
                                </span>
                            @endif
                        </a>
                    @endif
                </div>
            @endforeach
        </nav>

        <!-- Sidebar Footer -->
        <div class="sticky bottom-0 bg-themed-secondary border-t border-themed-primary p-4 transition-colors duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                    <img src="{{ Auth::user()?->profile_photo_url ?? asset('images/default-avatar.png') }}"
                        alt="User avatar" 
                        class="w-8 h-8 rounded-full border-2 border-themed-primary transition-colors duration-300 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">
                            {{ Auth::user()?->name ?? 'Guest' }}
                        </p>
                        <p class="text-xs text-themed-secondary truncate transition-colors duration-300">
                            {{ ucfirst(Auth::user()?->getRoleNames()->first() ?? 'User') }}
                        </p>
                    </div>
                </div>
                <x-sidebar-theme-selector />
            </div>
        </div>
    </aside>

    <style>
        /* Remove animation delays for simpler experience */
        .menu-item {
            opacity: 1;
            transform: translateX(0);
        }

        /* Custom scrollbar - theme aware */
        aside::-webkit-scrollbar {
            width: 4px;
        }

        aside::-webkit-scrollbar-track {
            background: transparent;
        }

        aside::-webkit-scrollbar-thumb {
            background: rgba(var(--text-tertiary), 0.3);
            border-radius: 2px;
        }

        aside::-webkit-scrollbar-thumb:hover {
            background: rgba(var(--text-tertiary), 0.5);
        }

        /* Theme-aware placeholder */
        .bg-themed-primary {
            background-color: rgb(var(--bg-primary));
        }
        
        .bg-themed-secondary {
            background-color: rgb(var(--bg-secondary));
        }
        
        .bg-themed-tertiary {
            background-color: rgb(var(--bg-tertiary));
        }
        
        .text-themed-primary {
            color: rgb(var(--text-primary));
        }
        
        .text-themed-secondary {
            color: rgb(var(--text-secondary));
        }
        
        .text-themed-tertiary {
            color: rgb(var(--text-tertiary));
        }
        
        .border-themed-primary {
            border-color: rgb(var(--border-primary));
        }
        
        .border-themed-secondary {
            border-color: rgb(var(--border-secondary));
        }
        
        .accent-themed-primary {
            color: rgb(var(--accent-primary));
        }
        
        .bg-accent-themed-primary {
            background-color: rgb(var(--accent-primary));
        }
        
        .bg-accent-themed-secondary {
            background-color: rgb(var(--accent-secondary));
        }

        .placeholder-themed-tertiary::placeholder {
            color: rgb(var(--text-tertiary));
        }

        /* Focus visible for accessibility */
        button:focus-visible,
        a:focus-visible {
            outline: 2px solid rgb(var(--accent-primary));
            outline-offset: 2px;
        }

        /* Ensure submenu items are always visible and not clipped */
        nav > div {
            overflow: visible;
        }

        .menu-item ul {
            overflow: visible !important;
        }
    </style>
</div>
