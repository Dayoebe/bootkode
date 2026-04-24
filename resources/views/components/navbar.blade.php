@php
    $user = Auth::user();
    $unreadNotificationCount = $user?->unreadNotifications()?->count() ?? 0;
    $unreadChatCount = $user
        ? \App\Models\Messaging\DirectMessage::query()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->whereHas('conversation', fn($query) => $query->forParticipant($user))
            ->count()
        : 0;
@endphp

<header
    class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30 transition-colors duration-300">
    <div class="flex items-center justify-between h-16 px-4 lg:px-6">
        <!-- Left: Mobile Menu & Breadcrumbs -->
        <div class="flex items-center space-x-4">
            <!-- Mobile Hamburger -->
            <button @click="toggleSidebar()"
                class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                aria-label="Toggle sidebar">
                <i class="fas fa-bars text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- Breadcrumbs / Page Title -->
            <div class="hidden sm:block">
                <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <i class="fas fa-home"></i>
                    </a>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="text-gray-900 dark:text-white font-medium capitalize">
                        {{ request()->route()?->getName() === 'dashboard' ? 'Dashboard' : str_replace(['.', '-', '_'], ' ', request()->route()?->getName() ?? 'Page') }}
                    </span>
                </nav>
            </div>
        </div>

        <!-- Center: Search Bar (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-md mx-6">
            <div class="relative w-full">
                <input type="text" placeholder="Search courses, content, or users..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    x-data="{ searchQuery: '' }" x-model="searchQuery"
                    @keydown.enter="console.log('Search:', searchQuery)">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Right: Actions & User Menu -->
        <div class="flex items-center space-x-3">
            <!-- Quick Actions (Desktop) -->
            <div class="hidden lg:flex items-center space-x-2">
                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Notifications">
                        <i class="fas fa-bell text-gray-600 dark:text-gray-300"></i>
                        @if ($unreadNotificationCount > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
                        style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @if ($unreadNotificationCount > 0)
                                @foreach ($user->unreadNotifications()->take(5)->get() as $notification)
                                    <a href="{{ $notification->data['action_url'] ?? route('notifications') }}"
                                        class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-0">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            {{ $notification->data['message'] ?? 'New notification' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}</p>
                                    </a>
                                @endforeach
                            @else
                                <div class="px-4 py-6 text-center">
                                    <i class="fas fa-bell-slash text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
                                </div>
                            @endif
                        </div>
                        @if ($unreadNotificationCount > 0)
                            <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('notifications') }}"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    View all notifications
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <a href="{{ route('messages.index') }}"
                    class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="Chat" wire:navigate>
                    <i class="fas fa-comments text-gray-600 dark:text-gray-300"></i>
                    @if ($unreadChatCount > 0)
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            {{ $unreadChatCount > 9 ? '9+' : $unreadChatCount }}
                        </span>
                    @endif
                </a>

                <!-- Quick Create -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Quick actions">
                        <i class="fas fa-plus text-gray-600 dark:text-gray-300"></i>
                    </button>

                    <!-- Quick Actions Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
                        style="display: none;">
                        @if($user && ($user->hasRole('instructor') || $user->hasRole('academy_admin') || $user->hasRole('super_admin')))
                            <a href="{{ route('create.course') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-book w-4 mr-3"></i>
                                New Course
                            </a>
                        @endif
                        @if($user && ($user->hasRole('content_editor') || $user->hasRole('academy_admin') || $user->hasRole('super_admin')))
                            <a href="{{ route('admin.blog.posts.create') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-pen w-4 mr-3"></i>
                                New Blog Post
                            </a>
                        @endif
                        <a href="{{ route('feedback') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-comment w-4 mr-3"></i>
                            Send Feedback
                        </a>
                    </div>
                </div>
            </div>
            <x-theme-selector />
            <!-- Mobile Search Toggle -->
            <button
                class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                @click="$dispatch('toggle-mobile-search')" aria-label="Toggle search">
                <i class="fas fa-search text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="User menu">
                    <img src="{{ $user?->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="User avatar"
                        class="w-8 h-8 rounded-full border-2 border-gray-200 dark:border-gray-600">
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user?->name ?? 'Guest' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ ucfirst($user?->getRoleNames()->first() ?? 'User') }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs hidden sm:block"></i>
                </button>

                <!-- User Menu Dropdown -->
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    @click.away="open = false"
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
                    style="display: none;">
                    @if ($user)
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('profile.view') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-user w-4 mr-3"></i>
                            View Profile
                        </a>

                        <a href="{{ route('settings') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-cog w-4 mr-3"></i>
                            Settings
                        </a>

                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <i class="fas fa-sign-out-alt w-4 mr-3"></i>
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sign-in-alt w-4 mr-3"></i>
                            Login
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Search Bar (Hidden by default) -->
    <div x-data="{ mobileSearchOpen: false }" @toggle-mobile-search.window="mobileSearchOpen = !mobileSearchOpen"
        x-show="mobileSearchOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="md:hidden border-t border-gray-200 dark:border-gray-700 px-4 py-3" style="display: none;">
        <div class="relative">
            <input type="text" placeholder="Search courses, content, or users..." x-data="{ searchQuery: '' }"
                x-model="searchQuery" @keydown.enter="console.log('Mobile Search:', searchQuery)"
                class="w-full pl-10 pr-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <button @click="mobileSearchOpen = false"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</header>
