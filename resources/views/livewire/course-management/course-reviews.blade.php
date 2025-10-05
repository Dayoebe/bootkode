<div class="bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 min-h-screen p-4 lg:p-6 transition-colors duration-300" 
     x-data="{ tooltip: '' }" 
     wire:review-updated.window="$refresh">

    <!-- Header Section -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg p-6 mb-6 border border-white/20 dark:border-gray-700/30 transition-colors duration-300">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between">
            <div class="flex items-center mb-4 lg:mb-0">
                <div class="relative">
                    <div class="bg-yellow-500 p-3 rounded-xl mr-4 shadow-md">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 bg-blue-500 w-4 h-4 rounded-full flex items-center justify-center">
                        <i class="fas fa-comments text-white text-[8px]"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-yellow-600 dark:from-white dark:to-yellow-400 bg-clip-text text-transparent">
                        Course Reviews
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">
                        Manage student feedback and respond to reviews
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg p-6 mb-6 border border-white/20 dark:border-gray-700/30 transition-colors duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center transition-colors duration-300">
                <i class="fas fa-search text-blue-500 mr-2"></i>
                Search Reviews
            </h3>
        </div>
        
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Search reviews or courses..."
                   class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-yellow-500 dark:focus:border-yellow-400 focus:ring-2 focus:ring-yellow-500/20 transition-all duration-300"
                   aria-label="Search reviews or courses">
            <div class="absolute left-3 top-3 text-gray-400">
                <i class="fas fa-search"></i>
            </div>
            <div wire:loading wire:target="search" class="absolute right-3 top-3">
                <i class="fas fa-spinner animate-spin text-yellow-500"></i>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl mb-4 transition-colors duration-300" 
             role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('message') }}
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-4 transition-colors duration-300" 
             role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Reviews Table -->
    @if ($reviews->isEmpty())
        <div class="text-center py-12 bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 dark:border-gray-700/30 transition-colors duration-300">
            <div class="bg-yellow-100 dark:bg-yellow-900/50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300">
                <i class="fas fa-star text-4xl text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 transition-colors duration-300">No reviews found</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors duration-300">
                {{ $search ? 'No reviews match your search criteria.' : 'Student reviews will appear here once they start reviewing courses.' }}
            </p>
        </div>
    @else
        <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 dark:border-gray-700/30 overflow-hidden transition-colors duration-300"
             wire:loading.class="opacity-50">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider transition-colors duration-300">
                                Course
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider transition-colors duration-300">
                                Reviewer
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider transition-colors duration-300">
                                Review
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider transition-colors duration-300">
                                Rating
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider transition-colors duration-300">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600 transition-colors duration-300">
                        <tr wire:loading wire:target="search, $refresh">
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">
                                <i class="fas fa-circle-notch fa-spin mr-2"></i> Loading...
                            </td>
                        </tr>
                        @foreach ($reviews as $review)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">
                                        {{ Str::limit($review->course->title, 40) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">
                                        by {{ $review->course->instructor->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">
                                                {{ $review->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">
                                                {{ $review->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <p class="text-sm text-gray-900 dark:text-white transition-colors duration-300">
                                            {{ Str::limit($review->review_text, 100) }}
                                        </p>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 transition-colors duration-300">
                                            {{ $review->created_at->diffForHumans() }}
                                        </div>
                                        
                                        <!-- Instructor Reply Display -->
                                        @if($review->instructor_reply)
                                            <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 transition-colors duration-300">
                                                <div class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1 transition-colors duration-300 flex items-center">
                                                    <i class="fas fa-reply mr-1"></i>
                                                    Instructor Response
                                                    <span class="ml-2 text-gray-500 dark:text-gray-400">{{ $review->replied_at?->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-xs text-gray-600 dark:text-gray-300 transition-colors duration-300">
                                                    {{ Str::limit($review->instructor_reply, 100) }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex items-center mr-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm {{ $review->rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">
                                            {{ $review->rating }}/5
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="openReplyModal({{ $review->id }})" 
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20" 
                                                title="{{ $review->instructor_reply ? 'Update reply' : 'Reply to review' }}">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $review->id }})" 
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors duration-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" 
                                                title="Delete review">
                                            <i class="fas fa-trash-alt"></i>
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
        @if($reviews->hasPages())
            <div class="mt-6">
                {{ $reviews->links('pagination::tailwind') }}
            </div>
        @endif
    @endif

    <!-- Reply Modal -->
    <div x-show="$wire.isReplyModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         x-cloak>
        <div @click.away="$wire.closeModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-2 p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-reply text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-300">Reply to Review</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors duration-300">
                    Respond professionally to student feedback
                </p>
            </div>

            <form wire:submit="saveReply" class="space-y-4">
                <div>
                    <label for="replyText" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                        Your Reply <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="replyText" id="replyText" rows="4"
                              class="w-full px-4 py-3 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 resize-none"
                              placeholder="Thank you for your feedback. I appreciate your review and..."
                              maxlength="1000"></textarea>
                    <div class="flex justify-between items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>Be professional and constructive</span>
                        <span>{{ strlen($replyText ?? '') }}/1000</span>
                    </div>
                    @error('replyText') 
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="closeModal"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold py-3 px-6 rounded-xl transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-colors duration-300 disabled:opacity-50">
                        <span wire:loading.remove wire:target="saveReply">Send Reply</span>
                        <span wire:loading wire:target="saveReply">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Sending...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="$wire.isDeleteModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         x-cloak>
        <div @click.away="$wire.closeModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-2 p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-300">Confirm Deletion</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors duration-300">
                    Are you sure you want to delete this review? This action cannot be undone.
                </p>
            </div>

            <div class="flex gap-3">
                <button wire:click="closeModal"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold py-3 px-6 rounded-xl transition-colors duration-300">
                    Cancel
                </button>
                <button wire:click="delete"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-colors duration-300">
                    Delete Review
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-40">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 flex items-center shadow-2xl border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="relative mr-4">
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-300 dark:border-gray-600"></div>
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-yellow-600 border-t-transparent absolute top-0"></div>
            </div>
            <span class="text-gray-800 dark:text-white font-semibold">Processing...</span>
        </div>
    </div>

    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
        </style>
    @endpush
</div>