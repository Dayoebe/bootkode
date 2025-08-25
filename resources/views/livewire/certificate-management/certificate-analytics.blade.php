<div class="min-h-screen bg-gray-900 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Certificate Analytics</h1>
            <p class="text-gray-300 mt-2">Comprehensive analytics and insights for certificate management</p>
        </div>

        <div class="flex gap-4">
            <select wire:model.live="dateRange"
                class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="90">Last 3 Months</option>
                <option value="180">Last 6 Months</option>
                <option value="365">Last Year</option>
                <option value="all">All Time</option>
            </select>

            @if (!auth()->user()->hasRole('instructor') || auth()->user()->hasRole('super_admin'))
                <select wire:model.live="selectedCourse"
                    class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="all">All Courses</option>
                    @foreach ($availableCourses as $course)
                        <option value="{{ $course->id }}">{{ Str::limit($course->title, 30) }}</option>
                    @endforeach
                </select>
            @endif

            <button wire:click="exportReport"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center">
                <i class="fas fa-download mr-2"></i>Export Report
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Certificates</p>
                    <p class="text-3xl font-bold">{{ number_format($totalCertificates) }}</p>
                    <p class="text-xs opacity-75">{{ $dateRange === 'all' ? 'All time' : 'Selected period' }}</p>
                </div>
                <i class="fas fa-certificate text-4xl opacity-80"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Approved</p>
                    <p class="text-3xl font-bold">{{ number_format($approvedCertificates) }}</p>
                    <p class="text-xs opacity-75">{{ $approvalRate }}% approval rate</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-80"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Pending</p>
                    <p class="text-3xl font-bold">{{ number_format($pendingCertificates) }}</p>
                    <p class="text-xs opacity-75">Awaiting review</p>
                </div>
                <i class="fas fa-clock text-4xl opacity-80"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">This Month</p>
                    <p class="text-3xl font-bold">{{ number_format($thisMonthCertificates) }}</p>
                    <p class="text-xs opacity-75">New certificates</p>
                </div>
                <i class="fas fa-calendar text-4xl opacity-80"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        <!-- Certificates Over Time -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Certificates Over Time</h3>
            <div class="h-64">
                @if (count($monthlyStats) > 0)
                    <div class="relative h-full">
                        <!-- Simple line chart representation -->
                        <div class="flex items-end justify-between h-full">
                            @foreach (array_slice($monthlyStats, -6) as $index => $stat)
                                <div class="flex flex-col items-center flex-1 mx-1">
                                    <div class="w-full bg-gray-700 rounded-t"
                                        style="height: {{ $stat['total'] > 0 ? max(($stat['total'] / max(array_column($monthlyStats, 'total'))) * 100, 10) : 10 }}%">
                                        <div class="bg-indigo-600 rounded-t h-full flex items-end justify-center">
                                            <span
                                                class="text-white text-xs font-semibold mb-1">{{ $stat['total'] }}</span>
                                        </div>
                                    </div>
                                    <div class="text-gray-400 text-xs mt-2 text-center">
                                        {{ Str::limit($stat['month'], 6) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="h-full bg-gray-700 rounded-lg flex items-center justify-center">
                        <p class="text-gray-400">No data available for selected period</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Status Distribution</h3>
            <div class="h-64 flex items-center justify-center">
                @if ($totalCertificates > 0)
                    <div class="w-48 h-48 relative">
                        <!-- Simple donut chart representation -->
                        <div class="w-full h-full rounded-full border-8 border-green-500"
                            style="background: conic-gradient(
                                    #10B981 0deg {{ ($approvedCertificates / $totalCertificates) * 360 }}deg,
                                    #F59E0B {{ ($approvedCertificates / $totalCertificates) * 360 }}deg {{ (($approvedCertificates + $pendingCertificates) / $totalCertificates) * 360 }}deg,
                                    #EF4444 {{ (($approvedCertificates + $pendingCertificates) / $totalCertificates) * 360 }}deg 360deg
                                 )">
                        </div>
                        <div class="absolute inset-4 bg-gray-800 rounded-full flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white">{{ $totalCertificates }}</div>
                                <div class="text-xs text-gray-400">Total</div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400">No certificates found</p>
                @endif
            </div>

            <!-- Legend -->
            @if ($totalCertificates > 0)
                <div class="flex justify-center space-x-6 mt-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-300 text-sm">Approved ({{ $approvedCertificates }})</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span class="text-gray-300 text-sm">Pending ({{ $pendingCertificates }})</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-gray-300 text-sm">Others
                            ({{ $totalCertificates - $approvedCertificates - $pendingCertificates }})</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Top Courses and Recent Activity -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Most Certificated Courses -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Top Certificated Courses</h3>
            <div class="space-y-4">
                @forelse($topCourses as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-white font-medium">
                                {{ Str::limit($item['course']['title'] ?? 'Unknown Course', 35) }}</h4>
                            <p class="text-gray-400 text-sm">{{ $item['instructor_name'] }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-indigo-400">{{ $item['certificate_count'] }}</div>
                            <div class="text-xs text-gray-400">certificates</div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-400 text-center py-8">
                        <i class="fas fa-chart-bar text-4xl mb-4"></i>
                        <p>No certificate data available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Recent Activity</h3>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($recentActivity as $activity)
                    <div class="flex items-center p-3 bg-gray-700 rounded-lg">
                        <div
                            class="flex-shrink-0 w-2 h-2 rounded-full mr-3
                            @if ($activity['status'] === 'approved') bg-green-400
                            @elseif($activity['status'] === 'requested') bg-yellow-400
                            @elseif($activity['status'] === 'rejected') bg-red-400
                            @else bg-gray-400 @endif">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm">
                                <strong>{{ $activity['user_name'] }}</strong>
                                {{ $activity['action_text'] }}
                                <strong>{{ Str::limit($activity['course_title'], 25) }}</strong>
                            </p>
                            <p class="text-gray-400 text-xs">{{ $activity['created_at']->diffForHumans() }}</p>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                            bg-{{ $activity['status_color'] }}-900 text-{{ $activity['status_color'] }}-300">
                            {{ ucfirst($activity['status']) }}
                        </span>
                    </div>
                @empty
                    <div class="text-gray-400 text-center py-8">
                        <i class="fas fa-history text-4xl mb-4"></i>
                        <p>No recent activity</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Statistics Table (if needed) -->
    @if (count($monthlyStats) > 0 &&
            auth()->user()->hasRole(['super_admin', 'academy_admin']))
        <div class="mt-8 bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Monthly Breakdown</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left text-gray-300 py-2">Month</th>
                            <th class="text-right text-gray-300 py-2">Total</th>
                            <th class="text-right text-gray-300 py-2">Approved</th>
                            <th class="text-right text-gray-300 py-2">Pending</th>
                            <th class="text-right text-gray-300 py-2">Rejected</th>
                            <th class="text-right text-gray-300 py-2">Approval Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monthlyStats as $stat)
                            <tr class="border-b border-gray-700/50">
                                <td class="text-white py-2">{{ $stat['month'] }}</td>
                                <td class="text-right text-white py-2">{{ $stat['total'] }}</td>
                                <td class="text-right text-green-400 py-2">{{ $stat['approved'] }}</td>
                                <td class="text-right text-yellow-400 py-2">{{ $stat['pending'] }}</td>
                                <td class="text-right text-red-400 py-2">{{ $stat['rejected'] }}</td>
                                <td class="text-right text-gray-300 py-2">
                                    {{ $stat['total'] > 0 ? round(($stat['approved'] / $stat['total']) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-8 flex items-center">
            <i class="fas fa-spinner fa-spin text-indigo-400 text-2xl mr-4"></i>
            <span class="text-white font-medium">Loading analytics...</span>
        </div>
    </div>
</div>
