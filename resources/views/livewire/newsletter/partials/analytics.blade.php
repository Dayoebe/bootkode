{{-- Analytics View --}}
{{-- resources/views/livewire/newsletter/partials/analytics.blade.php --}}
<div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <div>
            <label class="block text-sm font-medium text-content mb-1">Date Range</label>
            <select aria-label="Date range" wire:model.live="dateRange" class="px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
                <option value="all">All time</option>
            </select>
        </div>

        @if($campaignsForSelect->count() > 0)
            <div>
                <label class="block text-sm font-medium text-content mb-1">Campaign Details</label>
                <select aria-label="Selected campaign" wire:model.live="selectedCampaign" class="px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30">
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
        <div class="bg-surface p-6 rounded-lg shadow border border-line">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-brand/10 rounded-lg flex items-center justify-center">
                        <i aria-hidden="true" class="fas fa-paper-plane text-brand"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-content-muted">Total Sent</p>
                    <p class="text-2xl font-semibold text-content">{{ number_format($overviewStats['total_sent']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-surface p-6 rounded-lg shadow border border-line">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i aria-hidden="true" class="fas fa-envelope-open text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-content-muted">Avg Open Rate</p>
                    <p class="text-2xl font-semibold text-content">{{ $overviewStats['avg_open_rate'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-surface p-6 rounded-lg shadow border border-line">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-brand/10 rounded-lg flex items-center justify-center">
                        <i aria-hidden="true" class="fas fa-mouse-pointer text-brand"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-content-muted">Avg Click Rate</p>
                    <p class="text-2xl font-semibold text-content">{{ $overviewStats['avg_click_rate'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-surface p-6 rounded-lg shadow border border-line">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i aria-hidden="true" class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-content-muted">Total Bounces</p>
                    <p class="text-2xl font-semibold text-content">{{ number_format($overviewStats['total_bounces']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriber Growth Chart -->
    <div class="bg-surface p-6 rounded-lg shadow border border-line">
        <h3 class="text-lg font-medium text-content mb-4">Subscriber Growth</h3>
        <div class="h-64 flex items-center justify-center">
            @if($subscriberGrowth->count() > 0)
                <div class="w-full">
                    <canvas id="subscriberGrowthChart" width="400" height="200"></canvas>
                </div>
            @else
                <p class="text-content-muted">No data available for the selected period.</p>
            @endif
        </div>
    </div>

    <!-- Campaign-specific Analytics -->
    @if($campaignStats)
        <div class="space-y-6">
            <div class="bg-surface p-6 rounded-lg shadow border border-line">
                <h3 class="text-lg font-medium text-content mb-4">
                    {{ $campaignStats['campaign']->name }} Performance
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-brand">{{ number_format($campaignStats['campaign']->sent_count) }}</div>
                        <div class="text-sm text-content-muted">Sent</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $campaignStats['campaign']->open_rate }}%</div>
                        <div class="text-sm text-content-muted">Open Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-brand">{{ $campaignStats['campaign']->click_rate }}%</div>
                        <div class="text-sm text-content-muted">Click Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $campaignStats['campaign']->bounce_rate }}%</div>
                        <div class="text-sm text-content-muted">Bounce Rate</div>
                    </div>
                </div>

                <!-- Top Clicked Links -->
                @if($campaignStats['top_clicked_links']->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-content mb-3">Top Clicked Links</h4>
                        <div class="space-y-2">
                            @foreach($campaignStats['top_clicked_links'] as $link)
                                <div class="flex justify-between items-center p-3 bg-surface-muted rounded">
                                    <div class="flex-1 truncate">
                                        <a href="{{ $link->url }}" target="_blank" class="text-brand hover:text-brand text-sm">
                                            {{ $link->url }}
                                        </a>
                                    </div>
                                    <div class="text-sm font-medium text-content">
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
function renderNewsletterAnalyticsCharts() {
    @if($subscriberGrowth->count() > 0)
        const ctx = document.getElementById('subscriberGrowthChart')?.getContext('2d');
        if (ctx && window.Chart) {
            window.bootkodeNewsletterCharts ??= {};
            window.bootkodeNewsletterCharts.subscriberGrowthChart?.destroy();

            const theme = getComputedStyle(document.documentElement);
            const accent = `rgb(${theme.getPropertyValue('--accent-primary').trim() || '59 130 246'})`;
            const accentSoft = `rgb(${theme.getPropertyValue('--accent-primary').trim() || '59 130 246'} / 0.14)`;
            const text = `rgb(${theme.getPropertyValue('--text-secondary').trim() || '107 114 128'})`;
            const grid = `rgb(${theme.getPropertyValue('--border-primary').trim() || '229 231 235'} / 0.7)`;

            window.bootkodeNewsletterCharts.subscriberGrowthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($subscriberGrowth->pluck('date')),
                    datasets: [{
                        label: 'New Subscribers',
                        data: @json($subscriberGrowth->pluck('count')),
                        borderColor: accent,
                        backgroundColor: accentSoft,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: text }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: text },
                            grid: { color: grid }
                        },
                        x: {
                            ticks: { color: text },
                            grid: { color: grid }
                        }
                    }
                }
            });
        }
    @endif
}

renderNewsletterAnalyticsCharts();
document.addEventListener('livewire:navigated', renderNewsletterAnalyticsCharts);
</script>
