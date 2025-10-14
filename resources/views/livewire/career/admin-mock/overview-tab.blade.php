<div class="space-y-8">
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-lg shadow p-6" style="background-color: rgb(var(--bg-secondary))">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium truncate" style="color: rgb(var(--text-secondary))">Total Interviews</dt>
                        <dd class="text-lg font-medium" style="color: rgb(var(--text-primary))">{{ number_format($this->statistics['totalInterviews']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6" style="background-color: rgb(var(--bg-secondary))">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium truncate" style="color: rgb(var(--text-secondary))">Completed</dt>
                        <dd class="text-lg font-medium" style="color: rgb(var(--text-primary))">{{ number_format($this->statistics['completedInterviews']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6" style="background-color: rgb(var(--bg-secondary))">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium truncate" style="color: rgb(var(--text-secondary))">Average Score</dt>
                        <dd class="text-lg font-medium" style="color: rgb(var(--text-primary))">{{ number_format($this->statistics['averageScore'], 1) }}%</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6" style="background-color: rgb(var(--bg-secondary))">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium truncate" style="color: rgb(var(--text-secondary))">Active Users</dt>
                        <dd class="text-lg font-medium" style="color: rgb(var(--text-primary))">{{ number_format($this->statistics['totalUsers']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Popular Types -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Popular Interview Types -->
        <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
            <h3 class="text-lg font-medium mb-4" style="color: rgb(var(--text-primary))">Popular Interview Types</h3>
            <div class="space-y-3">
                @foreach($this->popularTypes as $type)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium capitalize" style="color: rgb(var(--text-primary))">
                            {{ str_replace('_', ' ', $type['type']) }}
                        </span>
                        <div class="flex items-center">
                            <div class="w-32 rounded-full h-2 mr-3" style="background-color: rgb(var(--bg-tertiary))">
                                <div class="h-2 rounded-full"
                                    style="width: {{ ($type['count'] / max(array_column($this->popularTypes, 'count'))) * 100 }}%; background-color: rgb(var(--accent-primary))">
                                </div>
                            </div>
                            <span class="text-sm" style="color: rgb(var(--text-secondary))">{{ $type['count'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Growth Metrics -->
        <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
            <h3 class="text-lg font-medium mb-4" style="color: rgb(var(--text-primary))">Growth Metrics</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Weekly Growth</span>
                        <span class="text-sm font-bold {{ $this->statistics['weeklyGrowth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $this->statistics['weeklyGrowth'] >= 0 ? '+' : '' }}{{ number_format($this->statistics['weeklyGrowth'], 1) }}%
                        </span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Premium Usage</span>
                        <span class="text-sm font-bold text-purple-600">{{ number_format($this->statistics['premiumUsage']) }}</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Daily Interviews</span>
                        <span class="text-sm font-bold" style="color: rgb(var(--accent-primary))">{{ number_format($this->statistics['dailyInterviews']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="shadow rounded-lg" style="background-color: rgb(var(--bg-secondary))">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium mb-4" style="color: rgb(var(--text-primary))">Recent Activity</h3>
            <div class="flow-root">
                <ul class="-mb-8">
                    @foreach($this->recentActivity as $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5" style="background-color: rgb(var(--border-primary))"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="bg-{{ $activity->getStatusColor() }}-500 h-8 w-8 rounded-full flex items-center justify-center ring-8" style="ring-color: rgb(var(--bg-secondary))">
                                            <span class="text-white text-sm">{{ $activity->getTypeIcon() }}</span>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm" style="color: rgb(var(--text-secondary))">
                                                <span class="font-medium" style="color: rgb(var(--text-primary))">{{ $activity->user->name }}</span>
                                                {{ $activity->isCompleted() ? 'completed' : 'started' }} interview
                                                <span class="font-medium" style="color: rgb(var(--text-primary))">{{ $activity->title }}</span>
                                            </p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap" style="color: rgb(var(--text-secondary))">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>