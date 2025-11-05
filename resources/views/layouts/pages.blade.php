<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="cmciE9Iqsl6Gl3u_0Zts_-SlchWbsZZ_8OMVpELH3CA" />

    <title>{{ config('app.name', 'BootKode') }} @hasSection('title') - @yield('title') @endif</title>

    <meta name="description" content="@yield('meta_description', 'BootKode: Empowering Africa\'s youth with digital skills, mentorship, and careers. Learn to code, get certified, and conquer the tech world.')">
    <meta name="keywords" content="@yield('meta_keywords', 'BootKode, coding, tech education, Africa, Nigeria, digital skills, mentorship, careers, Laravel, Vue.js, web development, programming, certification, online courses')">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'BootKode: Empowering Africa\'s youth with digital skills, mentorship, and careers.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="@yield('og_image', asset('img/logo.png'))">
    <meta property="og:site_name" content="{{ config('app.name', 'BootKode') }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@BootKodeAfrica">
    <meta name="twitter:title" content="@yield('og_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('og_description', 'BootKode: Empowering Africa\'s youth with digital skills, mentorship, and careers.')">
    <meta name="twitter:image" content="@yield('og_image', asset('img/logo.png'))">

    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Mobile Optimizations -->
    <style>
        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }
        body {
            min-width: 320px;
        }
        @media (max-width: 640px) {
            button, a, input, select, textarea {
                min-height: 44px;
            }
        }
    </style>

    @yield('styles')
</head>

<body class="font-san antialiased text-gray-900 bg-gray-50 flex flex-col min-h-screen w-full overflow-x-hidden">
    <x-header />
    <main class="flex-grow w-full">
        <div class="w-full px-4 py-6 sm:px-6 sm:py-8 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            @yield('content')
        </div>
    </main>
    <x-footer />

    <script>
        // Prevent zoom on input focus on iOS
        document.addEventListener('DOMContentLoaded', function() {
            if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
                const viewportMeta = document.querySelector('meta[name="viewport"]');
                viewportMeta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
            }
        });

        // Add viewport height CSS custom property for mobile browsers
        function setViewportHeight() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
        setViewportHeight();
        window.addEventListener('resize', setViewportHeight);
        window.addEventListener('orientationchange', setViewportHeight);
    </script>

    @yield('scripts')
</body>

</html>