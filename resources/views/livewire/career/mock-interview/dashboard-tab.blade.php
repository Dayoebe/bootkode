<div class="space-y-8">
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Create Interview Card -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-themed-primary">Create Interview</h3>
                    <p class="text-themed-secondary text-sm">Set up a new mock interview</p>
                </div>
            </div>
            <button wire:click="$set('showCreateForm', true)"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Create New
            </button>
        </div>

        <!-- Quick Practice Card -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-themed-primary">Quick Practice</h3>
                    <p class="text-themed-secondary text-sm">Start practicing immediately</p>
                </div>
            </div>
            <button wire:click="$set('activeTab', 'practice')"
                class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">
                Start Practice
            </button>
        </div>

        <!-- View Analytics Card -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors font-medium">
                View Reports
            </button>
        </div>

        <!-- Premium Features Card -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-themed-primary">Premium Features</h3>
                    <p class="text-themed-secondary text-sm">Unlock advanced analytics</p>
                </div>
            </div>
            <button class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors font-medium">
                Upgrade Now
            </button>
        </div>
    </div>

    <!-- Recent Interviews -->
    <div class="bg-themed-secondary rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-themed-primary">Recent Interviews</h3>
            <button wire:click="$set('activeTab', 'interviews')"
                class="text-blue-600 hover:text-blue-700 font-medium">
                View All →
            </button>
        </div>

        @if(count($mockInterviews) > 0)
            <div class="space-y-4">
                @foreach($mockInterviews->take(5) as $interview)
                    <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex items-center flex-1">
                            <div class="bg-{{ $interview->getStatusColor() }}-100 p-2 rounded-lg mr-4">
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
                            <span class="px-3 py-1 text-sm rounded-full bg-{{ $interview->getStatusColor() }}-100 text-{{ $interview->getStatusColor() }}-800">
                                {{ $interview->status_label }}
                            </span>
                            @if($interview->isScheduled())
                                <button wire:click="startInterview({{ $interview->id }})"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    Start
                                </button>
                            @elseif($interview->isCompleted())
                                <button wire:click="viewResults({{ $interview->id }})"
                                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                                    Results
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-themed-secondary mb-4">No interviews yet. Create your first mock interview to get started!</p>
                <button wire:click="$set('showCreateForm', true)"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Create Interview
                </button>
            </div>
        @endif
    </div>
</div>