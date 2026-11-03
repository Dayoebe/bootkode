<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        theme: localStorage.getItem('theme') || 'light',
        sidebarOpen: false,
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
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        toggleSidebarCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebar-collapsed', this.sidebarCollapsed.toString());
        }
    }"
    @keydown.escape.window="sidebarOpen = false"
    @online.window="online = true"
    @offline.window="online = false"
    @resize.window="if (window.innerWidth >= 1024) sidebarOpen = false"
    x-init="applyTheme(theme)"
    x-bind:class="theme"
>
<head>
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
    @endphp

    <title>{{ config('app.name', 'BootKode') }} - {{ $pageTitle }}</title>

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


    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']]
            },
            svg: { fontCache: 'global' },
            startup: {
                pageReady: function() {
                    return MathJax.startup.defaultPageReady().then(function() {
                        document.dispatchEvent(new Event('mathjax-loaded'));
                    });
                }
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" async></script>
</head>

<body class="bk-dashboard-app min-h-screen antialiased bg-themed-primary text-themed-primary transition-colors duration-200">
    <a class="bk-skip-link" href="#main-content">Skip to content</a>

    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TJ23X96Z"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
        <livewire:component.email-verification-alert />
    @endif

    <div class="min-h-screen bg-themed-primary">
        <div id="dashboard-navigation" x-trap.inert.noscroll="sidebarOpen">
            @livewire('dashboard-sidebar')
        </div>

        <div
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
            style="display: none;"
        ></div>

        <div
            class="flex min-h-screen min-w-0 flex-col transition-[padding] duration-300"
            :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'"
        >
            <x-navbar :title="$pageTitle" :description="$pageDescription" :icon="$pageIcon" />

            <main id="main-content" tabindex="-1" class="bk-workspace flex-1 px-4 pt-6 sm:px-6 lg:px-8 lg:pt-8">
                <div x-cloak x-show="!online" role="status" aria-live="polite" class="bk-feedback mb-4">
                    <p>You are offline. Server actions need a connection. <a href="{{ asset('offline-learning.html') }}" class="underline focus-visible:outline">Open downloaded lessons</a> to continue reading and save progress on this device.</p>
                </div>
                <x-ui.flash />

                <div class="bk-page w-full min-w-0">
                    {{ $slot }}
                </div>
            </main>

            <x-mobile-bottom-nav />
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
