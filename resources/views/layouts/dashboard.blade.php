<!DOCTYPE html>
<html lang="en" x-data="{ 
    theme: localStorage.getItem('theme') || 'light',
    sidebarOpen: false,
    setTheme(newTheme) {
        this.theme = newTheme;
        localStorage.setItem('theme', newTheme);
        document.documentElement.className = newTheme;
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    }
}" x-bind:class="theme" x-init="document.documentElement.className = theme">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BootKode - Dashboard</title>
    <!-- Google tag (gtag.js) -->
    <meta name="google-adsense-account" content="ca-pub-3911204427206897">
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-10833921436"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-10833921436');
</script>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TJ23X96Z');</script>
    <!-- End Google Tag Manager -->
    <meta name="google-site-verification" content="cmciE9Iqsl6Gl3u_0Zts_-SlchWbsZZ_8OMVpELH3CA" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ============================================
           COMPREHENSIVE THEME SYSTEM
           ============================================ */

        /* Light Theme */
        .light {
            --bg-primary: 249 250 251;
            --bg-secondary: 255 255 255;
            --bg-tertiary: 243 244 246;
            --text-primary: 17 24 39;
            --text-secondary: 75 85 99;
            --text-tertiary: 156 163 175;
            --border-primary: 229 231 235;
            --border-secondary: 209 213 219;
            --accent-primary: 59 130 246;
            --accent-secondary: 37 99 235;
            --tab-inactive-bg: 243 244 246;
            --tab-inactive-text: 107 114 128;
            --tab-active-bg: 37 99 235;
            --tab-active-text: 255 255 255;
            --btn-hover-bg: 229 231 235;
            --btn-disabled-text: 156 163 175;
            --input-border: 209 213 219;
            --input-focus-border: 59 130 246;
        }

        /* Dark Theme */
        .dark {
            --bg-primary: 17 24 39;
            --bg-secondary: 31 41 55;
            --bg-tertiary: 55 65 81;
            --text-primary: 255 255 255;
            --text-secondary: 209 213 219;
            --text-tertiary: 156 163 175;
            --border-primary: 55 65 81;
            --border-secondary: 75 85 99;
            --accent-primary: 96 165 250;
            --accent-secondary: 59 130 246;
            --tab-inactive-bg: 55 65 81;
            --tab-inactive-text: 209 213 219;
            --tab-active-bg: 96 165 250;
            --tab-active-text: 17 24 39;
            --btn-hover-bg: 75 85 99;
            --btn-disabled-text: 107 114 128;
            --input-border: 75 85 99;
            --input-focus-border: 96 165 250;
        }

        /* Sepia Theme */
        .sepia {
            --bg-primary: 244 236 216;
            --bg-secondary: 250 245 235;
            --bg-tertiary: 238 228 208;
            --text-primary: 92 75 55;
            --text-secondary: 120 100 80;
            --text-tertiary: 160 140 120;
            --border-primary: 220 205 180;
            --border-secondary: 200 185 160;
            --accent-primary: 139 92 46;
            --accent-secondary: 115 75 35;
            --tab-inactive-bg: 238 228 208;
            --tab-inactive-text: 92 75 55;
            --tab-active-bg: 92 65 35;
            --tab-active-text: 250 245 235;
            --btn-hover-bg: 220 205 180;
            --btn-disabled-text: 160 140 120;
            --input-border: 200 185 160;
            --input-focus-border: 139 92 46;
        }

        /* Ocean Theme */
        .ocean {
            --bg-primary: 240 249 255;
            --bg-secondary: 224 242 254;
            --bg-tertiary: 186 230 253;
            --text-primary: 12 74 110;
            --text-secondary: 7 89 133;
            --text-tertiary: 14 116 144;
            --border-primary: 125 211 252;
            --border-secondary: 56 189 248;
            --accent-primary: 2 132 199;
            --accent-secondary: 1 108 170;
            --tab-inactive-bg: 186 230 253;
            --tab-inactive-text: 12 74 110;
            --tab-active-bg: 1 89 144;
            --tab-active-text: 255 255 255;
            --btn-hover-bg: 125 211 252;
            --btn-disabled-text: 14 116 144;
            --input-border: 56 189 248;
            --input-focus-border: 2 132 199;
        }

        /* Forest Theme */
        .forest {
            --bg-primary: 236 253 245;
            --bg-secondary: 209 250 229;
            --bg-tertiary: 167 243 208;
            --text-primary: 20 83 45;
            --text-secondary: 21 128 61;
            --text-tertiary: 22 163 74;
            --border-primary: 134 239 172;
            --border-secondary: 74 222 128;
            --accent-primary: 34 197 94;
            --accent-secondary: 22 163 74;
            --tab-inactive-bg: 167 243 208;
            --tab-inactive-text: 20 83 45;
            --tab-active-bg: 5 122 35;
            --tab-active-text: 255 255 255;
            --btn-hover-bg: 134 239 172;
            --btn-disabled-text: 22 163 74;
            --input-border: 74 222 128;
            --input-focus-border: 34 197 94;
        }

        /* ============================================
           BASE STYLES
           ============================================ */
        body {
            background-color: rgb(var(--bg-primary));
            color: rgb(var(--text-primary));
            transition: background-color 0.3s, color 0.3s;
        }

        /* ============================================
           UTILITY CLASSES
           ============================================ */
        .bg-themed-primary {
            background-color: rgb(var(--bg-primary));
            transition: background-color 0.3s;
        }

        .bg-themed-secondary {
            background-color: rgb(var(--bg-secondary));
            transition: background-color 0.3s;
        }

        .bg-themed-tertiary {
            background-color: rgb(var(--bg-tertiary));
            transition: background-color 0.3s;
        }

        .text-themed-primary {
            color: rgb(var(--text-primary));
            transition: color 0.3s;
        }

        .text-themed-secondary {
            color: rgb(var(--text-secondary));
            transition: color 0.3s;
        }

        .text-themed-tertiary {
            color: rgb(var(--text-tertiary));
            transition: color 0.3s;
        }

        .border-themed-primary {
            border-color: rgb(var(--border-primary));
            transition: border-color 0.3s;
        }

        .border-themed-secondary {
            border-color: rgb(var(--border-secondary));
            transition: border-color 0.3s;
        }

        .accent-themed-primary {
            color: rgb(var(--accent-primary));
            transition: color 0.3s;
        }

        .accent-themed-secondary {
            color: rgb(var(--accent-secondary));
            transition: color 0.3s;
        }

        .bg-accent-themed-primary {
            background-color: rgb(var(--accent-primary));
            transition: background-color 0.3s;
        }

        .bg-accent-themed-secondary {
            background-color: rgb(var(--accent-secondary));
            transition: background-color 0.3s;
        }

        .bg-tab-active {
            background-color: rgb(var(--tab-active-bg));
            color: rgb(var(--tab-active-text));
            transition: all 0.3s;
        }

        .bg-tab-inactive {
            background-color: rgb(var(--tab-inactive-bg));
            color: rgb(var(--tab-inactive-text));
            transition: all 0.3s;
        }

        /* ============================================
           TAB NAVIGATION
           ============================================ */
        .tab-nav {
            display: flex;
            gap: 0;
            border-bottom: 2px solid rgb(var(--border-primary));
            overflow-x: auto;
            transition: border-color 0.3s;
        }

        .tab-button {
            padding: 1rem 1.25rem;
            background-color: transparent;
            color: rgb(var(--tab-inactive-text));
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 0.25rem;
        }

        .tab-button:hover:not(.active) {
            background-color: rgb(var(--tab-inactive-bg) / 0.5);
            color: rgb(var(--tab-inactive-text));
        }

        .tab-button.active {
            background-color: rgb(var(--tab-active-bg));
            color: rgb(var(--tab-active-text));
            border-bottom-color: rgb(var(--tab-active-bg));
            box-shadow: 0 2px 8px rgba(var(--tab-active-bg), 0.3);
        }

        /* ============================================
           BUTTON STYLES
           ============================================ */
        .btn-primary {
            background-color: rgb(var(--accent-primary));
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: rgb(var(--accent-secondary));
            box-shadow: 0 4px 12px rgba(var(--accent-primary), 0.3);
        }

        .btn-secondary {
            background-color: rgb(var(--bg-tertiary));
            color: rgb(var(--text-primary));
            padding: 0.5rem 1rem;
            border: 1px solid rgb(var(--border-primary));
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background-color: rgb(var(--btn-hover-bg));
            border-color: rgb(var(--accent-primary));
        }

        .btn-secondary:disabled {
            color: rgb(var(--btn-disabled-text));
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* ============================================
           INPUT STYLES
           ============================================ */
        input,
        textarea,
        select {
            background-color: rgb(var(--bg-secondary));
            color: rgb(var(--text-primary));
            border: 1px solid rgb(var(--input-border));
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s;
            font-family: inherit;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: rgb(var(--input-focus-border));
            box-shadow: 0 0 0 3px rgba(var(--input-focus-border), 0.1);
        }

        input:disabled,
        textarea:disabled,
        select:disabled {
            background-color: rgb(var(--bg-tertiary));
            color: rgb(var(--btn-disabled-text));
            cursor: not-allowed;
        }
    </style>


<!-- MathJax Configuration and Loading -->
<script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']]
      },
      svg: {
        fontCache: 'global'
      },
      startup: {
        pageReady: function() {
          return MathJax.startup.defaultPageReady().then(function() {
            console.log('MathJax is ready');
            // Dispatch event when MathJax is fully loaded
            document.dispatchEvent(new Event('mathjax-loaded'));
          });
        }
      }
    };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" async></script>

</head>

<body class="font-sans antialiased transition-colors duration-300">
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TJ23X96Z"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <!-- Email Verification Alert Component -->
    @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
        <livewire:components.email-verification-alert />
    @endif

    @php
        $user = Auth::user();
    @endphp

    <div class="flex min-h-screen">
        <!-- Desktop Sidebar -->
        @livewire('dashboard-sidebar')

        <!-- Sidebar Overlay for Mobile -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-themed-primary bg-opacity-50 lg:hidden" style="display: none;"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64 transition-all duration-300">
            <!-- Top Navbar -->
            <x-navbar />

            <!-- Content Area -->
            <main class="flex-1 p-4 lg:p-6 pb-20 lg:pb-6">
                <!-- Welcome Banner -->
                @if($user)
                    <div
                        class="mb-6 p-4 bg-themed-secondary rounded-xl shadow-lg border border-themed-primary animate__animated animate__fadeInDown transition-colors duration-300">
                        <h1 class="text-xl lg:text-2xl font-bold text-themed-primary transition-colors duration-300">Welcome
                            back, {{ $user->name }}!</h1>
                        <p class="text-themed-secondary mt-1 transition-colors duration-300">
                            {{ ucfirst($user->getRoleNames()->first() ?? 'User') }} Dashboard</p>
                    </div>
                @endif

                <!-- Main Content Slot -->
                <div class="animate__animated animate__fadeIn">
                    {{ $slot }}
                </div>
            </main>
        </div>
        <x-mobile-bottom-nav />
    </div>
    @livewireScripts
</body>

</html>