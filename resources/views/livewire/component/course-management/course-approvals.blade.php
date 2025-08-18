<div class="bg-gray-800 p-8 rounded-2xl shadow-2xl text-white animate__animated animate__fadeIn" x-data="{ tooltip: '' }" wire:course-updated.window="$refresh">
    @csrf
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-indigo-600 pb-4 mb-6 animate__animated animate__fadeInDown">
        <h2 class="text-3xl font-extrabold text-white">
            <i class="fas fa-check-circle mr-2 text-green-400" aria-hidden="true"></i> Course Approvals
        </h2>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 animate__animated animate__bounceIn" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 animate__animated animate__shakeX" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 animate__animated animate__fadeIn" style="animation-delay: 0.1s">
        <div class="w-full sm:w-1/3">
            <label for="search" class="sr-only">Search Courses</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-indigo-300"></i>
                </div>
                <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Search courses..."
                       class="w-full pl-10 pr-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-white placeholder-indigo-300 shadow-md transition-all duration-300 hover:shadow-lg"
                       aria-label="Search courses">
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    @if ($courses->isEmpty())
        <div class="text-center py-10 animate__animated animate__fadeIn" style="animation-delay: 0.2s">
            <p class="text-indigo-300 text-lg">No courses are pending for approval.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl shadow-md border border-indigo-600 animate__animated animate__fadeIn" style="animation-delay: 0.2s" wire:loading.class="opacity-50">
            <table class="min-w-full divide-y divide-indigo-600">
                <thead class="bg-indigo-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Course Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Instructor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Submitted</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-indigo-600" wire:loading.remove>
                    <tr wire:loading wire:target="search, $refresh">
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-indigo-300">
                            <i class="fas fa-circle-notch fa-spin mr-2"></i> Loading...
                        </td>
                    </tr>
                    @foreach ($courses as $index => $course)
                        <tr class="hover:bg-indigo-900/50 transition-colors duration-150 animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.1 }}s">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ Str::limit($course->title, 50) }}</div>
                                <div class="text-xs text-indigo-300">{{ Str::limit($course->subtitle ?? '', 60) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">{{ $course->instructor->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-indigo-300">{{ $course->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} capitalize">
                                    {{ $course->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('course.preview', ['course' => $course->id]) }}" class="text-indigo-400 hover:text-indigo-600 mr-3" title="Preview course" aria-label="Preview course {{ $course->title }}">
                                    <i class="fas fa-eye mr-1"></i> Preview
                                </a>
                                <button wire:click="openApproveModal({{ $course->id }})" class="text-green-400 hover:text-green-600 mr-3" title="Approve course" aria-label="Approve course {{ $course->title }}">
                                    <i class="fas fa-check-circle mr-1"></i> Approve
                                </button>
                                <button wire:click="openRejectModal({{ $course->id }})" class="text-red-400 hover:text-red-600" title="Reject course" aria-label="Reject course {{ $course->title }}">
                                    <i class="fas fa-times-circle mr-1"></i> Reject
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
            {{ $courses->links('pagination::tailwind') }}
        </div>
    @endif

    <!-- Approve Modal -->
    <div x-cloak x-show="$wire.isApproveModalOpen" x-trap="$wire.isApproveModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-2 p-6" role="dialog" aria-modal="true" aria-labelledby="approve-modal-title">
            <h2 id="approve-modal-title" class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Confirm Approval</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-4">Are you sure you want to approve this course? It will be published and accessible to users.</p>
            <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                <button type="button" wire:click="closeModal" @click="$wire.isApproveModalOpen = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    Cancel
                </button>
                <button wire:click="approveCourse" wire:loading.attr="disabled" class="px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 flex items-center">
                    <span wire:loading.remove>Approve</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Approving...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-cloak x-show="$wire.isRejectModalOpen" x-trap="$wire.isRejectModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-2 p-6" role="dialog" aria-modal="true" aria-labelledby="reject-modal-title">
            <h2 id="reject-modal-title" class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Reject Course</h2>
            <form wire:submit="rejectCourse">
                @csrf
                <div class="mb-4">
                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Reason for Rejection <span class="text-red-400">*</span></label>
                    <textarea wire:model.live="rejectionReason" id="rejectionReason" rows="4"
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-y"
                              aria-label="Reason for rejection" aria-required="true" maxlength="1000" x-ref="rejectInput"></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ strlen($rejectionReason) }}/1000 characters</p>
                    @error('rejectionReason') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" wire:click="closeModal" @click="$wire.isRejectModalOpen = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 flex items-center">
                        <span wire:loading.remove>Reject</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Rejecting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>