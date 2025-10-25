{{-- UPDATED: newsletter-center.blade.php --}}
<div class="w-full min-h-screen bg-gray-50 overflow-x-hidden">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center py-4 md:py-6 space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-600 p-3 rounded-lg animate__animated animate__fadeInLeft">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <div class="animate__animated animate__fadeInUp">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Newsletter Management</h1>
                            <p class="text-gray-600 mt-1 text-sm md:text-base">Manage subscribers, campaigns, and email marketing</p>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-2 md:space-x-4 animate__animated animate__fadeInRight">
                        <button 
                            wire:click="createCampaign"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg font-medium transition-all duration-200 text-sm md:text-base"
                        >
                            <i class="fas fa-plus mr-1 md:mr-2"></i>
                            <span class="hidden sm:inline">New Campaign</span>
                        </button>
                        <button 
                            wire:click="importSubscribers"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg font-medium transition-all duration-200 text-sm md:text-base"
                        >
                            <i class="fas fa-upload mr-1 md:mr-2"></i>
                            <span class="hidden sm:inline">Import</span>
                        </button>
                        <button 
                            wire:click="viewPerformance"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg font-medium transition-all duration-200 text-sm md:text-base"
                        >
                            <i class="fas fa-tachometer-alt mr-1 md:mr-2"></i>
                            <span class="hidden sm:inline">Performance</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Dashboard -->
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 md:gap-4 animate__animated animate__fadeInUp">
                <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-200 text-center">
                    <div class="text-lg md:text-2xl font-bold text-blue-600">{{ number_format($stats['total_subscribers']) }}</div>
                    <div class="text-xs md:text-sm text-gray-500">Subscribers</div>
                </div>
                <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-200 text-center">
                    <div class="text-lg md:text-2xl font-bold text-green-600">{{ number_format($stats['active_subscribers']) }}</div>
                    <div class="text-xs md:text-sm text-gray-500">Active</div>
                </div>
                <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-200 text-center">
                    <div class="text-lg md:text-2xl font-bold text-purple-600">{{ number_format($stats['total_campaigns']) }}</div>
                    <div class="text-xs md:text-sm text-gray-500">Campaigns</div>
                </div>
                <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-200 text-center">
                    <div class="text-lg md:text-2xl font-bold text-indigo-600">{{ number_format($stats['campaigns_sent']) }}</div>
                    <div class="text-xs md:text-sm text-gray-500">Sent</div>
                </div>
                <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-200 text-center">
                    <div class="text-lg md:text-2xl font-bold text-orange-600">{{ number_format($stats['total_templates']) }}</div>
                    <div class="text-xs md:text-sm text-gray-500">Templates</div>
                </div>
                <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-200 text-center">
                    <div class="text-lg md:text-2xl font-bold text-teal-600">{{ $stats['avg_open_rate'] }}%</div>
                    <div class="text-xs md:text-sm text-gray-500">Open Rate</div>
                </div>
                <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-200 text-center">
                    <div class="text-lg md:text-2xl font-bold text-pink-600">{{ $stats['avg_click_rate'] }}%</div>
                    <div class="text-xs md:text-sm text-gray-500">Click Rate</div>
                </div>
                <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-200 text-center relative">
                    <div class="text-lg md:text-2xl font-bold text-emerald-600">{{ number_format($stats['recent_signups']) }}</div>
                    <div class="text-xs md:text-sm text-gray-500">Recent</div>
                    <button 
                        wire:click="refreshStats"
                        class="absolute top-1 right-1 p-1 text-gray-400 hover:text-gray-600 rounded transition-colors"
                        title="Refresh Statistics"
                    >
                        <i class="fas fa-sync-alt text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-2 md:space-x-4 overflow-x-auto pb-1">
                    @php
                        $tabs = [
                            'subscribers' => ['label' => 'Subscribers', 'icon' => 'fas fa-users', 'full_label' => 'Manage Subscribers'],
                            'campaigns' => ['label' => 'Campaigns', 'icon' => 'fas fa-paper-plane', 'full_label' => 'Email Campaigns'],
                            'templates' => ['label' => 'Templates', 'icon' => 'fas fa-newspaper', 'full_label' => 'Newsletter Templates'],
                            'analytics' => ['label' => 'Analytics', 'icon' => 'fas fa-chart-bar', 'full_label' => 'Campaign Analytics'],
                            'reports' => ['label' => 'Reports', 'icon' => 'fas fa-chart-pie', 'full_label' => 'Campaign Reports'],
                            'performance' => ['label' => 'Performance', 'icon' => 'fas fa-tachometer-alt', 'full_label' => 'Performance & Optimization'],
                        ];
                        
                        // Add settings tab only for super admin
                        if($user->hasRole(App\Models\Core\User::ROLE_SUPER_ADMIN)) {
                            $tabs['settings'] = ['label' => 'Settings', 'icon' => 'fas fa-cog', 'full_label' => 'Newsletter Settings'];
                        }
                    @endphp

                    @foreach($tabs as $tab => $tabData)
                        <button
                            wire:click="setActiveTab('{{ $tab }}')"
                            class="{{ $activeTab === $tab 
                                ? 'border-blue-500 text-blue-600 bg-blue-50' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' 
                            }} whitespace-nowrap py-2 md:py-3 px-3 md:px-4 border-b-2 font-medium text-xs md:text-sm flex items-center rounded-t-lg transition-all duration-200"
                            title="{{ $tabData['full_label'] }}"
                        >
                            <i class="{{ $tabData['icon'] }} mr-1 md:mr-2"></i>
                            <span class="hidden sm:inline">{{ $tabData['label'] }}</span>
                            
                            @if($activeTab === $tab)
                                <span class="ml-1 md:ml-2 bg-blue-500 text-white text-xs px-1 md:px-2 py-0.5 md:py-1 rounded-full animate__animated animate__fadeInUp animate__faster hidden md:inline">
                                    Active
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
                @if($activeTab === 'subscribers')
                    @livewire('newsletter.partials.subscriber-management')
                @elseif($activeTab === 'campaigns')
                    @livewire('newsletter.partials.campaign-management')
                @elseif($activeTab === 'templates')
                    @livewire('newsletter.partials.template-management')
                @elseif($activeTab === 'analytics')
                    @livewire('newsletter.partials.analytics')
                @elseif($activeTab === 'reports')
                    @livewire('newsletter.partials.campaign-reports')
                @elseif($activeTab === 'performance')
                    @livewire('newsletter.partials.performance-optimization')
                @elseif($activeTab === 'settings')
                    @livewire('newsletter.partials.settings')
                @endif
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 shadow-xl animate__animated animate__fadeIn">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">Loading newsletter data...</span>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
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
                 class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
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
                 class="fixed top-4 right-4 bg-yellow-500 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="text-sm">{{ session('warning') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>