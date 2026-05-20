<div class="space-y-6">
    <!-- Header and Controls -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">License Management</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Monitor and manage institutional licenses</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="sendExpiryNotifications" 
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-bell mr-2"></i>
                Send Expiry Alerts
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors duration-300">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expiry</label>
                <select wire:model.live="expiryFilter" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Licenses</option>
                    <option value="expiring">Expiring Soon</option>
                    <option value="expired">Already Expired</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Per Page</label>
                <select wire:model.live="perPage" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- License Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Institution
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            License Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Users
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" 
                            wire:click="sortBy('license_end_date')">
                            <div class="flex items-center space-x-1">
                                <span>Expires</span>
                                @if($sortBy === 'license_end_date')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500 dark:text-blue-400"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300 dark:text-gray-600"></i>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($institutions as $institution)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
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
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ Str::limit($institution->name, 30) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $institution->institution_type_name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $institution->license_type_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $institution->api_key }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $institution->licenseLimitLabel() }}
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                    <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ min(100, $institution->getUserCapacityPercentage()) }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    @if($institution->license_type === 'enterprise')
                                        Unlimited seats
                                    @else
                                        {{ $institution->getUserCapacityPercentage() }}% capacity · {{ number_format($institution->remainingLicenseSeats()) }} left
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($institution->license_end_date)
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $institution->license_end_date->format('M d, Y') }}</div>
                                    @php $daysLeft = $institution->getDaysUntilExpiry(); @endphp
                                    @if($daysLeft <= 0)
                                        <div class="text-xs text-red-600 dark:text-red-400 font-medium">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Expired
                                        </div>
                                    @elseif($daysLeft <= 30)
                                        <div class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                                            <i class="fas fa-clock mr-1"></i>{{ $daysLeft }} days left
                                        </div>
                                    @else
                                        <div class="text-xs text-green-600 dark:text-green-400">
                                            <i class="fas fa-check mr-1"></i>{{ $daysLeft }} days left
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">No expiry set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($institution->status === 'active') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                    @elseif($institution->status === 'suspended') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                    @elseif($institution->status === 'expired') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                    @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @endif">
                                    {{ $institution->status_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="openRenewModal({{ $institution->id }})"
                                            class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 transition-colors"
                                            title="Renew License">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    <button wire:click="openUpgradeModal({{ $institution->id }})"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors"
                                            title="Upgrade License">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-key text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No licenses found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No institutions match your current filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($institutions->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>

    <!-- Renew License Modal -->
    @if($showRenewModal && $selectedInstitution)
        <div class="fixed inset-0 bg-gray-600 dark:bg-black bg-opacity-50 dark:bg-opacity-70 overflow-y-auto h-full w-full z-50" wire:click="closeModals">
            <div class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-11/12 md:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Renew License</h3>
                    <button wire:click="closeModals" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="renewLicense" class="mt-6">
                    <div class="space-y-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                            <h4 class="font-medium text-blue-900 dark:text-blue-300">{{ $selectedInstitution->name }}</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-400">Current License: {{ $selectedInstitution->license_type_name }}</p>
                            <p class="text-sm text-blue-700 dark:text-blue-400">Current Expiry: {{ $selectedInstitution->license_end_date?->format('F j, Y') ?? 'Not set' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Expiry Date *</label>
                            <input type="date" wire:model="newEndDate" 
                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('newEndDate') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Renewal Reason *</label>
                            <textarea wire:model="renewalReason" rows="3" 
                                      placeholder="Describe the reason for this renewal..."
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('renewalReason') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closeModals" 
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-white text-sm font-medium rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                            Renew License
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Upgrade License Modal -->
    @if($showUpgradeModal && $selectedInstitution)
        <div class="fixed inset-0 bg-gray-600 dark:bg-black bg-opacity-50 dark:bg-opacity-70 overflow-y-auto h-full w-full z-50" wire:click="closeModals">
            <div class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-11/12 md:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upgrade License</h3>
                    <button wire:click="closeModals" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="upgradeLicense" class="mt-6">
                    <div class="space-y-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                            <h4 class="font-medium text-blue-900 dark:text-blue-300">{{ $selectedInstitution->name }}</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-400">Current: {{ $selectedInstitution->license_type_name }} ({{ number_format($selectedInstitution->max_users) }} users)</p>
                            <p class="text-sm text-blue-700 dark:text-blue-400">Seats in use: {{ $selectedInstitution->licenseLimitLabel() }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">New License Type *</label>
                            <select wire:model.live="newLicenseType" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @foreach($licenseTypes as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('newLicenseType') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Users *</label>
                            <input type="number" wire:model="newMaxUsers" min="1" 
                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('newMaxUsers') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This cannot be lower than the active and pending seats already in use.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="closeModals" 
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-white text-sm font-medium rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                            Upgrade License
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
