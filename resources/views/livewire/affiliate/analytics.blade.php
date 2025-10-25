   {{-- resources/views/livewire/affiliate/analytics.blade.php --}}
   <div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Performance Analytics</h1>
                <p class="text-gray-600">Deep insights into your affiliate performance</p>
            </div>
            <div class="flex items-center space-x-4">
                <select wire:model.live="selectedPeriod" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <select wire:model.live="chartType" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="commission">Commission Trends</option>
                    <option value="referrals">Referral Trends</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Performance Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Period Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($analytics['period_stats']['total_commission'], 2) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">+{{ number_format($analytics['period_stats']['total_sales']) }} sales</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Avg Daily</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($analytics['period_stats']['average_daily'], 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Conversion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $conversionData['overall_conversion_rate'] }}%</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Best Day</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ $analytics['period_stats']['best_day'] ? number_format($analytics['period_stats']['best_day']['total'], 2) : '0.00' }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-trophy text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Main Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                {{ $chartType === 'commission' ? 'Commission' : 'Referral' }} Trends
            </h3>
            <div class="h-80">
                @if($chartData->isNotEmpty())
                    <canvas id="trendsChart"></canvas>
                @else
                    <div class="flex items-center justify-center h-full text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-chart-area text-4xl mb-4"></i>
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
     <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversion Funnel</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">1</div>
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
                    <p class="text-xs text-gray-500">{{ $conversionData['click_to_register_rate'] }}% conversion</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">3</div>
                    <span class="text-sm font-medium">Purchases</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-bold">{{ number_format($conversionData['purchases']) }}</span>
                    <p class="text-xs text-gray-500">{{ $conversionData['register_to_purchase_rate'] }}% conversion</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Sources -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Traffic Sources</h3>
        @if(count($trafficSources) > 0)
            <div class="space-y-3">
                @foreach($trafficSources as $source)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm font-medium">{{ $source['source'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-semibold">{{ $source['count'] }}</span>
                            <p class="text-xs text-gray-500">{{ round(($source['count'] / array_sum(array_column($trafficSources, 'count'))) * 100, 1) }}%</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-traffic-light text-4xl mb-4"></i>
                <p>No traffic source data available</p>
            </div>
        @endif
    </div>
</div>

<!-- Top Performing Courses -->
@if($analytics['top_courses']->isNotEmpty())
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Courses</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg per Sale</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($analytics['top_courses'] as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($course->course->title, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $course->sales_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">₦{{ number_format($course->total_commission, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₦{{ number_format($course->average_per_sale, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
