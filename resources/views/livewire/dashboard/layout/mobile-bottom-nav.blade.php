@php
    $currentRoute = request()->route()?->getName() ?? 'dashboard';
    $mobileMenuItems = [
        [
            'label' => 'Home',
            'icon' => 'fas fa-home',
            'route' => 'dashboard',
            'active' => $currentRoute === 'dashboard'
        ],
        [
            'label' => 'Courses',
            'icon' => 'fas fa-book',
            'route' => auth()->user()?->hasRole('student') ? 'student.enrolled-courses' : 'my-course',
            'active' => in_array($currentRoute, ['student.enrolled-courses', 'my-course', 'all-course', 'courses.available'])
        ],
        [
            'label' => 'Community',
            'icon' => 'fas fa-users',
            'route' => 'community.center',
            'active' => str_contains($currentRoute, 'community.')
        ],
        [
            'label' => 'Profile',
            'icon' => 'fas fa-user',
            'route' => 'profile.view',
            'active' => in_array($currentRoute, ['profile.view', 'profile.edit', 'settings'])
        ],
        [
            'label' => 'More',
            'icon' => 'fas fa-ellipsis-h',
            'route' => '#',
            'active' => false,
            'isMore' => true
        ]
    ];
@endphp

<!-- Mobile Bottom Navigation -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-themed-secondary shadow-lg border-t border-themed-primary z-40 transition-colors duration-300">
    <div class="flex items-center justify-around h-16 px-2">
        @foreach ($mobileMenuItems as $item)
            @if (isset($item['isMore']) && $item['isMore'])
                <!-- More Menu Button -->
                <div class="flex-1" x-data="{ moreMenuOpen: false }">
                    <button 
                        @click="moreMenuOpen = !moreMenuOpen"
                        class="flex flex-col items-center justify-center w-full h-full p-2 rounded-lg transition-colors duration-200 {{ $item['active'] ? 'accent-themed-primary' : 'text-themed-secondary' }} hover:accent-themed-primary hover:bg-accent-themed-primary hover:bg-opacity-10">
                        <i class="{{ $item['icon'] }} text-lg mb-1"></i>
                        <span class="text-xs font-medium">{{ $item['label'] }}</span>
                    </button>

                    <!-- More Menu Overlay -->
                    <div 
                        x-show="moreMenuOpen" 
                        @click.away="moreMenuOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-70 z-50"
                        style="display: none;">
                        <div class="absolute bottom-16 left-0 right-0">
                            <div class="bg-themed-secondary mx-4 rounded-t-xl shadow-xl border border-themed-primary max-h-96 overflow-y-auto transition-colors duration-300">
                                <div class="p-4 border-b border-themed-primary transition-colors duration-300">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-themed-primary transition-colors duration-300">Quick Access</h3>
                                        <button 
                                            @click="moreMenuOpen = false"
                                            class="p-2 rounded-lg hover:bg-themed-tertiary transition-colors">
                                            <i class="fas fa-times text-themed-tertiary"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="p-2 space-y-1">
                                    @if(auth()->user())
                                        <!-- Quick Actions -->
                                        <a href="{{ route('marketplace.browse') }}" 
                                           class="flex items-center p-3 rounded-lg hover:bg-themed-tertiary transition-colors"
                                           @click="moreMenuOpen = false">
                                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mr-3 transition-colors duration-300">
                                                <i class="fas fa-store text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">Marketplace</p>
                                                <p class="text-xs text-themed-secondary transition-colors duration-300">Browse courses & resources</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('student.certificates.index') }}" 
                                           class="flex items-center p-3 rounded-lg hover:bg-themed-tertiary transition-colors"
                                           @click="moreMenuOpen = false">
                                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center mr-3 transition-colors duration-300">
                                                <i class="fas fa-certificate text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">Certificates</p>
                                                <p class="text-xs text-themed-secondary transition-colors duration-300">View your achievements</p>
                                            </div>
                                        </a>

                                        @if(auth()->user()->hasRole(['instructor', 'academy_admin', 'super_admin']))
                                            <a href="{{ route('cbt.management') }}" 
                                               class="flex items-center p-3 rounded-lg hover:bg-themed-tertiary transition-colors"
                                               @click="moreMenuOpen = false">
                                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mr-3 transition-colors duration-300">
                                                    <i class="fas fa-laptop-code text-blue-600 dark:text-blue-400"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-themed-primary transition-colors duration-300">CBT Management</p>
                                                    <p class="text-xs text-themed-secondary transition-colors duration-300">Manage assessments</p>
                                                </div>
                                            </a>
                                        @endif

                                        <a href="{{ route('search.job') }}" 
                                           class="flex items-center p-3 rounded-lg hover:bg-themed-tertiary transition-colors"
                                           @click="moreMenuOpen = false">
                                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center mr-3 transition-colors duration-300">
                                                <i class="fas fa-briefcase text-orange-600 dark:text-orange-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">Job Search</p>
                                                <p class="text-xs text-themed-secondary transition-colors duration-300">Find career opportunities</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('help.support') }}" 
                                           class="flex items-center p-3 rounded-lg hover:bg-themed-tertiary transition-colors"
                                           @click="moreMenuOpen = false">
                                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center mr-3 transition-colors duration-300">
                                                <i class="fas fa-headset text-red-600 dark:text-red-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">Help & Support</p>
                                                <p class="text-xs text-themed-secondary transition-colors duration-300">Get assistance</p>
                                            </div>
                                        </a>

                                        <div class="border-t border-themed-primary my-2 transition-colors duration-300"></div>

                                        <!-- Settings -->
                                        <a href="{{ route('settings') }}" 
                                           class="flex items-center p-3 rounded-lg hover:bg-themed-tertiary transition-colors"
                                           @click="moreMenuOpen = false">
                                            <div class="w-10 h-10 bg-themed-tertiary rounded-lg flex items-center justify-center mr-3 transition-colors duration-300">
                                                <i class="fas fa-cog text-themed-secondary"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">Settings</p>
                                                <p class="text-xs text-themed-secondary transition-colors duration-300">Account preferences</p>
                                            </div>
                                        </a>

                                        <!-- Full Menu -->
                                        <button 
                                            @click="moreMenuOpen = false; toggleSidebar()"
                                            class="flex items-center w-full p-3 rounded-lg hover:bg-themed-tertiary transition-colors">
                                            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center mr-3 transition-colors duration-300">
                                                <i class="fas fa-bars text-indigo-600 dark:text-indigo-400"></i>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">Full Menu</p>
                                                <p class="text-xs text-themed-secondary transition-colors duration-300">Access all features</p>
                                            </div>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" 
                                           class="flex items-center p-3 rounded-lg hover:bg-themed-tertiary transition-colors"
                                           @click="moreMenuOpen = false">
                                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mr-3 transition-colors duration-300">
                                                <i class="fas fa-sign-in-alt text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">Login</p>
                                                <p class="text-xs text-themed-secondary transition-colors duration-300">Access your account</p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Regular Menu Item -->
                <div class="flex-1">
                    <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}"
                       class="flex flex-col items-center justify-center w-full h-full p-2 rounded-lg transition-all duration-200 {{ $item['active'] ? 'accent-themed-primary bg-accent-themed-primary bg-opacity-10' : 'text-themed-secondary' }} hover:accent-themed-primary hover:bg-accent-themed-primary hover:bg-opacity-10"
                       wire:navigate>
                        <div class="relative">
                            <i class="{{ $item['icon'] }} text-lg mb-1"></i>
                            @if($item['label'] === 'Home' && auth()->user()?->unreadNotifications()?->count() > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                                    {{ auth()->user()->unreadNotifications()->count() > 9 ? '9+' : auth()->user()->unreadNotifications()->count() }}
                                </span>
                            @endif
                        </div>
                        <span class="text-xs font-medium">{{ $item['label'] }}</span>
                    </a>
                </div>
            @endif
        @endforeach
    </div>
</nav>