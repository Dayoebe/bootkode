<div class="bg-themed-primary min-h-screen transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ 
        tooltip: '', 
        showMobileFilters: false,
        selectedCourses: @entangle('selectedCourses'),
        showQuickActions: false
    }">
        
        <!-- Page Header -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 animate__animated animate__fadeInDown transition-colors duration-300">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-accent-themed-primary to-purple-600 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary transition-colors duration-300">
                            Course Management
                        </h1>
                        <p class="text-themed-secondary mt-1 transition-colors duration-300">
                            Organize, manage, and publish your educational content
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('create.course') }}" 
                       class="inline-flex items-center justify-center gap-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-plus"></i>
                        <span>New Course</span>
                    </a>
                    
                    <button @click="showQuickActions = !showQuickActions"
                            class="inline-flex items-center justify-center gap-2 bg-themed-secondary hover:bg-themed-tertiary text-themed-primary px-6 py-3 rounded-xl font-semibold border border-themed-primary transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-sliders-h"></i>
                        <span>Quick Actions</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-4 sm:p-5 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl sm:text-3xl font-bold text-accent-themed-primary transition-colors duration-300">{{ $totalCourses }}</h3>
                        <div class="bg-accent-themed-primary/10 p-2 rounded-lg transition-colors duration-300">
                            <i class="fas fa-book text-accent-themed-primary text-lg"></i>
                        </div>
                    </div>
                    <p class="text-themed-secondary text-xs font-semibold transition-colors duration-300">Total Courses</p>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-4 sm:p-5 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl sm:text-3xl font-bold text-green-600">{{ $publishedCourses }}</h3>
                        <div class="bg-green-100/50 p-2 rounded-lg transition-colors duration-300">
                            <i class="fas fa-globe text-green-600 text-lg"></i>
                        </div>
                    </div>
                    <p class="text-themed-secondary text-xs font-semibold transition-colors duration-300">Published</p>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-4 sm:p-5 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl sm:text-3xl font-bold text-purple-600">{{ $approvedCourses }}</h3>
                        <div class="bg-purple-100/50 p-2 rounded-lg transition-colors duration-300">
                            <i class="fas fa-check-circle text-purple-600 text-lg"></i>
                        </div>
                    </div>
                    <p class="text-themed-secondary text-xs font-semibold transition-colors duration-300">Approved</p>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-4 sm:p-5 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl sm:text-3xl font-bold text-emerald-600">{{ $freeCourses }}</h3>
                        <div class="bg-emerald-100/50 p-2 rounded-lg transition-colors duration-300">
                            <i class="fas fa-gift text-emerald-600 text-lg"></i>
                        </div>
                    </div>
                    <p class="text-themed-secondary text-xs font-semibold transition-colors duration-300">Free</p>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-4 sm:p-5 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl sm:text-3xl font-bold text-amber-600">{{ $paidCourses }}</h3>
                        <div class="bg-amber-100/50 p-2 rounded-lg transition-colors duration-300">
                            <i class="fas fa-dollar-sign text-amber-600 text-lg"></i>
                        </div>
                    </div>
                    <p class="text-themed-secondary text-xs font-semibold transition-colors duration-300">Paid</p>
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-4 sm:p-5 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.6s">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl sm:text-3xl font-bold text-red-600">{{ $pendingCourses }}</h3>
                        <div class="bg-red-100/50 p-2 rounded-lg transition-colors duration-300">
                            <i class="fas fa-clock text-red-600 text-lg"></i>
                        </div>
                    </div>
                    <p class="text-themed-secondary text-xs font-semibold transition-colors duration-300">Pending</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions Panel -->
        <div x-show="showQuickActions" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300"
             style="display: none;">
            <h3 class="text-lg font-bold text-themed-primary mb-4 flex items-center gap-2 transition-colors duration-300">
                <i class="fas fa-bolt text-accent-themed-primary"></i>
                Bulk Actions
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <button wire:click="bulkApprove" x-bind:disabled="!selectedCourses.length" 
                        class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105">
                    <i class="fas fa-check-circle"></i>
                    <span>Approve</span>
                </button>
                
                <button wire:click="bulkPublish" x-bind:disabled="!selectedCourses.length" 
                        class="inline-flex items-center justify-center gap-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105">
                    <i class="fas fa-globe"></i>
                    <span>Publish</span>
                </button>
                
                <button wire:click="bulkUnpublish" x-bind:disabled="!selectedCourses.length" 
                        class="inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105">
                    <i class="fas fa-eye-slash"></i>
                    <span>Unpublish</span>
                </button>
                
                <button wire:click="bulkDelete" x-bind:disabled="!selectedCourses.length" 
                        class="inline-flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105">
                    <i class="fas fa-trash-alt"></i>
                    <span>Delete</span>
                </button>
            </div>
        </div>

        <!-- Enhanced Filters Section -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h3 class="text-lg font-bold text-themed-primary flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-filter text-accent-themed-primary"></i>
                    Search & Filters
                </h3>
                <div class="flex items-center gap-3">
                    <button @click="showMobileFilters = !showMobileFilters" 
                            class="lg:hidden inline-flex items-center gap-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105 border border-themed-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    <button wire:click="resetAllFilters" 
                            class="inline-flex items-center gap-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary px-4 py-2 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 border border-themed-primary">
                        <i class="fas fa-redo-alt"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4" 
                 :class="showMobileFilters ? 'block' : 'hidden lg:grid'">
                
                <!-- Enhanced Search -->
                <div class="relative xl:col-span-2">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search courses, instructors..."
                           class="w-full pl-10 pr-10 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-search"></i>
                    </div>
                    <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <i class="fas fa-spinner animate-spin text-accent-themed-primary"></i>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <select wire:model.live="categoryFilter" 
                        class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300 font-medium">
                    <option value="">All Categories</option>
                    @forelse($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @empty
                        <option value="" disabled>No Categories</option>
                    @endforelse
                </select>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter" 
                        class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300 font-medium">
                    <option value="">All Statuses</option>
                    <option value="published">Published</option>
                    <option value="unpublished">Unpublished</option>
                    <option value="approved">Approved</option>
                    <option value="unapproved">Unapproved</option>
                </select>

                <!-- Difficulty Filter -->
                <select wire:model.live="difficultyFilter" 
                        class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300 font-medium">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
        </div>

        <!-- Course Grid -->
        @if ($courses->isEmpty())
            <div class="text-center py-16 bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary transition-colors duration-300">
                <div class="animate-bounce mb-6">
                    <div class="bg-accent-themed-primary/10 w-24 h-24 rounded-full flex items-center justify-center mx-auto transition-colors duration-300">
                        <i class="fas fa-book-open text-4xl text-accent-themed-primary"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-themed-primary mb-3 transition-colors duration-300">No courses found</h3>
                <p class="text-themed-secondary mb-8 max-w-md mx-auto transition-colors duration-300">
                    Start creating amazing courses or adjust your search filters.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('course-categories') }}" 
                       class="inline-flex items-center justify-center gap-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 border border-themed-primary">
                        <i class="fas fa-tag"></i>
                        Manage Categories
                    </a>
                    <a href="{{ route('create.course') }}" 
                       class="inline-flex items-center justify-center gap-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus"></i>
                        Create Course
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                @foreach ($courses as $course)
                    <div class="bg-themed-secondary rounded-2xl shadow-lg hover:shadow-xl transition-all duration-500 overflow-hidden transform hover:-translate-y-1 border border-themed-primary group animate__animated animate__fadeInUp" 
                         style="animation-delay: {{ $loop->index * 0.05 }}s">
                        
                        <!-- Course Thumbnail -->
                        <div class="relative h-44 bg-gradient-to-br from-accent-themed-primary to-purple-600 overflow-hidden">
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
                                       class="h-5 w-5 text-accent-themed-primary rounded-lg border-2 border-white focus:ring-accent-themed-primary shadow-md cursor-pointer">
                            </div>
                            
                            <!-- Category Badge -->
                            <div class="absolute top-3 right-3">
                                <span class="bg-white/90 text-accent-themed-primary text-xs font-bold px-3 py-1 rounded-full shadow-md backdrop-blur-sm">
                                    {{ Str::limit($course->category->name ?? 'Uncategorized', 12) }}
                                </span>
                            </div>
                            
                            <!-- Approval Status Badge -->
                            <div class="absolute bottom-3 right-3">
                                @if($course->is_approved)
                                    <span class="bg-green-500/90 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center shadow-md backdrop-blur-sm">
                                        <i class="fas fa-check mr-1"></i> Approved
                                    </span>
                                @else
                                    <span class="bg-yellow-500/90 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center shadow-md backdrop-blur-sm">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-5">
                            <!-- Course Title (Clickable) -->
                            <a href="{{ route('course-builder', $course) }}" 
                               class="block group/title cursor-pointer mb-3">
                                <h3 class="font-bold text-themed-primary text-base mb-2 line-clamp-2 group-hover/title:text-accent-themed-primary transition-colors duration-300">
                                    {{ Str::limit($course->title, 50) }}
                                </h3>
                            </a>
                            
                            <!-- Instructor -->
                            <div class="flex items-center text-themed-secondary mb-4 text-sm transition-colors duration-300">
                                <div class="bg-themed-tertiary rounded-xl w-8 h-8 flex items-center justify-center mr-3 border border-themed-primary transition-colors duration-300">
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
                                    {{ ucfirst($course->difficulty_level) }}
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
<!-- Action Buttons -->
<div class="flex flex-wrap gap-2 pt-4 border-t border-themed-primary transition-colors duration-300">
    <!-- Edit Course -->
    <button wire:click="editCourse({{ $course->id }})" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold bg-accent-themed-primary/10 hover:bg-accent-themed-primary/20 text-accent-themed-primary transition-all duration-300"
            title="Edit Course">
        <i class="fas fa-edit"></i>
        <span>Edit</span>
    </button>
    
    <!-- Build Course -->
    <a href="{{ route('course-builder', $course) }}" 
       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold bg-purple-100/50 hover:bg-purple-200/50 text-purple-600 transition-all duration-300"
       title="Build Course">
        <i class="fas fa-cogs"></i>
        <span>Build</span>
    </a>
    
    <!-- Preview Course -->
    <button wire:click="previewCourse({{ $course->id }})" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold bg-indigo-100/50 hover:bg-indigo-200/50 text-indigo-600 transition-all duration-300"
            title="Preview Course">
        <i class="fas fa-eye"></i>
        <span>Preview</span>
    </button>
    
    <!-- Publish/Unpublish -->
    <button wire:click="togglePublished({{ $course->id }})" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-300 {{ $course->is_published ? 'bg-green-100/50 text-green-700 hover:bg-green-200/50' : 'bg-gray-100/50 text-gray-700 hover:bg-gray-200/50' }}"
            title="{{ $course->is_published ? 'Unpublish' : 'Publish' }}">
        <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }}"></i>
        <span>{{ $course->is_published ? 'Unpublish' : 'Publish' }}</span>
    </button>
    
    <!-- Approve/Unapprove -->
    <button wire:click="toggleApproved({{ $course->id }})" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-300 {{ $course->is_approved ? 'bg-green-100/50 text-green-700 hover:bg-green-200/50' : 'bg-yellow-100/50 text-yellow-700 hover:bg-yellow-200/50' }}"
            title="{{ $course->is_approved ? 'Unapprove' : 'Approve' }}">
        <i class="fas fa-{{ $course->is_approved ? 'check-circle' : 'clock' }}"></i>
        <span>{{ $course->is_approved ? 'Unapprove' : 'Approve' }}</span>
    </button>
    
    <!-- Delete Button -->
    <button wire:click="deleteCourse({{ $course->id }})" 
            wire:confirm="Are you sure you want to delete this course?"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold bg-red-100/50 hover:bg-red-200/50 text-red-600 transition-all duration-300"
            title="Delete Course">
        <i class="fas fa-trash-alt"></i>
        <span>Delete</span>
    </button>
</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Enhanced Pagination -->
            <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
                {{ $courses->links('pagination::tailwind') }}
            </div>
        @endif

        <!-- Modern Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-themed-secondary rounded-2xl p-8 flex flex-col items-center shadow-2xl border border-themed-primary transition-colors duration-300">
                <div class="relative mb-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-themed-tertiary"></div>
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-accent-themed-primary border-t-transparent absolute top-0"></div>
                </div>
                <span class="text-themed-primary font-semibold transition-colors duration-300">Processing your request...</span>
            </div>
        </div>

        <!-- Modern Notifications -->
        <div x-data="{ show: false, message: '', type: 'success' }" 
             x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 5000)"
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="fixed top-6 right-6 z-50 max-w-xs"
             style="display: none;">
            <div :class="type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-accent-themed-primary'" 
                 class="text-white px-6 py-4 rounded-xl shadow-lg flex items-center border border-white/20 transition-colors duration-300">
                <i :class="type === 'success' ? 'fas fa-check-circle' : type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle'" class="mr-3 text-lg"></i>
                <span x-text="message" class="font-semibold"></span>
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200 transition-colors duration-300">
                    <i class="fas fa-times"></i>
                </button>
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
</style>
</div>