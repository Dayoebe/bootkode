@props([
    'title' => null,
    'description' => null,
    'icon' => 'fas fa-gauge-high',
])

@php
    $user = Auth::user();
    $routeName = request()->route()?->getName() ?? 'dashboard';
    $pageTitle = $title ?: str($routeName)->replace(['.', '-', '_'], ' ')->title()->toString();
    $pageDescription = $description ?: 'Focused workspace for your BootKode activity.';
    $unreadNotificationCount = $user?->unreadNotifications()?->count() ?? 0;
    $unreadChatCount = $user
        ? \App\Models\Messaging\DirectMessage::query()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->whereHas('conversation', fn($query) => $query->forParticipant($user))
            ->count()
        : 0;
    $userInitials = collect(explode(' ', $user?->name ?? 'User'))
        ->filter()
        ->map(fn ($part) => substr($part, 0, 1))
        ->take(2)
        ->implode('') ?: 'U';
@endphp

<header class="sticky top-0 z-30 border-b border-themed-primary bg-themed-secondary/90 backdrop-blur-xl transition-colors duration-200">
    <div class="flex min-h-20 items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button
                @click="toggleSidebar()"
                class="grid h-11 w-11 shrink-0 place-items-center rounded-[8px] border border-themed-primary bg-themed-secondary text-themed-primary shadow-sm transition hover:bg-themed-tertiary lg:hidden"
                aria-label="Toggle sidebar"
            >
                <i class="fas fa-bars"></i>
            </button>

            <div class="hidden h-11 w-11 shrink-0 place-items-center rounded-[8px] bg-slate-950 text-white shadow-sm sm:grid">
                <i class="{{ $icon }} text-sm"></i>
            </div>

            <div class="min-w-0">
                <div class="flex min-w-0 items-center gap-2">
                    <h1 class="bk-display truncate text-base font-black text-themed-primary sm:text-xl">{{ $pageTitle }}</h1>
                    <span class="hidden rounded-full bg-teal-50 px-2 py-0.5 text-[11px] font-black text-teal-700 dark:bg-teal-400/10 dark:text-teal-200 md:inline-flex">
                        Live
                    </span>
                </div>
                <p class="hidden max-w-xl truncate text-xs font-semibold text-themed-secondary sm:block">{{ $pageDescription }}</p>
            </div>
        </div>

        <div class="hidden min-w-0 flex-1 justify-center px-4 md:flex">
            <label class="relative w-full max-w-lg">
                <span class="sr-only">Search workspace</span>
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-themed-tertiary"></i>
                <input
                    type="search"
                    placeholder="Search courses, people, certificates..."
                    class="h-11 w-full rounded-[8px] border border-themed-primary bg-themed-tertiary/70 pl-9 pr-4 text-sm font-semibold text-themed-primary placeholder:text-themed-tertiary outline-none transition focus:border-teal-500 focus:bg-themed-secondary focus:ring-4 focus:ring-teal-500/10"
                    x-data="{ searchQuery: '' }"
                    x-model="searchQuery"
                    @keydown.enter="console.log('Search:', searchQuery)"
                >
            </label>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <div class="hidden items-center gap-2 lg:flex">
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        class="relative grid h-11 w-11 place-items-center rounded-[8px] border border-themed-primary bg-themed-secondary text-themed-secondary transition hover:bg-themed-tertiary hover:text-themed-primary"
                        aria-label="Notifications"
                    >
                        <i class="fas fa-bell"></i>
                        @if ($unreadNotificationCount > 0)
                            <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-rose-600 px-1 text-[10px] font-black text-white">
                                {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </button>

                    <div
                        x-show="open"
                        x-transition.origin.top.right
                        @click.away="open = false"
                        class="absolute right-0 mt-3 w-80 overflow-hidden rounded-[8px] border border-themed-primary bg-themed-secondary shadow-2xl shadow-slate-950/10"
                        style="display: none;"
                    >
                        <div class="border-b border-themed-primary px-4 py-3">
                            <h3 class="text-sm font-black text-themed-primary">Notifications</h3>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @if ($unreadNotificationCount > 0)
                                @foreach ($user->unreadNotifications()->take(5)->get() as $notification)
                                    <a href="{{ $notification->data['action_url'] ?? route('notifications') }}"
                                        class="block border-b border-themed-primary px-4 py-3 transition last:border-0 hover:bg-themed-tertiary">
                                        <p class="text-sm font-semibold text-themed-primary">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                        <p class="mt-1 text-xs text-themed-tertiary">{{ $notification->created_at->diffForHumans() }}</p>
                                    </a>
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center">
                                    <i class="fas fa-bell-slash text-2xl text-themed-tertiary"></i>
                                    <p class="mt-2 text-sm font-semibold text-themed-secondary">No notifications</p>
                                </div>
                            @endif
                        </div>
                        <div class="border-t border-themed-primary px-4 py-3">
                            <a href="{{ route('notifications') }}" class="text-sm font-black text-teal-700 hover:underline dark:text-teal-300">
                                View notification center
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('messages.index') }}"
                    class="relative grid h-11 w-11 place-items-center rounded-[8px] border border-themed-primary bg-themed-secondary text-themed-secondary transition hover:bg-themed-tertiary hover:text-themed-primary"
                    aria-label="Chat" wire:navigate>
                    <i class="fas fa-comments"></i>
                    @if ($unreadChatCount > 0)
                        <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-rose-600 px-1 text-[10px] font-black text-white">
                            {{ $unreadChatCount > 9 ? '9+' : $unreadChatCount }}
                        </span>
                    @endif
                </a>

                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        class="grid h-11 w-11 place-items-center rounded-[8px] border border-themed-primary bg-themed-secondary text-themed-secondary transition hover:bg-themed-tertiary hover:text-themed-primary"
                        aria-label="Quick actions"
                    >
                        <i class="fas fa-plus"></i>
                    </button>

                    <div
                        x-show="open"
                        x-transition.origin.top.right
                        @click.away="open = false"
                        class="absolute right-0 mt-3 w-56 overflow-hidden rounded-[8px] border border-themed-primary bg-themed-secondary p-2 shadow-2xl shadow-slate-950/10"
                        style="display: none;"
                    >
                        @if($user && ($user->hasRole('instructor') || $user->hasRole('academy_admin') || $user->hasRole('super_admin')))
                            <a href="{{ route('create.course') }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary">
                                <i class="fas fa-book w-4 text-teal-700"></i>
                                New Course
                            </a>
                        @endif
                        @if($user && ($user->hasRole('content_editor') || $user->hasRole('academy_admin') || $user->hasRole('super_admin')))
                            <a href="{{ route('admin.blog.posts.create') }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary">
                                <i class="fas fa-pen w-4 text-sky-700"></i>
                                New Blog Post
                            </a>
                        @endif
                        <a href="{{ route('feedback') }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary">
                            <i class="fas fa-comment w-4 text-amber-600"></i>
                            Send Feedback
                        </a>
                    </div>
                </div>
            </div>

            <x-theme-selector />

            <button
                class="grid h-11 w-11 place-items-center rounded-[8px] border border-themed-primary bg-themed-secondary text-themed-secondary transition hover:bg-themed-tertiary hover:text-themed-primary md:hidden"
                @click="$dispatch('toggle-mobile-search')"
                aria-label="Toggle search"
            >
                <i class="fas fa-search"></i>
            </button>

            <div class="relative" x-data="{ open: false }">
                <button
                    @click="open = !open"
                    class="flex h-11 items-center gap-2 rounded-[8px] border border-themed-primary bg-themed-secondary px-2 text-themed-primary shadow-sm transition hover:bg-themed-tertiary"
                    aria-label="User menu"
                >
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[8px] border border-themed-primary bg-teal-600 text-xs font-black text-white">
                        {{ $userInitials }}
                    </span>
                    <div class="hidden min-w-0 text-left sm:block">
                        <p class="max-w-32 truncate text-sm font-black">{{ $user?->name ?? 'Guest' }}</p>
                        <p class="truncate text-[11px] font-semibold text-themed-tertiary">{{ ucfirst($user?->getRoleNames()->first() ?? 'User') }}</p>
                    </div>
                    <i class="fas fa-chevron-down hidden text-[10px] text-themed-tertiary sm:block" :class="{ 'rotate-180': open }"></i>
                </button>

                <div
                    x-show="open"
                    x-transition.origin.top.right
                    @click.away="open = false"
                    class="absolute right-0 mt-3 w-64 overflow-hidden rounded-[8px] border border-themed-primary bg-themed-secondary p-2 shadow-2xl shadow-slate-950/10"
                    style="display: none;"
                >
                    @if ($user)
                        <div class="px-3 py-3">
                            <p class="truncate text-sm font-black text-themed-primary">{{ $user->name }}</p>
                            <p class="truncate text-xs font-semibold text-themed-tertiary">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('profile.view') }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary">
                            <i class="fas fa-user w-4 text-teal-700"></i>
                            View Profile
                        </a>

                        <a href="{{ route('settings') }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary">
                            <i class="fas fa-cog w-4 text-sky-700"></i>
                            Settings
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-themed-primary pt-2">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 rounded-[8px] px-3 py-2.5 text-left text-sm font-black text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                <i class="fas fa-sign-out-alt w-4"></i>
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary">
                            <i class="fas fa-sign-in-alt w-4"></i>
                            Login
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div
        x-data="{ mobileSearchOpen: false }"
        @toggle-mobile-search.window="mobileSearchOpen = !mobileSearchOpen"
        x-show="mobileSearchOpen"
        x-transition
        class="border-t border-themed-primary px-4 py-3 md:hidden"
        style="display: none;"
    >
        <label class="relative block">
            <span class="sr-only">Search workspace</span>
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-themed-tertiary"></i>
            <input
                type="search"
                placeholder="Search BootKode..."
                x-data="{ searchQuery: '' }"
                x-model="searchQuery"
                @keydown.enter="console.log('Mobile Search:', searchQuery)"
                class="h-11 w-full rounded-[8px] border border-themed-primary bg-themed-tertiary pl-9 pr-10 text-sm font-semibold text-themed-primary placeholder:text-themed-tertiary outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10"
            >
            <button @click="mobileSearchOpen = false" class="absolute right-2 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-[8px] text-themed-tertiary hover:bg-themed-secondary" aria-label="Close search">
                <i class="fas fa-times"></i>
            </button>
        </label>
    </div>
</header>
