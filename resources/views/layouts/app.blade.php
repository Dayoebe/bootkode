<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="BootKode">
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

    {{-- 🧠 Dynamic Title for SEO --}}
    <title>
        {{ config('app.name', 'BootKode') }}
        @hasSection('title') - @yield('title') @endif
    </title>

    {{-- 🧭 SEO Meta Tags --}}

    <meta name="google-site-verification" content="cmciE9Iqsl6Gl3u_0Zts_-SlchWbsZZ_8OMVpELH3CA" />
    <meta name="description"
        content="@yield('description', 'BootKode: Empowering Africa\'s youth with digital skills, mentorship, and careers. Learn to code, get certified, and conquer the tech world.')">
    <meta name="keywords"
        content="@yield('keywords', 'BootKode, coding, tech education, Africa, Nigeria, digital skills, mentorship, careers, Laravel, Vue.js, web development, programming, certification, online courses')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- 🌍 Open Graph (for social sharing) --}}
    <meta property="og:title"
        content="{{ config('app.name', 'BootKode') }}@hasSection('title') - @yield('title') @endif">
    <meta property="og:description"
        content="@yield('description', 'BootKode: Empowering Africa\'s youth with digital skills, mentorship, and careers.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('img/logo.png') }}">
    <meta property="og:site_name" content="{{ config('app.name', 'BootKode') }}">

    {{-- 🐦 Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@BootKodeAfrica">
    <meta name="twitter:title"
        content="{{ config('app.name', 'BootKode') }}@hasSection('title') - @yield('title') @endif">
    <meta name="twitter:description"
        content="@yield('description', 'BootKode: Empowering Africa\'s youth with digital skills and careers.')">
    <meta name="twitter:image" content="{{ asset('img/logo.png') }}">

    {{-- 🧩 Favicons --}}
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    {{-- 🪶 Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- ✨ Animations --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
        referrerpolicy="no-referrer" />

    {{-- ⚙️ Vite Assets (Tailwind, Alpine, etc.) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/notifications.js'])

    {{-- ⚡ Livewire Styles --}}
    @livewireStyles

    {{-- 🧩 Mobile-friendly tweaks --}}
    <style>
        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            min-width: 320px;
        }

        @media (max-width: 640px) {
            button,
            a,
            input,
            select,
            textarea {
                min-height: 44px;
            }
        }
    </style>
</head>

<body class="bk-public-app font-sans antialiased text-slate-900 bg-slate-50 flex flex-col min-h-screen w-full overflow-x-hidden">

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TJ23X96Z"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    {{-- 🌐 Global Header --}}
    <x-header />

    {{-- 📄 Page Content --}}
    <main class="flex-grow w-full">
        <div class="w-full px-4 py-5 sm:px-6 sm:py-7 md:px-8 lg:px-10 xl:px-12 2xl:px-16">
            {{ $slot }}
        </div>
    </main>

    {{-- 🌐 Global Footer --}}
    <x-footer />

    {{-- ⚡ Livewire Scripts --}}
    @livewireScripts

    {{-- 🧠 Lazy-load External Libraries --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loadScript = (src, async = true) => {
                const s = document.createElement('script');
                s.src = src;
                s.async = async;
                document.body.appendChild(s);
            };

            // Load Trix only if editor is present
            if (document.querySelector('trix-editor')) {
                loadScript('https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js');
            }

            // Load Editor.js only if editor container exists
            if (document.querySelector('#editorjs')) {
                loadScript('https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest');
                loadScript('https://cdn.jsdelivr.net/npm/@editorjs/header@latest');
                loadScript('https://cdn.jsdelivr.net/npm/@editorjs/list@latest');
                loadScript('https://cdn.jsdelivr.net/npm/@editorjs/image@latest');
            }

            // Load Chart.js if chart canvas is found
            if (document.querySelector('#chart') || document.querySelector('.chartjs')) {
                loadScript('https://cdn.jsdelivr.net/npm/chart.js');
            }
        });
    </script>

    {{-- 📱 Viewport Fix for Mobile Browsers --}}
    <script>
        const setViewportHeight = () => {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        };

        setViewportHeight();
        window.addEventListener('resize', setViewportHeight);
        window.addEventListener('orientationchange', setViewportHeight);
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js').catch(() => {});
            });
        }
    </script>
</body>

</html>
