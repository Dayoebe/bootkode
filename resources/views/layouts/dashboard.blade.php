<!DOCTYPE html>
<html lang="en" class="dark" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BootKode - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans antialiased">
    @php
        $user = Auth::user();
    @endphp
    <div class="flex min-h-screen">
        <!-- Sidebar (Livewire Component) -->
        @livewire('dashboard-sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Top Navbar -->
            <header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center sticky top-0 z-40 animate__animated animate__fadeInDown">
                <!-- Hamburger for Mobile -->
                <button class="lg:hidden text-gray-500" @click="$dispatch('toggle-sidebar')" aria-label="Toggle sidebar">
                    <i class="fas fa-bars text-2xl"></i>
                </button>

                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="group relative">
                        <button class="relative text-gray-500 hover:text-blue-500" aria-label="Notifications">
                            <i class="fas fa-bell text-xl"></i>
                            @if($user?->unreadNotifications()?->count() > 0)
                                <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1">
                                    {{ $user->unreadNotifications()->count() }}
                                </span>
                            @endif
                        </button>
                        <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 -bottom-8 left-1/2 transform -translate-x-1/2">Notifications</span>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-blue-500" aria-label="User menu">
                            <img src="{{ $user?->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="User avatar" class="w-8 h-8 rounded-full">
                            <span>{{ $user?->name ?? 'Guest' }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 shadow-lg rounded-lg py-2 z-50">
                            @if($user)
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-700">Edit Profile</a>
                                <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-700">Logout</a>
                            @else
                                <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-700">Login</a>
                            @endif
                        </div>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="text-gray-500 hover:text-blue-500" aria-label="Toggle dark mode">
                        <i x-bind:class="darkMode ? 'fas fa-sun' : 'fas fa-moon'" class="text-xl"></i>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <main class="p-6 flex-1">
                @if(!$user)
                    <div class="bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-200 p-4 mb-4 rounded">
                        <p>Please <a href="{{ route('login') }}" class="underline">log in</a> to access the dashboard.</p>
                    </div>
                @endif
                {{ $slot }}
                {{-- @yield('content') --}}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>