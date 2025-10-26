<div class="bg-themed-primary min-h-screen transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{
        showFilters: false,
        viewMode: 'grid',
        enrollmentStats: {
            totalAvailable: {{ $totalAvailable }},
            totalEnrolled: {{ $totalEnrolled }},
            totalCompleted: {{ $totalCompleted }}
        }
    }" @enrollment-updated.window="enrollmentStats.totalEnrolled = $event.detail.totalEnrolled; enrollmentStats.totalCompleted = $event.detail.totalCompleted">

        <!-- Header with Stats Dashboard -->
        <div class="bg-themed-secondary rounded-2xl shadow-xl border border-themed-primary p-6 animate__animated animate__fadeInDown transition-colors duration-300">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6">
                <div class="flex items-center gap-4 mb-6 lg:mb-0">
                    <div class="bg-gradient-to-r from-accent-themed-primary to-purple-600 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-graduation-cap text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-black text-themed-primary transition-colors duration-300">
                            Course Explorer
                        </h1>
                        <p class="text-themed-secondary text-sm sm:text-base font-medium transition-colors duration-300">
                            Discover, learn, and grow with our curated collection
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="viewMode = viewMode === 'grid' ? 'list' : 'grid'"
                        class="inline-flex items-center justify-center gap-2 bg-themed-secondary hover:bg-themed-tertiary text-themed-primary px-6 py-3 rounded-xl font-bold border border-themed-primary transition-all duration-300 transform hover:scale-105">
                        <i :class="viewMode === 'grid' ? 'fas fa-list' : 'fas fa-th-large'"></i>
                        <span x-text="viewMode === 'grid' ? 'List View' : 'Grid View'"></span>
                    </button>

                    <button @click="showFilters = !showFilters"
                        class="inline-flex items-center justify-center gap-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-filter"></i>
                        <span>Filters</span>
                    </button>
                </div>
            </div>

            <!-- Learning Stats Dashboard -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-themed-tertiary rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-black text-accent-themed-primary transition-colors duration-300"
                                x-text="enrollmentStats.totalAvailable"></h3>
                            <p class="text-themed-secondary font-semibold text-sm transition-colors duration-300">
                                Courses Available</p>
                        </div>
                        <div class="bg-themed-secondary p-4 rounded-xl transition-colors duration-300 border border-themed-primary">
                            <i class="fas fa-book-open text-accent-themed-primary text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-themed-tertiary rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-black text-green-500 transition-colors duration-300"
                                x-text="enrollmentStats.totalEnrolled"></h3>
                            <p class="text-themed-secondary font-semibold text-sm transition-colors duration-300">
                                Currently Enrolled</p>
                        </div>
                        <div class="bg-themed-secondary p-4 rounded-xl transition-colors duration-300 border border-themed-primary">
                            <i class="fas fa-user-graduate text-green-500 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-themed-tertiary rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-black text-purple-500 transition-colors duration-300"
                                x-text="enrollmentStats.totalCompleted"></h3>
                            <p class="text-themed-secondary font-semibold text-sm transition-colors duration-300">
                                Courses Completed</p>
                        </div>
                        <div class="bg-themed-secondary p-4 rounded-xl transition-colors duration-300 border border-themed-primary">
                            <i class="fas fa-trophy text-purple-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filters Panel -->
        <div x-show="showFilters" 
             x-transition:enter="transform transition-all duration-500 ease-out"
             x-transition:enter-start="opacity-0 -translate-y-10" 
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transform transition-all duration-300 ease-in"
             x-transition:leave-start="opacity-100 translate-y-0" 
             x-transition:leave-end="opacity-0 -translate-y-10"
             class="bg-themed-secondary rounded-2xl shadow-xl border border-themed-primary p-6 transition-colors duration-300"
             style="display: none;">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                <h3 class="text-xl font-black text-themed-primary flex items-center gap-2 mb-4 sm:mb-0 transition-colors duration-300">
                    <i class="fas fa-magic text-accent-themed-primary"></i>
                    Smart Filters
                </h3>
                <button wire:click="resetFilters"
                    class="inline-flex items-center gap-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary px-6 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105 border border-themed-primary">
                    <i class="fas fa-redo-alt"></i>
                    <span>Reset All</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <!-- Enhanced Search -->
                <div class="col-span-full xl:col-span-2">
                    <label class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">Search Courses</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search by title, description, instructor..."
                            class="w-full pl-10 pr-4 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-themed-secondary transition-colors duration-300">
                            <i class="fas fa-search"></i>
                        </div>
                        <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                            <i class="fas fa-spinner animate-spin text-accent-themed-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">Category</label>
                    <select wire:model.live="categoryFilter"
                        class="w-full bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
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
                    <label class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">Difficulty</label>
                    <select wire:model.live="difficultyFilter"
                        class="w-full bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                        <option value="">All Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div>
                    <label class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">Sort By</label>
                    <select wire:model.live="sortBy"
                        class="w-full bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                        <option value="latest">Latest First</option>
                        <option value="popular">Most Popular</option>
                        <option value="rating">Highest Rated</option>
                        <option value="title">Alphabetical</option>
                    </select>
                </div>

                <!-- Special Filters -->
                <div class="col-span-full xl:col-span-2">
                    <label class="block text-sm font-bold text-themed-primary mb-3 transition-colors duration-300">Special Filters</label>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model.live="showOnlyFree" class="sr-only">
                            <div class="relative">
                                <div class="w-6 h-6 bg-themed-tertiary border-2 border-themed-primary rounded-lg group-hover:border-green-400 transition-colors duration-200"
                                    :class="$wire.showOnlyFree ? 'bg-green-500 border-green-500' : ''">
                                    <i class="fas fa-check text-white text-xs absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                                        x-show="$wire.showOnlyFree"></i>
                                </div>
                            </div>
                            <span class="ml-3 text-themed-primary font-medium group-hover:text-green-600 transition-colors duration-200">Free Only</span>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model.live="showOnlyWithCertificate" class="sr-only">
                            <div class="relative">
                                <div class="w-6 h-6 bg-themed-tertiary border-2 border-themed-primary rounded-lg group-hover:border-purple-400 transition-colors duration-200"
                                    :class="$wire.showOnlyWithCertificate ? 'bg-purple-500 border-purple-500' : ''">
                                    <i class="fas fa-check text-white text-xs absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                                        x-show="$wire.showOnlyWithCertificate"></i>
                                </div>
                            </div>
                            <span class="ml-3 text-themed-primary font-medium group-hover:text-purple-600 transition-colors duration-200">With Certificate</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Grid/List -->
        <div :class="viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6' : 'space-y-6'">
            @forelse($courses as $course)
                <div class="bg-themed-secondary rounded-xl shadow-lg overflow-hidden border border-themed-primary transition-all duration-300 hover:shadow-xl hover:-translate-y-1 animate__animated animate__fadeInUp"
                    :class="viewMode === 'list' ? 'flex flex-col sm:flex-row' : ''"
                    x-data="{
                        isEnrolled: @js($this->isEnrolled($course->id)),
                        isWishlisted: @js($this->isWishlisted($course->id)),
                        progress: @js($this->getCourseProgress($course->id)),
                        isEnrolling: false,
                        isDropping: false
                    }"
                    style="animation-delay: {{ $loop->index * 0.05 }}s">

                    <!-- Course Image/Thumbnail -->
                    <div class="relative" :class="viewMode === 'list' ? 'w-full sm:w-48 flex-shrink-0' : 'h-44'">
                        @if ($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-accent-themed-primary via-purple-600 to-pink-500">
                                <i class="fas fa-graduation-cap text-white text-4xl opacity-80"></i>
                            </div>
                        @endif

                        <!-- Course Status Overlays -->
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            @if ($course->is_free)
                                <span class="bg-green-500/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full">
                                    FREE
                                </span>
                            @else
                                <span class="bg-accent-themed-primary/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full">
                                    ${{ number_format($course->price, 2) }}
                                </span>
                            @endif

                            @if ($course->certificate_template)
                                <span class="bg-purple-500/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full flex items-center">
                                    <i class="fas fa-medal mr-1"></i> Cert
                                </span>
                            @endif
                        </div>

                        <!-- Wishlist Button -->
                        <button @click="$wire.toggleWishlist({{ $course->id }}); isWishlisted = !isWishlisted"
                            class="absolute top-3 right-3 p-2 rounded-full transition-all duration-300 transform hover:scale-110"
                            :class="isWishlisted ? 'bg-red-500/90 text-white' : 'bg-themed-secondary/90 text-themed-primary hover:text-red-500 border border-themed-primary'"
                            title="Toggle Wishlist">
                            <i class="fas fa-heart"></i>
                        </button>

                        <!-- Progress Ring -->
                        <div x-show="isEnrolled && progress > 0" class="absolute bottom-3 right-3 w-10 h-10">
                            <svg class="progress-ring w-10 h-10 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-themed-tertiary" stroke="currentColor" stroke-width="3" fill="none"
                                    d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831" />
                                <path class="text-accent-themed-primary" stroke="currentColor" stroke-width="3" fill="none"
                                    :stroke-dasharray="`${progress}, 100`"
                                    d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-[10px] font-bold text-white bg-accent-themed-primary/90 rounded-full px-0.5"
                                    x-text="`${progress}%`"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="p-5 flex-1">
                        <!-- Category & Duration -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-1 bg-accent-themed-primary/20 text-accent-themed-primary text-xs font-bold rounded-full transition-colors duration-300">
                                {{ Str::limit($course->category->name ?? 'Uncategorized', 12) }}
                            </span>
                            <span class="text-themed-secondary text-xs font-medium flex items-center transition-colors duration-300">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $course->estimated_duration_minutes ? intval($course->estimated_duration_minutes / 60) . 'h ' . ($course->estimated_duration_minutes % 60) . 'm' : 'Self-paced' }}
                            </span>
                        </div>

                        <!-- Course Title -->
                        <h3 class="text-base font-bold text-themed-primary mb-2 line-clamp-2 hover:text-accent-themed-primary transition-colors duration-300 cursor-pointer">
                            {{ Str::limit($course->title, 60) }}
                        </h3>

                        <!-- Description -->
                        <p class="text-themed-secondary mb-3 text-sm transition-colors duration-300 line-clamp-2">
                            {{ Str::limit($course->description, 80) }}
                        </p>

                        <!-- Rating -->
                        @if($course->average_rating > 0)
                            <div class="flex items-center gap-2 mb-3">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $course->average_rating >= $i ? 'text-yellow-400' : 'text-themed-tertiary' }}"></i>
                                    @endfor
                                </div>
                                <span class="font-semibold text-themed-primary text-sm">{{ number_format($course->average_rating, 1) }}</span>
                                <span class="text-themed-secondary text-xs">({{ $course->rating_count }})</span>
                            </div>
                        @endif

                        <!-- Instructor & Stats -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-themed-tertiary rounded-full w-7 h-7 flex items-center justify-center mr-2 transition-colors duration-300 border border-themed-primary">
                                    <i class="fas fa-user-tie text-themed-secondary text-xs"></i>
                                </div>
                                <p class="text-xs font-bold text-themed-primary transition-colors duration-300">
                                    {{ Str::limit($course->instructor->name, 15) }}
                                </p>
                            </div>
                            <div class="text-xs text-themed-secondary transition-colors duration-300">
                                {{ $course->total_enrollments }} students
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2">
                            <!-- Primary Action -->
                            <template x-if="!isEnrolled">
                                <button @click="$wire.enroll({{ $course->id }}); isEnrolling = true"
                                    :disabled="isEnrolling || @js(in_array($course->id, $enrollingCourseIds))"
                                    class="w-full bg-accent-themed-primary hover:bg-accent-themed-secondary text-white font-bold py-2.5 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-md">
                                    <template x-if="!isEnrolling && !@js(in_array($course->id, $enrollingCourseIds))">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-rocket"></i>
                                            Enroll Now
                                        </span>
                                    </template>
                                    <template x-if="isEnrolling || @js(in_array($course->id, $enrollingCourseIds))">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-spinner animate-spin"></i>
                                            Enrolling...
                                        </span>
                                    </template>
                                </button>
                            </template>

                            <template x-if="isEnrolled">
                                <div class="flex gap-2">
                                    <a href="{{ route('course.view', $course->slug) }}"
                                        class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-md">
                                        <i class="fas fa-play mr-2"></i>
                                        Continue
                                    </a>
                                    <button @click="$wire.dropCourse({{ $course->id }}); isDropping = true"
                                        :disabled="isDropping || @js(in_array($course->id, $droppingCourseIds))"
                                        class="bg-themed-tertiary hover:bg-red-100 text-red-700 font-bold py-2.5 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 border border-themed-primary">
                                        <template x-if="!isDropping && !@js(in_array($course->id, $droppingCourseIds))">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </template>
                                        <template x-if="isDropping || @js(in_array($course->id, $droppingCourseIds))">
                                            <i class="fas fa-spinner animate-spin"></i>
                                        </template>
                                    </button>
                                </div>
                            </template>

                            <!-- Secondary Actions -->
                            <a href="{{ route('courses.preview', $course) }}"
                                class="w-full bg-themed-tertiary hover:bg-themed-secondary text-themed-primary font-bold py-2 px-3 rounded-xl transition-all duration-300 transform hover:scale-105 border border-themed-primary flex items-center justify-center">
                                <i class="fas fa-eye mr-2"></i>
                                Preview
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 bg-themed-secondary rounded-2xl shadow-xl border border-themed-primary transition-colors duration-300">
                    <div class="bg-accent-themed-primary/10 w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-8 transition-colors duration-300">
                        <i class="fas fa-search text-6xl text-accent-themed-primary"></i>
                    </div>
                    <h3 class="text-3xl font-black text-themed-primary mb-4 transition-colors duration-300">
                        No courses found</h3>
                    <p class="text-themed-secondary mb-8 max-w-md mx-auto text-lg transition-colors duration-300">
                        Try adjusting your search criteria or explore different categories.
                    </p>
                    <button @click="$wire.resetFilters(); showFilters = true"
                        class="inline-flex items-center gap-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-8 py-4 rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-redo-alt"></i> Reset Filters
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Enhanced Pagination -->
        @if ($courses->hasPages())
            <div class="bg-themed-secondary rounded-2xl shadow-xl border border-themed-primary p-6 transition-colors duration-300">
                {{ $courses->links('pagination::tailwind') }}
            </div>
        @endif

        <!-- Enhanced Toast Notifications -->
        <div x-data="{
            show: false,
            message: '',
            type: 'success',
            icon: 'fas fa-check-circle',
            action: null
        }" @notify.window="
            show = true; 
            message = $event.detail.message; 
            type = $event.detail.type || 'success';
            icon = $event.detail.icon || 'fas fa-check-circle';
            action = $event.detail.action || null;
            setTimeout(() => show = false, action ? 8000 : 5000)
         " x-show="show" 
            x-transition:enter="transform transition-all duration-300 ease-out"
            x-transition:enter-start="translate-x-full opacity-0" 
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition-all duration-300 ease-in"
            x-transition:leave-start="translate-x-0 opacity-100" 
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-8 right-8 z-50 max-w-md" 
            style="display: none;">

            <div class="bg-themed-secondary rounded-xl shadow-2xl border border-themed-primary overflow-hidden backdrop-blur-sm transition-colors duration-300">
                <div :class="{
                    'bg-gradient-to-r from-emerald-500 to-green-500': type === 'success',
                    'bg-gradient-to-r from-red-500 to-pink-500': type === 'error',
                    'bg-gradient-to-r from-accent-themed-primary to-purple-500': type === 'info',
                    'bg-gradient-to-r from-yellow-500 to-orange-500': type === 'warning'
                }" class="p-6">

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
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-themed-secondary rounded-2xl p-12 flex flex-col items-center shadow-2xl border border-themed-primary transition-colors duration-300">
                <div class="relative mb-6">
                    <div class="animate-spin rounded-full h-20 w-20 border-4 border-themed-tertiary"></div>
                    <div class="animate-spin rounded-full h-20 w-20 border-4 border-accent-themed-primary border-t-transparent absolute top-0"></div>
                </div>
                <span class="text-themed-primary font-black text-xl transition-colors duration-300">Loading amazing courses...</span>
            </div>
        </div>
    </div>
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .progress-ring {
        transition: stroke-dasharray 0.35s;
        transform-origin: 50% 50%;
    }
    
    [x-cloak] {
        display: none !important;
    }
</style>
    </div>