<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" wire:poll.5000ms>
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-server mr-2"></i> System Status
        </h1>
        <p class="text-gray-400 mt-2">Check the operational status of our services</p>
    </div>

    <!-- Services Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($services as $service)
            <div class="bg-white shadow rounded-lg p-6 animate__animated animate__fadeInUp">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $service['name'] }}</h3>
                        <p class="text-sm text-gray-600 capitalize">{{ $service['status'] }}</p>
                    </div>
                    <div>
                        @if($service['status'] === 'operational')
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        @elseif($service['status'] === 'degraded')
                            <i class="fas fa-exclamation-circle text-yellow-500 text-2xl"></i>
                        @elseif($service['status'] === 'down')
                            <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                        @else
                            <i class="fas fa-wrench text-blue-500 text-2xl"></i>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Incident History -->
    <div class="bg-white shadow rounded-lg p-6 animate__animated animate__fadeInUp">
        <div class="mb-4 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search incidents..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <select wire:model.live="statusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Incidents</option>
                    <option value="active">Active</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
        </div>
        <div x-data="{ openIncident: null }" class="space-y-4">
            @forelse($incidents as $incident)
                <div class="border-b border-gray-200 pb-4 animate__animated animate__fadeInUp">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $incident->title }}</h3>
                            <p class="text-sm text-gray-600">{{ Str::limit($incident->description, 100) }}</p>
                            <p class="text-xs text-gray-400">
                                {{ ucfirst($incident->service) }} - {{ ucfirst($incident->status) }} - 
                                Started: {{ $incident->started_at->format('M d, Y H:i') }}
                                @if($incident->resolved_at)
                                    , Resolved: {{ $incident->resolved_at->format('M d, Y H:i') }}
                                @endif
                            </p>
                        </div>
                        <button @click="openIncident = openIncident === '{{ $incident->id }}' ? null : '{{ $incident->id }}'"
                                class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-chevron-down" x-bind:class="{ 'fa-chevron-up': openIncident === '{{ $incident->id }}' }"></i>
                        </button>
                    </div>
                    <div x-show="openIncident === '{{ $incident->id }}'" x-transition class="mt-2 text-sm text-gray-600">
                        {{ $incident->description }}
                        <p class="text-xs text-gray-400 mt-2">Reported by {{ $incident->user->name }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No incidents found.</p>
            @endforelse
            <div class="mt-4">{{ $incidents->links() }}</div>
        </div>
    </div>
</div>