<div class="space-y-8">
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Create Interview Card -->
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-themed-primary">Create Interview</h3>
                    <p class="text-themed-secondary text-sm">Set up a new mock interview</p>
                </div>
            </div>
            <button wire:click="$set('showCreateForm', true)"
                class="w-full bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2 rounded-lg transition-colors font-medium">
                Create New
            </button>
        </div>

        <!-- Quick Practice Card -->
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-themed-primary">Quick Practice</h3>
                    <p class="text-themed-secondary text-sm">Start practicing immediately</p>
                </div>
            </div>
            <button wire:click="$set('activeTab', 'practice')"
                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                Start Practice
            </button>
        </div>

        <!-- View Analytics Card -->
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-themed-primary">View Analytics</h3>
                    <p class="text-themed-secondary text-sm">Track your progress</p>
                </div>
            </div>
            <button wire:click="$set('activeTab', 'analytics')"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                View Reports
            </button>
        </div>

        <!-- Premium Features Card -->
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="bg-orange-100 dark:bg-orange-900/30 p-3 rounded-full">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-themed-primary">Premium Features</h3>
                    <p class="text-themed-secondary text-sm">Unlock advanced analytics</p>
                </div>
            </div>
            <button class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                Upgrade Now
            </button>
        </div>
    </div>

    <!-- Recent Interviews -->
    <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-themed-primary">Recent Interviews</h3>
            <button wire:click="$set('activeTab', 'interviews')"
                class="text-accent-themed-primary hover:text-accent-themed-secondary font-medium transition-colors">
                View All →
            </button>
        </div>

        @php
            $interviews = $this->mockInterviews;
            $interviewCount = is_countable($interviews) ? count($interviews) : 0;
        @endphp

        @if($interviewCount > 0)
            <div class="space-y-4">
                @foreach($interviews->take(5) as $interview)
                    <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex items-center flex-1">
                            <div class="bg-accent-themed-primary/10 p-2 rounded-lg mr-4">
                                <span class="text-lg">{{ $interview->getTypeIcon() }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-themed-primary">{{ $interview->title }}</h4>
                                <p class="text-sm text-themed-secondary">
                                    {{ $interview->type_label }} • {{ $interview->difficulty_label }}
                                    @if($interview->overall_score)
                                        • Score: {{ number_format($interview->overall_score, 1) }}%
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 text-sm rounded-full bg-accent-themed-primary/10 text-accent-themed-primary font-medium">
                                {{ $interview->status_label }}
                            </span>
                            @if($interview->isScheduled())
                                <button wire:click="startInterview({{ $interview->id }})"
                                    class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                    Start
                                </button>
                            @elseif($interview->isCompleted())
                                <button wire:click="viewResults({{ $interview->id }})"
                                    class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-4 py-2 rounded-lg transition-colors text-sm">
                                    Results
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="mx-auto w-24 h-24 bg-themed-tertiary rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-themed-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-themed-secondary mb-4">No interviews yet. Create your first mock interview to get started!</p>
                <button wire:click="$set('showCreateForm', true)"
                    class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg transition-colors">
                    Create Interview
                </button>
            </div>
        @endif
    </div>
</div>