<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BootKode</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=alef:400,700" rel="stylesheet" />
</head>
<body class="font-sans antialiased bg-gray-50 h-full flex flex-col" style="font-family: 'Alef', sans-serif;">
    <div class="flex flex-1 overflow-hidden"
         x-data="{ sidebarOpen: $persist(true).as('sidebarOpen') }"
         @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
        
        <!-- Sidebar - Full height -->
        <aside class="flex-shrink-0 bg-gray-800 text-gray-200 border-r border-gray-700 transition-all duration-300 ease-in-out z-30"
               :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }">
            <livewire:dashboard-sidebar />
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="sticky top-0 z-20 bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button @click="$dispatch('toggle-sidebar')"
                                class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        
                        <h1 class="text-xl font-bold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                            <!-- Notification and user dropdown -->
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <main class="flex-1 bg-white p-6 overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Footer - Outside main flex container -->
    <footer class="bg-gray-800 text-gray-300 py-4 border-t border-gray-700">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="text-sm mb-2 md:mb-0">
                &copy; {{ date('Y') }} BootKode Academy. All rights reserved.
            </div>
            <div class="flex space-x-4">
                <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="LinkedIn">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="GitHub">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- Mobile Sidebar Overlay -->
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
    @livewireScripts

    <!-- Notification Component -->
    <div x-data="notification" 
         @notify.window="showNotification($event)"
         x-show="show"
         x-transition
         class="fixed top-4 right-4 max-w-sm w-full bg-white shadow-xl rounded-xl ring-1 ring-gray-200 overflow-hidden z-50 border-l-4"
         :class="{
             'border-green-500': type === 'success',
             'border-red-500': type === 'error',
             'border-yellow-500': type === 'warning',
             'border-blue-500': type === 'info'
         }"
         style="display: none;">
        <!-- Notification content -->
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notification', () => ({
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
            }))
        })
    </script>
</body>
</html>