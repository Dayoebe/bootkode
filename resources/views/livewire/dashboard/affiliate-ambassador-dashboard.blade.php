<div class="min-h-screen bg-themed-primary transition-colors duration-300 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 relative overflow-hidden transition-colors duration-300">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-full -translate-y-12 translate-x-12 opacity-60"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-handshake text-green-600 dark:text-green-400 mr-3"></i>
                        Affiliate Dashboard
                    </h1>
                    <p class="text-themed-secondary mt-1 transition-colors duration-300">Welcome {{ auth()->user()->name }}! Track your referrals and maximize your earnings.</p>
                    
                    <div class="flex items-center space-x-4 mt-3">
                        <div class="flex items-center bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-sm transition-colors duration-300">
                            <i class="fas fa-trophy mr-2"></i>
                            {{ $this->overviewStats['tier_status'] }} Tier
                        </div>
                        <div class="flex items-center bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 px-3 py-1 rounded-full text-sm transition-colors duration-300">
                            <i class="fas fa-percentage mr-2"></i>
                            {{ $this->overviewStats['commission_rate'] }}% Commission
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Metric Selector -->
                    <select wire:model.live="selectedMetric" class="bg-themed-secondary border border-themed-secondary text-themed-primary px-3 py-2 text-sm rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors duration-300">
                        <option value="earnings">Earnings</option>
                        <option value="referrals">Referrals</option>
                        <option value="conversions">Conversions</option>
                    </select>
                    
                    <!-- Timeframe Selector -->
                    <div class="flex bg-themed-tertiary rounded-lg p-1 transition-colors duration-300">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d', '12months' => '12m'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-themed-secondary text-green-600 dark:text-green-400 shadow-sm' : 'text-themed-secondary hover:text-themed-primary' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <a href="{{ route('affiliate.tools') }}" class="bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
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
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Total Earnings</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">₦{{ number_format($this->overviewStats['total_earnings'], 0) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">₦{{ number_format($this->overviewStats['monthly_earnings'], 0) }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">this month</span>
                    </div>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-dollar-sign text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Total Referrals</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['total_referrals']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-blue-600 dark:text-blue-400 font-medium transition-colors duration-300">{{ $this->overviewStats['active_referrals'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">active</span>
                    </div>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Conversion Rate</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->overviewStats['conversion_rate'] }}%</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-purple-600 dark:text-purple-400 font-medium transition-colors duration-300">{{ $this->overviewStats['clicks_this_period'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">clicks</span>
                    </div>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Avg. Referral Value</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">₦{{ number_format($this->overviewStats['avg_referral_value'], 0) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-orange-600 dark:text-orange-400 font-medium transition-colors duration-300">{{ $this->overviewStats['pending_referrals'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">pending</span>
                    </div>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-coins text-orange-600 dark:text-orange-400 text-xl"></i>
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
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Earnings Analytics</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Daily Earnings Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Daily Earnings ({{ $selectedTimeframe === '7days' ? 'Last 7 Days' : ($selectedTimeframe === '30days' ? 'Last 30 Days' : ($selectedTimeframe === '90days' ? 'Last 90 Days' : 'Last 12 Months')) }})</h3>
                        <div class="h-48">
                            <canvas id="earningsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Top Earning Courses -->
                    <div>
                        <h3 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Top Earning Courses</h3>
                        <div class="space-y-3">
                            @forelse($this->earningsAnalytics['top_earning_courses'] ?? [] as $course)
                            <div class="flex items-center justify-between p-3 bg-themed-tertiary rounded-lg transition-colors duration-300">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $course['course_title'] }}</h4>
                                    <p class="text-xs text-themed-secondary transition-colors duration-300">{{ $course['sales_count'] }} sales</p>
                                </div>
                                <span class="text-sm font-bold text-green-600 dark:text-green-400 transition-colors duration-300">₦{{ number_format($course['total_commission'], 0) }}</span>
                            </div>
                            @empty
                            <p class="text-themed-tertiary text-sm transition-colors duration-300">No earnings from courses yet</p>
                            @endforelse
                        </div>
                        
                        <!-- Tier Progress -->
                        @if($this->overviewStats['next_tier_requirement'] > 0)
                        <div class="mt-6 pt-4 border-t border-themed-primary transition-colors duration-300">
                            <h4 class="text-sm font-semibold text-themed-primary mb-2 transition-colors duration-300">Next Tier Progress</h4>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-themed-secondary transition-colors duration-300">{{ $this->overviewStats['tier_status'] }} → Next Level</span>
                                <span class="text-themed-secondary transition-colors duration-300">₦{{ number_format($this->overviewStats['next_tier_requirement'], 0) }} to go</span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-2 transition-colors duration-300">
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
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Referral Performance</h2>
                    <a href="{{ route('affiliate.analytics') }}" class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-sm font-medium transition-colors duration-300">
                        View Details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse(collect($this->referralPerformance)->take(8) as $referral)
                    <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg hover:bg-themed-secondary transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-full flex items-center justify-center transition-colors duration-300">
                                <span class="font-bold text-green-600 dark:text-green-400 text-sm">{{ substr($referral['user_name'], 0, 2) }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-themed-primary transition-colors duration-300">{{ $referral['user_name'] }}</h4>
                                <div class="flex items-center space-x-3 text-sm text-themed-secondary mt-1 transition-colors duration-300">
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
                            <div class="text-lg font-bold text-green-600 dark:text-green-400 transition-colors duration-300">₦{{ number_format($referral['commission_earned'], 0) }}</div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $referral['status'] === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }} transition-colors duration-300">
                                {{ ucfirst($referral['status']) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-themed-tertiary text-4xl mb-4 transition-colors duration-300"></i>
                        <p class="text-themed-secondary mb-4 transition-colors duration-300">No referrals yet</p>
                        <button wire:click="copyToClipboard('{{ auth()->user()->affiliate?->referral_link ?? '' }}')" 
                                class="bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
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
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Marketing Tools</h2>
                
                <div class="space-y-4">
                    <!-- Referral Link -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Your Referral Link</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   value="{{ $this->marketingTools['referral_link'] ?? '' }}" 
                                   readonly 
                                   class="flex-1 bg-themed-tertiary border border-themed-secondary rounded-lg px-3 py-2 text-sm text-themed-primary transition-colors duration-300">
                            <button wire:click="copyToClipboard('{{ $this->marketingTools['referral_link'] ?? '' }}')" 
                                    class="bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Referral Code -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Referral Code</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   value="{{ $this->marketingTools['referral_code'] ?? '' }}" 
                                   readonly 
                                   class="flex-1 bg-themed-tertiary border border-themed-secondary rounded-lg px-3 py-2 text-sm font-mono text-themed-primary transition-colors duration-300">
                            <button wire:click="copyToClipboard('{{ $this->marketingTools['referral_code'] ?? '' }}')" 
                                    class="bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="text-center">
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">QR Code</label>
                        @if(isset($this->marketingTools['qr_code_url']))
                        <img src="{{ $this->marketingTools['qr_code_url'] }}" alt="QR Code" class="mx-auto mb-2 border border-themed-primary dark:border-themed-secondary rounded-lg transition-colors duration-300">
                        @endif
                        <button class="text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 transition-colors duration-300">Download QR Code</button>
                    </div>

                    <!-- Social Media Templates -->
                    <div>
                        <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Social Media Templates</h4>
                        <div class="space-y-2">
                            @foreach($this->marketingTools['social_media_posts'] ?? [] as $platform => $template)
                            <div class="flex items-center justify-between p-2 border border-themed-primary rounded transition-colors duration-300">
                                <span class="text-sm text-themed-secondary capitalize transition-colors duration-300">{{ $platform }}</span>
                                <button wire:click="copyToClipboard('{{ $template }}')" 
                                        class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-sm transition-colors duration-300">
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
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Top Referrals</h2>
                
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($this->topReferrals as $index => $referral)
                    <div class="flex items-center space-x-3 p-3 border border-themed-primary rounded-lg transition-colors duration-300">
                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-yellow-100 to-orange-100 dark:from-yellow-900/30 dark:to-orange-900/30 rounded-full flex items-center justify-center transition-colors duration-300">
                            <span class="font-bold text-yellow-600 dark:text-yellow-400 text-sm">#{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $referral['user_name'] }}</h4>
                            <div class="flex items-center space-x-2 text-xs text-themed-tertiary mt-1 transition-colors duration-300">
                                <span>{{ $referral['courses_purchased'] }} courses</span>
                                <span>•</span>
                                <span>{{ $referral['join_date']->format('M Y') }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-green-600 dark:text-green-400 transition-colors duration-300">₦{{ number_format($referral['commission_earned'], 0) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-medal text-themed-tertiary text-3xl mb-2 transition-colors duration-300"></i>
                        <p class="text-themed-secondary transition-colors duration-300">No active referrals yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Recent Transactions -->
            @if($showWidgets['recent_transactions'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Recent Transactions</h2>
                    <a href="{{ route('affiliate.commissions') }}" class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($this->recentTransactions as $transaction)
                    <div class="flex items-center justify-between p-3 border border-themed-primary rounded-lg transition-colors duration-300">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-themed-primary transition-colors duration-300">{{ $transaction['user_name'] }}</h4>
                            <p class="text-xs text-themed-secondary truncate transition-colors duration-300">{{ $transaction['course_title'] ?? 'Direct Referral' }}</p>
                            <span class="text-xs text-themed-tertiary transition-colors duration-300">{{ $transaction['created_at']->format('M j, Y') }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-green-600 dark:text-green-400 transition-colors duration-300">₦{{ number_format($transaction['commission'], 0) }}</div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $transaction['status'] === 'paid' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }} transition-colors duration-300">
                                {{ ucfirst($transaction['status']) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-receipt text-themed-tertiary text-3xl mb-2 transition-colors duration-300"></i>
                        <p class="text-themed-secondary transition-colors duration-300">No transactions yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Leaderboard -->
            @if($showWidgets['leaderboard'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Affiliate Leaderboard</h2>
                
                <div class="space-y-3">
                    @foreach($this->leaderboard as $affiliate)
                    <div class="flex items-center space-x-3 p-3 rounded-lg {{ $affiliate['is_current_user'] ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-themed-tertiary' }} transition-colors duration-300">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $affiliate['rank'] <= 3 ? 'bg-gradient-to-br from-yellow-100 to-orange-100 dark:from-yellow-900/30 dark:to-orange-900/30' : 'bg-themed-secondary' }} transition-colors duration-300">
                            <span class="font-bold text-sm {{ $affiliate['rank'] <= 3 ? 'text-yellow-600 dark:text-yellow-400' : 'text-themed-secondary' }}">
                                {{ $affiliate['rank'] }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium {{ $affiliate['is_current_user'] ? 'text-green-900 dark:text-green-300' : 'text-themed-primary' }} truncate transition-colors duration-300">
                                {{ $affiliate['is_current_user'] ? 'You' : $affiliate['name'] }}
                            </h4>
                            <div class="text-xs {{ $affiliate['is_current_user'] ? 'text-green-600 dark:text-green-400' : 'text-themed-tertiary' }} transition-colors duration-300">
                                {{ $affiliate['total_referrals'] }} referrals
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold {{ $affiliate['is_current_user'] ? 'text-green-600 dark:text-green-400' : 'text-themed-primary' }} transition-colors duration-300">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current theme for chart colors
    const isDark = document.documentElement.classList.contains('dark');
    
    // Chart.js default configuration for dark mode
    Chart.defaults.color = isDark ? '#D1D5DB' : '#374151';
    Chart.defaults.borderColor = isDark ? '#374151' : '#E5E7EB';
    Chart.defaults.backgroundColor = isDark ? '#1F2937' : '#FFFFFF';

    // Earnings Chart
    const earningsData = @json($this->earningsAnalytics['daily_earnings'] ?? []);
    if (window.bootkodeDashboardCharts?.shouldRender('earningsChart', earningsData, ['earnings', 'referrals'])) {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: earningsData.map(item => item.date),
                datasets: [{
                    label: 'Daily Earnings (₦)',
                    data: earningsData.map(item => item.earnings),
                    backgroundColor: isDark ? 'rgba(52, 211, 153, 0.8)' : 'rgba(34, 197, 94, 0.8)',
                    borderColor: isDark ? 'rgb(52, 211, 153)' : 'rgb(34, 197, 94)',
                    borderWidth: 1,
                    borderRadius: 4,
                }, {
                    label: 'Daily Referrals',
                    data: earningsData.map(item => item.referrals),
                    type: 'line',
                    borderColor: isDark ? 'rgb(34, 197, 94)' : 'rgb(16, 185, 129)',
                    backgroundColor: isDark ? 'rgba(34, 197, 94, 0.1)' : 'rgba(16, 185, 129, 0.1)',
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
                        labels: {
                            color: isDark ? '#D1D5DB' : '#374151'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280',
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: isDark ? 'rgba(55, 65, 81, 0.3)' : 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                    x: {
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Listen for dark mode changes and update charts
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                // Reload charts when dark mode changes
                setTimeout(() => {
                    location.reload();
                }, 100);
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
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
