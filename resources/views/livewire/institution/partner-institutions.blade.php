<div class="space-y-6">
    <!-- Header and Controls -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Partner Institutions</h2>
            <p class="text-sm text-gray-600">Manage institutional partnerships and licensing</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="export" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-download mr-2"></i>
                Export
            </button>
            <button wire:click="openCreateModal" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Add Institution
            </button>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Search institutions..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Statuses</option>
                    @foreach($statuses as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="typeFilter" class="w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Types</option>
                    @foreach($institutionTypes as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                <select wire:model.live="perPage" class="w-full border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Institutions Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" 
                            wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Institution</span>
                                @if($sortBy === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" 
                            wire:click="sortBy('institution_type')">
                            <div class="flex items-center space-x-1">
                                <span>Type</span>
                                @if($sortBy === 'institution_type')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" 
                            wire:click="sortBy('status')">
                            <div class="flex items-center space-x-1">
                                <span>Status</span>
                                @if($sortBy === 'status')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Users
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" 
                            wire:click="sortBy('license_type')">
                            <div class="flex items-center space-x-1">
                                <span>License</span>
                                @if($sortBy === 'license_type')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" 
                            wire:click="sortBy('created_at')">
                            <div class="flex items-center space-x-1">
                                <span>Created</span>
                                @if($sortBy === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($institutions as $institution)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($institution->logo)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($institution->logo) }}" alt="{{ $institution->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">{{ substr($institution->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ Str::limit($institution->name, 30) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $institution->email }}
                                        </div>
                                        @if($institution->city)
                                            <div class="text-xs text-gray-400">
                                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $institution->city }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $institution->institution_type_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($institution->status === 'active') bg-green-100 text-green-800
                                    @elseif($institution->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($institution->status === 'suspended') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 
                                        @if($institution->status === 'active') bg-green-500
                                        @elseif($institution->status === 'pending') bg-yellow-500
                                        @elseif($institution->status === 'suspended') bg-red-500
                                        @else bg-gray-500 @endif"></span>
                                    {{ $institution->status_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($institution->active_users_count) }} / {{ number_format($institution->max_users) }}
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         style="width: {{ $institution->getUserCapacityPercentage() }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $institution->getUserCapacityPercentage() }}% capacity
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $institution->license_type_name }}</div>
                                @if($institution->license_end_date)
                                    <div class="text-xs text-gray-500">
                                        Expires {{ $institution->license_end_date->format('M d, Y') }}
                                    </div>
                                    @if($institution->isNearExpiry())
                                        <div class="text-xs text-red-600">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            {{ $institution->getDaysUntilExpiry() }} days left
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $institution->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="openViewModal({{ $institution->id }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button wire:click="openEditModal({{ $institution->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    @if($institution->isPending())
                                        <button wire:click="approve({{ $institution->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    
                                    @if($institution->isActive())
                                        <button wire:click="suspend({{ $institution->id }})"
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                                title="Suspend">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @endif
                                    
                                    @if($institution->isSuspended())
                                        <button wire:click="unsuspend({{ $institution->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Unsuspend">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                    
                                    <button wire:click="openDeleteModal({{ $institution->id }})"
                                            class="text-red-600 hover:text-red-900 transition-colors"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-university text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No institutions found</h3>
                                    <p class="text-sm text-gray-500 mb-4">Get started by adding your first partner institution.</p>
                                    <button wire:click="openCreateModal"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Institution
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($institutions->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModals">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $showCreateModal ? 'Add New Institution' : 'Edit Institution' }}
                    </h3>
                    <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="{{ $showCreateModal ? 'create' : 'update' }}" class="mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-900">Basic Information</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Institution Name *</label>
                                <input type="text" wire:model="form.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email *</label>
                                <input type="email" wire:model="form.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" wire:model="form.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Website</label>
                                <input type="url" wire:model="form.website" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.website') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Institution Type *</label>
                                <select wire:model="form.institution_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($institutionTypes as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('form.institution_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Logo</label>
                                <input type="file" wire:model="logo" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('logo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                
                                @if($existingLogo && !$logo)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($existingLogo) }}" alt="Current logo" class="w-16 h-16 object-cover rounded-lg">
                                    </div>
                                @endif
                                
                                @if($logo)
                                    <div class="mt-2">
                                        <img src="{{ $logo->temporaryUrl() }}" alt="New logo preview" class="w-16 h-16 object-cover rounded-lg">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Address and License -->
                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-900">Address & License</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea wire:model="form.address" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                @error('form.address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" wire:model="form.city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('form.city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">State/Province</label>
                                    <input type="text" wire:model="form.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('form.state') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Country</label>
                                    <input type="text" wire:model="form.country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('form.country') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Postal Code</label>
                                    <input type="text" wire:model="form.postal_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('form.postal_code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">License Type *</label>
                                <select wire:model.live="form.license_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @foreach($licenseTypes as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('form.license_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Max Users *</label>
                                <input type="number" wire:model="form.max_users" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.max_users') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">License Start Date</label>
                                    <input type="date" wire:model="form.license_start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('form.license_start_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">License End Date</label>
                                    <input type="date" wire:model="form.license_end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('form.license_end_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Admin Email *</label>
                                <input type="email" wire:model="form.admin_email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('form.admin_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="form.description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('form.description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="closeModals" 
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                            {{ $showCreateModal ? 'Create Institution' : 'Update Institution' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- View Modal -->
    @if($showViewModal && $selectedInstitution)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModals">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Institution Details</h3>
                    <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="mt-6 space-y-6">
                    <div class="flex items-center space-x-4">
                        @if($selectedInstitution->logo)
                            <img class="h-16 w-16 rounded-lg object-cover" src="{{ Storage::url($selectedInstitution->logo) }}" alt="{{ $selectedInstitution->name }}">
                        @else
                            <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white font-semibold text-lg">{{ substr($selectedInstitution->name, 0, 2) }}</span>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">{{ $selectedInstitution->name }}</h4>
                            <p class="text-gray-600">{{ $selectedInstitution->institution_type_name }}</p>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $selectedInstitution->status_color }}-100 text-{{ $selectedInstitution->status_color }}-800">
                                    {{ $selectedInstitution->status_name }}
                                </span>
                                @if($selectedInstitution->website)
                                    <a href="{{ $selectedInstitution->website }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i>Website
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h5 class="font-medium text-gray-900">Contact Information</h5>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-envelope w-4 text-gray-400 mr-3"></i>
                                    <span>{{ $selectedInstitution->email }}</span>
                                </div>
                                @if($selectedInstitution->phone)
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-phone w-4 text-gray-400 mr-3"></i>
                                        <span>{{ $selectedInstitution->phone }}</span>
                                    </div>
                                @endif
                                @if($selectedInstitution->full_address)
                                    <div class="flex items-start text-sm">
                                        <i class="fas fa-map-marker-alt w-4 text-gray-400 mr-3 mt-0.5"></i>
                                        <span>{{ $selectedInstitution->full_address }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($selectedInstitution->adminUser)
                                <div class="pt-4 border-t border-gray-200">
                                    <h5 class="font-medium text-gray-900 mb-2">Administrator</h5>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium">{{ $selectedInstitution->adminUser->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $selectedInstitution->adminUser->email }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <h5 class="font-medium text-gray-900">License Information</h5>
                            <div class="space-y-3">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900">{{ $selectedInstitution->license_type_name }}</div>
                                    <div class="text-xs text-gray-500">License Type</div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-blue-50 p-3 rounded-lg">
                                        <div class="text-lg font-bold text-blue-600">{{ number_format($selectedInstitution->current_users) }}</div>
                                        <div class="text-xs text-blue-600">Active Users</div>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded-lg">
                                        <div class="text-lg font-bold text-purple-600">{{ number_format($selectedInstitution->max_users) }}</div>
                                        <div class="text-xs text-purple-600">Max Users</div>
                                    </div>
                                </div>

                                @if($selectedInstitution->license_start_date && $selectedInstitution->license_end_date)
                                    <div class="space-y-1">
                                        <div class="text-sm">
                                            <span class="text-gray-500">Valid:</span>
                                            {{ $selectedInstitution->license_start_date->format('M d, Y') }} - 
                                            {{ $selectedInstitution->license_end_date->format('M d, Y') }}
                                        </div>
                                        @if($selectedInstitution->isNearExpiry())
                                            <div class="text-xs text-red-600">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Expires in {{ $selectedInstitution->getDaysUntilExpiry() }} days
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($selectedInstitution->description)
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Description</h5>
                            <p class="text-sm text-gray-600">{{ $selectedInstitution->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($selectedInstitution->total_courses_accessed) }}</div>
                            <div class="text-xs text-gray-500">Courses Accessed</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($selectedInstitution->total_certificates_issued) }}</div>
                            <div class="text-xs text-gray-500">Certificates Issued</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $selectedInstitution->created_at->format('M Y') }}</div>
                            <div class="text-xs text-gray-500">Member Since</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <button wire:click="openEditModal({{ $selectedInstitution->id }})" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Institution
                    </button>
                    <button wire:click="closeModals" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-md transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal && $selectedInstitution)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModals">
            <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Delete Institution</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete <strong>{{ $selectedInstitution->name }}</strong>? 
                            This action cannot be undone and will remove all associated data.
                        </p>
                    </div>
                    <div class="flex items-center justify-center space-x-3 mt-4">
                        <button wire:click="closeModals" 
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-md transition-colors">
                            Cancel
                        </button>
                        <button wire:click="delete" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors">
                            Delete Institution
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>