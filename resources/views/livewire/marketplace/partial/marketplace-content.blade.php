{{-- resources/views/livewire/marketplace/partial/marketplace-content.blade.php --}}
<div class="space-y-8">
    <!-- Modern Header -->
    <div class="bg-surface border border-line rounded-lg shadow-sm overflow-hidden">
        <div class="relative bg-surface-muted px-8 py-8">
            <div class="absolute inset-0 bg-surface-muted  "></div>
            <div class="relative">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-6 lg:space-y-0">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4 mb-3">
                            <div class="p-3 bg-brand/10 rounded-lg">
                                <i aria-hidden="true" class="fas fa-chart-line text-brand text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-content">Content & Reviews Management</h2>
                                <p class="text-content-muted mt-1">Monitor and manage marketplace promotions and customer reviews</p>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Stats Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                        @if($activeTab === 'promotions')
                            <div class="bg-surface rounded-lg p-4 border border-line shadow-sm">
                                <div class="text-sm text-content-muted">Active Codes</div>
                                <div class="text-2xl font-bold text-content">{{ $promotionStats['active_codes'] ?? 0 }}</div>
                                <div class="text-xs text-green-600 mt-1 flex items-center">
                                    <i aria-hidden="true" class="fas fa-arrow-up mr-1"></i>
                                    Live now
                                </div>
                            </div>
                            <div class="bg-surface rounded-lg p-4 border border-line shadow-sm">
                                <div class="text-sm text-content-muted">Total Uses</div>
                                <div class="text-2xl font-bold text-content">{{ number_format($promotionStats['total_uses'] ?? 0) }}</div>
                                <div class="text-xs text-brand mt-1">All time</div>
                            </div>
                            <div class="bg-surface rounded-lg p-4 border border-line shadow-sm col-span-2 lg:col-span-1">
                                <div class="text-sm text-content-muted">Total Savings</div>
                                <div class="text-2xl font-bold text-content">₦{{ number_format($promotionStats['total_savings'] ?? 0, 0) }}</div>
                                <div class="text-xs text-emerald-600 mt-1">Customer benefits</div>
                            </div>
                        @else
                            <div class="bg-surface rounded-lg p-4 border border-line shadow-sm">
                                <div class="text-sm text-content-muted">Total Reviews</div>
                                <div class="text-2xl font-bold text-content">{{ $reviewStats['total_reviews'] ?? 0 }}</div>
                                <div class="text-xs text-content-muted mt-1">All feedback</div>
                            </div>
                            <div class="bg-surface rounded-lg p-4 border border-line shadow-sm">
                                <div class="text-sm text-content-muted">Pending</div>
                                <div class="text-2xl font-bold text-amber-600">{{ $reviewStats['pending_reviews'] ?? 0 }}</div>
                                <div class="text-xs text-amber-600 mt-1">Needs review</div>
                            </div>
                            <div class="bg-surface rounded-lg p-4 border border-line shadow-sm">
                                <div class="text-sm text-content-muted">Avg Rating</div>
                                <div class="text-2xl font-bold text-content flex items-center">
                                    {{ number_format($reviewStats['average_rating'] ?? 0, 1) }}/5
                                    <i aria-hidden="true" class="fas fa-star text-yellow-400 ml-1 text-sm"></i>
                                </div>
                                <div class="text-xs text-content-muted mt-1">Overall quality</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex flex-wrap gap-3">
                    @if($activeTab === 'promotions')
                        <button wire:click="openCreateModal"
                                class="inline-flex items-center px-4 py-2.5 bg-action text-action-foreground rounded-lg hover:bg-action transition-all duration-200 text-sm font-medium shadow-sm ">
                            <i aria-hidden="true" class="fas fa-plus mr-2"></i>
                            Create Discount Code
                        </button>
                    @else
                        @if(!empty($selectedReviews))
                            <div class="flex items-center gap-3">
                                <select aria-label="Bulk action" wire:model="bulkAction"
                                        class="px-4 py-2.5 bg-surface border border-line text-content rounded-lg text-sm font-medium focus:ring-2 focus:ring-brand/30 focus:border-brand">
                                    <option value="">Select Action</option>
                                    <option value="approve">Approve Selected</option>
                                    <option value="reject">Reject Selected</option>
                                    <option value="feature">Feature Selected</option>
                                    <option value="delete">Delete Selected</option>
                                </select>
                                <button wire:click="bulkActionReviews"
                                        @if(!$bulkAction) disabled @endif
                                        class="px-4 py-2.5 bg-slate-700 text-white rounded-lg hover:bg-slate-800 transition-all duration-200 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                    Apply to {{ count($selectedReviews) }} items
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Modern Navigation Tabs -->
        <div class="border-b border-line bg-surface px-8">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button wire:click="setActiveTab('promotions')"
                        class="{{ $activeTab === 'promotions' ? 'border-blue-500 text-brand bg-blue-50' : 'border-transparent text-content-muted hover:text-content hover:border-slate-300' }}
                        whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center transition-all duration-200 rounded-t-xl">
                    <i aria-hidden="true" class="fas fa-tags mr-3"></i>
                    Promotions & Discounts
                </button>

                <button wire:click="setActiveTab('reviews')"
                        class="{{ $activeTab === 'reviews' ? 'border-blue-500 text-brand bg-blue-50' : 'border-transparent text-content-muted hover:text-content hover:border-slate-300' }}
                        whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center transition-all duration-200 rounded-t-xl relative">
                    <i aria-hidden="true" class="fas fa-star mr-3"></i>
                    Reviews & Ratings
                    @if(($reviewStats['pending_reviews'] ?? 0) > 0)
                        <span class="ml-2 bg-amber-500 text-white text-xs px-2 py-1 rounded-full  font-semibold">
                            {{ $reviewStats['pending_reviews'] }}
                        </span>
                    @endif
                </button>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="px-8 py-8" wire:loading.class="opacity-50 pointer-events-none">
            <div class="transition-all duration-300 ease-in-out">
                @if($activeTab === 'promotions')
                    @include('livewire.marketplace.partial.content.promotions-tab')
                @elseif($activeTab === 'reviews')
                    @include('livewire.marketplace.partial.content.reviews-tab')
                @endif
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('livewire.marketplace.partial.content.content-modals')

    <!-- Modern Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-slate-900/20  z-50 flex items-center justify-center">
        <div class="bg-surface rounded-lg p-6 shadow-sm border border-line mx-4">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-6 w-6 border-2 border-brand border-t-transparent"></div>
                <span class="text-content font-medium">Processing your request...</span>
            </div>
        </div>
    </div>
</div>
