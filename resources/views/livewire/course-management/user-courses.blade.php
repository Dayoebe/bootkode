<div class="bg-white dark:bg-gray-900 p-4 sm:p-6 rounded-xl shadow-sm">
    <!-- Header Section with Stats -->
    <div
        class="flex flex-col md:flex-row md:items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-book-open mr-2 sm:mr-3 text-blue-500"></i> My Courses
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">Manage your courses and track their performance</p>
        </div>

        <a href="{{ route('create.course') }}"
            class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors flex items-center text-sm">
            <i class="fas fa-plus mr-1 sm:mr-2"></i> New Course
        </a>
    </div>

    <!-- Stats Cards - More Compact -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 sm:p-4 rounded-lg border border-blue-100 dark:border-blue-800">
            <div class="flex items-center">
                <div class="bg-blue-100 dark:bg-blue-800 p-2 sm:p-3 rounded-lg mr-3">
                    <i class="fas fa-book text-blue-600 dark:text-blue-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Total Courses</p>
                    <p class="text-lg sm:text-xl font-bold text-blue-800 dark:text-blue-200">{{ $courses->total() }}</p>
                </div>
            </div>
        </div>

        <div
            class="bg-green-50 dark:bg-green-900/20 p-3 sm:p-4 rounded-lg border border-green-100 dark:border-green-800">
            <div class="flex items-center">
                <div class="bg-green-100 dark:bg-green-800 p-2 sm:p-3 rounded-lg mr-3">
                    <i class="fas fa-users text-green-600 dark:text-green-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-green-600 dark:text-green-400 font-medium">Total Enrollments</p>
                    <p class="text-lg sm:text-xl font-bold text-green-800 dark:text-green-200">{{ $totalEnrollments }}
                    </p>
                </div>
            </div>
        </div>

        <div
            class="bg-purple-50 dark:bg-purple-900/20 p-3 sm:p-4 rounded-lg border border-purple-100 dark:border-purple-800">
            <div class="flex items-center">
                <div class="bg-purple-100 dark:bg-purple-800 p-2 sm:p-3 rounded-lg mr-3">
                    <i class="fas fa-globe text-purple-600 dark:text-purple-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Published Courses</p>
                    <p class="text-lg sm:text-xl font-bold text-purple-800 dark:text-purple-200">{{ $publishedCount }} /
                        {{ $courses->total() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section - More Compact -->
    <div class="mb-5 bg-gray-50 dark:bg-gray-800 p-3 sm:p-4 rounded-lg">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Filters</h3>
            <button wire:click="$set('search', '')"
                class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                <i class="fas fa-redo-alt mr-1 text-xs"></i> Clear Filters
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search your courses..."
                    class="w-full pl-8 pr-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm">
                <div class="absolute left-2.5 top-2.5 text-gray-400 text-sm">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <select wire:model.live="categoryFilter"
                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter"
                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm">
                <option value="">All Statuses</option>
                <option value="published">Published</option>
                <option value="unpublished">Unpublished</option>
                <option value="approved">Approved</option>
                <option value="unapproved">Pending Approval</option>
            </select>

            <select wire:model.live="difficultyFilter"
                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm">
                <option value="">All Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>
    </div>

    <!-- Bulk Actions - More Compact -->
    <div class="mb-5 flex flex-wrap gap-2" x-data="{ selectedCourses: @entangle('selectedCourses') }">
        <button wire:click="bulkPublish" x-bind:disabled="!selectedCourses.length"
            class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-2.5 py-1.5 rounded text-xs font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
            <i class="fas fa-globe mr-1.5 text-xs"></i> Publish
        </button>
        <button wire:click="bulkUnpublish" x-bind:disabled="!selectedCourses.length"
            class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-2.5 py-1.5 rounded text-xs font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
            <i class="fas fa-eye-slash mr-1.5 text-xs"></i> Unpublish
        </button>
        <button wire:click="bulkDelete" x-bind:disabled="!selectedCourses.length"
            class="bg-red-100 hover:bg-red-200 text-red-800 px-2.5 py-1.5 rounded text-xs font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
            <i class="fas fa-trash-alt mr-1.5 text-xs"></i> Delete
        </button>
    </div>

    <!-- Course Grid - More Compact Cards -->
    @if ($courses->isEmpty())
        <div
            class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
            <i class="fas fa-book-open text-3xl text-gray-400 mb-3"></i>
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">You haven't created any courses yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4 text-sm">Start by creating your first course to share your
                knowledge
                with students.</p>
            <a href="{{ route('create.course') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg font-medium transition-colors flex items-center justify-center mx-auto w-fit text-sm">
                <i class="fas fa-plus mr-1.5"></i> Create Your First Course
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            @foreach ($courses as $course)
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <!-- Course Header with Image -->
                    <div class="relative h-32 bg-gradient-to-r from-blue-500 to-purple-600">
                        @if ($course->thumbnail)
                            <img src="{{ asset('storage/' . ($course->thumbnail ?? 'images/default-course.png')) }}"
                                alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book-open text-white text-3xl opacity-50"></i>
                            </div>
                        @endif
                        <div class="absolute top-2 left-2">
                            <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}"
                                class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        </div>
                        <div class="absolute top-2 right-2 flex gap-1">
                            <span class="bg-blue-600 text-white text-xs px-1.5 py-0.5 rounded-full">
                                {{ Str::limit($course->category->name ?? 'Uncategorized', 12) }}
                            </span>
                        </div>
                        <div class="absolute bottom-2 left-2 bg-black/70 text-white text-xs px-1.5 py-0.5 rounded-full">
                            <i class="fas fa-users mr-0.5"></i> {{ $course->enrollments_count }}
                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1.5 line-clamp-2">
                            {{ Str::limit($course->title, 50) }}
                        </h3>

                        <div class="flex flex-wrap gap-1 mb-3">
                            <span class="px-1.5 py-0.5 rounded-full text-xs font-medium 
                                        @if ($course->difficulty_level === 'beginner') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($course->difficulty_level === 'intermediate') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ substr(ucfirst($course->difficulty_level), 0, 3) }}
                            </span>

                            <span
                                class="px-1.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $course->is_published ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                {{ $course->is_published ? 'Published' : 'Draft' }}
                            </span>

                            <span
                                class="px-1.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $course->is_approved ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ $course->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>

                        <!-- Progress Bar for Course Completion -->
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>Progress</span>
                                <span>{{ $course->completion_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="bg-blue-600 h-1.5 rounded-full"
                                    style="width: {{ $course->completion_percentage ?? 0 }}%"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex space-x-1.5">
                                <a href="{{ route('course-builder', $course) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                                    title="Build Course">
                                    <i class="fas fa-cog"></i>
                                </a>

                                <button wire:click="editCourse({{ $course->id }})"
                                    class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 text-sm"
                                    title="Edit Course">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <a href="{{ route('courses.preview', $course->slug) }}"
                                class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 text-sm"
                                title="Preview Course">
                                 <i class="fas fa-eye"></i>
                             </a>

                                <button wire:click="togglePublished({{ $course->id }})"
                                    class="{{ $course->is_published ? 'text-blue-600 hover:text-blue-800' : 'text-gray-600 hover:text-gray-800' }} dark:text-gray-400 dark:hover:text-gray-300 text-sm"
                                    title="{{ $course->is_published ? 'Unpublish' : 'Publish' }}">
                                    <i class="fas fa-{{ $course->is_published ? 'eye' : 'eye-slash' }}"></i>
                                </button>
                            </div>

                            <button wire:click="deleteCourse({{ $course->id }})"
                                wire:confirm="Are you sure you want to delete this course and all its content?"
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm"
                                title="Delete Course">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-5">
            {{ $courses->links('pagination::tailwind') }}
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-5 flex items-center shadow-lg">
            <i class="fas fa-spinner fa-spin text-blue-500 text-lg mr-2 sm:mr-3"></i>
            <span class="text-gray-800 dark:text-white text-sm">Processing...</span>
        </div>
    </div>
</div>