@extends('layouts.pages')

@section('title', $page->getMetaTitle())
@section('meta_description', $page->getMetaDescription())
@section('meta_keywords', $page->meta_keywords)

@if($page->no_index)
    @section('meta_robots', 'noindex, nofollow')
@endif

@section('og_title', $page->getMetaTitle())
@section('og_description', $page->getMetaDescription())
@section('og_image', $page->getOgImage())

@section('content')
    <article class="page-template-{{ $page->template }}">
        @if($page->featuredMedia && $page->featuredMedia->count())
            <div class="page-featured-image">
                @php
                    $featuredMedia = $page->featuredMedia->first();
                @endphp
                <img src="{{ $featuredMedia->getUrl() }}" alt="{{ $featuredMedia->alt_text ?? $page->title }}">
            </div>
        @endif
        
        <header class="page-header">
            <h1 class="page-title">{{ $page->title }}</h1>
            
            @if($page->settings['enable_reading_time'] ?? false)
                <div class="page-meta">
                    <span class="reading-time">
                        <i class="fas fa-clock"></i>
                        {{ ceil(str_word_count(strip_tags($page->content)) / 200) }} min read
                    </span>
                </div>
            @endif
        </header>
        
        <div class="page-content">
            {!! $page->getProcessedContent() !!}
        </div>
        
        @if($page->settings['enable_sharing'] ?? true)
            <footer class="page-footer">
                <div class="share-buttons">
                    <span>Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($page->title) }}&url={{ urlencode(url()->current()) }}" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($page->title) }}" target="_blank">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </footer>
        @endif
    </article>
@endsection

@section('styles')
    @if($page->custom_css && ($page->settings['custom_css_enabled'] ?? false))
        <style>
            {!! implode(' ', $page->custom_css) !!}
        </style>
    @endif
@endsection

@section('scripts')
    @if($page->custom_js && ($page->settings['custom_js_enabled'] ?? false))
        <script>
            {!! implode(' ', $page->custom_js) !!}
        </script>
    @endif
@endsection