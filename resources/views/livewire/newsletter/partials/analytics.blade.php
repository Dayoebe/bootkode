{{-- Analytics View --}}
{{-- resources/views/livewire/newsletter/partials/analytics.blade.php --}}
<div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
            <select wire:model.live="dateRange" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
                <option value="all">All time</option>
            </select>
        </div>
        
        @if($campaignsForSelect->count() > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Details</label>
                <select wire:model.live="selectedCampaign" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a campaign...</option>
                    @foreach($campaignsForSelect as $campaign)
                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-paper-plane text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Sent</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($overviewStats['total_sent']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope-open text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Avg Open Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $overviewStats['avg_open_rate'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-mouse-pointer text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Avg Click Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $overviewStats['avg_click_rate'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Bounces</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($overviewStats['total_bounces']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriber Growth Chart -->
    <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Subscriber Growth</h3>
        <div class="h-64 flex items-center justify-center">
            @if($subscriberGrowth->count() > 0)
                <div class="w-full">
                    <canvas id="subscriberGrowthChart" width="400" height="200"></canvas>
                </div>
            @else
                <p class="text-gray-500">No data available for the selected period.</p>
            @endif
        </div>
    </div>

    <!-- Campaign-specific Analytics -->
    @if($campaignStats)
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $campaignStats['campaign']->name }} Performance
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($campaignStats['campaign']->sent_count) }}</div>
                        <div class="text-sm text-gray-500">Sent</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $campaignStats['campaign']->open_rate }}%</div>
                        <div class="text-sm text-gray-500">Open Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $campaignStats['campaign']->click_rate }}%</div>
                        <div class="text-sm text-gray-500">Click Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $campaignStats['campaign']->bounce_rate }}%</div>
                        <div class="text-sm text-gray-500">Bounce Rate</div>
                    </div>
                </div>

                <!-- Top Clicked Links -->
                @if($campaignStats['top_clicked_links']->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Top Clicked Links</h4>
                        <div class="space-y-2">
                            @foreach($campaignStats['top_clicked_links'] as $link)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div class="flex-1 truncate">
                                        <a href="{{ $link->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                            {{ $link->url }}
                                        </a>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $link->clicks }} clicks
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:navigated', function () {
    @if($subscriberGrowth->count() > 0)
        const ctx = document.getElementById('subscriberGrowthChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($subscriberGrowth->pluck('date')),
                    datasets: [{
                        label: 'New Subscribers',
                        data: @json($subscriberGrowth->pluck('count')),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    @endif
});
</script>
