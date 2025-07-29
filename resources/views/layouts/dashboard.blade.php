<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BootKode</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=alef:400,700" rel="stylesheet" />

    <!-- Custom Styles -->
    <style>
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(55, 65, 81, 0.1);
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.8);
        }

        /* Smooth transitions */
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Glass effect for header */
        .glass-header {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
        }

        /* Content area styling */
        .main-content {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 h-full" style="font-family: 'Alef', sans-serif;">
    <div class="flex h-full bg-slate-50"
         x-data="{ sidebarOpen: $persist(true).as('sidebarOpen') }"
         @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
        
        <!-- Sidebar -->
        <aside class="flex-shrink-0 sidebar-transition z-30"
               :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }">
            <livewire:dashboard-sidebar />
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden sidebar-transition min-w-0 min-h-full">
            <!-- Top Bar/Header for Dashboard -->
            <header class="glass-header sticky top-0 z-20 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile menu button (for future mobile responsiveness) -->
                        <button @click="$dispatch('toggle-sidebar')"
                                class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                            <p class="text-sm text-gray-600 mt-1">Welcome back! Here's what's happening today.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                            <!-- Notifications -->
                            <button class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                            </button>
                            
                            <!-- User Profile Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                     style="display: none;">
                                    <div class="py-1">
                                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user-circle mr-3"></i>
                                            Profile Settings
                                        </a>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-cog mr-3"></i>
                                            Preferences
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-sign-out-alt mr-3"></i>
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Page Content - This will grow to fill available space -->
            <main class="flex-1 main-content">
                <!-- Content wrapper with consistent padding -->
                <div class="p-6 min-h-full">
                    {{ $slot }}
                </div>
            </main>
            
            <!-- Footer - This will stick to bottom -->
            <footer class="bg-gray-900 text-white py-6 mt-auto">
                <x-footer />
            </footer>
        </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden"
         style="display: none;"></div>

    <!-- Scripts -->
    <livewire:scripts />
    @livewireScripts

    <!-- Alpine.js Plugins -->
    <script src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Enhanced notification system
        document.addEventListener('alpine:init', () => {
            window.addEventListener('notify', event => {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: event.detail
                }));
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Alpine === 'undefined') {
                window.addEventListener('notify', event => {
                    const message = event.detail[0] || 'Action completed';
                    const type = event.detail[1] || 'success';
                    alert(`${type.toUpperCase()}: ${message}`);
                });
            }
        });
    </script>

    {{-- Enhanced Global Notification Component --}}
    <div x-data="{
        show: false,
        message: '',
        type: 'success',
        timeout: null,
        showNotification(event) {
            this.message = event.detail[0] || event.detail.message || 'Action completed';
            this.type = event.detail[1] || event.detail.type || 'success';
            this.show = true;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.show = false;
            }, 5000);
        }
    }"
         @notify.window="showNotification($event)"
         x-show="show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 max-w-sm w-full bg-white shadow-xl rounded-xl ring-1 ring-gray-200 overflow-hidden z-50 border-l-4"
         :class="{
             'border-green-500': type === 'success',
             'border-red-500': type === 'error',
             'border-yellow-500': type === 'warning',
             'border-blue-500': type === 'info'
         }"
         style="display: none;">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                         :class="{
                             'bg-green-100 text-green-600': type === 'success',
                             'bg-red-100 text-red-600': type === 'error',
                             'bg-yellow-100 text-yellow-600': type === 'warning',
                             'bg-blue-100 text-blue-600': type === 'info'
                         }">
                        <i x-show="type === 'success'" class="fas fa-check text-sm"></i>
                        <i x-show="type === 'error'" class="fas fa-times text-sm"></i>
                        <i x-show="type === 'warning'" class="fas fa-exclamation text-sm"></i>
                        <i x-show="type === 'info'" class="fas fa-info text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900" x-text="message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="show = false"
                            class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>