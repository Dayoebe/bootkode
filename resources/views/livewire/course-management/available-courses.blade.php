
<div
    class="bg-gray-900 min-h-screen">

    <div class="container p-4 lg:p-8" x-data="{
        showFilters: false,
        viewMode: 'grid',
        selectedCourse: null,
        previewModal: false,
        enrollmentStats: {
            totalAvailable: {{ $totalAvailable }},
            totalEnrolled: {{ $totalEnrolled }},
            totalCompleted: {{ $totalCompleted }}
        }
    }"
        @enrollment-updated.window="enrollmentStats.totalEnrolled = $event.detail.totalEnrolled; enrollmentStats.totalCompleted = $event.detail.totalCompleted"
        @confetti.window="confetti({particleCount: 100, spread: 70, origin: { y: 0.6 }})">

        <!-- Modern Header with Stats Dashboard -->
        <div class="glass-effect rounded-3xl shadow-2xl p-8 mb-8 border border-white/20 dark:border-gray-700/30">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between mb-8">
                <div class="flex items-center mb-6 xl:mb-0">
                    <div class="relative mr-6">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-5 rounded-3xl shadow-xl">
                            <i class="fas fa-graduation-cap text-white text-3xl"></i>
                        </div>
                        <div
                            class="absolute -top-2 -right-2 bg-emerald-500 w-8 h-8 rounded-full flex items-center justify-center animate-pulse">
                            <i class="fas fa-sparkles text-white text-sm"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-5xl font-black gradient-text mb-2">
                            Course Explorer
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 text-lg font-medium">
                            Discover, learn, and grow with our curated collection
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button @click="viewMode = viewMode === 'grid' ? 'list' : 'grid'"
                        class="group bg-white/80 hover:bg-white dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-2xl font-bold border border-gray-200 dark:border-gray-600 transition-all duration-300 flex items-center justify-center transform hover:scale-105">
                        <i :class="viewMode === 'grid' ? 'fas fa-list' : 'fas fa-th-large'"
                            class="mr-2 group-hover:rotate-12 transition-transform duration-300"></i>
                        <span x-text="viewMode === 'grid' ? 'List View' : 'Grid View'"></span>
                    </button>

                    <button @click="showFilters = !showFilters"
                        class="group bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center transform hover:scale-105 shadow-xl">
                        <i class="fas fa-filter mr-3 group-hover:rotate-90 transition-transform duration-300"></i>
                        Filters & Search
                    </button>
                </div>
            </div>

            <!-- Learning Stats Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white/60 dark:bg-gray-800/60 rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 border border-white/30 dark:border-gray-700/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-black text-blue-600 dark:text-blue-400"
                                x-text="enrollmentStats.totalAvailable"></h3>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold">Courses Available</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-xl">
                            <i class="fas fa-book-open text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white/60 dark:bg-gray-800/60 rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 border border-white/30 dark:border-gray-700/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400"
                                x-text="enrollmentStats.totalEnrolled"></h3>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold">Currently Enrolled</p>
                        </div>
                        <div class="bg-emerald-100 dark:bg-emerald-900 p-4 rounded-xl">
                            <i class="fas fa-user-graduate text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white/60 dark:bg-gray-800/60 rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 border border-white/30 dark:border-gray-700/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-black text-purple-600 dark:text-purple-400"
                                x-text="enrollmentStats.totalCompleted"></h3>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold">Courses Completed</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900 p-4 rounded-xl">
                            <i class="fas fa-trophy text-purple-600 dark:text-purple-400 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filters Panel -->
        <div x-show="showFilters" x-transition:enter="transform transition-all duration-500 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transform transition-all duration-300 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-10"
            x-cloak class="glass-effect rounded-3xl shadow-2xl p-8 mb-8 border border-white/20 dark:border-gray-700/30">

            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-black text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-magic text-purple-500 mr-3"></i>
                    Smart Filters
                </h3>
                <button wire:click="resetFilters"
                    class="group bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 px-6 py-3 rounded-xl font-bold transition-all duration-300 flex items-center transform hover:scale-105">
                    <i class="fas fa-redo-alt mr-2 group-hover:rotate-180 transition-transform duration-500"></i>
                    Reset All
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                <!-- Enhanced Search -->
                <div class="col-span-full xl:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Search Courses</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search by title, description, instructor..."
                            class="w-full pl-12 pr-4 py-4 bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300">
                        <div class="absolute left-4 top-4 text-gray-400">
                            <i class="fas fa-search text-lg"></i>
                        </div>
                        <div wire:loading wire:target="search" class="absolute right-4 top-4">
                            <i class="fas fa-spinner animate-spin text-blue-500 text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select wire:model.live="categoryFilter"
                        class="w-full bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl px-4 py-4 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300">
                        <option value="">All Categories</option>
                        @forelse($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @empty
                            <option value="" disabled>No Categories</option>
                        @endforelse
                    </select>
                </div>

                <!-- Difficulty Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Difficulty</label>
                    <select wire:model.live="difficultyFilter"
                        class="w-full bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl px-4 py-4 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300">
                        <option value="">All Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                    <select wire:model.live="sortBy"
                        class="w-full bg-white/80 dark:bg-gray-800/80 border-2 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl px-4 py-4 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300">
                        <option value="latest">Latest First</option>
                        <option value="popular">Most Popular</option>
                        <option value="rating">Highest Rated</option>
                        <option value="title">Alphabetical</option>
                    </select>
                </div>

                <!-- Special Filters -->
                <div class="col-span-full xl:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">Special Filters</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model.live="showOnlyFree" class="sr-only">
                            <div class="relative">
                                <div class="w-6 h-6 bg-white border-2 border-gray-300 rounded-lg group-hover:border-green-400 transition-colors duration-200"
                                    :class="$wire.showOnlyFree ? 'bg-green-500 border-green-500' : ''">
                                    <i class="fas fa-check text-white text-xs absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                                        x-show="$wire.showOnlyFree"></i>
                                </div>
                            </div>
                            <span
                                class="ml-3 text-gray-700 dark:text-gray-300 font-medium group-hover:text-green-600 transition-colors duration-200">Free
                                Only</span>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model.live="showOnlyWithCertificate" class="sr-only">
                            <div class="relative">
                                <div class="w-6 h-6 bg-white border-2 border-gray-300 rounded-lg group-hover:border-purple-400 transition-colors duration-200"
                                    :class="$wire.showOnlyWithCertificate ? 'bg-purple-500 border-purple-500' : ''">
                                    <i class="fas fa-check text-white text-xs absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                                        x-show="$wire.showOnlyWithCertificate"></i>
                                </div>
                            </div>
                            <span
                                class="ml-3 text-gray-700 dark:text-gray-300 font-medium group-hover:text-purple-600 transition-colors duration-200">With
                                Certificate</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Grid/List -->
        <div :class="viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8' : 'space-y-6'"
            class="mb-8">

            @forelse($courses as $course)
                <div class="course-card-hover bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/30 dark:border-gray-700/30"
                    :class="viewMode === 'list' ? 'flex' : ''" x-data="{
                        isEnrolled: @js($this->isEnrolled($course->id)),
                        isWishlisted: @js($this->isWishlisted($course->id)),
                        progress: @js($this->getCourseProgress($course->id)),
                        isEnrolling: false,
                        isDropping: false
                    }"
                    style="animation-delay: {{ $loop->index * 0.1 }}s">

                    <!-- Course Image/Thumbnail -->
                    <div class="relative" :class="viewMode === 'list' ? 'w-64 flex-shrink-0' : 'h-64'">
                        @if ($course->thumbnail)
                            <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}"
                                class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500">
                                <i class="fas fa-graduation-cap text-white text-6xl opacity-80"></i>
                            </div>
                        @endif

                        <!-- Course Status Overlays -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            @if ($course->is_free)
                                <span
                                    class="bg-emerald-500/90 backdrop-blur-sm text-white text-xs font-black px-3 py-1 rounded-full">
                                    FREE
                                </span>
                            @else
                                <span
                                    class="bg-blue-500/90 backdrop-blur-sm text-white text-xs font-black px-3 py-1 rounded-full">
                                    ${{ number_format($course->price, 2) }}
                                </span>
                            @endif

                            @if ($course->certificate_template)
                                <span
                                    class="bg-purple-500/90 backdrop-blur-sm text-white text-xs font-black px-3 py-1 rounded-full flex items-center">
                                    <i class="fas fa-medal mr-1"></i> Certificate
                                </span>
                            @endif
                        </div>

                        <!-- Wishlist Button -->
                        <button @click="$wire.toggleWishlist({{ $course->id }}); isWishlisted = !isWishlisted"
                            class="absolute top-4 right-4 p-3 rounded-full transition-all duration-300 transform hover:scale-110"
                            :class="isWishlisted ? 'bg-red-500/90 text-white' : 'bg-white/90 text-gray-600 hover:text-red-500'"
                            title="Toggle Wishlist">
                            <i class="fas fa-heart text-lg"></i>
                        </button>

                        <!-- Progress Ring (for enrolled courses) -->
                        <div x-show="isEnrolled && progress > 0" class="absolute bottom-4 right-4 w-16 h-16">
                            <svg class="progress-ring w-16 h-16" viewBox="0 0 36 36">
                                <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="none"
                                    d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831" />
                                <path class="text-blue-500" stroke="currentColor" stroke-width="3" fill="none"
                                    :stroke-dasharray="`${progress}, 100`"
                                    d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xs font-bold text-white bg-blue-500/90 rounded-full px-1"
                                    x-text="`${progress}%`"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="p-8 flex-1">
                        <!-- Category & Duration -->
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-bold rounded-full">
                                {{ $course->category->name ?? 'Uncategorized' }}
                            </span>
                            <span class="text-gray-500 dark:text-gray-400 text-sm font-medium flex items-center">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $course->formatted_duration }}
                            </span>
                        </div>

                        <!-- Course Title -->
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4 line-clamp-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 cursor-pointer"
                            @click="selectedCourse = {{ $course->id }}; previewModal = true">
                            {{ $course->title }}
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3"
                            :class="viewMode === 'list' ? 'line-clamp-2' : 'line-clamp-3'">
                            {{ Str::limit($course->description, 150) }}
                        </p>

                        <!-- Instructor & Rating -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div
                                    class="bg-gray-200 dark:bg-gray-700 rounded-full w-10 h-10 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-tie text-gray-600 dark:text-gray-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $course->instructor->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Instructor</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="flex items-center mr-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star text-sm {{ ($course->average_rating ?? 0) >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                    {{ number_format($course->average_rating ?? 0, 1) }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                    ({{ $course->rating_count ?? 0 }})
                                </span>
                            </div>
                        </div>

                        <!-- Course Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center bg-gray-50 dark:bg-gray-700/50 rounded-xl py-3">
                                <div class="text-2xl font-black text-blue-600 dark:text-blue-400">
                                    {{ $course->total_enrollments }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Students</div>
                            </div>
                            <div class="text-center bg-gray-50 dark:bg-gray-700/50 rounded-xl py-3">
                                <div
                                    class="text-2xl font-black 
                            {{ $course->difficulty_level === 'beginner'
                                ? 'text-green-600 dark:text-green-400'
                                : ($course->difficulty_level === 'intermediate'
                                    ? 'text-yellow-600 dark:text-yellow-400'
                                    : 'text-red-600 dark:text-red-400') }}">
                                    {{ strtoupper(substr($course->difficulty_level, 0, 3)) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Level</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-3">
                            <!-- Primary Action Button -->
                            <template x-if="!isEnrolled">
                                <button @click="$wire.enroll({{ $course->id }}); isEnrolling = true"
                                    :disabled="isEnrolling || @js(in_array($course->id, $enrollingCourseIds))"
                                    class="w-full group bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-black py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center shadow-xl">
                                    <template x-if="!isEnrolling && !@js(in_array($course->id, $enrollingCourseIds))">
                                        <span class="flex items-center">
                                            <i
                                                class="fas fa-rocket mr-3 group-hover:rotate-12 transition-transform duration-300"></i>
                                            Enroll Now
                                        </span>
                                    </template>
                                    <template x-if="isEnrolling || @js(in_array($course->id, $enrollingCourseIds))">
                                        <span class="flex items-center">
                                            <i class="fas fa-spinner animate-spin mr-3"></i>
                                            Enrolling...
                                        </span>
                                    </template>
                                </button>
                            </template>

                            <template x-if="isEnrolled">
                                <div class="flex gap-3">
                                    <a :href="`/courses/{{ $course->slug }}`"
                                        class="flex-1 group bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-700 hover:to-blue-700 text-white font-black py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-xl">
                                        <i
                                            class="fas fa-play mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                                        Continue Learning
                                    </a>
                                    <button @click="$wire.dropCourse({{ $course->id }}); isDropping = true"
                                        :disabled="isDropping || @js(in_array($course->id, $droppingCourseIds))"
                                        class="group bg-red-100 hover:bg-red-200 text-red-700 font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                        <template x-if="!isDropping && !@js(in_array($course->id, $droppingCourseIds))">
                                            <i
                                                class="fas fa-sign-out-alt group-hover:rotate-12 transition-transform duration-300"></i>
                                        </template>
                                        <template x-if="isDropping || @js(in_array($course->id, $droppingCourseIds))">
                                            <i class="fas fa-spinner animate-spin"></i>
                                        </template>
                                    </button>
                                </div>
                            </template>

                            <!-- Secondary Actions -->
                            <div class="flex gap-3">
                                <button @click="selectedCourse = {{ $course->id }}; previewModal = true"
                                    class="flex-1 group bg-white/80 hover:bg-white dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 border border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                    <i
                                        class="fas fa-eye mr-2 group-hover:scale-110 transition-transform duration-300"></i>
                                    Preview
                                </button>

                                <button
                                    class="group bg-white/80 hover:bg-white dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 border border-gray-200 dark:border-gray-600">
                                    <i
                                        class="fas fa-share-alt group-hover:rotate-12 transition-transform duration-300"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full text-center py-20 bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-gray-700/30">
                    <div
                        class="bg-blue-100 dark:bg-blue-900 w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-8">
                        <i class="fas fa-search text-6xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">No courses found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto text-lg">
                        Try adjusting your search criteria or explore different categories.
                    </p>
                    <button @click="$wire.resetFilters(); showFilters = true"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-bold text-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-redo-alt mr-3"></i> Reset Filters
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Enhanced Pagination -->
        @if ($courses->hasPages())
            <div class="glass-effect rounded-3xl shadow-xl p-8 border border-white/20 dark:border-gray-700/30">
                {{ $courses->links() }}
            </div>
        @endif

        <!-- Enhanced Toast Notifications -->
        <div x-data="{
            show: false,
            message: '',
            type: 'success',
            icon: 'fas fa-check-circle',
            action: null
        }"
            @notify.window="
            show = true; 
            message = $event.detail.message; 
            type = $event.detail.type || 'success';
            icon = $event.detail.icon || 'fas fa-check-circle';
            action = $event.detail.action || null;
            setTimeout(() => show = false, action ? 8000 : 5000)
         "
            x-show="show" x-transition:enter="transform transition-all duration-300 ease-out"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition-all duration-300 ease-in"
            x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-8 right-8 z-50 max-w-md" x-cloak>

            <div
                class="glass-effect rounded-2xl shadow-2xl border border-white/30 dark:border-gray-700/30 overflow-hidden">
                <div :class="{
                    'bg-gradient-to-r from-emerald-500 to-green-500': type === 'success',
                    'bg-gradient-to-r from-red-500 to-pink-500': type === 'error',
                    'bg-gradient-to-r from-blue-500 to-purple-500': type === 'info',
                    'bg-gradient-to-r from-yellow-500 to-orange-500': type === 'warning'
                }"
                    class="p-6">

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i :class="icon" class="text-white text-xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-white font-bold text-lg leading-tight" x-text="message"></p>
                            <template x-if="action">
                                <a :href="action.url"
                                    class="inline-block mt-3 bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200"
                                    x-text="action.label"></a>
                            </template>
                        </div>
                        <button @click="show = false"
                            class="flex-shrink-0 ml-4 text-white hover:text-gray-200 transition-colors duration-200">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div
                class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-3xl p-12 flex flex-col items-center shadow-2xl border border-white/20 dark:border-gray-700/30">
                <div class="relative mb-6">
                    <div class="animate-spin rounded-full h-20 w-20 border-4 border-blue-200 dark:border-gray-600">
                    </div>
                    <div
                        class="animate-spin rounded-full h-20 w-20 border-4 border-blue-600 border-t-transparent absolute top-0">
                    </div>
                </div>
                <span class="text-gray-800 dark:text-white font-black text-xl">Loading amazing courses...</span>
            </div>
        </div>
    </div>


    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.0/dist/confetti.browser.min.js"></script>
@endpush
@push('Styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .course-card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .course-card-hover:hover {
            transform: translateY(-8px) scale(1.02);
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
@endpush

</div>
