<div class="space-y-6">
    <!-- Search and Filters -->
    <div class="shadow rounded-lg p-6 bg-themed-secondary border border-themed-primary">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <input wire:model.live.debounce.300ms="searchTerm" type="text"
                    placeholder="Search interviews, users..."
                    class="w-full px-3 py-2 border border-themed-primary rounded-md focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
            </div>

            <select wire:model.live="filterType"
                class="px-3 py-2 border border-themed-primary rounded-md focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
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
                class="px-3 py-2 border border-themed-primary rounded-md focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="missed">Missed</option>
            </select>

            <select wire:model.live="filterDifficulty"
                class="px-3 py-2 border border-themed-primary rounded-md focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                <option value="">All Difficulty</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="expert">Expert</option>
            </select>

            <select wire:model.live="filterDateRange"
                class="px-3 py-2 border border-themed-primary rounded-md focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
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
                <span class="text-sm text-themed-secondary">{{ count($selectedInterviews) }} selected</span>
                <select wire:model="bulkAction"
                    class="px-3 py-2 border border-themed-primary rounded-md focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                    <option value="">Bulk Actions</option>
                    <option value="approve">Approve</option>
                    <option value="generate_feedback">Generate AI Feedback</option>
                    <option value="delete">Delete</option>
                </select>
                <button wire:click="executeBulkAction"
                    class="bg-accent-themed-primary text-white px-4 py-2 rounded-md hover:bg-accent-themed-secondary transition-colors text-sm font-medium">
                    Execute
                </button>
                <button wire:click="clearBulkSelection"
                    class="text-themed-secondary hover:text-themed-primary transition-colors text-sm">
                    Clear Selection
                </button>
            </div>
        @endif
    </div>

    <!-- Loading State -->
    <div wire:loading wire:target="searchTerm,filterType,filterStatus,filterDifficulty,filterDateRange" class="text-center py-4">
        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white transition ease-in-out duration-150 bg-accent-themed-primary">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        </div>
    </div>

    <!-- Interviews Table -->
    <div class="shadow overflow-hidden sm:rounded-lg bg-themed-secondary border border-themed-primary">
        <table class="min-w-full divide-y divide-themed-primary">
            <thead class="bg-themed-tertiary">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        <input type="checkbox" wire:click="selectAllVisible" class="w-4 h-4 rounded focus:ring-2 accent-accent-themed-primary">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer text-themed-secondary"
                        wire:click="$set('sortBy', 'title')">
                        Interview
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Score
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer text-themed-secondary"
                        wire:click="$set('sortBy', 'created_at')">
                        Created
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-themed-primary bg-themed-secondary">
                @forelse($this->interviews as $interview)
                    <tr class="hover:bg-themed-tertiary transition-colors" style="background-color: {{ in_array($interview->id, $selectedInterviews) ? 'rgba(var(--accent-primary), 0.1)' : 'transparent' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                wire:click="toggleBulkSelect({{ $interview->id }})" 
                                {{ in_array($interview->id, $selectedInterviews) ? 'checked' : '' }}
                                class="w-4 h-4 rounded focus:ring-2 accent-accent-themed-primary">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-themed-primary">{{ $interview->title }}</div>
                            <div class="text-sm text-themed-secondary">{{ Str::limit($interview->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center bg-themed-tertiary">
                                        <span class="text-sm font-medium text-themed-primary">
                                            {{ substr($interview->user->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-themed-primary">{{ $interview->user->name }}</div>
                                    <div class="text-sm text-themed-secondary">{{ $interview->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $interview->type_label }}
                            </span>
                            <div class="text-xs mt-1 text-themed-secondary">{{ $interview->difficulty_label }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $interview->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $interview->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $interview->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $interview->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $interview->status === 'missed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ $interview->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($interview->overall_score)
                                <div class="text-sm font-medium text-themed-primary">
                                    {{ number_format($interview->overall_score, 1) }}%
                                </div>
                                <div class="text-xs text-themed-secondary">{{ $interview->overall_rating }}</div>
                            @else
                                <span class="text-sm text-themed-tertiary">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary">
                            {{ $interview->created_at->format('M d, Y') }}
                            <div class="text-xs">{{ $interview->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button wire:click="viewInterview({{ $interview->id }})"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary transition-colors">
                                    View
                                </button>

                                @if($interview->isCompleted() && !$interview->ai_feedback)
                                    <button wire:click="generateAIFeedback({{ $interview->id }})"
                                        class="text-green-600 hover:text-green-800 transition-colors">
                                        Generate AI
                                    </button>
                                @endif

                                <button wire:click="deleteInterview({{ $interview->id }})"
                                    wire:confirm="Are you sure you want to delete this interview?"
                                    class="text-red-600 hover:text-red-800 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-themed-secondary">
                            No interviews found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-themed-primary bg-themed-secondary">
            {{ $this->interviews->links() }}
        </div>
    </div>
</div>