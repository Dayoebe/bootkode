<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full -translate-y-12 translate-x-12 opacity-60"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-handshake text-green-600 mr-3"></i>
                        Affiliate Dashboard
                    </h1>
                    <p class="text-gray-600 mt-1">Welcome {{ auth()->user()->name }}! Track your referrals and maximize your earnings.</p>
                    
                    <div class="flex items-center space-x-4 mt-3">
                        <div class="flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-trophy mr-2"></i>
                            {{ $this->overviewStats['tier_status'] }} Tier
                        </div>
                        <div class="flex items-center bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-percentage mr-2"></i>
                            {{ $this->overviewStats['commission_rate'] }}% Commission
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Metric Selector -->
                    <select wire:model.live="selectedMetric" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="earnings">Earnings</option>
                        <option value="referrals">Referrals</option>
                        <option value="conversions">Conversions</option>
                    </select>
                    
                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d', '12months' => '12m'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white text-green-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <a href="{{ route('affiliate.tools') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-tools mr-2"></i>
                        Marketing Tools
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Stats Grid -->
    @if($showWidgets['overview_stats'])
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Earnings</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">₦{{ number_format($this->overviewStats['total_earnings'], 0) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">₦{{ number_format($this->overviewStats['monthly_earnings'], 0) }}</span>
                        <span class="text-gray-500 ml-1">this month</span>
                    </div>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Referrals</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($this->overviewStats['total_referrals']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-blue-600 font-medium">{{ $this->overviewStats['active_referrals'] }}</span>
                        <span class="text-gray-500 ml-1">active</span>
                    </div>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Conversion Rate</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $this->overviewStats['conversion_rate'] }}%</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-purple-600 font-medium">{{ $this->overviewStats['clicks_this_period'] }}</span>
                        <span class="text-gray-500 ml-1">clicks</span>
                    </div>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Avg. Referral Value</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">₦{{ number_format($this->overviewStats['avg_referral_value'], 0) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-orange-600 font-medium">{{ $this->overviewStats['pending_referrals'] }}</span>
                        <span class="text-gray-500 ml-1">pending</span>
                    </div>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-coins text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Earnings Analytics -->
            @if($showWidgets['earnings_analytics'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Earnings Analytics</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Daily Earnings Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Daily Earnings ({{ $selectedTimeframe === '7days' ? 'Last 7 Days' : ($selectedTimeframe === '30days' ? 'Last 30 Days' : ($selectedTimeframe === '90days' ? 'Last 90 Days' : 'Last 12 Months')) }})</h3>
                        <div class="h-48">
                            <canvas id="earningsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Top Earning Courses -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Earning Courses</h3>
                        <div class="space-y-3">
                            @forelse($this->earningsAnalytics['top_earning_courses'] ?? [] as $course)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $course['course_title'] }}</h4>
                                    <p class="text-xs text-gray-600">{{ $course['sales_count'] }} sales</p>
                                </div>
                                <span class="text-sm font-bold text-green-600">₦{{ number_format($course['total_commission'], 0) }}</span>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm">No earnings from courses yet</p>
                            @endforelse
                        </div>
                        
                        <!-- Tier Progress -->
                        @if($this->overviewStats['next_tier_requirement'] > 0)
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Next Tier Progress</h4>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span>{{ $this->overviewStats['tier_status'] }} → Next Level</span>
                                <span>₦{{ number_format($this->overviewStats['next_tier_requirement'], 0) }} to go</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-2 rounded-full" 
                                     style="width: {{ 100 - (($this->overviewStats['next_tier_requirement'] / 10000) * 100) }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Referral Performance -->
            @if($showWidgets['referral_performance'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Referral Performance</h2>
                    <a href="{{ route('affiliate.analytics') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                        View Details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($this->referralPerformance->take(8) as $referral)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center">
                                <span class="font-bold text-green-600 text-sm">{{ substr($referral['user_name'], 0, 2) }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $referral['user_name'] }}</h4>
                                <div class="flex items-center space-x-3 text-sm text-gray-600 mt-1">
                                    <span>{{ $referral['courses_purchased'] }} courses</span>
                                    <span>₦{{ number_format($referral['total_spent'], 0) }} spent</span>
                                    <span class="flex items-center">
                                        <i class="fas fa-chart-line mr-1"></i>
                                        {{ $referral['activity_score'] }}% active
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-600">₦{{ number_format($referral['commission_earned'], 0) }}</div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $referral['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($referral['status']) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600 mb-4">No referrals yet</p>
                        <button wire:click="copyToClipboard('{{ auth()->user()->affiliate?->referral_link ?? '' }}')" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Share Your Link
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Marketing Tools -->
            @if($showWidgets['marketing_tools'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Marketing Tools</h2>
                
                <div class="space-y-4">
                    <!-- Referral Link -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Referral Link</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   value="{{ $this->marketingTools['referral_link'] ?? '' }}" 
                                   readonly 
                                   class="flex-1 bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <button wire:click="copyToClipboard('{{ $this->marketingTools['referral_link'] ?? '' }}')" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Referral Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Referral Code</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   value="{{ $this->marketingTools['referral_code'] ?? '' }}" 
                                   readonly 
                                   class="flex-1 bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono">
                            <button wire:click="copyToClipboard('{{ $this->marketingTools['referral_code'] ?? '' }}')" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
                        @if(isset($this->marketingTools['qr_code_url']))
                        <img src="{{ $this->marketingTools['qr_code_url'] }}" alt="QR Code" class="mx-auto mb-2 border rounded-lg">
                        @endif
                        <button class="text-xs text-green-600 hover:text-green-700">Download QR Code</button>
                    </div>

                    <!-- Social Media Templates -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Social Media Templates</h4>
                        <div class="space-y-2">
                            @foreach($this->marketingTools['social_media_posts'] ?? [] as $platform => $template)
                            <div class="flex items-center justify-between p-2 border border-gray-200 rounded">
                                <span class="text-sm text-gray-600 capitalize">{{ $platform }}</span>
                                <button wire:click="copyToClipboard('{{ $template }}')" 
                                        class="text-green-600 hover:text-green-700 text-sm">
                                    Copy
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top Referrals -->
            @if($showWidgets['top_referrals'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Top Referrals</h2>
                
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($this->topReferrals as $index => $referral)
                    <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full flex items-center justify-center">
                            <span class="font-bold text-yellow-600 text-sm">#{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $referral['user_name'] }}</h4>
                            <div class="flex items-center space-x-2 text-xs text-gray-500 mt-1">
                                <span>{{ $referral['courses_purchased'] }} courses</span>
                                <span>•</span>
                                <span>{{ $referral['join_date']->format('M Y') }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-green-600">₦{{ number_format($referral['commission_earned'], 0) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-medal text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No active referrals yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Recent Transactions -->
            @if($showWidgets['recent_transactions'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Transactions</h2>
                    <a href="{{ route('affiliate.commissions') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($this->recentTransactions as $transaction)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $transaction['user_name'] }}</h4>
                            <p class="text-xs text-gray-600 truncate">{{ $transaction['course_title'] ?? 'Direct Referral' }}</p>
                            <span class="text-xs text-gray-500">{{ $transaction['created_at']->format('M j, Y') }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-green-600">₦{{ number_format($transaction['commission'], 0) }}</div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $transaction['status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($transaction['status']) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-receipt text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No transactions yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Leaderboard -->
            @if($showWidgets['leaderboard'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Affiliate Leaderboard</h2>
                
                <div class="space-y-3">
                    @foreach($this->leaderboard as $affiliate)
                    <div class="flex items-center space-x-3 p-3 rounded-lg {{ $affiliate['is_current_user'] ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $affiliate['rank'] <= 3 ? 'bg-gradient-to-br from-yellow-100 to-orange-100' : 'bg-gray-100' }}">
                            <span class="font-bold text-sm {{ $affiliate['rank'] <= 3 ? 'text-yellow-600' : 'text-gray-600' }}">
                                {{ $affiliate['rank'] }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium {{ $affiliate['is_current_user'] ? 'text-green-900' : 'text-gray-900' }} truncate">
                                {{ $affiliate['is_current_user'] ? 'You' : $affiliate['name'] }}
                            </h4>
                            <div class="text-xs {{ $affiliate['is_current_user'] ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $affiliate['total_referrals'] }} referrals
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold {{ $affiliate['is_current_user'] ? 'text-green-600' : 'text-gray-900' }}">
                                ₦{{ number_format($affiliate['total_earned'], 0) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Earnings Chart
    const earningsData = @json($this->earningsAnalytics['daily_earnings'] ?? []);
    if (earningsData.length > 0) {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: earningsData.map(item => item.date),
                datasets: [{
                    label: 'Daily Earnings (₦)',
                    data: earningsData.map(item => item.earnings),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1,
                    borderRadius: 4,
                }, {
                    label: 'Daily Referrals',
                    data: earningsData.map(item => item.referrals),
                    type: 'line',
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});

// Copy to clipboard functionality
window.addEventListener('copy-to-clipboard', event => {
    navigator.clipboard.writeText(event.detail.text).then(() => {
        console.log('Copied to clipboard');
    });
});

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, 300000); // 5 minutes
</script>
@endpush