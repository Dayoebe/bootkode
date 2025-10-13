<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mock Interviews Management</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage interviews, templates, and analytics</p>
                </div>

                <!-- Quick Stats -->
                <div class="hidden lg:flex space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->statistics['totalInterviews']) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Interviews</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($this->statistics['dailyInterviews']) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Today</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($this->statistics['averageScore'], 1) }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Avg Score</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="$set('activeTab', 'overview')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    Overview
                </button>
                <button wire:click="$set('activeTab', 'interviews')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'interviews' ? 'border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    All Interviews
                </button>
                <button wire:click="$set('activeTab', 'analytics')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'analytics' ? 'border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    Analytics
                </button>
                <button wire:click="$set('activeTab', 'users')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'users' ? 'border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    Users
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        @if($activeTab === 'overview')
            @include('livewire.career.admin-mock.overview-tab')
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