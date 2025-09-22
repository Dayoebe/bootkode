<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8" 
         x-data="{
             activeTab: @entangle('activeTab'),
             showEnrollModal: @entangle('showEnrollModal'),
             showReviewModal: @entangle('showReviewModal'),
             showShareModal: false
         }">
        <!-- Hero Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6 sm:mb-8 transition-colors duration-300">
            <div class="relative">
                <!-- Course Banner/Thumbnail -->
                <div class="h-48 sm:h-64 lg:h-80 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 relative overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                             alt="{{ $course->title }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                    @else
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-6xl sm:text-8xl opacity-30"></i>
                        </div>
                    @endif
                    
                    <!-- Overlay Content -->
                    <div class="absolute inset-0 flex items-end p-4 sm:p-6 lg:p-8">
                        <div class="text-white max-w-4xl">
                            <!-- Category Badge -->
                            <div class="mb-3 sm:mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm text-white">
                                    <i class="fas fa-tag mr-2"></i>
                                    {{ $course->category->name ?? 'Uncategorized' }}
                                </span>
                            </div>
                            
                            <!-- Course Title -->
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black mb-2 sm:mb-4 leading-tight">
                                {{ $course->title }}
                            </h1>
                            
                            <!-- Subtitle -->
                            @if($course->subtitle)
                                <p class="text-lg sm:text-xl text-gray-200 mb-3 sm:mb-4 font-medium">
                                    {{ $course->subtitle }}
                                </p>
                            @endif
                            
                            <!-- Quick Stats -->
                            <div class="flex flex-wrap gap-4 sm:gap-6 text-sm sm:text-base">
                                <div class="flex items-center">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    <span>{{ $course->instructor->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span>{{ $this->formattedDuration }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    <span>{{ $totalLessons }} lessons</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-2"></i>
                                    <span>{{ number_format($totalEnrollments) }} students</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Left Column - Course Content -->
            <div class="lg:col-span-2 space-y-6 sm:space-y-8 mb-3">
                
                <!-- Navigation Tabs -->
                <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-2 transition-colors duration-300">
                    <nav class="flex space-x-2" role="tablist">
                        <button @click="activeTab = 'overview'" 
                                :class="activeTab === 'overview' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Overview
                        </button>
                        <button @click="activeTab = 'curriculum'" 
                                :class="activeTab === 'curriculum' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-list mr-2"></i>
                            Curriculum
                        </button>
                        <button @click="activeTab = 'reviews'" 
                                :class="activeTab === 'reviews' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-star mr-2"></i>
                            Reviews ({{ $totalReviews }})
                        </button>
                        <button @click="activeTab = 'instructor'" 
                                :class="activeTab === 'instructor' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Instructor
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                    
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="p-4 sm:p-6 lg:p-8">
                        
                        <!-- Course Description -->
                        <div class="mb-6 sm:mb-8">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-300">
                                About This Course
                            </h2>
                            <div class="prose prose-gray dark:prose-invert max-w-none text-sm sm:text-base">
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-300">
                                    {{ $course->description ?: 'No description available for this course.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Learning Outcomes -->
                        @if($course->learning_outcomes && count($course->learning_outcomes) > 0)
                            <div class="mb-6 sm:mb-8">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center transition-colors duration-300">
                                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                    What You'll Learn
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($course->learning_outcomes as $outcome)
                                        <div class="flex items-start bg-green-50 dark:bg-green-900/20 p-3 rounded-lg border border-green-200 dark:border-green-800 transition-colors duration-300">
                                            <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                                            <span class="text-gray-700 dark:text-gray-300 text-sm sm:text-base transition-colors duration-300">{{ $outcome }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Prerequisites -->
                        @if($course->prerequisites && count($course->prerequisites) > 0)
                            <div class="mb-6 sm:mb-8">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center transition-colors duration-300">
                                    <i class="fas fa-clipboard-list text-orange-500 mr-2"></i>
                                    Prerequisites
                                </h3>
                                <div class="space-y-2">
                                    @foreach($course->prerequisites as $prereq)
                                        <div class="flex items-start bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg border border-orange-200 dark:border-orange-800 transition-colors duration-300">
                                            <i class="fas fa-info-circle text-orange-500 mr-3 mt-0.5 flex-shrink-0"></i>
                                            <span class="text-gray-700 dark:text-gray-300 text-sm sm:text-base transition-colors duration-300">{{ $prereq }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Course Stats Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 sm:mb-8">
                            <div class="text-center bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800 transition-colors duration-300">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ number_format($totalLessons) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Lessons</div>
                            </div>
                            <div class="text-center bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-200 dark:border-green-800 transition-colors duration-300">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">{{ $this->formattedDuration }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Duration</div>
                            </div>
                            <div class="text-center bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-200 dark:border-purple-800 transition-colors duration-300">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">{{ number_format($totalEnrollments) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Students</div>
                            </div>
                            <div class="text-center bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-xl border border-yellow-200 dark:border-yellow-800 transition-colors duration-300">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mb-1">{{ number_format($averageRating, 1) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Rating</div>
                            </div>
                        </div>

                        <!-- Target Audience -->
                        @if($course->target_audience)
                            <div class="mb-6 sm:mb-8">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center transition-colors duration-300">
                                    <i class="fas fa-users text-purple-500 mr-2"></i>
                                    Target Audience
                                </h3>
                                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-200 dark:border-purple-800 transition-colors duration-300">
                                    <p class="text-gray-700 dark:text-gray-300 text-sm sm:text-base transition-colors duration-300">{{ $course->target_audience }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Curriculum Tab -->
                    <div x-show="activeTab === 'curriculum'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="p-4 sm:p-6 lg:p-8">
                        
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-6 transition-colors duration-300">
                            Course Curriculum
                        </h2>

                        @forelse($course->sections as $sectionIndex => $section)
                            <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-colors duration-300" x-data="{ expanded: {{ $sectionIndex === 0 ? 'true' : 'false' }} }">
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 sm:p-5 cursor-pointer transition-colors duration-300" @click="expanded = !expanded">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">
                                                {{ $sectionIndex + 1 }}
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900 dark:text-white text-base sm:text-lg transition-colors duration-300">
                                                    {{ $section->title }}
                                                </h3>
                                                <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors duration-300">
                                                    {{ $section->lessons->count() }} lessons • 
                                                    {{ $section->lessons->sum('duration_minutes') }}min
                                                </p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': expanded }"></i>
                                    </div>
                                </div>
                                
                                <div x-show="expanded" x-transition class="border-t border-gray-200 dark:border-gray-600">
                                    @forelse($section->lessons as $lesson)
                                        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-gray-100 dark:border-gray-600 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-play-circle text-blue-500 mr-3"></i>
                                                <div>
                                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm sm:text-base transition-colors duration-300">{{ $lesson->title }}</h4>
                                                    @if($lesson->description)
                                                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1 transition-colors duration-300">{{ Str::limit($lesson->description, 80) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                @if($lesson->duration_minutes)
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <span>{{ $lesson->duration_minutes }}min</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 sm:p-5 text-center text-gray-500 dark:text-gray-400 text-sm">
                                            No lessons in this section yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <i class="fas fa-book-open text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No curriculum available</h3>
                                <p class="text-gray-600 dark:text-gray-400">The course content is still being developed.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Reviews Tab -->
                    <div x-show="activeTab === 'reviews'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="p-4 sm:p-6 lg:p-8">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-0 transition-colors duration-300">
                                Student Reviews
                            </h2>
                            
                            @if($this->canReview())
                                <button @click="showReviewModal = true"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors text-sm">
                                    {{ $userReview ? 'Update Review' : 'Write Review' }}
                                </button>
                            @endif
                        </div>

                        <!-- Review Summary -->
                        @if($totalReviews > 0)
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 sm:p-6 rounded-xl mb-6 border border-yellow-200 dark:border-yellow-800 transition-colors duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mr-4">{{ number_format($averageRating, 1) }}</div>
                                        <div>
                                            <div class="flex items-center mb-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $averageRating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                @endfor
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($totalReviews) }} reviews</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Individual Reviews -->
                            <div class="space-y-6">
                                @foreach($course->reviews->take(5) as $review)
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0">
                                        <div class="flex items-start space-x-4">
                                            <div class="bg-gray-200 dark:bg-gray-600 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user text-gray-500 dark:text-gray-400"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">{{ $review->user->name }}</h4>
                                                    <div class="flex items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star text-sm {{ $review->rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                        @endfor
                                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                                <p class="text-gray-700 dark:text-gray-300 text-sm sm:text-base leading-relaxed">{{ $review->review_text }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="fas fa-star text-4xl text-gray-400 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No reviews yet</h3>
                                    <p class="text-gray-600 dark:text-gray-400">Be the first to review this course!</p>
                                </div>
                            @endif
                        </div>

                        <!-- Instructor Tab -->
                        <div x-show="activeTab === 'instructor'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform translate-y-4"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="p-4 sm:p-6 lg:p-8">
                            
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-6 transition-colors duration-300">
                                Meet Your Instructor
                            </h2>

                            <div class="flex flex-col sm:flex-row sm:items-start space-y-4 sm:space-y-0 sm:space-x-6 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-700/50 dark:to-gray-600/50 p-6 rounded-xl border border-blue-200 dark:border-gray-600">
                                <!-- Instructor Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-tie text-white text-2xl sm:text-3xl"></i>
                                    </div>
                                </div>
                                
                                <!-- Instructor Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $course->instructor->name }}</h3>
                                    <p class="text-blue-600 dark:text-blue-400 font-semibold mb-4">{{ $course->instructor->email }}</p>
                                    
                                    <!-- Instructor Stats -->
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-center">
                                        <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                            <div class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $course->instructor->courses()->count() }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Courses</div>
                                        </div>
                                        <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                            <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ $course->instructor->courses()->withCount('enrollments')->get()->sum('enrollments_count') }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Students</div>
                                        </div>
                                        <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                            <div class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($course->instructor->courses()->withAvg('reviews', 'rating')->get()->avg('reviews_avg_rating') ?: 0, 1) }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Rating</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQs Section -->
                @if($course->faqs && count($course->faqs) > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 lg:p-8 transition-colors duration-300 mb-3">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center transition-colors duration-300">
                            <i class="fas fa-question-circle text-purple-500 mr-3"></i>
                            Frequently Asked Questions
                        </h2>
                        
                        <div class="space-y-4">
                            @foreach($course->faqs as $index => $faq)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden transition-colors duration-300" x-data="{ expanded: false }">
                                    <button @click="expanded = !expanded" class="w-full text-left p-4 sm:p-5 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base pr-4">{{ $faq['question'] }}</h3>
                                            <i class="fas fa-chevron-down text-gray-400 transform transition-transform duration-200 flex-shrink-0" :class="{ 'rotate-180': expanded }"></i>
                                        </div>
                                    </button>
                                    <div x-show="expanded" x-transition class="p-4 sm:p-5 border-t border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                                        <p class="text-gray-700 dark:text-gray-300 text-sm sm:text-base leading-relaxed">{{ $faq['answer'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Course Actions & Info -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Course Action Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-6 transition-colors duration-300">
                    
                    <!-- Price Section -->
                    <div class="text-center mb-6">
                        @if($course->is_free)
                            <div class="text-3xl sm:text-4xl font-black text-green-600 dark:text-green-400 mb-2">FREE</div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Full access at no cost</p>
                        @else
                            <div class="text-3xl sm:text-4xl font-black text-blue-600 dark:text-blue-400 mb-2">
                                ${{ number_format($course->price, 2) }}
                                @if($course->is_premium)
                                    <span class="bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded-full ml-2">PREMIUM</span>
                                @endif
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">One-time payment</p>
                        @endif
                    </div>

                    <!-- Progress for Enrolled Students -->
                    @if($isEnrolled)
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-blue-700 dark:text-blue-300 font-semibold text-sm">Your Progress</span>
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">{{ $enrollmentProgress }}%</span>
                            </div>
                            <div class="w-full bg-blue-200 dark:bg-blue-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $enrollmentProgress }}%"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="space-y-2 xs:space-y-3 sm:space-y-4 mb-4 sm:mb-6 lg:mb-8">
                        @if($this->canEnroll())
                            <button @click="showEnrollModal = true" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold 
                                           py-2 xs:py-2.5 sm:py-3 lg:py-4 
                                           px-3 xs:px-4 sm:px-6 lg:px-8 
                                           rounded-md xs:rounded-lg sm:rounded-xl 
                                           transition-all duration-300 transform hover:scale-105 hover:shadow-md sm:hover:scale-105 sm:hover:shadow-lg 
                                           text-xs xs:text-sm sm:text-base lg:text-lg">
                                <i class="fas fa-rocket mr-1 xs:mr-1.5 sm:mr-2 lg:mr-3 text-xs xs:text-sm"></i>
                                <span class="hidden xs:inline">Enroll Now</span>
                                <span class="xs:hidden">Enroll</span>
                            </button>
                        @elseif($isEnrolled)
                            <a href="{{ route('course.view', $course->slug) }}" 
                               class="block w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold 
                                      py-2 xs:py-2.5 sm:py-3 lg:py-4 
                                      px-3 xs:px-4 sm:px-6 lg:px-8 
                                      rounded-md xs:rounded-lg sm:rounded-xl 
                                      transition-all duration-300 transform hover:scale-105 hover:shadow-md sm:hover:scale-105 sm:hover:shadow-lg 
                                      text-center text-xs xs:text-sm sm:text-base lg:text-lg">
                                <i class="fas fa-play mr-1 xs:mr-1.5 sm:mr-2 lg:mr-3 text-xs xs:text-sm"></i>
                                <span class="hidden sm:inline">Continue Learning</span>
                                <span class="hidden xs:inline sm:hidden">Continue</span>
                                <span class="xs:hidden">Learn</span>
                            </a>
                        @elseif(!$course->is_published || !$course->is_approved)
                            <div class="w-full bg-gray-400 text-white font-bold 
                                       py-2 xs:py-2.5 sm:py-3 lg:py-4 
                                       px-3 xs:px-4 sm:px-6 lg:px-8 
                                       rounded-md xs:rounded-lg sm:rounded-xl 
                                       text-center text-xs xs:text-sm sm:text-base lg:text-lg">
                                <i class="fas fa-lock mr-1 xs:mr-1.5 sm:mr-2 lg:mr-3 text-xs xs:text-sm"></i>
                                <span class="hidden sm:inline">Not Available</span>
                                <span class="sm:hidden">Unavailable</span>
                            </div>
                        @else
                            <a href="{{ route('login') }}" 
                               class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold 
                                      py-2 xs:py-2.5 sm:py-3 lg:py-4 
                                      px-3 xs:px-4 sm:px-6 lg:px-8 
                                      rounded-md xs:rounded-lg sm:rounded-xl 
                                      transition-all duration-300 transform hover:scale-105 hover:shadow-md sm:hover:scale-105 sm:hover:shadow-lg 
                                      text-center text-xs xs:text-sm sm:text-base lg:text-lg">
                                <i class="fas fa-sign-in-alt mr-1 xs:mr-1.5 sm:mr-2 lg:mr-3 text-xs xs:text-sm"></i>
                                <span class="hidden sm:inline">Login to Enroll</span>
                                <span class="hidden xs:inline sm:hidden">Login</span>
                                <span class="xs:hidden">Login</span>
                            </a>
                        @endif
                    
                        <button @click="showShareModal = true" 
                                class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold 
                                       py-2 xs:py-2.5 sm:py-3 lg:py-4 
                                       px-3 xs:px-4 sm:px-6 lg:px-8 
                                       rounded-md xs:rounded-lg sm:rounded-xl 
                                       transition-colors text-xs xs:text-sm sm:text-base lg:text-lg">
                            <i class="fas fa-share-alt mr-1 xs:mr-1.5 sm:mr-2 lg:mr-3 text-xs xs:text-sm"></i>
                            <span class="hidden xs:inline">Share Course</span>
                            <span class="xs:hidden">Share</span>
                        </button>
                    </div>

                    <!-- Course Details -->
                    <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg">Course Details</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-signal mr-2"></i>
                                    Difficulty
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white capitalize">{{ $course->difficulty_level }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-clock mr-2"></i>
                                    Duration
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $this->formattedDuration }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    Lessons
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $totalLessons }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-certificate mr-2"></i>
                                    Certificate
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $course->certificate_template ? 'Included' : 'Not Available' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-language mr-2"></i>
                                    Language
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-white">English</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Features -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg mb-4">What's Included</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center text-green-600 dark:text-green-400">
                                <i class="fas fa-check-circle mr-3"></i>
                                <span>Lifetime access</span>
                            </div>
                            <div class="flex items-center text-green-600 dark:text-green-400">
                                <i class="fas fa-check-circle mr-3"></i>
                                <span>{{ $totalLessons }} video lessons</span>
                            </div>
                            @if($course->has_projects)
                                <div class="flex items-center text-green-600 dark:text-green-400">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span>Hands-on projects</span>
                                </div>
                            @endif
                            @if($course->has_assessments)
                                <div class="flex items-center text-green-600 dark:text-green-400">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span>Quizzes and assessments</span>
                                </div>
                            @endif
                            @if($course->certificate_template)
                                <div class="flex items-center text-green-600 dark:text-green-400">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span>Certificate of completion</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Modal -->
       
<div x-show="$wire.showEnrollModal" 
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0" 
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100" 
x-transition:leave-end="opacity-0"
class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
x-cloak>


            <div @click.away="showEnrollModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8 w-full max-w-md border border-gray-200 dark:border-gray-700">
                
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">Enroll in Course</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Join {{ number_format($totalEnrollments) }} other students learning {{ $course->title }}</p>
                </div>

                <div class="text-center mb-6">
                    @if($course->is_free)
                        <div class="text-3xl font-black text-green-600 dark:text-green-400 mb-2">FREE</div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Get instant access to all course content</p>
                    @else
                        <div class="text-3xl font-black text-blue-600 dark:text-blue-400 mb-2">${{ number_format($course->price, 2) }}</div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">One-time payment • Lifetime access</p>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button @click="showEnrollModal = false"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold py-3 px-6 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button wire:click="enroll" @click="showEnrollModal = false"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                        {{ $course->is_free ? 'Enroll Free' : 'Enroll Now' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Review Modal -->
        <div x-show="$wire.showReviewModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        x-cloak>
            <div @click.away="showReviewModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8 w-full max-w-md border border-gray-200 dark:border-gray-700">
                
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $userReview ? 'Update Your Review' : 'Write a Review' }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Share your experience with other students</p>
                </div>

                <form wire:submit="submitReview" class="space-y-4">
                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                        <div class="flex justify-center space-x-1 mb-4" x-data="{ hover: 0 }">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        @click="$wire.reviewRating = {{ $i }}"
                                        @mouseover="hover = {{ $i }}"
                                        @mouseleave="hover = 0"
                                        class="text-2xl transition-colors"
                                        :class="(hover >= {{ $i }} || $wire.reviewRating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'">
                                    <i class="fas fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        @error('reviewRating') 
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Comment -->
                    <div>
                        <label for="reviewComment" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Your Review</label>
                        <textarea wire:model="reviewComment" 
                                  id="reviewComment"
                                  rows="4" 
                                  class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                  placeholder="Tell others about your experience with this course..."></textarea>
                        @error('reviewComment') 
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showReviewModal = false"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold py-3 px-6 rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                            {{ $userReview ? 'Update Review' : 'Submit Review' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Share Modal -->
        <div x-show="showShareModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        x-cloak>
            <div @click.away="showShareModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8 w-full max-w-md border border-gray-200 dark:border-gray-700">
                
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-share-alt text-green-600 dark:text-green-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">Share This Course</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Help others discover this amazing course</p>
                </div>

                <div class="space-y-3">
                    <!-- Copy Link -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center">
                            <input type="text" 
                                   value="{{ url()->current() }}" 
                                   class="flex-1 bg-transparent text-gray-700 dark:text-gray-300 text-sm"
                                   readonly>
                            <button onclick="navigator.clipboard.writeText('{{ url()->current() }}')"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                Copy
                            </button>
                        </div>
                    </div>

                    <!-- Social Share Buttons -->
                    <div class="grid grid-cols-2 gap-3">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode('Check out this course: ' . $course->title) }}"
                           target="_blank"
                           class="flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg font-semibold transition-colors">
                            <i class="fab fa-twitter mr-2"></i>
                            Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                           target="_blank"
                           class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition-colors">
                            <i class="fab fa-facebook-f mr-2"></i>
                            Facebook
                        </a>
                    </div>

                    <button @click="showShareModal = false"
                            class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold py-3 px-6 rounded-xl transition-colors mt-4">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notifications -->
        <div x-data="{
            show: false,
            message: '',
            type: 'success',
            icon: 'fas fa-check-circle'
        }" @notify.window="
            show = true; 
            message = $event.detail.message; 
            type = $event.detail.type || 'success';
            icon = $event.detail.icon || 'fas fa-check-circle';
            setTimeout(() => show = false, 5000)
         " x-show="show" 
            x-transition:enter="transform transition-all duration-300 ease-out"
            x-transition:enter-start="translate-x-full opacity-0" 
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition-all duration-300 ease-in"
            x-transition:leave-start="translate-x-0 opacity-100" 
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-4 sm:top-8 right-4 sm:right-8 z-50 max-w-sm sm:max-w-md" 
            x-cloak>

            <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden backdrop-blur-sm">
                <div :class="{
                    'bg-gradient-to-r from-emerald-500 to-green-500': type === 'success',
                    'bg-gradient-to-r from-red-500 to-pink-500': type === 'error',
                    'bg-gradient-to-r from-blue-500 to-purple-500': type === 'info',
                    'bg-gradient-to-r from-yellow-500 to-orange-500': type === 'warning'
                }" class="p-4 sm:p-6">

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i :class="icon" class="text-white text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                            <p class="text-white font-bold text-sm sm:text-lg leading-tight break-words" x-text="message"></p>
                        </div>
                        <button @click="show = false"
                            class="flex-shrink-0 ml-2 sm:ml-4 text-white hover:text-gray-200 transition-colors duration-200">
                            <i class="fas fa-times text-sm sm:text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl p-8 sm:p-12 flex flex-col items-center shadow-2xl border border-gray-200 dark:border-gray-700 mx-4">
                <div class="relative mb-4 sm:mb-6">
                    <div class="animate-spin rounded-full h-16 w-16 sm:h-20 sm:w-20 border-4 border-blue-200 dark:border-gray-600"></div>
                    <div class="animate-spin rounded-full h-16 w-16 sm:h-20 sm:w-20 border-4 border-blue-600 border-t-transparent absolute top-0"></div>
                </div>
                <span class="text-gray-800 dark:text-white font-black text-lg sm:text-xl text-center">Loading...</span>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
        </style>
    @endpush
</div>