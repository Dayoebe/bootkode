<div>
    <aside 
    class="bg-white dark:bg-gray-800 h-screen w-64 fixed left-0 top-0 overflow-y-auto transition-all duration-300 ease-in-out shadow-lg lg:translate-x-0 z-50 -translate-x-full" 
    x-data="{ open: false, toggle() { this.open = !this.open } }"
    :class="{ '-translate-x-full': !open, 'translate-x-0': open }"
    @toggle-sidebar.window="toggle()" 
    aria-label="Sidebar navigation" 
    wire:ignore.self
>

        <!-- Logo/Header -->
        <div class="p-4 border-b dark:border-gray-700 animate__animated animate__fadeInDown">
            <a href="{{ route('home') }}" class="flex items-center space-x-2" aria-label="BootKode Home">
                <i class="fas fa-code text-blue-500 text-2xl"></i>
                <span class="text-xl font-bold text-gray-900 dark:text-white">BootKode</span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="p-4">
            <div class="relative">
                <input type="text" placeholder="Search menu..."
                    class="w-full p-2 pl-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 animate__animated animate__zoomIn"
                    aria-label="Search navigation">
                <i class="fas fa-search absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Menu Items (Desktop) -->
        <nav class="p-4 space-y-2" role="navigation">
            @foreach ($menuItems as $item)
                <div x-data="{ expanded: '{{ $activeLink }}' === '{{ $item['link_id'] ?? str()->slug($item['label']) }}' }" class="animate__animated animate__fadeInLeft"
                    x-bind:class="{ 'animate__delay-1s': true }">
                    <button @click="expanded = !expanded"
                        class="flex items-center justify-between w-full p-2 rounded-lg hover:bg-blue-100 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-expanded="expanded"
                        :aria-controls="'menu-{{ $item['link_id'] ?? str()->slug($item['label']) }}'">
                        <div class="flex items-center space-x-3">
                            <i class="{{ $item['icon'] }} text-blue-500"></i>
                            <span class="text-gray-900 dark:text-white">{{ $item['label'] }}</span>
                        </div>
                        @if (isset($item['children']) && !empty($item['children']))
                            <i class="fas fa-chevron-down transition-transform duration-200"
                                x-bind:class="{ 'rotate-180': expanded }"></i>
                        @endif
                    </button>
                    @if (isset($item['children']) && !empty($item['children']))
                        <ul x-show="expanded" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100" class="ml-6 space-y-1 mt-2"
                            id="menu-{{ $item['link_id'] ?? str()->slug($item['label']) }}" role="menu">
                            @foreach ($item['children'] as $child)
                                <li role="menuitem">
                                    <a href="{{ $child['route_name'] === '#' ? '#' : route($child['route_name']) }}"
                                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-600 {{ $activeLink === ($child['link_id'] ?? str()->slug($child['label'])) ? 'bg-blue-100 dark:bg-gray-700' : '' }} transition-colors duration-200"
                                        wire:navigate>
                                        <i class="{{ $child['icon'] }} text-gray-500"></i>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $child['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </nav>
    </aside>

    <!-- Mobile Bottom Navigation -->
    <nav
        class="lg:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 shadow-lg flex justify-around items-center h-16 z-50 animate__animated animate__slideInUp">
        @foreach ($mobileMenuItems as $item)
            <a href="{{ $item['route_name'] === '#' ? '#' : route($item['route_name']) }}"
                class="flex flex-col items-center p-2 text-gray-600 dark:text-gray-300 hover:text-blue-500 transition-colors duration-200"
                aria-label="{{ $item['label'] }}" wire:navigate>
                <i class="{{ $item['icon'] }} text-xl"></i>
                <span class="text-xs">{{ $item['label'] }}</span>
                @if ($item['badge'])
                    <span class="bg-red-500 text-white text-xs rounded-full px-1">!</span>
                @endif
            </a>
        @endforeach


        <button 
    @click="$dispatch('toggle-sidebar')" 
    class="p-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700"
>
    ☰ Menu
</button>
    </nav>
</div>
