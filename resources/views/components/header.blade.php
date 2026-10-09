@php
    $user = auth()->user();
    $coursesUrl = $user ? route('student.course-catalog') : route('register');
    $accountUrl = $user ? route($user->getDashboardRouteName()) : route('login');
    $primaryCtaUrl = $user ? route($user->getDashboardRouteName()) : route('register');

    $learnLinks = [
        ['label' => 'Course catalogue', 'copy' => 'Browse structured, practical courses.', 'href' => $coursesUrl, 'icon' => 'fa-book-open', 'tone' => 'bg-cyan-50 text-cyan-700'],
        ['label' => 'Learning guidelines', 'copy' => 'Understand how learning works on BootKode.', 'href' => route('guideline'), 'icon' => 'fa-compass', 'tone' => 'bg-violet-50 text-violet-700'],
        ['label' => 'Verify a certificate', 'copy' => 'Confirm a BootKode credential.', 'href' => route('certificate.verify'), 'icon' => 'fa-certificate', 'tone' => 'bg-emerald-50 text-emerald-700'],
    ];
    $exploreLinks = [
        ['label' => 'Marketplace', 'copy' => 'Discover learning resources and products.', 'href' => route('marketplace.browse'), 'icon' => 'fa-store', 'tone' => 'bg-amber-50 text-amber-700'],
        ['label' => 'BootKode blog', 'copy' => 'Read practical learning and career insights.', 'href' => route('blog.index'), 'icon' => 'fa-newspaper', 'tone' => 'bg-sky-50 text-sky-700'],
        ['label' => 'Platform statistics', 'copy' => 'See current platform activity and progress.', 'href' => route('statistics'), 'icon' => 'fa-chart-simple', 'tone' => 'bg-rose-50 text-rose-700'],
    ];
    $companyLinks = [
        ['label' => 'About BootKode', 'href' => route('about'), 'icon' => 'fa-circle-info'],
        ['label' => 'Contact us', 'href' => route('contact'), 'icon' => 'fa-envelope'],
        ['label' => 'Plans & pricing', 'href' => route('pricing'), 'icon' => 'fa-tags'],
    ];
    $mobileTabs = [
        ['label' => 'Home', 'href' => url('/'), 'icon' => 'fa-house', 'active' => request()->is('/')],
        ['label' => 'Learn', 'href' => $coursesUrl, 'icon' => 'fa-book-open', 'active' => request()->routeIs('student.course-catalog')],
        ['label' => 'Market', 'href' => route('marketplace.browse'), 'icon' => 'fa-store', 'active' => request()->routeIs('marketplace.*')],
        ['label' => 'Pricing', 'href' => route('pricing'), 'icon' => 'fa-tags', 'active' => request()->routeIs('pricing')],
    ];
@endphp

<header
    class="bk-public-header sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 shadow-sm shadow-slate-950/5 backdrop-blur-xl"
    x-data="{ mobileOpen: false, accountOpen: false, menu: null, mobileSection: 'learn' }"
    @keydown.escape.window="mobileOpen = false; accountOpen = false; menu = null"
>
    <nav class="bk-shell flex h-[68px] items-center justify-between gap-4 lg:h-[76px]" aria-label="Primary navigation">
        <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-3" aria-label="BootKode home">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#07152d] text-[#8fe388] shadow-sm"><i class="fas fa-code text-sm"></i></span>
            <span class="min-w-0">
                <span class="block text-base font-black tracking-tight text-slate-950 sm:text-lg">BootKode</span>
                <span class="hidden text-[10px] font-extrabold uppercase tracking-[0.18em] text-teal-700 sm:block">Learn · Build · Advance</span>
            </span>
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            <a href="{{ url('/') }}" class="rounded-full px-4 py-2.5 text-sm font-bold transition {{ request()->is('/') ? 'bg-[#07152d] text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">Home</a>

            <div class="relative" @click.outside="if (menu === 'learn') menu = null">
                <button type="button" @click="menu = menu === 'learn' ? null : 'learn'" class="flex items-center gap-2 rounded-full px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950" :class="menu === 'learn' ? 'bg-slate-100 text-slate-950' : ''" :aria-expanded="(menu === 'learn').toString()">
                    Learn <i class="fas fa-chevron-down text-[9px] transition" :class="menu === 'learn' ? 'rotate-180' : ''"></i>
                </button>
                <div x-cloak x-show="menu === 'learn'" x-transition.origin.top class="absolute left-1/2 top-full mt-3 w-[590px] -translate-x-1/2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-950/15">
                    <div class="grid grid-cols-[1.25fr_.75fr]">
                        <div class="p-3">
                            @foreach($learnLinks as $item)
                                <a href="{{ $item['href'] }}" class="group flex gap-4 rounded-xl p-3 transition hover:bg-slate-50">
                                    <span class="{{ $item['tone'] }} grid h-11 w-11 shrink-0 place-items-center rounded-xl"><i class="fas {{ $item['icon'] }}"></i></span>
                                    <span><span class="block text-sm font-black text-slate-950">{{ $item['label'] }}</span><span class="mt-1 block text-xs font-medium leading-5 text-slate-500">{{ $item['copy'] }}</span></span>
                                </a>
                            @endforeach
                        </div>
                        <div class="bg-[#07152d] p-6 text-white">
                            <span class="text-[10px] font-black uppercase tracking-[0.18em] text-[#8fe388]">Start here</span>
                            <p class="mt-3 text-xl font-black leading-snug">Build skills through focused, practical learning.</p>
                            <p class="mt-3 text-xs font-medium leading-5 text-slate-300">Choose a path, complete the work, and keep visible evidence of your progress.</p>
                            <a href="{{ $primaryCtaUrl }}" class="mt-6 inline-flex items-center gap-2 text-xs font-black text-white">{{ $user ? 'Continue learning' : 'Create free account' }} <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative" @click.outside="if (menu === 'explore') menu = null">
                <button type="button" @click="menu = menu === 'explore' ? null : 'explore'" class="flex items-center gap-2 rounded-full px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950" :class="menu === 'explore' ? 'bg-slate-100 text-slate-950' : ''" :aria-expanded="(menu === 'explore').toString()">
                    Explore <i class="fas fa-chevron-down text-[9px] transition" :class="menu === 'explore' ? 'rotate-180' : ''"></i>
                </button>
                <div x-cloak x-show="menu === 'explore'" x-transition.origin.top class="absolute left-1/2 top-full mt-3 w-[420px] -translate-x-1/2 rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl shadow-slate-950/15">
                    @foreach($exploreLinks as $item)
                        <a href="{{ $item['href'] }}" class="flex gap-4 rounded-xl p-3 transition hover:bg-slate-50">
                            <span class="{{ $item['tone'] }} grid h-11 w-11 shrink-0 place-items-center rounded-xl"><i class="fas {{ $item['icon'] }}"></i></span>
                            <span><span class="block text-sm font-black text-slate-950">{{ $item['label'] }}</span><span class="mt-1 block text-xs font-medium leading-5 text-slate-500">{{ $item['copy'] }}</span></span>
                        </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('pricing') }}" class="rounded-full px-4 py-2.5 text-sm font-bold transition {{ request()->routeIs('pricing') ? 'bg-[#07152d] text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">Pricing</a>

            <div class="relative" @click.outside="if (menu === 'company') menu = null">
                <button type="button" @click="menu = menu === 'company' ? null : 'company'" class="flex items-center gap-2 rounded-full px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950" :class="menu === 'company' ? 'bg-slate-100 text-slate-950' : ''" :aria-expanded="(menu === 'company').toString()">
                    Company <i class="fas fa-chevron-down text-[9px] transition" :class="menu === 'company' ? 'rotate-180' : ''"></i>
                </button>
                <div x-cloak x-show="menu === 'company'" x-transition.origin.top.right class="absolute right-0 top-full mt-3 w-64 rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl shadow-slate-950/15">
                    @foreach($companyLinks as $item)
                        <a href="{{ $item['href'] }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-950"><i class="fas {{ $item['icon'] }} w-5 text-teal-700"></i>{{ $item['label'] }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('messages.index') }}" class="hidden h-10 w-10 place-items-center rounded-full border border-slate-200 text-slate-700 transition hover:border-teal-300 hover:bg-teal-50 hover:text-teal-800 sm:grid" aria-label="Messages" wire:navigate><i class="fas fa-message text-sm"></i></a>
                <div class="relative" @click.outside="accountOpen = false">
                    <button type="button" class="flex h-11 items-center gap-2 rounded-full border border-slate-200 bg-white p-1.5 pr-3 text-sm font-bold text-slate-800 shadow-sm transition hover:border-slate-300" @click="accountOpen = !accountOpen; menu = null" :aria-expanded="accountOpen.toString()">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-teal-700 text-xs font-black text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        <span class="hidden max-w-28 truncate sm:inline">{{ $user->name }}</span><i class="fas fa-chevron-down text-[9px] text-slate-400"></i>
                    </button>
                    <div x-cloak x-show="accountOpen" x-transition.origin.top.right class="absolute right-0 mt-3 w-72 rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl shadow-slate-950/15">
                        <div class="border-b border-slate-100 px-3 py-3"><p class="truncate text-sm font-black text-slate-950">{{ $user->name }}</p><p class="truncate text-xs text-slate-500">{{ $user->email }}</p></div>
                        <a href="{{ route($user->getDashboardRouteName()) }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50"><i class="fas fa-gauge-high w-4 text-teal-700"></i>Dashboard</a>
                        <a href="{{ route('student.course-catalog') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50"><i class="fas fa-book-open w-4 text-cyan-700"></i>My learning</a>
                        <a href="{{ route('profile.view') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50"><i class="fas fa-user w-4 text-violet-700"></i>Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-100 pt-1">@csrf<button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-bold text-red-600 hover:bg-red-50"><i class="fas fa-arrow-right-from-bracket w-4"></i>Log out</button></form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden rounded-full px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100 sm:inline-flex">Log in</a>
                <a href="{{ route('register') }}" class="hidden rounded-full bg-[#07152d] px-5 py-2.5 text-sm font-black text-white shadow-sm transition hover:bg-slate-800 sm:inline-flex">Start free</a>
            @endauth
            <button type="button" class="grid h-11 w-11 place-items-center rounded-full border border-slate-200 bg-white text-slate-800 shadow-sm lg:hidden" @click="mobileOpen = true" aria-label="Open navigation"><i class="fas fa-bars"></i></button>
        </div>
    </nav>

    <div x-cloak x-show="mobileOpen" class="fixed inset-0 z-50 lg:hidden">
        <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" x-transition.opacity @click="mobileOpen = false"></div>
        <aside class="absolute right-0 top-0 flex h-full w-[min(94vw,420px)] flex-col overflow-y-auto bg-white shadow-2xl" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur">
                <a href="{{ url('/') }}" class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#07152d] text-[#8fe388]"><i class="fas fa-code"></i></span><span><span class="block font-black text-slate-950">BootKode</span><span class="block text-[10px] font-bold uppercase tracking-[0.16em] text-teal-700">Learn · Build · Advance</span></span></a>
                <button type="button" class="grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-slate-700" @click="mobileOpen = false" aria-label="Close navigation"><i class="fas fa-xmark"></i></button>
            </div>

            <div class="space-y-6 px-5 py-5">
                <div class="rounded-2xl bg-[#07152d] p-5 text-white">
                    <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#8fe388]">Keep moving</p><p class="mt-2 text-xl font-black leading-tight">One place to learn, practise, and prove your progress.</p>
                    <a href="{{ $primaryCtaUrl }}" class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-[#8fe388] px-4 py-3 text-sm font-black text-[#07152d]">{{ $user ? 'Open dashboard' : 'Create free account' }}</a>
                </div>

                <div class="grid grid-cols-3 rounded-xl bg-slate-100 p-1">
                    @foreach(['learn' => 'Learn', 'explore' => 'Explore', 'company' => 'Company'] as $key => $label)
                        <button type="button" @click="mobileSection = '{{ $key }}'" class="rounded-lg px-2 py-2.5 text-xs font-black transition" :class="mobileSection === '{{ $key }}' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500'">{{ $label }}</button>
                    @endforeach
                </div>

                <div x-show="mobileSection === 'learn'" class="space-y-2">
                    @foreach($learnLinks as $item)
                        <a href="{{ $item['href'] }}" class="flex gap-3 rounded-xl border border-slate-200 p-3"><span class="{{ $item['tone'] }} grid h-10 w-10 shrink-0 place-items-center rounded-xl"><i class="fas {{ $item['icon'] }}"></i></span><span><span class="block text-sm font-black text-slate-950">{{ $item['label'] }}</span><span class="mt-0.5 block text-xs leading-5 text-slate-500">{{ $item['copy'] }}</span></span></a>
                    @endforeach
                </div>
                <div x-cloak x-show="mobileSection === 'explore'" class="space-y-2">
                    @foreach($exploreLinks as $item)
                        <a href="{{ $item['href'] }}" class="flex gap-3 rounded-xl border border-slate-200 p-3"><span class="{{ $item['tone'] }} grid h-10 w-10 shrink-0 place-items-center rounded-xl"><i class="fas {{ $item['icon'] }}"></i></span><span><span class="block text-sm font-black text-slate-950">{{ $item['label'] }}</span><span class="mt-0.5 block text-xs leading-5 text-slate-500">{{ $item['copy'] }}</span></span></a>
                    @endforeach
                </div>
                <div x-cloak x-show="mobileSection === 'company'" class="space-y-2">
                    @foreach($companyLinks as $item)
                        <a href="{{ $item['href'] }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700"><i class="fas {{ $item['icon'] }} w-5 text-teal-700"></i>{{ $item['label'] }}</a>
                    @endforeach
                </div>

                @auth
                    <div class="rounded-2xl border border-slate-200 p-4"><div class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-full bg-teal-700 text-sm font-black text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span><div class="min-w-0"><p class="truncate text-sm font-black text-slate-950">{{ $user->name }}</p><p class="truncate text-xs text-slate-500">{{ $user->email }}</p></div></div><div class="mt-3 grid grid-cols-2 gap-2"><a href="{{ route($user->getDashboardRouteName()) }}" class="rounded-xl bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-800">Dashboard</a><a href="{{ route('profile.view') }}" class="rounded-xl bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-800">Profile</a></div></div>
                @else
                    <div class="grid grid-cols-2 gap-2"><a href="{{ route('login') }}" class="rounded-full border border-slate-200 px-4 py-3 text-center text-sm font-black text-slate-800">Log in</a><a href="{{ route('register') }}" class="rounded-full bg-[#07152d] px-4 py-3 text-center text-sm font-black text-white">Start free</a></div>
                @endauth
            </div>
        </aside>
    </div>
</header>

<nav class="bk-mobile-tabbar lg:hidden" aria-label="Mobile primary navigation">
    @foreach($mobileTabs as $item)
        <a href="{{ $item['href'] }}" class="{{ $item['active'] ? 'active' : '' }}"><i class="fas {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span></a>
    @endforeach
    <a href="{{ $accountUrl }}" class="{{ request()->routeIs('login', 'register', 'dashboard') ? 'active' : '' }}"><i class="fas fa-user"></i><span>{{ $user ? 'Account' : 'Login' }}</span></a>
</nav>
