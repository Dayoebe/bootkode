<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BootKode Academy - Modern Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* Custom scrollbar */
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Glassmorphism */
        .glass { 
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .glass-dark { 
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Custom animations */
        @keyframes slideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideInUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes shimmer {
            0% { background-position: -200px 0; }
            100% { background-position: calc(200px + 100%) 0; }
        }
        
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            background-size: 200px 100%;
            animation: shimmer 2s infinite;
        }
        
        /* Gradient backgrounds */
        .bg-gradient-cosmic {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .bg-gradient-ocean {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Hover effects */
        .hover-lift { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-4px); }
        
        /* Menu item active state */
        .menu-item-active {
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.08) 100%);
            border-left: 3px solid #f472b6;
            box-shadow: 0 4px 20px rgba(244, 114, 182, 0.3);
        }
        
        /* Mobile bottom nav */
        .mobile-nav-active {
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
            box-shadow: 0 -2px 20px rgba(244, 114, 182, 0.4);
        }
        
        /* Pulse animation for notifications */
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2.5); opacity: 0; }
        }
        
        .pulse-ring::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 100%; height: 100%;
            border-radius: 50%;
            background: #ef4444;
            animation: pulse-ring 1.5s infinite;
        }
    </style>
</head>
<body class="bg-gradient-cosmic min-h-screen text-white overflow-x-hidden" 
      x-data="dashboardApp()" 
      x-init="init()"
      @resize.window="handleResize">

    <!-- Dynamic Background Particles -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-1/4 left-1/4 w-4 h-4 bg-white rounded-full animate-ping" style="animation-delay: 0s;"></div>
            <div class="absolute top-3/4 right-1/4 w-2 h-2 bg-pink-300 rounded-full animate-ping" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-3/4 w-3 h-3 bg-blue-300 rounded-full animate-ping" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/3 right-1/3 w-1 h-1 bg-purple-300 rounded-full animate-ping" style="animation-delay: 3s;"></div>
        </div>
    </div>

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 glass border-b border-white/10">
        <div class="px-4 lg:px-6 xl:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Left Section -->
                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Toggle -->
                    <button @click="toggleMobileSidebar" 
                            class="lg:hidden p-2.5 rounded-xl glass-dark hover:bg-white/10 transition-all duration-200 hover:scale-105 active:scale-95">
                        <i class="fas fa-bars text-lg" :class="mobileSidebarOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>

                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg hover-lift">
                                <i class="fas fa-code text-white text-lg"></i>
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white animate-pulse"></div>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-xl font-bold">Boot<span class="text-pink-300">Kode</span></h1>
                            <p class="text-xs text-white/60">Academy Dashboard</p>
                        </div>
                    </div>
                </div>

                <!-- Center Search -->
                <div class="hidden md:block flex-1 max-w-lg mx-8">
                    <div class="relative" x-data="{ searchFocused: false, searchQuery: '' }">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-white/40"></i>
                        </div>
                        <input type="search" 
                               x-model="searchQuery"
                               @focus="searchFocused = true"
                               @blur="setTimeout(() => searchFocused = false, 200)"
                               placeholder="Search anything..." 
                               class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-white/40 focus:bg-white/10 focus:border-white/20 focus:outline-none transition-all duration-300 focus:ring-2 focus:ring-pink-500/30">
                        
                        <!-- Search Suggestions -->
                        <div x-show="searchFocused && searchQuery.length > 0"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute top-full mt-2 w-full glass rounded-2xl shadow-2xl overflow-hidden z-50">
                            <div class="p-2 space-y-1">
                                <div class="p-3 rounded-xl hover:bg-white/10 transition-colors cursor-pointer">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-graduation-cap text-blue-400"></i>
                                        <div>
                                            <div class="font-medium">JavaScript Fundamentals</div>
                                            <div class="text-sm text-white/60">Course • 24 students enrolled</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 rounded-xl hover:bg-white/10 transition-colors cursor-pointer">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-user text-green-400"></i>
                                        <div>
                                            <div class="font-medium">John Smith</div>
                                            <div class="text-sm text-white/60">Student • Last active 2h ago</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="flex items-center space-x-3">
                    <!-- Theme Toggle -->
                    <button @click="toggleTheme" 
                            class="p-2.5 rounded-xl glass-dark hover:bg-white/10 transition-all duration-200 hover:scale-105 active:scale-95">
                        <i class="fas fa-moon text-lg" x-show="!darkMode"></i>
                        <i class="fas fa-sun text-lg" x-show="darkMode"></i>
                    </button>

                    <!-- Notifications -->
                    <div class="relative" x-data="{ notificationOpen: false }">
                        <button @click="notificationOpen = !notificationOpen" 
                                class="relative p-2.5 rounded-xl glass-dark hover:bg-white/10 transition-all duration-200 hover:scale-105 active:scale-95">
                            <i class="fas fa-bell text-lg"></i>
                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-xs font-bold pulse-ring">3</div>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notificationOpen"
                             @click.away="notificationOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-3 w-96 glass rounded-2xl shadow-2xl border border-white/10 overflow-hidden z-50">
                            
                            <div class="p-4 border-b border-white/10">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-lg">Notifications</h3>
                                    <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full">3 new</span>
                                </div>
                            </div>

                            <div class="max-h-96 overflow-y-auto scrollbar-none">
                                <div class="p-4 hover:bg-white/5 transition-colors border-b border-white/5">
                                    <div class="flex space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user-plus text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium">New student registered</p>
                                            <p class="text-sm text-white/60">Sarah Johnson enrolled in React Masterclass</p>
                                            <p class="text-xs text-white/40 mt-1">2 minutes ago</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-4 hover:bg-white/5 transition-colors border-b border-white/5">
                                    <div class="flex space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-certificate text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium">Course completed</p>
                                            <p class="text-sm text-white/60">Mike Wilson finished JavaScript Basics</p>
                                            <p class="text-xs text-white/40 mt-1">1 hour ago</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 hover:bg-white/5 transition-colors">
                                    <div class="flex space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-star text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium">New review received</p>
                                            <p class="text-sm text-white/60">5-star rating for Python Advanced</p>
                                            <p class="text-xs text-white/40 mt-1">3 hours ago</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 border-t border-white/10 text-center">
                                <button class="text-sm text-pink-400 hover:text-pink-300 font-medium">View all notifications</button>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" 
                                class="flex items-center space-x-3 p-1 rounded-2xl glass-dark hover:bg-white/10 transition-all duration-200">
                            <div class="w-8 h-8 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center font-bold text-sm">
                                J
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="font-medium text-sm">John Doe</div>
                                <div class="text-xs text-white/60">Super Admin</div>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                               :class="profileOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <!-- Profile Dropdown Menu -->
                        <div x-show="profileOpen"
                             @click.away="profileOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-3 w-64 glass rounded-2xl shadow-2xl border border-white/10 overflow-hidden z-50">
                            
                            <div class="p-4 border-b border-white/10">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center font-bold">
                                        J
                                    </div>
                                    <div>
                                        <div class="font-semibold">John Doe</div>
                                        <div class="text-sm text-white/60">john@bootkode.com</div>
                                        <div class="text-xs text-pink-400">Super Admin</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2">
                                <a href="#" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-white/10 transition-colors">
                                    <i class="fas fa-user text-blue-400 w-5"></i>
                                    <span>Profile Settings</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-white/10 transition-colors">
                                    <i class="fas fa-cog text-green-400 w-5"></i>
                                    <span>Preferences</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-white/10 transition-colors">
                                    <i class="fas fa-shield-alt text-purple-400 w-5"></i>
                                    <span>Privacy & Security</span>
                                </a>
                                <div class="border-t border-white/10 my-2"></div>
                                <button class="w-full flex items-center space-x-3 p-3 rounded-xl hover:bg-red-500/10 text-red-400 transition-colors">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>Sign Out</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Desktop Sidebar -->
    <aside class="fixed top-16 left-0 bottom-0 w-72 glass border-r border-white/10 z-40 hidden lg:flex flex-col">
        <nav class="flex-1 p-6 overflow-y-auto scrollbar-none" x-data="sidebarApp()">
            
            <!-- Quick Actions -->
            <div class="mb-8">
                <div class="grid grid-cols-2 gap-3">
                    <button class="p-3 glass-dark rounded-xl hover:bg-white/10 transition-all duration-200 hover:scale-105 group">
                        <i class="fas fa-plus text-green-400 mb-2 group-hover:scale-110 transition-transform"></i>
                        <div class="text-xs font-medium">New Course</div>
                    </button>
                    <button class="p-3 glass-dark rounded-xl hover:bg-white/10 transition-all duration-200 hover:scale-105 group">
                        <i class="fas fa-user-plus text-blue-400 mb-2 group-hover:scale-110 transition-transform"></i>
                        <div class="text-xs font-medium">Add Student</div>
                    </button>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="space-y-2">
                <template x-for="(item, index) in menuItems" :key="index">
                    <div class="animate__animated animate__fadeInLeft" :style="'animation-delay: ' + (index * 0.1) + 's'">
                        <!-- Menu item with children -->
                        <div x-show="item.children && item.children.length > 0" 
                             x-data="{ expanded: item.id === activeMenu }">
                            <button @click="toggleMenu(item.id); expanded = !expanded"
                                    class="w-full flex items-center justify-between p-3 rounded-xl transition-all duration-300 hover:bg-white/10 group"
                                    :class="item.id === activeMenu ? 'menu-item-active' : ''">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                        <i :class="item.icon + ' text-lg'" 
                                           :style="item.id === activeMenu ? 'color: #f472b6' : ''"></i>
                                    </div>
                                    <span class="font-medium" x-text="item.label"></span>
                                </div>
                                <i class="fas fa-chevron-right text-xs transition-transform duration-300"
                                   :class="expanded ? 'rotate-90' : ''"></i>
                            </button>
                            
                            <div x-show="expanded"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="ml-6 mt-2 space-y-1">
                                <template x-for="child in item.children" :key="child.id">
                                    <a :href="child.route" 
                                       @click="setActiveItem(child.id)"
                                       class="flex items-center space-x-3 p-2.5 rounded-lg hover:bg-white/10 transition-all duration-200 group"
                                       :class="child.id === activeItem ? 'bg-white/10 text-pink-400' : 'text-white/80 hover:text-white'">
                                        <i :class="child.icon + ' text-sm w-4'" 
                                           class="group-hover:scale-110 transition-transform"></i>
                                        <span class="text-sm" x-text="child.label"></span>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- Single menu item -->
                        <div x-show="!item.children || item.children.length === 0">
                            <a :href="item.route" 
                               @click="setActiveItem(item.id)"
                               class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 hover:bg-white/10 group"
                               :class="item.id === activeItem ? 'menu-item-active' : ''">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                    <i :class="item.icon + ' text-lg'"></i>
                                </div>
                                <span class="font-medium" x-text="item.label"></span>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </nav>

        <!-- Mobile Sidebar Footer -->
        <div class="p-6 border-t border-white/10">
            <div class="flex items-center space-x-3 p-3 glass-dark rounded-xl">
                <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center font-bold">
                    J
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm truncate">John Doe</div>
                    <div class="text-xs text-white/60 truncate">Super Admin</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Bottom Navigation -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 glass border-t border-white/10" 
         x-data="bottomNavApp()">
        <div class="flex items-center justify-between px-2 py-2">
            <template x-for="(item, index) in bottomNavItems" :key="index">
                <div class="flex-1">
                    <!-- Bottom nav item with modal -->
                    <div x-show="item.hasModal" x-data="{ modalOpen: false }">
                        <button @click="modalOpen = true; setActiveBottom(item.id)"
                                class="w-full flex flex-col items-center justify-center py-2 px-1 rounded-2xl transition-all duration-200"
                                :class="item.id === activeBottom ? 'mobile-nav-active' : 'hover:bg-white/10'">
                            <div class="relative mb-1">
                                <i :class="item.icon + ' text-xl'" 
                                   :style="item.id === activeBottom ? 'color: #f472b6' : ''"></i>
                                <div x-show="item.badge" 
                                     class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                            </div>
                            <span class="text-xs font-medium" 
                                  :style="item.id === activeBottom ? 'color: #f472b6' : ''"
                                  x-text="item.label"></span>
                        </button>

                        <!-- Bottom Sheet Modal -->
                        <div x-show="modalOpen"
                             @click.away="modalOpen = false"
                             @keydown.escape.window="modalOpen = false"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end">
                            
                            <div class="w-full glass rounded-t-3xl p-6 max-h-[85vh] overflow-y-auto animate__animated animate__slideInUp">
                                <!-- Drag Handle -->
                                <div class="flex justify-center mb-6">
                                    <div class="w-12 h-1.5 bg-white/30 rounded-full"></div>
                                </div>
                                
                                <!-- Modal Header -->
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center">
                                            <i :class="item.icon + ' text-white text-xl'"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-2xl font-bold" x-text="item.label"></h2>
                                            <p class="text-white/60 text-sm">Choose an option</p>
                                        </div>
                                    </div>
                                    <button @click="modalOpen = false" 
                                            class="p-3 hover:bg-white/10 rounded-2xl transition-colors">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                                
                                <!-- Modal Content -->
                                <div class="grid gap-3">
                                    <template x-for="child in item.children" :key="child.id">
                                        <a :href="child.route" 
                                           @click="modalOpen = false"
                                           class="flex items-center p-4 rounded-2xl glass-dark hover:bg-white/10 transition-all duration-200 group border border-white/5 hover:border-white/20">
                                            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center mr-4 group-hover:bg-white/20 transition-colors">
                                                <i :class="child.icon + ' text-lg'"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold mb-1" x-text="child.label"></div>
                                                <div class="text-sm text-white/60" x-text="child.description || 'Access ' + child.label.toLowerCase()"></div>
                                            </div>
                                            <i class="fas fa-chevron-right text-white/40 group-hover:text-white/60 transition-colors ml-2"></i>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Regular bottom nav item -->
                    <div x-show="!item.hasModal">
                        <a :href="item.route" 
                           @click="setActiveBottom(item.id)"
                           class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl transition-all duration-200"
                           :class="item.id === activeBottom ? 'mobile-nav-active' : 'hover:bg-white/10'">
                            <div class="relative mb-1">
                                <i :class="item.icon + ' text-xl'" 
                                   :style="item.id === activeBottom ? 'color: #f472b6' : ''"></i>
                                <div x-show="item.badge" 
                                     class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                            </div>
                            <span class="text-xs font-medium" 
                                  :style="item.id === activeBottom ? 'color: #f472b6' : ''"
                                  x-text="item.label"></span>
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="lg:ml-72 pt-16 pb-20 lg:pb-8 min-h-screen">
        <div class="p-6 lg:p-8 xl:p-10">
            
            <!-- Welcome Section -->
            <div class="mb-8 animate__animated animate__fadeInDown">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold mb-2">
                            Welcome back, <span class="text-pink-400">John! 👋</span>
                        </h1>
                        <p class="text-white/70 text-lg">Here's what's happening with your academy today</p>
                    </div>
                    <div class="flex space-x-3">
                        <button class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl font-medium hover:shadow-lg hover:scale-105 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Create Course
                        </button>
                        <button class="px-6 py-3 glass-dark rounded-2xl font-medium hover:bg-white/10 transition-all duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="glass-dark rounded-2xl p-6 hover-lift animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-users text-blue-400 text-xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">2,847</div>
                            <div class="text-sm text-green-400">+12.5%</div>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold mb-1">Total Students</div>
                        <div class="text-sm text-white/60">Active learners this month</div>
                    </div>
                    <div class="mt-4 w-full bg-white/10 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" style="width: 75%"></div>
                    </div>
                </div>

                <div class="glass-dark rounded-2xl p-6 hover-lift animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-green-400 text-xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">156</div>
                            <div class="text-sm text-green-400">+8.2%</div>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold mb-1">Active Courses</div>
                        <div class="text-sm text-white/60">Currently available</div>
                    </div>
                    <div class="mt-4 w-full bg-white/10 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: 85%"></div>
                    </div>
                </div>

                <div class="glass-dark rounded-2xl p-6 hover-lift animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-certificate text-purple-400 text-xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">1,392</div>
                            <div class="text-sm text-green-400">+24.1%</div>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold mb-1">Certificates</div>
                        <div class="text-sm text-white/60">Issued this month</div>
                    </div>
                    <div class="mt-4 w-full bg-white/10 rounded-full h-2">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full" style="width: 92%"></div>
                    </div>
                </div>

                <div class="glass-dark rounded-2xl p-6 hover-lift animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-pink-500/20 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-pink-400 text-xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">$47.2K</div>
                            <div class="text-sm text-green-400">+18.7%</div>
                        </div>
                    </div>
                    <div>
                        <div class="font-semibold mb-1">Revenue</div>
                        <div class="text-sm text-white/60">Monthly earnings</div>
                    </div>
                    <div class="mt-4 w-full bg-white/10 rounded-full h-2">
                        <div class="bg-gradient-to-r from-pink-500 to-pink-600 h-2 rounded-full" style="width: 68%"></div>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Activity -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                <!-- Analytics Chart -->
                <div class="xl:col-span-2 glass-dark rounded-2xl p-6 animate__animated animate__fadeInLeft" style="animation-delay: 0.5s">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold mb-1">Learning Analytics</h3>
                            <p class="text-white/60">Student engagement over time</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-4 py-2 bg-pink-500 rounded-xl text-sm font-medium">Week</button>
                            <button class="px-4 py-2 glass hover:bg-white/10 rounded-xl text-sm transition-colors">Month</button>
                            <button class="px-4 py-2 glass hover:bg-white/10 rounded-xl text-sm transition-colors">Year</button>
                        </div>
                    </div>
                    
                    <!-- Chart Placeholder -->
                    <div class="h-80 flex items-center justify-center glass rounded-xl border border-white/10">
                        <div class="text-center">
                            <i class="fas fa-chart-line text-6xl text-white/20 mb-4"></i>
                            <div class="text-white/60">Interactive chart would go here</div>
                            <div class="text-sm text-white/40 mt-2">Integration with Chart.js or similar</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="glass-dark rounded-2xl p-6 animate__animated animate__fadeInRight" style="animation-delay: 0.6s">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold">Recent Activity</h3>
                        <button class="text-pink-400 hover:text-pink-300 text-sm font-medium">View All</button>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 p-3 glass rounded-xl">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm">New enrollment</div>
                                <div class="text-xs text-white/60">Sarah joined React Masterclass</div>
                                <div class="text-xs text-white/40">2 minutes ago</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 glass rounded-xl">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check-circle text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm">Course completed</div>
                                <div class="text-xs text-white/60">Mike finished Python Basics</div>
                                <div class="text-xs text-white/40">1 hour ago</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 glass rounded-xl">
                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-star text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm">New review</div>
                                <div class="text-xs text-white/60">5-star rating received</div>
                                <div class="text-xs text-white/40">3 hours ago</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 glass rounded-xl">
                            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm">Course update needed</div>
                                <div class="text-xs text-white/60">JavaScript Advanced requires review</div>
                                <div class="text-xs text-white/40">5 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Courses -->
            <div class="glass-dark rounded-2xl p-6 animate__animated animate__fadeInUp" style="animation-delay: 0.7s">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold">Popular Courses</h3>
                    <button class="text-pink-400 hover:text-pink-300 text-sm font-medium">Manage All</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div class="glass rounded-2xl p-4 hover-lift group">
                        <div class="aspect-video bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl mb-4 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                            <i class="fas fa-code text-3xl text-white"></i>
                        </div>
                        <h4 class="font-semibold mb-2">JavaScript Fundamentals</h4>
                        <p class="text-sm text-white/60 mb-3">Learn the basics of modern JavaScript programming</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-green-400">124 students</span>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                                <span class="text-sm">4.8</span>
                            </div>
                        </div>
                    </div>

                    <div class="glass rounded-2xl p-4 hover-lift group">
                        <div class="aspect-video bg-gradient-to-br from-green-500 to-teal-600 rounded-xl mb-4 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                            <i class="fab fa-react text-3xl text-white"></i>
                        </div>
                        <h4 class="font-semibold mb-2">React Masterclass</h4>
                        <p class="text-sm text-white/60 mb-3">Master React with hooks, context, and best practices</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-green-400">89 students</span>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                                <span class="text-sm">4.9</span>
                            </div>
                        </div>
                    </div>

                    <div class="glass rounded-2xl p-4 hover-lift group">
                        <div class="aspect-video bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl mb-4 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                            <i class="fab fa-python text-3xl text-white"></i>
                        </div>
                        <h4 class="font-semibold mb-2">Python for Beginners</h4>
                        <p class="text-sm text-white/60 mb-3">Start your programming journey with Python</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-green-400">203 students</span>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                                <span class="text-sm">4.7</span>
                            </div>
                        </div>
                    </div>

                    <div class="glass rounded-2xl p-4 hover-lift group">
                        <div class="aspect-video bg-gradient-to-br from-orange-500 to-red-600 rounded-xl mb-4 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                            <i class="fab fa-html5 text-3xl text-white"></i>
                        </div>
                        <h4 class="font-semibold mb-2">Web Development</h4>
                        <p class="text-sm text-white/60 mb-3">HTML, CSS, and JavaScript from scratch</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-green-400">167 students</span>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                                <span class="text-sm">4.6</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        // Dashboard App
        function dashboardApp() {
            return {
                mobileSidebarOpen: false,
                darkMode: false,

                init() {
                    // Initialize theme
                    this.darkMode = localStorage.getItem('darkMode') === 'true';
                    
                    // Handle window resize
                    this.handleResize();
                },

                toggleMobileSidebar() {
                    this.mobileSidebarOpen = !this.mobileSidebarOpen;
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    // Theme implementation would go here
                },

                handleResize() {
                    if (window.innerWidth >= 1024) {
                        this.mobileSidebarOpen = false;
                    }
                }
            }
        }

        // Sidebar App
        function sidebarApp() {
            return {
                activeMenu: 'dashboard',
                activeItem: 'dashboard',
                menuItems: [
                    {
                        id: 'dashboard',
                        label: 'Dashboard',
                        icon: 'fas fa-home',
                        route: '#dashboard'
                    },
                    {
                        id: 'course_management',
                        label: 'Course Management',
                        icon: 'fas fa-graduation-cap',
                        children: [
                            { id: 'all_courses', label: 'All Courses', icon: 'fas fa-list', route: '#courses' },
                            { id: 'create_course', label: 'Create Course', icon: 'fas fa-plus', route: '#create' },
                            { id: 'categories', label: 'Categories', icon: 'fas fa-tags', route: '#categories' }
                        ]
                    },
                    {
                        id: 'students',
                        label: 'Student Management',
                        icon: 'fas fa-users',
                        children: [
                            { id: 'all_students', label: 'All Students', icon: 'fas fa-user-friends', route: '#students' },
                            { id: 'enrollments', label: 'Enrollments', icon: 'fas fa-user-plus', route: '#enrollments' },
                            { id: 'analytics', label: 'Analytics', icon: 'fas fa-chart-bar', route: '#analytics' }
                        ]
                    },
                    {
                        id: 'certificates',
                        label: 'Certificates',
                        icon: 'fas fa-certificate',
                        children: [
                            { id: 'issued', label: 'Issued Certificates', icon: 'fas fa-award', route: '#certificates' },
                            { id: 'templates', label: 'Templates', icon: 'fas fa-file-alt', route: '#templates' },
                            { id: 'verify', label: 'Verify Certificate', icon: 'fas fa-check-circle', route: '#verify' }
                        ]
                    },
                    {
                        id: 'settings',
                        label: 'Settings',
                        icon: 'fas fa-cog',
                        route: '#settings'
                    },
                    {
                        id: 'support',
                        label: 'Support',
                        icon: 'fas fa-headset',
                        route: '#support'
                    }
                ],

                toggleMenu(menuId) {
                    this.activeMenu = this.activeMenu === menuId ? '' : menuId;
                },

                setActiveItem(itemId) {
                    this.activeItem = itemId;
                }
            }
        }

        // Bottom Navigation App
        function bottomNavApp() {
            return {
                activeBottom: 'dashboard',
                bottomNavItems: [
                    {
                        id: 'dashboard',
                        label: 'Home',
                        icon: 'fas fa-home',
                        route: '#dashboard',
                        hasModal: false
                    },
                    {
                        id: 'courses',
                        label: 'Courses',
                        icon: 'fas fa-graduation-cap',
                        badge: true,
                        hasModal: true,
                        children: [
                            { id: 'all_courses', label: 'All Courses', icon: 'fas fa-list', route: '#courses', description: 'View and manage all courses' },
                            { id: 'create_course', label: 'Create Course', icon: 'fas fa-plus', route: '#create', description: 'Add a new course' },
                            { id: 'categories', label: 'Categories', icon: 'fas fa-tags', route: '#categories', description: 'Manage course categories' }
                        ]
                    },
                    {
                        id: 'students',
                        label: 'Students',
                        icon: 'fas fa-users',
                        hasModal: true,
                        children: [
                            { id: 'all_students', label: 'All Students', icon: 'fas fa-user-friends', route: '#students', description: 'View student list' },
                            { id: 'enrollments', label: 'Enrollments', icon: 'fas fa-user-plus', route: '#enrollments', description: 'Manage enrollments' },
                            { id: 'analytics', label: 'Student Analytics', icon: 'fas fa-chart-bar', route: '#analytics', description: 'View performance data' }
                        ]
                    },
                    {
                        id: 'analytics',
                        label: 'Analytics',
                        icon: 'fas fa-chart-line',
                        route: '#analytics',
                        hasModal: false
                    },
                    {
                        id: 'more',
                        label: 'More',
                        icon: 'fas fa-ellipsis-h',
                        hasModal: true,
                        children: [
                            { id: 'certificates', label: 'Certificates', icon: 'fas fa-certificate', route: '#certificates', description: 'Manage certificates' },
                            { id: 'settings', label: 'Settings', icon: 'fas fa-cog', route: '#settings', description: 'App preferences' },
                            { id: 'support', label: 'Support', icon: 'fas fa-headset', route: '#support', description: 'Get help' }
                        ]
                    }
                ],

                setActiveBottom(itemId) {
                    this.activeBottom = itemId;
                }
            }
        }

        // Initialize animations on load
        document.addEventListener('DOMContentLoaded', function() {
            // Add staggered animations to elements
            const fadeElements = document.querySelectorAll('.animate__animated');
            fadeElements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.visibility = 'visible';
                }, index * 100);
            });

            // Add intersection observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__fadeInUp');
                    }
                });
            }, observerOptions);

            // Observe all cards for scroll animations
            document.querySelectorAll('.hover-lift').forEach(card => {
                observer.observe(card);
            });
        });

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
            
            // Escape to close modals
            if (e.key === 'Escape') {
                // Close any open modals
                document.dispatchEvent(new CustomEvent('close-modals'));
            }
        });

        // Add touch gestures for mobile
        let touchStartX = 0;
        let touchStartY = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        });

        document.addEventListener('touchend', function(e) {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;

            // Swipe right to open sidebar (mobile)
            if (deltaX > 100 && Math.abs(deltaY) < 50 && window.innerWidth < 1024) {
                const event = new CustomEvent('swipe-right');
                document.dispatchEvent(event);
            }

            // Swipe left to close sidebar (mobile)
            if (deltaX < -100 && Math.abs(deltaY) < 50 && window.innerWidth < 1024) {
                const event = new CustomEvent('swipe-left');
                document.dispatchEvent(event);
            }
        });

        // Performance optimization - lazy load images
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => imageObserver.observe(img));

        // Add notification system
        function showNotification(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-50 max-w-sm glass rounded-2xl p-4 border-l-4 animate__animated animate__slideInRight ${
                type === 'success' ? 'border-green-500' :
                type === 'error' ? 'border-red-500' :
                type === 'warning' ? 'border-yellow-500' :
                'border-blue-500'
            }`;

            notification.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <i class="fas ${
                            type === 'success' ? 'fa-check-circle text-green-400' :
                            type === 'error' ? 'fa-exclamation-circle text-red-400' :
                            type === 'warning' ? 'fa-exclamation-triangle text-yellow-400' :
                            'fa-info-circle text-blue-400'
                        }"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white">${message}</p>
                    </div>
                    <button onclick="this.closest('div').remove()" class="flex-shrink-0 text-white/60 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.remove('animate__slideInRight');
                    notification.classList.add('animate__slideOutRight');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, duration);
        }

        // Example usage of notifications (you can call these from your app)
        window.showSuccess = (message) => showNotification(message, 'success');
        window.showError = (message) => showNotification(message, 'error');
        window.showWarning = (message) => showNotification(message, 'warning');
        window.showInfo = (message) => showNotification(message, 'info');

        // Add progress indicator for page transitions
        function showPageLoader() {
            const loader = document.createElement('div');
            loader.id = 'page-loader';
            loader.className = 'fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-pink-500 to-purple-600 z-50 transform -translate-x-full';
            document.body.appendChild(loader);

            // Animate the loader
            setTimeout(() => {
                loader.style.transform = 'translateX(0)';
                loader.style.transition = 'transform 0.3s ease-out';
            }, 10);

            return loader;
        }

        function hidePageLoader() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.style.transform = 'translateX(100%)';
                setTimeout(() => loader.remove(), 300);
            }
        }

        // Auto-hide mobile sidebar when clicking links
        document.addEventListener('click', function(e) {
            if (e.target.matches('a[href^="#"]') && window.innerWidth < 1024) {
                // Dispatch event to close mobile sidebar
                setTimeout(() => {
                    const event = new CustomEvent('close-mobile-sidebar');
                    document.dispatchEvent(event);
                }, 100);
            }
        });

        // Add ripple effect to buttons
        function createRipple(event) {
            const button = event.currentTarget;
            const circle = document.createElement('span');
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
            circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
            circle.classList.add('ripple');

            const ripple = button.getElementsByClassName('ripple')[0];
            if (ripple) {
                ripple.remove();
            }

            button.appendChild(circle);
        }

        // Apply ripple effect to buttons
        document.querySelectorAll('button, .btn').forEach(button => {
            button.addEventListener('click', createRipple);
        });

        // Add CSS for ripple effect
        const rippleStyle = document.createElement('style');
        rippleStyle.textContent = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }

            button, .btn {
                position: relative;
                overflow: hidden;
            }
        `;
        document.head.appendChild(rippleStyle);

        // Add focus management for accessibility
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });

        document.addEventListener('mousedown', function() {
            document.body.classList.remove('keyboard-navigation');
        });

        // Add focus styles for keyboard navigation
        const focusStyle = document.createElement('style');
        focusStyle.textContent = `
            body:not(.keyboard-navigation) *:focus {
                outline: none;
            }

            .keyboard-navigation *:focus {
                outline: 2px solid #f472b6;
                outline-offset: 2px;
            }
        `;
        document.head.appendChild(focusStyle);

        // Initialize tooltips (you can extend this with a proper tooltip library)
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            element.addEventListener('mouseenter', function(e) {
                const tooltip = document.createElement('div');
                tooltip.className = 'absolute z-50 px-3 py-2 text-sm bg-black text-white rounded-lg shadow-lg pointer-events-none';
                tooltip.textContent = this.dataset.tooltip;
                tooltip.id = 'tooltip-' + Math.random().toString(36).substr(2, 9);

                document.body.appendChild(tooltip);

                const rect = this.getBoundingClientRect();
                tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

                // Store tooltip reference
                this._tooltip = tooltip;
            });

            element.addEventListener('mouseleave', function() {
                if (this._tooltip) {
                    this._tooltip.remove();
                    this._tooltip = null;
                }
            });
        });
    </script>
</body>
</html>lg'" 
                                       :style="item.id === activeItem ? 'color: #f472b6' : ''"></i>
                                </div>
                                <span class="font-medium" x-text="item.label"></span>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-6 border-t border-white/10">
            <!-- Upgrade Card -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-pink-500/20 to-purple-500/20 border border-pink-500/30 mb-4">
                <div class="text-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-crown text-white text-xl"></i>
                    </div>
                    <h3 class="font-semibold mb-1">Upgrade to Pro</h3>
                    <p class="text-xs text-white/70 mb-3">Unlock premium features</p>
                    <button class="w-full py-2.5 bg-gradient-to-r from-pink-500 to-purple-600 rounded-xl text-sm font-medium hover:shadow-lg hover:scale-105 transition-all duration-200">
                        Upgrade Now
                    </button>
                </div>
            </div>

            <!-- User Card -->
            <div class="flex items-center space-x-3 p-3 glass-dark rounded-xl">
                <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center font-bold">
                    J
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm truncate">John Doe</div>
                    <div class="text-xs text-white/60 truncate">Super Admin</div>
                </div>
                <button class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                    <i class="fas fa-ellipsis-v text-xs"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="mobileSidebarOpen"
         @click="mobileSidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="lg:hidden fixed inset-0 z-40 bg-black/60 backdrop-blur-sm">
    </div>

    <!-- Mobile Sidebar -->
    <aside x-show="mobileSidebarOpen"
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           class="lg:hidden fixed top-16 left-0 bottom-0 w-80 glass z-50 flex flex-col">
        
        <nav class="flex-1 p-6 overflow-y-auto scrollbar-none" x-data="sidebarApp()">
            <!-- Mobile menu content (same as desktop) -->
            <div class="space-y-2">
                <template x-for="(item, index) in menuItems" :key="index">
                    <div class="animate__animated animate__fadeInLeft" :style="'animation-delay: ' + (index * 0.05) + 's'">
                        <!-- Menu item with children -->
                        <div x-show="item.children && item.children.length > 0" 
                             x-data="{ expanded: false }">
                            <button @click="expanded = !expanded"
                                    class="w-full flex items-center justify-between p-3 rounded-xl transition-all duration-300 hover:bg-white/10 group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                        <i :class="item.icon + ' text-lg'"></i>
                                    </div>
                                    <span class="font-medium" x-text="item.label"></span>
                                </div>
                                <i class="fas fa-chevron-right text-xs transition-transform duration-300"
                                   :class="expanded ? 'rotate-90' : ''"></i>
                            </button>
                            
                            <div x-show="expanded"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="ml-6 mt-2 space-y-1">
                                <template x-for="child in item.children" :key="child.id">
                                    <a :href="child.route" 
                                       @click="mobileSidebarOpen = false"
                                       class="flex items-center space-x-3 p-2.5 rounded-lg hover:bg-white/10 transition-all duration-200 group text-white/80 hover:text-white">
                                        <i :class="child.icon + ' text-sm w-4'" 
                                           class="group-hover:scale-110 transition-transform"></i>
                                        <span class="text-sm" x-text="child.label"></span>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- Single menu item -->
                        <div x-show="!item.children || item.children.length === 0">
                            <a :href="item.route" 
                               @click="mobileSidebarOpen = false"
                               class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 hover:bg-white/10 group">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                    <i :class="item.icon + ' text-