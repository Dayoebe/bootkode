<div>
    <!-- Desktop Sidebar -->
    <aside
        class="bg-white dark:bg-gray-800 h-screen w-64 fixed left-0 top-0 overflow-y-auto transition-all duration-300 ease-in-out shadow-xl lg:translate-x-0 z-50"
        :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
        x-show="sidebarOpen || window.innerWidth >= 1024"
        x-transition:enter="transition-transform ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform ease-in duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        wire:ignore.self>

        <!-- Logo/Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 z-10 p-4 border-b dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3" aria-label="BootKode Home">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-code text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">BootKode</span>
                </a>
                <!-- Close button for mobile -->
                <button 
                    @click="sidebarOpen = false"
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    aria-label="Close sidebar">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="p-4">
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="Search menu..."
                    x-data="{ searchTerm: '' }"
                    x-model="searchTerm"
                    @input="$dispatch('menu-search', { term: searchTerm })"
                    class="w-full p-3 pl-10 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 border border-transparent focus:border-blue-200"
                    aria-label="Search navigation">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Menu Items -->
        <nav class="px-4 pb-4 space-y-1" role="navigation" x-data="{ searchTerm: '' }" @menu-search.window="searchTerm = $event.detail.term">
            @foreach ($menuItems as $index => $item)
                <div 
                    x-data="{ 
                        expanded: '{{ $activeLink }}' === '{{ $item['link_id'] ?? str()->slug($item['label']) }}',
                        visible: true
                    }"
                    x-show="visible"
                    x-effect="visible = searchTerm === '' || '{{ strtolower($item['label']) }}'.includes(searchTerm.toLowerCase())"
                    class="menu-item"
                    style="animation-delay: {{ $index * 0.1 }}s;">
                    
                    @if(isset($item['children']) && !empty($item['children']))
                        <!-- Parent Menu Item with Children -->
                        <button 
                            @click="expanded = !expanded"
                            class="flex items-center justify-between w-full p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 group"
                            :class="{ 'bg-blue-50 dark:bg-blue-900/20': expanded }"
                            aria-expanded="expanded">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                    <i class="{{ $item['icon'] }} text-gray-600 dark:text-gray-300 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $item['label'] }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 text-sm"
                                :class="{ 'rotate-180': expanded }"></i>
                        </button>

                        <!-- Sub Menu Items -->
                        <ul 
                            x-show="expanded" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="ml-6 space-y-1 mt-2" 
                            role="menu">
                            @foreach ($item['children'] as $child)
                                <li role="menuitem">
                                    <a href="{{ $child['route_name'] === '#' ? '#' : route($child['route_name']) }}"
                                        class="flex items-center space-x-3 p-2.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 group {{ $activeLink === ($child['link_id'] ?? str()->slug($child['label'])) ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}"
                                        wire:navigate>
                                        <div class="w-6 h-6 flex items-center justify-center">
                                            <i class="{{ $child['icon'] }} text-xs {{ $activeLink === ($child['link_id'] ?? str()->slug($child['label'])) ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 group-hover:text-blue-600' }}"></i>
                                        </div>
                                        <span class="text-sm font-medium">{{ $child['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <!-- Single Menu Item -->
                        <a href="{{ $item['route_name'] === '#' ? '#' : route($item['route_name']) }}"
                            class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 group {{ $activeLink === ($item['link_id'] ?? str()->slug($item['label'])) ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}"
                            wire:navigate>
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ $activeLink === ($item['link_id'] ?? str()->slug($item['label'])) ? 'bg-blue-200 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30' }} transition-colors">
                                <i class="{{ $item['icon'] }} text-sm {{ $activeLink === ($item['link_id'] ?? str()->slug($item['label'])) ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' }}"></i>
                            </div>
                            <span class="font-medium">{{ $item['label'] }}</span>
                        </a>
                    @endif
                </div>
            @endforeach
        </nav>

        <!-- Sidebar Footer -->
        <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img 
                        src="{{ Auth::user()?->profile_photo_url ?? asset('images/default-avatar.png') }}" 
                        alt="User avatar" 
                        class="w-8 h-8 rounded-full border-2 border-gray-200 dark:border-gray-600">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ Auth::user()?->name ?? 'Guest' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ ucfirst(Auth::user()?->getRoleNames()->first() ?? 'User') }}
                        </p>
                    </div>
                </div>
                <button 
                    @click="toggleDarkMode()"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    aria-label="Toggle dark mode">
                    <i x-show="!darkMode" class="fas fa-moon text-gray-500 text-sm"></i>
                    <i x-show="darkMode" class="fas fa-sun text-yellow-500 text-sm"></i>
                </button>
            </div>
        </div>
    </aside>
    
    <style>
@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.menu-item {
    animation: fadeInLeft 0.6s ease-out forwards;
}

/* Custom scrollbar */
aside::-webkit-scrollbar {
    width: 4px;
}

aside::-webkit-scrollbar-track {
    background: transparent;
}

aside::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 2px;
}

aside::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.5);
}
</style>
</div>