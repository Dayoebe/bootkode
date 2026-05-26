<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        theme: localStorage.getItem('theme') || 'light',
        sidebarOpen: false,
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
        }
    }"
    x-init="applyTheme(theme)"
    x-bind:class="theme"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        $showDashboardWelcome = str_contains($currentRouteName, 'dashboard');
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <script src="{{ asset('js/offline-learning.js') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TJ23X96Z"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
        <livewire:components.email-verification-alert />
    @endif

    <div class="min-h-screen bg-themed-primary">
        @livewire('dashboard-sidebar')

        <div
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm lg:hidden"
            style="display: none;"
        ></div>

        <div class="flex min-h-screen min-w-0 flex-col transition-all duration-300 lg:pl-72">
            <x-navbar :title="$pageTitle" :description="$pageDescription" :icon="$pageIcon" />

            <main class="flex-1 px-4 pb-24 pt-4 sm:px-6 lg:px-8 lg:pb-8 lg:pt-6">
                @if($user && $showDashboardWelcome)
                    <section class="relative mb-6 overflow-hidden rounded-[8px] border border-slate-900/10 bg-slate-950 text-white shadow-xl shadow-slate-900/10">
                        <x-icon-field class="opacity-10" />
                        <div class="relative grid gap-5 p-5 sm:p-6 lg:grid-cols-[1fr_auto] lg:items-center">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-extrabold text-teal-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-teal-300"></span>
                                        {{ $roleLabel }} workspace
                                    </span>
                                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-extrabold text-sky-100">
                                        <i class="fas fa-calendar-day text-[11px]"></i>
                                        {{ now()->format('M j, Y') }}
                                    </span>
                                </div>
                                <h1 class="bk-display mt-3 text-2xl font-black leading-tight sm:text-3xl">
                                    Welcome back, {{ $user->name }}.
                                </h1>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">
                                    Continue learning, review activity, and jump into the tools that move your BootKode work forward.
                                </p>
                            </div>
                            <div class="grid gap-2 sm:grid-cols-2 lg:w-80">
                                <a href="{{ route('student.course-catalog') }}" class="bk-dashboard-btn bg-white text-slate-950 hover:bg-slate-100">
                                    <i class="fas fa-book-open text-teal-700"></i>
                                    Courses
                                </a>
                                <a href="{{ route('messages.index') }}" class="bk-dashboard-btn border border-white/15 bg-white/10 text-white hover:bg-white/15">
                                    <i class="fas fa-message text-sky-200"></i>
                                    Messages
                                </a>
                            </div>
                        </div>
                    </section>
                @endif

                <div class="min-w-0">
                    {{ $slot }}
                </div>
            </main>

            <x-mobile-bottom-nav />
        </div>
    </div>

    @livewireScripts
    <script>
        window.bootkodeDashboardCharts = {
            hasData(rows, keys) {
                if (!Array.isArray(rows) || rows.length === 0) {
                    return false;
                }

                return rows.some((row) => keys.some((key) => {
                    const value = Number(row?.[key] ?? 0);

                    return Number.isFinite(value) && value !== 0;
                }));
            },

            hasDatasetData(chartData) {
                const datasets = chartData?.datasets ?? [];

                return Array.isArray(datasets) && datasets.some((dataset) => {
                    const values = Array.isArray(dataset?.data) ? dataset.data : Object.values(dataset?.data ?? {});

                    return values.some((value) => {
                        const numericValue = Number(value ?? 0);

                        return Number.isFinite(numericValue) && numericValue !== 0;
                    });
                });
            },

            showEmpty(canvasOrId, message = 'No chart data yet') {
                const canvas = typeof canvasOrId === 'string' ? document.getElementById(canvasOrId) : canvasOrId;

                if (!canvas) {
                    return;
                }

                canvas.style.display = 'none';

                const parent = canvas.parentElement;
                if (!parent) {
                    return;
                }

                parent.classList.add('relative');

                let emptyState = parent.querySelector(`[data-chart-empty-for="${canvas.id}"]`);
                if (!emptyState) {
                    emptyState = document.createElement('div');
                    emptyState.dataset.chartEmptyFor = canvas.id;
                    emptyState.className = 'flex h-full min-h-[160px] flex-col items-center justify-center rounded-lg border border-dashed border-themed-primary bg-themed-tertiary px-4 py-6 text-center text-sm text-themed-secondary';
                    parent.appendChild(emptyState);
                }

                emptyState.innerHTML = `
                    <i class="fas fa-chart-line mb-3 text-2xl text-themed-tertiary"></i>
                    <span>${message}</span>
                `;
            },

            shouldRender(canvasId, rows, keys) {
                const canvas = document.getElementById(canvasId);

                if (!canvas) {
                    return false;
                }

                if (!window.Chart) {
                    this.showEmpty(canvas, 'Chart library could not load');

                    return false;
                }

                if (!this.hasData(rows, keys)) {
                    this.showEmpty(canvas);

                    return false;
                }

                canvas.style.display = '';
                canvas.parentElement?.querySelector(`[data-chart-empty-for="${canvas.id}"]`)?.remove();

                return true;
            },

            shouldRenderDataset(canvasId, chartData) {
                const canvas = document.getElementById(canvasId);

                if (!canvas) {
                    return false;
                }

                if (!window.Chart) {
                    this.showEmpty(canvas, 'Chart library could not load');

                    return false;
                }

                if (!this.hasDatasetData(chartData)) {
                    this.showEmpty(canvas);

                    return false;
                }

                canvas.style.display = '';
                canvas.parentElement?.querySelector(`[data-chart-empty-for="${canvas.id}"]`)?.remove();

                return true;
            }
        };
    </script>
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
