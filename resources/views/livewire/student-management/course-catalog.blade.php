<div class="bg-white dark:bg-gray-900 rounded-xl p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Explore Courses</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Discover skills that will shape your future</p>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-full">
                {{ $courses->total() }} courses available
            </span>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-5 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filter Courses</h2>
            <button wire:click="resetFilters" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm flex items-center">
                <i class="fas fa-redo-alt mr-2"></i> Reset Filters
            </button>
        </div>
        
        <!-- Search and Main Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Search courses..." 
                       class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                <div class="absolute left-3 top-3.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <select wire:model.live="categoryFilter" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="difficultyFilter" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                <option value="">All Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
            
            <select wire:model.live="sortBy" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                <option value="newest">Newest First</option>
                <option value="popular">Most Popular</option>
                <option value="rating">Highest Rated</option>
                <option value="duration">Duration</option>
            </select>
        </div>
        
        <!-- Secondary Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <select wire:model.live="durationFilter" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                <option value="">Any Duration</option>
                <option value="short">Short (< 1 hour)</option>
                <option value="medium">Medium (1-3 hours)</option>
                <option value="long">Long (> 3 hours)</option>
            </select>
            
            <select wire:model.live="priceFilter" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                <option value="">All Pricing</option>
                <option value="free">Free Courses</option>
                <option value="premium">Premium Courses</option>
            </select>
            
            <select wire:model.live="ratingFilter" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3">
                <option value="">Any Rating</option>
                <option value="4">4+ Stars</option>
                <option value="3">3+ Stars</option>
                <option value="2">2+ Stars</option>
            </select>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="flex justify-end mb-6">
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                <i class="fas fa-th-large mr-2"></i> Grid
            </button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-r border-gray-200 rounded-r-md hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                <i class="fas fa-list mr-2"></i> List
            </button>
        </div>
    </div>

    <!-- Course Grid -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300 overflow-hidden group">
                    <div class="relative overflow-hidden">
                        <img src="{{ $course->thumbnail ?? asset('images/default-course.png') }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            <span class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-full shadow">
                                {{ $course->category->name ?? 'Uncategorized' }}
                            </span>
                            <span class="bg-gray-800/90 text-white text-xs px-3 py-1.5 rounded-full shadow">
                                {{ ucfirst($course->difficulty_level) }}
                            </span>
                        </div>
                        <div class="absolute top-3 right-3 flex gap-2">
                            <button wire:click="toggleWishlist({{ $course->id }})" 
                                    class="p-2 bg-white/90 dark:bg-gray-800/90 rounded-full shadow-sm text-{{ in_array($course->id, $wishlistCourseIds) ? 'red-500' : 'gray-500' }} hover:text-red-500 transition-colors">
                                <i class="fas fa-heart text-sm"></i>
                            </button>
                            <button wire:click="showPreview({{ $course->id }})" 
                                    class="p-2 bg-white/90 dark:bg-gray-800/90 rounded-full shadow-sm text-gray-500 hover:text-blue-500 transition-colors">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <div class="absolute bottom-3 left-3 bg-black/70 text-white text-xs px-3 py-1.5 rounded-full">
                            <i class="far fa-clock mr-1"></i> 
                            {{ $course->estimated_duration_minutes ? round($course->estimated_duration_minutes/60) . 'h' : 'N/A' }}
                        </div>
                    </div>
                    
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-bold text-gray-900 dark:text-white text-lg line-clamp-2">{{ $course->title }}</h3>
                            <div class="flex items-center text-yellow-400 ml-2">
                                <i class="fas fa-star text-sm"></i>
                                <span class="text-sm font-medium ml-1 text-gray-700 dark:text-gray-300">{{ number_format($course->reviews_avg_rating ?? 0, 1) }}</span>
                            </div>
                        </div>
                        
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">{{ Str::limit($course->description, 80) }}</p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <div class="flex items-center">
                                <img src="{{ $course->instructor->avatar ?? asset('images/default-avatar.png') }}" 
                                     alt="{{ $course->instructor->name }}" 
                                     class="w-6 h-6 rounded-full mr-2">
                                <span>{{ $course->instructor->name }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users mr-1.5"></i> 
                                <span>{{ $course->enrollments_count }}</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                            <div class="text-sm font-medium">
                                @if($course->is_premium)
                                    <span class="text-yellow-600 dark:text-yellow-400">
                                        <i class="fas fa-crown mr-1.5"></i> ${{ number_format($course->price, 2) }}
                                    </span>
                                @else
                                    <span class="text-green-600 dark:text-green-400">
                                        <i class="fas fa-badge-check mr-1.5"></i> Free
                                    </span>
                                @endif
                            </div>
                            
                            @if(in_array($course->id, $enrolledCourseIds))
                                <a href="{{ route('course.view', $course->slug) }}" 
                                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center">
                                    Continue <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                                </a>
                            @else
                                <button wire:click="enroll({{ $course->id }})" 
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center">
                                    Enroll <i class="fas fa-plus-circle ml-1.5 text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-gray-50 dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-10 text-center">
            <div class="mx-auto w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-book-open text-gray-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No courses found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">Try adjusting your search filters or search terms to find what you're looking for.</p>
            <button wire:click="resetFilters" class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-redo-alt mr-2"></i> Reset Filters
            </button>
        </div>
    @endif

    <!-- Preview Modal -->
    @if($previewCourse)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-data x-on:click.self="$wire.closePreview()">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $previewCourse->title }}</h2>
                        <button wire:click="closePreview" class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-1">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div class="rounded-lg overflow-hidden mb-6">
                                <img src="{{ $previewCourse->thumbnail ?? asset('images/default-course.png') }}" 
                                     alt="{{ $previewCourse->title }}" 
                                     class="w-full h-60 object-cover">
                            </div>
                            
                            <div class="prose prose-gray dark:prose-invert max-w-none">
                                <h3 class="text-lg font-semibold mb-3">Description</h3>
                                <p class="text-gray-600 dark:text-gray-300">{{ $previewCourse->description }}</p>
                            </div>
                            
                            @if($previewCourse->learning_outcomes && count($previewCourse->learning_outcomes) > 0)
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold mb-3">What you'll learn</h3>
                                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach(array_slice($previewCourse->learning_outcomes, 0, 4) as $outcome)
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                                <span class="text-gray-600 dark:text-gray-300">{{ $outcome }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-5 mb-5">
                                <h3 class="font-bold text-gray-900 dark:text-white mb-4 text-lg">Course Details</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-300">Instructor:</span>
                                        <div class="flex items-center">
                                            <img src="{{ $previewCourse->instructor->avatar ?? asset('images/default-avatar.png') }}" 
                                                 alt="{{ $previewCourse->instructor->name }}" 
                                                 class="w-6 h-6 rounded-full mr-2">
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $previewCourse->instructor->name }}</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Duration:</span>
                                        <span class="text-gray-900 dark:text-white font-medium">
                                            {{ $previewCourse->estimated_duration_minutes ? round($previewCourse->estimated_duration_minutes/60) . ' hours' : 'Self-paced' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Level:</span>
                                        <span class="text-gray-900 dark:text-white font-medium capitalize">{{ $previewCourse->difficulty_level }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Students:</span>
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $previewCourse->enrollments_count }} enrolled</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">Rating:</span>
                                        <span class="text-yellow-500 font-medium flex items-center">
                                            {{ number_format($previewCourse->reviews_avg_rating ?? 0, 1) }} 
                                            <i class="fas fa-star ml-1 text-sm"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-5 mb-5">
                                <h3 class="font-bold text-gray-900 dark:text-white mb-3">This course includes</h3>
                                <ul class="space-y-2 text-sm">
                                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                                        <i class="fas fa-play-circle text-blue-500 mr-3"></i>
                                        {{ $previewCourse->total_lessons }} lessons
                                    </li>
                                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                                        <i class="fas fa-tasks text-blue-500 mr-3"></i>
                                        {{ $previewCourse->total_assessments }} exercises
                                    </li>
                                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                                        <i class="fas fa-infinity text-blue-500 mr-3"></i>
                                        Full lifetime access
                                    </li>
                                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                                        <i class="fas fa-mobile-alt text-blue-500 mr-3"></i>
                                        Access on mobile and TV
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="mt-6">
                                @if(in_array($previewCourse->id, $enrolledCourseIds))
                                    <a href="{{ route('course.view', $previewCourse->slug) }}" 
                                       class="w-full block text-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                        Continue Learning
                                    </a>
                                @else
                                    <button wire:click="enroll({{ $previewCourse->id }})" 
                                            class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center">
                                        <i class="fas fa-lock-open mr-2"></i>
                                        Enroll Now
                                    </button>
                                    <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        {{ $previewCourse->is_premium ? '7-day money-back guarantee' : 'Free forever' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>