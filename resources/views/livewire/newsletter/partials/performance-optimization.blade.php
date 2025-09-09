{{-- Performance & Optimization View --}}
{{-- resources/views/livewire/newsletter/partials/performance-optimization.blade.php --}}
<div class="space-y-6">
    <!-- Header and Tabs -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
            <button 
                wire:click="setActiveSection('deliverability')"
                class="{{ $activeSection === 'deliverability' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 text-sm font-medium rounded-md transition-all"
            >
                <i class="fas fa-shield-check mr-2"></i>Deliverability
            </button>
            <button 
                wire:click="setActiveSection('send-time')"
                class="{{ $activeSection === 'send-time' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 text-sm font-medium rounded-md transition-all"
            >
                <i class="fas fa-clock mr-2"></i>Send Time
            </button>
            <button 
                wire:click="setActiveSection('list-health')"
                class="{{ $activeSection === 'list-health' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 text-sm font-medium rounded-md transition-all"
            >
                <i class="fas fa-heartbeat mr-2"></i>List Health
            </button>
            <button 
                wire:click="setActiveSection('ab-testing')"
                class="{{ $activeSection === 'ab-testing' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 text-sm font-medium rounded-md transition-all"
            >
                <i class="fas fa-flask mr-2"></i>A/B Testing
            </button>
        </div>

        <div class="flex items-center space-x-2">
            <select wire:model.live="dateRange" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
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
                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Delivery Rate</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $deliverabilityStats['delivery_rate'] }}%</p>
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
                            <p class="text-sm font-medium text-gray-500">Bounce Rate</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $deliverabilityStats['bounce_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-times text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Unsubscribe Rate</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $deliverabilityStats['unsubscribe_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-paper-plane text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Sent</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($deliverabilityStats['total_sent']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bounce Reasons -->
            @if($deliverabilityStats['bounce_reasons']->count() > 0)
                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Bounce Analysis</h3>
                        <div class="space-y-4">
                            @foreach($deliverabilityStats['bounce_reasons'] as $reason)
                                <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-red-800">{{ $reason->reason ?: 'Unknown' }}</p>
                                        <p class="text-sm text-red-600">{{ $reason->count }} occurrences</p>
                                    </div>
                                    <div class="text-red-600">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Domain Reputation -->
            @if($deliverabilityStats['domain_stats']->count() > 0)
                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Domain Performance</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domain</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bounces</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bounce Rate</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($deliverabilityStats['domain_stats'] as $domain => $stats)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $domain }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($stats['sent']) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($stats['bounces']) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $stats['bounce_rate'] }}%</td>
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
                            </table>
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
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Best Time to Send</p>
                    <p class="text-xl font-semibold text-gray-900">
                        @if($sendTimeAnalysis['best_hour'])
                            {{ $sendTimeAnalysis['best_hour']->hour }}:00
                            ({{ $sendTimeAnalysis['best_hour']['avg_' . $selectedMetric] }}% {{ str_replace('_', ' ', $selectedMetric) }})
                        @else
                            No data available
                        @endif
                    </p>
                </div>
            </div>
            <p class="text-sm text-gray-600">
                Based on historical performance, this hour typically generates the highest engagement.
            </p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Best Day to Send</p>
                    <p class="text-xl font-semibold text-gray-900">
                        @if($sendTimeAnalysis['best_day'])
                            @php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                $dayName = $days[$sendTimeAnalysis['best_day']->day_of_week - 1] ?? 'Unknown';
                            @endphp
                            {{ $dayName }}
                            ({{ $sendTimeAnalysis['best_day']['avg_' . $selectedMetric] }}% {{ str_replace('_', ' ', $selectedMetric) }})
                        @else
                            No data available
                        @endif
                    </p>
                </div>
            </div>
            <p class="text-sm text-gray-600">
                This day of the week consistently performs best for engagement metrics.
            </p>
        </div>
    </div>

    <!-- Metric Selector -->
    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-gray-700">Analyze by:</span>
            <div class="flex space-x-2">
                <button 
                    wire:click="$set('selectedMetric', 'open_rate')"
                    class="{{ $selectedMetric === 'open_rate' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }} px-3 py-1 rounded text-sm transition-colors"
                >
                    Open Rate
                </button>
                <button 
                    wire:click="$set('selectedMetric', 'click_rate')"
                    class="{{ $selectedMetric === 'click_rate' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }} px-3 py-1 rounded text-sm transition-colors"
                >
                    Click Rate
                </button>
            </div>
        </div>
    </div>

    <!-- Hourly Performance Chart -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Hourly Performance</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Chart will go here -->
                <div class="bg-gray-50 rounded-lg p-4 h-64 flex items-center justify-center">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-chart-bar text-3xl mb-2"></i>
                        <p>Hourly performance chart</p>
                        <p class="text-sm">(Chart implementation would go here)</p>
                    </div>
                </div>
                
                <!-- Data Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hour</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Open Rate</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Click Rate</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campaigns</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($sendTimeAnalysis['hourly_stats'] as $stat)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $stat->hour }}:00</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ round($stat->avg_open_rate, 1) }}%</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ round($stat->avg_click_rate, 1) }}%</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $stat->campaign_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Performance Chart -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Performance</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Chart will go here -->
                <div class="bg-gray-50 rounded-lg p-4 h-64 flex items-center justify-center">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-chart-bar text-3xl mb-2"></i>
                        <p>Daily performance chart</p>
                        <p class="text-sm">(Chart implementation would go here)</p>
                    </div>
                </div>
                
                <!-- Data Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Open Rate</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Click Rate</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campaigns</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($sendTimeAnalysis['daily_stats'] as $stat)
                                @php
                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $dayName = $days[$stat->day_of_week - 1] ?? 'Unknown';
                                @endphp
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $dayName }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ round($stat->avg_open_rate, 1) }}%</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ round($stat->avg_click_rate, 1) }}%</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $stat->campaign_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- List Health Analysis -->
@if($activeSection === 'list-health')
<div class="space-y-6">
    <!-- List Health Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Subscribers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($listHealth['total_subscribers']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Subscribers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($listHealth['active_subscribers']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Engagement Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $listHealth['engaged_rate'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-slash text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Inactive Subscribers</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($listHealth['inactive_count']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriber Segments -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Subscriber Segments</h3>
                <button 
                    wire:click="exportInactiveSubscribers"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm transition-colors"
                >
                    <i class="fas fa-download mr-1"></i> Export Inactive
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-800">Highly Engaged</p>
                            <p class="text-2xl font-semibold text-green-600">{{ number_format($listHealth['segments']['highly_engaged']) }}</p>
                        </div>
                        <i class="fas fa-fire text-green-500 text-xl"></i>
                    </div>
                    <p class="text-xs text-green-600 mt-2">Opened an email in the last 7 days</p>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Moderately Engaged</p>
                            <p class="text-2xl font-semibold text-blue-600">{{ number_format($listHealth['segments']['moderately_engaged']) }}</p>
                        </div>
                        <i class="fas fa-user-clock text-blue-500 text-xl"></i>
                    </div>
                    <p class="text-xs text-blue-600 mt-2">Opened an email in the last 30 days</p>
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Low Engaged</p>
                            <p class="text-2xl font-semibold text-yellow-600">{{ number_format($listHealth['segments']['low_engaged']) }}</p>
                        </div>
                        <i class="fas fa-bed text-yellow-500 text-xl"></i>
                    </div>
                    <p class="text-xs text-yellow-600 mt-2">Opened an email in the last 90 days</p>
                </div>

                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-800">At Risk</p>
                            <p class="text-2xl font-semibold text-red-600">{{ number_format($listHealth['segments']['at_risk']) }}</p>
                        </div>
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    </div>
                    <p class="text-xs text-red-600 mt-2">No opens in the last 90 days</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Trends -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Growth Trends</h3>
            <div class="bg-gray-50 rounded-lg p-4 h-64 flex items-center justify-center">
                <div class="text-center text-gray-500">
                    <i class="fas fa-chart-line text-3xl mb-2"></i>
                    <p>Subscriber growth chart</p>
                    <p class="text-sm">(Chart implementation would go here)</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- A/B Testing Recommendations -->
@if($activeSection === 'ab-testing')
<div class="space-y-6">
    <!-- A/B Testing Overview -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">A/B Test Recommendations</h3>
            
            <div class="space-y-4">
                @foreach($abTesting['suggested_tests'] as $test)
                    <div class="p-4 border rounded-lg
                        {{ $test['priority'] === 'High' ? 'border-red-200 bg-red-50' : 'border-blue-200 bg-blue-50' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $test['priority'] === 'High' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $test['priority'] }} Priority
                                    </span>
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $test['type'] }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $test['recommendation'] }}</p>
                                <p class="text-xs text-gray-500">{{ $test['test_idea'] }}</p>
                            </div>
                            <i class="fas fa-flask text-gray-400 text-xl"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Subject Line Performance -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Subject Line Performance</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject Line</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Open Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($abTesting['subject_variations'] as $subject)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $subject->subject }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ round($subject->avg_open_rate, 1) }}%</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $subject->usage_count }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $subject->avg_open_rate >= 25 ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $subject->avg_open_rate >= 15 && $subject->avg_open_rate < 25 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $subject->avg_open_rate < 15 ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ $subject->avg_open_rate >= 25 ? 'Excellent' : ($subject->avg_open_rate >= 15 ? 'Good' : 'Needs Improvement') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Send Time Variations -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Send Time Performance</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Send Hour</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Open Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Click Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaigns</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($abTesting['send_time_variations'] as $time)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $time->send_hour }}:00</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ round($time->avg_open_rate, 1) }}%</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ round($time->avg_click_rate, 1) }}%</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $time->campaign_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif