<header class="bg-white shadow-sm sticky top-0 z-50" 
        x-data="{ 
            open: false, 
            dropdowns: {
                courses: false,
                roadmaps: false,
                mentorship: false,
                community: false
            }
        }">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="#" class="flex items-center space-x-2 transform transition-transform hover:scale-105">
                    <div class="flex flex-row gap-3 bg-gradient-to-r from-blue-50 to-purple-100 p-2 rounded-lg shadow-sm">
                        <svg class="h-8 w-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                        <span class="text-2xl font-bold text-gray-900">Boot<span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-800 to-purple-400">Kode</span></span>
                    </div>
                </a>
            </div>


            <div class="md:hidden px-3 py-3">
                <div class="relative">
                    <input type="text" placeholder="Search courses..." class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex md:items-center md:space-x-1">
                <!-- Courses Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-4 py-2 text-gray-700 hover:text-blue-500 font-medium hover:uppercase flex items-center space-x-1 transition-all duration-300 relative group">
                        <span>Courses</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all duration-300 group-hover:w-3/4"></span>
                    </button>
                    <div class="absolute left-0 w-56 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50 origin-top-left transition-all duration-300"
                         x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         style="display: none;"
                         @click.away="open = false">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-laptop-code text-blue-400 mr-3"></i>
                                Frontend Development
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-server text-blue-400 mr-3"></i>
                                Backend Development
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-mobile-alt text-blue-400 mr-3"></i>
                                Mobile Development
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-database text-blue-400 mr-3"></i>
                                Data Science
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Roadmaps Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-4 py-2 text-gray-700 hover:text-blue-400 font-medium hover:uppercase flex items-center space-x-1 transition-all duration-300 relative group">
                        <span>Roadmaps</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all duration-300 group-hover:w-3/4"></span>
                    </button>
                    <div class="absolute left-0 w-64 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50 origin-top-left transition-all duration-300"
                         x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         style="display: none;"
                         @click.away="open = false">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-map-signs text-blue-400 mr-3"></i>
                                Career Paths
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-graduation-cap text-blue-400 mr-3"></i>
                                Learning Tracks
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-certificate text-blue-400 mr-3"></i>
                                Certification Guide
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-chart-line text-blue-400 mr-3"></i>
                                Skill Progression
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Mentorship Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-4 py-2 text-gray-700 hover:text-blue-400 font-medium hover:uppercase flex items-center space-x-1 transition-all duration-300 relative group">
                        <span>Mentorship</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                        <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all duration-300 group-hover:w-3/4"></span>
                    </button>
                    <div class="absolute left-0 w-56 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50 origin-top-left transition-all duration-300"
                         x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         style="display: none;"
                         @click.away="open = false">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-users text-blue-400 mr-3"></i>
                                Find a Mentor
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-calendar-check text-blue-400 mr-3"></i>
                                Book Sessions
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-code text-blue-400 mr-3"></i>
                                Code Reviews
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center rounded-lg mx-2 my-1">
                                <i class="fas fa-briefcase text-blue-400 mr-3"></i>
                                Career Guidance
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Community Link -->
                <a href="#" class="px-4 py-2 text-gray-700 hover:text-blue-400 font-medium hover:uppercase transition-all duration-300 relative nav-link group">
                    <span class="relative">
                        Community
                        <span class="absolute -top-1 -right-5 bg-secondary text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">New</span>
                    </span>
                    <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all duration-300 group-hover:w-3/4"></span>
                </a>
                
                <!-- About Link -->
                <a href="#" class="px-4 py-2 text-gray-700 hover:text-blue-400 font-medium hover:uppercase transition-all duration-300 relative nav-link group">
                    <span>About Us</span>
                    <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-400 transition-all duration-300 group-hover:w-3/4"></span>
                </a>
            </div>

            <!-- Right Section - Auth Buttons & Search -->
            <div class="hidden md:flex items-center space-x-4">
                <div class="relative" x-data="{ searchOpen: false }">
                    <button @click="searchOpen = !searchOpen" class="p-2 text-gray-600 hover:text-blue-400 rounded-full transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                    <div x-show="searchOpen" class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl z-50 p-2"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="searchOpen = false">
                        <input type="text" placeholder="Search courses..." class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                    </div>
                </div>
                
                <a href="#" class="text-gray-600 hover:text-blue-400 font-medium hover:uppercase px-4 py-2 rounded-lg transition-colors duration-300 group">
                    <i class="fas fa-sign-in-alt mr-2 transition-transform group-hover:translate-x-0.5"></i>Log in
                </a>
                <a href="#" class="bg-gradient-to-r from-blue-400 to-purple-400 text-black font-medium hover:uppercase px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 flex items-center animate-pulse-custom">
                    <i class="fas fa-user-plus mr-2"></i>Register
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-600 hover:text-blue-400 focus:outline-none p-2 rounded-lg transition-colors">
                    <i class="fas fa-bars text-xl" x-show="!open"></i>
                    <i class="fas fa-times text-xl" x-show="open" style="display: none;"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile menu - Slide-in panel -->
    <div class="md:hidden fixed inset-0 z-40 overflow-y-auto" 
         x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>
        
        <!-- Panel -->
        <div class="relative bg-white w-80 max-w-full h-full ml-auto shadow-2xl transform transition-transform duration-300"
             :class="open ? 'translate-x-0' : 'translate-x-full'">
            <div class="flex justify-end p-4">
                <button @click="open = false" class="text-gray-600 hover:text-blue-400 p-2 rounded-full">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="px-4 pt-2 pb-5 space-y-1">
                <!-- Mobile: Courses Dropdown -->
                <div class="relative">
                    <button @click="dropdowns.courses = !dropdowns.courses" class="w-full flex justify-between items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50">
                        <div class="flex items-center">
                            <i class="fas fa-book-open text-blue-400 mr-3"></i>
                            <span class="font-medium hover:uppercase">Courses</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': dropdowns.courses}"></i>
                    </button>
                    <div class="mt-1 ml-8 space-y-1" x-show="dropdowns.courses" x-collapse
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-96"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 max-h-96"
                         x-transition:leave-end="opacity-0 max-h-0"
                         style="display: none;">
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-laptop-code text-blue-400 mr-3 text-sm"></i>
                            Frontend Development
                        </a>
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-server text-blue-400 mr-3 text-sm"></i>
                            Backend Development
                        </a>
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-mobile-alt text-blue-400 mr-3 text-sm"></i>
                            Mobile Development
                        </a>
                    </div>
                </div>
                
                <!-- Mobile: Roadmaps Dropdown -->
                <div class="relative">
                    <button @click="dropdowns.roadmaps = !dropdowns.roadmaps" class="w-full flex justify-between items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50">
                        <div class="flex items-center">
                            <i class="fas fa-map-marked-alt text-blue-400 mr-3"></i>
                            <span class="font-medium hover:uppercase">Roadmaps</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': dropdowns.roadmaps}"></i>
                    </button>
                    <div class="mt-1 ml-8 space-y-1" x-show="dropdowns.roadmaps" x-collapse
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-96"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 max-h-96"
                         x-transition:leave-end="opacity-0 max-h-0"
                         style="display: none;">
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-map-signs text-blue-400 mr-3 text-sm"></i>
                            Career Paths
                        </a>
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-graduation-cap text-blue-400 mr-3 text-sm"></i>
                            Learning Tracks
                        </a>
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-certificate text-blue-400 mr-3 text-sm"></i>
                            Certification Guide
                        </a>
                    </div>
                </div>
                
                <!-- Mobile: Mentorship Dropdown -->
                <div class="relative">
                    <button @click="dropdowns.mentorship = !dropdowns.mentorship" class="w-full flex justify-between items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50">
                        <div class="flex items-center">
                            <i class="fas fa-hands-helping text-blue-400 mr-3"></i>
                            <span class="font-medium hover:uppercase">Mentorship</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': dropdowns.mentorship}"></i>
                    </button>
                    <div class="mt-1 ml-8 space-y-1" x-show="dropdowns.mentorship" x-collapse
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-96"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 max-h-96"
                         x-transition:leave-end="opacity-0 max-h-0"
                         style="display: none;">
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-users text-blue-400 mr-3 text-sm"></i>
                            Find a Mentor
                        </a>
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-calendar-check text-blue-400 mr-3 text-sm"></i>
                            Book Sessions
                        </a>
                        <a href="#" class="block px-3 py-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-400 transition-colors duration-200 flex items-center">
                            <i class="fas fa-code text-blue-400 mr-3 text-sm"></i>
                            Code Reviews
                        </a>
                    </div>
                </div>
                
                <!-- Mobile: Community Link -->
                <a href="#" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50">
                    <i class="fas fa-users text-blue-400 mr-3"></i>
                    <span class="font-medium hover:uppercase">Community</span>
                    <span class="ml-2 bg-secondary text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">New</span>
                </a>
                
                <!-- Mobile: About Link -->
                <a href="#" class="flex items-center px-3 py-3 text-gray-700 rounded-lg hover:bg-blue-50">
                    <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                    <span class="font-medium hover:uppercase">About Us</span>
                </a>
                
                <!-- Mobile: Search -->
                <div class="px-3 py-3">
                    <div class="relative">
                        <input type="text" placeholder="Search courses..." class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Mobile: Auth Buttons -->
                <div class="pt-4 border-t border-gray-800 mt-4">
                    <a href="#" class="block w-full text-center px-4 py-3 text-gray-700 font-medium hover:uppercase rounded-lg border border-gray-300 mb-3 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Log in
                    </a>
                    <a href="#" class="block w-full text-center bg-gradient-to-r from-blue-400 to-purple-300 text-black font-medium hover:uppercase px-4 py-3.5 rounded-lg shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>