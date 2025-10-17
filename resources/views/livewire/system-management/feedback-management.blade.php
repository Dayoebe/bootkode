<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-rose-600 to-pink-600 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-comment-dots mr-2"></i> Feedback Management
        </h1>
        <p class="text-rose-100 mt-2">Review and respond to user feedback</p>
    </div>

    <!-- Search & Filter -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-themed-primary mb-2">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-3 text-themed-secondary"></i>
                <input wire:model.live.debounce.300ms="search" type="text" id="search"
                       placeholder="Search feedback..."
                       class="w-full pl-10 pr-4 py-2 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-themed-primary transition-colors duration-200">
            </div>
        </div>
        <div class="flex-1">
            <label for="status_filter" class="block text-sm font-medium text-themed-primary mb-2">Status Filter</label>
            <select wire:model.live="statusFilter" id="status_filter"
                    class="w-full px-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-themed-primary transition-colors duration-200">
                <option value="open">Open</option>
                <option value="responded">Responded</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>

    <!-- Feedback Table -->
    <div class="bg-themed-secondary shadow rounded-lg overflow-hidden border border-themed-primary transition-colors duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-themed-primary">
                <thead class="bg-themed-tertiary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-themed-primary">
                    @forelse($feedbacks as $feedback)
                        <tr class="hover:bg-themed-tertiary transition-colors duration-200 animate__animated animate__fadeInUp">
                            <td class="px-6 py-4 text-sm text-themed-primary">
                                <div class="font-medium">{{ $feedback->user->name }}</div>
                                <div class="text-themed-secondary text-xs">{{ $feedback->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300">
                                    <i class="fas fa-tag mr-1"></i>{{ ucfirst($feedback->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-themed-primary">
                                @if($feedback->course)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        <i class="fas fa-book mr-1"></i>{{ $feedback->course->title }}
                                    </span>
                                @else
                                    <span class="text-themed-secondary italic">Platform-wide</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex space-x-1 text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $feedback->rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-themed-secondary">
                                <p class="truncate max-w-xs">{{ Str::limit($feedback->message, 50) }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    @if($feedback->attachment_url)
                                        <a href="{{ $feedback->attachment_url }}" target="_blank" 
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 p-2 rounded hover:bg-blue-100/30 dark:hover:bg-blue-900/20 transition-colors"
                                           title="View Attachment">
                                            <i class="fas fa-paperclip"></i>
                                        </a>
                                    @endif
                                    <button wire:click="$emit('editFeedback', {{ $feedback->id }})"
                                            class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 p-2 rounded hover:bg-green-100/30 dark:hover:bg-green-900/20 transition-colors"
                                            title="Respond">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <button wire:click="close({{ $feedback->id }})" wire:confirm="Close this feedback?"
                                            class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 p-2 rounded hover:bg-gray-100/30 dark:hover:bg-gray-900/20 transition-colors"
                                            title="Close">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="bg-themed-tertiary rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                                    <i class="fas fa-inbox text-2xl text-themed-secondary"></i>
                                </div>
                                <p class="text-themed-secondary text-lg font-medium">No feedback found</p>
                                <p class="text-themed-tertiary text-sm mt-1">Check back later for user feedback</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($feedbacks->hasPages())
            <div class="p-4 border-t border-themed-primary flex justify-center">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>

    <!-- Response Modal (Simple inline form) -->
    @if(isset($editingFeedbackId))
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-themed-secondary rounded-lg shadow-xl max-w-md w-full p-6 border border-themed-primary">
                <h3 class="text-lg font-semibold text-themed-primary mb-4">Respond to Feedback</h3>
                <form wire:submit.prevent="respond">
                    <textarea wire:model="response" rows="4" placeholder="Enter your response..."
                              class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-themed-primary transition-colors duration-200 mb-4"></textarea>
                    <div class="flex space-x-3 justify-end">
                        <button type="button" wire:click="$set('editingFeedbackId', null)"
                                class="px-4 py-2 border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 dark:bg-rose-700 dark:hover:bg-rose-800 text-white rounded-lg transition-colors disabled:opacity-50">
                            <span wire:loading.remove><i class="fas fa-send mr-2"></i>Send Response</span>
                            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Sending...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>