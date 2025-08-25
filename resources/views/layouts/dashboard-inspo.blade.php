<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BootKode - Modern Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-effect { 
            backdrop-filter: blur(20px); 
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-effect { 
            background: rgba(17, 24, 39, 0.8);
            border: 1px solid rgba(55, 65, 81, 0.3);
        }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); }
        .mobile-nav-item { transition: all 0.2s ease; }
        .mobile-nav-item.active { transform: translateY(-4px); }
        .stat-card { 
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
        }
        .menu-item { transition: all 0.2s ease; }
        .menu-item:hover { background: rgba(59, 130, 246, 0.1); }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-inter">
    <!-- Mobile Header -->
    <header class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center space-x-3">
                <button id="mobileMenuBtn" class="p-2 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-code text-blue-600 text-xl"></i>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">BootKode</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button class="p-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 relative">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">3</span>
                </button>
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                    <span class="text-white text-sm font-medium">JD</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Overlay Menu -->
    <div id="mobileOverlay" class="lg:hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-sm opacity-0 invisible transition-all duration-300">
        <div id="mobileMenu" class="fixed left-0 top-0 h-full w-80 bg-white dark:bg-gray-800 transform -translate-x-full transition-transform duration-300">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-code text-blue-600 text-xl"></i>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">BootKode</span>
                    </div>
                    <button id="closeMobileMenu" class="p-2 rounded-lg text-gray-500 dark:text-gray-400">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="mt-4 flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
                        <span class="text-white font-medium">JD</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">John Doe</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Super Admin</p>
                    </div>
                </div>
            </div>
            <nav class="p-6 space-y-2 overflow-y-auto h-full pb-32">
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-tachometer-alt text-blue-600"></i>
                        <span class="font-medium text-gray-900 dark:text-white">Dashboard</span>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-book text-green-600"></i>
                        <span class="font-medium text-gray-900 dark:text-white">Courses</span>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-users text-purple-600"></i>
                        <span class="font-medium text-gray-900 dark:text-white">Students</span>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-certificate text-yellow-600"></i>
                        <span class="font-medium text-gray-900 dark:text-white">Certificates</span>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-chart-bar text-red-600"></i>
                        <span class="font-medium text-gray-900 dark:text-white">Analytics</span>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <aside class="hidden lg:block fixed left-0 top-0 h-full w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center">
                    <i class="fas fa-code text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">BootKode</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Learning Platform</p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="mb-6">
                <div class="relative">
                    <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700 border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <nav class="space-y-2">
                <div class="menu-item p-3 rounded-xl cursor-pointer bg-blue-50 dark:bg-blue-900/30">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-tachometer-alt text-blue-600"></i>
                        <span class="font-medium text-blue-600">Dashboard</span>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-book text-green-600"></i>
                            <span class="font-medium text-gray-900 dark:text-white">Course Management</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-users text-purple-600"></i>
                            <span class="font-medium text-gray-900 dark:text-white">User Management</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-certificate text-yellow-600"></i>
                            <span class="font-medium text-gray-900 dark:text-white">Certification</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-chart-bar text-red-600"></i>
                        <span class="font-medium text-gray-900 dark:text-white">Analytics</span>
                    </div>
                </div>
                <div class="menu-item p-3 rounded-xl cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-graduation-cap text-indigo-600"></i>
                        <span class="font-medium text-gray-900 dark:text-white">Learning Hub</span>
                    </div>
                </div>
            </nav>
        </div>

        <div class="absolute bottom-6 left-6 right-6">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
                        <span class="text-white font-medium">JD</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">John Doe</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Super Admin</p>
                    </div>
                    <button class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-72 pt-20 lg:pt-0">
        <!-- Desktop Header -->
        <header class="hidden lg:flex items-center justify-between p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Good morning, John!</h2>
                <p class="text-gray-600 dark:text-gray-400">Here's what's happening with your platform today.</p>
            </div>
            <div class="flex items-center space-x-4">
                <button class="p-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 relative">
                    <i class="fas fa-bell"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">3</span>
                </button>
                <button class="p-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline"></i>
                </button>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="p-6 lg:p-8 space-y-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <div class="stat-card p-6 rounded-2xl border border-gray-200 dark:border-gray-700 card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+12%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">2,847</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Total Students</p>
                </div>

                <div class="stat-card p-6 rounded-2xl border border-gray-200 dark:border-gray-700 card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="fas fa-book text-green-600 text-xl"></i>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+8%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">156</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Active Courses</p>
                </div>

                <div class="stat-card p-6 rounded-2xl border border-gray-200 dark:border-gray-700 card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                            <i class="fas fa-certificate text-yellow-600 text-xl"></i>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+23%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">1,284</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Certificates Issued</p>
                </div>

                <div class="stat-card p-6 rounded-2xl border border-gray-200 dark:border-gray-700 card-hover">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+15%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">$24.8k</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Monthly Revenue</p>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Left Column - Charts and Activity -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
                            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</button>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <i class="fas fa-user-plus text-green-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900 dark:text-white font-medium">New student enrolled</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">Sarah Connor joined "Web Development Bootcamp"</p>
                                </div>
                                <span class="text-gray-400 text-sm">2m ago</span>
                            </div>
                            <div class="flex items-center space-x-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <i class="fas fa-certificate text-blue-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900 dark:text-white font-medium">Certificate issued</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">React Advanced Concepts - John Smith</p>
                                </div>
                                <span class="text-gray-400 text-sm">5m ago</span>
                            </div>
                            <div class="flex items-center space-x-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                    <i class="fas fa-book text-purple-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900 dark:text-white font-medium">Course published</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">"Python for Data Science" is now live</p>
                                </div>
                                <span class="text-gray-400 text-sm">12m ago</span>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Courses -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Popular Courses</h3>
                            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">Manage</button>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 rounded-xl border border-gray-200 dark:border-gray-600 card-hover cursor-pointer">
                                <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
                                    <i class="fas fa-code text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Complete Web Development Bootcamp</h4>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">847 students • 4.9 ⭐</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900 dark:text-white">$199</p>
                                    <p class="text-green-500 text-sm">Active</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 rounded-xl border border-gray-200 dark:border-gray-600 card-hover cursor-pointer">
                                <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
                                    <i class="fas fa-mobile-alt text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">React Native Mobile Development</h4>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">623 students • 4.8 ⭐</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900 dark:text-white">$149</p>
                                    <p class="text-green-500 text-sm">Active</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 rounded-xl border border-gray-200 dark:border-gray-600 card-hover cursor-pointer">
                                <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center">
                                    <i class="fas fa-database text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Python Data Science Masterclass</h4>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">492 students • 4.7 ⭐</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900 dark:text-white">$179</p>
                                    <p class="text-green-500 text-sm">Active</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions and Stats -->
                <div class="space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Quick Actions</h3>
                        <div class="space-y-3">
                            <button class="w-full p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors flex items-center space-x-3">
                                <i class="fas fa-plus-circle"></i>
                                <span class="font-medium">Create New Course</span>
                            </button>
                            <button class="w-full p-4 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors flex items-center space-x-3">
                                <i class="fas fa-user-plus"></i>
                                <span class="font-medium">Add New User</span>
                            </button>
                            <button class="w-full p-4 rounded-xl bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors flex items-center space-x-3">
                                <i class="fas fa-certificate"></i>
                                <span class="font-medium">Issue Certificate</span>
                            </button>
                            <button class="w-full p-4 rounded-xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors flex items-center space-x-3">
                                <i class="fas fa-chart-bar"></i>
                                <span class="font-medium">View Analytics</span>
                            </button>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">System Status</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                    <span class="text-gray-900 dark:text-white">API Services</span>
                                </div>
                                <span class="text-green-500 text-sm font-medium">Operational</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                    <span class="text-gray-900 dark:text-white">Database</span>
                                </div>
                                <span class="text-green-500 text-sm font-medium">Operational</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                    <span class="text-gray-900 dark:text-white">File Storage</span>
                                </div>
                                <span class="text-yellow-500 text-sm font-medium">Degraded</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                    <span class="text-gray-900 dark:text-white">CDN</span>
                                </div>
                                <span class="text-green-500 text-sm font-medium">Operational</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Pending Approvals</h3>
                            <span class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-medium px-2 py-1 rounded-full">5</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                <div>
                                    <p class="text-gray-900 dark:text-white font-medium text-sm">Certificate Request</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs">Alice Johnson - React Course</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-700 text-xs font-medium">Review</button>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                <div>
                                    <p class="text-gray-900 dark:text-white font-medium text-sm">Course Submission</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs">Advanced JavaScript - Mike Davis</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-700 text-xs font-medium">Review</button>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                <div>
                                    <p class="text-gray-900 dark:text-white font-medium text-sm">Instructor Application</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs">Emma Wilson</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-700 text-xs font-medium">Review</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-700 z-50">
        <div class="flex items-center justify-around py-2">
            <button class="mobile-nav-item active flex flex-col items-center p-3 rounded-2xl bg-blue-50 dark:bg-blue-900/30">
                <i class="fas fa-home text-blue-600 mb-1"></i>
                <span class="text-xs text-blue-600 font-medium">Dashboard</span>
            </button>
            <button class="mobile-nav-item flex flex-col items-center p-3 rounded-2xl">
                <i class="fas fa-book text-gray-400 mb-1"></i>
                <span class="text-xs text-gray-400">Courses</span>
            </button>
            <button class="mobile-nav-item flex flex-col items-center p-3 rounded-2xl">
                <i class="fas fa-users text-gray-400 mb-1"></i>
                <span class="text-xs text-gray-400">Users</span>
            </button>
            <button class="mobile-nav-item flex flex-col items-center p-3 rounded-2xl relative">
                <i class="fas fa-bell text-gray-400 mb-1"></i>
                <span class="text-xs text-gray-400">Alerts</span>
                <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-xs">3</span>
                </div>
            </button>
            <button class="mobile-nav-item flex flex-col items-center p-3 rounded-2xl">
                <i class="fas fa-cog text-gray-400 mb-1"></i>
                <span class="text-xs text-gray-400">Settings</span>
            </button>
        </div>
    </nav>

    <script>
        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mobileMenu = document.getElementById('mobileMenu');
        const closeMobileMenu = document.getElementById('closeMobileMenu');

        function openMobileMenu() {
            mobileOverlay.classList.remove('opacity-0', 'invisible');
            mobileOverlay.classList.add('opacity-100', 'visible');
            mobileMenu.classList.remove('-translate-x-full');
            mobileMenu.classList.add('translate-x-0');
        }

        function closeMobileMenuFunc() {
            mobileOverlay.classList.add('opacity-0', 'invisible');
            mobileOverlay.classList.remove('opacity-100', 'visible');
            mobileMenu.classList.add('-translate-x-full');
            mobileMenu.classList.remove('translate-x-0');
        }

        mobileMenuBtn.addEventListener('click', openMobileMenu);
        closeMobileMenu.addEventListener('click', closeMobileMenuFunc);
        mobileOverlay.addEventListener('click', function(e) {
            if (e.target === mobileOverlay) {
                closeMobileMenuFunc();
            }
        });

        // Mobile navigation active state
        document.querySelectorAll('.mobile-nav-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.mobile-nav-item').forEach(nav => {
                    nav.classList.remove('active', 'bg-blue-50', 'dark:bg-blue-900/30');
                    nav.querySelector('i').classList.remove('text-blue-600');
                    nav.querySelector('i').classList.add('text-gray-400');
                    nav.querySelector('span').classList.remove('text-blue-600');
                    nav.querySelector('span').classList.add('text-gray-400');
                });
                
                this.classList.add('active', 'bg-blue-50', 'dark:bg-blue-900/30');
                this.querySelector('i').classList.add('text-blue-600');
                this.querySelector('i').classList.remove('text-gray-400');
                this.querySelector('span').classList.add('text-blue-600');
                this.querySelector('span').classList.remove('text-gray-400');
            });
        });

        // Dark mode toggle (basic implementation)
        const darkModeToggle = document.querySelector('[class*="fa-moon"], [class*="fa-sun"]').parentElement;
        darkModeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            const moonIcon = document.querySelector('.fa-moon');
            const sunIcon = document.querySelector('.fa-sun');
            
            if (document.documentElement.classList.contains('dark')) {
                moonIcon.classList.add('hidden');
                sunIcon.classList.remove('hidden');
            } else {
                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
            }
        });

        // Add smooth hover animations for cards
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '';
            });
        });

        // Simulate real-time updates
        function updateStats() {
            const stats = [
                { element: document.querySelector('.stat-card:nth-child(1) h3'), base: 2847, variance: 5 },
                { element: document.querySelector('.stat-card:nth-child(2) h3'), base: 156, variance: 2 },
                { element: document.querySelector('.stat-card:nth-child(3) h3'), base: 1284, variance: 8 },
                { element: document.querySelector('.stat-card:nth-child(4) h3'), base: 24.8, variance: 0.3, prefix: ', suffix: 'k' }
            ];

            stats.forEach(stat => {
                if (stat.element) {
                    const change = (Math.random() - 0.5) * stat.variance;
                    const newValue = stat.base + change;
                    const displayValue = stat.prefix || '';
                    
                    if (stat.suffix === 'k') {
                        stat.element.textContent = displayValue + newValue.toFixed(1) + stat.suffix;
                    } else {
                        stat.element.textContent = displayValue + Math.round(newValue).toLocaleString();
                    }
                }
            });
        }

        // Update stats every 30 seconds for demo
        setInterval(updateStats, 30000);

        // Add notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transform transition-all duration-300 translate-x-full`;
            
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            notification.classList.add(bgColor, 'text-white');
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Demo notifications
        setTimeout(() => showNotification('Welcome to your dashboard!', 'success'), 1000);
    </script>
</body>
</html>