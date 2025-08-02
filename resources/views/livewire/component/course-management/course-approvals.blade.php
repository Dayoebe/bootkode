<div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white max-w-7xl mx-auto my-8">
    <div class="flex items-center justify-between border-b border-gray-700 pb-4 mb-6">
        <h2 class="text-3xl font-extrabold text-white">Course Approvals</h2>
    </div>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
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
    </div>

    @if ($courses->isEmpty())
        <div class="text-center py-10">
            <p class="text-gray-400 text-lg">No courses are pending for approval.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Course Title
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Instructor
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Submitted
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700">
                    @foreach ($courses as $course)
                        <tr class="hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ Str::limit($course->title, 50) }}</div>
                                <div class="text-xs text-gray-400">{{ Str::limit($course->subtitle, 60) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ $course->instructor->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ $course->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 capitalize">
                                    {{ $course->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="approveCourse({{ $course->id }})"
                                        wire:confirm="Are you sure you want to approve this course?"
                                        class="text-green-400 hover:text-green-600 mr-3">
                                    <i class="fas fa-check-circle mr-1"></i> Approve
                                </button>
                                <button wire:click="rejectCourse({{ $course->id }})"
                                        wire:confirm="Are you sure you want to reject this course?"
                                        class="text-red-400 hover:text-red-600">
                                    <i class="fas fa-times-circle mr-1"></i> Reject
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