<div class="space-y-4" 
     x-data="{ isCollapsed: @entangle('isCollapsed') }"
     wire:poll.10s="{{ !$showReviewForm ? '' : 'keep-alive' }}">
    
    <!-- Header with Collapse Toggle -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Course Reviews</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $totalReviews }} {{ Str::plural('review', $totalReviews) }} • 
                        {{ number_format($averageRating, 1) }} <i class="fas fa-star text-yellow-400 text-xs"></i>
                    </p>
                </div>
            </div>
            <button @click="$wire.toggleCollapse()"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <i class="fas fa-chevron-down text-lg transform transition-transform" 
                   :class="{ 'rotate-180': !isCollapsed }"></i>
            </button>
        </div>
    </div>

    <!-- Collapsible Content -->
    <div x-show="!isCollapsed" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="space-y-6">

        <!-- User Review Status Card -->
        @auth
            @if($this->canReview())
                <div class="bg-blue-50 dark:bg-gray-800 rounded-xl p-6 border border-blue-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-star text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-lg">
                                    @if($editingReview)
                                        You've already reviewed this course
                                    @else
                                        Share your experience
                                    @endif
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 mt-1">
                                    @if($editingReview)
                                        You can update your review or see it below
                                    @else
                                        Help other students by sharing your thoughts
                                    @endif
                                </p>
                            </div>
                        </div>
                        <button wire:click="toggleReviewForm" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-{{ $showReviewForm ? 'times' : 'star' }}"></i>
                            {{ $editingReview ? 'Edit Review' : ($showReviewForm ? 'Cancel' : 'Write Review') }}
                        </button>
                    </div>

                    <!-- Quick Stats -->
                    @if($editingReview)
                        <div class="mt-4 flex items-center gap-6 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Your rating:</span>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-yellow-400 {{ $editingReview->rating >= $i ? '' : 'opacity-30' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-gray-500 dark:text-gray-400">•</span>
                            <span class="text-gray-600 dark:text-gray-300">{{ $editingReview->created_at->diffForHumans() }}</span>
                            <span class="text-gray-500 dark:text-gray-400">•</span>
                            <button wire:click="deleteReview({{ $editingReview->id }})" 
                                    onclick="return confirm('Are you sure you want to delete your review?')"
                                    class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="bg-blue-50 dark:bg-gray-800 rounded-xl p-6 text-center border border-blue-200 dark:border-gray-600">
                <i class="fas fa-star text-blue-500 text-3xl mb-3"></i>
                <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">Want to share your experience?</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Login to review this course and help other students</p>
                <a href="{{ route('login') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 inline-flex items-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to Review
                </a>
            </div>
        @endauth

        <!-- Review Form -->
        @if($showReviewForm)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    {{ $editingReview ? 'Edit Your Review' : 'Write a Review' }}
                </h3>
                
                <form wire:submit.prevent="submitReview">
                    <!-- Star Rating -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                            Your Rating *
                        </label>
                        <div class="flex items-center gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        wire:click="$set('rating', {{ $i }})"
                                        class="text-3xl transition-all duration-200 transform hover:scale-110">
                                    <i class="fas fa-star {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                </button>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ $rating }} out of 5 stars
                        </p>
                    </div>

                    <!-- Comment -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Your Review *
                        </label>
                        <textarea wire:model="comment"
                                  rows="5"
                                  placeholder="Share your thoughts about this course. What did you like? What could be improved?"
                                  class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300"></textarea>
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span>Minimum 10 characters</span>
                            <span>{{ strlen($comment) }}/1000</span>
                        </div>
                        @error('comment') 
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button type="button"
                                wire:click="toggleReviewForm"
                                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold rounded-lg transition-all duration-300">
                            Cancel
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-all duration-300 shadow-sm disabled:opacity-50">
                            <span wire:loading.remove wire:target="submitReview">
                                {{ $editingReview ? 'Update Review' : 'Submit Review' }}
                            </span>
                            <span wire:loading wire:target="submitReview">
                                <i class="fas fa-spinner fa-spin"></i> Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Course Rating Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Overall Rating -->
                <div class="text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-3 mb-2">
                        <span class="text-5xl font-black text-gray-900 dark:text-white">
                            {{ number_format($averageRating, 1) }}
                        </span>
                        <div>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400 {{ $averageRating >= $i ? '' : ($averageRating >= $i - 0.5 ? 'opacity-50' : 'opacity-20') }}"></i>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}
                            </p>
                            @if($verifiedCount > 0)
                                <p class="text-xs text-green-600 dark:text-green-400 font-semibold mt-1">
                                    {{ $verifiedCount }} verified
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rating Distribution -->
                <div class="lg:col-span-2 space-y-2">
                    @php
                        $distribution = array_replace([5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0], $ratingDistribution);
                        krsort($distribution);
                    @endphp
                    
                    @foreach($distribution as $star => $count)
                        @php
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-12">{{ $star }} star</span>
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400 w-16 text-right">{{ $count }} ({{ number_format($percentage, 0) }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Reviews List Header -->
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Student Reviews</h3>
            
            <!-- Simple Filters -->
            <div class="flex gap-3">
                <select wire:model.live="sortBy"
                        class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white text-sm">
                    <option value="recent">Most Recent</option>
                    <option value="rating_high">Highest Rating</option>
                    <option value="rating_low">Lowest Rating</option>
                </select>

                <select wire:model.live="filterRating"
                        class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-white text-sm">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }} Stars</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="space-y-4">
            @forelse($reviews as $review)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-sm">
                    <!-- Review Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $review->user->name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $review->rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    @if($review->isVerified())
                                        <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-semibold rounded-full flex items-center gap-1"
                                              title="Completed the course">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Edit button for user's own review -->
                        @if(Auth::check() && $review->user_id === Auth::id())
                            <button wire:click="toggleReviewForm" 
                                    class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                    title="Edit your review">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Review Content -->
                    <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $review->review_text }}</p>

                    <!-- Instructor Reply -->
                    @if($review->instructor_reply)
                        <div class="mt-4 pl-4 border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-r-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-reply text-blue-600 dark:text-blue-400"></i>
                                <span class="font-semibold text-gray-900 dark:text-white">Instructor Response</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $review->replied_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300">{{ $review->instructor_reply }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <i class="fas fa-comments text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $searchQuery || $filterRating ? 'No reviews match your filters' : 'No reviews yet. Be the first to review this course!' }}
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>