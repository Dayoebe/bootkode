<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-server mr-2"></i> System Status Management
        </h1>
        <p class="text-indigo-100 mt-2">Manage system status, services, and incidents</p>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-8">
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden">
            <nav class="flex border-b border-themed-primary" aria-label="Tabs">
                <button @click="activeTab = 'services'"
                        :class="{ 
                            'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-100/10 dark:bg-indigo-900/20': activeTab === 'services', 
                            'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'services' 
                        }"
                        class="flex-1 whitespace-nowrap py-4 px-4 font-medium text-sm flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-cogs mr-2"></i> Services
                </button>
                <button @click="activeTab = 'incidents'"
                        :class="{ 
                            'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-100/10 dark:bg-indigo-900/20': activeTab === 'incidents', 
                            'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'incidents' 
                        }"
                        class="flex-1 whitespace-nowrap py-4 px-4 font-medium text-sm flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Incidents
                </button>
                <button @click="activeTab = 'create_incident'"
                        :class="{ 
                            'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-100/10 dark:bg-indigo-900/20': activeTab === 'create_incident', 
                            'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'create_incident' 
                        }"
                        class="flex-1 whitespace-nowrap py-4 px-4 font-medium text-sm flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-plus-circle mr-2"></i> Report Incident
                </button>
            </nav>
        </div>
    </div>

    <!-- Services Tab -->
    <div x-show="activeTab === 'services'" x-transition class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($services as $service)
                <div class="bg-themed-secondary shadow rounded-xl p-6 border border-themed-primary hover:shadow-lg transition-all duration-200 animate__animated animate__fadeInUp">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-themed-primary">{{ $service['name'] }}</h3>
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

                    <p class="text-sm text-themed-secondary capitalize mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($service['status'] === 'operational') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                            @elseif($service['status'] === 'degraded') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                            @elseif($service['status'] === 'down') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                            @else bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 @endif">
                            {{ ucfirst($service['status']) }}
                        </span>
                    </p>

                    @if(isset($service['uptime']))
                        <div class="pt-4 border-t border-themed-primary">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-themed-secondary">Uptime</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ $service['uptime'] }}%</span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-2 mt-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $service['uptime'] }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Incidents Tab -->
    <div x-show="activeTab === 'incidents'" x-transition class="space-y-6">
        <!-- Filter -->
        <div class="bg-themed-secondary rounded-xl p-6 border border-themed-primary">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-themed-primary mb-2">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-themed-secondary"></i>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search"
                               placeholder="Search incidents..."
                               class="w-full pl-10 pr-4 py-2 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-colors duration-200">
                    </div>
                </div>
                <div class="flex-1">
                    <label for="status_filter" class="block text-sm font-medium text-themed-primary mb-2">Status</label>
                    <select wire:model.live="statusFilter" id="status_filter"
                            class="w-full px-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-colors duration-200">
                        <option value="all">All Incidents</option>
                        <option value="active">Active</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Incidents List -->
        <div class="bg-themed-secondary shadow rounded-xl border border-themed-primary overflow-hidden">
            <div class="divide-y divide-themed-primary">
                @forelse($incidents as $incident)
                    <div class="p-6 hover:bg-themed-tertiary transition-colors duration-200 last:border-b-0 animate__animated animate__fadeInUp">
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

            @if($incidents->hasPages())
                <div class="p-4 border-t border-themed-primary flex justify-center">
                    {{ $incidents->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Report Incident Tab -->
    <div x-show="activeTab === 'create_incident'" x-transition class="space-y-6">
        <form wire:submit.prevent="saveIncident" class="bg-themed-secondary shadow rounded-lg p-6 border border-themed-primary animate__animated animate__fadeInUp">
            <h3 class="text-lg font-semibold text-themed-primary mb-6 flex items-center">
                <i class="fas fa-exclamation-triangle text-indigo-600 dark:text-indigo-400 mr-2"></i>
                {{ $editId ? 'Update' : 'Report New' }} Incident
            </h3>

            <div class="space-y-6">
                <div>
                    <label for="service" class="block text-sm font-semibold text-themed-primary mb-2">Service <span class="text-red-600">*</span></label>
                    <select wire:model="service" id="service"
                            class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-themed-primary transition-colors duration-200">
                        <option value="">Select a service</option>
                        @foreach(['website' => 'Website', 'database' => 'Database', 'api' => 'API', 'cdn' => 'CDN'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('service') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-themed-primary mb-2">Status <span class="text-red-600">*</span></label>
                    <select wire:model="status" id="status"
                            class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-themed-primary transition-colors duration-200">
                        <option value="">Select status</option>
                        <option value="operational">Operational</option>
                        <option value="degraded">Degraded</option>
                        <option value="down">Down</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                    @error('status') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-semibold text-themed-primary mb-2">Title <span class="text-red-600">*</span></label>
                    <input wire:model="title" type="text" id="title"
                           class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-themed-primary transition-colors duration-200"
                           placeholder="Brief incident title">
                    @error('title') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-themed-primary mb-2">Description <span class="text-red-600">*</span></label>
                    <textarea wire:model="description" id="description" rows="6"
                              class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-themed-primary transition-colors duration-200"
                              placeholder="Detailed description of the incident"></textarea>
                    @error('description') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="severity" class="block text-sm font-semibold text-themed-primary mb-2">Severity <span class="text-red-600">*</span></label>
                        <select wire:model="severity" id="severity"
                                class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-themed-primary transition-colors duration-200">
                            <option value="">Select severity</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        @error('severity') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="started_at" class="block text-sm font-semibold text-themed-primary mb-2">Started At <span class="text-red-600">*</span></label>
                        <input wire:model="started_at" type="datetime-local" id="started_at"
                               class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-themed-primary transition-colors duration-200">
                        @error('started_at') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                @if($editId)
                    <button type="button" wire:click="$set('editId', null)"
                            class="px-4 py-2 border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors font-medium">
                        Cancel
                    </button>
                @endif
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-themed-secondary disabled:opacity-50 transition-colors duration-200">
                    <span wire:loading.remove><i class="fas fa-save mr-2"></i>{{ $editId ? 'Update' : 'Report' }} Incident</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- System Health Summary -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-heartbeat text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Overall Uptime</p>
                    <p class="text-2xl font-bold text-themed-primary">99.9%</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Operational Services</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ count($services) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Active Incidents</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $incidents->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>