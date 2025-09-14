{{-- resources/views/livewire/marketplace/partial/content/reviews-tab.blade.php --}}

<!-- Modern Stats Overview -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600">Total Reviews</p>
                <p class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($reviewStats['total_reviews'] ?? 0) }}</p>
            </div>
            <div class="p-3 bg-slate-100 rounded-xl">
                <i class="fas fa-comments text-slate-600 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center mt-4 text-sm">
            <span class="text-slate-500">All marketplace feedback</span>
        </div>
    </div>

    <div class="bg-white border border-green-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-green-600">Approved</p>
                <p class="text-3xl font-bold text-green-700 mt-2">{{ number_format($reviewStats['approved_reviews'] ?? 0) }}</p>
            </div>
            <div class="p-3 bg-green-100 rounded-xl">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center mt-4 text-sm">
            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
            <span class="text-green-600">Live on platform</span>
        </div>
    </div>

    <div class="bg-white border border-amber-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-amber-600">Pending Review</p>
                <p class="text-3xl font-bold text-amber-700 mt-2">{{ number_format($reviewStats['pending_reviews'] ?? 0) }}</p>
            </div>
            <div class="p-3 bg-amber-100 rounded-xl">
                <i class="fas fa-clock text-amber-600 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center mt-4 text-sm">
            <span class="text-amber-600">Awaiting moderation</span>
        </div>
    </div>

    <div class="bg-white border border-blue-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-blue-600">Average Rating</p>
                <div class="flex items-center mt-2">
                    <p class="text-3xl font-bold text-blue-700">{{ number_format($reviewStats['average_rating'] ?? 0, 1) }}</p>
                    <div class="flex ml-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="p-3 bg-blue-100 rounded-xl">
                <i class="fas fa-star text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center mt-4 text-sm">
            <span class="text-blue-600">Platform quality</span>
        </div>
    </div>
</div>

<!-- Rating Distribution Chart -->
<div class="bg-white border border-slate-200 rounded-xl p-6 mb-8 shadow-sm">
    <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center">
        <i class="fas fa-chart-bar mr-3 text-slate-600"></i>
        Rating Distribution Analysis
    </h3>
    <div class="space-y-4">
        @for($i = 5; $i >= 1; $i--)
            <div class="flex items-center space-x-4">
                <div class="flex items-center w-16 text-sm font-medium text-slate-700">
                    {{ $i }} <i class="fas fa-star text-yellow-400 ml-1"></i>
                </div>
                <div class="flex-1 bg-slate-100 rounded-full h-3 relative overflow-hidden">
                    @php
                        $total = array_sum($ratingDistribution ?? []);
                        $count = $ratingDistribution[$i] ?? 0;
                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                        $colorClasses = [
                            5 => 'from-emerald-500 to-green-500',
                            4 => 'from-blue-500 to-blue-600', 
                            3 => 'from-yellow-500 to-amber-500',
                            2 => 'from-orange-500 to-red-500',
                            1 => 'from-red-500 to-red-600'
                        ];
                    @endphp
                    <div class="bg-gradient-to-r {{ $colorClasses[$i] }} h-3 rounded-full transition-all duration-500 ease-out" 
                         style="width: {{ $percentage }}%"></div>
                </div>
                <div class="text-sm font-medium text-slate-700 w-12 text-right">{{ $count }}</div>
                <div class="text-xs text-slate-500 w-12 text-right">{{ $percentage > 0 ? round($percentage, 1) : 0 }}%</div>
            </div>
        @endfor
    </div>
</div>

<!-- Modern Filters -->
<div class="bg-white border border-slate-200 rounded-xl p-6 mb-6 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-400"></i>
            </div>
            <input wire:model.live.debounce.300ms="reviewSearch" 
                   type="text" 
                   placeholder="Search reviews, customers, or products..." 
                   class="block w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        
        <div class="flex flex-wrap gap-3">
            <select wire:model.live="reviewStatus" 
                    class="px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium min-w-[140px]">
                <option value="all">All Status</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="featured">Featured</option>
            </select>

            <select wire:model.live="rating" 
                    class="px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium min-w-[120px]">
                <option value="">All Ratings</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>

            <select wire:model.live="sortBy" 
                    class="px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium min-w-[140px]">
                <option value="created_at">Newest First</option>
                <option value="rating">Highest Rating</option>
                <option value="helpful_count">Most Helpful</option>
            </select>
        </div>
    </div>
</div>

<!-- Reviews List -->
@if(isset($reviews) && $reviews->count() > 0)
    <div class="space-y-6">
        <!-- Select All Control -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" 
                           wire:model="selectAll"
                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-2">
                    <span class="ml-3 text-sm font-medium text-slate-700">
                        Select all reviews on this page
                    </span>
                </label>
                @if(!empty($selectedReviews))
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-blue-600 font-medium">{{ count($selectedReviews) }} selected</span>
                        <div class="h-4 w-px bg-slate-300"></div>
                        <select wire:model="bulkAction" 
                                class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm">
                            <option value="">Bulk Actions</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                            <option value="feature">Feature</option>
                            <option value="unfeature">Unfeature</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button wire:click="bulkActionReviews" 
                                @if(!$bulkAction) disabled @endif
                                class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            Apply
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @foreach($reviews as $review)
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                <div class="flex items-start space-x-4">
                    <!-- Selection Checkbox -->
                    <div class="pt-1">
                        <input type="checkbox" 
                               wire:model="selectedReviews" 
                               value="{{ $review->id }}"
                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </div>

                    <!-- User Avatar -->
                    <div class="flex-shrink-0">
                        @if($review->user && $review->user->profile_picture)
                            <img src="{{ asset('storage/' . $review->user->profile_picture) }}" 
                                 alt="{{ $review->user->name }}" 
                                 class="w-12 h-12 object-cover rounded-full border-2 border-slate-200">
                        @else
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center border-2 border-slate-200">
                                <span class="text-slate-600 font-semibold text-sm">
                                    {{ $review->user ? strtoupper(substr($review->user->name, 0, 2)) : 'UN' }}
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Review Content -->
                    <div class="flex-1 min-w-0">
                        <!-- Header with Rating and Status -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-300' }}"></i>
                                    @endfor
                                    <span class="ml-2 text-sm font-semibold text-slate-900">{{ $review->rating }}/5</span>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 bg-{{ $review->is_approved ? 'green' : 'amber' }}-100 text-{{ $review->is_approved ? 'green' : 'amber' }}-800 text-xs font-semibold rounded-full">
                                        {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                    
                                    @if($review->is_featured)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-xs text-slate-500">
                                {{ $review->created_at->format('M d, Y \a\t g:i A') }}
                            </div>
                        </div>

                        <!-- Review Details -->
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-slate-600">
                                <span class="font-semibold text-slate-900">{{ $review->user->name ?? 'Anonymous User' }}</span>
                                <span class="mx-2">reviewed</span>
                                <a href="{{ route('marketplace.item.public', $review->reviewable->slug ?? '#') }}" 
                                   class="text-blue-600 hover:text-blue-700 font-semibold underline">
                                    {{ $review->reviewable->title ?? 'Unknown Item' }}
                                </a>
                            </div>
                            
                            @if($review->title)
                                <h4 class="font-semibold text-slate-900 text-lg">{{ $review->title }}</h4>
                            @endif
                            
                            @if($review->comment)
                                <p class="text-slate-700 leading-relaxed">{{ $review->comment }}</p>
                            @endif
                            
                            <div class="flex items-center space-x-6 text-sm text-slate-500">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    {{ $review->created_at->diffForHumans() }}
                                </span>
                                @if(($review->helpful_count ?? 0) > 0)
                                    <span class="flex items-center">
                                        <i class="fas fa-thumbs-up mr-2"></i>
                                        {{ $review->helpful_count }} found helpful
                                    </span>
                                @endif
                                <span class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    {{ $review->user->email ?? 'No email' }}
                                </span>
                            </div>
                        </div>

                        <!-- Moderation Reason -->
                        @if(!$review->is_approved && $review->moderation_reason)
                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-2 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-red-900">Moderation Note</p>
                                        <p class="text-sm text-red-800 mt-1">{{ $review->moderation_reason }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-2 ml-4">
                        @if(!$review->is_approved)
                            <button wire:click="approveReview({{ $review->id }})"
                                    class="px-4 py-2 bg-green-600 text-white text-sm rounded-xl hover:bg-green-700 transition-colors font-medium">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                            
                            <button wire:click="openModerationModal({{ $review->id }}, 'reject')"
                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-xl hover:bg-red-700 transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        @endif

                        <button wire:click="toggleFeatured({{ $review->id }})"
                                class="px-4 py-2 bg-{{ $review->is_featured ? 'amber' : 'slate' }}-600 text-white text-sm rounded-xl hover:bg-{{ $review->is_featured ? 'amber' : 'slate' }}-700 transition-colors font-medium">
                            <i class="fas fa-star mr-2"></i>{{ $review->is_featured ? 'Unfeature' : 'Feature' }}
                        </button>
                        
                        <button wire:click="openModerationModal({{ $review->id }}, 'delete')"
                                class="px-4 py-2 border border-red-300 text-red-700 text-sm rounded-xl hover:bg-red-50 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>

                        @if($review->user && $review->user->email)
                            <a href="mailto:{{ $review->user->email }}" 
                               class="px-4 py-2 border border-slate-300 text-slate-700 text-sm rounded-xl hover:bg-slate-50 transition-colors font-medium text-center">
                                <i class="fas fa-envelope mr-2"></i>Contact
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Modern Pagination -->
    <div class="mt-8">
        {{ $reviews->links() }}
    </div>
@else
    <!-- Enhanced Empty State -->
    <div class="text-center py-16 bg-white border border-slate-200 rounded-2xl shadow-sm">
        <div class="mx-auto w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-star text-slate-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-slate-900 mb-3">
            @if($reviewSearch || $reviewStatus !== 'all' || $rating)
                No reviews match your criteria
            @else
                No reviews yet
            @endif
        </h3>
        <p class="text-slate-500 max-w-md mx-auto mb-6">
            @if($reviewSearch || $reviewStatus !== 'all' || $rating)
                Try adjusting your search filters to find the reviews you're looking for.
            @else
                Customer reviews will appear here once marketplace items start receiving feedback from buyers.
            @endif
        </p>
        @if($reviewSearch || $reviewStatus !== 'all' || $rating)
            <button wire:click="$set('reviewSearch', ''); $set('reviewStatus', 'all'); $set('rating', '')" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-refresh mr-2"></i>Clear Filters
            </button>
        @endif
    </div>
@endif