<div class="space-y-6">
    <!-- Search and Filters -->
    <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <input wire:model.live.debounce.300ms="searchTerm" type="text"
                    placeholder="Search interviews, users..."
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                    style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary)); --tw-ring-color: rgb(var(--accent-primary))">
            </div>

            <select wire:model.live="filterType"
                class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary)); --tw-ring-color: rgb(var(--accent-primary))">
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
                class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary)); --tw-ring-color: rgb(var(--accent-primary))">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="missed">Missed</option>
            </select>

            <select wire:model.live="filterDifficulty"
                class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary)); --tw-ring-color: rgb(var(--accent-primary))">
                <option value="">All Difficulty</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="expert">Expert</option>
            </select>

            <select wire:model.live="filterDateRange"
                class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary)); --tw-ring-color: rgb(var(--accent-primary))">
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
                <span class="text-sm" style="color: rgb(var(--text-secondary))">{{ count($selectedInterviews) }} selected</span>
                <select wire:model="bulkAction"
                    class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                    style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary)); --tw-ring-color: rgb(var(--accent-primary))">
                    <option value="">Bulk Actions</option>
                    <option value="approve">Approve</option>
                    <option value="generate_feedback">Generate AI Feedback</option>
                    <option value="delete">Delete</option>
                </select>
                <button wire:click="executeBulkAction"
                    class="text-white px-4 py-2 rounded-md hover:opacity-90 transition-opacity text-sm"
                    style="background-color: rgb(var(--accent-primary))">
                    Execute
                </button>
                <button wire:click="clearBulkSelection"
                    class="hover:opacity-70 transition-opacity text-sm"
                    style="color: rgb(var(--text-secondary))">
                    Clear Selection
                </button>
            </div>
        @endif>
    </div>

    <!-- Loading State -->
    <div wire:loading wire:target="searchTerm,filterType,filterStatus,filterDifficulty,filterDateRange" class="text-center py-4">
        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white transition ease-in-out duration-150"
             style="background-color: rgb(var(--accent-primary))">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        </div>
    </div>

    <!-- Interviews Table -->
    <div class="shadow overflow-hidden sm:rounded-lg" style="background-color: rgb(var(--bg-secondary))">
        <table class="min-w-full divide-y" style="border-color: rgb(var(--border-primary))">
            <thead style="background-color: rgb(var(--bg-tertiary))">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        <input type="checkbox" wire:click="selectAllVisible" class="w-4 h-4 rounded focus:ring-2">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer" style="color: rgb(var(--text-secondary))"
                        wire:click="$set('sortBy', 'title')">
                        Interview
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Score
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer" style="color: rgb(var(--text-secondary))"
                        wire:click="$set('sortBy', 'created_at')">
                        Created
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" style="background-color: rgb(var(--bg-secondary)); border-color: rgb(var(--border-primary))">
                @forelse($this->interviews as $interview)
                    <tr class="transition-colors" style="background-color: {{ in_array($interview->id, $selectedInterviews) ? 'rgba(var(--accent-primary), 0.1)' : 'transparent' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                wire:click="toggleBulkSelect({{ $interview->id }})" 
                                {{ in_array($interview->id, $selectedInterviews) ? 'checked' : '' }}
                                class="w-4 h-4 rounded focus:ring-2">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium" style="color: rgb(var(--text-primary))">{{ $interview->title }}</div>
                            <div class="text-sm" style="color: rgb(var(--text-secondary))">{{ Str::limit($interview->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background-color: rgb(var(--bg-tertiary))">
                                        <span class="text-sm font-medium" style="color: rgb(var(--text-primary))">
                                            {{ substr($interview->user->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium" style="color: rgb(var(--text-primary))">{{ $interview->user->name }}</div>
                                    <div class="text-sm" style="color: rgb(var(--text-secondary))">{{ $interview->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $interview->type_label }}
                            </span>
                            <div class="text-xs mt-1" style="color: rgb(var(--text-secondary))">{{ $interview->difficulty_label }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $interview->getStatusColor() }}-100 text-{{ $interview->getStatusColor() }}-800">
                                {{ $interview->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($interview->overall_score)
                                <div class="text-sm font-medium" style="color: rgb(var(--text-primary))">
                                    {{ number_format($interview->overall_score, 1) }}%
                                </div>
                                <div class="text-xs" style="color: rgb(var(--text-secondary))">{{ $interview->overall_rating }}</div>
                            @else
                                <span class="text-sm" style="color: rgb(var(--text-tertiary))">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-secondary))">
                            {{ $interview->created_at->format('M d, Y') }}
                            <div class="text-xs">{{ $interview->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button wire:click="viewInterview({{ $interview->id }})"
                                    class="hover:opacity-80 transition-opacity"
                                    style="color: rgb(var(--accent-primary))">View</button>

                                @if($interview->isCompleted() && !$interview->ai_feedback)
                                    <button wire:click="generateAIFeedback({{ $interview->id }})"
                                        class="text-green-600 hover:text-green-800 transition-colors">Generate AI</button>
                                @endif

                                <button wire:click="deleteInterview({{ $interview->id }})"
                                    wire:confirm="Are you sure you want to delete this interview?"
                                    class="text-red-600 hover:text-red-800 transition-colors">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center" style="color: rgb(var(--text-secondary))">
                            No interviews found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t" style="border-color: rgb(var(--border-primary))">
            {{ $this->interviews->links() }}
        </div>
    </div>
</div>