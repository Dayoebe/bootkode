<div class="flex flex-col h-full bg-gray-900 text-white shadow-2xl relative border-r border-gray-700"
     x-data="{ sidebarOpen: $persist(true).as('sidebarOpen') }"
     @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
     :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
     style="transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);">

    <!-- Sidebar Header/Logo -->
    <div class="flex items-center h-16 bg-gray-900 px-4 relative border-b border-gray-700">
        <a href="/" class="flex items-center space-x-2 transform transition-all duration-200 hover:scale-105"
           :class="{ 'justify-center w-full': !sidebarOpen, 'justify-start': sidebarOpen }">
            <div class="flex flex-row items-center gap-3 bg-gray-800/50 p-2.5 rounded-xl shadow-lg">
                <i class="fas fa-code text-blue-400"></i>
                <span class="text-xl font-bold text-white" 
                      x-show="sidebarOpen" 
                      x-transition:enter="transition ease-out duration-300 delay-100" 
                      x-transition:enter-start="opacity-0 transform translate-x-2" 
                      x-transition:enter-end="opacity-100 transform translate-x-0">
                    Boot<span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-800 to-purple-500">Kode</span> Academy
                </span>
            </div>
        </a>
        
        <!-- Toggle Button -->
        <button @click="$dispatch('toggle-sidebar')"
                class="absolute -right-3 top-1/2 -translate-y-1/2 bg-gradient-to-r from-gray-600 to-gray-700 text-white p-2 rounded-full shadow-lg z-10
                       hover:from-gray-500 hover:to-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 
                       transition-all duration-300 border border-gray-500">
            <i class="fas text-md transition-transform duration-300" :class="{ 'fa-chevron-left': sidebarOpen, 'fa-chevron-right': !sidebarOpen }"></i>
        </button>
    </div>

    <!-- User Profile Section -->
    @auth
    <div class="p-4 border-b border-gray-700 bg-gray-800/50" 
         x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-300 delay-150" 
         x-transition:enter-start="opacity-0 transform translate-y-2" 
         x-transition:enter-end="opacity-100 transform translate-y-0">
        <div class="flex items-center space-x-3 p-3 rounded-xl bg-gradient-to-r from-gray-700/50 to-gray-600/50 border border-gray-600/50">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-blue-400/30">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-gray-100 truncate">{{ $user->name }}</div>
                <div class="text-xs text-blue-300 font-medium">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
            </div>
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
        </div>
    </div>
    @endauth

    <!-- Navigation Links -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
        @foreach($menuItems as $item)
            @if(isset($item['children']))
                <!-- Dropdown Menu Item -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-3 text-gray-300 rounded-xl hover:bg-gradient-to-r hover:from-gray-700 hover:to-gray-600 hover:text-white transition-all duration-200 group border border-transparent hover:border-gray-600/50"
                            :class="{ 'justify-center': !sidebarOpen, 'justify-between': sidebarOpen }">
                        <span class="flex items-center" :class="{ 'w-full justify-center': !sidebarOpen }">
                            <i class="{{ $item['icon'] }} mr-3 text-lg transition-colors duration-200 group-hover:text-blue-300" :class="{ 'mr-0': !sidebarOpen }"></i>
                            <span class="text-sm font-medium" 
                                  x-show="sidebarOpen" 
                                  x-transition:enter="transition ease-out duration-300" 
                                  x-transition:enter-start="opacity-0" 
                                  x-transition:enter-end="opacity-100">{{ $item['label'] }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs transition-all duration-200 group-hover:text-blue-300" 
                           x-show="sidebarOpen" 
                           :class="{'rotate-180': open, 'text-blue-300': open}"></i>
                    </button>
                    <div x-show="open && sidebarOpen" 
                         x-collapse.duration.300ms
                         class="ml-6 mt-2 space-y-1 pl-4 border-l-2 border-gray-600"
                         style="display: none;">
                        @foreach($item['children'] as $child)
                            <a href="{{ $child['route'] }}"
                               class="flex items-center px-4 py-2.5 text-gray-400 rounded-lg hover:bg-gray-700/50 hover:text-white transition-all duration-200 text-sm group border border-transparent hover:border-gray-600/30
                               {{ $activeLink === ($child['link_id'] ?? '') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white border-blue-500/50 shadow-lg' : '' }}">
                                <i class="{{ $child['icon'] }} mr-3 text-sm group-hover:text-blue-300"></i>
                                <span class="group-hover:translate-x-1 transition-transform duration-200">{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Single Menu Item -->
                <a href="{{ $item['route'] }}"
                   class="flex items-center px-4 py-3 text-gray-300 rounded-xl hover:bg-gradient-to-r hover:from-gray-700 hover:to-gray-600 hover:text-white transition-all duration-200 group border border-transparent hover:border-gray-600/50
                   {{ $activeLink === ($item['link_id'] ?? '') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white border-blue-500/50 shadow-lg' : '' }}"
                   :class="{ 'justify-center': !sidebarOpen, 'justify-start': sidebarOpen }">
                    <i class="{{ $item['icon'] }} mr-3 text-lg transition-colors duration-200 
                       {{ $activeLink === ($item['link_id'] ?? '') ? 'text-blue-200' : 'group-hover:text-blue-300' }}" 
                       :class="{ 'mr-0': !sidebarOpen }"></i>
                    <span class="text-sm font-medium group-hover:translate-x-1 transition-transform duration-200" 
                          x-show="sidebarOpen" 
                          x-transition:enter="transition ease-out duration-300" 
                          x-transition:enter-start="opacity-0" 
                          x-transition:enter-end="opacity-100">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-gray-700 bg-gray-800/30">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center px-4 py-3 border border-red-500/50 text-sm font-medium rounded-xl text-red-200 bg-gradient-to-r from-red-600/20 to-red-700/20 hover:from-red-600 hover:to-red-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 focus:ring-offset-gray-800 transition-all duration-200 group"
                    :class="{ 'w-full': sidebarOpen, 'w-auto px-3': !sidebarOpen }">
                <i class="fas fa-sign-out-alt group-hover:animate-pulse" :class="{ 'mr-2': sidebarOpen, 'mr-0': !sidebarOpen }"></i>
                <span x-show="sidebarOpen" 
                      x-transition:enter="transition ease-out duration-300" 
                      x-transition:enter-start="opacity-0" 
                      x-transition:enter-end="opacity-100">Logout</span>
            </button>
        </form>
    </div>
</div>