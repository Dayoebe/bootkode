<div class="space-y-6">
    <!-- Date Range Selector -->
    <div class="shadow rounded-lg p-6 bg-themed-secondary border border-themed-primary">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-themed-primary">Analytics Dashboard</h2>
            <div class="flex flex-wrap items-center gap-4">
                <select aria-label="Analytics date range" wire:model.live="analyticsDateRange"
                    class="px-3 py-2 border border-themed-primary rounded-md focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <button wire:click="exportAnalytics"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors font-medium">
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-lg shadow p-6 bg-themed-secondary border border-themed-primary">
            <div class="text-center">
                <div class="text-3xl font-bold text-brand">
                    {{ number_format($this->statistics['completedInterviews'] / max($this->statistics['totalInterviews'], 1) * 100, 1) }}%
                </div>
                <div class="text-sm text-themed-secondary">Avg Completion Rate</div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 bg-themed-secondary border border-themed-primary">
            <div class="text-center">
                <div class="text-sm font-medium text-themed-secondary">Not collected</div>
                <div class="text-sm text-themed-secondary">User Satisfaction</div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 bg-themed-secondary border border-themed-primary">
            <div class="text-center">
                <div class="text-3xl font-bold text-brand">
                    {{ number_format($this->statistics['totalUsers']) }}
                </div>
                <div class="text-sm text-themed-secondary">Active Users</div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 bg-themed-secondary border border-themed-primary">
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600">
                    {{ number_format($this->statistics['plannedHours'], 1) }}
                </div>
                <div class="text-sm text-themed-secondary">Planned Interview Hours</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Interview Trends Chart -->
        <div class="min-w-0 shadow rounded-lg p-6 bg-themed-secondary border border-themed-primary">
            <h3 class="text-lg font-medium mb-4 text-themed-primary">Interview Trends</h3>
            <div class="h-64">
                @if(!empty($this->chartData))
                    <div class="h-full" wire:key="interview-trends-{{ $analyticsDateRange }}"
                        x-data="bootkodeInterviewTrendChart(@js($this->chartData))">
                        <canvas x-ref="canvas" aria-hidden="true"></canvas>
                    </div>
                @else
                    <div class="h-full flex items-center justify-center text-themed-secondary">
                        No data available for the selected period
                    </div>
                @endif
            </div>
            <details class="mt-4">
                <summary class="cursor-pointer text-sm font-medium text-themed-primary">Interview trend data</summary>
                <x-ui.table label="Recorded interview trends">
                    <table>
                        <thead><tr><th scope="col">Date</th><th scope="col">Interviews</th></tr></thead>
                        <tbody>
                            @forelse($this->chartData as $point)
                                <tr><td>{{ $point['date'] }}</td><td>{{ $point['count'] }}</td></tr>
                            @empty
                                <tr><td colspan="2">No interviews in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-ui.table>
            </details>
        </div>

        <!-- Score Distribution -->
        <div class="shadow rounded-lg p-6 bg-themed-secondary border border-themed-primary">
            <h3 class="text-lg font-medium mb-4 text-themed-primary">Average Scores by Type</h3>
            <div class="space-y-4">
                @foreach($this->popularTypes as $type)
                    @php
                        $avgScore = $type['average_score'];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-themed-primary">
                                {{ ucfirst(str_replace('_', ' ', $type['type'])) }}
                            </span>
                            <span class="text-themed-primary">{{ $avgScore !== null ? number_format($avgScore, 1).'%' : 'Not scored' }}</span>
                        </div>
                        <div class="w-full rounded-full h-2 bg-themed-tertiary">
                            <div class="h-2 rounded-full transition-all duration-300"
                                style="width: {{ min(100, max(0, (float) $avgScore)) }}%; background: rgb(var(--accent-primary));"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- User Engagement -->
    <div class="shadow rounded-lg p-6 bg-themed-secondary border border-themed-primary">
        <h3 class="text-lg font-medium mb-4 text-themed-primary">User Engagement Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-brand">{{ $this->statistics['totalUsers'] }}</div>
                <div class="text-sm text-themed-secondary">Active Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ number_format($this->statistics['repeatUsers']) }}
                </div>
                <div class="text-sm text-themed-secondary">Repeat Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-brand">
                    {{ $this->statistics['totalUsers'] > 0 ? round($this->statistics['totalInterviews'] / $this->statistics['totalUsers'], 1) : 0 }}
                </div>
                <div class="text-sm text-themed-secondary">Avg Interviews/User</div>
            </div>
        </div>
    </div>
</div>
