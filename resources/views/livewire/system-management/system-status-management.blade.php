<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-server mr-2"></i> System Status Management
        </h1>
        <p class="text-gray-400 mt-2">Manage system status and incidents</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-8 animate__animated animate__fadeInUp">
        <form wire:submit.prevent="saveIncident">
            <div class="space-y-6">
                <div>
                    <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
                    <select wire:model="service" id="service"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="website">Website</option>
                        <option value="database">Database</option>
                        <option value="api">API</option>
                    </select>
                    @error('service') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model="status" id="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="operational">Operational</option>
                        <option value="degraded">Degraded</option>
                        <option value="down">Down</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input wire:model="title" type="text" id="title"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="description" id="description" rows="5"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-700">Severity</label>
                    <select wire:model="severity" id="severity"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    @error('severity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="started_at" class="block text-sm font-medium text-gray-700">Started At</label>
                    <input wire:model="started_at" type="datetime-local" id="started_at"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('started_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <span wire:loading.remove><i class="fas fa-save mr-2"></i> {{ $editId ? 'Update' : 'Report' }} Incident</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Incidents List -->
    <div class="bg-white shadow rounded-lg p-6">
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
        <div class="space-y-4">
            @forelse($incidents as $incident)
                <div class="border-b border-gray-200 pb-4 animate__animated animate__fadeInUp">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $incident->title }}</h3>
                            <p class="text-sm text-gray-600">{{ Str::limit($incident->description, 100) }}</p>
                            <p class="text-xs text-gray-400">
                                {{ ucfirst($incident->service) }} - {{ ucfirst($incident->status) }} - 
                                Severity: {{ ucfirst($incident->severity) }} -
                                Started: {{ $incident->started_at->format('M d, Y H:i') }}
                                @if($incident->resolved_at)
                                    , Resolved: {{ $incident->resolved_at->format('M d, Y H:i') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="editIncident({{ $incident->id }})"
                                    class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(!$incident->resolved_at)
                                <button wire:click="resolveIncident({{ $incident->id }})"
                                        class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                            <button wire:click="deleteIncident({{ $incident->id }})"
                                    class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No incidents found.</p>
            @endforelse
            <div class="mt-4">{{ $incidents->links() }}</div>
        </div>
    </div>
</div>