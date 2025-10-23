<div class="space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
        <div class="flex flex-wrap items-center gap-4">
            <button wire:click="$set('showCreateForm', true)"
                class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-xl transition-colors font-semibold shadow-lg">
                <i class="fas fa-plus mr-2"></i> Create New Interview
            </button>
        </div>

        <!-- Search and Filters -->
        <div class="flex flex-wrap items-center gap-4">
            <input wire:model.live.debounce.300ms="searchTerm" type="text" placeholder="Search interviews..."
                class="px-4 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary placeholder-themed-tertiary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all w-64">

            <select wire:model.live="filterType"
                class="px-4 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                <option value="">All Types</option>
                <option value="technical">Technical</option>
                <option value="behavioral">Behavioral</option>
                <option value="case_study">Case Study</option>
                <option value="system_design">System Design</option>
                <option value="coding">Coding</option>
                <option value="hr">HR</option>
            </select>

            <select wire:model.live="filterStatus"
                class="px-4 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Interviews Grid -->
    @php
        $interviews = $this->mockInterviews;
        $interviewCount = is_countable($interviews) ? count($interviews) : 0;
    @endphp

    @if($interviewCount > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($interviews as $interview)
                <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <!-- Header -->
                    <div class="p-6 border-b border-themed-primary">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center flex-1">
                                <span class="text-2xl mr-3">{{ $interview->getTypeIcon() }}</span>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-themed-primary line-clamp-2">{{ $interview->title }}</h3>
                                    <p class="text-sm text-themed-secondary">{{ $interview->type_label }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-sm rounded-full bg-accent-themed-primary/10 text-accent-themed-primary whitespace-nowrap font-medium">
                                {{ $interview->status_label }}
                            </span>
                        </div>

                        @if($interview->description)
                            <p class="text-themed-secondary text-sm mb-4 line-clamp-3">{{ $interview->description }}</p>
                        @endif

                        <!-- Meta Information -->
                        <div class="space-y-2 text-sm text-themed-secondary">
                            <div class="flex items-center justify-between">
                                <span>Difficulty:</span>
                                <span class="font-medium px-2 py-1 rounded bg-themed-tertiary text-themed-primary">
                                    {{ $interview->difficulty_label }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Duration:</span>
                                <span class="font-medium text-themed-primary">{{ $interview->duration_formatted }}</span>
                            </div>
                            @if($interview->job_role)
                                <div class="flex items-center justify-between">
                                    <span>Role:</span>
                                    <span class="font-medium truncate ml-2 text-themed-primary">{{ $interview->job_role }}</span>
                                </div>
                            @endif
                            @if($interview->overall_score)
                                <div class="flex items-center justify-between">
                                    <span>Score:</span>
                                    <span class="font-bold text-accent-themed-primary">
                                        {{ number_format($interview->overall_score, 1) }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-6 bg-themed-tertiary border-t border-themed-primary">
                        <div class="flex space-x-2">
                            @if($interview->isScheduled())
                                <button wire:click="startInterview({{ $interview->id }})"
                                    class="flex-1 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2 rounded-lg transition-colors font-medium">
                                    <i class="fas fa-play mr-1"></i> Start Interview
                                </button>
                            @elseif($interview->isCompleted())
                                <button wire:click="viewResults({{ $interview->id }})"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                                    <i class="fas fa-chart-bar mr-1"></i> View Results
                                </button>
                                @if($interview->allow_retakes && $interview->retake_count < $interview->max_retakes)
                                    <button wire:click="retakeInterview({{ $interview->id }})"
                                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                @endif
                            @endif

                            <div class="flex space-x-1">
                                <button wire:click="editInterview({{ $interview->id }})"
                                    class="p-2 bg-accent-themed-primary/10 text-accent-themed-primary hover:bg-accent-themed-primary/20 rounded-lg transition-colors"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button wire:click="deleteInterview({{ $interview->id }})"
                                    wire:confirm="Are you sure you want to delete this interview?"
                                    class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-lg transition-colors"
                                    title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <div class="mx-auto w-32 h-32 bg-themed-tertiary rounded-full flex items-center justify-center mb-6">
                <svg class="w-16 h-16 text-themed-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="text-2xl font-semibold text-themed-primary mb-2">No interviews found</h3>
            <p class="text-themed-secondary mb-6">
                @if($searchTerm || $filterType || $filterStatus)
                    Try adjusting your filters or search terms
                @else
                    Create your first mock interview to start practicing
                @endif
            </p>
            @if(!$searchTerm && !$filterType && !$filterStatus)
                <button wire:click="$set('showCreateForm', true)"
                    class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-8 py-3 rounded-xl transition-colors font-semibold">
                    <i class="fas fa-plus mr-2"></i> Create Your First Interview
                </button>
            @endif
        </div>
    @endif
</div>