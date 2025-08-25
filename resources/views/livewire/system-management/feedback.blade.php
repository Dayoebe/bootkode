<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-comment-dots mr-2"></i> Feedback
        </h1>
        <p class="text-gray-400 mt-2">Share your thoughts to help us improve</p>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'submit_feedback'"
                    :class="{ 'border-blue-500 text-blue-600': activeTab === 'submit_feedback', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'submit_feedback' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-pen mr-2"></i> Submit Feedback
                </button>
                <button @click="activeTab = 'my_feedback'"
                    :class="{ 'border-blue-500 text-blue-600': activeTab === 'my_feedback', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'my_feedback' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-history mr-2"></i> My Feedback
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="mt-8">
            <!-- Submit Feedback Tab -->
            <div x-show="activeTab === 'submit_feedback'" x-transition class="bg-white shadow rounded-lg p-6 animate__animated animate__fadeInUp">
                <form wire:submit.prevent="saveFeedback" enctype="multipart/form-data">
                    <div class="space-y-6">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select wire:model="category" id="category"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="general">General</option>
                                <option value="course">Course</option>
                                <option value="platform">Platform</option>
                            </select>
                            @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        @if($category === 'course')
                            <div>
                                <label for="course_id" class="block text-sm font-medium text-gray-700">Select Course</label>
                                <select wire:model="course_id" id="course_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                @error('course_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea wire:model="message" id="message" rows="4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                            <div class="flex items-center space-x-1 mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-2xl cursor-pointer transition-colors duration-200"
                                        wire:click="$set('rating', {{ $i }})"
                                        :class="{ 'text-yellow-400': '{{ $i }}' <= $wire.rating, 'text-gray-300': '{{ $i }}' > $wire.rating }"></i>
                                @endfor
                            </div>
                            @error('rating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment (optional)</label>
                            <input wire:model="attachment" type="file" id="attachment"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('attachment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <span wire:loading.remove><i class="fas fa-paper-plane mr-2"></i> Submit Feedback</span>
                                <span wire:loading><i class="fas fa-spinner fa-spin mr-2"></i> Submitting...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- My Feedback Tab -->
            <div x-show="activeTab === 'my_feedback'" x-transition class="bg-white shadow rounded-lg p-6 animate__animated animate__fadeInUp">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search my feedback..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-1/2">
                        <select wire:model.live="statusFilter"
                                class="ml-4 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Statuses</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 space-y-4">
                    @forelse($feedbacks as $feedback)
                        <div class="border-b border-gray-200 pb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        @if($feedback->course)
                                            Feedback for: {{ $feedback->course->title }}
                                        @else
                                            General Feedback
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $feedback->message }}</p>
                                    <div class="flex items-center mt-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-xs text-gray-400">Status: {{ ucfirst($feedback->status) }}</p>
                                    @if ($feedback->response)
                                        <p class="text-sm text-gray-600 mt-2"><strong>Response:</strong> {{ $feedback->response }}</p>
                                        <p class="text-xs text-gray-400">By: {{ $feedback->responder?->name ?? 'N/A' }} on {{ $feedback->responded_at?->format('M d, Y') }}</p>
                                    @endif
                                    @if ($feedback->attachment_url)
                                        <a href="{{ $feedback->attachment_url }}" target="_blank"
                                           class="text-blue-600 hover:text-blue-800 text-sm mt-1 inline-block">
                                            <i class="fas fa-paperclip mr-1"></i> View Attachment
                                        </a>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 text-right mt-1">{{ $feedback->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No feedback found.</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    {{ $feedbacks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
