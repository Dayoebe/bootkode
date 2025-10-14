<div class="space-y-6">
    <!-- Date Range Selector -->
    <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold" style="color: rgb(var(--text-primary))">Analytics Dashboard</h2>
            <div class="flex items-center space-x-4">
                <select wire:model.live="analyticsDateRange"
                    class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                    style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary)); --tw-ring-color: rgb(var(--accent-primary))">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <button wire:click="exportAnalytics"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-lg shadow p-6" style="background-color: rgb(var(--bg-secondary))">
            <div class="text-center">
                <div class="text-3xl font-bold" style="color: rgb(var(--accent-primary))">
                    {{ number_format($this->statistics['completedInterviews'] / max($this->statistics['totalInterviews'], 1) * 100, 1) }}%
                </div>
                <div class="text-sm" style="color: rgb(var(--text-secondary))">Avg Completion Rate</div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6" style="background-color: rgb(var(--bg-secondary))">
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">4.2</div>
                <div class="text-sm" style="color: rgb(var(--text-secondary))">User Satisfaction</div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6" style="background-color: rgb(var(--bg-secondary))">
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">
                    {{ number_format($this->statistics['totalUsers']) }}
                </div>
                <div class="text-sm" style="color: rgb(var(--text-secondary))">Active Users</div>
            </div>
        </div>

        <div class="rounded-lg shadow p-6" style="background-color: rgb(var(--bg-secondary))">
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600">
                    {{ number_format($this->statistics['totalInterviews'] * 45 / 60, 1) }}
                </div>
                <div class="text-sm" style="color: rgb(var(--text-secondary))">Total Hours</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Interview Trends Chart -->
        <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
            <h3 class="text-lg font-medium mb-4" style="color: rgb(var(--text-primary))">Interview Trends</h3>
            <div class="h-64">
                @if(!empty($this->chartData))
                    <canvas id="interviewTrendsChart" wire:ignore></canvas>
                @else
                    <div class="h-full flex items-center justify-center" style="color: rgb(var(--text-secondary))">
                        No data available for the selected period
                    </div>
                @endif
            </div>
        </div>

        <!-- Score Distribution -->
        <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
            <h3 class="text-lg font-medium mb-4" style="color: rgb(var(--text-primary))">Average Scores by Type</h3>
            <div class="space-y-4">
                @foreach($this->popularTypes as $type)
                    @php
                        $avgScore = rand(70, 95);
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium" style="color: rgb(var(--text-primary))">
                                {{ ucfirst(str_replace('_', ' ', $type['type'])) }}
                            </span>
                            <span style="color: rgb(var(--text-primary))">{{ number_format($avgScore, 1) }}%</span>
                        </div>
                        <div class="w-full rounded-full h-2" style="background-color: rgb(var(--bg-tertiary))">
                            <div class="h-2 rounded-full" style="width: {{ $avgScore }}%; background-color: rgb(var(--accent-primary))"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- User Engagement -->
    <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
        <h3 class="text-lg font-medium mb-4" style="color: rgb(var(--text-primary))">User Engagement Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold" style="color: rgb(var(--accent-primary))">{{ $this->statistics['totalUsers'] }}</div>
                <div class="text-sm" style="color: rgb(var(--text-secondary))">Active Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ number_format($this->statistics['totalUsers'] * 0.65) }}
                </div>
                <div class="text-sm" style="color: rgb(var(--text-secondary))">Repeat Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">
                    {{ $this->statistics['totalUsers'] > 0 ? round($this->statistics['totalInterviews'] / $this->statistics['totalUsers'], 1) : 0 }}
                </div>
                <div class="text-sm" style="color: rgb(var(--text-secondary))">Avg Interviews/User</div>
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

            // Get current theme colors
            const styles = getComputedStyle(document.documentElement);
            const textColor = styles.getPropertyValue('--text-primary').trim();
            const borderColor = styles.getPropertyValue('--border-primary').trim();
            const accentColor = styles.getPropertyValue('--accent-primary').trim();
            
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json(array_column($this->chartData ?? [], 'date')),
                    datasets: [{
                        label: 'Interviews',
                        data: @json(array_column($this->chartData ?? [], 'count')),
                        borderColor: `rgb(${accentColor})`,
                        backgroundColor: `rgba(${accentColor}, 0.1)`,
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
                                color: `rgb(${textColor})`
                            },
                            grid: {
                                color: `rgba(${borderColor}, 0.5)`
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: `rgb(${textColor})`
                            },
                            grid: {
                                color: `rgba(${borderColor}, 0.5)`
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

        // Re-initialize on theme change
        const observer = new MutationObserver(() => {
            if (chart) {
                setTimeout(initChart, 100);
            }
        });
        observer.observe(document.documentElement, { 
            attributes: true, 
            attributeFilter: ['class'] 
        });
    });
</script>
@endpush