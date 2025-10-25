{{-- resources/views/livewire/affiliate/dashboard.blade.php --}}
<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Affiliate Dashboard</h1>
                <p class="text-gray-600">Track your referrals and commission earnings</p>
            </div>
            <div class="flex items-center space-x-4">
                <select wire:model.live="selectedPeriod" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Earnings -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Earnings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $affiliateStats['formatted_total_earned'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-coins text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Referrals -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Referrals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $affiliateStats['total_referrals'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Referrals -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Referrals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $affiliateStats['active_referrals'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-user-check text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Commissions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($affiliateStats['pending_commissions'], 2) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Link Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Referral Link</h3>
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <input type="text" value="{{ $affiliateStats['referral_link'] }}" readonly 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm">
            </div>
            <button onclick="copyToClipboard('{{ $affiliateStats['referral_link'] }}')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-copy mr-2"></i> Copy
            </button>
            <a href="{{ route('affiliate.tools') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-bullhorn mr-2"></i> Marketing Tools
            </a>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Commission Trends -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Commission Trends</h3>
            <div class="h-64">
                @if($analytics['daily_commissions']->isNotEmpty())
                    <canvas id="commissionChart"></canvas>
                @else
                    <div class="flex items-center justify-center h-full text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-chart-line text-4xl mb-4"></i>
                            <p>No commission data available for this period</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Performing Courses -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Courses</h3>
            @if($analytics['top_courses']->isNotEmpty())
                <div class="space-y-4">
                    @foreach($analytics['top_courses']->take(5) as $course)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ Str::limit($course->course->title, 40) }}</p>
                                <p class="text-xs text-gray-500">{{ $course->sales_count }} sales</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-green-600">₦{{ number_format($course->total_commission, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                    <p>No course sales data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Commissions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Commissions</h3>
                <a href="{{ route('affiliate.commissions') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            @if($recentCommissions->isNotEmpty())
                <div class="space-y-3">
                    @foreach($recentCommissions->take(5) as $commission)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $commission->description }}</p>
                                <p class="text-xs text-gray-500">{{ $commission->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-green-600">{{ $commission->formatted_amount }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-8">No commission transactions yet</p>
            @endif
        </div>

        <!-- Recent Referral Activity -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Referral Activity</h3>
                <a href="{{ route('affiliate.analytics') }}" class="text-sm text-blue-600 hover:text-blue-800">View Analytics</a>
            </div>
            @if($referredActivity->isNotEmpty())
                <div class="space-y-3">
                    @foreach($referredActivity->take(5) as $referral)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $referral->referredUser->name }}</p>
                                <span class="px-2 py-1 text-xs bg-{{ $referral->status === 'active' ? 'green' : 'yellow' }}-100 text-{{ $referral->status === 'active' ? 'green' : 'yellow' }}-800 rounded-full">
                                    {{ ucfirst($referral->status) }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <p class="text-xs text-gray-500">
                                    Spent: {{ $referral->formatted_total_spent }} | 
                                    Commission: {{ $referral->formatted_commission_earned }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-8">No referral activity yet</p>
            @endif
        </div>
    </div>

    <!-- Monthly Performance (if available) -->
    @if(count($monthlyPerformance) > 0)
        <div class="bg-white rounded-xl shadow-lg p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Performance</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($monthlyPerformance as $month)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $month['month_name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['formatted_commission'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['total_sales'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['unique_courses'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript for Charts and Copy Function -->
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Referral link copied to clipboard',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
    }

    @if($analytics['daily_commissions']->isNotEmpty())
        // Initialize Chart.js for commission trends
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('commissionChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['daily_commissions']->pluck('date')->map(function($date) { 
                        return \Carbon\Carbon::parse($date)->format('M d'); 
                    })) !!},
                    datasets: [{
                        label: 'Commission (₦)',
                        data: {!! json_encode($analytics['daily_commissions']->pluck('total')) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
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
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₦' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    @endif
</script>
