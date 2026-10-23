<div class="min-h-screen bg-themed-primary transition-colors duration-300">
    <div class="px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8" x-data="{
             activeTab: @entangle('activeTab'),
             showEnrollModal: @entangle('showEnrollModal'),
             showReviewModal: @entangle('showReviewModal')
         }">

        <!-- Hero Section -->
        <div
            class="bg-themed-secondary rounded-lg sm:rounded-lg shadow-sm border border-themed-primary overflow-hidden mb-6 sm:mb-8 transition-colors duration-300">
            <div class="relative">
                <!-- Course Banner/Thumbnail -->
                <div
                    class="h-48 sm:h-64 lg:h-80 bg-surface-muted   relative overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                            class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                    @else
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i aria-hidden="true" class="fas fa-graduation-cap text-white text-6xl sm:text-8xl opacity-30"></i>
                        </div>
                    @endif

                    <!-- Overlay Content -->
                    <div class="absolute inset-0 flex items-end p-4 sm:p-6 lg:p-8">
                        <div class="text-white max-w-4xl">
                            <!-- Category Badge -->
                            <div class="mb-3 sm:mb-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20  text-white">
                                    <i aria-hidden="true" class="fas fa-tag mr-2"></i>
                                    {{ $course->category->name ?? 'Uncategorized' }}
                                </span>
                                @if($course->quality_label_text)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ml-2 {{ $course->quality_label_class }}">
                                        <i aria-hidden="true" class="fas fa-shield-check mr-2"></i>
                                        {{ $course->quality_label_text }}
                                    </span>
                                @endif
                            </div>

                            <!-- Course Title -->
                            <h1
                                class="    font-bold mb-2 sm:mb-4 leading-tight bk-page-title">
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
                                    <i aria-hidden="true" class="fas fa-user-tie mr-2"></i>
                                    <span>{{ $course->instructor->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i aria-hidden="true" class="fas fa-clock mr-2"></i>
                                    <span>{{ $this->formattedDuration }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i aria-hidden="true" class="fas fa-play-circle mr-2"></i>
                                    <span>{{ $totalLessons }} lessons</span>
                                </div>
                                <div class="flex items-center">
                                    <i aria-hidden="true" class="fas fa-users mr-2"></i>
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
            <div class="min-w-0 lg:col-span-2 space-y-6 sm:space-y-8 mb-3">

                <!-- Navigation Tabs -->
                <div
                    class="bg-themed-secondary rounded-lg sm:rounded-lg shadow-sm border border-themed-primary p-2 transition-colors duration-300">
                    <nav class="flex max-w-full gap-2 overflow-x-auto" aria-label="Course information">
                        <button @click="activeTab = 'overview'"
                            :class="activeTab === 'overview' ? 'bg-action text-action-foreground' : 'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary'"
                            class="min-h-11 shrink-0 flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i aria-hidden="true" class="fas fa-info-circle mr-2"></i>
                            Overview
                        </button>
                        <button @click="activeTab = 'curriculum'"
                            :class="activeTab === 'curriculum' ? 'bg-action text-action-foreground' : 'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary'"
                            class="min-h-11 shrink-0 flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i aria-hidden="true" class="fas fa-list mr-2"></i>
                            Curriculum
                        </button>
                        <button @click="activeTab = 'reviews'"
                            :class="activeTab === 'reviews' ? 'bg-action text-action-foreground' : 'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary'"
                            class="min-h-11 shrink-0 flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i aria-hidden="true" class="fas fa-star mr-2"></i>
                            Reviews ({{ $totalReviews }})
                        </button>
                        <button @click="activeTab = 'instructor'"
                            :class="activeTab === 'instructor' ? 'bg-action text-action-foreground' : 'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary'"
                            class="min-h-11 shrink-0 flex-1 py-2 sm:py-3 px-3 sm:px-4 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 flex items-center justify-center">
                            <i aria-hidden="true" class="fas fa-user-graduate mr-2"></i>
                            Instructor
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div
                    class="bg-themed-secondary rounded-lg sm:rounded-lg shadow-sm border border-themed-primary transition-colors duration-300">

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
                                    <i aria-hidden="true" class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                    What You'll Learn
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($course->learning_outcomes as $outcome)
                                        <div
                                            class="flex items-start bg-themed-tertiary p-3 rounded-lg border border-themed-primary transition-colors duration-300">
                                            <i aria-hidden="true" class="fas fa-check-circle text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
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
                                class="text-center bg-themed-tertiary p-4 rounded-lg border border-themed-primary transition-colors duration-300">
                                <div class="text-2xl font-bold text-brand mb-1">
                                    {{ number_format($totalLessons) }}
                                </div>
                                <div class="text-sm text-themed-secondary font-medium">Lessons</div>
                            </div>
                            <div
                                class="text-center bg-themed-tertiary p-4 rounded-lg border border-themed-primary transition-colors duration-300">
                                <div class="text-2xl font-bold text-brand mb-1">
                                    {{ $this->formattedDuration }}
                                </div>
                                <div class="text-sm text-themed-secondary font-medium">Duration</div>
                            </div>
                            <div
                                class="text-center bg-themed-tertiary p-4 rounded-lg border border-themed-primary transition-colors duration-300">
                                <div class="text-2xl font-bold text-brand mb-1">
                                    {{ number_format($totalEnrollments) }}
                                </div>
                                <div class="text-sm text-themed-secondary font-medium">Students</div>
                            </div>
                            <div
                                class="text-center bg-themed-tertiary p-4 rounded-lg border border-themed-primary transition-colors duration-300">
                                <div class="text-2xl font-bold text-brand mb-1">
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
                                        class="bg-orange-50 dark:bg-orange-900/20 p-4 sm:p-6 rounded-lg border border-orange-200 dark:border-orange-800 transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-orange-700 dark:text-orange-300 mb-4 flex items-center transition-colors duration-300">
                                            <i aria-hidden="true"
                                                class="fas fa-exclamation-circle text-orange-500 dark:text-orange-400 mr-2"></i>
                                            Prerequisites
                                        </h3>
                                        <ul class="space-y-2">
                                            @foreach($course->prerequisites as $prerequisite)
                                                @if(!empty(trim($prerequisite)))
                                                    <li
                                                        class="flex items-start text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                                        <i aria-hidden="true"
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
                                        class="bg-brand/10 dark:bg-surface/20 p-4 sm:p-6 rounded-lg border border-brand dark:border-brand transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-brand dark:text-brand mb-4 flex items-center transition-colors duration-300">
                                            <i aria-hidden="true" class="fas fa-box-open text-brand dark:text-brand mr-2"></i>
                                            Materials Included
                                        </h3>
                                        <ul class="space-y-2">
                                            @foreach($course->materials_included as $material)
                                                @if(!empty(trim($material)))
                                                    <li
                                                        class="flex items-start text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                                        <i aria-hidden="true"
                                                            class="fas fa-check-circle text-brand dark:text-brand mr-3 mt-1 flex-shrink-0"></i>
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
                                        class="bg-brand/10 dark:bg-surface/20 p-4 sm:p-6 rounded-lg border border-brand dark:border-brand transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-brand dark:text-brand mb-4 flex items-center transition-colors duration-300">
                                            <i aria-hidden="true" class="fas fa-users text-brand dark:text-brand mr-2"></i>
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
                                        class="bg-green-50 dark:bg-green-900/20 p-4 sm:p-6 rounded-lg border border-green-200 dark:border-green-800 transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-green-700 dark:text-green-300 mb-4 flex items-center transition-colors duration-300">
                                            <i aria-hidden="true" class="fas fa-book-open text-green-500 dark:text-green-400 mr-2"></i>
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
                                        class="bg-brand/10 dark:bg-surface/20 p-4 sm:p-6 rounded-lg border border-brand dark:border-brand transition-colors duration-300">
                                        <h3
                                            class="text-lg sm:text-xl font-bold text-brand dark:text-brand mb-4 flex items-center transition-colors duration-300">
                                            <i aria-hidden="true" class="fas fa-tags text-brand dark:text-brand mr-2"></i>
                                            Course Tags
                                        </h3>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($course->tags as $tag)
                                                @if(!empty(trim($tag)))
                                                    <span
                                                        class="px-3 py-1 bg-brand/10 dark:bg-surface/50 text-brand dark:text-brand rounded-full text-sm font-medium transition-colors duration-300">
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
                                    <i aria-hidden="true" class="fas fa-question-circle text-brand dark:text-brand mr-2"></i>
                                    Frequently Asked Questions
                                </h3>
                                <div class="space-y-4">
                                    @foreach($course->faqs as $index => $faq)
                                        @if(!empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? '')))
                                            <div class="bg-themed-tertiary border border-themed-primary rounded-lg overflow-hidden transition-colors duration-300"
                                                x-data="{ expanded: false }">
                                                <button @click="expanded = !expanded"
                                                    class="w-full px-4 sm:px-6 py-4 text-left flex items-center justify-between hover:bg-themed-primary transition-colors duration-300">
                                                    <span
                                                        class="font-semibold text-themed-primary text-sm sm:text-base transition-colors duration-300">
                                                        {{ $faq['question'] }}
                                                    </span>
                                                    <i aria-hidden="true" class="fas fa-chevron-down text-themed-secondary transform transition-transform duration-200"
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
                            <div class="mb-6 border border-themed-primary rounded-lg overflow-hidden transition-colors duration-300"
                                x-data="{ expanded: {{ $sectionIndex === 0 ? 'true' : 'false' }} }">
                                <div class="bg-themed-tertiary p-4 sm:p-5 cursor-pointer transition-colors duration-300"
                                    @click="expanded = !expanded">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div
                                                class="bg-action text-action-foreground rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">
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
                                        <i aria-hidden="true" class="fas fa-chevron-down text-themed-secondary transform transition-transform duration-200"
                                            :class="{ 'rotate-180': expanded }"></i>
                                    </div>
                                </div>

                                <div x-show="expanded" x-transition class="border-t border-themed-primary">
                                    @forelse($section->lessons as $lesson)
                                        <div
                                            class="flex items-center justify-between p-4 sm:p-5 border-b border-themed-primary last:border-b-0 hover:bg-themed-tertiary transition-colors duration-200">
                                            <div class="flex items-center">
                                                <i aria-hidden="true" class="fas fa-play-circle text-brand mr-3"></i>
                                                <div>
                                                    <h4
                                                        class="font-medium text-themed-primary text-sm sm:text-base transition-colors duration-300">
                                                        {{ $lesson->title }}
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="flex items-center text-sm text-themed-secondary">
                                                @if($lesson->duration_minutes)
                                                    <i aria-hidden="true" class="fas fa-clock mr-1"></i>
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
                                <i aria-hidden="true" class="fas fa-book-open text-4xl text-themed-tertiary mb-4"></i>
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
                                                <i aria-hidden="true" class="fas fa-user text-themed-secondary"></i>
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
                                <i aria-hidden="true" class="fas fa-star text-4xl text-themed-tertiary mb-4"></i>
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
                            class="flex flex-col sm:flex-row sm:items-start space-y-4 sm:space-y-0 sm:space-x-6 bg-themed-tertiary p-6 rounded-lg border border-themed-primary">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-20 h-20 sm:w-24 sm:h-24 bg-surface-muted   rounded-full flex items-center justify-center">
                                    <i aria-hidden="true" class="fas fa-user-tie text-brand text-2xl sm:text-3xl"></i>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-xl sm:text-2xl font-bold text-themed-primary mb-2 transition-colors duration-300">
                                    {{ $course->instructor->name }}
                                </h3>
                                <p class="text-brand font-semibold mb-4">
                                    {{ $course->instructor->email }}
                                </p>

                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div class="bg-themed-secondary p-3 rounded-lg border border-themed-primary">
                                        <div class="text-xl font-bold text-brand">
                                            {{ $course->instructor->courses()->count() }}
                                        </div>
                                        <div class="text-sm text-themed-secondary">Courses</div>
                                    </div>
                                    <div class="bg-themed-secondary p-3 rounded-lg border border-themed-primary">
                                        <div class="text-xl font-bold text-brand">
                                            {{ $course->instructor->courses()->withCount('enrollments')->get()->sum('enrollments_count') }}
                                        </div>
                                        <div class="text-sm text-themed-secondary">Students</div>
                                    </div>
                                    <div class="bg-themed-secondary p-3 rounded-lg border border-themed-primary">
                                        <div class="text-xl font-bold text-brand">
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
                    class="bg-themed-secondary rounded-lg sm:rounded-lg shadow-sm border border-themed-primary p-6 sticky top-6 transition-colors duration-300">

                    <!-- Price Section -->
                    <div class="text-center mb-6">
                        @if($course->is_free)
                            <div class="text-3xl sm:text-4xl font-bold text-green-500 mb-2">FREE</div>
                            <p class="text-themed-secondary text-sm">Full access at no cost</p>
                        @else
                            <div class="text-3xl sm:text-4xl font-bold text-brand mb-2">
                                ₦{{ number_format($course->price, 2) }}
                            </div>
                            <p class="text-themed-secondary text-sm">One-time payment</p>
                        @endif
                    </div>

                    <!-- Action Button -->
                    @if(!$isEnrolled)
                        <button wire:click="openEnrollmentModal" wire:loading.attr="disabled"
                            class="w-full bg-action hover:bg-action/90 text-action-foreground font-bold py-3 px-6 rounded-lg transition-all duration-300 transform  disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-sm mb-4">
                            <span wire:loading.remove wire:target="openEnrollmentModal" class="flex items-center gap-2">
                                <i aria-hidden="true" class="fas fa-rocket"></i>
                                Enroll Now
                            </span>
                            <span wire:loading wire:target="openEnrollmentModal" class="flex items-center gap-2">
                                <i aria-hidden="true" class="fas fa-spinner animate-spin"></i>
                                Processing...
                            </span>
                        </button>
                    @else
                        <div class="flex gap-2 mb-4">
                            <a href="{{ route('course.view', $course->slug) }}"
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform  flex items-center justify-center shadow-sm">
                                <i aria-hidden="true" class="fas fa-play mr-2"></i>
                                Continue Learning
                            </a>
                            <button wire:click="dropCourse({{ $course->id }})" wire:loading.attr="disabled"
                                class="bg-themed-tertiary hover:bg-red-100 text-red-700 font-bold py-3 px-6 rounded-lg transition-all duration-300 transform  disabled:opacity-50 border border-themed-primary">
                                <i aria-hidden="true" class="fas fa-sign-out-alt"></i>
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
                                    <i aria-hidden="true" class="fas fa-signal mr-2"></i>
                                    Difficulty
                                </span>
                                <span
                                    class="font-semibold text-themed-primary capitalize transition-colors duration-300">{{ $course->difficulty_level }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-themed-secondary flex items-center transition-colors duration-300">
                                    <i aria-hidden="true" class="fas fa-clock mr-2"></i>
                                    Duration
                                </span>
                                <span
                                    class="font-semibold text-themed-primary transition-colors duration-300">{{ $this->formattedDuration }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-themed-secondary flex items-center transition-colors duration-300">
                                    <i aria-hidden="true" class="fas fa-play-circle mr-2"></i>
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
                                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform  shadow-sm flex items-center justify-center">
                                            <i aria-hidden="true" class="fas fa-play-circle mr-2"></i>
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
                                    class="flex-1 bg-themed-tertiary hover:bg-themed-primary border border-themed-primary text-themed-secondary hover:text-red-500 font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                                    <i aria-hidden="true" class="fas {{ $isFavorited ? 'fa-heart text-red-500' : 'fa-heart' }} mr-2"></i>
                                    {{ $isFavorited ? 'Favorited' : 'Favorite' }}
                                </button>

                                <!-- Share Button -->
                                <button @click="$dispatch('open-modal', 'course-share')"
                                    class="flex-1 bg-themed-tertiary hover:bg-themed-primary border border-themed-primary text-themed-secondary hover:text-brand font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                                    <i aria-hidden="true" class="fas fa-share-alt mr-2"></i>
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
                class="bg-themed-secondary rounded-lg sm:rounded-lg shadow-sm border border-themed-primary overflow-hidden  transition-colors duration-300">
                <div :class="{
                    'bg-brand-ink  ': type === 'success',
                    'bg-brand-ink  ': type === 'error',
                    'bg-brand-ink  ': type === 'info'
                }" class="p-4 sm:p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i aria-hidden="true" :class="type === 'success' ? 'fas fa-check-circle' : (type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle')"
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
        <div wire:loading class="fixed inset-0 bg-black/30  flex items-center justify-center z-50">
            <div
                class="bg-themed-secondary rounded-lg sm:rounded-lg p-8 sm:p-12 flex flex-col items-center shadow-sm border border-themed-primary mx-4 transition-colors duration-300">
                <div class="relative mb-4 sm:mb-6">
                    <div class="animate-spin rounded-full h-16 w-16 sm:h-20 sm:w-20 border-4 border-themed-tertiary">
                    </div>
                    <div
                        class="animate-spin rounded-full h-16 w-16 sm:h-20 sm:w-20 border-4 border-brand border-t-transparent absolute top-0">
                    </div>
                </div>
                <span
                    class="text-themed-primary font-bold text-lg sm:text-xl text-center transition-colors duration-300">Loading...</span>
            </div>
        </div>
    </div>
    <!-- Share Modal -->
    <x-modal name="course-share" maxWidth="md" label="Share this course">
        <div
            class="bg-themed-secondary rounded-lg sm:rounded-lg p-6 sm:p-8 max-w-md w-full shadow-sm border border-themed-primary transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Share This Course</h3>
                <button aria-label="Close share dialog" @click="$dispatch('close-modal', 'course-share')"
                    class="text-themed-secondary hover:text-themed-primary transition-colors duration-300">
                    <i aria-hidden="true" class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Share Options -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <button wire:click="shareCourse('twitter')"
                    class="bg-blue-400 hover:bg-action text-action-foreground p-4 rounded-lg transition-all duration-300 transform  flex flex-col items-center">
                    <i aria-hidden="true" class="fab fa-twitter text-xl mb-2"></i>
                    <span class="text-xs font-semibold">Twitter</span>
                </button>

                <button wire:click="shareCourse('facebook')"
                    class="bg-action hover:bg-action text-action-foreground p-4 rounded-lg transition-all duration-300 transform  flex flex-col items-center">
                    <i aria-hidden="true" class="fab fa-facebook text-xl mb-2"></i>
                    <span class="text-xs font-semibold">Facebook</span>
                </button>

                <button wire:click="shareCourse('linkedin')"
                    class="bg-action hover:bg-action text-action-foreground p-4 rounded-lg transition-all duration-300 transform  flex flex-col items-center">
                    <i aria-hidden="true" class="fab fa-linkedin text-xl mb-2"></i>
                    <span class="text-xs font-semibold">LinkedIn</span>
                </button>

                <button wire:click="shareCourse('whatsapp')"
                    class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg transition-all duration-300 transform  flex flex-col items-center">
                    <i aria-hidden="true" class="fab fa-whatsapp text-xl mb-2"></i>
                    <span class="text-xs font-semibold">WhatsApp</span>
                </button>
            </div>

            <!-- Copy Link -->
            <div class="flex space-x-2">
                <input type="text" value="{{ $shareUrl }}" readonly
                    class="min-w-0 flex-1 bg-themed-tertiary border border-themed-primary rounded-lg px-4 py-3 text-themed-primary text-sm transition-colors duration-300">
                <button onclick="navigator.clipboard.writeText('{{ $shareUrl }}')" @click="$dispatch('close-modal', 'course-share')"
                    aria-label="Copy course link" class="bk-button bk-button-primary">
                    <i aria-hidden="true" class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </x-modal>









<!-- Payment Confirmation Modal -->
<div x-show="$wire.showPaymentModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50  flex items-center justify-center p-4 z-50"
     style="display: none;" x-trap.inert.noscroll="$wire.showPaymentModal" role="dialog" aria-modal="true" aria-label="Confirm Enrollment" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.showPaymentModal = false">

   <div @click.away="$wire.closePaymentModal()"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-themed-secondary rounded-lg shadow-sm w-full max-w-lg border border-themed-primary transition-colors duration-300">

       <!-- Modal Header -->
       <div class="p-6 border-b border-themed-primary">
           <div class="flex items-center justify-between">
               <h3 class="text-2xl font-bold text-themed-primary flex items-center gap-3">
                   <div class="bg-brand/10 p-3 rounded-lg">
                       <i aria-hidden="true" class="fas fa-wallet text-brand"></i>
                   </div>
                   Confirm Enrollment
               </h3>
               <button wire:click="closePaymentModal"
                       class="text-themed-secondary hover:text-themed-primary p-2 rounded-lg hover:bg-themed-tertiary transition-all duration-300" aria-label="Close">
                   <i aria-hidden="true" class="fas fa-times text-xl"></i>
               </button>
           </div>
       </div>

       <!-- Modal Body -->
       <div class="p-6 space-y-6">
           <!-- Course Info -->
           <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary">
               <div class="flex items-start gap-4">
                   @if($course && $course->thumbnail)
                       <img src="{{ asset('storage/' . $course->thumbnail) }}"
                            alt="{{ $course->title ?? 'Course' }}"
                            class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                   @else
                       <div class="w-20 h-20 rounded-lg bg-surface-muted   flex items-center justify-center flex-shrink-0">
                           <i aria-hidden="true" class="fas fa-graduation-cap text-brand text-2xl"></i>
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
               <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg border border-themed-primary">
                   <div class="flex items-center gap-3">
                       <div class="bg-action/10 p-2 rounded-lg">
                           <i aria-hidden="true" class="fas fa-wallet text-brand"></i>
                       </div>
                       <span class="text-themed-secondary font-medium">Current Balance</span>
                   </div>
                   <span class="font-bold text-themed-primary text-lg">
                       ₦{{ number_format($walletBalance ?? 0, 2) }}
                   </span>
               </div>

               <!-- Course Price -->
               <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg border border-themed-primary">
                   <div class="flex items-center gap-3">
                       <div class="bg-orange-500/10 p-2 rounded-lg">
                           <i aria-hidden="true" class="fas fa-tag text-orange-500"></i>
                       </div>
                       <span class="text-themed-secondary font-medium">Course Price</span>
                   </div>
                   <span class="font-bold text-themed-primary text-lg">
                       - ₦{{ number_format($coursePrice ?? 0, 2) }}
                   </span>
               </div>

               <!-- Balance After Payment -->
               <div class="flex items-center justify-between p-4 rounded-lg border-2 transition-colors duration-300
                   {{ ($hasSufficientFunds ?? false) ? 'bg-green-50 border-green-300 dark:bg-green-900/20 dark:border-green-700' : 'bg-red-50 border-red-300 dark:bg-red-900/20 dark:border-red-700' }}">
                   <div class="flex items-center gap-3">
                       <div class="{{ ($hasSufficientFunds ?? false) ? 'bg-green-500/10' : 'bg-red-500/10' }} p-2 rounded-lg">
                           <i aria-hidden="true" class="fas {{ ($hasSufficientFunds ?? false) ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500' }}"></i>
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
                   <div class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded-lg p-4">
                       <div class="flex gap-3">
                           <i aria-hidden="true" class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl flex-shrink-0"></i>
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
                           class="flex-1 bg-themed-secondary hover:bg-themed-primary text-themed-primary font-semibold py-3 px-6 rounded-lg transition-all duration-300 border border-themed-primary transform ">
                       Cancel
                   </button>
                   <button wire:click="confirmEnrollment"
                           wire:loading.attr="disabled"
                           class="flex-1 bg-action hover:bg-action/90 text-action-foreground font-bold py-3 px-6 rounded-lg transition-all duration-300 disabled:opacity-50 transform  shadow-sm">
                       <span wire:loading.remove wire:target="confirmEnrollment" class="flex items-center justify-center gap-2">
                           <i aria-hidden="true" class="fas fa-check-circle"></i>
                           Confirm & Enroll
                       </span>
                       <span wire:loading wire:target="confirmEnrollment" class="flex items-center justify-center gap-2">
                           <i aria-hidden="true" class="fas fa-spinner animate-spin"></i>
                           Processing...
                       </span>
                   </button>
               @else
                   <button wire:click="closePaymentModal"
                           class="flex-1 bg-themed-secondary hover:bg-themed-primary text-themed-primary font-semibold py-3 px-6 rounded-lg transition-all duration-300 border border-themed-primary transform ">
                       Cancel
                   </button>
                   <a href="{{ route('wallet.index') }}"
                      class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform  shadow-sm flex items-center justify-center gap-2">
                       <i aria-hidden="true" class="fas fa-plus-circle"></i>
                       Fund Wallet
                   </a>
               @endif
           </div>
       </div>
   </div>
</div>

    <!-- Loading Overlay -->
    <div wire:loading wire:target="confirmEnrollment"
        class="fixed inset-0 bg-black/30  flex items-center justify-center z-50">
        <div
            class="bg-themed-secondary rounded-lg p-8 flex flex-col items-center shadow-sm border border-themed-primary transition-colors duration-300">
            <div class="relative mb-4">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-themed-tertiary"></div>
                <div
                    class="animate-spin rounded-full h-16 w-16 border-4 border-brand border-t-transparent absolute top-0">
                </div>
            </div>
            <span class="text-themed-primary font-bold text-xl transition-colors duration-300">Processing
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
