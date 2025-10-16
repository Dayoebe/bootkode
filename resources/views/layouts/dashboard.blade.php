<!DOCTYPE html>
<html lang="en" x-data="{ 
    theme: localStorage.getItem('theme') || 'light',
    sidebarOpen: false,
    setTheme(newTheme) {
        this.theme = newTheme;
        localStorage.setItem('theme', newTheme);
        document.documentElement.className = newTheme;
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    }
}" x-bind:class="theme" x-init="document.documentElement.className = theme">

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
    <style>
        /* Light Theme (Default) */
        .light {
            --bg-primary: 249 250 251; /* gray-50 */
            --bg-secondary: 255 255 255; /* white */
            --bg-tertiary: 243 244 246; /* gray-100 */
            --text-primary: 17 24 39; /* gray-900 */
            --text-secondary: 75 85 99; /* gray-600 */
            --text-tertiary: 156 163 175; /* gray-400 */
            --border-primary: 229 231 235; /* gray-200 */
            --border-secondary: 209 213 219; /* gray-300 */
            --accent-primary: 59 130 246; /* blue-500 */
            --accent-secondary: 37 99 235; /* blue-600 */
        }
        
        /* Dark Theme */
        .dark {
            --bg-primary: 17 24 39; /* gray-900 */
            --bg-secondary: 31 41 55; /* gray-800 */
            --bg-tertiary: 55 65 81; /* gray-700 */
            --text-primary: 255 255 255; /* white */
            --text-secondary: 209 213 219; /* gray-300 */
            --text-tertiary: 156 163 175; /* gray-400 */
            --border-primary: 55 65 81; /* gray-700 */
            --border-secondary: 75 85 99; /* gray-600 */
            --accent-primary: 96 165 250; /* blue-400 */
            --accent-secondary: 59 130 246; /* blue-500 */
        }
        
        /* Sepia Theme (Reading Mode) */
        .sepia {
            --bg-primary: 244 236 216; /* warm beige */
            --bg-secondary: 250 245 235; /* lighter beige */
            --bg-tertiary: 238 228 208; /* darker beige */
            --text-primary: 92 75 55; /* warm brown */
            --text-secondary: 120 100 80; /* medium brown */
            --text-tertiary: 160 140 120; /* light brown */
            --border-primary: 220 205 180; /* warm border */
            --border-secondary: 200 185 160; /* darker border */
            --accent-primary: 139 92 46; /* warm accent */
            --accent-secondary: 115 75 35; /* darker accent */
        }
        
        /* Ocean Blue Theme */
        .ocean {
            --bg-primary: 240 249 255; /* blue-50 */
            --bg-secondary: 224 242 254; /* blue-100 */
            --bg-tertiary: 186 230 253; /* blue-200 */
            --text-primary: 12 74 110; /* blue-900 */
            --text-secondary: 7 89 133; /* blue-800 */
            --text-tertiary: 14 116 144; /* cyan-700 */
            --border-primary: 125 211 252; /* blue-300 */
            --border-secondary: 56 189 248; /* blue-400 */
            --accent-primary: 2 132 199; /* cyan-600 */
            --accent-secondary: 3 105 161; /* cyan-700 */
        }
        
        /* Forest Green Theme */
        .forest {
            --bg-primary: 236 253 245; /* green-50 */
            --bg-secondary: 209 250 229; /* green-100 */
            --bg-tertiary: 167 243 208; /* green-200 */
            --text-primary: 20 83 45; /* green-900 */
            --text-secondary: 21 128 61; /* green-800 */
            --text-tertiary: 22 163 74; /* green-700 */
            --border-primary: 134 239 172; /* green-300 */
            --border-secondary: 74 222 128; /* green-400 */
            --accent-primary: 34 197 94; /* green-500 */
            --accent-secondary: 22 163 74; /* green-600 */
        }
        
        /* Apply theme variables to elements */
        body {
            background-color: rgb(var(--bg-primary));
            color: rgb(var(--text-primary));
        }
        
        /* Update all themed classes */
        .bg-themed-primary {
            background-color: rgb(var(--bg-primary));
        }
        
        .bg-themed-secondary {
            background-color: rgb(var(--bg-secondary));
        }
        
        .bg-themed-tertiary {
            background-color: rgb(var(--bg-tertiary));
        }
        
        .text-themed-primary {
            color: rgb(var(--text-primary));
        }
        
        .text-themed-secondary {
            color: rgb(var(--text-secondary));
        }
        
        .text-themed-tertiary {
            color: rgb(var(--text-tertiary));
        }
        
        .border-themed-primary {
            border-color: rgb(var(--border-primary));
        }
        
        .border-themed-secondary {
            border-color: rgb(var(--border-secondary));
        }
        
        .accent-themed-primary {
            color: rgb(var(--accent-primary));
        }
        
        .bg-accent-themed-primary {
            background-color: rgb(var(--accent-primary));
        }
        
        .bg-accent-themed-secondary {
            background-color: rgb(var(--accent-secondary));
        }
        </style>
        
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
                <div class="mb-6 p-4 bg-themed-secondary rounded-xl shadow-lg border border-themed-primary animate__animated animate__fadeInDown transition-colors duration-300">
                    <h1 class="text-xl lg:text-2xl font-bold text-themed-primary transition-colors duration-300">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-themed-secondary mt-1 transition-colors duration-300">{{ ucfirst($user->getRoleNames()->first() ?? 'User') }} Dashboard</p>
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