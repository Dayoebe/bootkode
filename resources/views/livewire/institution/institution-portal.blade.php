<div class="w-full min-h-screen bg-gray-50 dark:bg-gray-900 overflow-x-hidden transition-colors duration-300">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center py-4 md:py-6 space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-600 dark:bg-blue-500 p-3 rounded-lg animate__animated animate__fadeInLeft transition-colors duration-300">
                            <i class="fas fa-university text-white text-xl"></i>
                        </div>
                        <div class="animate__animated animate__fadeInUp">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Institution Portal</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm md:text-base">Manage institutional partnerships and licensing</p>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="flex items-center space-x-4 md:space-x-6 animate__animated animate__fadeInRight overflow-x-auto pb-2 md:pb-0 w-full md:w-auto">
                        <div class="text-center min-w-[80px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['total_institutions']) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Institutions</div>
                        </div>
                        <div class="text-center min-w-[80px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['active_institutions']) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Active</div>
                        </div>
                        <div class="text-center min-w-[80px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['total_users']) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Users</div>
                        </div>
                        <div class="text-center min-w-[80px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['pending_approvals']) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Pending</div>
                        </div>
                        @if($stats['expiring_licenses'] > 0)
                        <div class="text-center min-w-[80px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['expiring_licenses']) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Expiring</div>
                        </div>
                        @endif
                        <button 
                            wire:click="refreshStats"
                            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200"
                            title="Refresh Statistics"
                        >
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Quick Filters -->
                <div class="flex flex-col sm:flex-row gap-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600 dark:text-gray-400">Period:</label>
                        <select wire:model.live="selectedPeriod" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="7">Last 7 days</option>
                            <option value="30">Last 30 days</option>
                            <option value="90">Last 90 days</option>
                            <option value="365">Last year</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600 dark:text-gray-400">Type:</label>
                        <select wire:model.live="selectedInstitutionType" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Types</option>
                            @foreach($institutionTypes as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-2 md:space-x-4 overflow-x-auto pb-1">
                    @foreach([
                            'overview' => ['label' => 'Overview', 'icon' => 'fas fa-chart-line', 'full_label' => 'Dashboard Overview'],
                            'partners' => ['label' => 'Partners', 'icon' => 'fas fa-school', 'full_label' => 'Partner Institutions'],
                            'licenses' => ['label' => 'Licenses', 'icon' => 'fas fa-key', 'full_label' => 'License Management'],
                            'bulk-enrollment' => ['label' => 'Bulk Enroll', 'icon' => 'fas fa-user-plus', 'full_label' => 'Bulk Enrollment'],
                            'analytics' => ['label' => 'Analytics', 'icon' => 'fas fa-chart-pie', 'full_label' => 'Institution Analytics'],
                            'whitelabel' => ['label' => 'Branding', 'icon' => 'fas fa-paint-roller', 'full_label' => 'White-label Settings'],
                        ] as $tab => $tabData)
                        <button
                            wire:click="setActiveTab('{{ $tab }}')"
                            class="{{ $activeTab === $tab 
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/50' 
                            }} whitespace-nowrap py-2 md:py-3 px-3 md:px-4 border-b-2 font-medium text-xs md:text-sm flex items-center rounded-t-lg transition-all duration-200"
                            title="{{ $tabData['full_label'] }}"
                        >
                            <i class="{{ $tabData['icon'] }} mr-1 md:mr-2"></i>
                            <span class="hidden sm:inline">{{ $tabData['label'] }}</span>
                            
                            @if($activeTab === $tab)
                                <span class="ml-1 md:ml-2 bg-blue-500 dark:bg-blue-600 text-white text-xs px-1 md:px-2 py-0.5 md:py-1 rounded-full animate__animated animate__fadeInUp animate__faster hidden md:inline">
                                    Active
                                </span>
                            @endif

                            <!-- Add notification badges for specific tabs -->
                            @if($tab === 'partners' && $stats['pending_approvals'] > 0)
                                <span class="ml-1 md:ml-2 bg-red-500 text-white text-xs px-1 md:px-2 py-0.5 md:py-1 rounded-full">
                                    {{ $stats['pending_approvals'] }}
                                </span>
                            @endif
                            @if($tab === 'licenses' && $stats['expiring_licenses'] > 0)
                                <span class="ml-1 md:ml-2 bg-yellow-500 text-white text-xs px-1 md:px-2 py-0.5 md:py-1 rounded-full">
                                    {{ $stats['expiring_licenses'] }}
                                </span>
                            @endif
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Content Area -->
        <div class="px-4 sm:px-6 lg:px-8 py-4 overflow-x-auto" wire:loading.class="opacity-50 pointer-events-none">
            <div class="animate__animated animate__fadeIn w-full min-w-0">
                @if($activeTab === 'overview')
                    @include('livewire.institution.partials.overview')
                @elseif($activeTab === 'partners')
                    @livewire('institution.partner-institutions')
                @elseif($activeTab === 'licenses')
                    @livewire('institution.license-management')
                @elseif($activeTab === 'bulk-enrollment')
                    @livewire('institution.bulk-enrollment')
                @elseif($activeTab === 'analytics')
                    @livewire('institution.institution-analytics')
                @elseif($activeTab === 'whitelabel')
                    @livewire('institution.whitelabel-settings')
                @endif
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl animate__animated animate__fadeIn">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 dark:border-blue-400"></div>
                    <span class="text-gray-700 dark:text-gray-300">Loading content...</span>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-green-500 dark:bg-green-600 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-sm">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-red-500 dark:bg-red-600 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('warning'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-yellow-500 dark:bg-yellow-600 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="text-sm">{{ session('warning') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>