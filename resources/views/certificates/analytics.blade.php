<?php
// resources/views/admin/certificates/analytics.blade.php
?>
<x-app-layout>
<div class="min-h-screen bg-gray-900 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Certificate Analytics</h1>
                <p class="text-gray-300 mt-2">Comprehensive analytics and insights for certificate management</p>
            </div>
            
            <div class="flex gap-4">
                <select class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white">
                    <option>Last 30 Days</option>
                    <option>Last 3 Months</option>
                    <option>Last 6 Months</option>
                    <option>Last Year</option>
                    <option>All Time</option>
                </select>
                <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
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
                        <p class="text-3xl font-bold">{{ \App\Models\Certificate::count() }}</p>
                        <p class="text-xs opacity-75">+12% from last month</p>
                    </div>
                    <i class="fas fa-certificate text-4xl opacity-80"></i>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Approved</p>
                        <p class="text-3xl font-bold">{{ \App\Models\Certificate::approved()->count() }}</p>
                        <p class="text-xs opacity-75">{{ round((\App\Models\Certificate::approved()->count() / max(\App\Models\Certificate::count(), 1)) * 100) }}% approval rate</p>
                    </div>
                    <i class="fas fa-check-circle text-4xl opacity-80"></i>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Pending</p>
                        <p class="text-3xl font-bold">{{ \App\Models\Certificate::requested()->count() }}</p>
                        <p class="text-xs opacity-75">Awaiting review</p>
                    </div>
                    <i class="fas fa-clock text-4xl opacity-80"></i>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">This Month</p>
                        <p class="text-3xl font-bold">{{ \App\Models\Certificate::whereMonth('created_at', now()->month)->count() }}</p>
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
                <div class="h-64 bg-gray-700 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400">Chart placeholder - integrate with your preferred charting library</p>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4">Status Distribution</h3>
                <div class="h-64 bg-gray-700 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400">Pie chart placeholder - showing status breakdown</p>
                </div>
            </div>
        </div>

        <!-- Top Courses -->
        <div class="grid lg:grid-cols-2 gap-6">
            <!-- Most Certificated Courses -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4">Top Certificated Courses</h3>
                <div class="space-y-4">
                    @php
                        $topCourses = \App\Models\Certificate::selectRaw('course_id, count(*) as certificate_count')
                            ->with('course')
                            ->groupBy('course_id')
                            ->orderByDesc('certificate_count')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @forelse($topCourses as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-white font-medium">{{ $item->course->title ?? 'Unknown Course' }}</h4>
                            <p class="text-gray-400 text-sm">{{ $item->course->instructor->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-indigo-400">{{ $item->certificate_count }}</div>
                            <div class="text-xs text-gray-400">certificates</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-center py-8">No certificate data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    @php
                        $recentCertificates = \App\Models\Certificate::with(['user', 'course'])
                            ->latest()
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @forelse($recentCertificates as $certificate)
                    <div class="flex items-center p-3 bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0 w-2 h-2 rounded-full mr-3
                            @if($certificate->status === 'approved') bg-green-400
                            @elseif($certificate->status === 'requested') bg-yellow-400
                            @elseif($certificate->status === 'rejected') bg-red-400
                            @else bg-gray-400 @endif">
                        </div>
                        <div class="flex-1">
                            <p class="text-white text-sm">
                                <strong>{{ $certificate->user->name }}</strong> 
                                {{ $certificate->status === 'approved' ? 'earned' : 'requested' }} 
                                certificate for <strong>{{ Str::limit($certificate->course->title, 30) }}</strong>
                            </p>
                            <p class="text-gray-400 text-xs">{{ $certificate->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-center py-8">No recent activity</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>