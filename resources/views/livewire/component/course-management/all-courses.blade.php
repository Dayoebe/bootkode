<div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white max-w-7xl mx-auto my-8">
    <h2 class="text-3xl font-extrabold text-white mb-6 border-b border-gray-700 pb-4">
        All Courses
    </h2>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between space-y-4 sm:space-y-0 sm:space-x-4">
        <div class="w-full sm:w-1/3">
            <label for="search" class="sr-only">Search Courses</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Search courses..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm">
            </div>
        </div>

        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full sm:w-2/3 justify-end">
            <div class="w-full sm:w-1/3">
                <label for="categoryFilter" class="sr-only">Filter by Category</label>
                <select id="categoryFilter" wire:model.live="categoryFilter"
                        class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white shadow-sm">
                    <option value="">All Categories</option>
                    @forelse($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @empty
                        <option value="" disabled>No Categories Found</option>
                    @endforelse
                </select>
            </div>

            <div class="w-full sm:w-1/3">
                <label for="statusFilter" class="sr-only">Filter by Status</label>
                <select id="statusFilter" wire:model.live="statusFilter"
                        class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white shadow-sm">
                    <option value="">All Statuses</option>
                    <option value="published">Published</option>
                    <option value="unpublished">Unpublished</option>
                    <option value="approved">Approved</option>
                    <option value="unapproved">Unapproved</option>
                </select>
            </div>

            <div class="w-full sm:w-1/3">
                <label for="difficultyFilter" class="sr-only">Filter by Difficulty</label>
                <select id="difficultyFilter" wire:model.live="difficultyFilter"
                        class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white shadow-sm">
                    <option value="">All Difficulties</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
        </div>
    </div>

    @if ($courses->isEmpty())
        <div class="text-center py-10">
            <p class="text-gray-400 text-lg">No courses found matching your criteria.</p>
        </div>
    @else
        <div x-data="{ selectedCourses: @entangle('selectedCourses') }"
             x-cloak
             x-show="selectedCourses.length > 0"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-2xl z-50 p-4">
            <div class="bg-gray-700 border border-gray-600 rounded-2xl shadow-2xl p-4 flex justify-between items-center space-x-4">
                <span class="text-gray-300 font-medium whitespace-nowrap">{{ count($selectedCourses) }} selected</span>
                <div class="flex space-x-3">
                    <button wire:click="bulkPublish" wire:confirm="Are you sure you want to publish the selected courses?"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors duration-200">
                        <i class="fas fa-upload mr-2"></i>Publish
                    </button>
                    <button wire:click="bulkApprove" wire:confirm="Are you sure you want to approve the selected courses?"
                            class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-xl text-sm font-semibold transition-colors duration-200">
                        <i class="fas fa-check-circle mr-2"></i>Approve
                    </button>
                    <button wire:click="bulkDelete" wire:confirm="Are you sure you want to delete the selected courses and all their content?"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-colors duration-200">
                        <i class="fas fa-trash-alt mr-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-1/12">
                            <input type="checkbox" wire:model.live="selectAll" class="form-checkbox text-blue-600 bg-gray-800 border-gray-600 rounded focus:ring-blue-500">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-3/12">
                            Title
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-2/12">
                            Instructor
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-1/12">
                            Category
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-1/12">
                            Sections
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-1/12">
                            Difficulty
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-1/12">
                            Published
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-1/12">
                            Approved
                        </th>
                        <th scope="col" class="relative px-6 py-3 w-2/12">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700">
                    @foreach ($courses as $course)
                        <tr class="hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:model.live="selectedCourses" value="{{ $course->id }}" class="form-checkbox text-blue-600 bg-gray-800 border-gray-600 rounded focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ $course->title }}</div>
                                <div class="text-xs text-gray-400">{{ Str::limit($course->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ $course->instructor->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ $course->category->name ?? 'Uncategorized' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $course->sections->pluck('title')->join(', ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $course->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $course->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $course->difficulty_level === 'advanced' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($course->difficulty_level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="togglePublished({{ $course->id }})"
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $course->is_published ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-600 text-gray-300 hover:bg-gray-500' }}
                                        transition-colors duration-200">
                                    {{ $course->is_published ? 'Published' : 'Unpublished' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if (Auth::user()->isSuperAdmin() || Auth::user()->isAcademyAdmin())
                                    <button wire:click="toggleApproved({{ $course->id }})"
                                            class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $course->status === 'approved' ? 'bg-pink-600 text-white hover:bg-pink-700' : 'bg-gray-600 text-gray-300 hover:bg-gray-500' }}
                                            transition-colors duration-200">
                                        {{ $course->status === 'approved' ? 'Approved' : 'Unapproved' }}
                                    </button>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $course->status === 'approved' ? 'bg-pink-600/30 text-pink-300' : 'bg-yellow-600/30 text-yellow-300' }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                @endif
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

        <div class="mt-6">
            {{ $courses->links('pagination::tailwind') }}
        </div>
    @endif
</div>