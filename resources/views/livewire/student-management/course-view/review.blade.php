<!-- REVIEW COMPONENT -->
<div class="space-y-4" 
     x-data="{ isCollapsed: @entangle('isCollapsed') }"
     wire:poll.10s="{{ !$showReviewForm ? '' : 'keep-alive' }}">
    
    <!-- Header with Collapse Toggle -->
    <div class="rounded-xl border border-themed-secondary bg-themed-secondary/70 p-4 transition-colors duration-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-themed-secondary bg-themed-tertiary">
                    <i class="fas fa-star text-sm accent-themed-primary"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-themed-primary">Learner Reviews</h2>
                    <p class="text-xs text-themed-secondary">
                        {{ $totalReviews }} {{ Str::plural('review', $totalReviews) }} • 
                        {{ number_format($averageRating, 1) }} <i class="fas fa-star text-yellow-400 text-xs"></i>
                    </p>
                </div>
            </div>
            <button @click="$wire.toggleCollapse()"
                    class="rounded-lg border border-themed-secondary bg-themed-tertiary px-3 py-2 text-themed-tertiary transition-colors duration-300 hover:text-themed-primary">
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
                <div class="bg-accent-themed-primary/20 rounded-xl p-6 border border-accent-themed-primary/30 transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-accent-themed-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-star text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-themed-primary text-lg">
                                    @if($editingReview)
                                        You've already reviewed this course
                                    @else
                                        Share your experience
                                    @endif
                                </h3>
                                <p class="text-themed-secondary mt-1">
                                    @if($editingReview)
                                        You can update your review or see it below
                                    @else
                                        Help other students by sharing your thoughts
                                    @endif
                                </p>
                            </div>
                        </div>
                        <button wire:click="toggleReviewForm" 
                                class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-{{ $showReviewForm ? 'times' : 'star' }}"></i>
                            {{ $editingReview ? 'Edit Review' : ($showReviewForm ? 'Cancel' : 'Write Review') }}
                        </button>
                    </div>

                    @if($editingReview)
                        <div class="mt-4 flex items-center gap-6 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-themed-primary">Your rating:</span>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-yellow-400 {{ $editingReview->rating >= $i ? '' : 'opacity-30' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-themed-tertiary">•</span>
                            <span class="text-themed-secondary">{{ $editingReview->created_at->diffForHumans() }}</span>
                            <span class="text-themed-tertiary">•</span>
                            <button wire:click="deleteReview({{ $editingReview->id }})" 
                                    onclick="return confirm('Are you sure you want to delete your review?')"
                                    class="text-red-600 hover:text-red-700 text-sm transition-colors duration-300">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="bg-accent-themed-primary/20 rounded-xl p-6 text-center border border-accent-themed-primary/30 transition-colors duration-300">
                <i class="fas fa-star text-accent-themed-primary text-3xl mb-3"></i>
                <h3 class="font-bold text-themed-primary text-lg mb-2">Want to share your experience?</h3>
                <p class="text-themed-secondary mb-4">Login to review this course and help other students</p>
                <a href="{{ route('login') }}" 
                   class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to Review
                </a>
            </div>
        @endauth

        <!-- Review Form -->
        @if($showReviewForm)
            <div class="bg-themed-secondary rounded-xl p-6 border border-themed-primary transition-colors duration-300">
                <h3 class="text-lg font-bold text-themed-primary mb-4">
                    {{ $editingReview ? 'Edit Your Review' : 'Write a Review' }}
                </h3>
                
                <form wire:submit.prevent="submitReview">
                    <!-- Star Rating -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-themed-primary mb-3">
                            Your Rating *
                        </label>
                        <div class="flex items-center gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        wire:click="$set('rating', {{ $i }})"
                                        class="text-3xl transition-all duration-200 transform hover:scale-110">
                                    <i class="fas fa-star {{ $rating >= $i ? 'text-yellow-400' : 'text-themed-tertiary' }}"></i>
                                </button>
                            @endfor
                        </div>
                        <p class="text-sm text-themed-secondary mt-2">
                            {{ $rating }} out of 5 stars
                        </p>
                    </div>

                    <!-- Comment -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-themed-primary mb-2">
                            Your Review *
                        </label>
                        <textarea wire:model="comment"
                                  rows="5"
                                  placeholder="Share your thoughts about this course. What did you like? What could be improved?"
                                  class="w-full px-4 py-3 bg-themed-tertiary border border-themed-secondary rounded-lg text-themed-primary placeholder-themed-tertiary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300"></textarea>
                        <div class="flex justify-between text-xs text-themed-tertiary mt-1">
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
                                class="px-6 py-3 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary font-semibold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg border border-themed-secondary">
                            Cancel
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 px-6 py-3 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-50">
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
        <div class="bg-themed-secondary rounded-xl p-6 border border-themed-primary transition-colors duration-300">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Overall Rating -->
                <div class="text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-3 mb-2">
                        <span class="text-5xl font-black text-themed-primary">
                            {{ number_format($averageRating, 1) }}
                        </span>
                        <div>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400 {{ $averageRating >= $i ? '' : ($averageRating >= $i - 0.5 ? 'opacity-50' : 'opacity-20') }}"></i>
                                @endfor
                            </div>
                            <p class="text-sm text-themed-secondary mt-1">
                                {{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}
                            </p>
                            @if($verifiedCount > 0)
                                <p class="text-xs text-green-600 font-semibold mt-1">
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
                            <span class="text-sm font-medium text-themed-primary w-12">{{ $star }} star</span>
                            <div class="flex-1 bg-themed-tertiary rounded-full h-2">
                                <div class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-themed-tertiary w-16 text-right">{{ $count }} ({{ number_format($percentage, 0) }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Reviews List Header -->
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
            <h3 class="text-lg font-bold text-themed-primary">Student Reviews</h3>
            
            <!-- Simple Filters -->
            <div class="flex gap-3">
                <select wire:model.live="sortBy"
                        class="bg-themed-tertiary border border-themed-secondary rounded-lg px-4 py-2 text-themed-primary text-sm focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
                    <option value="recent">Most Recent</option>
                    <option value="rating_high">Highest Rating</option>
                    <option value="rating_low">Lowest Rating</option>
                </select>

                <select wire:model.live="filterRating"
                        class="bg-themed-tertiary border border-themed-secondary rounded-lg px-4 py-2 text-themed-primary text-sm focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
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
                <div class="bg-themed-secondary rounded-lg p-6 border border-themed-primary transition-all duration-300 hover:shadow-sm">
                    <!-- Review Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent-themed-primary rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-themed-primary">{{ $review->user->name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $review->rating >= $i ? 'text-yellow-400' : 'text-themed-tertiary' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-themed-tertiary">{{ $review->created_at->diffForHumans() }}</span>
                                    @if($review->isVerified())
                                        <span class="px-2 py-0.5 bg-green-500/20 text-green-400 text-xs font-semibold rounded-full flex items-center gap-1 border border-green-500/30"
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
                                    class="text-themed-tertiary hover:text-accent-themed-primary transition-colors duration-300"
                                    title="Edit your review">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Review Content -->
                    <p class="text-themed-primary mb-4">{{ $review->review_text }}</p>

                    <!-- Instructor Reply -->
                    @if($review->instructor_reply)
                        <div class="mt-4 pl-4 border-l-4 border-accent-themed-primary bg-accent-themed-primary/10 p-4 rounded-r-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-reply text-accent-themed-primary"></i>
                                <span class="font-semibold text-themed-primary">Instructor Response</span>
                                <span class="text-xs text-themed-tertiary">{{ $review->replied_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-themed-primary">{{ $review->instructor_reply }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 bg-themed-tertiary rounded-lg border border-themed-secondary transition-colors duration-300">
                    <i class="fas fa-comments text-4xl text-themed-tertiary mb-3"></i>
                    <p class="text-themed-secondary">
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

    <style>
        /* Theme transition support */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</div>
