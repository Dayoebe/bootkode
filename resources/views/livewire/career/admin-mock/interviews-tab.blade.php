<div class="space-y-6">
    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <input wire:model.live.debounce.300ms="searchTerm" type="text"
                    placeholder="Search interviews, users..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <select wire:model.live="filterType"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Types</option>
                <option value="technical">Technical</option>
                <option value="behavioral">Behavioral</option>
                <option value="case_study">Case Study</option>
                <option value="system_design">System Design</option>
                <option value="coding">Coding</option>
                <option value="hr">HR</option>
                <option value="custom">Custom</option>
            </select>

            <select wire:model.live="filterStatus"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="missed">Missed</option>
            </select>

            <select wire:model.live="filterDifficulty"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Difficulty</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="expert">Expert</option>
            </select>

            <select wire:model.live="filterDateRange"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Time</option>
                <option value="1">Last 24 hours</option>
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>
        </div>

        <!-- Bulk Actions -->
        @if(count($selectedInterviews) > 0)
            <div class="mt-4 flex items-center space-x-4">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ count($selectedInterviews) }} selected</span>
                <select wire:model="bulkAction"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Bulk Actions</option>
                    <option value="approve">Approve</option>
                    <option value="generate_feedback">Generate AI Feedback</option>
                    <option value="delete">Delete</option>
                </select>
                <button wire:click="executeBulkAction"
                    class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors text-sm">
                    Execute
                </button>
                <button wire:click="clearBulkSelection"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors text-sm">
                    Clear Selection
                </button>
            </div>
        @endif
    </div>

    <!-- Loading State -->
    <div wire:loading wire:target="searchTerm,filterType,filterStatus,filterDifficulty,filterDateRange" class="text-center py-4">
        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-blue-500 dark:bg-blue-600 transition ease-in-out duration-150">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        </div>
    </div>

    <!-- Interviews Table -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <input type="checkbox" wire:click="selectAllVisible" class="w-4 h-4 text-blue-600 dark:text-blue-500 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer"
                        wire:click="$set('sortBy', 'title')">
                        Interview
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Score
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer"
                        wire:click="$set('sortBy', 'created_at')">
                        Created
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($this->interviews as $interview)
                    <tr class="{{ in_array($interview->id, $selectedInterviews) ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                wire:click="toggleBulkSelect({{ $interview->id }})" 
                                {{ in_array($interview->id, $selectedInterviews) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 dark:text-blue-500 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $interview->title }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($interview->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ substr($interview->user->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $interview->user->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $interview->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400">
                                {{ $interview->type_label }}
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $interview->difficulty_label }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $interview->getStatusColor() }}-100 dark:bg-{{ $interview->getStatusColor() }}-900/30 text-{{ $interview->getStatusColor() }}-800 dark:text-{{ $interview->getStatusColor() }}-400">
                                {{ $interview->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($interview->overall_score)
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($interview->overall_score, 1) }}%
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $interview->overall_rating }}</div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 text-sm">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $interview->created_at->format('M d, Y') }}
                            <div class="text-xs">{{ $interview->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button wire:click="viewInterview({{ $interview->id }})"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">View</button>

                                @if($interview->isCompleted() && !$interview->ai_feedback)
                                    <button wire:click="generateAIFeedback({{ $interview->id }})"
                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">Generate AI</button>
                                @endif

                                <button wire:click="deleteInterview({{ $interview->id }})"
                                    wire:confirm="Are you sure you want to delete this interview?"
                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No interviews found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $this->interviews->links() }}
        </div>
    </div>
</div>