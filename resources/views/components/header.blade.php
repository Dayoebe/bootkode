<header class="bg-white shadow-sm sticky top-0 z-50" x-data="{
    mobileOpen: false,
    authOpen: false,
    dropdowns: {
        courses: false,
        roadmaps: false,
        mentorship: false,
        marketplace: false,
        community: false,
        userMenu: false
    }
}">
    <nav class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/" class="flex items-center space-x-2 hover:scale-105 transition-transform">
                    <div class="flex gap-2 bg-gradient-to-r from-blue-50 to-red-100 p-2 rounded-lg shadow-sm">
                        <i class="fas fa-code text-xl text-blue-500"></i>
                        <span class="text-xl font-bold text-gray-900">
                            Boot<span class="text-blue-900">Kode</span>
                        </span>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-1">
                <!-- Courses Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-3 py-2 text-sm text-gray-700 hover:text-blue-500 font-medium flex items-center space-x-1 transition-all relative group">
                        <span>Courses</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                    </button>
                    <div class="absolute left-0 w-56 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50" x-show="open" x-transition style="display: none;" @click.away="open = false">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-laptop-code text-blue-400 mr-3"></i>
                                Frontend Development
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-server text-blue-400 mr-3"></i>
                                Backend Development
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-mobile-alt text-blue-400 mr-3"></i>
                                Mobile Development
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-database text-blue-400 mr-3"></i>
                                Data Science
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Marketplace Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium flex items-center space-x-1 transition-all relative group">
                        <span>Marketplace</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                    </button>
                    <div class="absolute left-0 w-56 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50" x-show="open" x-transition style="display: none;" @click.away="open = false">
                        <div class="py-1">
                            <a href="{{ route('marketplace.browse') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-store text-blue-400 mr-3"></i>
                                Browse All Products
                            </a>
                            <a href="{{ route('marketplace.categories') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-tags text-blue-400 mr-3"></i>
                                Categories
                            </a>
                            @auth
                                <a href="{{ route('marketplace.cart') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                    <i class="fas fa-shopping-cart text-blue-400 mr-3"></i>
                                    My Cart
                                </a>
                                <a href="{{ route('marketplace.purchases') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                    <i class="fas fa-shopping-bag text-blue-400 mr-3"></i>
                                    My Purchases
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Roadmaps Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium flex items-center space-x-1 transition-all relative group">
                        <span>Roadmaps</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                    </button>
                    <div class="absolute left-0 w-64 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50" x-show="open" x-transition style="display: none;" @click.away="open = false">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-map-signs text-blue-400 mr-3"></i>
                                Career Paths
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-graduation-cap text-blue-400 mr-3"></i>
                                Learning Tracks
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-certificate text-blue-400 mr-3"></i>
                                Certification Guide
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-chart-line text-blue-400 mr-3"></i>
                                Skill Progression
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mentorship Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium flex items-center space-x-1 transition-all relative group">
                        <span>Mentorship</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                    </button>
                    <div class="absolute left-0 w-56 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50" x-show="open" x-transition style="display: none;" @click.away="open = false">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-users text-blue-400 mr-3"></i>
                                Find a Mentor
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-calendar-check text-blue-400 mr-3"></i>
                                Book Sessions
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-code text-blue-400 mr-3"></i>
                                Code Reviews
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-briefcase text-blue-400 mr-3"></i>
                                Career Guidance
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Simple Links -->
                <a href="#" class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium transition-all relative group">
                    <span class="flex items-center">
                        Community
                        <span class="ml-1 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">New</span>
                    </span>
                    <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                </a>

                <a href="{{ route('about') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium transition-all relative group">
                    <span>About</span>
                    <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                </a>

                <a href="{{ route('contact') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium transition-all relative group">
                    <span>Contact</span>
                    <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                </a>

                <a href="{{ route('statistics') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium transition-all relative group">
                    <span>Statistics</span>
                    <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                </a>

                <a href="{{ route('guideline') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium transition-all relative group">
                    <span>Guideline</span>
                    <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                </a>

                <a href="{{ route('blog.index') }}" class="px-3 py-2 text-sm text-gray-700 hover:text-blue-400 font-medium transition-all relative group">
                    <span>Blog</span>
                    <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all group-hover:w-3/4"></span>
                </a>
            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-3">
                @auth
                    <!-- Authenticated User Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none hover:opacity-80 transition">
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="hidden md:inline text-sm text-gray-700 font-medium">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-50" style="display: none;">
                            <a href="{{ route(auth()->user()->getDashboardRouteName()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-500 transition">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-500 transition">
                                <i class="fas fa-user-cog mr-2"></i> Profile
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest Auth Dropdown -->
                    <div class="hidden md:block relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="bg-blue-500 hover:bg-blue-600 text-white font-medium px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <i class="fas fa-user"></i>
                            <span>Account</span>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-50" style="display: none;">
                            <a href="{{ route('login') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-500 transition flex items-center">
                                <i class="fas fa-sign-in-alt text-blue-500 mr-3"></i>
                                <span class="font-medium">Log In</span>
                            </a>
                            <a href="{{ route('register') }}" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-500 transition flex items-center">
                                <i class="fas fa-user-plus text-blue-500 mr-3"></i>
                                <span class="font-medium">Register</span>
                            </a>
                        </div>
                    </div>
                @endauth

                <!-- Mobile menu button -->
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden text-gray-600 hover:text-blue-500 focus:outline-none p-2 transition">
                    <i class="fas text-xl" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile menu -->
    <div class="lg:hidden fixed inset-0 z-40" x-show="mobileOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="mobileOpen = false"></div>

        <!-- Panel -->
        <div class="relative bg-white w-80 max-w-full h-full ml-auto shadow-2xl overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b">
                <span class="font-bold text-gray-800">Menu</span>
                <button @click="mobileOpen = false" class="text-gray-600 hover:text-blue-500 p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-4 space-y-2">
                <!-- Mobile: Courses Dropdown -->
                <div>
                    <button @click="dropdowns.courses = !dropdowns.courses" class="w-full flex justify-between items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                        <div class="flex items-center">
                            <i class="fas fa-book-open text-blue-500 mr-3"></i>
                            <span class="font-medium">Courses</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': dropdowns.courses }"></i>
                    </button>
                    <div class="mt-1 ml-8 space-y-1" x-show="dropdowns.courses" x-collapse style="display: none;">
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-laptop-code text-blue-500 mr-2"></i>
                            Frontend Development
                        </a>
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-server text-blue-500 mr-2"></i>
                            Backend Development
                        </a>
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-mobile-alt text-blue-500 mr-2"></i>
                            Mobile Development
                        </a>
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-database text-blue-500 mr-2"></i>
                            Data Science
                        </a>
                    </div>
                </div>

                <!-- Mobile: Marketplace Dropdown -->
                <div>
                    <button @click="dropdowns.marketplace = !dropdowns.marketplace" class="w-full flex justify-between items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                        <div class="flex items-center">
                            <i class="fas fa-store text-blue-500 mr-3"></i>
                            <span class="font-medium">Marketplace</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': dropdowns.marketplace }"></i>
                    </button>
                    <div class="mt-1 ml-8 space-y-1" x-show="dropdowns.marketplace" x-collapse style="display: none;">
                        <a href="{{ route('marketplace.browse') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-store text-blue-500 mr-2"></i>
                            Browse All
                        </a>
                        <a href="{{ route('marketplace.categories') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-tags text-blue-500 mr-2"></i>
                            Categories
                        </a>
                        @auth
                            <a href="{{ route('marketplace.cart') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                                <i class="fas fa-shopping-cart text-blue-500 mr-2"></i>
                                My Cart
                            </a>
                            <a href="{{ route('marketplace.purchases') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                                <i class="fas fa-shopping-bag text-blue-500 mr-2"></i>
                                My Purchases
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile: Roadmaps Dropdown -->
                <div>
                    <button @click="dropdowns.roadmaps = !dropdowns.roadmaps" class="w-full flex justify-between items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                        <div class="flex items-center">
                            <i class="fas fa-map-marked-alt text-blue-500 mr-3"></i>
                            <span class="font-medium">Roadmaps</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': dropdowns.roadmaps }"></i>
                    </button>
                    <div class="mt-1 ml-8 space-y-1" x-show="dropdowns.roadmaps" x-collapse style="display: none;">
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-map-signs text-blue-500 mr-2"></i>
                            Career Paths
                        </a>
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-graduation-cap text-blue-500 mr-2"></i>
                            Learning Tracks
                        </a>
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-certificate text-blue-500 mr-2"></i>
                            Certification
                        </a>
                    </div>
                </div>

                <!-- Mobile: Mentorship Dropdown -->
                <div>
                    <button @click="dropdowns.mentorship = !dropdowns.mentorship" class="w-full flex justify-between items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                        <div class="flex items-center">
                            <i class="fas fa-hands-helping text-blue-500 mr-3"></i>
                            <span class="font-medium">Mentorship</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': dropdowns.mentorship }"></i>
                    </button>
                    <div class="mt-1 ml-8 space-y-1" x-show="dropdowns.mentorship" x-collapse style="display: none;">
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-users text-blue-500 mr-2"></i>
                            Find a Mentor
                        </a>
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                            Book Sessions
                        </a>
                        <a href="#" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-500 transition">
                            <i class="fas fa-code text-blue-500 mr-2"></i>
                            Code Reviews
                        </a>
                    </div>
                </div>

                <!-- Mobile: Simple Links -->
                <a href="#" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-users text-blue-500 mr-3"></i>
                    <span class="font-medium">Community</span>
                    <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">New</span>
                </a>

                <a href="{{ route('about') }}" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                    <span class="font-medium">About</span>
                </a>

                <a href="{{ route('contact') }}" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-envelope text-blue-500 mr-3"></i>
                    <span class="font-medium">Contact</span>
                </a>

                <a href="{{ route('statistics') }}" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-chart-bar text-blue-500 mr-3"></i>
                    <span class="font-medium">Statistics</span>
                </a>

                <a href="{{ route('guideline') }}" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-book text-blue-500 mr-3"></i>
                    <span class="font-medium">Guideline</span>
                </a>

                <a href="{{ route('blog.index') }}" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-blog text-blue-500 mr-3"></i>
                    <span class="font-medium">Blog</span>
                </a>

                <!-- Mobile: Auth Section -->
                <div class="border-t pt-4 mt-4">
                    @auth
                        <div class="space-y-2">
                            <div class="flex items-center px-3 py-2 bg-blue-50 rounded-lg">
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <span class="ml-3 font-medium text-gray-800">{{ auth()->user()->name }}</span>
                            </div>
                            <a href="{{ route(auth()->user()->getDashboardRouteName()) }}" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                                <i class="fas fa-tachometer-alt text-blue-500 mr-3"></i>
                                <span class="font-medium">Dashboard</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50 transition">
                                <i class="fas fa-user-cog text-blue-500 mr-3"></i>
                                <span class="font-medium">Profile</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-3 py-3 text-red-600 rounded-lg hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt text-red-600 mr-3"></i>
                                    <span class="font-medium">Logout</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="space-y-2">
                            <a href="{{ route('login') }}" class="flex items-center justify-center px-4 py-3 border-2 border-blue-500 text-blue-500 rounded-lg hover:bg-blue-50 transition font-medium">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Log In
                            </a>
                            <a href="{{ route('register') }}" class="flex items-center justify-center px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium shadow-md">
                                <i class="fas fa-user-plus mr-2"></i>
                                Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>