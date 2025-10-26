<div class="bg-themed-primary min-h-screen transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="{ tooltip: '' }"
        wire:course-updated.window="$refresh">

        <!-- Page Header -->
        <div
            class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 animate__animated animate__fadeInDown transition-colors duration-300">
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary transition-colors duration-300">
                        Course Approvals
                    </h1>
                    <p class="text-themed-secondary mt-1 transition-colors duration-300">
                        Review and approve pending course submissions
                    </p>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="bg-green-100/50 border border-green-400 text-green-700 px-6 py-4 rounded-xl transition-colors duration-300 animate__animated animate__fadeIn"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-lg"></i>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100/50 border border-red-400 text-red-700 px-6 py-4 rounded-xl transition-colors duration-300 animate__animated animate__fadeIn"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Search Section -->
        <div
            class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3
                    class="text-lg font-bold text-themed-primary flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-search text-accent-themed-primary"></i>
                    Search & Filter
                </h3>
            </div>

            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search courses by title or subtitle..."
                    class="w-full pl-10 pr-10 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300"
                    aria-label="Search courses">
                <div
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-themed-secondary transition-colors duration-300">
                    <i class="fas fa-search"></i>
                </div>
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="fas fa-spinner animate-spin text-accent-themed-primary"></i>
                </div>
            </div>
        </div>

        <!-- Courses Table/Cards -->
        @if ($courses->isEmpty())
            <div
                class="text-center py-16 bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary transition-colors duration-300">
                <div
                    class="bg-green-100/50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300">
                    <i class="fas fa-inbox text-4xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-themed-primary mb-3 transition-colors duration-300">No pending approvals
                </h3>
                <p class="text-themed-secondary transition-colors duration-300">
                    All courses have been reviewed and approved.
                </p>
            </div>
        @else
            <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary overflow-hidden transition-colors duration-300"
                wire:loading.class="opacity-50">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-themed-primary">
                        <thead class="bg-themed-tertiary transition-colors duration-300">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider transition-colors duration-300">
                                    Course Title
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider transition-colors duration-300">
                                    Instructor
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider transition-colors duration-300">
                                    Submitted
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider transition-colors duration-300">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider transition-colors duration-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-themed-primary transition-colors duration-300">
                            <tr wire:loading wire:target="search, $refresh">
                                <td colspan="5"
                                    class="px-6 py-8 text-center text-sm text-themed-secondary transition-colors duration-300">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="fas fa-circle-notch fa-spin"></i>
                                        <span>Loading...</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($courses as $index => $course)
                                <tr class="hover:bg-themed-tertiary transition-colors duration-200 animate__animated animate__fadeInUp"
                                    style="animation-delay: {{ $index * 0.05 }}s">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold transition-colors duration-300
                                                    {{ $course->is_approved ? 'bg-green-100/50 text-green-800' : 'bg-yellow-100/50 text-yellow-800' }}">
                                            @if($course->is_approved)
                                                <i class="fas fa-check mr-1"></i> Approved
                                            @else
                                                <i class="fas fa-hourglass-half mr-1"></i> Pending
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('courses.preview', $course) }}" target="_blank"
                                                class="p-2 rounded-lg text-accent-themed-primary hover:bg-accent-themed-primary/10 transition-all duration-300 transform hover:scale-110"
                                                title="Preview course">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button wire:click="openApproveModal({{ $course->id }})"
                                                class="p-2 rounded-lg text-green-600 hover:bg-green-100/50 transition-all duration-300 transform hover:scale-110"
                                                title="Approve course">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                            <button wire:click="openRejectModal({{ $course->id }})"
                                                class="p-2 rounded-lg text-red-600 hover:bg-red-100/50 transition-all duration-300 transform hover:scale-110"
                                                title="Reject course">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div
                class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-4 transition-colors duration-300">
                {{ $courses->links('pagination::tailwind') }}
            </div>
        @endif

        <!-- Approve Modal -->
        <div x-show="$wire.isApproveModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-cloak>
            <div @click.away="$wire.closeModal()" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-themed-secondary rounded-2xl shadow-2xl w-full max-w-md mx-2 p-6 border border-themed-primary transition-colors duration-300">

                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 bg-green-100/50 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-themed-primary mb-2 transition-colors duration-300">Confirm
                        Approval</h3>
                    <p class="text-themed-secondary text-sm transition-colors duration-300">
                        Are you sure you want to approve this course? It will be published and accessible to users.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button wire:click="closeModal"
                        class="flex-1 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary font-semibold py-3 px-6 rounded-xl transition-all duration-300 border border-themed-primary transform hover:scale-105">
                        Cancel
                    </button>
                    <button wire:click="approveCourse" wire:loading.attr="disabled"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 disabled:opacity-50 transform hover:scale-105">
                        <span wire:loading.remove wire:target="approveCourse">Approve</span>
                        <span wire:loading wire:target="approveCourse" class="flex items-center justify-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>Approving...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="$wire.isRejectModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-cloak>
            <div @click.away="$wire.closeModal()" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-themed-secondary rounded-2xl shadow-2xl w-full max-w-md mx-2 p-6 border border-themed-primary transition-colors duration-300">

                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 bg-red-100/50 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-themed-primary mb-2 transition-colors duration-300">Reject Course
                    </h3>
                    <p class="text-themed-secondary text-sm transition-colors duration-300">
                        Please provide a reason for rejecting this course.
                    </p>
                </div>

                <form wire:submit="rejectCourse" class="space-y-4">
                    <div>
                        <label for="rejectionReason"
                            class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="rejectionReason" id="rejectionReason" rows="4"
                            class="w-full px-4 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all duration-300 resize-none"
                            placeholder="Explain why this course needs revision..." maxlength="1000"></textarea>
                        <div
                            class="flex justify-between items-center mt-2 text-xs text-themed-secondary transition-colors duration-300">
                            <span>Be specific to help the instructor improve</span>
                            <span>{{ strlen($rejectionReason) }}/1000</span>
                        </div>
                        @error('rejectionReason')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" wire:click="closeModal"
                            class="flex-1 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary font-semibold py-3 px-6 rounded-xl transition-all duration-300 border border-themed-primary transform hover:scale-105">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 disabled:opacity-50 transform hover:scale-105">
                            <span wire:loading.remove wire:target="rejectCourse">Reject Course</span>
                            <span wire:loading wire:target="rejectCourse"
                                class="flex items-center justify-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i>Rejecting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-40">
            <div
                class="bg-themed-secondary rounded-2xl p-8 flex items-center shadow-2xl border border-themed-primary transition-colors duration-300">
                <div class="relative mr-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-themed-tertiary"></div>
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-4 border-accent-themed-primary border-t-transparent absolute top-0">
                    </div>
                </div>
                <span class="text-themed-primary font-semibold transition-colors duration-300">Processing...</span>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</div>