<div class="bg-gray-800 p-8 rounded-2xl shadow-2xl text-white animate__animated animate__fadeIn" x-data="{ tooltip: '' }" wire:review-updated.window="$refresh">
    @csrf
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-indigo-600 pb-4 mb-6 animate__animated animate__fadeInDown">
        <h2 class="text-3xl font-extrabold text-white">
            <i class="fas fa-star mr-2 text-yellow-400" aria-hidden="true"></i> Course Reviews
        </h2>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 animate__animated animate__fadeIn" style="animation-delay: 0.1s">
        <div class="w-full sm:w-1/3">
            <label for="search" class="sr-only">Search Reviews</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-indigo-300"></i>
                </div>
                <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Search reviews or courses..."
                       class="w-full pl-10 pr-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-white placeholder-indigo-300 shadow-md transition-all duration-300 hover:shadow-lg"
                       aria-label="Search reviews or courses">
            </div>
        </div>
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

    <!-- Reviews Table -->
    @if ($reviews->isEmpty())
        <div class="text-center py-10 animate__animated animate__fadeIn" style="animation-delay: 0.2s">
            <p class="text-indigo-300 text-lg">No course reviews found.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl shadow-md border border-indigo-600 animate__animated animate__fadeIn" style="animation-delay: 0.2s" wire:loading.class="opacity-50">
            <table class="min-w-full divide-y divide-indigo-600">
                <thead class="bg-indigo-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Course</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Reviewer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Review</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Rating</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-indigo-600" wire:loading.remove>
                    <tr wire:loading wire:target="search, $refresh">
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-indigo-300">
                            <i class="fas fa-circle-notch fa-spin mr-2"></i> Loading...
                        </td>
                    </tr>
                    @foreach ($reviews as $index => $review)
                        <tr class="hover:bg-indigo-900/50 transition-colors duration-150 animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.1 }}s">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ Str::limit($review->course->title, 40) }}</div>
                                <div class="text-xs text-indigo-300">by {{ $review->course->instructor->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">{{ $review->user->name }}</div>
                                <div class="text-xs text-indigo-300">{{ $review->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-white max-w-sm">{{ Str::limit($review->review_text, 150) }}</p>
                                @if ($review->replies->isNotEmpty())
                                    <div class="mt-2 text-xs text-indigo-200">
                                        @foreach ($review->replies as $reply)
                                            <p><strong>{{ $reply->user->name }} replied:</strong> {{ Str::limit($reply->reply_text, 100) }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-400">
                                @for ($i = 0; $i < $review->rating; $i++)
                                    <i class="fas fa-star" aria-hidden="true"></i>
                                @endfor
                                @for ($i = $review->rating; $i < 5; $i++)
                                    <i class="far fa-star" aria-hidden="true"></i>
                                @endfor
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="openReplyModal({{ $review->id }})" class="text-indigo-400 hover:text-indigo-600 mr-3" title="Reply to review" aria-label="Reply to review by {{ $review->user->name }}">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                                <button wire:click="confirmDelete({{ $review->id }})" class="text-red-400 hover:text-red-600" title="Delete review" aria-label="Delete review by {{ $review->user->name }}">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
            {{ $reviews->links('pagination::tailwind') }}
        </div>
    @endif

    <!-- Reply Modal -->
    <div x-cloak x-show="$wire.isReplyModalOpen" x-trap="$wire.isReplyModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-2 p-6" role="dialog" aria-modal="true" aria-labelledby="reply-modal-title">
            <h2 id="reply-modal-title" class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Reply to Review</h2>
            <form wire:submit="saveReply">
                @csrf
                <div class="mb-4">
                    <label for="replyText" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Your Reply <span class="text-red-400">*</span></label>
                    <textarea wire:model.live="replyText" id="replyText" rows="4"
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-y"
                              aria-label="Reply to review" aria-required="true" maxlength="1000" x-ref="replyInput"></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ strlen($replyText) }}/1000 characters</p>
                    @error('replyText') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" wire:click="closeModal" @click="$wire.isReplyModalOpen = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50 flex items-center">
                        <span wire:loading.remove>Save Reply</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-cloak x-show="$wire.isDeleteModalOpen" x-trap="$wire.isDeleteModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-2 p-6" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
            <h2 id="delete-modal-title" class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Confirm Deletion</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-4">Are you sure you want to delete this review? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button wire:click="closeModal" @click="$wire.isDeleteModalOpen = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    Cancel
                </button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>