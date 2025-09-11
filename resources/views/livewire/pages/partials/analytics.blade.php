{{-- Analytics Dashboard Partial --}}
<div class="space-y-6">
    <!-- Analytics Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Page Analytics</h2>
                <p class="text-gray-600 mt-1">Track performance and engagement metrics across all your pages</p>
            </div>
            
            <!-- Controls -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <select wire:model="analyticsRange" wire:change="updateAnalyticsRange" 
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                
                <div class="flex space-x-2">
                    <button wire:click="loadAnalyticsData" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        <i class="fas fa-refresh mr-2"></i>Refresh
                    </button>
                    
                    <button wire:click="exportAnalytics"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Views -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-blue-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Views</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($analyticsData['total_views'] ?? 0) }}
                    </div>
                    <div class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up text-xs"></i> +12.5% from last period
                    </div>
                </div>
            </div>
        </div>

        <!-- Unique Visitors -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-green-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Unique Visitors</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($analyticsData['unique_visitors'] ?? 0) }}
                    </div>
                    <div class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up text-xs"></i> +8.3% from last period
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Session Duration -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Avg Session</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $analyticsData['avg_session_duration'] ?? '0:00' }}
                    </div>
                    <div class="text-sm text-red-600 mt-1">
                        <i class="fas fa-arrow-down text-xs"></i> -2.1% from last period
                    </div>
                </div>
            </div>
        </div>

        <!-- Bounce Rate -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-red-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Bounce Rate</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $analyticsData['bounce_rate'] ?? '0%' }}
                    </div>
                    <div class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-down text-xs"></i> -5.2% from last period
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Views Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Views Over Time</h3>
                <div class="flex space-x-2">
                    <button class="text-sm px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg">Views</button>
                    <button class="text-sm px-3 py-1 text-gray-500 hover:bg-gray-100 rounded-lg">Sessions</button>
                </div>
            </div>
            
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <i class="fas fa-chart-line text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Interactive chart will be rendered here</p>
                    <p class="text-sm text-gray-400 mt-2">Integration with Chart.js or similar library</p>
                </div>
            </div>
        </div>

        <!-- Traffic Sources -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Traffic Sources</h3>
            <div class="space-y-4">
                @foreach(($analyticsData['top_referrers'] ?? []) as $source => $percentage)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ 
                                $source === 'Google' ? 'bg-blue-500' : 
                                ($source === 'Direct' ? 'bg-gray-500' : 
                                ($source === 'Facebook' ? 'bg-blue-600' : 
                                ($source === 'Twitter' ? 'bg-sky-500' : 'bg-purple-500'))) 
                            }} mr-3"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $source }}</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="h-2 rounded-full {{ 
                                    $source === 'Google' ? 'bg-blue-500' : 
                                    ($source === 'Direct' ? 'bg-gray-500' : 
                                    ($source === 'Facebook' ? 'bg-blue-600' : 
                                    ($source === 'Twitter' ? 'bg-sky-500' : 'bg-purple-500'))) 
                                }}" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600 min-w-[3rem] text-right">{{ $percentage }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Pages Table -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Top Performing Pages</h3>
                <button class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bounce Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topPages as $page)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $page['title'] }}</div>
                                    <div class="text-sm text-gray-500">/{{ $page['slug'] }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ number_format($page['views']) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ number_format($page['unique_views']) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $page['avg_time'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $page['bounce_rate'] }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ 
                                    $page['status'] === 'published' ? 'bg-green-100 text-green-800' : 
                                    ($page['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') 
                                }}">
                                    {{ ucfirst($page['status']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <button wire:click="viewPageDetails({{ $page['id'] }})"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-chart-line text-gray-300 text-4xl mb-4"></i>
                                <p>No page data available yet</p>
                                <p class="text-sm mt-1">Create some pages to see analytics</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity & Additional Metrics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                <button class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-external-link-alt"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @if($activity['user_avatar'])
                                <img class="h-8 w-8 rounded-full" src="{{ $activity['user_avatar'] }}" alt="{{ $activity['user_name'] }}">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">{{ $activity['user_name'] }}</span>
                                {{ $activity['action'] }}
                                <span class="font-medium">"{{ Str::limit($activity['page_title'], 30) }}"</span>
                            </p>
                            <div class="flex items-center mt-1 text-xs text-gray-500">
                                <span>{{ $activity['time'] }}</span>
                                @if($activity['views_since'] > 0)
                                    <span class="mx-2">•</span>
                                    <span>{{ $activity['views_since'] }} views since</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="px-2 py-1 text-xs rounded-full {{ 
                                $activity['status'] === 'published' ? 'bg-green-100 text-green-700' : 
                                ($activity['status'] === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') 
                            }}">
                                {{ ucfirst($activity['status']) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-300 text-3xl mb-3"></i>
                        <p class="text-gray-500">No recent activity</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Performance Insights</h3>
            
            <!-- Device Breakdown -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Device Breakdown</h4>
                <div class="space-y-3">
                    @foreach(($analyticsData['device_breakdown'] ?? []) as $device => $percentage)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-{{ 
                                    $device === 'Desktop' ? 'desktop' : 
                                    ($device === 'Mobile' ? 'mobile-alt' : 'tablet-alt') 
                                }} text-gray-400 w-4 mr-3"></i>
                                <span class="text-sm text-gray-900">{{ $device }}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-20 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="h-2 bg-indigo-500 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600 min-w-[2.5rem] text-right">{{ $percentage }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Page Speed -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Page Speed</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $analyticsData['page_load_speed']['avg_load_time'] ?? '2.1' }}s
                        </div>
                        <div class="text-xs text-gray-500">Avg Load Time</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600">
                            {{ $analyticsData['page_load_speed']['core_web_vitals_score'] ?? '85' }}
                        </div>
                        <div class="text-xs text-gray-500">Core Web Vitals</div>
                    </div>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mt-3">
                    <span>{{ $analyticsData['page_load_speed']['fast_pages'] ?? 0 }} fast pages</span>
                    <span>{{ $analyticsData['page_load_speed']['slow_pages'] ?? 0 }} slow pages</span>
                </div>
            </div>
        </div>
    </div>
</div>