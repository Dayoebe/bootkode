{{-- resources/views/livewire/marketplace/marketplace-center.blade.php --}}
<div class="w-full min-h-screen bg-gray-50 overflow-x-hidden">
    <div class="w-full mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center py-4 md:py-6 space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-600 p-2 md:p-3 rounded-lg animate__animated animate__fadeInLeft">
                            <i class="fas fa-store text-white text-lg md:text-xl"></i>
                        </div>
                        <div class="animate__animated animate__fadeInUp">
                            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Marketplace</h1>
                            <p class="text-gray-600 mt-1 text-xs sm:text-sm md:text-base">Browse, buy and sell courses, resources and services</p>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="flex flex-wrap items-center gap-3 md:gap-6 animate__animated animate__fadeInRight w-full md:w-auto mt-2 md:mt-0">
                        <div class="text-center min-w-[60px] sm:min-w-[70px]">
                            <div class="text-lg sm:text-xl md:text-2xl font-bold text-purple-600">{{ $stats['total_items'] }}</div>
                            <div class="text-xs text-gray-500">Items</div>
                        </div>
                        @if($user)
                            <div class="text-center min-w-[60px] sm:min-w-[70px]">
                                <div class="text-lg sm:text-xl md:text-2xl font-bold text-blue-600">{{ $stats['my_orders'] }}</div>
                                <div class="text-xs text-gray-500">Orders</div>
                            </div>
                            @if($user->canManageCourses())
                                <div class="text-center min-w-[60px] sm:min-w-[70px]">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold text-green-600">{{ $stats['my_listings'] }}</div>
                                    <div class="text-xs text-gray-500">Listings</div>
                                </div>
                                <div class="text-center min-w-[60px] sm:min-w-[70px]">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold text-orange-600">₦{{ number_format($stats['total_earnings'], 0) }}</div>
                                    <div class="text-xs text-gray-500">Earnings</div>
                                </div>
                            @endif
                        @endif
                        <button 
                            wire:click="refreshStats"
                            class="p-1 md:p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100 transition-all duration-200 ml-auto md:ml-0"
                            title="Refresh Statistics"
                        >
                            <i class="fas fa-sync-alt text-sm md:text-base"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex flex-wrap gap-1 md:gap-2 lg:gap-4 overflow-visible pb-1">
                    {{-- General Tabs (Everyone) --}}
                    @foreach([
                            'browse' => ['label' => 'Browse', 'icon' => 'fas fa-search', 'full_label' => 'Browse Marketplace', 'roles' => []],
                            'categories' => ['label' => 'Categories', 'icon' => 'fas fa-tags', 'full_label' => 'Categories', 'roles' => []],
                            'product-details' => ['label' => 'Details', 'icon' => 'fas fa-box-open', 'full_label' => 'Product Details', 'roles' => []],
                            'checkout' => ['label' => 'Cart', 'icon' => 'fas fa-credit-card', 'full_label' => 'Cart & Checkout', 'roles' => []],
                        ] as $tab => $tabData)
                        
                        @if(empty($tabData['roles']) || $user?->hasRole($tabData['roles']))
                            <button
                                wire:click="setActiveTab('{{ $tab }}')"
                                class="{{ $activeTab === $tab 
                                    ? 'border-purple-500 text-purple-600 bg-purple-50' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' 
                                }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                                title="{{ $tabData['full_label'] }}"
                            >
                                <i class="{{ $tabData['icon'] }} mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                <span class="hidden xs:inline">{{ $tabData['label'] }}</span>
                                
                                @if($activeTab === $tab)
                                    <span class="ml-1 sm:ml-2 bg-purple-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full animate__animated animate__fadeInUp animate__faster hidden sm:inline">
                                        Active
                                    </span>
                                @endif
                            </button>
                        @endif
                    @endforeach

                    {{-- Student Tabs --}}
                    @if($user && ($user->isStudent() || $user->isAffiliateAmbassador()))
                        <button
                            wire:click="setActiveTab('purchases')"
                            class="{{ $activeTab === 'purchases' 
                                ? 'border-purple-500 text-purple-600 bg-purple-50' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' 
                            }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                            title="My Purchases"
                        >
                            <i class="fas fa-shopping-cart mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                            <span class="hidden xs:inline">Purchases</span>
                            
                            @if($stats['my_orders'] > 0)
                                <span class="ml-1 sm:ml-2 bg-blue-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full">
                                    {{ $stats['my_orders'] }}
                                </span>
                            @endif
                        </button>
                    @endif

                    {{-- Vendor Tabs --}}
                    @if($user && $user->canManageCourses())
                        @foreach([
                                'create-listing' => ['label' => 'Create', 'icon' => 'fas fa-upload', 'full_label' => 'Sell a Product'],
                                'my-listings' => ['label' => 'Listings', 'icon' => 'fas fa-list-alt', 'full_label' => 'My Listings'],
                                'drafts' => ['label' => 'Drafts', 'icon' => 'fas fa-save', 'full_label' => 'Saved Drafts'],
                                'vendor-dashboard' => ['label' => 'Dashboard', 'icon' => 'fas fa-store-alt', 'full_label' => 'Vendor Dashboard'],
                                'vendor-orders' => ['label' => 'Orders', 'icon' => 'fas fa-box', 'full_label' => 'Customer Orders'],
                                'withdrawals' => ['label' => 'Withdrawals', 'icon' => 'fas fa-money-bill-wave', 'full_label' => 'Withdrawals'],
                            ] as $tab => $tabData)
                            <button
                                wire:click="setActiveTab('{{ $tab }}')"
                                class="{{ $activeTab === $tab 
                                    ? 'border-purple-500 text-purple-600 bg-purple-50' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' 
                                }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                                title="{{ $tabData['full_label'] }}"
                            >
                                <i class="{{ $tabData['icon'] }} mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                <span class="hidden xs:inline">{{ $tabData['label'] }}</span>
                            </button>
                        @endforeach
                    @endif

                    {{-- Admin Tabs --}}
                    @if($user && ($user->isSuperAdmin() || $user->isAcademyAdmin()))
                        @foreach([
                                'vendor-applications' => ['label' => 'Applications', 'icon' => 'fas fa-user-plus', 'full_label' => 'Vendor Applications'],
                                'all-orders' => ['label' => 'All Orders', 'icon' => 'fas fa-boxes', 'full_label' => 'All Orders'],
                                'payments' => ['label' => 'Payments', 'icon' => 'fas fa-wallet', 'full_label' => 'Payments & Payouts'],
                                'analytics' => ['label' => 'Analytics', 'icon' => 'fas fa-chart-line', 'full_label' => 'Marketplace Analytics'],
                                'settings' => ['label' => 'Settings', 'icon' => 'fas fa-cog', 'full_label' => 'Marketplace Settings'],
                            ] as $tab => $tabData)
                            <button
                                wire:click="setActiveTab('{{ $tab }}')"
                                class="{{ $activeTab === $tab 
                                    ? 'border-purple-500 text-purple-600 bg-purple-50' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' 
                                }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                                title="{{ $tabData['full_label'] }}"
                            >
                                <i class="{{ $tabData['icon'] }} mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                <span class="hidden xs:inline">{{ $tabData['label'] }}</span>
                            </button>
                        @endforeach
                    @endif

                    {{-- Content Editor & Admin Tabs --}}
                    @if($user && ($user->isContentEditor() || $user->isAcademyAdmin() || $user->isSuperAdmin()))
                        @foreach([
                                'promotions' => ['label' => 'Promotions', 'icon' => 'fas fa-bullhorn', 'full_label' => 'Promotions & Discounts'],
                                'reviews' => ['label' => 'Reviews', 'icon' => 'fas fa-star', 'full_label' => 'Reviews & Ratings'],
                            ] as $tab => $tabData)
                            <button
                                wire:click="setActiveTab('{{ $tab }}')"
                                class="{{ $activeTab === $tab 
                                    ? 'border-purple-500 text-purple-600 bg-purple-50' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' 
                                }} whitespace-nowrap py-2 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm flex items-center rounded-t-lg transition-all duration-200 flex-shrink-0"
                                title="{{ $tabData['full_label'] }}"
                            >
                                <i class="{{ $tabData['icon'] }} mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                <span class="hidden xs:inline">{{ $tabData['label'] }}</span>
                            </button>
                        @endforeach
                    @endif
                </nav>
            </div>
        </div>

        <!-- Content Area with Loading Animation -->
        <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-4 overflow-x-auto" wire:loading.class="opacity-50 pointer-events-none">
            <div class="animate__animated animate__fadeIn w-full min-w-0">
                @switch($activeTab)
                    @case('browse')
                        @livewire('marketplace.partial.browse-marketplace')
                        @break
                    @case('categories')
                        @livewire('marketplace.partial.marketplace-categories')
                        @break
                    @case('product-details')
                        @livewire('marketplace.partial.product-details')
                        @break
                    @case('checkout')
                        @livewire('marketplace.partial.cart-checkout')
                        @break
                    @case('purchases')
                        @livewire('marketplace.partial.my-purchases')
                        @break
                    @case('create-listing')
                        @livewire('marketplace.partial.create-listing')
                        @break
                    @case('my-listings')
                        @livewire('marketplace.partial.my-listings')
                        @break
                    @case('drafts')
                        @livewire('marketplace.partial.saved-drafts')
                        @break
                    @case('vendor-dashboard')
                        @livewire('marketplace.partial.vendor-dashboard')
                        @break
                    @case('vendor-orders')
                        @livewire('marketplace.partial.vendor-orders')
                        @break
                    @case('withdrawals')
                        @livewire('marketplace.partial.vendor-withdrawals')
                        @break
                    @case('vendor-applications')
                        @livewire('marketplace.partial.vendor-applications')
                        @break
                    @case('all-orders')
                        @livewire('marketplace.partial.all-orders')
                        @break
                    @case('payments')
                        @livewire('marketplace.partial.payments-payouts')
                        @break
                    @case('promotions')
                        @livewire('marketplace.partial.promotions-discounts')
                        @break
                    @case('reviews')
                        @livewire('marketplace.partial.reviews-ratings')
                        @break
                    @case('analytics')
                        @livewire('marketplace.partial.marketplace-analytics')
                        @break
                    @case('settings')
                        @livewire('marketplace.partial.marketplace-settings')
                        @break
                    @default
                        @livewire('marketplace.partial.browse-marketplace')
                @endswitch
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-4 sm:p-6 shadow-xl animate__animated animate__fadeIn mx-4">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-5 w-5 sm:h-6 sm:w-6 border-b-2 border-purple-600"></div>
                    <span class="text-gray-700 text-sm sm:text-base">Loading marketplace...</span>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-green-500 text-white px-3 py-2 sm:px-4 sm:py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-md mx-2 sm:mx-0">
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
                 class="fixed top-4 right-4 bg-red-500 text-white px-3 py-2 sm:px-4 sm:py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-md mx-2 sm:mx-0">
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
                 class="fixed top-4 right-4 bg-yellow-500 text-white px-3 py-2 sm:px-4 sm:py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-md mx-2 sm:mx-0">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-sm"></i>
                    <span class="text-xs sm:text-sm">{{ session('warning') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>