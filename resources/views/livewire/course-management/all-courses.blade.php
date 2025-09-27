<div class="bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 min-h-screen p-4 lg:p-6" x-data="{ 
    tooltip: '', 
    showMobileFilters: false,
    selectedCourses: @entangle('selectedCourses'),
    showQuickActions: false,
    showStats: true
}">
    <!-- Modern Header Section - More Compact -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg p-6 mb-6 border border-white/20 dark:border-gray-700/30">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between">
            <div class="flex items-center mb-4 lg:mb-0">
                <div class="relative">
                    <div class="bg-blue-500 p-3 rounded-xl mr-4 shadow-md">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 bg-green-500 w-4 h-4 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-[8px]"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-blue-600 dark:from-white dark:to-blue-400 bg-clip-text text-transparent">
                        Course Management
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">
                        Organize, manage, and publish your educational content
                    </p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('create.course') }}" 
                   class="group bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform duration-300"></i> 
                    New Course
                </a>
                
                <button @click="showQuickActions = !showQuickActions"
                        class="group bg-white hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-xl font-semibold text-sm border border-gray-200 dark:border-gray-600 transition-all duration-300 flex items-center justify-center transform hover:scale-105">
                    <i class="fas fa-sliders-h mr-2 group-hover:rotate-12 transition-transform duration-300"></i> 
                    Quick Actions
                </button>
            </div>
        </div>
    </div>

    <!-- Course Statistics Dashboard - More Compact -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-xl shadow-md p-4 border border-white/20 dark:border-gray-700/30 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalCourses }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">Total Courses</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg">
                    <i class="fas fa-book text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-xl shadow-md p-4 border border-white/20 dark:border-gray-700/30 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-green-600 dark:text-green-400">{{ $publishedCourses }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">Published</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-2 rounded-lg">
                    <i class="fas fa-globe text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-xl shadow-md p-4 border border-white/20 dark:border-gray-700/30 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $approvedCourses }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">Approved</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 p-2 rounded-lg">
                    <i class="fas fa-check-circle text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-xl shadow-md p-4 border border-white/20 dark:border-gray-700/30 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ $freeCourses }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">Free</p>
                </div>
                <div class="bg-emerald-100 dark:bg-emerald-900 p-2 rounded-lg">
                    <i class="fas fa-gift text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-xl shadow-md p-4 border border-white/20 dark:border-gray-700/30 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ $paidCourses }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">Paid</p>
                </div>
                <div class="bg-amber-100 dark:bg-amber-900 p-2 rounded-lg">
                    <i class="fas fa-dollar-sign text-amber-600 dark:text-amber-400"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-xl shadow-md p-4 border border-white/20 dark:border-gray-700/30 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-red-600 dark:text-red-400">{{ $pendingCourses }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-xs font-semibold">Pending</p>
                </div>
                <div class="bg-red-100 dark:bg-red-900 p-2 rounded-lg">
                    <i class="fas fa-clock text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel - More Compact -->
    <div x-show="showQuickActions" 
         x-transition:enter="animate__animated animate__slideInDown animate__faster"
         x-transition:leave="animate__animated animate__slideOutUp animate__faster"
         class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg p-6 mb-6 border border-white/20 dark:border-gray-700/30">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Bulk Actions
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <button wire:click="bulkApprove" x-bind:disabled="!selectedCourses.length" 
                    class="group bg-green-500 hover:bg-green-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transform hover:scale-105 disabled:hover:scale-100">
                <i class="fas fa-check-circle mr-2"></i> 
                Approve
            </button>
            
            <button wire:click="bulkPublish" x-bind:disabled="!selectedCourses.length" 
                    class="group bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transform hover:scale-105 disabled:hover:scale-100">
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

    <!-- Enhanced Filters Section - More Compact -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg p-6 mb-6 border border-white/20 dark:border-gray-700/30">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-filter text-purple-500 mr-2"></i>
                Search & Filters
            </h3>
            <div class="flex items-center space-x-3">
                <button @click="showMobileFilters = !showMobileFilters" 
                        class="xl:hidden bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 p-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-filter"></i>
                </button>
                <button wire:click="resetAllFilters" 
                        class="group bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-300 flex items-center transform hover:scale-105">
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
                       class="w-full pl-10 pr-8 py-3 bg-white/50 dark:bg-gray-700/50 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 backdrop-blur-sm text-sm">
                <div class="absolute left-3 top-3 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <div class="absolute right-3 top-3">
                    <div wire:loading wire:target="search" class="animate-spin">
                        <i class="fas fa-spinner text-blue-500"></i>
                    </div>
                </div>
            </div>
            
            <!-- Category Filter -->
            <select wire:model.live="categoryFilter" 
                    class="bg-white/50 dark:bg-gray-700/50 border-2 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl px-3 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 backdrop-blur-sm font-semibold text-sm">
                <option value="">All Categories</option>
                @forelse($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @empty
                    <option value="" disabled>No Categories</option>
                @endforelse
            </select>

            <!-- Status Filter -->
            <select wire:model.live="statusFilter" 
                    class="bg-white/50 dark:bg-gray-700/50 border-2 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl px-3 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 backdrop-blur-sm font-semibold text-sm">
                <option value="">All Statuses</option>
                <option value="published">Published</option>
                <option value="unpublished">Unpublished</option>
                <option value="approved">Approved</option>
                <option value="unapproved">Unapproved</option>
            </select>

            <!-- Difficulty Filter -->
            <select wire:model.live="difficultyFilter" 
                    class="bg-white/50 dark:bg-gray-700/50 border-2 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl px-3 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 backdrop-blur-sm font-semibold text-sm">
                <option value="">All Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>
    </div>

    <!-- Course Grid - More Compact -->
    @if ($courses->isEmpty())
        <div class="text-center py-12 bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 dark:border-gray-700/30">
            <div class="animate__animated animate__bounceIn">
                <div class="bg-blue-100 dark:bg-blue-900 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-book-open text-4xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">No courses found</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto text-sm">
                Start creating amazing courses or adjust your search filters to find existing ones.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('course-categories') }}" 
                   class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105">
                    <i class="fas fa-tag mr-2"></i> Manage Categories
                </a>
                <a href="{{ route('create.course') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i> Create Course
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6">
            @foreach ($courses as $course)
                <div class="group bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-lg hover:shadow-xl transition-all duration-500 overflow-hidden transform hover:-translate-y-1 border border-white/20 dark:border-gray-700/30" 
                     style="animation-delay: {{ $loop->index * 0.1 }}s">
                    
                    <!-- Course Thumbnail -->
                    <div class="relative h-40 bg-blue-500 overflow-hidden">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . ($course->thumbnail ?? 'images/default-course.png')) }}"
                                alt="{{ $course->title }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                <i class="fas fa-graduation-cap text-white text-4xl opacity-70"></i>
                            </div>
                        @endif
                        
                        <!-- Selection Checkbox -->
                        <div class="absolute top-3 left-3">
                            <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}" 
                                   class="h-4 w-4 text-blue-600 rounded-lg border-2 border-white focus:ring-blue-500 shadow-md">
                        </div>
                        
                        <!-- Category Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="bg-white/90 backdrop-blur-sm text-blue-600 text-xs font-bold px-2 py-1 rounded-lg shadow-md">
                                {{ Str::limit($course->category->name ?? 'Uncategorized', 12) }}
                            </span>
                        </div>
                        
                        <!-- Approval Status Badge -->
                        <div class="absolute bottom-3 right-3">
                            @if($course->is_approved)
                                <span class="bg-green-500/90 backdrop-blur-sm text-white text-xs font-bold px-2 py-1 rounded-lg flex items-center shadow-md">
                                    <i class="fas fa-check mr-1"></i> Approved
                                </span>
                            @else
                                <span class="bg-yellow-500/90 backdrop-blur-sm text-white text-xs font-bold px-2 py-1 rounded-lg flex items-center shadow-md">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Course Content -->
                    <div class="p-5">
                        <!-- Clickable Course Title -->
                        <a href="{{ route('course-builder', $course) }}" 
                           class="block group/title cursor-pointer mb-4">
                            <h3 class="font-bold text-gray-900 dark:text-white text-base mb-2 line-clamp-2 group-hover/title:text-blue-600 dark:group-hover/title:text-blue-400 transition-colors duration-300">
                                {{ Str::limit($course->title, 50) }}
                            </h3>
                        </a>
                        
                        <!-- Instructor -->
                        <div class="flex items-center text-gray-600 dark:text-gray-400 mb-4 text-sm">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl w-8 h-8 flex items-center justify-center mr-3">
                                <i class="fas fa-user-tie text-xs"></i>
                            </div>
                            <span class="font-semibold">{{ Str::limit($course->instructor->name, 20) }}</span>
                        </div>
                        
                        <!-- Stats -->
                        <div class="flex flex-wrap gap-2 mb-5">
                            <!-- Difficulty Badge -->
                            <span class="px-2 py-1 rounded-lg text-xs font-bold
                                @if($course->difficulty_level === 'beginner') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($course->difficulty_level === 'intermediate') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                <i class="fas fa-signal mr-1"></i>
                                {{ substr(ucfirst($course->difficulty_level), 0, 3) }}
                            </span>
                            
                            <!-- Enrollment Count -->
                            <span class="px-2 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <i class="fas fa-users mr-1"></i> 
                                {{ $course->enrollments->count() }}
                            </span>
                            
                            <!-- Published Status -->
                            <span class="px-2 py-1 rounded-lg text-xs font-bold 
                                {{ $course->is_published ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }} mr-1"></i>
                                {{ $course->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                           <!-- In all-courses.blade.php - remove the duplicate preview button -->
<div class="flex space-x-2">
    <!-- Edit Course Button -->
    <button wire:click="CourseForm({{ $course->id }})" 
            class="group bg-blue-100 hover:bg-blue-200 text-blue-600 p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
            title="Edit Course">
        <i class="fas fa-edit text-xs"></i>
    </button>
    
    <!-- Course Builder Button -->
    <a href="{{ route('course-builder', $course) }}" 
       class="group bg-purple-100 hover:bg-purple-200 text-purple-600 p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
       title="Build Course">
        <i class="fas fa-cogs text-xs"></i>
    </a>
    
    <!-- Preview Button (keep only one) -->
    <button wire:click="previewCourse({{ $course->id }})" 
            class="group bg-indigo-100 hover:bg-indigo-200 text-indigo-600 p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
            title="Preview Course">
        <i class="fas fa-search text-xs"></i>
    </button>
    
    <!-- Publish Toggle -->
    <button wire:click="togglePublished({{ $course->id }})" 
            class="group {{ $course->is_published ? 'bg-green-100 hover:bg-green-200 text-green-600' : 'bg-gray-100 hover:bg-gray-200 text-gray-600' }} p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
            title="{{ $course->is_published ? 'Unpublish' : 'Publish' }}">
        <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }} text-xs"></i>
    </button>
    
    <!-- Approve/Unapprove Button -->
    <button wire:click="toggleApproved({{ $course->id }})" 
            class="group {{ $course->is_approved ? 'bg-green-100 hover:bg-green-200 text-green-600' : 'bg-yellow-100 hover:bg-yellow-200 text-yellow-600' }} p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
            title="{{ $course->is_approved ? 'Unapprove Course' : 'Approve Course' }}">
        <i class="fas fa-{{ $course->is_approved ? 'check-circle' : 'clock' }} text-xs"></i>
    </button>
</div>
                            
                            <!-- Delete Button -->
                            <button wire:click="deleteCourse({{ $course->id }})" 
                                    wire:confirm="Are you sure you want to delete this course and all its content?"
                                    class="group bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-all duration-300 transform hover:scale-110"
                                    title="Delete Course">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Enhanced Pagination -->
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg p-6 border border-white/20 dark:border-gray-700/30">
            {{ $courses->links('pagination::tailwind') }}
        </div>
    @endif

    <!-- Modern Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl p-6 flex flex-col items-center shadow-xl border border-white/20 dark:border-gray-700/30">
            <div class="relative">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-200"></div>
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent absolute top-0"></div>
            </div>
            <span class="text-gray-800 dark:text-white font-semibold text-sm mt-4">Processing your request...</span>
        </div>
    </div>

    <!-- Modern Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 5000)"
         x-show="show" 
         x-transition:enter="animate__animated animate__slideInRight" 
         x-transition:leave="animate__animated animate__slideOutRight"
         class="fixed top-6 right-6 z-50 max-w-xs">
        <div :class="type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'" 
             class="text-white px-4 py-3 rounded-xl shadow-lg flex items-center backdrop-blur-xl border border-white/20 text-sm">
            <i :class="type === 'success' ? 'fas fa-check-circle' : type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle'" class="mr-2"></i>
            <span x-text="message" class="font-semibold"></span>
            <button @click="show = false" class="ml-4 text-white hover:text-gray-200 transition-colors duration-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>