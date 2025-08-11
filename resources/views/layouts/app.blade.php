<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamic Title for SEO --}}
    <title>{{ config('app.name', 'BootKode') }} @hasSection('title') - @yield('title')@endif</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="@yield('description', 'BootKode: Empowering Africa\'s youth with digital skills, mentorship, and careers. Learn to code, get certified, and conquer the tech world.')">
    <meta name="keywords" content="@yield('keywords', 'BootKode, coding, tech education, Africa, Nigeria, digital skills, mentorship, careers, Laravel, Vue.js, web development, programming, certification, online courses')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph Meta Tags (for social media sharing) --}}
    <meta property="og:title" content="{{ config('app.name', 'BootKode') }} @hasSection('title') - @yield('title')@endif">
    <meta property="og:description" content="@yield('description', 'BootKode: Empowering Africa\'s youth with digital skills, mentorship, and careers. Learn to code, get certified, and conquer the tech world.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('img/logo.png') }}"> {{-- Replace with your actual social share image --}}
    <meta property="og:site_name" content="{{ config('app.name', 'BootKode') }}">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@BootKodeAfrica"> {{-- Replace with your Twitter handle --}}
    <meta name="twitter:title" content="{{ config('app.name', 'BootKode') }} @hasSection('title') - @yield('title')@endif">
    <meta name="twitter:description" content="@yield('description', 'BootKode: Empowering Africa\'s youth with digital skills, mentorship, and careers. Learn to code, get certified, and conquer the tech world.')">
    <meta name="twitter:image" content="{{ asset('img/logo.png') }}"> {{-- Replace with your actual Twitter card image --}}

    
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/x-icon"> 

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 (for icons) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Animate.css (for animations) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    {{-- Vite Scripts (Tailwind CSS, Alpine.js, etc.) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Additional Mobile Optimizations --}}
    <style>
        /* Ensure smooth scrolling and prevent horizontal overflow */
        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }
        
        /* Prevent content from being too narrow on very small screens */
        body {
            min-width: 320px;
        }
        
        /* Better touch targets for mobile */
        @media (max-width: 640px) {
            button, a, input, select, textarea {
                min-height: 44px;
            }
        }
        
     
    </style>
</head>
<body class="font-inter antialiased text-gray-900 bg-gray-50 flex flex-col min-h-screen w-full overflow-x-hidden">
    {{-- Header Section --}}
    
        <x-header /> {{-- Using the Header component --}}
    

    {{-- Main Content Area - Mobile-first responsive --}}
    <main class="flex-grow w-full">
        <div class="w-full px-4 py-6 sm:px-6 sm:py-8 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            <div class="max-w-7xl mx-auto w-full">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Footer Section --}}
    
        <x-footer /> {{-- Using the Footer component --}}
    

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Mobile-specific JavaScript optimizations --}}
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
</body>
</html>
