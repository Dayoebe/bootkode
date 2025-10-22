<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" wire:poll.5000ms>
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-server mr-2"></i> System Status
        </h1>
        <p class="text-indigo-100 mt-2">Check the operational status of our services</p>
    </div>

    <!-- Services Status Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($services as $service)
            <div class="bg-themed-secondary shadow rounded-xl p-6 animate__animated animate__fadeInUp border border-themed-primary hover:shadow-lg transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">{{ $service['name'] }}</h3>
                        <p class="text-sm text-themed-secondary capitalize mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($service['status'] === 'operational') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @elseif($service['status'] === 'degraded') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                @elseif($service['status'] === 'down') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                @else bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 @endif">
                                {{ ucfirst($service['status']) }}
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        @if($service['status'] === 'operational')
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-2xl"></i>
                            </div>
                        @elseif($service['status'] === 'degraded')
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                                <i class="fas fa-exclamation-circle text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                            </div>
                        @elseif($service['status'] === 'down')
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                                <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-2xl"></i>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <i class="fas fa-wrench text-blue-600 dark:text-blue-400 text-2xl"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Incident History -->
    <div class="bg-themed-secondary shadow rounded-xl border border-themed-primary overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-themed-primary">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-history text-indigo-600 dark:text-indigo-400 mr-2"></i>Incident History
            </h2>
            <p class="text-themed-secondary mt-1">Recent incidents and system events</p>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b border-themed-primary bg-themed-tertiary/50">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label for="incident_search" class="block text-sm font-medium text-themed-primary mb-2">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-themed-secondary"></i>
                        <input wire:model.live.debounce.300ms="search" type="text" id="incident_search"
                               placeholder="Search incidents..."
                               class="w-full pl-10 pr-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="flex-1">
                    <label for="incident_status" class="block text-sm font-medium text-themed-primary mb-2">Status</label>
                    <select wire:model.live="statusFilter" id="incident_status"
                            class="w-full px-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary">
                        <option value="all">All Incidents</option>
                        <option value="active">Active</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Incidents List -->
        <div class="space-y-0">
            @forelse($incidents as $incident)
                <div class="p-6 border-b border-themed-primary hover:bg-themed-tertiary transition-colors duration-200 last:border-b-0 animate__animated animate__fadeInUp">
                    <div class="flex justify-between items-start">
                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <h3 class="text-lg font-semibold text-themed-primary">{{ $incident->title }}</h3>
                                
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($incident->status === 'active') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                    @else bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 @endif">
                                    {{ ucfirst($incident->status) }}
                                </span>

                                <!-- Severity Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($incident->severity === 'high') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300
                                    @elseif($incident->severity === 'medium') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300
                                    @else bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 @endif">
                                    {{ ucfirst($incident->severity) }} Severity
                                </span>
                            </div>

                            <p class="text-sm text-themed-secondary mb-3">{{ Str::limit($incident->description, 200) }}</p>

                            <!-- Timeline -->
                            <div class="flex items-center flex-wrap gap-3 text-xs text-themed-secondary">
                                <span><i class="fas fa-server mr-1"></i>{{ ucfirst($incident->service) }}</span>
                                <span>•</span>
                                <span><i class="fas fa-calendar mr-1"></i>Started: {{ $incident->started_at->format('M d, Y H:i') }}</span>
                                @if($incident->resolved_at)
                                    <span>•</span>
                                    <span><i class="fas fa-check mr-1"></i>Resolved: {{ $incident->resolved_at->format('M d, Y H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Status Icon -->
                        <div class="flex-shrink-0 ml-4">
                            @if($incident->status === 'active')
                                <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/30">
                                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                                </div>
                            @else
                                <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="bg-themed-tertiary rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <p class="text-themed-primary text-lg font-medium">All Systems Operational</p>
                    <p class="text-themed-secondary mt-1">No active incidents at this time</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($incidents->hasPages())
            <div class="p-4 border-t border-themed-primary flex justify-center">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>

    <!-- System Health Info -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Status Summary -->
        <div class="bg-themed-secondary rounded-xl p-6 border border-themed-primary">
            <h3 class="text-lg font-semibold text-themed-primary mb-4 flex items-center">
                <i class="fas fa-heartbeat text-red-600 dark:text-red-400 mr-2"></i>System Health
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Uptime</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">99.9%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Last Check</span>
                    <span class="font-semibold text-themed-primary">Just now</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Active Incidents</span>
                    <span class="font-semibold text-themed-primary">{{ $incidents->where('status', 'active')->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Last Updated -->
        <div class="bg-themed-secondary rounded-xl p-6 border border-themed-primary">
            <h3 class="text-lg font-semibold text-themed-primary mb-4 flex items-center">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-2"></i>Information
            </h3>
            <div class="space-y-3 text-sm text-themed-secondary">
                <p><strong class="text-themed-primary">Page Updates:</strong> Every 5 seconds</p>
                <p><strong class="text-themed-primary">Services Monitored:</strong> {{ count($services) }}</p>
                <p><strong class="text-themed-primary">Status Page:</strong> Public</p>
            </div>
        </div>
    </div>
</div>