<!DOCTYPE html>
<html lang="en" class="dark" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true' || localStorage.getItem('darkMode') === null,
    sidebarOpen: false,
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    }
}" x-bind:class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BootKode - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @livewireStyles
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    @php
        $user = Auth::user();
    @endphp
    
    <div class="flex min-h-screen">
        <!-- Desktop Sidebar -->
        @livewire('dashboard-sidebar')

        <!-- Sidebar Overlay for Mobile -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden"
            style="display: none;"
        ></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64 transition-all duration-300">
            <!-- Top Navbar -->
            <x-navbar />

            <!-- Content Area -->
            <main class="flex-1 p-4 lg:p-6 pb-20 lg:pb-6">
                <!-- Welcome Banner (Optional) -->
                @if($user)
                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg text-white animate__animated animate__fadeInDown">
                        <h1 class="text-xl lg:text-2xl font-bold">Welcome back, {{ $user->name }}!</h1>
                        <p class="text-blue-100 mt-1">{{ ucfirst($user->getRoleNames()->first() ?? 'User') }} Dashboard</p>
                    </div>
                @endif

                <!-- Main Content Slot -->
                <div class="animate__animated animate__fadeIn">
                    {{ $slot }}
                </div>
            </main>
        </div>
        <x-mobile-bottom-nav />
    </div>
    @livewireScripts
</body>
</html>