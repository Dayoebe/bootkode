<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Mentor Dashboard</h1>
                    <p class="text-xl text-gray-600 dark:text-gray-400">Manage your mentorship activities and grow your impact</p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $activeMentees }}</div>
                        <div class="text-sm opacity-90">Active Mentees</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $totalSessions }}</div>
                        <div class="text-sm opacity-90">Total Sessions</div>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $upcomingSessions }}</div>
                        <div class="text-sm opacity-90">Upcoming</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ number_format($averageRating, 1) }}</div>
                        <div class="text-sm opacity-90">Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg relative animate-fade-in">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative animate-fade-in">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="px-6 py-8">
        <!-- Navigation Tabs -->
        <div class="mb-8">
            <nav class="flex flex-wrap space-x-2 bg-white dark:bg-gray-800 rounded-xl p-2 shadow-sm border border-gray-200 dark:border-gray-700">
                @foreach([
                    'overview' => ['label' => 'Overview', 'icon' => 'fas fa-tachometer-alt'],
                    'mentorships' => ['label' => 'My Mentees', 'icon' => 'fas fa-users'],
                    'sessions' => ['label' => 'Sessions', 'icon' => 'fas fa-calendar-check'],
                    'code-reviews' => ['label' => 'Code Reviews', 'icon' => 'fas fa-code'],
                    'profile' => ['label' => 'Profile', 'icon' => 'fas fa-user-edit'],
                    'analytics' => ['label' => 'Analytics', 'icon' => 'fas fa-chart-line']
                ] as $tab => $data)
                    <button wire:click="setActiveTab('{{ $tab }}')"
                        class="{{ $activeTab === $tab 
                            ? 'bg-blue-600 dark:bg-blue-500 text-white shadow-md' 
                            : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700' 
                        }} px-6 py-3 rounded-lg font-semibold transition-all">
                        <i class="{{ $data['icon'] }} mr-2"></i>{{ $data['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-8" wire:loading.class="opacity-50 pointer-events-none">
            <div class="animate__animated animate__fadeIn">
                @if($activeTab === 'overview')
                    @livewire('mentorship.partial.overview')
                @elseif($activeTab === 'mentorships')
                    @livewire('mentorship.partial.mentorships')
                @elseif($activeTab === 'sessions')
                    @livewire('mentorship.sessions')
                @elseif($activeTab === 'code-reviews')
                    @livewire('mentorship.code-reviews')
                @elseif($activeTab === 'profile')
                    @livewire('mentorship.partial.profile')
                @elseif($activeTab === 'analytics')
                    @livewire('mentorship.partial.analytics')
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 dark:border-blue-400"></div>
                <span class="text-gray-700 dark:text-gray-300">Processing...</span>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</div>