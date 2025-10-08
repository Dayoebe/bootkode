<div>
    <!-- Feedback Header -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-indigo-100 dark:bg-indigo-900/20 p-3 rounded-lg border border-indigo-200 dark:border-indigo-800">
                    <i class="fas fa-comments text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Feedback System</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Share your thoughts and help us improve the platform</p>
                </div>
            </div>
            <button wire:click="$set('showCreateForm', true)" 
                    class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>Submit Feedback
            </button>
        </div>
    </div>

    <!-- Stats (Admin View) -->
    @if($isAdmin)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 dark:bg-blue-900/20 p-3 rounded-lg mr-4 border border-blue-200 dark:border-blue-800">
                    <i class="fas fa-inbox text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Total Feedback</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="bg-red-100 dark:bg-red-900/20 p-3 rounded-lg mr-4 border border-red-200 dark:border-red-800">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['open'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Open Issues</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="bg-yellow-100 dark:bg-yellow-900/20 p-3 rounded-lg mr-4 border border-yellow-200 dark:border-yellow-800">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['in_progress'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">In Progress</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="bg-green-100 dark:bg-green-900/20 p-3 rounded-lg mr-4 border border-green-200 dark:border-green-800">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['resolved'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Resolved</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <select wire:model.live="statusFilter" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    <option value="all">All Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div>
                <select wire:model.live="categoryFilter" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-sm">
                    <option value="all">All Categories</option>
                    <option value="feature_request">Feature Request</option>
                    <option value="bug_report">Bug Report</option>
                    <option value="course_feedback">Course Feedback</option>
                    <option value="general">General</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button wire:click="$refresh" 
                        class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-medium transition-colors text-sm border border-gray-200 dark:border-gray-600">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Feedback List -->
    <div class="space-y-6">
        @forelse($feedback as $item)
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 animate__animated animate__fadeIn">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $item->subject }}</h3>
                        <span class="bg-{{ $item->status_color }}-100 dark:bg-{{ $item->status_color }}-900/20 text-{{ $item->status_color }}-800 dark:text-{{ $item->status_color }}-300 
                                   text-xs px-2 py-1 rounded-full font-medium border border-{{ $item->status_color }}-200 dark:border-{{ $item->status_color }}-800">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs px-2 py-1 rounded-full border border-gray-200 dark:border-gray-600">
                            {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                        </span>
                        <span class="bg-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-100 dark:bg-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-900/20 
                                   text-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-800 dark:text-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-300 
                                   text-xs px-2 py-1 rounded-full border border-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-200 dark:border-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-800">
                            {{ ucfirst($item->priority) }} Priority
                        </span>
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                        <i class="fas fa-user mr-2"></i>
                        By {{ $item->user->name }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-clock mr-2"></i>
                        {{ $item->created_at->diffForHumans() }}
                        @if($item->assigned_to)
                        <span class="mx-2">•</span>
                        <i class="fas fa-user-check mr-2"></i>
                        Assigned to {{ $item->assignedTo->name }}
                        @endif
                    </div>
                </div>
                
                @if($isAdmin)
                <div class="flex items-center space-x-2">
                    <select wire:change="updateFeedbackStatus({{ $item->id }}, $event.target.value)"
                            class="text-sm px-3 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                        <option value="open" {{ $item->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $item->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $item->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $item->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <button wire:click="openResponseModal({{ $item->id }})" 
                            class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-3 py-1 rounded-lg text-sm font-medium">
                        <i class="fas fa-reply mr-1"></i>Respond
                    </button>
                </div>
                @endif
            </div>

            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-4 border border-gray-200 dark:border-gray-600">
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $item->message }}</p>
            </div>

            @if($item->admin_response)
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 dark:border-green-600 p-4 rounded-r-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-reply text-green-600 dark:text-green-400 mr-2"></i>
                    <span class="font-medium text-green-800 dark:text-green-300">Admin Response</span>
                    <span class="text-green-600 dark:text-green-400 text-sm ml-2">{{ $item->responded_at?->diffForHumans() }}</span>
                </div>
                <p class="text-green-700 dark:text-green-200 whitespace-pre-wrap">{{ $item->admin_response }}</p>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-12">
            <div class="bg-gray-100 dark:bg-gray-700 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-comments text-gray-400 dark:text-gray-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Feedback Found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Be the first to share your thoughts and feedback!</p>
            <button wire:click="$set('showCreateForm', true)" 
                    class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium">
                Submit First Feedback
            </button>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $feedback->links() }}
    </div>

    <!-- Submit Feedback Modal -->
    @if($showCreateForm)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 z-50 flex items-center justify-center animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 animate__animated animate__zoomIn">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Submit Feedback</h3>
                    <button wire:click="$set('showCreateForm', false)" 
                            class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="submitFeedback" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                            <select wire:model="category" 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent">
                                <option value="general">General Feedback</option>
                                <option value="feature_request">Feature Request</option>
                                <option value="bug_report">Bug Report</option>
                                <option value="course_feedback">Course Feedback</option>
                            </select>
                            @error('category') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority *</label>
                            <select wire:model="priority" 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                        <input type="text" wire:model="subject" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent"
                               placeholder="Brief summary of your feedback">
                        @error('subject') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message *</label>
                        <textarea wire:model="message" rows="6"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent"
                                  placeholder="Please provide detailed feedback. Include steps to reproduce if reporting a bug."></textarea>
                        @error('message') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 dark:text-blue-400 mt-1 mr-3"></i>
                            <div class="text-sm text-blue-700 dark:text-blue-300">
                                <p class="font-medium mb-1">Tips for effective feedback:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Be specific and provide examples when possible</li>
                                    <li>For bug reports, include steps to reproduce the issue</li>
                                    <li>For feature requests, explain how it would benefit users</li>
                                    <li>Keep it constructive and respectful</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <button type="button" wire:click="$set('showCreateForm', false)"
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Admin Response Modal -->
    @if($showResponseModal && $selectedFeedback && $isAdmin)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 z-50 flex items-center justify-center animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 animate__animated animate__zoomIn">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Respond to Feedback</h3>
                    <button wire:click="$set('showResponseModal', false)" 
                            class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Original Feedback -->
                <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $selectedFeedback->subject }}</h4>
                    <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">{{ $selectedFeedback->message }}</p>
                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <span>From: {{ $selectedFeedback->user->name }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $selectedFeedback->created_at->format('M j, Y g:i A') }}</span>
                    </div>
                </div>

                <form wire:submit.prevent="respondToFeedback" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Response *</label>
                        <textarea wire:model="adminResponse" rows="6"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent"
                                  placeholder="Write your response to the user..."></textarea>
                        @error('adminResponse') <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assign To (Optional)</label>
                        <select wire:model="assignTo" 
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent">
                            <option value="">No assignment</option>
                            @foreach($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <button type="button" wire:click="$set('showResponseModal', false)"
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Send Response
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>