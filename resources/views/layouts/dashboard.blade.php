<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BootKode</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=alef:400,700" rel="stylesheet" />

    @livewireScripts
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
</head>

<body class="font-sans antialiased bg-gray-50 h-full" style="font-family: 'Alef', sans-serif;">
    <div id="global-notifications" class="fixed top-4 right-4 z-50 space-y-2"></div>
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- User dropdown - Moved to the right -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                <span class="hidden md:inline text-gray-700 font-medium">{{ auth()->user()->name }}</span>

                                @if (auth()->user()->profile_picture)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                                        class="h-8 w-8 rounded-full object-cover border border-gray-200 hover:border-blue-400 transition-colors"
                                        alt="{{ auth()->user()->name }}">
                                @else
                                    <div
                                        class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-400 to-pink-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </div>
                                @endif

                                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route(auth()->user()->getDashboardRouteName()) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                </a>
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-cog mr-2"></i> Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content with Sidebar -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar - Full height and scrollable -->
            <aside
                class="flex-shrink-0 bg-gray-800 text-gray-200 border-r border-gray-700 transition-all duration-300 ease-in-out overflow-y-auto h-[calc(100vh-4rem)]"
                :class="{ 'w-2/5': sidebarOpen, 'w-1/6': !sidebarOpen }">
                <livewire:dashboard-sidebar />
            </aside>

            <!-- Main Content Area - Horizontally scrollable -->
            <main class="flex-1 overflow-y-auto overflow-x-auto">
                <div class="py-8 px-4 sm:px-6 lg:px-8 min-h-[calc(100vh-8rem)] min-w-max">
                    <div class="max-w-7xl mx-auto w-full">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>




        <div class="fixed bottom-4 right-4 bg-gray-300 px-3 py-2 rounded-lg shadow-lg border border-gray-700 flex items-center gap-2 text-sm"
            id="connection-status">
            <i class="fas fa-wifi text-green-500"></i>
            <span class="text-green-500">Online</span>
        </div>



        <!-- Footer -->
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
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (params) => {
                // Handle both string and object notifications
                const message = typeof params === 'string' ? params : params.message;
                const type = typeof params === 'string' ? 'info' : (params.type || 'info');

                showNotification(message, type);
            });
        });

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `px-6 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-600' : 
            type === 'error' ? 'bg-red-600' : 
            type === 'warning' ? 'bg-yellow-600' : 
            'bg-blue-600'
        }`;

            notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${
                    type === 'success' ? 'check' : 
                    type === 'error' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-circle' : 
                    'info'
                } mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            const container = document.getElementById('global-notifications');
            container.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 10);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
</body>

</html>
