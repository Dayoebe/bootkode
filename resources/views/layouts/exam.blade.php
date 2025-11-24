<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CBT System') }} - Exam Mode</title>

    <!-- Meta tags -->
    <meta name="google-adsense-account" content="ca-pub-3911204427206897">
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10833921436"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'AW-10833921436');
    </script>

    <!-- Security headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Fonts and assets -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- MathJax Configuration - OPTIMIZED & CLEAN -->
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
                pageReady: function () {
                    return MathJax.startup.defaultPageReady().then(function () {
                        document.dispatchEvent(new Event('mathjax-loaded'));
                    });
                }
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
                // Disable the MathJax menu to remove blue clickable boxes
                renderActions: {
                    addMenu: []
                }
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" async></script>
    @push('styles')
    <style>
        mjx-container {
            all: revert !important;
            pointer-events: none !important;
        }
        
        mjx-container[jax="CHTML"] {
            cursor: default !important;
            pointer-events: none !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            background: transparent !important;
            outline: none !important;
            border: none !important;
            box-shadow: none !important;
            text-decoration: none !important;
        }
        
        mjx-container[jax="CHTML"]:hover,
        mjx-container[jax="CHTML"]:focus {
            background-color: transparent !important;
            outline: none !important;
            text-decoration: none !important;
        }
        
        mjx-container mjx-math {
            pointer-events: none !important;
            text-decoration: none !important;
        }
        
        /* Hide context menu on math elements */
        mjx-container {
            -webkit-touch-callout: none !important;
        }
    </style>
    
    @endpush

    @stack('styles')
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans antialiased" id="examBody">
    {{-- Main Content --}}
    <main class="h-full">
        {{ $slot }}
    </main>

    @livewireScripts
    
    @stack('scripts')
</body>
</html>