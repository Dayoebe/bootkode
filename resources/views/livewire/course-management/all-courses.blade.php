<div class="bg-themed-primary min-h-screen p-4 lg:p-6 transition-colors duration-300">
    <div x-data="{ 
        tooltip: '', 
        showMobileFilters: false,
        selectedCourses: @entangle('selectedCourses'),
        showQuickActions: false
    }">
        
        <!-- Modern Header Section -->
        <div class="bg-themed-secondary rounded-2xl sm:rounded-3xl shadow-lg p-6 mb-6 border border-themed-primary transition-colors duration-300">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between">
                <div class="flex items-center mb-4 lg:mb-0">
                    <div class="relative">
                        <div class="bg-accent-themed-primary p-3 rounded-xl mr-4 shadow-md">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 bg-green-500 w-4 h-4 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-[8px]"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-themed-primary to-accent-themed-primary bg-clip-text text-transparent">
                            Course Management
                        </h1>
                        <p class="text-themed-secondary mt-1 text-sm transition-colors duration-300">
                            Organize, manage, and publish your educational content
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('create.course') }}" 
                       class="group bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform duration-300"></i> 
                        New Course
                    </a>
                    
                    <button @click="showQuickActions = !showQuickActions"
                            class="group bg-themed-secondary hover:bg-themed-tertiary text-themed-primary px-4 py-2.5 rounded-xl font-semibold text-sm border border-themed-primary transition-all duration-300 flex items-center justify-center transform hover:scale-105">
                        <i class="fas fa-sliders-h mr-2 group-hover:rotate-12 transition-transform duration-300"></i> 
                        Quick Actions
                    </button>
                </div>
            </div>
        </div>

        <!-- Course Statistics Dashboard -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <div class="bg-themed-secondary rounded-xl shadow-md p-4 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-accent-themed-primary">{{ $totalCourses }}</h3>
                        <p class="text-themed-secondary text-xs font-semibold">Total Courses</p>
                    </div>
                    <div class="bg-accent-themed-primary/20 p-2 rounded-lg transition-colors duration-300">
                        <i class="fas fa-book text-accent-themed-primary"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md p-4 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-green-600">{{ $publishedCourses }}</h3>
                        <p class="text-themed-secondary text-xs font-semibold">Published</p>
                    </div>
                    <div class="bg-green-100/50 p-2 rounded-lg transition-colors duration-300">
                        <i class="fas fa-globe text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md p-4 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-purple-600">{{ $approvedCourses }}</h3>
                        <p class="text-themed-secondary text-xs font-semibold">Approved</p>
                    </div>
                    <div class="bg-purple-100/50 p-2 rounded-lg transition-colors duration-300">
                        <i class="fas fa-check-circle text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md p-4 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-emerald-600">{{ $freeCourses }}</h3>
                        <p class="text-themed-secondary text-xs font-semibold">Free</p>
                    </div>
                    <div class="bg-emerald-100/50 p-2 rounded-lg transition-colors duration-300">
                        <i class="fas fa-gift text-emerald-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md p-4 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-amber-600">{{ $paidCourses }}</h3>
                        <p class="text-themed-secondary text-xs font-semibold">Paid</p>
                    </div>
                    <div class="bg-amber-100/50 p-2 rounded-lg transition-colors duration-300">
                        <i class="fas fa-dollar-sign text-amber-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md p-4 transform hover:scale-105 transition-all duration-300 border border-themed-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-red-600">{{ $pendingCourses }}</h3>
                        <p class="text-themed-secondary text-xs font-semibold">Pending</p>
                    </div>
                    <div class="bg-red-100/50 p-2 rounded-lg transition-colors duration-300">
                        <i class="fas fa-clock text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Panel -->
        <div x-show="showQuickActions" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             class="bg-themed-secondary rounded-2xl shadow-lg p-6 mb-6 border border-themed-primary transition-colors duration-300">
            <h3 class="text-lg font-bold text-themed-primary mb-4 flex items-center transition-colors duration-300">
                <i class="fas fa-bolt text-accent-themed-primary mr-2"></i>
                Bulk Actions
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <button wire:click="bulkApprove" x-bind:disabled="!selectedCourses.length" 
                        class="group bg-green-500 hover:bg-green-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transform hover:scale-105 disabled:hover:scale-100">
                    <i class="fas fa-check-circle mr-2"></i> 
                    Approve
                </button>
                
                <button wire:click="bulkPublish" x-bind:disabled="!selectedCourses.length" 
                        class="group bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transform hover:scale-105 disabled:hover:scale-100">
                    <i class="fas fa-globe mr-2"></i> 
                    Publish
                </button>
                
                <button wire:click="bulkUnpublish" x-bind:disabled="!selectedCourses.length" 
                        class="group bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transform hover:scale-105 disabled:hover:scale-100">
                    <i class="fas fa-eye-slash mr-2"></i> 
                    Unpublish
                </button>
                
                <button wire:click="bulkDelete" x-bind:disabled="!selectedCourses.length" 
                        class="group bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transform hover:scale-105 disabled:hover:scale-100">
                    <i class="fas fa-trash-alt mr-2"></i> 
                    Delete
                </button>
            </div>
        </div>

        <!-- Enhanced Filters Section -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 mb-6 border border-themed-primary transition-colors duration-300">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-themed-primary flex items-center transition-colors duration-300">
                    <i class="fas fa-filter text-accent-themed-primary mr-2"></i>
                    Search & Filters
                </h3>
                <div class="flex items-center space-x-3">
                    <button @click="showMobileFilters = !showMobileFilters" 
                            class="xl:hidden bg-themed-tertiary hover:bg-themed-secondary text-themed-primary p-2 rounded-lg transition-all duration-300 transform hover:scale-105 border border-themed-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    <button wire:click="resetAllFilters" 
                            class="group bg-themed-tertiary hover:bg-themed-secondary text-themed-primary px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-300 flex items-center transform hover:scale-105 border border-themed-primary">
                        <i class="fas fa-redo-alt mr-1 group-hover:rotate-180 transition-transform duration-500"></i> 
                        Reset
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4" 
                 :class="showMobileFilters ? 'block' : 'hidden xl:grid'">
                
                <!-- Enhanced Search -->
                <div class="relative col-span-full xl:col-span-2">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search courses, instructors, descriptions..."
                           class="w-full pl-10 pr-8 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300 text-sm">
                    <div class="absolute left-3 top-3 text-themed-secondary">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="absolute right-3 top-3">
                        <div wire:loading wire:target="search" class="animate-spin">
                            <i class="fas fa-spinner text-accent-themed-primary"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <select wire:model.live="categoryFilter" 
                        class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-3 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300 font-semibold text-sm">
                    <option value="">All Categories</option>
                    @forelse($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @empty
                        <option value="" disabled>No Categories</option>
                    @endforelse
                </select>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter" 
                        class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-3 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300 font-semibold text-sm">
                    <option value="">All Statuses</option>
                    <option value="published">Published</option>
                    <option value="unpublished">Unpublished</option>
                    <option value="approved">Approved</option>
                    <option value="unapproved">Unapproved</option>
                </select>

                <!-- Difficulty Filter -->
                <select wire:model.live="difficultyFilter" 
                        class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-3 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300 font-semibold text-sm">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
        </div>

        <!-- Course Grid -->
        @if ($courses->isEmpty())
            <div class="text-center py-12 bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary transition-colors duration-300">
                <div class="animate-bounce mb-6">
                    <div class="bg-accent-themed-primary/20 w-24 h-24 rounded-full flex items-center justify-center mx-auto transition-colors duration-300">
                        <i class="fas fa-book-open text-4xl text-accent-themed-primary"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-themed-primary mb-3 transition-colors duration-300">No courses found</h3>
                <p class="text-themed-secondary mb-8 max-w-md mx-auto text-sm transition-colors duration-300">
                    Start creating amazing courses or adjust your search filters to find existing ones.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('course-categories') }}" 
                       class="bg-themed-tertiary hover:bg-themed-secondary text-themed-primary px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105 border border-themed-primary">
                        <i class="fas fa-tag mr-2"></i> Manage Categories
                    </a>
                    <a href="{{ route('create.course') }}" 
                       class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i> Create Course
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6">
                @foreach ($courses as $course)
                    <div class="group bg-themed-secondary rounded-2xl shadow-lg hover:shadow-xl transition-all duration-500 overflow-hidden transform hover:-translate-y-1 border border-themed-primary" 
                         style="animation-delay: {{ $loop->index * 0.1 }}s">
                        
                        <!-- Course Thumbnail -->
                        <div class="relative h-40 bg-gradient-to-br from-accent-themed-primary to-purple-600 overflow-hidden">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                    alt="{{ $course->title }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-white text-4xl opacity-70"></i>
                                </div>
                            @endif
                            
                            <!-- Selection Checkbox -->
                            <div class="absolute top-3 left-3">
                                <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}" 
                                       class="h-4 w-4 text-accent-themed-primary rounded-lg border-2 border-white focus:ring-accent-themed-primary shadow-md">
                            </div>
                            
                            <!-- Category Badge -->
                            <div class="absolute top-3 right-3">
                                <span class="bg-white/90 text-accent-themed-primary text-xs font-bold px-2 py-1 rounded-lg shadow-md">
                                    {{ Str::limit($course->category->name ?? 'Uncategorized', 12) }}
                                </span>
                            </div>
                            
                            <!-- Approval Status Badge -->
                            <div class="absolute bottom-3 right-3">
                                @if($course->is_approved)
                                    <span class="bg-green-500/90 text-white text-xs font-bold px-2 py-1 rounded-lg flex items-center shadow-md">
                                        <i class="fas fa-check mr-1"></i> Approved
                                    </span>
                                @else
                                    <span class="bg-yellow-500/90 text-white text-xs font-bold px-2 py-1 rounded-lg flex items-center shadow-md">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-5">
                            <!-- Course Title (Clickable) -->
                            <a href="{{ route('course-builder', $course) }}" 
                               class="block group/title cursor-pointer mb-4">
                                <h3 class="font-bold text-themed-primary text-base mb-2 line-clamp-2 group-hover/title:text-accent-themed-primary transition-colors duration-300">
                                    {{ Str::limit($course->title, 50) }}
                                </h3>
                            </a>
                            
                            <!-- Instructor -->
                            <div class="flex items-center text-themed-secondary mb-4 text-sm transition-colors duration-300">
                                <div class="bg-themed-tertiary rounded-xl w-8 h-8 flex items-center justify-center mr-3 border border-themed-primary">
                                    <i class="fas fa-user-tie text-xs"></i>
                                </div>
                                <span class="font-semibold">{{ Str::limit($course->instructor->name, 20) }}</span>
                            </div>
                            
                            <!-- Stats -->
                            <div class="flex flex-wrap gap-2 mb-5">
                                <!-- Difficulty Badge -->
                                <span class="px-2 py-1 rounded-lg text-xs font-bold transition-colors duration-300
                                    @if($course->difficulty_level === 'beginner') bg-green-100/50 text-green-800
                                    @elseif($course->difficulty_level === 'intermediate') bg-yellow-100/50 text-yellow-800
                                    @else bg-red-100/50 text-red-800 @endif">
                                    <i class="fas fa-signal mr-1"></i>
                                    {{ substr(ucfirst($course->difficulty_level), 0, 3) }}
                                </span>
                                
                                <!-- Enrollment Count -->
                                <span class="px-2 py-1 rounded-lg text-xs font-bold bg-accent-themed-primary/20 text-accent-themed-primary transition-colors duration-300">
                                    <i class="fas fa-users mr-1"></i> 
                                    {{ $course->enrollments->count() }}
                                </span>
                                
                                <!-- Published Status -->
                                <span class="px-2 py-1 rounded-lg text-xs font-bold transition-colors duration-300
                                    {{ $course->is_published ? 'bg-purple-100/50 text-purple-800' : 'bg-themed-tertiary text-themed-secondary' }}">
                                    <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }} mr-1"></i>
                                    {{ $course->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-between items-center pt-4 border-t border-themed-primary">
                                <div class="flex space-x-2">
                                    <button wire:click="CourseForm({{ $course->id }})" 
                                            class="group bg-accent-themed-primary/20 hover:bg-accent-themed-primary/40 text-accent-themed-primary p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
                                            title="Edit Course">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    
                                    <a href="{{ route('course-builder', $course) }}" 
                                       class="group bg-purple-100/50 hover:bg-purple-200/50 text-purple-600 p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
                                       title="Build Course">
                                        <i class="fas fa-cogs text-xs"></i>
                                    </a>
                                    
                                    <button wire:click="previewCourse({{ $course->id }})" 
                                            class="group bg-indigo-100/50 hover:bg-indigo-200/50 text-indigo-600 p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
                                            title="Preview Course">
                                        <i class="fas fa-search text-xs"></i>
                                    </button>
                                    
                                    <button wire:click="togglePublished({{ $course->id }})" 
                                            class="group {{ $course->is_published ? 'bg-green-100/50 hover:bg-green-200/50 text-green-600' : 'bg-themed-tertiary hover:bg-gray-200 text-themed-secondary' }} p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
                                            title="{{ $course->is_published ? 'Unpublish' : 'Publish' }}">
                                        <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }} text-xs"></i>
                                    </button>
                                    
                                    <button wire:click="toggleApproved({{ $course->id }})" 
                                            class="group {{ $course->is_approved ? 'bg-green-100/50 hover:bg-green-200/50 text-green-600' : 'bg-yellow-100/50 hover:bg-yellow-200/50 text-yellow-600' }} p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
                                            title="{{ $course->is_approved ? 'Unapprove Course' : 'Approve Course' }}">
                                        <i class="fas fa-{{ $course->is_approved ? 'check-circle' : 'clock' }} text-xs"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Button -->
                                <button wire:click="deleteCourse({{ $course->id }})" 
                                        wire:confirm="Are you sure you want to delete this course and all its content?"
                                        class="group bg-red-100/50 hover:bg-red-200/50 text-red-600 p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
                                        title="Delete Course">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Enhanced Pagination -->
            <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
                {{ $courses->links('pagination::tailwind') }}
            </div>
        @endif

        <!-- Modern Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-themed-secondary rounded-2xl p-6 flex flex-col items-center shadow-xl border border-themed-primary transition-colors duration-300">
                <div class="relative">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-themed-tertiary"></div>
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-accent-themed-primary border-t-transparent absolute top-0"></div>
                </div>
                <span class="text-themed-primary font-semibold text-sm mt-4 transition-colors duration-300">Processing your request...</span>
            </div>
        </div>

        <!-- Modern Notifications -->
        <div x-data="{ show: false, message: '', type: 'success' }" 
             x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 5000)"
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             class="fixed top-6 right-6 z-50 max-w-xs">
            <div :class="type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-accent-themed-primary'" 
                 class="text-white px-4 py-3 rounded-xl shadow-lg flex items-center border border-white/20 text-sm transition-colors duration-300">
                <i :class="type === 'success' ? 'fas fa-check-circle' : type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle'" class="mr-2"></i>
                <span x-text="message" class="font-semibold"></span>
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200 transition-colors duration-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>