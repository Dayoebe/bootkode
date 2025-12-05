<div class="min-h-screen bg-themed-primary transition-colors duration-300">
    <div class="px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8" x-data="{
             activeTab: @entangle('activeTab'),
             showEnrollModal: @entangle('showEnrollModal'),
             showReviewModal: @entangle('showReviewModal'),
             showShareModal: false
         }">

        <!-- Hero Section -->
        <div
            class="bg-themed-secondary rounded-2xl sm:rounded-3xl shadow-lg border border-themed-primary overflow-hidden mb-6 sm:mb-8 transition-colors duration-300">
            <div class="relative">
                <!-- Course Banner/Thumbnail -->
                <div
                    class="h-48 sm:h-64 lg:h-80 bg-gradient-to-br from-accent-themed-primary to-purple-600 relative overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
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
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm text-white">
                                    <i class="fas fa-tag mr-2"></i>
                                    {{ $course->category->name ?? 'Uncategorized' }}
                                </span>
                            </div>

                            <!-- Course Title -->
                            <h1
                                class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black mb-2 sm:mb-4 leading-tight">
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
                <div
                    class="bg-themed-secondary rounded-xl sm:rounded-2xl shadow-lg border border-themed-primary p-2 transition-colors duration-300">
                    <nav class="flex space-x-2" role="tablist">
                        <button @click="activeTab = 'overview'"
                            :class="activeTab === 'overview' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary'"
                            class="flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Overview
                        </button>
                        <button @click="activeTab = 'curriculum'"
                            :class="activeTab === 'curriculum' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary'"
                            class="flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-list mr-2"></i>
                            Curriculum
                        </button>
                        <button @click="activeTab = 'reviews'"
                            :class="activeTab === 'reviews' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary'"
                            class="flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-star mr-2"></i>
                            Reviews ({{ $totalReviews }})
                        </button>
                        <button @click="activeTab = 'instructor'"
                            :class="activeTab === 'instructor' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary'"
                            class="flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Instructor
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div
                    class="bg-themed-secondary rounded-xl sm:rounded-2xl shadow-lg border border-themed-primary transition-colors duration-300">

                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0" class="p-4 sm:p-6 lg:p-8">

                        <!-- Course Description -->
                        <div class="mb-6 sm:mb-8">
                            <h2
                                class="text-xl sm:text-2xl font-bold text-themed-primary mb-4 transition-colors duration-300">
                                About This Course
                            </h2>
                            <p class="text-themed-secondary leading-relaxed transition-colors duration-300">
                                {{ $course->description ?: 'No description available for this course.' }}
                            </p>
                        </div>

                        <!-- Learning Outcomes -->
                        @if($course->learning_outcomes && count($course->learning_outcomes) > 0)
                            <div class="mb-6 sm:mb-8">
                                <h3
                                    class="text-lg sm:text-xl font-bold text-themed-primary mb-4 flex items-center transition-colors duration-300">
                                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                    What You'll Learn
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($course->learning_outcomes as $outcome)
                                        <div
                                            class="flex items-start bg-themed-tertiary p-3 rounded-lg border border-themed-primary transition-colors duration-300">
                                            <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                                            <span
                                                class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">{{ $outcome }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Course Stats Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div
                                class="text-center bg-themed-tertiary p-4 rounded-xl border border-themed-primary transition-colors duration-300">
                                <div class="text-2xl font-bold text-accent-themed-primary mb-1">
                                    {{ number_format($totalLessons) }}
                                </div>
                                <div class="text-sm text-themed-secondary font-medium">Lessons</div>
                            </div>
                            <div
                                class="text-center bg-themed-tertiary p-4 rounded-xl border border-themed-primary transition-colors duration-300">
                                <div class="text-2xl font-bold text-accent-themed-primary mb-1">
                                    {{ $this->formattedDuration }}
                                </div>
                                <div class="text-sm text-themed-secondary font-medium">Duration</div>
                            </div>
                            <div
                                class="text-center bg-themed-tertiary p-4 rounded-xl border border-themed-primary transition-colors duration-300">
                                <div class="text-2xl font-bold text-accent-themed-primary mb-1">
                                    {{ number_format($totalEnrollments) }}
                                </div>
                                <div class="text-sm text-themed-secondary font-medium">Students</div>
                            </div>
                            <div
                                class="text-center bg-themed-tertiary p-4 rounded-xl border border-themed-primary transition-colors duration-300">
                                <div class="text-2xl font-bold text-accent-themed-primary mb-1">
                                    {{ number_format($averageRating, 1) }}
                                </div>
                                <div class="text-sm text-themed-secondary font-medium">Rating</div>
                            </div>
                        </div>

                        <!-- Course Details Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mt-6 sm:mt-8">

                            <!-- Left Column - Prerequisites & Materials -->
                            <div class="space-y-6 sm:space-y-8">
                                <!-- Prerequisites -->
                                @if($course->prerequisites && count($course->prerequisites) > 0)
                                    <div
                                        class="bg-orange-50 dark:bg-orange-900/20 p-4 sm:p-6 rounded-xl border border-orange-200 dark:border-orange-800 transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-orange-700 dark:text-orange-300 mb-4 flex items-center transition-colors duration-300">
                                            <i
                                                class="fas fa-exclamation-circle text-orange-500 dark:text-orange-400 mr-2"></i>
                                            Prerequisites
                                        </h3>
                                        <ul class="space-y-2">
                                            @foreach($course->prerequisites as $prerequisite)
                                                @if(!empty(trim($prerequisite)))
                                                    <li
                                                        class="flex items-start text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                                        <i
                                                            class="fas fa-check text-orange-500 dark:text-orange-400 mr-3 mt-1 flex-shrink-0"></i>
                                                        <span>{{ $prerequisite }}</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Materials Included -->
                                @if($course->materials_included && count($course->materials_included) > 0)
                                    <div
                                        class="bg-purple-50 dark:bg-purple-900/20 p-4 sm:p-6 rounded-xl border border-purple-200 dark:border-purple-800 transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-purple-700 dark:text-purple-300 mb-4 flex items-center transition-colors duration-300">
                                            <i class="fas fa-box-open text-purple-500 dark:text-purple-400 mr-2"></i>
                                            Materials Included
                                        </h3>
                                        <ul class="space-y-2">
                                            @foreach($course->materials_included as $material)
                                                @if(!empty(trim($material)))
                                                    <li
                                                        class="flex items-start text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                                        <i
                                                            class="fas fa-check-circle text-purple-500 dark:text-purple-400 mr-3 mt-1 flex-shrink-0"></i>
                                                        <span>{{ $material }}</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column - Target Audience & Tags -->
                            <div class="space-y-6 sm:space-y-8">
                                <!-- Target Audience -->
                                @if($course->target_audience)
                                    <div
                                        class="bg-blue-50 dark:bg-blue-900/20 p-4 sm:p-6 rounded-xl border border-blue-200 dark:border-blue-800 transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-blue-700 dark:text-blue-300 mb-4 flex items-center transition-colors duration-300">
                                            <i class="fas fa-users text-blue-500 dark:text-blue-400 mr-2"></i>
                                            Target Audience
                                        </h3>
                                        <p
                                            class="text-themed-secondary text-sm sm:text-base leading-relaxed transition-colors duration-300">
                                            {{ $course->target_audience }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Syllabus Overview -->
                                @if($course->syllabus_overview)
                                    <div
                                        class="bg-green-50 dark:bg-green-900/20 p-4 sm:p-6 rounded-xl border border-green-200 dark:border-green-800 transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-green-700 dark:text-green-300 mb-4 flex items-center transition-colors duration-300">
                                            <i class="fas fa-book-open text-green-500 dark:text-green-400 mr-2"></i>
                                            Syllabus Overview
                                        </h3>
                                        <p
                                            class="text-themed-secondary text-sm sm:text-base leading-relaxed transition-colors duration-300">
                                            {{ $course->syllabus_overview }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Course Tags -->
                                @if($course->tags && count($course->tags) > 0)
                                    <div
                                        class="bg-indigo-50 dark:bg-indigo-900/20 p-4 sm:p-6 rounded-xl border border-indigo-200 dark:border-indigo-800 transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-indigo-700 dark:text-indigo-300 mb-4 flex items-center transition-colors duration-300">
                                            <i class="fas fa-tags text-indigo-500 dark:text-indigo-400 mr-2"></i>
                                            Course Tags
                                        </h3>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($course->tags as $tag)
                                                @if(!empty(trim($tag)))
                                                    <span
                                                        class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-medium transition-colors duration-300">
                                                        {{ $tag }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- FAQs Section -->
                        @if($course->faqs && count($course->faqs) > 0)
                            <div class="mt-6 sm:mt-8">
                                <h3
                                    class="text-lg sm:text-xl font-bold text-themed-primary mb-6 flex items-center transition-colors duration-300">
                                    <i class="fas fa-question-circle text-teal-500 dark:text-teal-400 mr-2"></i>
                                    Frequently Asked Questions
                                </h3>
                                <div class="space-y-4">
                                    @foreach($course->faqs as $index => $faq)
                                        @if(!empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? '')))
                                            <div class="bg-themed-tertiary border border-themed-primary rounded-xl overflow-hidden transition-colors duration-300"
                                                x-data="{ expanded: false }">
                                                <button @click="expanded = !expanded"
                                                    class="w-full px-4 sm:px-6 py-4 text-left flex items-center justify-between hover:bg-themed-primary transition-colors duration-300">
                                                    <span
                                                        class="font-semibold text-themed-primary text-sm sm:text-base transition-colors duration-300">
                                                        {{ $faq['question'] }}
                                                    </span>
                                                    <i class="fas fa-chevron-down text-themed-secondary transform transition-transform duration-200"
                                                        :class="{ 'rotate-180': expanded }"></i>
                                                </button>
                                                <div x-show="expanded" x-transition
                                                    class="px-4 sm:px-6 pb-4 border-t border-themed-primary">
                                                    <p
                                                        class="text-themed-secondary text-sm sm:text-base leading-relaxed pt-4 transition-colors duration-300">
                                                        {{ $faq['answer'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif



                    </div>

                    <!-- Curriculum Tab -->
                    <div x-show="activeTab === 'curriculum'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0" class="p-4 sm:p-6 lg:p-8">

                        <h2
                            class="text-xl sm:text-2xl font-bold text-themed-primary mb-6 transition-colors duration-300">
                            Course Curriculum
                        </h2>

                        @forelse($course->sections as $sectionIndex => $section)
                            <div class="mb-6 border border-themed-primary rounded-xl overflow-hidden transition-colors duration-300"
                                x-data="{ expanded: {{ $sectionIndex === 0 ? 'true' : 'false' }} }">
                                <div class="bg-themed-tertiary p-4 sm:p-5 cursor-pointer transition-colors duration-300"
                                    @click="expanded = !expanded">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div
                                                class="bg-accent-themed-primary text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">
                                                {{ $sectionIndex + 1 }}
                                            </div>
                                            <div>
                                                <h3
                                                    class="font-semibold text-themed-primary text-base sm:text-lg transition-colors duration-300">
                                                    {{ $section->title }}
                                                </h3>
                                                <p class="text-themed-secondary text-sm transition-colors duration-300">
                                                    {{ $section->lessons->count() }} lessons •
                                                    {{ $section->lessons->sum('duration_minutes') }}min
                                                </p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-down text-themed-secondary transform transition-transform duration-200"
                                            :class="{ 'rotate-180': expanded }"></i>
                                    </div>
                                </div>

                                <div x-show="expanded" x-transition class="border-t border-themed-primary">
                                    @forelse($section->lessons as $lesson)
                                        <div
                                            class="flex items-center justify-between p-4 sm:p-5 border-b border-themed-primary last:border-b-0 hover:bg-themed-tertiary transition-colors duration-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-play-circle text-accent-themed-primary mr-3"></i>
                                                <div>
                                                    <h4
                                                        class="font-medium text-themed-primary text-sm sm:text-base transition-colors duration-300">
                                                        {{ $lesson->title }}
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="flex items-center text-sm text-themed-secondary">
                                                @if($lesson->duration_minutes)
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <span>{{ $lesson->duration_minutes }}min</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 sm:p-5 text-center text-themed-secondary text-sm">
                                            No lessons in this section yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <i class="fas fa-book-open text-4xl text-themed-tertiary mb-4"></i>
                                <h3 class="text-lg font-medium text-themed-primary mb-2 transition-colors duration-300">No
                                    curriculum available</h3>
                                <p class="text-themed-secondary transition-colors duration-300">The course content is still
                                    being developed.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Reviews Tab -->
                    <div x-show="activeTab === 'reviews'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0" class="p-4 sm:p-6 lg:p-8">

                        @if($totalReviews > 0)
                            <div class="space-y-6">
                                @foreach($course->reviews->take(5) as $review)
                                    <div class="border-b border-themed-primary pb-6 last:border-b-0">
                                        <div class="flex items-start space-x-4">
                                            <div
                                                class="bg-themed-tertiary rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user text-themed-secondary"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="font-semibold text-themed-primary text-sm sm:text-base">
                                                        {{ $review->user->name }}
                                                    </h4>
                                                    <span
                                                        class="text-sm text-themed-secondary">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-themed-secondary text-sm sm:text-base leading-relaxed">
                                                    {{ $review->review_text }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-star text-4xl text-themed-tertiary mb-4"></i>
                                <h3 class="text-lg font-medium text-themed-primary mb-2 transition-colors duration-300">No
                                    reviews yet</h3>
                                <p class="text-themed-secondary transition-colors duration-300">Be the first to review this
                                    course!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Instructor Tab -->
                    <div x-show="activeTab === 'instructor'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0" class="p-4 sm:p-6 lg:p-8">

                        <h2
                            class="text-xl sm:text-2xl font-bold text-themed-primary mb-6 transition-colors duration-300">
                            Meet Your Instructor
                        </h2>

                        <div
                            class="flex flex-col sm:flex-row sm:items-start space-y-4 sm:space-y-0 sm:space-x-6 bg-themed-tertiary p-6 rounded-xl border border-themed-primary">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-accent-themed-primary to-purple-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-tie text-white text-2xl sm:text-3xl"></i>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-xl sm:text-2xl font-bold text-themed-primary mb-2 transition-colors duration-300">
                                    {{ $course->instructor->name }}
                                </h3>
                                <p class="text-accent-themed-primary font-semibold mb-4">
                                    {{ $course->instructor->email }}
                                </p>

                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div class="bg-themed-secondary p-3 rounded-lg border border-themed-primary">
                                        <div class="text-xl font-bold text-accent-themed-primary">
                                            {{ $course->instructor->courses()->count() }}
                                        </div>
                                        <div class="text-sm text-themed-secondary">Courses</div>
                                    </div>
                                    <div class="bg-themed-secondary p-3 rounded-lg border border-themed-primary">
                                        <div class="text-xl font-bold text-accent-themed-primary">
                                            {{ $course->instructor->courses()->withCount('enrollments')->get()->sum('enrollments_count') }}
                                        </div>
                                        <div class="text-sm text-themed-secondary">Students</div>
                                    </div>
                                    <div class="bg-themed-secondary p-3 rounded-lg border border-themed-primary">
                                        <div class="text-xl font-bold text-accent-themed-primary">
                                            {{ number_format($course->instructor->courses()->withAvg('reviews', 'rating')->get()->avg('reviews_avg_rating') ?: 0, 1) }}
                                        </div>
                                        <div class="text-sm text-themed-secondary">Rating</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Course Actions & Info -->
            <div class="lg:col-span-1 space-y-6">
                <div
                    class="bg-themed-secondary rounded-xl sm:rounded-2xl shadow-lg border border-themed-primary p-6 sticky top-6 transition-colors duration-300">

                    <!-- Price Section -->
                    <div class="text-center mb-6">
                        @if($course->is_free)
                            <div class="text-3xl sm:text-4xl font-black text-green-500 mb-2">FREE</div>
                            <p class="text-themed-secondary text-sm">Full access at no cost</p>
                        @else
                            <div class="text-3xl sm:text-4xl font-black text-accent-themed-primary mb-2">
                                ₦{{ number_format($course->price, 2) }}
                            </div>
                            <p class="text-themed-secondary text-sm">One-time payment</p>
                        @endif
                    </div>

                    <!-- Action Button -->
                    @if(!$isEnrolled)
                        <button wire:click="openEnrollmentModal" wire:loading.attr="disabled"
                            class="w-full bg-accent-themed-primary hover:bg-accent-themed-secondary text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-lg mb-4">
                            <span wire:loading.remove wire:target="openEnrollmentModal" class="flex items-center gap-2">
                                <i class="fas fa-rocket"></i>
                                Enroll Now
                            </span>
                            <span wire:loading wire:target="openEnrollmentModal" class="flex items-center gap-2">
                                <i class="fas fa-spinner animate-spin"></i>
                                Processing...
                            </span>
                        </button>
                    @else
                        <div class="flex gap-2 mb-4">
                            <a href="{{ route('course.view', $course->slug) }}"
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-lg">
                                <i class="fas fa-play mr-2"></i>
                                Continue Learning
                            </a>
                            <button wire:click="dropCourse({{ $course->id }})" wire:loading.attr="disabled"
                                class="bg-themed-tertiary hover:bg-red-100 text-red-700 font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 border border-themed-primary">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    @endif






                    <!-- Course Details -->
                    <div class="space-y-4 pt-6 border-t border-themed-primary">
                        <h3 class="font-semibold text-themed-primary text-lg transition-colors duration-300">Course
                            Details</h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-themed-secondary flex items-center transition-colors duration-300">
                                    <i class="fas fa-signal mr-2"></i>
                                    Difficulty
                                </span>
                                <span
                                    class="font-semibold text-themed-primary capitalize transition-colors duration-300">{{ $course->difficulty_level }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-themed-secondary flex items-center transition-colors duration-300">
                                    <i class="fas fa-clock mr-2"></i>
                                    Duration
                                </span>
                                <span
                                    class="font-semibold text-themed-primary transition-colors duration-300">{{ $this->formattedDuration }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-themed-secondary flex items-center transition-colors duration-300">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    Lessons
                                </span>
                                <span
                                    class="font-semibold text-themed-primary transition-colors duration-300">{{ $totalLessons }}</span>
                            </div>
                        </div>
                    </div>


                    <!-- Add to the right column after course details -->
                    @auth
                        <!-- Progress Section for Enrolled Users -->
                        @if($isEnrolled)
                            <div class="pt-6 border-t border-themed-primary">
                                <h3 class="font-semibold text-themed-primary text-lg mb-4 transition-colors duration-300">
                                    Your Progress
                                </h3>
                                <div class="space-y-4">
                                    <!-- Progress Bar -->
                                    <div>
                                        <div class="flex justify-between text-sm mb-2">
                                            <span class="text-themed-secondary">Course Progress</span>
                                            <span class="font-semibold text-themed-primary">{{ $enrollmentProgress }}%</span>
                                        </div>
                                        <div class="w-full bg-themed-tertiary rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                                                style="width: {{ $enrollmentProgress }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Continue Learning Button -->
                                    @if($this->getNextLesson())
                                        <a href="{{ route('course.view', ['course' => $course->slug]) }}"
                                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center">
                                            <i class="fas fa-play-circle mr-2"></i>
                                            Continue Learning
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="pt-6 border-t border-themed-primary">
                            <div class="flex space-x-3">
                                <!-- Favorite Button -->
                                <button wire:click="toggleFavorite"
                                    class="flex-1 bg-themed-tertiary hover:bg-themed-primary border border-themed-primary text-themed-secondary hover:text-red-500 font-semibold py-2 px-4 rounded-xl transition-all duration-300 flex items-center justify-center">
                                    <i class="fas {{ $isFavorited ? 'fa-heart text-red-500' : 'fa-heart' }} mr-2"></i>
                                    {{ $isFavorited ? 'Favorited' : 'Favorite' }}
                                </button>

                                <!-- Share Button -->
                                <button @click="showShareModal = true"
                                    class="flex-1 bg-themed-tertiary hover:bg-themed-primary border border-themed-primary text-themed-secondary hover:text-blue-500 font-semibold py-2 px-4 rounded-xl transition-all duration-300 flex items-center justify-center">
                                    <i class="fas fa-share-alt mr-2"></i>
                                    Share
                                </button>
                            </div>
                        </div>
                    @endauth

                </div>
            </div>
        </div>

        <!-- Toast Notifications -->
        <div x-data="{
            show: false,
            message: '',
            type: 'success'
        }" @notify.window="
            show = true; 
            message = $event.detail.message; 
            type = $event.detail.type || 'success';
            setTimeout(() => show = false, 5000)
         " x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-4 sm:top-8 right-4 sm:right-8 z-50 max-w-sm sm:max-w-md" x-cloak>

            <div
                class="bg-themed-secondary rounded-xl sm:rounded-2xl shadow-2xl border border-themed-primary overflow-hidden backdrop-blur-sm transition-colors duration-300">
                <div :class="{
                    'bg-gradient-to-r from-emerald-500 to-green-500': type === 'success',
                    'bg-gradient-to-r from-red-500 to-pink-500': type === 'error',
                    'bg-gradient-to-r from-blue-500 to-purple-500': type === 'info'
                }" class="p-4 sm:p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i :class="type === 'success' ? 'fas fa-check-circle' : (type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle')"
                                class="text-white text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                            <p class="text-white font-bold text-sm sm:text-lg leading-tight break-words"
                                x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div
                class="bg-themed-secondary rounded-2xl sm:rounded-3xl p-8 sm:p-12 flex flex-col items-center shadow-2xl border border-themed-primary mx-4 transition-colors duration-300">
                <div class="relative mb-4 sm:mb-6">
                    <div class="animate-spin rounded-full h-16 w-16 sm:h-20 sm:w-20 border-4 border-themed-tertiary">
                    </div>
                    <div
                        class="animate-spin rounded-full h-16 w-16 sm:h-20 sm:w-20 border-4 border-accent-themed-primary border-t-transparent absolute top-0">
                    </div>
                </div>
                <span
                    class="text-themed-primary font-black text-lg sm:text-xl text-center transition-colors duration-300">Loading...</span>
            </div>
        </div>
    </div>
    <!-- Share Modal -->
    <div x-show="showShareModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-cloak>

        <div
            class="bg-themed-secondary rounded-2xl sm:rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-themed-primary transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Share This Course</h3>
                <button @click="showShareModal = false"
                    class="text-themed-secondary hover:text-themed-primary transition-colors duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Share Options -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                <button wire:click="shareCourse('twitter')"
                    class="bg-blue-400 hover:bg-blue-500 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 flex flex-col items-center">
                    <i class="fab fa-twitter text-xl mb-2"></i>
                    <span class="text-xs font-semibold">Twitter</span>
                </button>

                <button wire:click="shareCourse('facebook')"
                    class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 flex flex-col items-center">
                    <i class="fab fa-facebook text-xl mb-2"></i>
                    <span class="text-xs font-semibold">Facebook</span>
                </button>

                <button wire:click="shareCourse('linkedin')"
                    class="bg-blue-800 hover:bg-blue-900 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 flex flex-col items-center">
                    <i class="fab fa-linkedin text-xl mb-2"></i>
                    <span class="text-xs font-semibold">LinkedIn</span>
                </button>

                <button wire:click="shareCourse('whatsapp')"
                    class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-xl transition-all duration-300 transform hover:scale-105 flex flex-col items-center">
                    <i class="fab fa-whatsapp text-xl mb-2"></i>
                    <span class="text-xs font-semibold">WhatsApp</span>
                </button>
            </div>

            <!-- Copy Link -->
            <div class="flex space-x-2">
                <input type="text" value="{{ $shareUrl }}" readonly
                    class="flex-1 bg-themed-tertiary border border-themed-primary rounded-xl px-4 py-3 text-themed-primary text-sm transition-colors duration-300">
                <button onclick="navigator.clipboard.writeText('{{ $shareUrl }}')" @click="showShareModal = false"
                    class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-3 rounded-xl transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>









<!-- Payment Confirmation Modal -->
<div x-show="$wire.showPaymentModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
     style="display: none;">
   
   <div @click.away="$wire.closePaymentModal()"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" 
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-themed-secondary rounded-2xl shadow-2xl w-full max-w-lg border border-themed-primary transition-colors duration-300">
       
       <!-- Modal Header -->
       <div class="p-6 border-b border-themed-primary">
           <div class="flex items-center justify-between">
               <h3 class="text-2xl font-bold text-themed-primary flex items-center gap-3">
                   <div class="bg-accent-themed-primary/10 p-3 rounded-xl">
                       <i class="fas fa-wallet text-accent-themed-primary"></i>
                   </div>
                   Confirm Enrollment
               </h3>
               <button wire:click="closePaymentModal" 
                       class="text-themed-secondary hover:text-themed-primary p-2 rounded-lg hover:bg-themed-tertiary transition-all duration-300">
                   <i class="fas fa-times text-xl"></i>
               </button>
           </div>
       </div>

       <!-- Modal Body -->
       <div class="p-6 space-y-6">
           <!-- Course Info -->
           <div class="bg-themed-tertiary rounded-xl p-4 border border-themed-primary">
               <div class="flex items-start gap-4">
                   @if($course && $course->thumbnail)
                       <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                            alt="{{ $course->title ?? 'Course' }}"
                            class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                   @else
                       <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-accent-themed-primary to-purple-500 flex items-center justify-center flex-shrink-0">
                           <i class="fas fa-graduation-cap text-white text-2xl"></i>
                       </div>
                   @endif
                   
                   <div class="flex-1 min-w-0">
                       <h4 class="font-bold text-themed-primary text-base mb-1 line-clamp-2">
                           {{ $course->title ?? 'Course Title' }}
                       </h4>
                       <p class="text-themed-secondary text-sm">
                           by {{ $course->instructor->name ?? 'Instructor' }}
                       </p>
                   </div>
               </div>
           </div>

           <!-- Payment Summary -->
           <div class="space-y-4">
               <h4 class="font-bold text-themed-primary text-lg">Payment Summary</h4>
               
               <!-- Wallet Balance -->
               <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-xl border border-themed-primary">
                   <div class="flex items-center gap-3">
                       <div class="bg-blue-500/10 p-2 rounded-lg">
                           <i class="fas fa-wallet text-blue-500"></i>
                       </div>
                       <span class="text-themed-secondary font-medium">Current Balance</span>
                   </div>
                   <span class="font-bold text-themed-primary text-lg">
                       ₦{{ number_format($walletBalance ?? 0, 2) }}
                   </span>
               </div>

               <!-- Course Price -->
               <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-xl border border-themed-primary">
                   <div class="flex items-center gap-3">
                       <div class="bg-orange-500/10 p-2 rounded-lg">
                           <i class="fas fa-tag text-orange-500"></i>
                       </div>
                       <span class="text-themed-secondary font-medium">Course Price</span>
                   </div>
                   <span class="font-bold text-themed-primary text-lg">
                       - ₦{{ number_format($coursePrice ?? 0, 2) }}
                   </span>
               </div>

               <!-- Balance After Payment -->
               <div class="flex items-center justify-between p-4 rounded-xl border-2 transition-colors duration-300
                   {{ ($hasSufficientFunds ?? false) ? 'bg-green-50 border-green-300 dark:bg-green-900/20 dark:border-green-700' : 'bg-red-50 border-red-300 dark:bg-red-900/20 dark:border-red-700' }}">
                   <div class="flex items-center gap-3">
                       <div class="{{ ($hasSufficientFunds ?? false) ? 'bg-green-500/10' : 'bg-red-500/10' }} p-2 rounded-lg">
                           <i class="fas {{ ($hasSufficientFunds ?? false) ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500' }}"></i>
                       </div>
                       <span class="{{ ($hasSufficientFunds ?? false) ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }} font-medium">
                           Balance After Payment
                       </span>
                   </div>
                   <span class="font-bold text-xl {{ ($hasSufficientFunds ?? false) ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                       ₦{{ number_format($balanceAfterPayment ?? 0, 2) }}
                   </span>
               </div>

               <!-- Insufficient Funds Warning -->
               @if(!($hasSufficientFunds ?? true))
                   <div class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded-xl p-4">
                       <div class="flex gap-3">
                           <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl flex-shrink-0"></i>
                           <div>
                               <h5 class="font-bold text-red-700 dark:text-red-300 mb-1">Insufficient Funds</h5>
                               <p class="text-red-600 dark:text-red-400 text-sm">
                                   You need ₦{{ number_format(($coursePrice ?? 0) - ($walletBalance ?? 0), 2) }} more to enroll in this course.
                               </p>
                           </div>
                       </div>
                   </div>
               @endif
           </div>
       </div>

       <!-- Modal Footer -->
       <div class="p-6 border-t border-themed-primary bg-themed-tertiary">
           <div class="flex gap-3">
               @if($hasSufficientFunds ?? false)
                   <button wire:click="closePaymentModal"
                           class="flex-1 bg-themed-secondary hover:bg-themed-primary text-themed-primary font-semibold py-3 px-6 rounded-xl transition-all duration-300 border border-themed-primary transform hover:scale-105">
                       Cancel
                   </button>
                   <button wire:click="confirmEnrollment" 
                           wire:loading.attr="disabled"
                           class="flex-1 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 disabled:opacity-50 transform hover:scale-105 shadow-lg">
                       <span wire:loading.remove wire:target="confirmEnrollment" class="flex items-center justify-center gap-2">
                           <i class="fas fa-check-circle"></i>
                           Confirm & Enroll
                       </span>
                       <span wire:loading wire:target="confirmEnrollment" class="flex items-center justify-center gap-2">
                           <i class="fas fa-spinner animate-spin"></i>
                           Processing...
                       </span>
                   </button>
               @else
                   <button wire:click="closePaymentModal"
                           class="flex-1 bg-themed-secondary hover:bg-themed-primary text-themed-primary font-semibold py-3 px-6 rounded-xl transition-all duration-300 border border-themed-primary transform hover:scale-105">
                       Cancel
                   </button>
                   <a href="{{ route('wallet.index') }}"
                      class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                       <i class="fas fa-plus-circle"></i>
                       Fund Wallet
                   </a>
               @endif
           </div>
       </div>
   </div>
</div>

    <!-- Loading Overlay -->
    <div wire:loading wire:target="confirmEnrollment"
        class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
        <div
            class="bg-themed-secondary rounded-2xl p-8 flex flex-col items-center shadow-2xl border border-themed-primary transition-colors duration-300">
            <div class="relative mb-4">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-themed-tertiary"></div>
                <div
                    class="animate-spin rounded-full h-16 w-16 border-4 border-accent-themed-primary border-t-transparent absolute top-0">
                </div>
            </div>
            <span class="text-themed-primary font-black text-xl transition-colors duration-300">Processing
                enrollment...</span>
        </div>
    </div>
    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush
</div>