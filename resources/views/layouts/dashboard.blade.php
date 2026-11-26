<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        theme: localStorage.getItem('theme') || 'light',
        sidebarOpen: false,
        lastSidebarTrigger: null,
        online: navigator.onLine,
        sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true',
        themes: ['light', 'dark', 'sepia', 'ocean', 'forest'],
        applyTheme(value) {
            this.themes.forEach((themeName) => document.documentElement.classList.remove(themeName));
            document.documentElement.classList.add(value);
        },
        setTheme(value) {
            this.theme = value;
            localStorage.setItem('theme', value);
            this.applyTheme(value);
        },
        openSidebar(event) {
            this.lastSidebarTrigger = event?.currentTarget || document.activeElement;
            this.sidebarOpen = true;
            this.$nextTick(() => document.getElementById('workspace-sidebar-close')?.focus());
        },
        closeSidebar(restoreFocus = true) {
            const trigger = this.lastSidebarTrigger;
            this.sidebarOpen = false;
            if (restoreFocus) {
                window.setTimeout(() => trigger?.focus(), 200);
            }
        },
        toggleSidebar(event) {
            this.sidebarOpen ? this.closeSidebar() : this.openSidebar(event);
        },
        toggleSidebarCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebar-collapsed', this.sidebarCollapsed.toString());
            document.documentElement.dataset.sidebarCollapsed = this.sidebarCollapsed.toString();
        }
    }"
    @keydown.escape.window="if (sidebarOpen) closeSidebar()"
    @online.window="online = true"
    @offline.window="online = false"
    @resize.window="if (window.innerWidth >= 1024 && sidebarOpen) closeSidebar(false)"
    x-init="applyTheme(theme); document.documentElement.dataset.sidebarCollapsed = sidebarCollapsed.toString()"
    x-bind:class="theme"
>
<head>
    <script>
        (() => {
            try {
                const theme = localStorage.getItem('theme') || 'light';
                const allowedThemes = ['light', 'dark', 'sepia', 'ocean', 'forest'];
                document.documentElement.classList.add(allowedThemes.includes(theme) ? theme : 'light');
                document.documentElement.dataset.sidebarCollapsed = (localStorage.getItem('sidebar-collapsed') === 'true').toString();
            } catch {
                document.documentElement.classList.add('light');
                document.documentElement.dataset.sidebarCollapsed = 'false';
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="bootkode-user-id" content="{{ auth()->id() ?? '' }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="BootKode">
    <meta name="google-adsense-account" content="ca-pub-3911204427206897">
    <meta name="google-site-verification" content="cmciE9Iqsl6Gl3u_0Zts_-SlchWbsZZ_8OMVpELH3CA" />

    @php
        $user = Auth::user();
        $currentRouteName = request()->route()?->getName() ?? 'dashboard';
        $pageTitle = $title ?? (str($currentRouteName)->replace(['.', '-', '_'], ' ')->title()->toString() ?: 'Dashboard');
        $pageDescription = $description ?? 'Manage learning, courses, mentorship, and platform tools from one focused workspace.';
        $pageIcon = $icon ?? 'fas fa-gauge-high';
        $roleLabel = ucfirst($user?->getRoleNames()->first() ?? 'User');
        $privacySettings = $user?->privacy_settings ?? [];
        $marketingAnalyticsEnabled = ($privacySettings['data_collection_consent'] ?? false)
            && ($privacySettings['analytics_tracking'] ?? false)
            && ($privacySettings['marketing_analytics'] ?? false)
            && ($privacySettings['third_party_integrations'] ?? false);
        $isLearningWorkspace = request()->routeIs('course.view');
    @endphp

    <title>{{ config('app.name', 'BootKode') }} - {{ $pageTitle }}</title>

    @if($marketingAnalyticsEnabled)
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10833921436"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'AW-10833921436');
        </script>
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-TJ23X96Z');
        </script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <script src="{{ asset('js/offline-learning.js') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashboard.js', 'resources/js/notifications.js'])
    @livewireStyles
    @stack('styles')
</head>

<body class="bk-dashboard-app min-h-screen antialiased bg-themed-primary text-themed-primary transition-colors duration-200">
    <a class="bk-skip-link" href="#main-content">Skip to content</a>

    @if($marketingAnalyticsEnabled)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TJ23X96Z"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
        <livewire:component.email-verification-alert />
    @endif

    <div class="min-h-screen bg-themed-primary">
        @unless($isLearningWorkspace)
            <div id="dashboard-navigation" x-trap.inert.noscroll.noreturn="sidebarOpen">
                @livewire('dashboard-sidebar')
            </div>
        @endunless

        <div
            x-show="sidebarOpen"
            x-transition.opacity
            @click="closeSidebar()"
            class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
            style="display: none;"
        ></div>

        <div
            class="{{ $isLearningWorkspace ? 'bk-learning-shell' : 'bk-dashboard-shell' }} flex min-h-screen min-w-0 flex-col"
        >
            @if($isLearningWorkspace)
                <header class="bk-learning-topbar">
                    <div class="flex min-h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                        <div class="flex min-w-0 items-center gap-3">
                            <a href="{{ route('student.enrolled-courses') }}" wire:navigate
                                class="bk-topbar-icon-button" aria-label="Back to my learning">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('dashboard') }}" wire:navigate class="flex min-h-11 min-w-0 items-center gap-3 rounded-lg">
                                <span class="grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-lg border border-line bg-white">
                                    <img src="{{ asset('img/logo.png') }}" alt="" class="h-full w-full object-contain">
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate font-display text-sm font-semibold text-content">BootKode Learning</span>
                                    <span class="hidden text-xs text-content-muted sm:block">Learn. Build. Grow.</span>
                                </span>
                            </a>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <span class="hidden items-center gap-2 text-xs font-semibold text-content-muted md:inline-flex">
                                <span class="h-2 w-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                Progress saves to your account
                            </span>
                            <a href="{{ route('student.enrolled-courses') }}" wire:navigate class="bk-button bk-button-secondary">
                                <i class="fas fa-book-open" aria-hidden="true"></i>
                                <span class="hidden sm:inline">My learning</span>
                            </a>
                        </div>
                    </div>
                </header>
            @else
                <x-navbar :title="$pageTitle" :description="$pageDescription" :icon="$pageIcon" />
            @endif

            <main id="main-content" tabindex="-1"
                class="{{ $isLearningWorkspace ? 'bk-learning-workspace px-3 py-4 sm:px-5 lg:px-6' : 'bk-workspace px-4 pt-6 sm:px-6 lg:px-8 lg:pt-8' }} flex-1">
                <div x-cloak x-show="!online" role="status" aria-live="polite" class="bk-feedback mb-4">
                    <p>You are offline. Server actions need a connection. <a href="{{ asset('offline-learning.html') }}" class="underline focus-visible:outline">Open downloaded lessons</a> to continue reading and save progress on this device.</p>
                </div>
                <x-ui.flash />

                <div class="{{ $isLearningWorkspace ? 'bk-learning-page' : 'bk-page' }} w-full min-w-0">
                    {{ $slot }}
                </div>
            </main>

            @unless (request()->routeIs('course.view'))
                <x-mobile-bottom-nav />
            @endunless
        </div>
    </div>

    @livewireScripts
    <x-pwa-install-prompt />
    @stack('scripts')
</body>
</html>
