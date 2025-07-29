<!DOCTYPE html>
<html lang="en">
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
</head>
<body class="font-sans antialiased bg-gray-100" style="font-family: 'Alef', sans-serif;">
    <div class="flex h-screen bg-gray-100"
         x-data="{ sidebarOpen: $persist(true).as('sidebarOpen') }"
         @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
        <!-- Sidebar -->
        <aside class="flex-shrink-0"
               :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
               style="transition: width 0.3s ease-in-out;">
            {{-- Remove the :sidebar-open prop since we'll handle it with Alpine.js events --}}
            <livewire:dashboard-sidebar />
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden"
             :class="{ 'ml-64': sidebarOpen, 'ml-20': !sidebarOpen }"
             style="transition: margin-left 0.3s ease-in-out;">
            <!-- Top Bar/Header for Dashboard -->
            <header class="w-full bg-white shadow-sm py-4 px-6 flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-700 text-sm hidden sm:block">Welcome, {{ auth()->user()->name }}!</span>
                        <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-blue-500">
                            <i class="fas fa-user-circle text-xl"></i>
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <x-header />
                {{ $slot }}
                <x-footer />
            </main>
        </div>
    </div>

    <!-- Scripts -->
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

    {{-- Global Notification Component --}}
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
            }, 4000);
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
         class="fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50"
         style="display: none;">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i x-show="type === 'success'" class="fas fa-check-circle h-6 w-6 text-green-400"></i>
                    <i x-show="type === 'error'" class="fas fa-times-circle h-6 w-6 text-red-400"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900" x-text="message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="show = false"
                            class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>