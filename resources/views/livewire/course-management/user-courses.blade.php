<div class="bg-themed-primary transition-colors duration-300 p-4 sm:p-6">
    <!-- Header Section with Stats -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 pb-4 border-b border-themed-primary">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                <i class="fas fa-book-open mr-2 sm:mr-3 text-accent-themed-primary"></i> 
                My Courses
            </h1>
            <p class="text-themed-secondary mt-1 text-sm transition-colors duration-300">Manage your courses and track their performance</p>
        </div>

        <a href="{{ route('create.course') }}"
            class="mt-4 md:mt-0 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> New Course
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5 mb-6">
        <div class="bg-themed-secondary rounded-xl shadow-md p-4 sm:p-5 border border-themed-primary transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-accent-themed-primary/20 p-3 sm:p-4 rounded-lg mr-3 sm:mr-4">
                    <i class="fas fa-book text-accent-themed-primary text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-themed-secondary font-medium transition-colors duration-300">Total Courses</p>
                    <p class="text-lg sm:text-xl font-bold text-themed-primary">{{ $courses->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-md p-4 sm:p-5 border border-themed-primary transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-green-100/50 p-3 sm:p-4 rounded-lg mr-3 sm:mr-4">
                    <i class="fas fa-users text-green-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-themed-secondary font-medium transition-colors duration-300">Total Enrollments</p>
                    <p class="text-lg sm:text-xl font-bold text-themed-primary">{{ $totalEnrollments }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-md p-4 sm:p-5 border border-themed-primary transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-purple-100/50 p-3 sm:p-4 rounded-lg mr-3 sm:mr-4">
                    <i class="fas fa-globe text-purple-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-themed-secondary font-medium transition-colors duration-300">Published Courses</p>
                    <p class="text-lg sm:text-xl font-bold text-themed-primary">{{ $publishedCount }} / {{ $courses->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mb-5 bg-themed-secondary rounded-xl shadow-md p-4 sm:p-5 border border-themed-primary transition-colors duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-bold text-themed-primary transition-colors duration-300">Filters & Search</h3>
            <button wire:click="$set('search', '')"
                class="text-xs text-accent-themed-primary hover:text-accent-themed-secondary transition-colors duration-300 flex items-center font-medium">
                <i class="fas fa-redo-alt mr-1"></i> Clear Filters
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Search your courses..."
                       class="w-full pl-8 pr-3 py-2.5 bg-themed-tertiary border-2 border-themed-primary rounded-lg text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300 text-sm">
                <div class="absolute left-2.5 top-2.5 text-themed-secondary">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>

            <select wire:model.live="categoryFilter"
                class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-lg px-3 py-2.5 text-sm font-medium focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter"
                class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-lg px-3 py-2.5 text-sm font-medium focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                <option value="">All Statuses</option>
                <option value="published">Published</option>
                <option value="unpublished">Unpublished</option>
                <option value="approved">Approved</option>
                <option value="unapproved">Pending Approval</option>
            </select>

            <select wire:model.live="difficultyFilter"
                class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-lg px-3 py-2.5 text-sm font-medium focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                <option value="">All Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-5 flex flex-wrap gap-2" x-data="{ selectedCourses: @entangle('selectedCourses') }">
        <button wire:click="bulkPublish" x-bind:disabled="!selectedCourses.length"
            class="bg-accent-themed-primary/20 hover:bg-accent-themed-primary/40 text-accent-themed-primary disabled:opacity-50 disabled:cursor-not-allowed px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-300 flex items-center transform hover:scale-105">
            <i class="fas fa-globe mr-1.5"></i> Publish
        </button>
        <button wire:click="bulkUnpublish" x-bind:disabled="!selectedCourses.length"
            class="bg-themed-tertiary hover:bg-themed-secondary text-themed-primary disabled:opacity-50 disabled:cursor-not-allowed px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-300 flex items-center transform hover:scale-105 border border-themed-primary">
            <i class="fas fa-eye-slash mr-1.5"></i> Unpublish
        </button>
        <button wire:click="bulkDelete" x-bind:disabled="!selectedCourses.length"
            class="bg-red-100/50 hover:bg-red-200/50 text-red-600 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-300 flex items-center transform hover:scale-105">
            <i class="fas fa-trash-alt mr-1.5"></i> Delete
        </button>
    </div>

    <!-- Course Grid -->
    @if ($courses->isEmpty())
        <div class="text-center py-12 bg-themed-secondary rounded-xl shadow-md border border-themed-primary transition-colors duration-300">
            <i class="fas fa-book-open text-4xl text-themed-tertiary mb-4"></i>
            <h3 class="text-base font-bold text-themed-primary mb-2 transition-colors duration-300">You haven't created any courses yet</h3>
            <p class="text-themed-secondary mb-6 text-sm transition-colors duration-300">Start by creating your first course to share your knowledge with students.</p>
            <a href="{{ route('create.course') }}"
                class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2.5 rounded-lg font-semibold transition-colors duration-300 flex items-center justify-center mx-auto w-fit text-sm transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i> Create Your First Course
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            @foreach ($courses as $course)
                <div class="bg-themed-secondary rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-themed-primary group"
                     style="animation-delay: {{ $loop->index * 0.05 }}s">
                    
                    <!-- Course Thumbnail -->
                    <div class="relative h-36 bg-gradient-to-br from-accent-themed-primary to-purple-600 overflow-hidden">
                        @if ($course->thumbnail)
                            <img src="{{ asset('storage/' . ($course->thumbnail ?? 'images/default-course.png')) }}"
                                alt="{{ $course->title }}" 
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book-open text-white text-3xl opacity-60"></i>
                            </div>
                        @endif
                        
                        <!-- Selection Checkbox -->
                        <div class="absolute top-3 left-3">
                            <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}"
                                class="h-4 w-4 text-accent-themed-primary rounded border-2 border-white focus:ring-accent-themed-primary shadow-md">
                        </div>
                        
                        <!-- Enrollment Count Badge -->
                        <div class="absolute bottom-3 left-3 bg-black/70 text-white text-xs px-2.5 py-1 rounded-full font-semibold">
                            <i class="fas fa-users mr-1"></i> {{ $course->enrollments_count }}
                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="p-4">
                        <!-- Title -->
                        <h3 class="font-bold text-themed-primary text-sm mb-2 line-clamp-2 group-hover:text-accent-themed-primary transition-colors duration-300">
                            {{ Str::limit($course->title, 50) }}
                        </h3>

                        <!-- Status Badges -->
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        @if ($course->difficulty_level === 'beginner') bg-green-100/50 text-green-800
                                        @elseif($course->difficulty_level === 'intermediate') bg-yellow-100/50 text-yellow-800
                                        @else bg-red-100/50 text-red-800 @endif
                                        transition-colors duration-300">
                                {{ substr(ucfirst($course->difficulty_level), 0, 3) }}
                            </span>

                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        {{ $course->is_published ? 'bg-accent-themed-primary/20 text-accent-themed-primary' : 'bg-themed-tertiary text-themed-secondary' }}
                                        transition-colors duration-300">
                                {{ $course->is_published ? 'Published' : 'Draft' }}
                            </span>

                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        {{ $course->is_approved ? 'bg-green-100/50 text-green-800' : 'bg-yellow-100/50 text-yellow-800' }}
                                        transition-colors duration-300">
                                {{ $course->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-themed-secondary mb-1">
                                <span>Completion</span>
                                <span>{{ $course->completion_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-2 overflow-hidden border border-themed-primary">
                                <div class="bg-gradient-to-r from-accent-themed-primary to-purple-500 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ $course->completion_percentage ?? 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center pt-3 border-t border-themed-primary">
                            <div class="flex space-x-2">
                                <a href="{{ route('course-builder', $course) }}"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary hover:bg-accent-themed-primary/20 text-sm p-2 rounded-lg transition-all duration-300"
                                    title="Build Course">
                                    <i class="fas fa-cog"></i>
                                </a>

                                <button wire:click="editCourse({{ $course->id }})"
                                    class="text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary text-sm p-2 rounded-lg transition-all duration-300"
                                    title="Edit Course">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <a href="{{ route('courses.preview', $course->slug) }}"
                                    target="_blank"
                                    class="text-purple-600 hover:text-purple-700 hover:bg-purple-100/50 text-sm p-2 rounded-lg transition-all duration-300"
                                    title="Preview Course">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <button wire:click="togglePublished({{ $course->id }})"
                                    class="text-sm p-2 rounded-lg transition-all duration-300 {{ $course->is_published ? 'text-accent-themed-primary hover:bg-accent-themed-primary/20' : 'text-themed-secondary hover:bg-themed-tertiary' }}"
                                    title="{{ $course->is_published ? 'Unpublish' : 'Publish' }}">
                                    <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }}"></i>
                                </button>
                            </div>

                            <button wire:click="deleteCourse({{ $course->id }})"
                                wire:confirm="Are you sure you want to delete this course and all its content?"
                                class="text-red-600 hover:text-red-700 hover:bg-red-100/50 text-sm p-2 rounded-lg transition-all duration-300"
                                title="Delete Course">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 bg-themed-secondary rounded-xl shadow-md p-4 border border-themed-primary transition-colors duration-300">
            {{ $courses->links('pagination::tailwind') }}
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-themed-secondary rounded-xl p-6 sm:p-8 flex flex-col items-center shadow-xl border border-themed-primary transition-colors duration-300">
            <div class="relative mb-4">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-themed-tertiary"></div>
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-accent-themed-primary border-t-transparent absolute top-0"></div>
            </div>
            <span class="text-themed-primary font-semibold text-sm transition-colors duration-300">Processing...</span>
        </div>
    </div>
</div>