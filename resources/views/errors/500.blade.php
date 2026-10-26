<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Unable to complete your request · BootKode</title>
    @if(is_file(public_path('build/manifest.json')))
        @vite('resources/css/app.css')
    @endif
</head>
<body class="min-h-screen bg-surface text-content">
    <a href="#main-content" class="sr-only focus:not-sr-only">Skip to content</a>
    <main id="main-content" tabindex="-1" class="mx-auto flex min-h-screen max-w-lg items-center px-4 py-12 sm:px-6">
        <x-ui.panel class="w-full space-y-5">
            <img src="{{ asset('img/logo.png') }}" alt="BootKode" width="48" height="48">
            <p class="text-sm font-semibold text-content-muted">Error 500</p>
            <h1 class="text-2xl font-bold sm:text-3xl">Unable to complete your request</h1>
            <p class="text-content-muted">Something went wrong. Please try again shortly.</p>
            <a href="{{ route('home') }}" class="bk-button bk-button-primary">Return home</a>
        </x-ui.panel>
    </main>
</body>
</html>
