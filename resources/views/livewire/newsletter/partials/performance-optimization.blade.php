{{-- Performance & Optimization View --}}
{{-- resources/views/livewire/newsletter/partials/performance-optimization.blade.php --}}
<div class="space-y-6">
    <!-- Header and Tabs -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex space-x-1 bg-surface-muted p-1 rounded-lg">
            <button
                wire:click="setActiveSection('deliverability')"
                class="{{ $activeSection === 'deliverability' ? 'bg-white text-brand shadow-sm' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 text-sm font-medium rounded-md transition-all"
            >
                <i class="fas fa-shield-check mr-2" aria-hidden="true"></i>Deliverability
            </button>
            <button
                wire:click="setActiveSection('send-time')"
                class="{{ $activeSection === 'send-time' ? 'bg-white text-brand shadow-sm' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 text-sm font-medium rounded-md transition-all"
            >
                <i class="fas fa-clock mr-2" aria-hidden="true"></i>Send Time
            </button>
            <button
                wire:click="setActiveSection('list-health')"
                class="{{ $activeSection === 'list-health' ? 'bg-white text-brand shadow-sm' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 text-sm font-medium rounded-md transition-all"
            >
                <i class="fas fa-heartbeat mr-2" aria-hidden="true"></i>List Health
            </button>
            <button
                wire:click="setActiveSection('ab-testing')"
                class="{{ $activeSection === 'ab-testing' ? 'bg-white text-brand shadow-sm' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 text-sm font-medium rounded-md transition-all"
            >
                <i class="fas fa-flask mr-2" aria-hidden="true"></i>A/B Testing
            </button>
        </div>

        <div class="flex items-center space-x-2">
            <select aria-label="Date range" wire:model.live="dateRange" class="text-sm border border-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/30">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="all">All time</option>
            </select>
        </div>
    </div>

    <!-- Deliverability Monitor -->
    @if($activeSection === 'deliverability')
        <div class="space-y-6">
            <!-- Deliverability Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-content-muted">Delivery Rate</p>
                            <p class="text-2xl font-semibold text-content">{{ $deliverabilityStats['delivery_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-content-muted">Bounce Rate</p>
                            <p class="text-2xl font-semibold text-content">{{ $deliverabilityStats['bounce_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-times text-yellow-600" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-content-muted">Unsubscribe Rate</p>
                            <p class="text-2xl font-semibold text-content">{{ $deliverabilityStats['unsubscribe_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-brand/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-paper-plane text-brand" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-content-muted">Total Sent</p>
                            <p class="text-2xl font-semibold text-content">{{ number_format($deliverabilityStats['total_sent']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bounce Reasons -->
            @if($deliverabilityStats['bounce_reasons']->count() > 0)
                <div class="bg-surface rounded-lg shadow border border-line">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-content mb-4">Bounce Analysis</h3>
                        <div class="space-y-4">
                            @foreach($deliverabilityStats['bounce_reasons'] as $reason)
                                <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-red-800">{{ $reason->reason ?: 'Unknown' }}</p>
                                        <p class="text-sm text-red-600">{{ $reason->count }} occurrences</p>
                                    </div>
                                    <div class="text-red-600">
                                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Domain Reputation -->
            @if($deliverabilityStats['domain_stats']->count() > 0)
                <div class="bg-surface rounded-lg shadow border border-line">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-content mb-4">Domain Performance</h3>
                        <div class="overflow-x-auto">
                            <x-ui.table label="Performance Optimization records"><table class="min-w-full divide-y divide-line">
                                <thead class="bg-surface-muted">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Domain</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Sent</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Bounces</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Bounce Rate</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @foreach($deliverabilityStats['domain_stats'] as $domain => $stats)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-content">{{ $domain }}</td>
                                            <td class="px-6 py-4 text-sm text-content">{{ number_format($stats['sent']) }}</td>
                                            <td class="px-6 py-4 text-sm text-content">{{ number_format($stats['bounces']) }}</td>
                                            <td class="px-6 py-4 text-sm text-content">{{ $stats['bounce_rate'] }}%</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $stats['bounce_rate'] < 5 ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $stats['bounce_rate'] >= 5 && $stats['bounce_rate'] < 10 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $stats['bounce_rate'] >= 10 ? 'bg-red-100 text-red-800' : '' }}
                                                ">
                                                    {{ $stats['bounce_rate'] < 5 ? 'Excellent' : ($stats['bounce_rate'] < 10 ? 'Good' : 'Poor') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table></x-ui.table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Send Time Optimization -->
    @if($activeSection === 'send-time')
        <div class="space-y-6">
            <!-- Best Times Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <h3 class="text-lg font-medium text-content mb-4">Best Hour to Send</h3>
                    @if($sendTimeAnalysis['best_hour'])
                        <div class="text-center">
                            <div class="text-3xl font-bold text-brand">{{ $sendTimeAnalysis['best_hour']->hour }}:00</div>
                            <div class="text-sm text-content-muted mt-2">
                                {{ round($sendTimeAnalysis['best_hour']['avg_' . $selectedMetric], 2) }}% {{ $selectedMetric === 'open_rate' ? 'open rate' : 'click rate' }}
                            </div>
                            <div class="text-xs text-content-muted mt-1">
                                Based on {{ $sendTimeAnalysis['best_hour']->campaign_count }} campaigns
                            </div>
                        </div>
                    @else
                        <p class="text-content-muted text-center">Not enough data available</p>
                    @endif
                </div>

                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <h3 class="text-lg font-medium text-content mb-4">Best Day to Send</h3>
                    @if($sendTimeAnalysis['best_day'])
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$sendTimeAnalysis['best_day']->day_of_week - 1] }}
                            </div>
                            <div class="text-sm text-content-muted mt-2">
                                {{ round($sendTimeAnalysis['best_day']['avg_' . $selectedMetric], 2) }}% {{ $selectedMetric === 'open_rate' ? 'open rate' : 'click rate' }}
                            </div>
                            <div class="text-xs text-content-muted mt-1">
                                Based on {{ $sendTimeAnalysis['best_day']->campaign_count }} campaigns
                            </div>
                        </div>
                    @else
                        <p class="text-content-muted text-center">Not enough data available</p>
                    @endif
                </div>
            </div>

            <!-- Metric Selection -->
            <div class="bg-surface p-6 rounded-lg shadow border border-line">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-content">Performance by Time</h3>
                    <select aria-label="Selected metric" wire:model.live="selectedMetric" class="text-sm border border-line rounded-lg px-3 py-2">
                        <option value="open_rate">Open Rate</option>
                        <option value="click_rate">Click Rate</option>
                    </select>
                </div>

                <!-- Hourly Performance Chart Placeholder -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-content mb-3">Hourly Performance</h4>
                    <div class="h-64 bg-surface-muted rounded-lg flex items-center justify-center">
                        @if($sendTimeAnalysis['hourly_stats']->count() > 0)
                            <div class="w-full p-4">
                                <canvas id="hourlyChart" width="400" height="200"></canvas>
                            </div>
                        @else
                            <p class="text-content-muted">No hourly data available for the selected period</p>
                        @endif
                    </div>
                </div>

                <!-- Daily Performance Chart Placeholder -->
                <div>
                    <h4 class="text-md font-medium text-content mb-3">Daily Performance</h4>
                    <div class="h-64 bg-surface-muted rounded-lg flex items-center justify-center">
                        @if($sendTimeAnalysis['daily_stats']->count() > 0)
                            <div class="w-full p-4">
                                <canvas id="dailyChart" width="400" height="200"></canvas>
                            </div>
                        @else
                            <p class="text-content-muted">No daily data available for the selected period</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- List Health Dashboard -->
    @if($activeSection === 'list-health')
        <div class="space-y-6">
            <!-- Health Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-brand/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-brand" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-content-muted">Total Subscribers</p>
                            <p class="text-2xl font-semibold text-content">{{ number_format($listHealth['total_subscribers']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-check text-green-600" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-content-muted">Active Subscribers</p>
                            <p class="text-2xl font-semibold text-content">{{ number_format($listHealth['active_subscribers']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-brand/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-heart text-brand" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-content-muted">Engagement Rate</p>
                            <p class="text-2xl font-semibold text-content">{{ $listHealth['engaged_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface p-6 rounded-lg shadow border border-line">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-content-muted">At Risk</p>
                            <p class="text-2xl font-semibold text-content">{{ number_format($listHealth['inactive_count']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscriber Segments -->
            <div class="bg-surface rounded-lg shadow border border-line">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-content">Subscriber Segments</h3>
                        <button
                            wire:click="exportInactiveSubscribers"
                            class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors"
                        >
                            <i class="fas fa-download mr-2" aria-hidden="true"></i>Export Inactive
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($listHealth['segments']['highly_engaged']) }}</div>
                            <div class="text-sm text-green-800">Highly Engaged</div>
                            <div class="text-xs text-green-600 mt-1">Opened within 7 days</div>
                        </div>

                        <div class="bg-brand/10 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-brand">{{ number_format($listHealth['segments']['moderately_engaged']) }}</div>
                            <div class="text-sm text-brand">Moderately Engaged</div>
                            <div class="text-xs text-brand mt-1">Opened 7-30 days ago</div>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ number_format($listHealth['segments']['low_engaged']) }}</div>
                            <div class="text-sm text-yellow-800">Low Engaged</div>
                            <div class="text-xs text-yellow-600 mt-1">Opened 30-90 days ago</div>
                        </div>

                        <div class="bg-red-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-red-600">{{ number_format($listHealth['segments']['at_risk']) }}</div>
                            <div class="text-sm text-red-800">At Risk</div>
                            <div class="text-xs text-red-600 mt-1">No opens in 90+ days</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Growth Trends -->
            <div class="bg-surface rounded-lg shadow border border-line">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-content mb-4">Growth Trends (Last 12 Months)</h3>
                    <div class="h-64 bg-surface-muted rounded-lg flex items-center justify-center">
                        @if($listHealth['growth_data']->count() > 0)
                            <div class="w-full p-4">
                                <canvas id="growthChart" width="400" height="200"></canvas>
                            </div>
                        @else
                            <p class="text-content-muted">No growth data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- A/B Testing Tools -->
    @if($activeSection === 'ab-testing')
        <div class="space-y-6">
            <!-- Test Suggestions -->
            <div class="bg-surface rounded-lg shadow border border-line">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-content mb-4">Recommended Tests</h3>
                    <div class="space-y-4">
                        @forelse($abTesting['suggested_tests'] as $suggestion)
                            <div class="border border-line rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-content">{{ $suggestion['type'] }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $suggestion['priority'] === 'High' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}
                                    ">
                                        {{ $suggestion['priority'] }} Priority
                                    </span>
                                </div>
                                <p class="text-sm text-content-muted mb-2">{{ $suggestion['recommendation'] }}</p>
                                <div class="bg-brand/10 p-3 rounded text-sm">
                                    <strong>Test Idea:</strong> {{ $suggestion['test_idea'] }}
                                </div>
                            </div>
                        @empty
                            <p class="text-content-muted">No test suggestions available. Send more campaigns to get recommendations.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Subject Line Performance -->
            @if($abTesting['subject_variations']->count() > 0)
                <div class="bg-surface rounded-lg shadow border border-line">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-content mb-4">Subject Line Performance</h3>
                        <div class="overflow-x-auto">
                            <x-ui.table label="Performance Optimization records"><table class="min-w-full divide-y divide-line">
                                <thead class="bg-surface-muted">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Subject Line</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Avg Open Rate</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Times Used</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Performance</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @foreach($abTesting['subject_variations'] as $variation)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-content max-w-xs truncate">{{ $variation->subject }}</td>
                                            <td class="px-6 py-4 text-sm text-content">{{ round($variation->avg_open_rate, 2) }}%</td>
                                            <td class="px-6 py-4 text-sm text-content">{{ $variation->usage_count }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $variation->avg_open_rate >= 25 ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $variation->avg_open_rate >= 15 && $variation->avg_open_rate < 25 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $variation->avg_open_rate < 15 ? 'bg-red-100 text-red-800' : '' }}
                                                ">
                                                    {{ $variation->avg_open_rate >= 25 ? 'Excellent' : ($variation->avg_open_rate >= 15 ? 'Good' : 'Poor') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table></x-ui.table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Send Time Variations -->
            @if($abTesting['send_time_variations']->count() > 0)
                <div class="bg-surface rounded-lg shadow border border-line">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-content mb-4">Send Time Performance Comparison</h3>
                        <div class="overflow-x-auto">
                            <x-ui.table label="Performance Optimization records"><table class="min-w-full divide-y divide-line">
                                <thead class="bg-surface-muted">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Hour</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Avg Open Rate</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Avg Click Rate</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Campaigns</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    @foreach($abTesting['send_time_variations'] as $variation)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-content">{{ $variation->send_hour }}:00</td>
                                            <td class="px-6 py-4 text-sm text-content">{{ round($variation->avg_open_rate, 2) }}%</td>
                                            <td class="px-6 py-4 text-sm text-content">{{ round($variation->avg_click_rate, 2) }}%</td>
                                            <td class="px-6 py-4 text-sm text-content-muted">{{ $variation->campaign_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table></x-ui.table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
<!-- ... existing code ... -->
<!-- ... existing code ... -->

<script>
    function renderNewsletterPerformanceCharts() {
        window.bootkodeNewsletterCharts ??= {};

        const theme = getComputedStyle(document.documentElement);
        const themeRgb = (name, fallback, alpha = null) => {
            const value = theme.getPropertyValue(name).trim() || fallback;

            return alpha === null ? `rgb(${value})` : `rgb(${value} / ${alpha})`;
        };
        const chartColors = {
            accent: themeRgb('--accent-primary', '59 130 246'),
            accentSoft: themeRgb('--accent-primary', '59 130 246', 0.14),
            secondary: themeRgb('--accent-secondary', '34 197 94'),
            secondarySoft: themeRgb('--accent-secondary', '34 197 94', 0.14),
            text: themeRgb('--text-secondary', '107 114 128'),
            grid: themeRgb('--border-primary', '229 231 235', 0.7),
            danger: 'rgb(239 68 68)',
            dangerSoft: 'rgb(239 68 68 / 0.14)',
        };
        const renderNewsletterChart = (key, context, config) => {
            if (!context || !window.Chart) {
                return;
            }

            window.bootkodeNewsletterCharts[key]?.destroy();
            window.bootkodeNewsletterCharts[key] = new Chart(context, config);
        };

        // Hourly Chart
        @if($activeSection === 'send-time' && $sendTimeAnalysis['hourly_stats']->count() > 0)
            const hourlyCtx = document.getElementById('hourlyChart')?.getContext('2d');
            if (hourlyCtx) {
                const hourlyLabels = @json($sendTimeAnalysis['hourly_stats']->pluck('hour'));
                const hourlyData = @json($sendTimeAnalysis['hourly_stats']->pluck('avg_' . $selectedMetric));

                renderNewsletterChart('hourlyChart', hourlyCtx, {
                    type: 'line',
                    data: {
                        labels: hourlyLabels,
                        datasets: [{
                            label: '{{ ucwords(str_replace("_", " ", $selectedMetric)) }}',
                            data: hourlyData,
                            borderColor: chartColors.accent,
                            backgroundColor: chartColors.accentSoft,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: chartColors.text }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: chartColors.grid },
                                ticks: {
                                    color: chartColors.text,
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                ticks: { color: chartColors.text },
                                grid: { color: chartColors.grid },
                                title: {
                                    display: true,
                                    text: 'Hour of Day',
                                    color: chartColors.text
                                }
                            }
                        }
                    }
                });
            }
        @endif

        // Daily Chart
        @if($activeSection === 'send-time' && $sendTimeAnalysis['daily_stats']->count() > 0)
            const dailyCtx = document.getElementById('dailyChart')?.getContext('2d');
            if (dailyCtx) {
                // Define day names in PHP first
                @php
                    $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                @endphp

                const dailyLabels = @json($sendTimeAnalysis['daily_stats']->map(function($stat) use ($dayNames) {
                    return $dayNames[$stat->day_of_week - 1];
                }));
                const dailyData = @json($sendTimeAnalysis['daily_stats']->pluck('avg_' . $selectedMetric));

                renderNewsletterChart('dailyChart', dailyCtx, {
                    type: 'bar',
                    data: {
                        labels: dailyLabels,
                        datasets: [{
                            label: '{{ ucwords(str_replace("_", " ", $selectedMetric)) }}',
                            data: dailyData,
                            backgroundColor: chartColors.secondary,
                            borderColor: chartColors.secondary,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: chartColors.text }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: chartColors.grid },
                                ticks: {
                                    color: chartColors.text,
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                ticks: { color: chartColors.text },
                                grid: { color: chartColors.grid }
                            }
                        }
                    }
                });
            }
        @endif

        // Growth Chart
        @if($activeSection === 'list-health' && $listHealth['growth_data']->count() > 0)
            const growthCtx = document.getElementById('growthChart')?.getContext('2d');
            if (growthCtx) {
                const growthLabels = @json($listHealth['growth_data']->pluck('month'));
                const newSubscribersData = @json($listHealth['growth_data']->pluck('new_subscribers'));
                const unsubscribesData = @json($listHealth['growth_data']->pluck('unsubscribes'));

                renderNewsletterChart('growthChart', growthCtx, {
                    type: 'line',
                    data: {
                        labels: growthLabels,
                        datasets: [
                            {
                                label: 'New Subscribers',
                                data: newSubscribersData,
                                borderColor: chartColors.secondary,
                                backgroundColor: chartColors.secondarySoft,
                                tension: 0.1
                            },
                            {
                                label: 'Unsubscribes',
                                data: unsubscribesData,
                                borderColor: chartColors.danger,
                                backgroundColor: chartColors.dangerSoft,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: chartColors.text }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { color: chartColors.text },
                                grid: { color: chartColors.grid }
                            },
                            x: {
                                ticks: { color: chartColors.text },
                                grid: { color: chartColors.grid }
                            }
                        }
                    }
                });
            }
        @endif
    }

    renderNewsletterPerformanceCharts();
    document.addEventListener('livewire:navigated', renderNewsletterPerformanceCharts);
    </script>
