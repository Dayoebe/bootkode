@props([
    'title' => null,
    'description' => null,
    'icon' => 'fas fa-gauge-high',
])

@php
    $user = Auth::user();
    $routeName = request()->route()?->getName() ?? 'dashboard';
    // Dynamic Blade component attributes arrive escaped; render the title as plain text once.
    $pageTitle = html_entity_decode((string) ($title ?: str($routeName)->replace(['.', '-', '_'], ' ')->title()->toString()), ENT_QUOTES | ENT_HTML5, 'UTF-8');
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

<header class="bk-topbar">
    <div class="bk-topbar-inner">
        <div class="bk-topbar-context">
            <button
                @click="toggleSidebar($event)"
                :aria-expanded="sidebarOpen.toString()" aria-controls="dashboard-navigation"
                class="bk-topbar-icon-button lg:hidden"
                aria-label="Open navigation"
            >
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>

            <span class="bk-topbar-context-icon" aria-hidden="true"><i class="{{ $icon }}"></i></span>
            <div class="min-w-0">
                <p class="bk-topbar-kicker hidden sm:block">Current workspace</p>
                <p class="bk-topbar-title truncate">{{ $pageTitle }}</p>
                <p class="bk-topbar-description hidden max-w-md truncate xl:block">{{ $pageDescription }}</p>
            </div>
        </div>

        <div class="hidden min-w-0 flex-1 justify-center px-4 xl:flex">
            <form
                class="relative w-full max-w-lg"
                x-data="dashboardSearch({
                    suggestUrl: @js(route('dashboard.search.suggest')),
                    resultsUrl: @js(route('dashboard.search')),
                })"
                @submit.prevent="submit()"
                @click.outside="close()"
            >
                <label class="relative block">
                    <span class="sr-only">Search workspace</span>
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-themed-tertiary"></i>
                    <input
                        type="search"
                        placeholder="Search courses, lessons, users, certificates..."
                        class="bk-topbar-search"
                        x-model="query"
                        @input.debounce.250ms="search()"
                        @focus="search()"
                        @keydown.escape.stop="close()"
                        autocomplete="off"
                    >
                    <button type="submit" class="absolute right-0 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-[8px] text-themed-tertiary transition hover:bg-themed-secondary hover:text-themed-primary" aria-label="Search">
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </label>

                <div
                    x-show="open"
                    x-transition.origin.top
                    class="bk-topbar-popover left-0 right-0 z-50 max-h-[28rem] overflow-y-auto p-2"
                    style="display: none;"
                >
                    <template x-if="loading">
                        <div role="status" class="flex items-center gap-3 px-3 py-4 text-sm font-bold text-themed-secondary">
                            <i class="fas fa-spinner animate-spin text-teal-600"></i>
                            Searching...
                        </div>
                    </template>

                    <template x-if="!loading && error">
                    <div class="px-3 py-4 text-sm text-content-muted" role="status">Search is unavailable. <button type="button" @click="search()" class="underline">Try again</button></div>
                </template>
                <template x-if="!loading && !error && searched && total === 0">
                        <div class="px-3 py-6 text-center">
                            <i class="fas fa-search text-2xl text-themed-tertiary"></i>
                            <p class="mt-2 text-sm font-semibold text-themed-primary">No results found</p>
                            <p class="mt-1 text-xs font-semibold text-themed-secondary">Try a course, lesson, user, certificate, page, or post.</p>
                        </div>
                    </template>

                    <template x-if="!loading && groups.length">
                        <div class="space-y-2">
                            <template x-for="group in groups" :key="group.type">
                                <div>
                                    <div class="flex items-center gap-2 px-2 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-themed-tertiary">
                                        <i :class="group.icon" class="text-teal-600"></i>
                                        <span x-text="group.label"></span>
                                    </div>
                                    <template x-for="item in group.items" :key="item.url + item.title">
                                        <a :href="item.url" class="flex min-w-0 gap-3 rounded-[8px] px-3 py-2.5 transition hover:bg-themed-tertiary">
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[8px] bg-teal-50 text-teal-700 dark:bg-teal-400/10 dark:text-teal-200">
                                                <i :class="item.icon"></i>
                                            </span>
                                            <span class="min-w-0">
                                                <span class="block truncate text-sm font-semibold text-themed-primary" x-text="item.title"></span>
                                                <span class="mt-0.5 line-clamp-1 block text-xs font-semibold text-themed-secondary" x-text="item.description"></span>
                                                <span class="mt-1 block truncate text-[11px] font-semibold uppercase tracking-wide text-themed-tertiary" x-text="item.meta"></span>
                                            </span>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-[8px] border border-themed-primary px-3 py-2.5 text-sm font-semibold text-teal-700 transition hover:bg-themed-tertiary dark:text-teal-200">
                                <i class="fas fa-search"></i>
                                View all results
                            </button>
                        </div>
                    </template>
                </div>
            </form>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <div class="hidden items-center gap-2 xl:flex">
                <div class="relative" x-data="{ open: false }" @keydown.escape.stop="open = false; $el.querySelector('button').focus()">
                    <button
                        @click="open = !open" :aria-expanded="open.toString()"
                        class="bk-topbar-icon-button relative"
                        aria-label="Notifications"
                    >
                        <i class="fas fa-bell"></i>
                        @if ($unreadNotificationCount > 0)
                            <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-rose-600 px-1 text-[10px] font-semibold text-white">
                                {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </button>

                    <div
                        x-show="open"
                        x-transition.origin.top.right
                        @click.away="open = false"
                        class="bk-topbar-popover w-80 max-w-[calc(100vw-2rem)]"
                        style="display: none;"
                    >
                        <div class="border-b border-themed-primary px-4 py-3">
                            <h3 class="text-sm font-semibold text-themed-primary">Notifications</h3>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @if ($unreadNotificationCount > 0)
                                @foreach ($user->unreadNotifications()->take(5)->get() as $notification)
                                    <form method="POST" action="{{ route('notifications.open', $notification->id) }}"
                                        class="border-b border-themed-primary last:border-0">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full px-4 py-3 text-left transition hover:bg-themed-tertiary">
                                            <p class="text-sm font-semibold text-themed-primary">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                            <p class="mt-1 text-xs text-themed-tertiary">{{ $notification->created_at->diffForHumans() }}</p>
                                        </button>
                                    </form>
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center">
                                    <i class="fas fa-bell-slash text-2xl text-themed-tertiary"></i>
                                    <p class="mt-2 text-sm font-semibold text-themed-secondary">No notifications</p>
                                </div>
                            @endif
                        </div>
                        <div class="border-t border-themed-primary px-4 py-3">
                            <a href="{{ route('notifications') }}" class="text-sm font-semibold text-teal-700 hover:underline dark:text-teal-300">
                                View notification center
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('messages.index') }}"
                    class="bk-topbar-icon-button relative"
                    aria-label="Chat" wire:navigate>
                    <i class="fas fa-comments"></i>
                    @if ($unreadChatCount > 0)
                        <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-rose-600 px-1 text-[10px] font-semibold text-white">
                            {{ $unreadChatCount > 9 ? '9+' : $unreadChatCount }}
                        </span>
                    @endif
                </a>

                <div class="relative" x-data="{ open: false }" @keydown.escape.stop="open = false; $el.querySelector('button').focus()">
                    <button
                        @click="open = !open" :aria-expanded="open.toString()"
                        class="bk-topbar-icon-button"
                        aria-label="Quick actions"
                    >
                        <i class="fas fa-plus"></i>
                    </button>

                    <div
                        x-show="open"
                        x-transition.origin.top.right
                        @click.away="open = false"
                        class="bk-topbar-popover w-56 p-2"
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

            <div class="hidden sm:block"><x-theme-selector /></div>

            <button
                class="bk-topbar-icon-button xl:hidden"
                @click="$dispatch('toggle-mobile-search')"
                aria-label="Toggle search"
            >
                <i class="fas fa-search"></i>
            </button>

            <div class="relative" x-data="{ open: false }" @keydown.escape.stop="open = false; $el.querySelector('button').focus()">
                <button
                    @click="open = !open" :aria-expanded="open.toString()"
                    class="bk-user-trigger"
                    aria-label="User menu"
                >
                    <span class="bk-user-avatar">
                        {{ $userInitials }}
                    </span>
                    <div class="hidden min-w-0 text-left sm:block">
                        <p class="max-w-32 truncate text-sm font-semibold">{{ $user?->name ?? 'Guest' }}</p>
                        <p class="truncate text-[11px] font-semibold text-themed-tertiary">{{ str($user?->getRoleNames()->first() ?? 'User')->replace('_', ' ')->title() }}</p>
                    </div>
                    <i class="fas fa-chevron-down hidden text-[10px] text-themed-tertiary sm:block" :class="{ 'rotate-180': open }"></i>
                </button>

                <div
                    x-show="open"
                    x-transition.origin.top.right
                    @click.away="open = false"
                    class="bk-topbar-popover w-64 max-w-[calc(100vw-2rem)] p-2"
                    style="display: none;"
                >
                    @if ($user)
                        <div class="px-3 py-3">
                            <p class="truncate text-sm font-semibold text-themed-primary">{{ $user->name }}</p>
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
                            <button type="submit" class="flex w-full items-center gap-3 rounded-[8px] px-3 py-2.5 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">
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
        <form
            class="relative"
            x-data="dashboardSearch({
                suggestUrl: @js(route('dashboard.search.suggest')),
                resultsUrl: @js(route('dashboard.search')),
            })"
            @submit.prevent="submit()"
            @click.outside="close()"
        >
            <label class="relative block">
                <span class="sr-only">Search workspace</span>
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-themed-tertiary"></i>
                <input
                    type="search"
                    placeholder="Search BootKode..."
                    x-model="query"
                    @input.debounce.250ms="search()"
                    @focus="search()"
                    @keydown.escape.stop="close()"
                    class="bk-topbar-search pr-24"
                    autocomplete="off"
                >
                <button type="submit" class="absolute right-11 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-[8px] text-themed-tertiary hover:bg-themed-secondary hover:text-themed-primary" aria-label="Search">
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
                <button type="button" @click="mobileSearchOpen = false; close()" class="absolute right-0 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-[8px] text-themed-tertiary hover:bg-themed-secondary" aria-label="Close search">
                    <i class="fas fa-times"></i>
                </button>
            </label>

            <div
                x-show="open"
                x-transition.origin.top
                class="bk-topbar-popover left-0 right-0 z-50 max-h-[70vh] overflow-y-auto p-2"
                style="display: none;"
            >
                <template x-if="loading">
                    <div role="status" class="flex items-center gap-3 px-3 py-4 text-sm font-bold text-themed-secondary">
                        <i class="fas fa-spinner animate-spin text-teal-600"></i>
                        Searching...
                    </div>
                </template>

                <template x-if="!loading && error">
                    <div class="px-3 py-4 text-sm text-content-muted" role="status">Search is unavailable. <button type="button" @click="search()" class="underline">Try again</button></div>
                </template>
                <template x-if="!loading && !error && searched && total === 0">
                    <div class="px-3 py-6 text-center">
                        <i class="fas fa-search text-2xl text-themed-tertiary"></i>
                        <p class="mt-2 text-sm font-semibold text-themed-primary">No results found</p>
                        <p class="mt-1 text-xs font-semibold text-themed-secondary">Try a course, lesson, user, certificate, page, or post.</p>
                    </div>
                </template>

                <template x-if="!loading && groups.length">
                    <div class="space-y-2">
                        <template x-for="group in groups" :key="group.type">
                            <div>
                                <div class="flex items-center gap-2 px-2 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-themed-tertiary">
                                    <i :class="group.icon" class="text-teal-600"></i>
                                    <span x-text="group.label"></span>
                                </div>
                                <template x-for="item in group.items" :key="item.url + item.title">
                                    <a :href="item.url" class="flex min-w-0 gap-3 rounded-[8px] px-3 py-2.5 transition hover:bg-themed-tertiary">
                                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[8px] bg-teal-50 text-teal-700 dark:bg-teal-400/10 dark:text-teal-200">
                                            <i :class="item.icon"></i>
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-themed-primary" x-text="item.title"></span>
                                            <span class="mt-0.5 line-clamp-1 block text-xs font-semibold text-themed-secondary" x-text="item.description"></span>
                                            <span class="mt-1 block truncate text-[11px] font-semibold uppercase tracking-wide text-themed-tertiary" x-text="item.meta"></span>
                                        </span>
                                    </a>
                                </template>
                            </div>
                        </template>

                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-[8px] border border-themed-primary px-3 py-2.5 text-sm font-semibold text-teal-700 transition hover:bg-themed-tertiary dark:text-teal-200">
                            <i class="fas fa-search"></i>
                            View all results
                        </button>
                    </div>
                </template>
            </div>
        </form>
    </div>
</header>

@once
    <script>
        window.dashboardSearch = function (config) {
            return {
                query: '',
                groups: [],
                total: 0,
                loading: false,
                searched: false,
                error: false,
                open: false,
                abortController: null,

                search() {
                    const value = this.query.trim();

                    if (!value) {
                        this.reset();
                        return;
                    }

                    this.open = true;
                    this.searched = true;
                    this.error = false;

                    if (this.abortController) {
                        this.abortController.abort();
                    }

                    this.abortController = new AbortController();
                    const controller = this.abortController;
                    this.loading = true;

                    fetch(`${config.suggestUrl}?q=${encodeURIComponent(value)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        signal: controller.signal,
                    })
                        .then((response) => response.ok ? response.json() : Promise.reject(response))
                        .then((payload) => {
                            this.groups = Array.isArray(payload.groups) ? payload.groups : [];
                            this.total = Number(payload.total || 0);
                            this.open = value === this.query.trim();
                        })
                        .catch((error) => {
                            if (error.name !== 'AbortError') {
                                this.error = true;
                                this.groups = [];
                                this.total = 0;
                                this.open = true;
                            }
                        })
                        .finally(() => {
                            if (this.abortController === controller) {
                                this.loading = false;
                            }
                        });
                },

                submit() {
                    const value = this.query.trim();

                    if (!value) {
                        return;
                    }

                    window.location.href = `${config.resultsUrl}?q=${encodeURIComponent(value)}`;
                },

                close() {
                    this.open = false;
                },

                reset() {
                    this.groups = [];
                    this.total = 0;
                    this.loading = false;
                    this.searched = false;
                    this.error = false;
                    this.abortController?.abort();
                    this.open = false;
                },
            };
        };
    </script>
@endonce
