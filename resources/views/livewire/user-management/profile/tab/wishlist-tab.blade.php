<!-- Wishlist Tab -->
<div x-show="activeTab === 'wishlist'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
    <div class="flex items-center justify-between mb-6 sm:mb-8">
        <div class="flex items-center">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-rose-500 to-pink-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4 shadow-lg">
                <i class="fas fa-heart text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">Wishlist</h2>
                <p class="text-sm sm:text-base text-themed-secondary transition-colors duration-300">Courses you're planning to take</p>
            </div>
        </div>
        @if (isset($wishlist) && $wishlist->count() > 0)
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-rose-100 border border-rose-200 text-rose-700 rounded-full text-sm font-medium">
                    {{ $wishlist->count() }} {{ Str::plural('item', $wishlist->count()) }}
                </span>
            </div>
        @endif
    </div>

    @if (isset($wishlist) && $wishlist->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            @foreach ($wishlist as $item)
                @if ($item->course && $item->course->is_published && $item->course->is_approved)
                    <div class="bg-themed-secondary hover:bg-themed-tertiary rounded-xl p-4 sm:p-6 transition-all duration-300 border border-themed-primary backdrop-blur-sm group hover:shadow-xl hover:shadow-rose-500/10 hover:-translate-y-1">
                        <div class="flex items-start gap-4">
                            <!-- Course Thumbnail -->
                            <a href="{{ route('course.view', $item->course) }}" 
                               class="flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-gradient-to-br from-themed-tertiary to-themed-primary shadow-md group-hover:shadow-lg transition-all duration-300">
                                @if ($item->course->thumbnail)
                                    <img src="{{ asset('storage/' . $item->course->thumbnail) }}" 
                                         alt="{{ $item->course->title }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-rose-500/10 to-pink-500/10 border border-rose-500/20 flex items-center justify-center">
                                        <i class="fas fa-book text-rose-500 text-xl sm:text-2xl"></i>
                                    </div>
                                @endif
                            </a>

                            <!-- Course Details -->
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('course.view', $item->course) }}" 
                                   class="block mb-2 group/title">
                                    <h4 class="text-themed-primary font-bold text-sm sm:text-base mb-1 line-clamp-2 group-hover/title:text-rose-600 transition-colors duration-300">
                                        {{ $item->course->title }}
                                    </h4>
                                </a>
                                
                                @if($item->course->category)
                                    <a href="{{ route('student.course-catalog', ['category' => $item->course->category->id]) }}" 
                                       class="inline-flex items-center text-xs sm:text-sm text-themed-secondary hover:text-rose-600 transition-colors duration-300 mb-3 group/cat">
                                        <i class="fas fa-folder-open mr-1.5 text-rose-500"></i>
                                        <span class="group-hover/cat:underline">{{ $item->course->category->name }}</span>
                                    </a>
                                @else
                                    <p class="text-xs sm:text-sm text-themed-secondary mb-3">
                                        <i class="fas fa-folder mr-1.5"></i>No category
                                    </p>
                                @endif
                                
                                <!-- Course Meta -->
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="px-2.5 py-1 bg-accent-themed-primary/10 border border-accent-themed-primary/20 rounded-lg text-xs font-medium text-accent-themed-primary flex items-center">
                                        <i class="fas fa-signal mr-1.5"></i>
                                        {{ $item->course->difficulty_level ?? 'Beginner' }}
                                    </span>
                                    @if($item->course->estimated_duration_minutes)
                                        <span class="px-2.5 py-1 bg-purple-500/10 border border-purple-500/20 rounded-lg text-xs font-medium text-purple-600 flex items-center">
                                            <i class="fas fa-clock mr-1.5"></i>
                                            {{ $item->course->formatted_duration }}
                                        </span>
                                    @endif
                                    @if($item->course->price)
                                        <span class="px-2.5 py-1 bg-green-500/10 border border-green-500/20 rounded-lg text-xs font-medium text-green-600 flex items-center">
                                            <i class="fas fa-tag mr-1.5"></i>
                                            {{ $item->course->formatted_price }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('course.view', $item->course) }}" 
                                       class="flex-1 px-3 py-2 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white rounded-lg text-xs font-semibold transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center group/btn">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        <span>View Course</span>
                                        <i class="fas fa-arrow-right ml-1.5 opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all duration-300"></i>
                                    </a>
                                    <button wire:click="removeFromWishlist({{ $item->course->id }})" 
                                            class="px-3 py-2 bg-themed-tertiary hover:bg-red-100 border border-themed-primary hover:border-red-300 text-themed-secondary hover:text-red-600 rounded-lg text-xs font-medium transition-all duration-300">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Added Date -->
                        <div class="mt-3 pt-3 border-t border-themed-primary">
                            <p class="text-xs text-themed-secondary flex items-center">
                                <i class="fas fa-clock mr-1.5"></i>
                                Added {{ $item->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Unavailable Course -->
                    <div class="bg-themed-tertiary rounded-xl p-4 sm:p-6 border border-themed-primary backdrop-blur-sm opacity-75">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-themed-primary/20">
                                <div class="w-full h-full bg-gradient-to-br from-themed-primary/10 to-themed-secondary/10 border border-themed-primary/30 flex items-center justify-center">
                                    <i class="fas fa-ban text-themed-secondary text-xl sm:text-2xl"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-themed-secondary font-semibold text-sm sm:text-base mb-1 sm:mb-2">
                                    {{ $item->course->title ?? 'Course Unavailable' }}
                                </h4>
                                <p class="text-themed-secondary text-xs sm:text-sm mb-3">
                                    <i class="fas fa-info-circle mr-1.5"></i>This course is no longer available
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 bg-themed-primary/10 border border-themed-primary/20 rounded-lg text-xs text-themed-secondary">
                                        <i class="fas fa-ban mr-1.5"></i>Unavailable
                                    </span>
                                    <button wire:click="removeFromWishlist({{ $item->course_id }})" 
                                            class="px-3 py-1.5 bg-themed-tertiary hover:bg-red-100 border border-themed-primary hover:border-red-300 text-themed-secondary hover:text-red-600 rounded-lg text-xs font-medium transition-all duration-300">
                                        <i class="fas fa-trash-alt mr-1.5"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-themed-secondary rounded-xl p-8 sm:p-12 md:p-16 text-center border border-themed-primary backdrop-blur-sm transition-colors duration-300">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg">
                <i class="fas fa-heart text-rose-500 text-3xl sm:text-4xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl text-themed-primary font-bold mb-2">Your wishlist is empty</h3>
            <p class="text-sm sm:text-base text-themed-secondary mb-6 sm:mb-8 max-w-md mx-auto">Start building your learning journey by adding courses you're interested in</p>
            <a href="{{ route('student.course-catalog') }}" 
               class="inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-rose-500/25 text-sm sm:text-base group">
                <i class="fas fa-compass mr-2"></i> 
                <span>Browse Courses</span>
                <i class="fas fa-arrow-right ml-2 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
            </a>
        </div>
    @endif
</div>