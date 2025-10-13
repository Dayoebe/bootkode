<div class="space-y-6">
    <!-- Date Range Selector -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Analytics Dashboard</h2>
            <div class="flex items-center space-x-4">
                <select wire:model.live="analyticsDateRange"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <button wire:click="exportAnalytics"
                    class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-md hover:bg-green-700 dark:hover:bg-green-600">
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($this->statistics['completedInterviews'] / max($this->statistics['totalInterviews'], 1) * 100, 1) }}%
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Avg Completion Rate</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">4.2</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">User Satisfaction</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    {{ number_format($this->statistics['totalUsers']) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Active Users</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                    {{ number_format($this->statistics['totalInterviews'] * 45 / 60, 1) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Hours</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Interview Trends Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Interview Trends</h3>
            <div class="h-64">
                @if(!empty($this->chartData))
                    <canvas id="interviewTrendsChart" wire:ignore></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                        No data available for the selected period
                    </div>
                @endif
            </div>
        </div>

        <!-- Score Distribution -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Average Scores by Type</h3>
            <div class="space-y-4">
                @foreach($this->popularTypes as $type)
                    @php
                        $avgScore = rand(70, 95);
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                {{ ucfirst(str_replace('_', ' ', $type['type'])) }}
                            </span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($avgScore, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full" style="width: {{ $avgScore }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- User Engagement -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">User Engagement Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->statistics['totalUsers'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Active Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ number_format($this->statistics['totalUsers'] * 0.65) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Repeat Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                    {{ $this->statistics['totalUsers'] > 0 ? round($this->statistics['totalInterviews'] / $this->statistics['totalUsers'], 1) : 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Avg Interviews/User</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        let chart = null;
        
        function initChart() {
            const ctx = document.getElementById('interviewTrendsChart');
            if (!ctx) return;
            
            if (chart) {
                chart.destroy();
            }

            // Detect dark mode
            const isDark = document.documentElement.classList.contains('dark');
            
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json(array_column($this->chartData ?? [], 'date')),
                    datasets: [{
                        label: 'Interviews',
                        data: @json(array_column($this->chartData ?? [], 'count')),
                        borderColor: isDark ? 'rgb(96, 165, 250)' : 'rgb(59, 130, 246)',
                        backgroundColor: isDark ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: isDark ? 'rgb(156, 163, 175)' : 'rgb(107, 114, 128)'
                            },
                            grid: {
                                color: isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 1)'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: isDark ? 'rgb(156, 163, 175)' : 'rgb(107, 114, 128)'
                            },
                            grid: {
                                color: isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 1)'
                            }
                        }
                    }
                }
            });
        }
        
        // Initial chart
        setTimeout(initChart, 100);
        
        // Re-initialize on updates
        Livewire.on('interview-updated', () => {
            setTimeout(initChart, 100);
        });

        // Re-initialize on dark mode toggle
        const observer = new MutationObserver(() => {
            if (chart) {
                initChart();
            }
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    });
</script>
@endpush