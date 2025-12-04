<div class="p-4 sm:p-6 bg-themed-secondary rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden" x-data="{ tooltip: '', selectedUsers: [] }">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
                <span class="text-sm sm:text-base">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
                <span class="text-sm sm:text-base">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-themed-primary bg-opacity-50 flex items-center justify-center z-50 transition-colors duration-300">
        <div class="bg-themed-secondary p-6 rounded-xl shadow-2xl border border-themed-primary transition-colors duration-300">
            <i class="fas fa-spinner fa-spin text-accent-themed-primary text-3xl mb-2 block mx-auto"></i>
            <p class="text-themed-primary text-sm">Processing roles...</p>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary transition-colors duration-300">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Roles & Permissions
                </h1>
                <p class="mt-1 text-sm sm:text-base text-themed-secondary transition-colors duration-300">Manage user roles and permissions</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-2 bg-accent-themed-primary/10 border border-accent-themed-primary/20 rounded-lg">
                <span class="text-xs sm:text-sm text-accent-themed-primary font-medium">{{ $users->total() }} Users</span>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Search -->
        <div class="lg:col-span-2">
            <label for="search-input" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Search Users</label>
            <div class="relative">
                <input wire:model.debounce.300ms="search" type="text" id="search-input" placeholder="Search by name or email..."
                    class="w-full p-3 pl-10 pr-4 border border-themed-primary rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-all duration-200 text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Role Filter -->
        <div>
            <label for="role-filter" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Role</label>
            <div class="relative">
                <select wire:model="roleFilter" id="role-filter" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Per Page -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Per Page</label>
            <div class="relative">
                <select wire:model="perPage" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="10">10 per page</option>
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Export Format -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Export</label>
            <div class="relative">
                <select wire:model="exportFormat" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="csv">Export as CSV</option>
                    <option value="json">Export as JSON</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Export Button -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">&nbsp;</label>
            <button wire:click="export"
                class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-green-500/25 whitespace-nowrap">
                <i class="fas fa-download mr-2"></i> Export
            </button>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-6 bg-themed-tertiary p-4 sm:p-6 rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
        <h3 class="text-sm font-bold text-themed-primary mb-4 flex items-center">
            <i class="fas fa-tasks mr-2 text-accent-themed-primary"></i>
            Bulk Actions
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="relative">
                <select wire:model="bulkRoleAction" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="assign">Assign Role</option>
                    <option value="remove">Remove Role</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>

            <div class="relative">
                <select wire:model="bulkRole" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="">Select Role</option>
                    @foreach($allRoles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>

            <button wire:click="bulkRoleAction" 
                class="px-4 py-3 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-accent-themed-primary/25 disabled:opacity-50 disabled:cursor-not-allowed"
                x-bind:disabled="!selectedUsers.length || !$wire.bulkRole">
                <i class="fas fa-user-tag mr-2"></i> Apply
            </button>

            <button wire:click="openCreateRoleModal" 
                class="px-4 py-3 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-accent-themed-primary/25">
                <i class="fas fa-plus mr-2"></i> Create Role
            </button>

            <div class="flex items-center justify-center px-4 py-3 bg-accent-themed-primary/10 border border-accent-themed-primary/20 rounded-xl">
                <span class="text-sm text-accent-themed-primary font-medium">
                    <span x-text="selectedUsers.length"></span> selected
                </span>
            </div>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-themed-secondary rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary ">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                            <input type="checkbox" x-model="selectedUsers" 
                                x-on:change="selectedUsers = selectedUsers.length ? [] : @json($users->pluck('id')->toArray())" 
                                class="rounded border-white/30 focus:ring-white bg-white/10">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Avatar</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Roles</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Active</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                    @forelse($users as $user)
                        <tr class="hover:bg-themed-tertiary transition-colors duration-300 animate__animated animate__fadeIn">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" x-model="selectedUsers" value="{{ $user->id }}" 
                                    class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    @if($user->profile_picture)
                                        {{-- <img src="{{ asset('storage/' . $user->profile_picture) }}"  --}}
                                        <img src="{{ $user->profile_picture }}"
                                            alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary flex items-center justify-center font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-themed-primary">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-themed-secondary break-all">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @foreach($user->getAllPermissions()->take(2)->pluck('name') as $permission)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 border border-green-200">
                                            {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                        </span>
                                    @endforeach
                                    @if($user->getAllPermissions()->count() > 2)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-themed-tertiary text-themed-secondary border border-themed-primary">
                                            +{{ $user->getAllPermissions()->count() - 2 }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }}"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1 flex-wrap">
                                    <button wire:click="openRoleModal({{ $user->id }})" 
                                        class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20 rounded-lg hover:bg-accent-themed-primary/20 transition-colors duration-200 whitespace-nowrap">
                                        <i class="fas fa-user-tag"></i><span class="hidden sm:inline">Roles</span>
                                    </button>
                                    <button wire:click="openActivityModal({{ $user->id }})" 
                                        class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-themed-tertiary text-themed-primary border border-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors duration-200 whitespace-nowrap">
                                        <i class="fas fa-eye"></i><span class="hidden sm:inline">Activity</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mb-4 transition-colors duration-300">
                                        <i class="fas fa-user-shield text-themed-secondary text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-themed-primary transition-colors duration-300">No users found</h3>
                                    <p class="text-sm text-themed-secondary transition-colors duration-300 mt-1">Try adjusting your search filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Cards View -->
    <div class="lg:hidden grid grid-cols-1 gap-4">
        @forelse($users as $user)
            <div class="bg-themed-secondary rounded-xl border border-themed-primary p-4 shadow-md hover:shadow-lg transition-shadow duration-300">
                <!-- Header with checkbox -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <input type="checkbox" x-model="selectedUsers" value="{{ $user->id }}" 
                            class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary flex-shrink-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                            @if($user->profile_picture)
                                {{-- <img src="{{ asset('storage/' . $user->profile_picture) }}"  --}}
                                <img src="{{ $user->profile_picture }}"
                                    alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-themed-primary font-semibold text-sm truncate">{{ $user->name }}</h4>
                            <p class="text-themed-secondary text-xs truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }} text-sm flex-shrink-0 ml-2"></i>
                </div>

                <!-- Roles Section -->
                <div class="mb-3">
                    <p class="text-xs text-themed-secondary mb-2 font-medium">Roles:</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($user->getRoleNames() as $role)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                                {{ ucfirst($role) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="mb-4">
                    <p class="text-xs text-themed-secondary mb-2 font-medium">Permissions:</p>
                    <div class="flex flex-wrap gap-1 max-h-16 overflow-y-auto">
                        @foreach($user->getAllPermissions()->take(3)->pluck('name') as $permission)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 border border-green-200">
                                {{ ucfirst(str_replace('_', ' ', $permission)) }}
                            </span>
                        @endforeach
                        @if($user->getAllPermissions()->count() > 3)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-themed-tertiary text-themed-secondary border border-themed-primary">
                                +{{ $user->getAllPermissions()->count() - 3 }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button wire:click="openRoleModal({{ $user->id }})" 
                        class="flex-1 text-xs px-3 py-2 bg-accent-themed-primary text-white rounded-lg hover:bg-accent-themed-secondary transition-colors duration-200 font-medium">
                        <i class="fas fa-user-tag mr-1"></i>Roles
                    </button>
                    <button wire:click="openActivityModal({{ $user->id }})" 
                        class="flex-1 text-xs px-3 py-2 bg-themed-tertiary text-themed-primary border border-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors duration-200 font-medium">
                        <i class="fas fa-eye mr-1"></i>Activity
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-themed-secondary rounded-xl border border-themed-primary p-8 text-center">
                <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-user-shield text-themed-secondary text-2xl"></i>
                </div>
                <h3 class="text-sm font-medium text-themed-primary transition-colors duration-300">No users found</h3>
                <p class="text-sm text-themed-secondary transition-colors duration-300 mt-1">Try adjusting your search filters</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6 bg-themed-secondary p-4 rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
        {{ $users->links('pagination::tailwind') }}
    </div>

    <!-- Role Assignment Modal -->
    <div x-data="{ open: false }" 
         x-on:open-role-modal.window="open = true" 
         x-on:close-role-modal.window="open = false" 
         x-show="open" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-themed-primary bg-opacity-75 flex items-center justify-center z-50 p-4" 
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="role-modal-title" 
         style="display: none;">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" 
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-themed-secondary rounded-2xl p-6 w-full max-w-md shadow-2xl border border-themed-primary transition-colors duration-300 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 id="role-modal-title" class="text-lg sm:text-xl font-bold text-themed-primary transition-colors duration-300">Assign Roles</h2>
                <button x-on:click="open = false" class="text-themed-secondary hover:text-themed-primary transition-colors duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-3 max-h-64 overflow-y-auto mb-6 bg-themed-tertiary p-4 rounded-lg border border-themed-primary">
                @foreach($allRoles as $role)
                    <label class="flex items-center space-x-3 p-2 hover:bg-themed-secondary rounded-lg transition-colors duration-200 cursor-pointer">
                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}" 
                               class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary">
                        <span class="text-themed-primary font-medium text-sm">{{ ucfirst($role) }}</span>
                    </label>
                @endforeach
            </div>
            
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4">
                <button x-on:click="open = false" 
                    class="w-full sm:w-auto px-4 py-2 bg-themed-tertiary text-themed-primary rounded-xl hover:bg-themed-primary/20 font-medium transition-colors duration-300">
                    Cancel
                </button>
                <button wire:click="saveRoles" 
                    class="w-full sm:w-auto px-4 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-xl font-medium transition-colors duration-300 shadow-lg hover:shadow-accent-themed-primary/25">
                    <i class="fas fa-save mr-2"></i>Save Roles
                </button>
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div x-data="{ open: false }" 
         x-on:open-create-role-modal.window="open = true" 
         x-on:close-create-role-modal.window="open = false" 
         x-show="open" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-themed-primary bg-opacity-75 flex items-center justify-center z-50 p-4" 
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="create-role-modal-title" 
         style="display: none;">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" 
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-themed-secondary rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-themed-primary transition-colors duration-300 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 id="create-role-modal-title" class="text-lg sm:text-xl font-bold text-themed-primary transition-colors duration-300">Create New Role</h2>
                <button x-on:click="open = false" class="text-themed-secondary hover:text-themed-primary transition-colors duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <label for="new-role-name" class="block text-sm font-medium text-themed-primary mb-2">Role Name</label>
                <input wire:model="newRoleName" type="text" id="new-role-name" placeholder="Enter role name..." 
                    class="w-full p-3 border border-themed-primary rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-all duration-200 text-sm">
                @error('newRoleName') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-themed-primary mb-3">Permissions</label>
                <div class="space-y-2 max-h-64 overflow-y-auto bg-themed-tertiary p-4 rounded-lg border border-themed-primary">
                    @foreach($allPermissions as $permission)
                        <label class="flex items-center space-x-3 p-2 hover:bg-themed-secondary rounded-lg transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" wire:model="newRolePermissions" value="{{ $permission }}" 
                                   class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary">
                            <span class="text-themed-primary text-sm">{{ ucfirst(str_replace('_', ' ', $permission)) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4">
                <button x-on:click="open = false" 
                    class="w-full sm:w-auto px-4 py-2 bg-themed-tertiary text-themed-primary rounded-xl hover:bg-themed-primary/20 font-medium transition-colors duration-300">
                    Cancel
                </button>
                <button wire:click="createRole" 
                    class="w-full sm:w-auto px-4 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-xl font-medium transition-colors duration-300 shadow-lg hover:shadow-accent-themed-primary/25">
                    <i class="fas fa-plus mr-2"></i>Create Role
                </button>
            </div>
        </div>
    </div>

    <!-- Activity Log Modal -->
    <div x-data="{ open: false, activity: [] }" 
         x-on:open-activity-modal.window="open = true; activity = $event.detail.activity" 
         x-on:close-activity-modal.window="open = false" 
         x-show="open" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-themed-primary bg-opacity-75 flex items-center justify-center z-50 p-4" 
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="activity-modal-title" 
         style="display: none;">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" 
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-themed-secondary rounded-2xl p-6 w-full max-w-lg shadow-2xl border border-themed-primary transition-colors duration-300 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 id="activity-modal-title" class="text-lg sm:text-xl font-bold text-themed-primary transition-colors duration-300">
                    <i class="fas fa-history mr-2"></i>User Activity Log
                </h2>
                <button x-on:click="open = false" class="text-themed-secondary hover:text-themed-primary transition-colors duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <template x-for="log in activity" :key="log.created_at">
                    <div class="bg-themed-tertiary p-4 rounded-lg border border-themed-primary">
                        <p x-text="log.description" class="text-sm text-themed-primary mb-2 font-medium"></p>
                        <p x-text="log.created_at" class="text-xs text-themed-secondary"></p>
                    </div>
                </template>
                <div x-show="!activity.length" class="text-center py-8">
                    <div class="w-12 h-12 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-themed-secondary"></i>
                    </div>
                    <p class="text-themed-secondary text-sm">No activity recorded</p>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button x-on:click="open = false" 
                    class="px-4 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-xl font-medium transition-colors duration-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>