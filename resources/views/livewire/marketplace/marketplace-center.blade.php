{{-- resources/views/livewire/marketplace/marketplace-center.blade.php --}}
<div class="w-full min-h-screen bg-gray-50 dark:bg-gray-900 overflow-x-hidden transition-colors duration-300">
    <div class="w-full mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center py-4 md:py-6 space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-600 dark:bg-purple-500 p-2 md:p-3 rounded-lg animate__animated animate__fadeInLeft transition-colors duration-300">
                            <i class="fas fa-store text-white text-lg md:text-xl"></i>
                        </div>
                        <div class="animate__animated animate__fadeInUp">
                            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Marketplace</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1 text-xs sm:text-sm md:text-base transition-colors duration-300">Browse, buy and sell courses, resources and services</p>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="flex flex-wrap items-center gap-3 md:gap-6 animate__animated animate__fadeInRight w-full md:w-auto mt-2 md:mt-0">
                        <div class="text-center min-w-[60px] sm:min-w-[70px]">
                            <div class="text-lg sm:text-xl md:text-2xl font-bold text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ $stats['total_items'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">Items</div>
                        </div>
                        @if($user)
                            <div class="text-center min-w-[60px] sm:min-w-[70px]">
                                <div class="text-lg sm:text-xl md:text-2xl font-bold text-blue-600 dark:text-blue-400 transition-colors duration-300">{{ $stats['my_orders'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">Orders</div>
                            </div>
                            @if($user->canManageCourses())
                                <div class="text-center min-w-[60px] sm:min-w-[70px]">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold text-green-600 dark:text-green-400 transition-colors duration-300">{{ $stats['my_listings'] }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">Listings</div>
                                </div>
                                <div class="text-center min-w-[60px] sm:min-w-[70px]">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold text-orange-600 dark:text-orange-400 transition-colors duration-300">₦{{ number_format($stats['total_earnings'], 0) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">Earnings</div>
                                </div>
                            @endif
                        @endif
                        <button 
                            wire:click="refreshStats"
                            class="p-1 md:p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 ml-auto md:ml-0"
                            title="Refresh Statistics"
                        >
                            <i class="fas fa-sync-alt text-sm md:text-base"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs - Updated with active indicators -->
        <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-4">
            <div class="border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
                <nav class="-mb-px flex flex-nowrap gap-1 md:gap-2 lg:gap-4 overflow-x-auto pb-1">
                    
                    {{-- Browse & Discovery Tab (Everyone) --}}
                    <button
                        wire:click="setActiveTab('browse')"
                        class="{{ $activeTab === 'browse' 
                            ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900' 
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' 
                        }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                        title="Browse Marketplace, Categories & Product Details"
                    >
                        <i class="fas fa-search mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                        <span class="inline">Browse</span>
                        @if($activeTab === 'browse')
                            <span class="ml-1 sm:ml-2 bg-purple-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full animate__animated animate__fadeInUp animate__faster">
                                Active
                            </span>
                        @endif
                    </button>

                    {{-- Shopping Cart & Purchases Tab (Students & Affiliates) --}}
                    @if($user && ($user->isStudent() || $user->isAffiliateAmbassador()))
                        <button
                            wire:click="setActiveTab('shopping')"
                            class="{{ $activeTab === 'shopping' 
                                ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' 
                            }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                            title="Shopping Cart, Checkout & Purchase History"
                        >
                            <i class="fas fa-shopping-cart mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                            <span class="inline">Shopping</span>
                            @if($activeTab === 'shopping')
                                <span class="ml-1 sm:ml-2 bg-purple-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full animate__animated animate__fadeInUp animate__faster">
                                    Active
                                </span>
                            @endif
                            @if($stats['my_orders'] > 0)
                                <span class="ml-1 sm:ml-2 bg-blue-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full">
                                    {{ $stats['my_orders'] }}
                                </span>
                            @endif
                        </button>
                    @endif

                    {{-- Vendor Management Tab (Vendors) --}}
                    @if($user && $user->canManageCourses())
                        <button
                            wire:click="setActiveTab('vendor')"
                            class="{{ $activeTab === 'vendor' 
                                ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' 
                            }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                            title="Create Listings, My Products & Drafts"
                        >
                            <i class="fas fa-store-alt mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                            <span class="inline">Vendor</span>
                            @if($activeTab === 'vendor')
                                <span class="ml-1 sm:ml-2 bg-purple-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full animate__animated animate__fadeInUp animate__faster">
                                    Active
                                </span>
                            @endif
                        </button>

                        {{-- Vendor Business Tab (Vendors) --}}
                        <button
                            wire:click="setActiveTab('business')"
                            class="{{ $activeTab === 'business' 
                                ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' 
                            }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                            title="Dashboard, Orders & Withdrawals"
                        >
                            <i class="fas fa-chart-line mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                            <span class="inline">Business</span>
                            @if($activeTab === 'business')
                                <span class="ml-1 sm:ml-2 bg-purple-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full animate__animated animate__fadeInUp animate__faster">
                                    Active
                                </span>
                            @endif
                        </button>
                    @endif
                    {{-- Content & Reviews Tab (Content Editors & Admins) --}}
                    @if($user && ($user->isContentEditor() || $user->isAcademyAdmin() || $user->isSuperAdmin()))
                        <button
                            wire:click="setActiveTab('content')"
                            class="{{ $activeTab === 'content' 
                                ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' 
                            }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                            title="Promotions, Discounts & Reviews Management"
                        >
                            <i class="fas fa-star mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                            <span class="inline">Content</span>
                            @if($activeTab === 'content')
                                <span class="ml-1 sm:ml-2 bg-purple-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full animate__animated animate__fadeInUp animate__faster">
                                    Active
                                </span>
                            @endif
                        </button>
                    @endif

                    {{-- Admin Operations Tab (Admins) --}}
                    @if($user && ($user->isSuperAdmin() || $user->isAcademyAdmin()))
                        <button
                            wire:click="setActiveTab('admin')"
                            class="{{ $activeTab === 'admin' 
                                ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' 
                            }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                            title="Vendor Applications, All Orders & Payments"
                        >
                            <i class="fas fa-user-shield mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                            <span class="inline">Admin</span>
                            @if($activeTab === 'admin')
                                <span class="ml-1 sm:ml-2 bg-purple-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full animate__animated animate__fadeInUp animate__faster">
                                    Active
                                </span>
                            @endif
                        </button>

                        {{-- Analytics & Settings Tab (Admins) --}}
                        <button
                            wire:click="setActiveTab('system')"
                            class="{{ $activeTab === 'system' 
                                ? 'border-purple-500 text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900' 
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' 
                            }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                            title="Analytics & System Settings"
                        >
                            <i class="fas fa-cog mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                            <span class="inline">System</span>
                            @if($activeTab === 'system')
                                <span class="ml-1 sm:ml-2 bg-purple-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full animate__animated animate__fadeInUp animate__faster">
                                    Active
                                </span>
                            @endif
                        </button>
                    @endif

                </nav>
            </div>
        </div>

        <!-- Content Area with Loading Animation -->
        <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-4 overflow-x-auto" wire:loading.class="opacity-50 pointer-events-none">
            <div class="animate__animated animate__fadeIn w-full min-w-0">
                @switch($activeTab)
                    @case('browse')
                        @livewire('marketplace.partial.marketplace-browse')
                        @break
                    @case('shopping')
                        @livewire('marketplace.partial.marketplace-shopping')
                        @break
                    @case('vendor')
                        @livewire('marketplace.partial.marketplace-vendor')
                        @break
                    @case('business')
                        @livewire('marketplace.partial.marketplace-business')
                        @break
                    @case('admin')
                        @livewire('marketplace.partial.marketplace-admin')
                        @break
                    @case('content')
                        @livewire('marketplace.partial.marketplace-content')
                        @break
                    @case('system')
                        @livewire('marketplace.partial.marketplace-system')
                        @break
                    @default
                        @livewire('marketplace.partial.marketplace-browse')
                @endswitch
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 z-50 flex items-center justify-center transition-colors duration-300">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 shadow-xl animate__animated animate__fadeIn mx-4 transition-colors duration-300">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-5 w-5 sm:h-6 sm:w-6 border-b-2 border-purple-600 dark:border-purple-400"></div>
                    <span class="text-gray-700 dark:text-gray-300 text-sm sm:text-base transition-colors duration-300">Loading marketplace...</span>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-green-500 dark:bg-green-600 text-white px-3 py-2 sm:px-4 sm:py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-md mx-2 sm:mx-0 transition-colors duration-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-sm"></i>
                    <span class="text-xs sm:text-sm">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-red-500 dark:bg-red-600 text-white px-3 py-2 sm:px-4 sm:py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-md mx-2 sm:mx-0 transition-colors duration-300">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2 text-sm"></i>
                    <span class="text-xs sm:text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('warning'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-yellow-500 dark:bg-yellow-600 text-white px-3 py-2 sm:px-4 sm:py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-md mx-2 sm:mx-0 transition-colors duration-300">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-sm"></i>
                    <span class="text-xs sm:text-sm">{{ session('warning') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>