<div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-sm">
    <!-- Header Section with Stats -->
    <div
        class="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-4 border-b border-gray-200 dark:border-gray-700">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-book-open mr-3 text-blue-500"></i> My Courses
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your courses and track their performance</p>
        </div>

        <a href="{{ route('create_course') }}"
            class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i> New Course
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-5 rounded-lg border border-blue-100 dark:border-blue-800">
            <div class="flex items-center">
                <div class="bg-blue-100 dark:bg-blue-800 p-3 rounded-lg mr-4">
                    <i class="fas fa-book text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Courses</p>
                    <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">{{ $courses->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 p-5 rounded-lg border border-green-100 dark:border-green-800">
            <div class="flex items-center">
                <div class="bg-green-100 dark:bg-green-800 p-3 rounded-lg mr-4">
                    <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Total Enrollments</p>
                    <p class="text-2xl font-bold text-green-800 dark:text-green-200">{{ $totalEnrollments }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 dark:bg-purple-900/20 p-5 rounded-lg border border-purple-100 dark:border-purple-800">
            <div class="flex items-center">
                <div class="bg-purple-100 dark:bg-purple-800 p-3 rounded-lg mr-4">
                    <i class="fas fa-globe text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Published Courses</p>
                    <p class="text-2xl font-bold text-purple-800 dark:text-purple-200">{{ $publishedCount }} /
                        {{ $courses->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mb-6 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-medium text-gray-900 dark:text-white">Filters</h3>
            <button wire:click="$set('search', '')"
                class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                <i class="fas fa-redo-alt mr-1 text-xs"></i> Clear Filters
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search your courses..."
                    class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <select wire:model.live="categoryFilter"
                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter"
                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5">
                <option value="">All Statuses</option>
                <option value="published">Published</option>
                <option value="unpublished">Unpublished</option>
                <option value="approved">Approved</option>
                <option value="unapproved">Pending Approval</option>
            </select>

            <select wire:model.live="difficultyFilter"
                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2.5">
                <option value="">All Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-6 flex flex-wrap gap-2" x-data="{ selectedCourses: @entangle('selectedCourses') }">
        <button wire:click="bulkPublish" x-bind:disabled="!selectedCourses.length"
            class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
            <i class="fas fa-globe mr-1.5"></i> Publish Selected
        </button>
        <button wire:click="bulkUnpublish" x-bind:disabled="!selectedCourses.length"
            class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
            <i class="fas fa-eye-slash mr-1.5"></i> Unpublish Selected
        </button>
        <button wire:click="bulkDelete" x-bind:disabled="!selectedCourses.length"
            class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
            <i class="fas fa-trash-alt mr-1.5"></i> Delete Selected
        </button>
    </div>

    <!-- Course Grid -->
    @if ($courses->isEmpty())
        <div
            class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
            <i class="fas fa-book-open text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">You haven't created any courses yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Start by creating your first course to share your knowledge
                with students.</p>
            <a href="{{ route('create_course') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition-colors flex items-center justify-center mx-auto w-fit">
                <i class="fas fa-plus mr-2"></i> Create Your First Course
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @foreach ($courses as $course)
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <!-- Course Header with Image -->
                    <div class="relative h-40 bg-gradient-to-r from-blue-500 to-purple-600">
                        @if ($course->thumbnail)
                            <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book-open text-white text-4xl opacity-50"></i>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3">
                            <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}"
                                class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        </div>
                        <div class="absolute top-3 right-3 flex gap-2">
                            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                {{ $course->category->name ?? 'Uncategorized' }}
                            </span>
                        </div>
                        <div class="absolute bottom-3 left-3 bg-black/70 text-white text-xs px-2 py-1 rounded-full">
                            <i class="fas fa-users mr-1"></i> {{ $course->enrollments_count }} students
                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg mb-2 line-clamp-2">
                            {{ $course->title }}</h3>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium 
                                @if ($course->difficulty_level === 'beginner') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($course->difficulty_level === 'intermediate') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ ucfirst($course->difficulty_level) }}
                            </span>

                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $course->is_published ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                {{ $course->is_published ? 'Published' : 'Unpublished' }}
                            </span>

                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $course->is_approved ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ $course->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>

                        <!-- Progress Bar for Course Completion -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>Course Completion</span>
                                <span>{{ $course->completion_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ $course->completion_percentage ?? 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex space-x-2">
                                <a href="{{ route('course-builder', $course) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Build Course">
                                    <i class="fas fa-cog"></i>
                                </a>

                                <button wire:click="editCourse({{ $course->id }})"
                                    class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                    title="Edit Course">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button wire:click="togglePublished({{ $course->id }})"
                                    class="{{ $course->is_published ? 'text-blue-600 hover:text-blue-800' : 'text-gray-600 hover:text-gray-800' }} dark:text-gray-400 dark:hover:text-gray-300"
                                    title="{{ $course->is_published ? 'Unpublish' : 'Publish' }}">
                                    <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }}"></i>
                                </button>
                            </div>

                            <button wire:click="deleteCourse({{ $course->id }})"
                                wire:confirm="Are you sure you want to delete this course and all its content?"
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                title="Delete Course">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $courses->links('pagination::tailwind') }}
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center shadow-lg">
            <i class="fas fa-spinner fa-spin text-blue-500 text-xl mr-3"></i>
            <span class="text-gray-800 dark:text-white">Processing...</span>
        </div>
    </div>
</div>
