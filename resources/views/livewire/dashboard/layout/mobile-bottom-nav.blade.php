@php
    $currentRoute = request()->route()?->getName() ?? 'dashboard';
    $user = auth()->user();
    $unreadChatCount = $user
        ? \App\Models\Messaging\DirectMessage::query()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->whereHas('conversation', fn($query) => $query->forParticipant($user))
            ->count()
        : 0;
    $unreadNotificationCount = $user?->unreadNotifications()?->count() ?? 0;
    $mobileMenuItems = [
        [
            'label' => 'Home',
            'icon' => 'fas fa-home',
            'route' => 'dashboard',
            'active' => $currentRoute === 'dashboard' || str_ends_with($currentRoute, '.dashboard'),
            'badge' => $unreadNotificationCount
        ],
        [
            'label' => 'Courses',
            'icon' => 'fas fa-book-open',
            'route' => $user?->hasRole('student') ? 'student.enrolled-courses' : 'my-course',
            'active' => in_array($currentRoute, ['student.enrolled-courses', 'student.course-catalog', 'my-course', 'all-course', 'courses.available'])
        ],
        [
            'label' => 'Chat',
            'icon' => 'fas fa-comments',
            'route' => 'messages.index',
            'active' => in_array($currentRoute, ['messages.index', 'messages.show']),
            'badge' => $unreadChatCount
        ],
        [
            'label' => 'Journey',
            'icon' => 'fas fa-route',
            'route' => 'learner.journey',
            'active' => $currentRoute === 'learner.journey'
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

<nav class="fixed bottom-3 left-3 right-3 z-40 rounded-[8px] border border-themed-primary bg-themed-secondary/95 shadow-2xl shadow-slate-950/15 backdrop-blur-xl transition-colors duration-200 lg:hidden"
    style="bottom: calc(0.75rem + env(safe-area-inset-bottom, 0px));"
    aria-label="Mobile dashboard navigation">
    <div class="grid h-[68px] grid-cols-5 gap-1 p-1">
        @foreach ($mobileMenuItems as $item)
            @if (isset($item['isMore']) && $item['isMore'])
                <div x-data="{ moreMenuOpen: false }">
                    <button
                        type="button"
                        @click="moreMenuOpen = !moreMenuOpen"
                        class="flex h-full w-full flex-col items-center justify-center gap-1 rounded-[8px] text-themed-secondary transition hover:bg-themed-tertiary hover:text-themed-primary"
                    >
                        <i class="{{ $item['icon'] }} text-base"></i>
                        <span class="max-w-full truncate text-[11px] font-black">{{ $item['label'] }}</span>
                    </button>

                    <div
                        x-show="moreMenuOpen"
                        @click.away="moreMenuOpen = false"
                        x-transition.opacity
                        class="fixed inset-0 z-50 bg-slate-950/55 backdrop-blur-sm"
                        style="display: none;"
                    >
                        <div class="absolute bottom-24 left-3 right-3">
                            <div class="overflow-hidden rounded-[8px] border border-themed-primary bg-themed-secondary shadow-2xl shadow-slate-950/20">
                                <div class="flex items-center justify-between border-b border-themed-primary px-4 py-3">
                                    <div>
                                        <h3 class="text-base font-black text-themed-primary">Quick Access</h3>
                                        <p class="text-xs font-semibold text-themed-tertiary">Open the next workspace area.</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="moreMenuOpen = false"
                                        class="grid h-10 w-10 place-items-center rounded-[8px] bg-themed-tertiary text-themed-secondary"
                                        aria-label="Close quick access"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="grid max-h-[60vh] gap-2 overflow-y-auto p-3">
                                    @if($user)
                                        @foreach ([
                                            ['route' => 'community.center', 'icon' => 'fas fa-users', 'label' => 'Community', 'copy' => 'Forums and study groups', 'color' => 'text-indigo-600'],
                                            ['route' => 'marketplace.browse', 'icon' => 'fas fa-store', 'label' => 'Marketplace', 'copy' => 'Courses and resources', 'color' => 'text-teal-700'],
                                            ['route' => 'student.certificates.index', 'icon' => 'fas fa-certificate', 'label' => 'Certificates', 'copy' => 'Proof and achievements', 'color' => 'text-amber-600'],
                                            ['route' => 'search.job', 'icon' => 'fas fa-briefcase', 'label' => 'Job Search', 'copy' => 'Career opportunities', 'color' => 'text-sky-700'],
                                            ['route' => 'help.support', 'icon' => 'fas fa-headset', 'label' => 'Help & Support', 'copy' => 'Tickets and answers', 'color' => 'text-rose-600'],
                                            ['route' => 'settings', 'icon' => 'fas fa-cog', 'label' => 'Settings', 'copy' => 'Account preferences', 'color' => 'text-slate-600'],
                                        ] as $quick)
                                            <a href="{{ route($quick['route']) }}"
                                                class="flex items-center gap-3 rounded-[8px] border border-themed-primary bg-themed-secondary px-3 py-3 transition hover:bg-themed-tertiary"
                                                @click="moreMenuOpen = false"
                                                wire:navigate>
                                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[8px] bg-themed-tertiary {{ $quick['color'] }}">
                                                    <i class="{{ $quick['icon'] }}"></i>
                                                </span>
                                                <span class="min-w-0">
                                                    <span class="block truncate text-sm font-black text-themed-primary">{{ $quick['label'] }}</span>
                                                    <span class="block truncate text-xs font-semibold text-themed-tertiary">{{ $quick['copy'] }}</span>
                                                </span>
                                            </a>
                                        @endforeach

                                        @if($user->hasRole(['instructor', 'academy_admin', 'super_admin']))
                                            <a href="{{ route('cbt.management') }}"
                                                class="flex items-center gap-3 rounded-[8px] border border-themed-primary bg-themed-secondary px-3 py-3 transition hover:bg-themed-tertiary"
                                                @click="moreMenuOpen = false"
                                                wire:navigate>
                                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[8px] bg-themed-tertiary text-indigo-600">
                                                    <i class="fas fa-laptop-code"></i>
                                                </span>
                                                <span class="min-w-0">
                                                    <span class="block truncate text-sm font-black text-themed-primary">CBT Management</span>
                                                    <span class="block truncate text-xs font-semibold text-themed-tertiary">Assessments and exams</span>
                                                </span>
                                            </a>
                                        @endif

                                        <button
                                            type="button"
                                            @click="moreMenuOpen = false; toggleSidebar()"
                                            class="flex w-full items-center gap-3 rounded-[8px] border border-themed-primary bg-slate-950 px-3 py-3 text-left text-white"
                                        >
                                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[8px] bg-white/10 text-teal-200">
                                                <i class="fas fa-bars"></i>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-black">Full Menu</span>
                                                <span class="block text-xs font-semibold text-slate-400">All platform tools</span>
                                            </span>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="flex items-center gap-3 rounded-[8px] border border-themed-primary px-3 py-3">
                                            <span class="grid h-11 w-11 place-items-center rounded-[8px] bg-themed-tertiary text-teal-700">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-black text-themed-primary">Login</span>
                                                <span class="block text-xs font-semibold text-themed-tertiary">Access your account</span>
                                            </span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}"
                    class="flex min-w-0 flex-col items-center justify-center gap-1 rounded-[8px] transition {{ $item['active'] ? 'bg-slate-950 text-white shadow-lg shadow-slate-950/15' : 'text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary' }}"
                    wire:navigate>
                    <span class="relative">
                        <i class="{{ $item['icon'] }} text-base"></i>
                        @if(($item['badge'] ?? 0) > 0)
                            <span class="absolute -right-2 -top-2 grid h-4 min-w-4 place-items-center rounded-full bg-rose-600 px-1 text-[9px] font-black leading-none text-white">
                                {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}
                            </span>
                        @endif
                    </span>
                    <span class="max-w-full truncate px-1 text-[11px] font-black">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </div>
</nav>
