<div class="bg-themed-primary min-h-screen transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Header with Stats Dashboard -->
        <div class="bg-themed-secondary rounded-2xl shadow-xl border border-themed-primary p-6 transition-colors duration-300">
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
                        class="inline-flex items-center justify-center gap-2 bg-themed-secondary hover:bg-themed-tertiary text-themed-primary px-6 py-3 rounded-xl font-bold border border-themed-primary transition-all duration-300 transform hover:scale-105"
                        x-data="{ viewMode: 'grid' }">
                        <i :class="viewMode === 'grid' ? 'fas fa-list' : 'fas fa-th-large'"></i>
                        <span x-text="viewMode === 'grid' ? 'List View' : 'Grid View'"></span>
                    </button>

                    <button @click="showFilters = !showFilters"
                        x-data="{ showFilters: false }"
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
                            <h3 class="text-3xl font-black text-accent-themed-primary transition-colors duration-300">
                                {{ $totalAvailable }}
                            </h3>
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
                            <h3 class="text-3xl font-black text-green-500 transition-colors duration-300">
                                {{ $totalEnrolled }}
                            </h3>
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
                            <h3 class="text-3xl font-black text-purple-500 transition-colors duration-300">
                                {{ $totalCompleted }}
                            </h3>
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
        <div x-data="{ showFilters: false }" x-show="showFilters" 
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
                            <input type="checkbox" wire:model.live="showOnlyFree" class="w-5 h-5 rounded border-2 border-themed-primary text-green-500 focus:ring-2 focus:ring-green-500">
                            <span class="ml-3 text-themed-primary font-medium group-hover:text-green-600 transition-colors duration-200">Free Only</span>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model.live="showOnlyWithCertificate" class="w-5 h-5 rounded border-2 border-themed-primary text-purple-500 focus:ring-2 focus:ring-purple-500">
                            <span class="ml-3 text-themed-primary font-medium group-hover:text-purple-600 transition-colors duration-200">With Certificate</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @forelse($courses as $course)
                <div class="bg-themed-secondary rounded-xl shadow-lg overflow-hidden border border-themed-primary transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                    x-data="{
                        isEnrolled: @js($this->isEnrolled($course->id)),
                        isWishlisted: @js($this->isWishlisted($course->id)),
                        progress: @js($this->getCourseProgress($course->id))
                    }">

                    <!-- Course Image/Thumbnail -->
                    <div class="relative h-44">
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
                                ₦{{ number_format($course->price, 2) }}
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
                                <button @click="$wire.openPaymentModal({{ $course->id }})"
                                    :disabled="@js(in_array($course->id, $enrollingCourseIds))"
                                    class="w-full bg-accent-themed-primary hover:bg-accent-themed-secondary text-white font-bold py-2.5 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-md">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-rocket"></i>
                                        Enroll Now
                                    </span>
                                </button>
                            </template>

                            <template x-if="isEnrolled">
                                <div class="flex gap-2">
                                    <a href="{{ route('course.view', $course->slug) }}"
                                        class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center shadow-md">
                                        <i class="fas fa-play mr-2"></i>
                                        Continue
                                    </a>
                                    <button @click="$wire.dropCourse({{ $course->id }})"
                                        :disabled="@js(in_array($course->id, $droppingCourseIds))"
                                        class="bg-themed-tertiary hover:bg-red-100 text-red-700 font-bold py-2.5 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 border border-themed-primary">
                                        <i class="fas fa-sign-out-alt"></i>
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
                    <button wire:click="resetFilters"
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
            @if($selectedCourse ?? null)
                <div class="bg-themed-tertiary rounded-xl p-4 border border-themed-primary">
                    <div class="flex items-start gap-4">
                        @if($selectedCourse->thumbnail ?? null)
                            <img src="{{ asset('storage/' . $selectedCourse->thumbnail) }}" 
                                 alt="{{ $selectedCourse->title }}"
                                 class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-accent-themed-primary to-purple-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-graduation-cap text-white text-2xl"></i>
                            </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-themed-primary text-base mb-1 line-clamp-2">
                                {{ $selectedCourse->title ?? 'Loading...' }}
                            </h4>
                            <p class="text-themed-secondary text-sm">
                                by {{ $selectedCourse->instructor->name ?? 'Instructor' }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-themed-tertiary rounded-xl p-4 border border-themed-primary">
                    <div class="flex items-center justify-center py-4">
                        <i class="fas fa-spinner animate-spin text-accent-themed-primary text-2xl mr-3"></i>
                        <span class="text-themed-secondary">Loading course details...</span>
                    </div>
                </div>
            @endif

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
                                    You need ₦{{ number_format(max(0, ($coursePrice ?? 0) - ($walletBalance ?? 0)), 2) }} more to enroll in this course.
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
        <div wire:loading wire:target="confirmEnrollment" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-themed-secondary rounded-2xl p-8 flex flex-col items-center shadow-2xl border border-themed-primary transition-colors duration-300">
                <div class="relative mb-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-themed-tertiary"></div>
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-accent-themed-primary border-t-transparent absolute top-0"></div>
                </div>
                <span class="text-themed-primary font-black text-xl transition-colors duration-300">Processing enrollment...</span>
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

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('enrollment-success', (event) => {
                const redirectUrl = (event && (event[0]?.redirectUrl || event.redirectUrl)) || null;
                if (redirectUrl) {
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1500);
                }
            });
        });
    </script>
    <style>
        /* Visual feedback for loading states */
        [wire\:loading] {
            position: relative;
            opacity: 0.6;
            pointer-events: none;
        }
    
        [wire\:loading]::after {
            content: '⏳';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            z-index: 1000;
            animation: spin 1s linear infinite;
        }
    
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
</div>