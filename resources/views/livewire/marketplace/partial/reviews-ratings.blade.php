{{-- resources/views/livewire/marketplace/partial/reviews-ratings.blade.php --}}
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Reviews & Ratings</h2>
                <p class="text-gray-600">Moderate customer reviews and manage ratings</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex items-center space-x-6 text-sm">
                <div class="text-center">
                    <div class="text-lg font-semibold text-purple-600">{{ $stats['total_reviews'] }}</div>
                    <div class="text-gray-500">Total Reviews</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-green-600">{{ $stats['approved_reviews'] }}</div>
                    <div class="text-gray-500">Approved</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-yellow-600">{{ $stats['pending_reviews'] }}</div>
                    <div class="text-gray-500">Pending</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-blue-600">{{ number_format($stats['average_rating'], 1) }}/5</div>
                    <div class="text-gray-500">Avg Rating</div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Rating Distribution</h4>
            <div class="space-y-2">
                @for($i = 5; $i >= 1; $i--)
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 w-8">{{ $i }} ⭐</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-3">
                            @php
                                $total = array_sum($ratingDistribution);
                                $percentage = $total > 0 ? ($ratingDistribution[$i] / $total) * 100 : 0;
                            @endphp
                            <div class="bg-yellow-400 h-3 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-12">{{ $ratingDistribution[$i] }}</span>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Filters -->
        <div class="mt-6 flex flex-wrap gap-3">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Search reviews..." 
                       class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <select wire:model.live="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="all">All Reviews</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending Approval</option>
                <option value="featured">Featured</option>
            </select>

            <select wire:model.live="rating" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="">All Ratings</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>

            <select wire:model.live="sortBy" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="created_at">Newest First</option>
                <option value="rating">Highest Rating</option>
                <option value="helpful_count">Most Helpful</option>
            </select>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($reviews->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($reviews as $review)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- User Avatar -->
                                <div class="flex-shrink-0">
                                    @if($review->user->profile_picture)
                                        <img src="{{ asset('storage/' . $review->user->profile_picture) }}" 
                                             alt="{{ $review->user->name }}" 
                                             class="w-12 h-12 object-cover rounded-full">
                                    @else
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-purple-600 font-medium text-sm">
                                                {{ substr($review->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Review Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <!-- Rating and Status -->
                                            <div class="flex items-center space-x-3 mb-2">
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star text-{{ $i <= $review->rating ? 'yellow' : 'gray' }}-400 text-sm"></i>
                                                    @endfor
                                                    <span class="ml-2 text-sm font-medium text-gray-900">{{ $review->rating }}/5</span>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-1 bg-{{ $review->is_approved ? 'green' : 'yellow' }}-100 text-{{ $review->is_approved ? 'green' : 'yellow' }}-800 text-xs font-medium rounded-full">
                                                        {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                                    </span>
                                                    
                                                    @if($review->is_featured)
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                            <i class="fas fa-star mr-1"></i>Featured
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Review Details -->
                                            <div class="space-y-2">
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium">{{ $review->user->name }}</span> reviewed
                                                    <a href="{{ route('marketplace.item.public', $review->reviewable->slug) }}" 
                                                       class="text-purple-600 hover:text-purple-700 font-medium">
                                                        {{ $review->reviewable->title }}
                                                    </a>
                                                </div>
                                                
                                                @if($review->title)
                                                    <h4 class="font-medium text-gray-900">{{ $review->title }}</h4>
                                                @endif
                                                
                                                @if($review->comment)
                                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                                @endif
                                                
                                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                    <span>{{ $review->created_at->format('M d, Y') }}</span>
                                                    @if($review->helpful_count > 0)
                                                        <span>{{ $review->helpful_count }} found helpful</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Moderation Reason -->
                                            @if(!$review->is_approved && $review->moderation_reason)
                                                <div class="mt-3 p-3 bg-red-50 rounded-lg">
                                                    <p class="text-sm text-red-900">
                                                        <span class="font-medium">Rejection reason:</span> 
                                                        {{ $review->moderation_reason }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="mt-4 flex flex-wrap items-center gap-2">
                                        @if(!$review->is_approved)
                                            <button wire:click="approveReview({{ $review->id }})"
                                                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-1"></i>
                                                Approve
                                            </button>
                                            
                                            <button wire:click="openModerationModal({{ $review->id }}, 'reject')"
                                                    class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                                <i class="fas fa-times mr-1"></i>
                                                Reject
                                            </button>
                                        @endif

                                        <button wire:click="toggleFeatured({{ $review->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-{{ $review->is_featured ? 'yellow' : 'gray' }}-600 text-white text-sm rounded hover:bg-{{ $review->is_featured ? 'yellow' : 'gray' }}-700 transition-colors">
                                            <i class="fas fa-star mr-1"></i>
                                            {{ $review->is_featured ? 'Unfeature' : 'Feature' }}
                                        </button>
                                        
                                        <button wire:click="openModerationModal({{ $review->id }}, 'delete')"
                                                class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 text-sm rounded hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash mr-1"></i>
                                            Delete
                                        </button>

                                        <a href="mailto:{{ $review->user->email }}" 
                                           class="inline-flex items-center px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-envelope mr-1"></i>
                                            Contact User
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reviews->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i class="fas fa-star text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews found</h3>
                <p class="text-gray-500">
                    @if($search || $status !== 'all' || $rating)
                        Try adjusting your filters to see more reviews.
                    @else
                        Customer reviews will appear here once items start receiving feedback.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Moderation Modal -->
    @if($showModerationModal && $selectedReview)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ ucfirst($moderationAction) }} Review
                    </h3>
                    <button wire:click="closeModerationModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Review Preview -->
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-2 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-{{ $i <= $selectedReview->rating ? 'yellow' : 'gray' }}-400 text-sm"></i>
                        @endfor
                        <span class="text-sm font-medium">{{ $selectedReview->user->name }}</span>
                    </div>
                    @if($selectedReview->title)
                        <h5 class="font-medium text-sm">{{ $selectedReview->title }}</h5>
                    @endif
                    @if($selectedReview->comment)
                        <p class="text-sm text-gray-700">{{ Str::limit($selectedReview->comment, 150) }}</p>
                    @endif
                </div>
                
                @if($moderationAction === 'reject' || $moderationAction === 'delete')
                    <div class="mb-4">
                        <label for="moderationReason" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $moderationAction === 'reject' ? 'Rejection' : 'Deletion' }} Reason
                        </label>
                        <textarea wire:model="moderationReason" 
                                  id="moderationReason"
                                  rows="3"
                                  placeholder="Please explain why this review is being {{ $moderationAction === 'reject' ? 'rejected' : 'deleted' }}..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeModerationModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    
                    @if($moderationAction === 'reject')
                        <button wire:click="rejectReview"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Reject Review
                        </button>
                    @elseif($moderationAction === 'delete')
                        <button wire:click="deleteReview"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Delete Review
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>