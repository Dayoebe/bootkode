@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <x-header />

    {{-- @include('partials.header') --}}
    
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    @yield('dashboard-title', 'Dashboard')
                </h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <div>
                                <a href="{{ route(auth()->user()->getDashboardRouteName()) }}" class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-home"></i>
                                    <span class="sr-only">Home</span>
                                </a>
                            </div>
                        </li>
                        @yield('breadcrumbs')
                    </ol>
                </nav>
            </div>
            
            <div class="bg-white shadow rounded-lg">
                @yield('dashboard-content')
            </div>
        </div>
    </main>
    <x-footer />
    {{-- @include('partials.footer') --}}
</div>
@endsection