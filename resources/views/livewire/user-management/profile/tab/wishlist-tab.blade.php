<div x-show="activeTab === 'wishlist'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
    <div class="flex items-center justify-between mb-6 sm:mb-8">
        <div class="flex items-center">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-rose-500 to-pink-600 dark:from-rose-600 dark:to-pink-700 rounded-xl flex items-center justify-center mr-3 sm:mr-4 shadow-lg">
                <i class="fas fa-heart text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Wishlist</h2>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300">Courses you're planning to take</p>
            </div>
        </div>
        @if (isset($wishlist) && $wishlist->count() > 0)
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-rose-100 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 rounded-full text-sm font-medium">
                    {{ $wishlist->count() }} {{ Str::plural('item', $wishlist->count()) }}
                </span>
            </div>
        @endif
    </div>

    @if (isset($wishlist) && $wishlist->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            @foreach ($wishlist as $item)
                @if ($item->course && $item->course->is_published && $item->course->is_approved)
                    <div class="bg-white dark:bg-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-800/70 rounded-xl p-4 sm:p-6 transition-all duration-300 border border-gray-200 dark:border-gray-700/50 backdrop-blur-sm group hover:shadow-xl hover:shadow-rose-500/10 dark:hover:shadow-rose-500/5 hover:-translate-y-1">
                        <div class="flex items-start gap-4">
                            <!-- Course Thumbnail -->
                            <a href="{{ route('course.view', $item->course) }}" 
                               class="flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 shadow-md group-hover:shadow-lg transition-all duration-300">
                                @if ($item->course->thumbnail)
                                    <img src="{{ asset('storage/' . $item->course->thumbnail) }}" 
                                         alt="{{ $item->course->title }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-rose-500/10 to-pink-500/10 dark:from-rose-500/20 dark:to-pink-500/20 border border-rose-500/20 dark:border-rose-500/30 flex items-center justify-center">
                                        <i class="fas fa-book text-rose-500 dark:text-rose-400 text-xl sm:text-2xl"></i>
                                    </div>
                                @endif
                            </a>

                            <!-- Course Details -->
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('course.view', $item->course) }}" 
                                   class="block mb-2 group/title">
                                    <h4 class="text-gray-900 dark:text-white font-bold text-sm sm:text-base mb-1 line-clamp-2 group-hover/title:text-rose-600 dark:group-hover/title:text-rose-400 transition-colors duration-300">
                                        {{ $item->course->title }}
                                    </h4>
                                </a>
                                
                                @if($item->course->category)
                                    <a href="{{ route('student.course-catalog', ['category' => $item->course->category->id]) }}" 
                                       class="inline-flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors duration-300 mb-3 group/cat">
                                        <i class="fas fa-folder-open mr-1.5 text-rose-500 dark:text-rose-400"></i>
                                        <span class="group-hover/cat:underline">{{ $item->course->category->name }}</span>
                                    </a>
                                @else
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-500 mb-3">
                                        <i class="fas fa-folder mr-1.5"></i>No category
                                    </p>
                                @endif
                                
                                <!-- Course Meta -->
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-500/20 border border-blue-200 dark:border-blue-500/30 rounded-lg text-xs font-medium text-blue-700 dark:text-blue-300 flex items-center">
                                        <i class="fas fa-signal mr-1.5"></i>
                                        {{ $item->course->difficulty_level ?? 'Beginner' }}
                                    </span>
                                    @if($item->course->estimated_duration_minutes)
                                        <span class="px-2.5 py-1 bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-500/30 rounded-lg text-xs font-medium text-purple-700 dark:text-purple-300 flex items-center">
                                            <i class="fas fa-clock mr-1.5"></i>
                                            {{ $item->course->formatted_duration }}
                                        </span>
                                    @endif
                                    @if($item->course->price)
                                        <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/30 rounded-lg text-xs font-medium text-emerald-700 dark:text-emerald-300 flex items-center">
                                            <i class="fas fa-tag mr-1.5"></i>
                                            {{ $item->course->formatted_price }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('course.view', $item->course) }}" 
                                       class="flex-1 px-3 py-2 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 dark:from-rose-600 dark:to-pink-700 dark:hover:from-rose-700 dark:hover:to-pink-800 text-white rounded-lg text-xs font-semibold transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center group/btn">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        <span>View Course</span>
                                        <i class="fas fa-arrow-right ml-1.5 opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all duration-300"></i>
                                    </a>
                                    <button wire:click="removeFromWishlist({{ $item->course->id }})" 
                                            class="px-3 py-2 bg-gray-100 dark:bg-gray-700/50 hover:bg-red-100 dark:hover:bg-red-500/20 border border-gray-200 dark:border-gray-600/50 hover:border-red-300 dark:hover:border-red-500/30 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg text-xs font-medium transition-all duration-300">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Added Date -->
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700/50">
                            <p class="text-xs text-gray-500 dark:text-gray-500 flex items-center">
                                <i class="fas fa-clock mr-1.5"></i>
                                Added {{ $item->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Unavailable Course -->
                    <div class="bg-gray-100 dark:bg-gray-800/30 rounded-xl p-4 sm:p-6 border border-gray-300 dark:border-gray-700/30 backdrop-blur-sm opacity-75">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700/50">
                                <div class="w-full h-full bg-gradient-to-br from-gray-300/50 to-gray-400/50 dark:from-gray-600/20 dark:to-gray-700/20 border border-gray-400/30 dark:border-gray-600/30 flex items-center justify-center">
                                    <i class="fas fa-ban text-gray-500 dark:text-gray-500 text-xl sm:text-2xl"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-gray-600 dark:text-gray-500 font-semibold text-sm sm:text-base mb-1 sm:mb-2">
                                    {{ $item->course->title ?? 'Course Unavailable' }}
                                </h4>
                                <p class="text-gray-500 dark:text-gray-600 text-xs sm:text-sm mb-3">
                                    <i class="fas fa-info-circle mr-1.5"></i>This course is no longer available
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 bg-gray-200 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600/30 rounded-lg text-xs text-gray-600 dark:text-gray-500">
                                        <i class="fas fa-ban mr-1.5"></i>Unavailable
                                    </span>
                                    <button wire:click="removeFromWishlist({{ $item->course_id }})" 
                                            class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700/50 hover:bg-red-100 dark:hover:bg-red-500/20 border border-gray-300 dark:border-gray-600/50 hover:border-red-300 dark:hover:border-red-500/30 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg text-xs font-medium transition-all duration-300">
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
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/20 dark:to-gray-900/20 rounded-xl p-8 sm:p-12 md:p-16 text-center border border-gray-200 dark:border-gray-700/50 backdrop-blur-sm">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-rose-100 to-pink-100 dark:from-rose-500/20 dark:to-pink-500/20 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg">
                <i class="fas fa-heart text-rose-500 dark:text-rose-400 text-3xl sm:text-4xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl text-gray-900 dark:text-white font-bold mb-2">Your wishlist is empty</h3>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-6 sm:mb-8 max-w-md mx-auto">Start building your learning journey by adding courses you're interested in</p>
            <a href="{{ route('student.course-catalog') }}" 
               class="inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 dark:from-rose-600 dark:to-pink-700 dark:hover:from-rose-700 dark:hover:to-pink-800 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-rose-500/25 dark:hover:shadow-rose-500/20 text-sm sm:text-base group">
                <i class="fas fa-compass mr-2"></i> 
                <span>Browse Courses</span>
                <i class="fas fa-arrow-right ml-2 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
            </a>
        </div>
    @endif
</div>
