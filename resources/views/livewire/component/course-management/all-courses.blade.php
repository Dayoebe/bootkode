<div class="bg-gradient-to-br from-blue-900 to-teal-600 p-8 rounded-2xl shadow-2xl text-white animate__animated animate__fadeIn" x-data="{ tooltip: '' }">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-800 text-green-200 rounded-xl animate__animated animate__fadeIn">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-800 text-red-200 rounded-xl animate__animated animate__fadeIn">
            {{ session('error') }}
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <i class="fas fa-spinner fa-spin text-teal-500 text-3xl" aria-label="Loading"></i>
    </div>

    <!-- Header -->
    <h2 class="text-4xl font-bold text-indigo-100 mb-8 border-b-2 border-teal-500 pb-4 flex items-center">
        <i class="fas fa-book mr-3 text-teal-300"></i> All Courses
    </h2>

    <!-- Empty State for Instructors -->
    @if (Auth::user()->hasRole('instructor') && $courses->isEmpty())
        <div class="text-center py-12 bg-indigo-800 rounded-xl shadow-lg animate__animated animate__fadeInUp">
            <i class="fas fa-book-open text-5xl text-teal-300 mb-4"></i>
            <h3 class="text-2xl font-semibold text-indigo-100 mb-4">No Courses Yet!</h3>
            <p class="text-teal-300 mb-6 max-w-md mx-auto">
                It looks like you haven't created any courses yet. Follow these steps to get started:
            </p>
            <ol class="list-decimal list-inside text-indigo-200 mb-6 max-w-md mx-auto text-left">
                <li>Click the "Create New Course" button below.</li>
                <li>Fill in the course details, such as title, description, and category.</li>
                <li>Add sections, lessons, and assessments using the course builder.</li>
                <li>Submit for approval to make it available to students!</li>
            </ol>
            <a href="{{ route('course_management.create_course') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition duration-300 shadow-md flex items-center justify-center mx-auto animate__animated animate__pulse animate__infinite"
               aria-label="Create a new course">
                <i class="fas fa-plus mr-2"></i> Create Your First Course
            </a>
        </div>
    @else
        <!-- Filters -->
        <div class="mb-8 flex flex-col lg:flex-row lg:items-end lg:justify-between space-y-6 lg:space-y-0 lg:space-x-6">
            <div class="w-full lg:w-1/4">
                <label for="search" class="sr-only">Search Courses</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-teal-300"></i>
                    </div>
                    <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Search courses..."
                           class="w-full pl-10 pr-4 py-3 bg-indigo-800 border border-teal-600 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-white placeholder-teal-300 shadow-md transition-all duration-300 hover:shadow-lg">
                </div>
            </div>

            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 w-full lg:w-3/4 justify-end">
                <div class="w-full md:w-1/5">
                    <label for="categoryFilter" class="sr-only">Filter by Category</label>
                    <select id="categoryFilter" wire:model.live="categoryFilter"
                            class="w-full px-4 py-3 bg-indigo-800 border border-teal-600 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-white shadow-md transition-all duration-300 hover:shadow-lg">
                        <option value="">All Categories</option>
                        @forelse($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @empty
                            <option value="" disabled>No Categories Found</option>
                        @endforelse
                    </select>
                </div>

                <div class="w-full md:w-1/5">
                    <label for="statusFilter" class="sr-only">Filter by Status</label>
                    <select id="statusFilter" wire:model.live="statusFilter"
                            class="w-full px-4 py-3 bg-indigo-800 border border-teal-600 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-white shadow-md transition-all duration-300 hover:shadow-lg">
                        <option value="">All Statuses</option>
                        <option value="published">Published</option>
                        <option value="unpublished">Unpublished</option>
                        <option value="approved">Approved</option>
                        <option value="unapproved">Unapproved</option>
                    </select>
                </div>

                <div class="w-full md:w-1/5">
                    <label for="difficultyFilter" class="sr-only">Filter by Difficulty</label>
                    <select id="difficultyFilter" wire:model.live="difficultyFilter"
                            class="w-full px-4 py-3 bg-indigo-800 border border-teal-600 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-white shadow-md transition-all duration-300 hover:shadow-lg">
                        <option value="">All Difficulties</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                @can('view-courses', ['super_admin'])
                    <div class="w-full md:w-1/5">
                        <label for="instructorFilter" class="sr-only">Filter by Instructor</label>
                        <select id="instructorFilter" wire:model.live="instructorFilter"
                                class="w-full px-4 py-3 bg-indigo-800 border border-teal-600 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-white shadow-md transition-all duration-300 hover:shadow-lg">
                            <option value="">All Instructors</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endcan

                <div class="w-full md:w-1/5">
                    <label for="perPage" class="sr-only">Items per page</label>
                    <select id="perPage" wire:model.live="perPage"
                            class="w-full px-4 py-3 bg-indigo-800 border border-teal-600 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-white shadow-md transition-all duration-300 hover:shadow-lg">
                        <option value="10">10 per page</option>
                        <option value="20">20 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="mb-6 flex space-x-4" x-data="{ selectedCourses: @entangle('selectedCourses') }">
            @can('publish-courses')
                <button wire:click="bulkPublish" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition duration-300 disabled:opacity-50 flex items-center" x-bind:disabled="!selectedCourses.length" aria-label="Publish selected courses">
                    <i class="fas fa-globe mr-2"></i> Publish
                </button>
                <button wire:click="bulkUnpublish" class="bg-gray-600 text-white px-4 py-2 rounded-xl hover:bg-gray-700 transition duration-300 disabled:opacity-50 flex items-center" x-bind:disabled="!selectedCourses.length" aria-label="Unpublish selected courses">
                    <i class="fas fa-eye-slash mr-2"></i> Unpublish
                </button>
            @endcan
            @can('approve-courses')
                <button wire:click="bulkApprove" class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700 transition duration-300 disabled:opacity-50 flex items-center" x-bind:disabled="!selectedCourses.length" aria-label="Approve selected courses">
                    <i class="fas fa-check-circle mr-2"></i> Approve
                </button>
            @endcan
            @can('delete-courses')
                <button wire:click="bulkDelete" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 transition duration-300 disabled:opacity-50 flex items-center" x-bind:disabled="!selectedCourses.length" aria-label="Delete selected courses">
                    <i class="fas fa-trash-alt mr-2"></i> Delete
                </button>
            @endcan
            <button wire:click="exportSelected" class="bg-teal-600 text-white px-4 py-2 rounded-xl hover:bg-teal-700 transition duration-300 disabled:opacity-50 flex items-center" x-bind:disabled="!selectedCourses.length" aria-label="Export selected courses">
                <i class="fas fa-download mr-2"></i> Export
            </button>
        </div>

        <!-- Course Table -->
        @if ($courses->isEmpty())
            <div class="text-center py-12">
                <p class="text-teal-300 text-xl animate__animated animate__pulse">No courses found matching your criteria.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl shadow-lg border border-teal-700">
                <table wire:poll.10s class="min-w-full divide-y divide-teal-700" aria-label="Courses Table">
                    <thead class="bg-indigo-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-teal-200 uppercase tracking-wider">
                                <input type="checkbox" x-model="selectAll" aria-label="Select all courses">
                            </th>
                            <th wire:click="sortBy('title')" class="px-6 py-4 text-left text-xs font-medium text-teal-200 uppercase tracking-wider cursor-pointer" aria-sort="{{ $sortField === 'title' ? $sortDirection : 'none' }}">
                                Title @if($sortField === 'title') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-teal-300"></i> @endif
                            </th>
                            <th wire:click="sortBy('instructor.name')" class="px-6 py-4 text-left text-xs font-medium text-teal-200 uppercase tracking-wider cursor-pointer hidden md:table-cell" aria-sort="{{ $sortField === 'instructor.name' ? $sortDirection : 'none' }}">
                                Instructor @if($sortField === 'instructor.name') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-teal-300"></i> @endif
                            </th>
                            <th wire:click="sortBy('category.name')" class="px-6 py-4 text-left text-xs font-medium text-teal-200 uppercase tracking-wider cursor-pointer" aria-sort="{{ $sortField === 'category.name' ? $sortDirection : 'none' }}">
                                Category @if($sortField === 'category.name') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-teal-300"></i> @endif
                            </th>
                            <th wire:click="sortBy('enrollments_count')" class="px-6 py-4 text-left text-xs font-medium text-teal-200 uppercase tracking-wider cursor-pointer" x-on:mouseover="tooltip = 'Number of enrolled students'" x-on:mouseout="tooltip = ''">
                                Enrollments @if($sortField === 'enrollments_count') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-teal-300"></i> @endif
                            </th>
                            <th wire:click="sortBy('difficulty_level')" class="px-6 py-4 text-left text-xs font-medium text-teal-200 uppercase tracking-wider cursor-pointer" aria-sort="{{ $sortField === 'difficulty_level' ? $sortDirection : 'none' }}">
                                Difficulty @if($sortField === 'difficulty_level') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-teal-300"></i> @endif
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-teal-200 uppercase tracking-wider">Published</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-teal-200 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-teal-200 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-indigo-950 divide-y divide-teal-800">
                        @foreach($courses as $course)
                            <tr class="hover:bg-indigo-900 transition duration-200 ease-in-out animate__animated animate__fadeIn">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}" aria-label="Select course {{ $course->title }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-indigo-100">{{ $course->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell text-teal-300">{{ $course->instructor->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-teal-300">{{ $course->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-teal-300">{{ $course->enrollments->count() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                        {{ $course->difficulty_level === 'beginner' ? 'bg-green-800 text-green-300' : '' }}
                                        {{ $course->difficulty_level === 'intermediate' ? 'bg-yellow-800 text-yellow-300' : '' }}
                                        {{ $course->difficulty_level === 'advanced' ? 'bg-red-800 text-red-300' : '' }}"
                                        x-on:mouseover="tooltip = 'Difficulty: {{ ucfirst($course->difficulty_level) }}'" x-on:mouseout="tooltip = ''">
                                        {{ ucfirst($course->difficulty_level) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @can('publish-courses')
                                        <button wire:click="togglePublished({{ $course->id }})" 
                                                class="px-3 py-1 rounded-full text-xs font-semibold transition duration-200
                                                {{ $course->is_published ? 'bg-blue-800 text-blue-300 hover:bg-blue-700' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                                                x-on:mouseover="tooltip = '{{ $course->is_published ? 'Unpublish' : 'Publish' }} course'" x-on:mouseout="tooltip = ''">
                                            {{ $course->is_published ? 'Published' : 'Unpublished' }}
                                        </button>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $course->is_published ? 'bg-blue-800/50 text-blue-300' : 'bg-gray-800/50 text-gray-300' }}">
                                            {{ $course->is_published ? 'Published' : 'Unpublished' }}
                                        </span>
                                    @endcan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @can('approve-courses')
                                        @if (Auth::user()->hasRole('instructor') && $course->instructor_id === Auth::id())
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                {{ $course->status === 'approved' ? 'bg-green-800/50 text-green-300' : 'bg-yellow-800/50 text-yellow-300' }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        @else
                                            <button wire:click="toggleApproved({{ $course->id }})"
                                                    class="px-3 py-1 rounded-full text-xs font-semibold transition duration-200
                                                    {{ $course->status === 'approved' ? 'bg-green-800 text-green-300 hover:bg-green-700' : 'bg-yellow-800 text-yellow-300 hover:bg-yellow-700' }}"
                                                    x-on:mouseover="tooltip = '{{ $course->status === 'approved' ? 'Unapprove' : 'Approve' }} course'" x-on:mouseout="tooltip = ''">
                                                {{ ucfirst($course->status) }}
                                            </button>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $course->status === 'approved' ? 'bg-green-800/50 text-green-300' : 'bg-yellow-800/50 text-yellow-300' }}">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    @endcan
                                </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('course-builder', $course) }}" class="text-blue-400 hover:text-blue-600 mr-3">
                                    <i class="fas fa-edit mr-1"></i> Build Course
                                </a>
                            
                                <button wire:click="editCourse({{ $course->id }})" class="text-yellow-400 hover:text-yellow-600 mr-3">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                                <button wire:click="deleteCourse({{ $course->id }})"
                                        wire:confirm="Are you sure you want to delete this course and all its content?"
                                        class="text-red-400 hover:text-red-600">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete
                                </button>
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination and Create Button -->
            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('course_management.create_course') }}" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition duration-300 shadow-md flex items-center animate__animated animate__pulse animate__infinite" aria-label="Create a new course">
                    <i class="fas fa-plus mr-2"></i> Create New Course
                </a>
                <div>
                    {{ $courses->links('pagination::tailwind') }}
                </div>
            </div>
        @endif

        <!-- Tooltip -->
        <div x-show="tooltip" class="fixed bg-indigo-900 text-white text-sm px-3 py-1 rounded-lg shadow-lg" x-text="tooltip" style="z-index: 1000;"></div>
    @endif
</div>