<div class="bg-gray-800 rounded-xl p-6">
    <!-- Filters Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
            <h1 class="text-2xl font-bold text-white">Course Catalog</h1>
            <button wire:click="resetFilters" class="text-blue-400 hover:text-blue-300 text-sm">
                <i class="fas fa-redo mr-1"></i> Reset Filters
            </button>
        </div>
        
        <!-- Main Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Search courses..." 
                       class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <select wire:model.live="categoryFilter" class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="difficultyFilter" class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                <option value="">All Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
            
            <select wire:model.live="sortBy" class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                <option value="newest">Newest</option>
                <option value="popular">Most Popular</option>
                <option value="rating">Highest Rated</option>
                <option value="duration">Duration (Longest)</option>
            </select>
        </div>
        
        <!-- Secondary Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <select wire:model.live="durationFilter" class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                <option value="">Any Duration</option>
                <option value="short">Short (< 1 hour)</option>
                <option value="medium">Medium (1-3 hours)</option>
                <option value="long">Long (> 3 hours)</option>
            </select>
            
            <select wire:model.live="priceFilter" class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                <option value="">All Pricing</option>
                <option value="free">Free Courses</option>
                <option value="premium">Premium Courses</option>
            </select>
            
            <select wire:model.live="ratingFilter" class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                <option value="">Any Rating</option>
                <option value="4">4+ Stars</option>
                <option value="3">3+ Stars</option>
            </select>
        </div>
    </div>

    <!-- Course Grid -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-gray-700 rounded-lg border border-gray-600 hover:border-blue-500 transition-colors overflow-hidden">
                    <div class="relative">
                        <img src="{{ $course->thumbnail ?? asset('images/default-course.png') }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-40 object-cover">
                        <div class="absolute top-2 left-2 flex gap-2">
                            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                {{ $course->category->name ?? 'Uncategorized' }}
                            </span>
                            <span class="bg-gray-800/90 text-white text-xs px-2 py-1 rounded">
                                {{ ucfirst($course->difficulty_level) }}
                            </span>
                        </div>
                        <div class="absolute top-2 right-2 flex gap-2">
                            <button wire:click="toggleWishlist({{ $course->id }})" 
                                    class="p-1.5 bg-gray-800/90 rounded-full text-{{ in_array($course->id, $wishlistCourseIds) ? 'red-400' : 'gray-300' }} hover:text-red-400">
                                <i class="fas fa-heart text-sm"></i>
                            </button>
                            <button wire:click="showPreview({{ $course->id }})" 
                                    class="p-1.5 bg-gray-800/90 rounded-full text-gray-300 hover:text-blue-400">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <div class="absolute bottom-2 left-2 bg-gray-900/80 text-white text-xs px-2 py-1 rounded">
                            {{ $course->estimated_duration_minutes ? round($course->estimated_duration_minutes/60) . 'h' : 'N/A' }}
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-white">{{ $course->title }}</h3>
                            <span class="text-yellow-400 text-sm">
                                <i class="fas fa-star"></i> {{ number_format($course->reviews_avg_rating ?? 0, 1) }}
                            </span>
                        </div>
                        
                        <p class="text-gray-400 text-sm mb-3 line-clamp-2">{{ $course->description }}</p>
                        
                        <div class="flex justify-between text-xs text-gray-400 mb-3">
                            <span>
                                <i class="fas fa-user-tie mr-1"></i> {{ $course->instructor->name }}
                            </span>
                            <span>
                                <i class="fas fa-users mr-1"></i> {{ $course->enrollments_count }} students
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-white">
                                @if($course->is_premium)
                                    <i class="fas fa-crown text-yellow-400 mr-1"></i> Premium
                                @else
                                    <i class="fas fa-badge-check text-blue-400 mr-1"></i> Free
                                @endif
                            </span>
                            
                            @if(in_array($course->id, $enrolledCourseIds))
                                <a href="{{ route('course.view', $course->slug) }}" 
                                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                                    Continue <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            @else
                                <button wire:click="enroll({{ $course->id }})" 
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">
                                    Enroll Now <i class="fas fa-plus-circle ml-1"></i>
                                </button>
                            @endif
                        </div>
                    </div>



                    <div class="absolute top-3 right-3">
                        <livewire:component.common.bookmark-button 
                            resourceableType="App\Models\Course" 
                            resourceableId="{{ $course->id }}"
                            size="sm"
                        />
                    </div>


                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    @else
        <div class="bg-gray-700/50 border border-dashed border-gray-600 rounded-xl p-8 text-center">
            <i class="fas fa-book-open text-gray-400 text-4xl mb-3"></i>
            <h3 class="text-lg font-medium text-gray-300 mb-2">No courses found</h3>
            <p class="text-gray-500 mb-4">Try adjusting your search filters</p>
            <button wire:click="resetFilters" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                <i class="fas fa-redo mr-2"></i> Reset Filters
            </button>
        </div>
    @endif

    <!-- Preview Modal -->
    @if($previewCourse)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-gray-800 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-2xl font-bold text-white">{{ $previewCourse->title }}</h2>
                        <button wire:click="closePreview" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div class="aspect-w-16 aspect-h-9 mb-4">
                                <img src="{{ $previewCourse->thumbnail ?? asset('images/default-course.png') }}" 
                                     alt="{{ $previewCourse->title }}" 
                                     class="w-full rounded-lg">
                            </div>
                            
                            <div class="prose prose-invert max-w-none">
                                {!! $previewCourse->description !!}
                            </div>
                        </div>
                        
                        <div class="lg:col-span-1">
                            <div class="bg-gray-700 rounded-lg p-4 mb-4">
                                <h3 class="font-bold text-white mb-3">Course Details</h3>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Instructor:</span>
                                        <span class="text-white">{{ $previewCourse->instructor->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Duration:</span>
                                        <span class="text-white">
                                            {{ $previewCourse->estimated_duration_minutes ? round($previewCourse->estimated_duration_minutes/60) . ' hours' : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Difficulty:</span>
                                        <span class="text-white capitalize">{{ $previewCourse->difficulty_level }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Students:</span>
                                        <span class="text-white">{{ $previewCourse->enrollments_count }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Rating:</span>
                                        <span class="text-yellow-400">
                                            {{ number_format($previewCourse->reviews_avg_rating ?? 0, 1) }} <i class="fas fa-star"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-700 rounded-lg p-4">
                                <h3 class="font-bold text-white mb-3">What You'll Learn</h3>
                                <ul class="space-y-2 text-sm">
                                    @foreach($previewCourse->sections->take(3) as $section)
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-400 mt-1 mr-2"></i>
                                            <span class="text-gray-300">{{ $section->title }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <div class="mt-4">
                                @if(in_array($previewCourse->id, $enrolledCourseIds))
                                    <a href="{{ route('course.view', $previewCourse->slug) }}" 
                                       class="w-full block text-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                        Continue Learning
                                    </a>
                                @else
                                    <button wire:click="enroll({{ $previewCourse->id }})" 
                                            class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                                        Enroll Now
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>