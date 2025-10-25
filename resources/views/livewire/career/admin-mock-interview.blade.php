<div class="min-h-screen bg-themed-primary">
    <!-- Header -->
    <div class="shadow-sm border-b border-themed-primary bg-themed-secondary">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-themed-primary">Mock Interviews Management</h1>
                    <p class="mt-1 text-themed-secondary">Manage interviews, templates, and analytics</p>
                </div>

                <!-- Quick Stats -->
                <div class="hidden lg:flex space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-accent-themed-primary">
                            {{ number_format($this->statistics['totalInterviews']) }}</div>
                        <div class="text-sm text-themed-secondary">Total Interviews</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ number_format($this->statistics['dailyInterviews']) }}</div>
                        <div class="text-sm text-themed-secondary">Today</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ number_format($this->statistics['averageScore'], 1) }}%</div>
                        <div class="text-sm text-themed-secondary">Avg Score</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class=" px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class=" px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tab Navigation -->
        <div class="border-b border-themed-primary mb-8">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="$set('activeTab', 'overview')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'overview' ? 'border-accent-themed-primary text-accent-themed-primary' : 'border-transparent text-themed-secondary hover:text-themed-primary' }}">
                    Overview
                </button>
                <button wire:click="$set('activeTab', 'questions')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'questions' ? 'border-accent-themed-primary text-accent-themed-primary' : 'border-transparent text-themed-secondary hover:text-themed-primary' }}">
                    Question Bank
                </button>
                <button wire:click="$set('activeTab', 'interviews')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'interviews' ? 'border-accent-themed-primary text-accent-themed-primary' : 'border-transparent text-themed-secondary hover:text-themed-primary' }}">
                    All Interviews
                </button>
                <button wire:click="$set('activeTab', 'analytics')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'analytics' ? 'border-accent-themed-primary text-accent-themed-primary' : 'border-transparent text-themed-secondary hover:text-themed-primary' }}">
                    Analytics
                </button>
                <button wire:click="$set('activeTab', 'users')"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'users' ? 'border-accent-themed-primary text-accent-themed-primary' : 'border-transparent text-themed-secondary hover:text-themed-primary' }}">
                    Users
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        @if($activeTab === 'overview')
            @include('livewire.career.admin-mock.overview-tab')
        @endif

        @if($activeTab === 'questions')
            @include('livewire.career.admin-question-bank')
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