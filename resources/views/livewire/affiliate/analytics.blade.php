   {{-- resources/views/livewire/affiliate/analytics.blade.php --}}
   <div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h1 class=" font-bold text-content bk-page-title">Performance Analytics</h1>
                <p class="text-content-muted">Deep insights into your affiliate performance</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <select aria-label="Analytics period" wire:model.live="selectedPeriod" class="rounded-md border-line shadow-sm focus:border-brand focus:ring-brand/30">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <select aria-label="Analytics chart" wire:model.live="chartType" class="rounded-md border-line shadow-sm focus:border-brand focus:ring-brand/30">
                    <option value="commission">Commission Trends</option>
                    <option value="referrals">Referral Trends</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Performance Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-brand">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Period Revenue</p>
                    <p class="text-2xl font-bold text-content">₦{{ number_format($analytics['period_stats']['total_commission'], 2) }}</p>
                </div>
                <div class="bg-brand/10 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-brand text-xl" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xs text-content-muted mt-2">+{{ number_format($analytics['period_stats']['total_sales']) }} sales</p>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Avg Daily</p>
                    <p class="text-2xl font-bold text-content">₦{{ number_format($analytics['period_stats']['average_daily'], 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-calendar-day text-green-600 text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-brand">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Conversion Rate</p>
                    <p class="text-2xl font-bold text-content">{{ $conversionData['overall_conversion_rate'] }}%</p>
                </div>
                <div class="bg-brand/10 p-3 rounded-lg">
                    <i class="fas fa-percentage text-brand text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Best Day</p>
                    <p class="text-2xl font-bold text-content">₦{{ $analytics['period_stats']['best_day'] ? number_format($analytics['period_stats']['best_day']['total'], 2) : '0.00' }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-trophy text-yellow-600 text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Main Chart -->
        <div class="bg-surface rounded-lg shadow-sm p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-content mb-4">
                {{ $chartType === 'commission' ? 'Commission' : 'Referral' }} Trends
            </h3>
            <div class="h-80">
                @if($chartData->isNotEmpty())
                    <canvas id="trendsChart"></canvas>
                @else
                    <div class="flex items-center justify-center h-full text-content-muted">
                        <div class="text-center">
                            <i class="fas fa-chart-area text-4xl mb-4" aria-hidden="true"></i>
                            <p>No data available for this period</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Conversion Funnel and Traffic Sources -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
     <!-- Conversion Funnel -->
     <div class="bg-surface rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-content mb-4">Conversion Funnel</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-brand/10 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-action rounded-full flex items-center justify-center text-action-foreground text-sm font-semibold">1</div>
                    <span class="text-sm font-medium">Clicks</span>
                </div>
                <span class="text-lg font-bold">{{ number_format($conversionData['clicks']) }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">2</div>
                    <span class="text-sm font-medium">Registrations</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-bold">{{ number_format($conversionData['registrations']) }}</span>
                    <p class="text-xs text-content-muted">{{ $conversionData['click_to_register_rate'] }}% conversion</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-brand/10 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-action rounded-full flex items-center justify-center text-action-foreground text-sm font-semibold">3</div>
                    <span class="text-sm font-medium">Purchases</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-bold">{{ number_format($conversionData['purchases']) }}</span>
                    <p class="text-xs text-content-muted">{{ $conversionData['register_to_purchase_rate'] }}% conversion</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Sources -->
    <div class="bg-surface rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-content mb-4">Traffic Sources</h3>
        @if(count($trafficSources) > 0)
            <div class="space-y-3">
                @foreach($trafficSources as $source)
                    <div class="flex items-center justify-between p-3 bg-surface-muted rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-action rounded-full"></div>
                            <span class="text-sm font-medium">{{ $source['source'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-semibold">{{ $source['count'] }}</span>
                            <p class="text-xs text-content-muted">{{ round(($source['count'] / array_sum(array_column($trafficSources, 'count'))) * 100, 1) }}%</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-content-muted">
                <i class="fas fa-traffic-light text-4xl mb-4" aria-hidden="true"></i>
                <p>No traffic source data available</p>
            </div>
        @endif
    </div>
</div>

<!-- Top Performing Courses -->
@if($analytics['top_courses']->isNotEmpty())
    <div class="bg-surface rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-content mb-4">Top Performing Courses</h3>
        <div class="overflow-x-auto">
            <x-ui.table label="Analytics records"><table class="min-w-full divide-y divide-line">
                <thead class="bg-surface-muted">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Course</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Sales</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Commission</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Avg per Sale</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-line">
                    @foreach($analytics['top_courses'] as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-content">{{ Str::limit($course->course->title, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-content">{{ $course->sales_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">₦{{ number_format($course->total_commission, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-content">₦{{ number_format($course->average_per_sale, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table></x-ui.table>
        </div>
    </div>
@endif
</div>

<script>
@if($chartData->isNotEmpty())
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData->pluck('date')->map(function($date) {
                    return \Carbon\Carbon::parse($date)->format('M d');
                })) !!},
                datasets: [{
                    label: '{{ $chartType === "commission" ? "Commission (₦)" : "Referrals" }}',
                    data: {!! json_encode($chartData->pluck('value')) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
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
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '{{ $chartType === "commission" ? "₦" : "" }}' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
@endif
</script>
