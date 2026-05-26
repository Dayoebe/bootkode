@php
    $user = auth()->user();
    $coursesUrl = $user ? route('student.course-catalog') : route('register');
    $coursesLabel = $user ? 'Courses' : 'Start learning';
    $accountUrl = $user ? route($user->getDashboardRouteName()) : route('login');
    $primaryCtaUrl = $user ? route($user->getDashboardRouteName()) : route('register');

    $navItems = [
        ['label' => 'Home', 'href' => url('/'), 'icon' => 'fa-house', 'active' => request()->is('/')],
        ['label' => $coursesLabel, 'href' => $coursesUrl, 'icon' => 'fa-book-open', 'active' => request()->routeIs('student.course-catalog')],
        ['label' => 'Pricing', 'href' => route('pricing'), 'icon' => 'fa-tags', 'active' => request()->routeIs('pricing')],
        ['label' => 'Marketplace', 'href' => route('marketplace.browse'), 'icon' => 'fa-store', 'active' => request()->routeIs('marketplace.*')],
        ['label' => 'Blog', 'href' => route('blog.index'), 'icon' => 'fa-newspaper', 'active' => request()->routeIs('blog.*')],
        ['label' => 'About', 'href' => route('about'), 'icon' => 'fa-circle-info', 'active' => request()->routeIs('about')],
        ['label' => 'Contact', 'href' => route('contact'), 'icon' => 'fa-envelope', 'active' => request()->routeIs('contact')],
    ];

    $quickLinks = [
        ['label' => 'Statistics', 'href' => route('statistics'), 'icon' => 'fa-chart-simple'],
        ['label' => 'Guidelines', 'href' => route('guideline'), 'icon' => 'fa-compass'],
        ['label' => 'Certificate Verify', 'href' => route('certificate.verify'), 'icon' => 'fa-certificate'],
    ];
@endphp

<header
    class="bk-public-header sticky top-0 z-50 border-b border-slate-200/80 bg-white/94 shadow-sm shadow-slate-950/5 backdrop-blur-xl"
    x-data="{ mobileOpen: false, accountOpen: false }"
    @keydown.escape.window="mobileOpen = false; accountOpen = false"
>
    <nav class="bk-shell flex h-16 items-center justify-between gap-3 lg:h-[72px]" aria-label="Main navigation">
        <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3" aria-label="BootKode home">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-[8px] bg-teal-700 text-white shadow-sm">
                <i class="fas fa-code text-sm"></i>
            </span>
            <span class="min-w-0">
                <span class="block text-base font-black tracking-tight text-slate-950 sm:text-lg">BootKode</span>
                <span class="hidden text-[11px] font-semibold uppercase tracking-[0.18em] text-teal-700 sm:block">Academy</span>
            </span>
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    class="rounded-[8px] px-4 py-2 text-sm font-bold transition {{ $item['active'] ? 'bg-slate-950 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            @auth
                <a
                    href="{{ route('messages.index') }}"
                    class="hidden h-10 w-10 place-items-center rounded-[8px] border border-slate-200 text-slate-700 transition hover:border-teal-300 hover:bg-teal-50 hover:text-teal-800 sm:grid"
                    aria-label="Messages"
                    wire:navigate
                >
                    <i class="fas fa-message text-sm"></i>
                </a>

                <div class="relative" @click.away="accountOpen = false">
                    <button
                        type="button"
                        class="flex h-11 items-center gap-2 rounded-[8px] border border-slate-200 bg-white px-2 pl-2 pr-3 text-sm font-bold text-slate-800 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                        @click="accountOpen = !accountOpen"
                        :aria-expanded="accountOpen.toString()"
                    >
                        <span class="grid h-8 w-8 place-items-center rounded-[8px] bg-teal-700 text-xs font-black text-white">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                        <span class="hidden max-w-28 truncate sm:inline">{{ $user->name }}</span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400" :class="{ 'rotate-180': accountOpen }"></i>
                    </button>

                    <div
                        x-show="accountOpen"
                        x-transition.origin.top.right
                        class="absolute right-0 mt-3 w-64 rounded-[8px] border border-slate-200 bg-white p-2 shadow-2xl shadow-slate-900/10"
                        style="display: none;"
                    >
                        <div class="px-3 py-3">
                            <p class="truncate text-sm font-black text-slate-950">{{ $user->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                        <a href="{{ route($user->getDashboardRouteName()) }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100">
                            <i class="fas fa-gauge-high w-4 text-teal-700"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('profile.view') }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100">
                            <i class="fas fa-user w-4 text-sky-700"></i>
                            Profile
                        </a>
                        <a href="{{ route('student.course-catalog') }}" class="flex items-center gap-3 rounded-[8px] px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100">
                            <i class="fas fa-book-open w-4 text-rose-700"></i>
                            Course catalog
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-100 pt-1">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 rounded-[8px] px-3 py-2.5 text-left text-sm font-bold text-red-600 hover:bg-red-50">
                                <i class="fas fa-arrow-right-from-bracket w-4"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden rounded-[8px] px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100 sm:inline-flex">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="hidden rounded-[8px] bg-teal-700 px-4 py-2 text-sm font-black text-white shadow-sm transition hover:bg-teal-800 sm:inline-flex">
                    Start free
                </a>
            @endauth

            <button
                type="button"
                class="grid h-11 w-11 place-items-center rounded-[8px] border border-slate-200 bg-white text-slate-800 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 lg:hidden"
                @click="mobileOpen = true"
                aria-label="Open menu"
            >
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <div x-show="mobileOpen" class="fixed inset-0 z-50 lg:hidden" style="display: none;">
        <div
            class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm"
            x-transition.opacity
            @click="mobileOpen = false"
        ></div>

        <aside
            class="absolute right-0 top-0 flex h-full w-[min(92vw,390px)] flex-col overflow-y-auto bg-white shadow-2xl"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-[8px] bg-slate-950 text-white">
                        <i class="fas fa-code text-sm"></i>
                    </span>
                    <div>
                        <p class="font-black text-slate-950">BootKode</p>
                        <p class="text-xs font-semibold text-slate-500">Learn, build, certify</p>
                    </div>
                </div>
                <button type="button" class="grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-slate-700" @click="mobileOpen = false" aria-label="Close menu">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-5 px-5 py-5">
                <div class="rounded-3xl bg-slate-950 p-5 text-white">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-teal-200">Mobile learning shell</p>
                    <p class="mt-2 text-xl font-black leading-tight">Pick a path and continue with fewer taps.</p>
                    <a href="{{ $primaryCtaUrl }}" class="mt-4 inline-flex w-full items-center justify-center rounded-[8px] bg-white px-4 py-3 text-sm font-black text-slate-950">
                        {{ $user ? 'Open dashboard' : 'Create account' }}
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" class="flex items-center gap-3 rounded-[8px] border border-slate-200 px-3 py-3 text-sm font-bold {{ $item['active'] ? 'bg-slate-950 text-white' : 'text-slate-700' }}">
                            <i class="fas {{ $item['icon'] }} w-4 {{ $item['active'] ? 'text-white' : 'text-teal-700' }}"></i>
                            <span class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                <div>
                    <p class="px-1 text-xs font-black uppercase tracking-[0.16em] text-slate-400">More</p>
                    <div class="mt-2 space-y-2">
                        @foreach ($quickLinks as $item)
                            <a href="{{ $item['href'] }}" class="flex items-center gap-3 rounded-[8px] px-3 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100">
                                <i class="fas {{ $item['icon'] }} w-5 text-sky-700"></i>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @auth
                    <div class="rounded-3xl border border-slate-200 p-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-11 w-11 place-items-center rounded-full bg-teal-700 text-sm font-black text-white">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-black text-slate-950">{{ $user->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <a href="{{ route($user->getDashboardRouteName()) }}" class="rounded-[8px] bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-800">Dashboard</a>
                            <a href="{{ route('profile.view') }}" class="rounded-[8px] bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-800">Profile</a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="rounded-[8px] border border-slate-200 px-4 py-3 text-center text-sm font-black text-slate-800">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-[8px] bg-teal-700 px-4 py-3 text-center text-sm font-black text-white">Register</a>
                    </div>
                @endauth
            </div>
        </aside>
    </div>

</header>

<nav class="bk-mobile-tabbar lg:hidden" aria-label="Mobile primary navigation">
    @foreach (array_slice($navItems, 0, 4) as $item)
        <a href="{{ $item['href'] }}" class="{{ $item['active'] ? 'active' : '' }}">
            <i class="fas {{ $item['icon'] }}"></i>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
    <a href="{{ $accountUrl }}" class="{{ request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-user"></i>
        <span>{{ $user ? 'Account' : 'Login' }}</span>
    </a>
</nav>
