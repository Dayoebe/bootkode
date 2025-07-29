<div class="flex flex-col h-full bg-gray-800 text-white shadow-lg relative"
     x-data="{ sidebarOpen: $persist(true).as('sidebarOpen') }"
     @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
     :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
     style="transition: width 0.3s ease-in-out;">

    <!-- Sidebar Header/Logo -->
    <div class="flex items-center h-16 bg-gray-900 px-4 relative">
        <a href="#" class="flex items-center space-x-2 transform transition-transform hover:scale-105"
           :class="{ 'justify-center w-full': !sidebarOpen, 'justify-start': sidebarOpen }">
            <div class="flex flex-row gap-3 bg-gradient-to-r from-blue-50 to-purple-100 p-2 rounded-lg shadow-sm">
                <svg class="h-8 w-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                <span class="text-2xl font-bold text-gray-900" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Boot<span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-800 to-purple-400">Kode</span></span>
            </div>
        </a>
        <!-- Toggle Button -->
        <button @click="$dispatch('toggle-sidebar')"
                class="absolute -right-3 top-1/2 -translate-y-1/2 bg-gray-700 text-white p-1 rounded-full shadow-md z-10
                       hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
            <i class="fas" :class="{ 'fa-chevron-left': sidebarOpen, 'fa-chevron-right': !sidebarOpen }"></i>
        </button>
    </div>

    <!-- User Profile Section -->
    @auth
    <div class="p-4 border-b border-gray-700" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <div class="font-medium text-gray-100">{{ $user->name }}</div>
                <div class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
            </div>
        </div>
    </div>
    @endauth

    <!-- Navigation Links -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto custom-scrollbar">
        @foreach($menuItems as $item)
            @if(isset($item['children']))
                <!-- Dropdown Menu Item -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-3 py-2 text-gray-300 rounded-md hover:bg-gray-700 hover:text-white transition-colors duration-200"
                            :class="{ 'justify-center': !sidebarOpen, 'justify-between': sidebarOpen }">
                        <span class="flex items-center" :class="{ 'w-full justify-center': !sidebarOpen }">
                            <i class="{{ $item['icon'] }} mr-3 text-lg" :class="{ 'mr-0': !sidebarOpen }"></i>
                            <span class="text-sm font-medium" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">{{ $item['label'] }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" x-show="sidebarOpen" :class="{'rotate-180': open}"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse.duration.300ms
                         class="ml-6 mt-1 space-y-1"
                         style="display: none;">
                        @foreach($item['children'] as $child)
                            <a href="{{ $child['route'] }}"
                               class="flex items-center px-3 py-2 text-gray-400 rounded-md hover:bg-gray-700 hover:text-white transition-colors duration-200 text-sm">
                                <i class="{{ $child['icon'] }} mr-3 text-base"></i>
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Single Menu Item -->
                <a href="{{ $item['route'] }}"
                   class="flex items-center px-3 py-2 text-gray-300 rounded-md hover:bg-gray-700 hover:text-white transition-colors duration-200
                   {{ $activeLink === ($item['link_id'] ?? '') ? 'bg-gray-700 text-white' : '' }}"
                   :class="{ 'justify-center': !sidebarOpen, 'justify-start': sidebarOpen }">
                    <i class="{{ $item['icon'] }} mr-3 text-lg" :class="{ 'mr-0': !sidebarOpen }"></i>
                    <span class="text-sm font-medium" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-100 bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                    :class="{ 'w-full': sidebarOpen, 'w-auto px-2': !sidebarOpen }">
                <i class="fas fa-sign-out-alt" :class="{ 'mr-2': sidebarOpen, 'mr-0': !sidebarOpen }"></i>
                <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Logout</span>
            </button>
        </form>
    </div>
</div>