<div class="bg-themed-primary min-h-screen transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Page Header -->
        <div
            class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 animate__animated animate__fadeInDown transition-colors duration-300">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1
                        class="text-2xl sm:text-3xl font-bold text-themed-primary flex items-center gap-3 transition-colors duration-300">
                        <div class="bg-accent-themed-primary/10 p-3 rounded-xl">
                            <i class="fas fa-book-open text-accent-themed-primary text-xl"></i>
                        </div>
                        My Courses
                    </h1>
                    <p class="text-themed-secondary mt-2 transition-colors duration-300">
                        Manage your courses and track their performance
                    </p>
                </div>

                <a href="{{ route('create.course') }}"
                    class="inline-flex items-center justify-center gap-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-plus"></i>
                    <span>New Course</span>
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Total Courses -->
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-6 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp"
                style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-themed-secondary text-sm font-medium mb-1 transition-colors duration-300">
                            Total Courses
                        </p>
                        <p class="text-3xl font-bold text-themed-primary transition-colors duration-300">
                            {{ $courses->total() }}
                        </p>
                    </div>
                    <div class="bg-accent-themed-primary/10 p-4 rounded-xl transition-colors duration-300">
                        <i class="fas fa-book text-accent-themed-primary text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Enrollments -->
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-6 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp"
                style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-themed-secondary text-sm font-medium mb-1 transition-colors duration-300">
                            Total Enrollments
                        </p>
                        <p class="text-3xl font-bold text-themed-primary transition-colors duration-300">
                            {{ $totalEnrollments }}
                        </p>
                    </div>
                    <div class="bg-green-500/10 p-4 rounded-xl transition-colors duration-300">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Published Courses -->
            <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-6 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp"
                style="animation-delay: 0.3s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-themed-secondary text-sm font-medium mb-1 transition-colors duration-300">
                            Published
                        </p>
                        <p class="text-3xl font-bold text-themed-primary transition-colors duration-300">
                            {{ $publishedCount }} / {{ $courses->total() }}
                        </p>
                    </div>
                    <div class="bg-purple-500/10 p-4 rounded-xl transition-colors duration-300">
                        <i class="fas fa-globe text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div
            class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-6 transition-colors duration-300 animate__animated animate__fadeIn">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-themed-primary transition-colors duration-300">
                    Filters & Search
                </h3>
                <button wire:click="$set('search', '')"
                    class="text-sm text-accent-themed-primary hover:text-accent-themed-secondary font-medium flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-redo-alt"></i>
                    Clear Filters
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search courses..."
                        class="w-full pl-10 pr-4 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                    <div
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <!-- Category Filter -->
                <select wire:model.live="categoryFilter"
                    class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 font-medium focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter"
                    class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 font-medium focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                    <option value="">All Statuses</option>
                    <option value="published">Published</option>
                    <option value="unpublished">Unpublished</option>
                    <option value="approved">Approved</option>
                    <option value="unapproved">Pending Approval</option>
                </select>

                <!-- Difficulty Filter -->
                <select wire:model.live="difficultyFilter"
                    class="bg-themed-tertiary border-2 border-themed-primary text-themed-primary rounded-xl px-4 py-3 font-medium focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="flex flex-wrap gap-3" x-data="{ selectedCourses: @entangle('selectedCourses') }">
            <button wire:click="bulkPublish" x-bind:disabled="!selectedCourses.length"
                class="inline-flex items-center gap-2 bg-accent-themed-primary/10 hover:bg-accent-themed-primary/20 text-accent-themed-primary disabled:opacity-50 disabled:cursor-not-allowed px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-globe"></i>
                Publish
            </button>
            <button wire:click="bulkUnpublish" x-bind:disabled="!selectedCourses.length"
                class="inline-flex items-center gap-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary disabled:opacity-50 disabled:cursor-not-allowed px-4 py-2 rounded-lg font-medium border border-themed-primary transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-eye-slash"></i>
                Unpublish
            </button>
            <button wire:click="bulkDelete" x-bind:disabled="!selectedCourses.length"
                class="inline-flex items-center gap-2 bg-red-100/50 hover:bg-red-200/50 text-red-600 disabled:opacity-50 disabled:cursor-not-allowed px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-trash-alt"></i>
                Delete
            </button>
        </div>

        <!-- Course Grid -->
        @if ($courses->isEmpty())
            <div
                class="text-center py-16 bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary transition-colors duration-300">
                <div
                    class="bg-accent-themed-primary/10 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300">
                    <i class="fas fa-book-open text-4xl text-accent-themed-primary"></i>
                </div>
                <h3 class="text-xl font-bold text-themed-primary mb-2 transition-colors duration-300">
                    No courses yet
                </h3>
                <p class="text-themed-secondary mb-6 transition-colors duration-300">
                    Start by creating your first course to share your knowledge.
                </p>
                <a href="{{ route('create.course') }}"
                    class="inline-flex items-center gap-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-plus"></i>
                    Create Your First Course
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($courses as $course)
                    <div class="bg-themed-secondary rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-themed-primary group animate__animated animate__fadeInUp"
                        style="animation-delay: {{ $loop->index * 0.05 }}s">

                        <!-- Course Thumbnail -->
                        <div class="relative h-44 bg-gradient-to-br from-accent-themed-primary to-purple-600 overflow-hidden">
                            @if ($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-book-open text-white text-4xl opacity-60"></i>
                                </div>
                            @endif

                            <!-- Selection Checkbox -->
                            <div class="absolute top-3 left-3">
                                <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}"
                                    class="h-5 w-5 text-accent-themed-primary rounded border-2 border-white focus:ring-accent-themed-primary shadow-md cursor-pointer">
                            </div>

                            <!-- Enrollment Badge -->
                            <div
                                class="absolute bottom-3 left-3 bg-black/70 text-white text-xs px-3 py-1 rounded-full font-semibold backdrop-blur-sm">
                                <i class="fas fa-users mr-1"></i> {{ $course->enrollments_count }}
                            </div>
                        </div>

                        <!-- Course Content -->
                        <div class="p-5">
                            <!-- Title -->
                            <h3
                                class="font-bold text-themed-primary text-base mb-3 line-clamp-2 group-hover:text-accent-themed-primary transition-colors duration-300">
                                {{ Str::limit($course->title, 50) }}
                            </h3>

                            <!-- Status Badges -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold transition-colors duration-300
                                                    @if ($course->difficulty_level === 'beginner') bg-green-100/50 text-green-800
                                                    @elseif($course->difficulty_level === 'intermediate') bg-yellow-100/50 text-yellow-800
                                                    @else bg-red-100/50 text-red-800 @endif">
                                    {{ ucfirst($course->difficulty_level) }}
                                </span>

                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-semibold transition-colors duration-300
                                                    {{ $course->is_published ? 'bg-accent-themed-primary/20 text-accent-themed-primary' : 'bg-themed-tertiary text-themed-secondary' }}">
                                    {{ $course->is_published ? 'Published' : 'Draft' }}
                                </span>

                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-semibold transition-colors duration-300
                                                    {{ $course->is_approved ? 'bg-green-100/50 text-green-800' : 'bg-yellow-100/50 text-yellow-800' }}">
                                    {{ $course->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div
                                    class="flex justify-between text-xs text-themed-secondary mb-2 transition-colors duration-300">
                                    <span class="font-medium">Completion</span>
                                    <span class="font-bold">{{ $course->completion_percentage ?? 0 }}%</span>
                                </div>
                                <div
                                    class="w-full bg-themed-tertiary rounded-full h-2 overflow-hidden border border-themed-primary transition-colors duration-300">
                                    <div class="bg-gradient-to-r from-accent-themed-primary to-purple-500 h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $course->completion_percentage ?? 0 }}%"></div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div
                                class="flex items-center justify-between pt-4 border-t border-themed-primary transition-colors duration-300">
                                <div class="flex gap-2">
                                    <a href="{{ route('course-builder', $course) }}"
                                        class="p-2 rounded-lg text-accent-themed-primary hover:bg-accent-themed-primary/10 transition-all duration-300 transform hover:scale-110"
                                        title="Build Course">
                                        <i class="fas fa-cog"></i>
                                    </a>

                                    <button wire:click="editCourse({{ $course->id }})"
                                        class="p-2 rounded-lg text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary transition-all duration-300 transform hover:scale-110"
                                        title="Edit Course">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <a href="{{ route('courses.preview', $course->slug) }}" target="_blank"
                                        class="p-2 rounded-lg text-purple-600 hover:bg-purple-100/50 transition-all duration-300 transform hover:scale-110"
                                        title="Preview Course">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button wire:click="togglePublished({{ $course->id }})"
                                        class="p-2 rounded-lg transition-all duration-300 transform hover:scale-110 {{ $course->is_published ? 'text-accent-themed-primary hover:bg-accent-themed-primary/10' : 'text-themed-secondary hover:bg-themed-tertiary' }}"
                                        title="{{ $course->is_published ? 'Unpublish' : 'Publish' }}">
                                        <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }}"></i>
                                    </button>
                                </div>

                                <button wire:click="deleteCourse({{ $course->id }})"
                                    wire:confirm="Are you sure you want to delete this course?"
                                    class="p-2 rounded-lg text-red-600 hover:bg-red-100/50 transition-all duration-300 transform hover:scale-110"
                                    title="Delete Course">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div
                class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-4 transition-colors duration-300">
                {{ $courses->links('pagination::tailwind') }}
            </div>
        @endif

        <!-- Loading Spinner -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div
                class="bg-themed-secondary rounded-2xl p-8 flex flex-col items-center shadow-2xl border border-themed-primary transition-colors duration-300">
                <div class="relative mb-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-themed-tertiary"></div>
                    <div
                        class="animate-spin rounded-full h-16 w-16 border-4 border-accent-themed-primary border-t-transparent absolute top-0">
                    </div>
                </div>
                <span class="text-themed-primary font-semibold transition-colors duration-300">Processing...</span>
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