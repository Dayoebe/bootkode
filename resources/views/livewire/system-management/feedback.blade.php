<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-rose-600 to-pink-600 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-comment-dots mr-2"></i> Feedback
        </h1>
        <p class="text-rose-100 mt-2">Share your thoughts to help us improve</p>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-8">
        <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary overflow-hidden">
            <nav class="flex border-b border-themed-primary" aria-label="Tabs">
                <button @click="activeTab = 'submit_feedback'"
                    :class="{ 
                        'border-b-2 border-rose-500 text-rose-600 dark:text-rose-400 bg-rose-100/10 dark:bg-rose-900/20': activeTab === 'submit_feedback', 
                        'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'submit_feedback' 
                    }"
                    class="flex-1 whitespace-nowrap py-4 px-1 font-medium text-sm flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-pen mr-2"></i> Submit Feedback
                </button>
                <button @click="activeTab = 'my_feedback'"
                    :class="{ 
                        'border-b-2 border-rose-500 text-rose-600 dark:text-rose-400 bg-rose-100/10 dark:bg-rose-900/20': activeTab === 'my_feedback', 
                        'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'my_feedback' 
                    }"
                    class="flex-1 whitespace-nowrap py-4 px-1 font-medium text-sm flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-history mr-2"></i> My Feedback
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="mt-8">
            <!-- Submit Feedback Tab -->
            <div x-show="activeTab === 'submit_feedback'" x-transition class="bg-themed-secondary shadow rounded-2xl p-6 animate__animated animate__fadeInUp border border-themed-primary">
                <h3 class="text-xl font-semibold text-themed-primary mb-6 flex items-center">
                    <i class="fas fa-lightbulb text-rose-600 dark:text-rose-400 mr-2"></i>
                    Share Your Feedback
                </h3>
                
                <form wire:submit.prevent="saveFeedback" enctype="multipart/form-data">
                    <div class="space-y-6">
                        <!-- Category Selection -->
                        <div>
                            <label for="category" class="block text-sm font-semibold text-themed-primary mb-3">
                                <i class="fas fa-tag mr-2 text-rose-600 dark:text-rose-400"></i>Category
                            </label>
                            <select wire:model="category" id="category"
                                class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-xl text-themed-primary focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200">
                                <option value="general">General Feedback</option>
                                <option value="course">Course Related</option>
                                <option value="platform">Platform Features</option>
                                <option value="bug">Bug Report</option>
                                <option value="feature">Feature Request</option>
                            </select>
                            @error('category') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- Course Selection (conditional) -->
                        @if($category === 'course')
                            <div>
                                <label for="course_id" class="block text-sm font-semibold text-themed-primary mb-3">
                                    <i class="fas fa-graduation-cap mr-2 text-rose-600 dark:text-rose-400"></i>Select Course
                                </label>
                                <select wire:model="course_id" id="course_id"
                                    class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-xl text-themed-primary focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200">
                                    <option value="">-- Select a course --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                @error('course_id') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-themed-primary mb-3">
                                <i class="fas fa-message mr-2 text-rose-600 dark:text-rose-400"></i>Your Feedback
                            </label>
                            <textarea wire:model="message" id="message" rows="5"
                                class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-xl text-themed-primary focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200"
                                placeholder="Please share your feedback, suggestions, or concerns..."></textarea>
                            @error('message') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- Rating -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-3">
                                <i class="fas fa-star mr-2 text-rose-600 dark:text-rose-400"></i>How satisfied are you?
                            </label>
                            <div class="flex items-center space-x-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                        wire:click="$set('rating', {{ $i }})"
                                        class="text-3xl transition-all duration-200 transform hover:scale-125"
                                        :class="{ 'text-yellow-400': {{ $i }} <= $wire.rating, 'text-gray-300 dark:text-gray-600': {{ $i }} > $wire.rating }">
                                        <i class="fas fa-star"></i>
                                    </button>
                                @endfor
                            </div>
                            <p class="text-xs text-themed-secondary mt-2">
                                {{ $rating ? ['Very Dissatisfied', 'Dissatisfied', 'Neutral', 'Satisfied', 'Very Satisfied'][$rating - 1] : 'Select a rating' }}
                            </p>
                            @error('rating') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- Attachment -->
                        <div>
                            <label for="attachment" class="block text-sm font-semibold text-themed-primary mb-3">
                                <i class="fas fa-paperclip mr-2 text-rose-600 dark:text-rose-400"></i>Attachment (optional)
                            </label>
                            <div class="border-2 border-dashed border-themed-primary bg-themed-tertiary rounded-xl p-6 text-center hover:border-rose-500 dark:hover:border-rose-400 transition-colors duration-200">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-rose-600 dark:text-rose-400 mb-2"></i>
                                    <label for="attachment" class="cursor-pointer">
                                        <span class="text-sm font-medium text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300">Upload a file</span>
                                    </label>
                                    <input wire:model="attachment" type="file" id="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx">
                                    <p class="text-xs text-themed-secondary mt-1">or drag and drop (PNG, JPG, PDF up to 2MB)</p>
                                    @if ($attachment)
                                        <div class="mt-3 text-sm text-green-600 dark:text-green-400 bg-green-100/30 dark:bg-green-900/20 rounded-lg p-2 flex items-center">
                                            <i class="fas fa-check-circle mr-2"></i>{{ $attachment->getClientOriginalName() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('attachment') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex items-center justify-end space-x-4">
                        <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center px-8 py-3 border border-transparent text-base font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 dark:from-rose-700 dark:to-pink-800 dark:hover:from-rose-800 dark:hover:to-pink-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 dark:focus:ring-offset-themed-secondary disabled:opacity-50 transition-all duration-200 transform hover:scale-105">
                            <span wire:loading.remove><i class="fas fa-paper-plane mr-2"></i>Submit Feedback</span>
                            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Submitting...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- My Feedback Tab -->
            <div x-show="activeTab === 'my_feedback'" x-transition class="space-y-6">
                <!-- Search and Filter -->
                <div class="bg-themed-secondary shadow rounded-2xl p-6 border border-themed-primary">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search_feedback" class="block text-sm font-medium text-themed-primary mb-2">Search</label>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-3 text-themed-secondary"></i>
                                <input wire:model.live.debounce.300ms="search" type="text" id="search_feedback"
                                       placeholder="Search your feedback..."
                                       class="w-full pl-10 pr-4 py-2 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-themed-primary">
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="status_filter" class="block text-sm font-medium text-themed-primary mb-2">Status</label>
                            <select wire:model.live="statusFilter" id="status_filter"
                                    class="w-full px-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-themed-primary">
                                <option value="all">All</option>
                                <option value="open">Open</option>
                                <option value="responded">Responded</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Feedback List -->
                <div class="bg-themed-secondary shadow rounded-2xl border border-themed-primary overflow-hidden">
                    <div class="space-y-4 p-6">
                        @forelse($feedbacks as $feedback)
                            <div class="border border-themed-primary rounded-xl p-4 hover:shadow-lg hover:bg-themed-tertiary transition-all duration-200 animate__animated animate__fadeInUp">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                @if($feedback->status === 'open') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                                @elseif($feedback->status === 'responded') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                                <i class="fas 
                                                    @if($feedback->status === 'open') fa-clock
                                                    @elseif($feedback->status === 'responded') fa-check-circle
                                                    @else fa-times-circle @endif mr-1"></i>
                                                {{ ucfirst($feedback->status) }}
                                            </span>
                                            <span class="text-xs text-themed-secondary">
                                                <i class="fas fa-calendar mr-1"></i>{{ $feedback->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-semibold text-themed-primary">
                                            @if($feedback->course)
                                                <i class="fas fa-graduation-cap mr-1 text-rose-600 dark:text-rose-400"></i>{{ $feedback->course->title }}
                                            @else
                                                <i class="fas fa-comments mr-1 text-rose-600 dark:text-rose-400"></i>{{ ucfirst($feedback->category) }} Feedback
                                            @endif
                                        </h4>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }} text-sm"></i>
                                        @endfor
                                    </div>
                                </div>

                                <p class="text-sm text-themed-primary mb-3">{{ Str::limit($feedback->message, 200) }}</p>

                                @if($feedback->response)
                                    <div class="bg-green-100/30 dark:bg-green-900/20 rounded-lg p-3 border border-green-300 dark:border-green-700 mb-3">
                                        <p class="text-xs font-semibold text-green-900 dark:text-green-100 mb-1">
                                            <i class="fas fa-reply mr-1"></i>Response from Team
                                        </p>
                                        <p class="text-sm text-green-800 dark:text-green-200">{{ Str::limit($feedback->response, 150) }}</p>
                                        <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                            By: {{ $feedback->responder?->name ?? 'Support Team' }} on {{ $feedback->responded_at?->format('M d, Y') }}
                                        </p>
                                    </div>
                                @endif

                                @if($feedback->attachment_url)
                                    <a href="{{ $feedback->attachment_url }}" target="_blank"
                                       class="inline-flex items-center text-xs text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 transition-colors">
                                        <i class="fas fa-paperclip mr-1"></i> View Attachment
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="bg-themed-tertiary rounded-full p-4 w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                                    <i class="fas fa-inbox text-2xl text-themed-secondary"></i>
                                </div>
                                <p class="text-themed-secondary font-medium">No feedback yet</p>
                                <p class="text-sm text-themed-secondary mt-1">Your feedback submissions will appear here</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                @if($feedbacks->hasPages())
                    <div class="flex justify-center">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>