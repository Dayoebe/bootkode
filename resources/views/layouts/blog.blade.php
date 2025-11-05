{{-- resources/views/layouts/blog.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TJ23X96Z');</script>
    <!-- End Google Tag Manager -->
    <meta name="google-site-verification" content="cmciE9Iqsl6Gl3u_0Zts_-SlchWbsZZ_8OMVpELH3CA" />

    {{-- Dynamic Meta Tags --}}
    @isset($post)
        <title>{{ $post->meta_title ?: $post->title }} - {{ config('app.name') }}</title>
        <meta name="description" content="{{ $post->meta_description ?: $post->excerpt }}">
        
        @if($post->meta_keywords)
            <meta name="keywords" content="{{ implode(', ', $post->meta_keywords) }}">
        @endif

        {{-- Open Graph Tags --}}
        <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
        <meta property="og:description" content="{{ $post->meta_description ?: $post->excerpt }}">
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ request()->url() }}">
        
        @if($post->featured_image)
            <meta property="og:image" content="{{ Storage::url($post->featured_image) }}">
        @endif
        
        <meta property="article:author" content="{{ $post->author->name }}">
        <meta property="article:published_time" content="{{ $post->published_at->toISOString() }}">
        
        @if($post->category)
            <meta property="article:section" content="{{ $post->category->name }}">
        @endif

        {{-- Twitter Cards --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $post->meta_title ?: $post->title }}">
        <meta name="twitter:description" content="{{ $post->meta_description ?: $post->excerpt }}">
        
        @if($post->featured_image)
            <meta name="twitter:image" content="{{ Storage::url($post->featured_image) }}">
        @endif

        {{-- Structured Data --}}
        <script type="application/ld+json">
        @php
            $structuredData = [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'description' => $post->excerpt,
                'image' => $post->featured_image ? Storage::url($post->featured_image) : '',
                'author' => [
                    '@type' => 'Person',
                    'name' => $post->author->name
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('app.name')
                ],
                'datePublished' => $post->published_at->toISOString(),
                'dateModified' => $post->updated_at->toISOString(),
                'wordCount' => str_word_count(strip_tags($post->content)),
                'timeRequired' => 'PT' . $post->read_time . 'M'
            ];
        @endphp
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES) !!}
        </script>
    @else
        <title>{{ $title ?? 'Blog' }} - {{ config('app.name') }}</title>
        <meta name="description" content="Discover insights, stories, and knowledge from our experts">
    @endisset

    <link rel="canonical" href="{{ request()->url() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        .prose {
            max-width: none;
        }

        .prose img {
            @apply rounded-lg shadow-md my-6;
        }

        .prose h1,
        .prose h2,
        .prose h3,
        .prose h4 {
            @apply font-bold text-gray-900;
        }

        .prose h1 {
            @apply text-3xl mb-4;
        }

        .prose h2 {
            @apply text-2xl mb-3;
        }

        .prose h3 {
            @apply text-xl mb-2;
        }

        .prose p {
            @apply mb-4 leading-relaxed;
        }

        .prose blockquote {
            @apply border-l-4 border-indigo-500 pl-4 italic text-gray-600 my-4;
        }

        .prose code {
            @apply bg-gray-100 px-2 py-1 rounded text-sm;
        }

        .prose pre {
            @apply bg-gray-900 text-white p-4 rounded-lg overflow-x-auto my-4;
        }

        .prose a {
            @apply text-indigo-600 hover:text-indigo-800 underline;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TJ23X96Z"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div class="min-h-screen">
        {{-- Navigation --}}
        <x-header />


        {{-- Page Content --}}
        <main>
            <div class="w-full px-4 py-6 sm:px-6 sm:py-8 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
                {{ $slot }}
            </div> 
        </main>

        {{-- Footer --}}
        <x-footer />
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>