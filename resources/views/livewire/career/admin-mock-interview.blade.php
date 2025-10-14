<div class="min-h-screen" style="background-color: rgb(var(--bg-primary))">
    <!-- Header -->
    <div class="shadow-sm border-b"
        style="background-color: rgb(var(--bg-secondary)); border-color: rgb(var(--border-primary))">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold" style="color: rgb(var(--text-primary))">Mock Interviews Management
                    </h1>
                    <p class="mt-1" style="color: rgb(var(--text-secondary))">Manage interviews, templates, and
                        analytics</p>
                </div>

                <!-- Quick Stats -->
                <div class="hidden lg:flex space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold" style="color: rgb(var(--accent-primary))">
                            {{ number_format($this->statistics['totalInterviews']) }}</div>
                        <div class="text-sm" style="color: rgb(var(--text-secondary))">Total Interviews</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ number_format($this->statistics['dailyInterviews']) }}</div>
                        <div class="text-sm" style="color: rgb(var(--text-secondary))">Today</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ number_format($this->statistics['averageScore'], 1) }}%</div>
                        <div class="text-sm" style="color: rgb(var(--text-secondary))">Avg Score</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tab Navigation -->
        <div class="border-b mb-8" style="border-color: rgb(var(--border-primary))">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="$set('activeTab', 'overview')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    style="border-color: {{ $activeTab === 'overview' ? 'rgb(var(--accent-primary))' : 'transparent' }}; color: {{ $activeTab === 'overview' ? 'rgb(var(--accent-primary))' : 'rgb(var(--text-secondary))' }}">
                    Overview
                </button>
                <button wire:click="$set('activeTab', 'questions')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    style="border-color: {{ $activeTab === 'questions' ? 'rgb(var(--accent-primary))' : 'transparent' }}">
                    Question Bank
                </button>
                <button wire:click="$set('activeTab', 'interviews')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    style="border-color: {{ $activeTab === 'interviews' ? 'rgb(var(--accent-primary))' : 'transparent' }}; color: {{ $activeTab === 'interviews' ? 'rgb(var(--accent-primary))' : 'rgb(var(--text-secondary))' }}">
                    All Interviews
                </button>
                <button wire:click="$set('activeTab', 'analytics')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    style="border-color: {{ $activeTab === 'analytics' ? 'rgb(var(--accent-primary))' : 'transparent' }}; color: {{ $activeTab === 'analytics' ? 'rgb(var(--accent-primary))' : 'rgb(var(--text-secondary))' }}">
                    Analytics
                </button>
                <button wire:click="$set('activeTab', 'users')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    style="border-color: {{ $activeTab === 'users' ? 'rgb(var(--accent-primary))' : 'transparent' }}; color: {{ $activeTab === 'users' ? 'rgb(var(--accent-primary))' : 'rgb(var(--text-secondary))' }}">
                    Users
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        @if($activeTab === 'overview')
            @include('livewire.career.admin-mock.overview-tab')
        @endif
        @if($activeTab === 'questions')
            @include('livewire.career.admin-mock.question-bank-tab')
        @endif
        @if($activeTab === 'interviews')
            @include('livewire.career.admin-mock.interviews-tab')
        @endif

        @if($activeTab === 'analytics')
            @include('livewire.career.admin-mock.analytics-tab')
        @endif

        @if($activeTab === 'users')
            @include('livewire.career.admin-mock.users-tab')
        @endif
    </div>

    <!-- Modals -->
    @include('livewire.career.admin-mock.interview-details-modal')
    @include('livewire.career.admin-mock.user-details-modal')
</div>