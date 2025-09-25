<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden" x-data="{ tooltip: '', selectedUsers: [] }">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-4 p-3 sm:p-4 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-700 dark:text-green-300 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 sm:p-4 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-white dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-50 flex items-center justify-center z-50 transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-600 transition-colors duration-300">
            <i class="fas fa-spinner fa-spin text-blue-500 dark:text-blue-400 text-3xl mb-2 block mx-auto" aria-label="Loading"></i>
            <p class="text-gray-700 dark:text-gray-300 text-sm">Processing roles...</p>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Roles & Permissions</h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300 mt-1">Manage user roles and system permissions</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-2">
            <div class="px-3 py-2 bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-500/30 rounded-lg">
                <span class="text-xs sm:text-sm text-purple-700 dark:text-purple-300 font-medium">{{ $users->total() }} Total Users</span>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4 mb-6">
        <!-- Search -->
        <div class="lg:col-span-2">
            <label for="search-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Search Users</label>
            <div class="relative">
                <input wire:model.debounce.300ms="search" type="text" id="search-input" placeholder="Search by name or email..."
                    class="w-full p-3 pl-10 pr-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 transition-all duration-200 text-sm"
                    aria-describedby="search-help">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 dark:text-gray-500 text-sm"></i>
                </div>
            </div>
            <span id="search-help" class="sr-only">Search users by name or email</span>
        </div>

        <!-- Role Filter -->
        <div>
            <label for="role-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Role Filter</label>
            <div class="relative">
                <select wire:model="roleFilter" id="role-filter" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400" aria-describedby="role-filter-help">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-sm"></i>
                </div>
            </div>
            <span id="role-filter-help" class="sr-only">Filter users by role</span>
        </div>

        <!-- Per Page -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Per Page</label>
            <div class="relative">
                <select wire:model="perPage" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400" aria-label="Items per page">
                    <option value="10">10 per page</option>
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Export Format -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Export Format</label>
            <div class="relative">
                <select wire:model="exportFormat" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400" aria-label="Export format">
                    <option value="csv">Export as CSV</option>
                    <option value="json">Export as JSON</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Export Button -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Export</label>
            <button wire:click="export"
                class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-green-500/25"
                aria-label="Export users">
                <i class="fas fa-download mr-2"></i> Export
            </button>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-6 bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
            <i class="fas fa-tasks mr-2 text-purple-500 dark:text-purple-400"></i>
            Bulk Actions
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Bulk Action Type -->
            <div class="relative">
                <select wire:model="bulkRoleAction" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400" aria-label="Bulk action type">
                    <option value="assign">Assign Role</option>
                    <option value="remove">Remove Role</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-sm"></i>
                </div>
            </div>

            <!-- Bulk Role -->
            <div class="relative">
                <select wire:model="bulkRole" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400" aria-label="Bulk role">
                    <option value="">Select Role</option>
                    @foreach($allRoles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-sm"></i>
                </div>
            </div>

            <!-- Apply Button -->
            <button wire:click="bulkRoleAction" 
                class="px-4 py-3 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed" 
                x-bind:disabled="!selectedUsers.length || !$wire.bulkRole" 
                aria-label="Apply bulk role action">
                <i class="fas fa-user-tag mr-2"></i> Apply
            </button>

            <!-- Create Role Button -->
            <button wire:click="openCreateRoleModal" 
                class="px-4 py-3 bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-purple-500/25" 
                aria-label="Create new role">
                <i class="fas fa-plus mr-2"></i> Create Role
            </button>

            <!-- Selected Count -->
            <div class="flex items-center justify-center px-4 py-3 bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-500/30 rounded-xl">
                <span class="text-sm text-purple-700 dark:text-purple-300 font-medium">
                    <span x-text="selectedUsers.length"></span> selected
                </span>
            </div>
        </div>
    </div>

    <!-- Responsive Table Container -->
    <div class="bg-white dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300 overflow-hidden">
        <!-- Mobile Cards (visible on small screens) -->
        <div class="block lg:hidden">
            @forelse($users as $user)
                <div class="border-b border-gray-200 dark:border-gray-600/50 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-300">
                    <!-- User Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <input type="checkbox" x-model="selectedUsers" value="{{ $user->id }}" 
                                class="rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500 dark:focus:ring-purple-400 bg-white dark:bg-gray-700" 
                                aria-label="Select user {{ $user->name }}">
                            <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                        alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-gray-900 dark:text-white font-medium text-base break-words">{{ $user->name }}</h4>
                                <p class="text-gray-500 dark:text-gray-400 text-sm break-all">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-3">
                            <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }} text-sm"
                                aria-label="{{ $user->is_active ? 'Active' : 'Inactive' }}"></i>
                        </div>
                    </div>
                    
                    <!-- Roles -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Roles:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->getRoleNames() as $role)
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $role === 'super_admin' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-500/30' : 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30' }}"
                                    x-on:mouseover="tooltip = 'Role: {{ ucfirst($role) }}'" x-on:mouseout="tooltip = ''">
                                    {{ ucfirst($role) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Permissions:</p>
                        <div class="flex flex-wrap gap-2 max-h-20 overflow-y-auto">
                            @foreach($user->getAllPermissions()->pluck('name') as $permission)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-500/30"
                                    x-on:mouseover="tooltip = 'Permission: {{ ucfirst(str_replace('_', ' ', $permission)) }}'" x-on:mouseout="tooltip = ''">
                                    {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex justify-end space-x-2">
                        <button wire:click="openRoleModal({{ $user->id }})" 
                            class="px-3 py-2 bg-indigo-100 dark:bg-indigo-500/20 hover:bg-indigo-200 dark:hover:bg-indigo-500/30 border border-indigo-200 dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-medium transition-colors duration-300" 
                            aria-label="Edit roles for {{ $user->name }}">
                            <i class="fas fa-user-tag mr-1"></i> Roles
                        </button>
                        <button wire:click="openActivityModal({{ $user->id }})" 
                            class="px-3 py-2 bg-gray-100 dark:bg-gray-600/50 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-medium transition-colors duration-300" 
                            aria-label="View activity for {{ $user->name }}">
                            <i class="fas fa-eye mr-1"></i> Activity
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-shield text-gray-400 dark:text-gray-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No users found</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm">Try adjusting your search filters</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table (hidden on small screens) -->
        <div class="hidden lg:block overflow-x-auto">
            <table wire:poll.10s class="min-w-full" aria-label="Roles and Permissions Table">
                <thead class="bg-gradient-to-r from-purple-500 to-indigo-600 dark:from-purple-600 dark:to-indigo-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            <input type="checkbox" x-model="selectedUsers" x-bind:value="[]" 
                                x-on:change="selectedUsers = selectedUsers.length ? [] : @json($users->pluck('id')->toArray())" 
                                class="rounded border-white/30 text-purple-600 focus:ring-purple-500 bg-white/10"
                                aria-label="Select all users">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Avatar</th>
                        <th wire:click="sortBy('name')" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-purple-600 dark:hover:bg-purple-700 transition-colors duration-200" 
                            aria-sort="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <div class="flex items-center">
                                Name 
                                @if($sortField === 'name') 
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> 
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('email')" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-purple-600 dark:hover:bg-purple-700 transition-colors duration-200" 
                            aria-sort="{{ $sortField === 'email' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <div class="flex items-center">
                                Email 
                                @if($sortField === 'email') 
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> 
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Roles</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Permissions</th>
                        <th wire:click="sortBy('is_active')" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-purple-600 dark:hover:bg-purple-700 transition-colors duration-200" 
                            aria-sort="{{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <div class="flex items-center">
                                Active 
                                @if($sortField === 'is_active') 
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> 
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-700/30 divide-y divide-gray-200 dark:divide-gray-600/50">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200 animate__animated animate__fadeIn">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" x-model="selectedUsers" value="{{ $user->id }}" 
                                    class="rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500 dark:focus:ring-purple-400 bg-white dark:bg-gray-700"
                                    aria-label="Select user {{ $user->name }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    @if($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                            alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300 break-all">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $role === 'super_admin' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-500/30' : 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30' }}"
                                            x-on:mouseover="tooltip = 'Role: {{ ucfirst($role) }}'" x-on:mouseout="tooltip = ''">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-xs overflow-hidden">
                                    @foreach($user->getAllPermissions()->take(3)->pluck('name') as $permission)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-500/30"
                                            x-on:mouseover="tooltip = 'Permission: {{ ucfirst(str_replace('_', ' ', $permission)) }}'" x-on:mouseout="tooltip = ''">
                                            {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                        </span>
                                    @endforeach
                                    @if($user->getAllPermissions()->count() > 3)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-600/20 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                            +{{ $user->getAllPermissions()->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }}" 
                                    aria-label="{{ $user->is_active ? 'Active' : 'Inactive' }}"></i>
                                <span class="sr-only">{{ $user->is_active ? 'Yes' : 'No' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center space-x-3">
                                    <button wire:click="openRoleModal({{ $user->id }})" 
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors duration-200" 
                                        aria-label="Edit roles for {{ $user->name }}">
                                        <i class="fas fa-user-tag"></i>
                                    </button>
                                    <button wire:click="openActivityModal({{ $user->id }})" 
                                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors duration-200" 
                                        aria-label="View activity for {{ $user->name }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-user-shield text-gray-400 dark:text-gray-500 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No users found</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm">Try adjusting your search filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Role Assignment Modal -->
    <div x-data="{ open: false }" 
         x-on:open-role-modal.window="open = true" 
         x-on:close-role-modal.window="open = false" 
         x-show="open" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center z-50 p-4" 
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="role-modal-title" 
         style="display: none;">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-90" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" 
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md shadow-2xl border border-gray-200 dark:border-gray-600 transition-colors duration-300 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 id="role-modal-title" class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-300">Assign Roles</h2>
                <button x-on:click="open = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-3 max-h-64 overflow-y-auto mb-6 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600/50">
                @foreach($allRoles as $role)
                    <label class="flex items-center space-x-3 p-2 hover:bg-gray-100 dark:hover:bg-gray-600/50 rounded-lg transition-colors duration-200 cursor-pointer">
                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}" 
                               class="rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500 dark:focus:ring-purple-400 bg-white dark:bg-gray-700" 
                               aria-label="Assign {{ ucfirst($role) }} role">
                        <span class="text-gray-900 dark:text-white font-medium">{{ ucfirst($role) }}</span>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $role === 'super_admin' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300' : 'bg-gray-100 dark:bg-gray-600/50 text-gray-600 dark:text-gray-300' }}">
                            {{ $role }}
                        </span>
                    </label>
                @endforeach
            </div>
            
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                <button x-on:click="open = false" 
                    class="w-full sm:w-auto px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-500 font-medium transition-colors duration-300" 
                    aria-label="Cancel">
                    Cancel
                </button>
                <button wire:click="saveRoles" 
                    class="w-full sm:w-auto px-4 py-2 bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white rounded-xl font-medium transition-colors duration-300 shadow-lg hover:shadow-purple-500/25" 
                    aria-label="Save roles">
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
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center z-50 p-4" 
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="create-role-modal-title" 
         style="display: none;">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-90" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" 
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-lg shadow-2xl border border-gray-200 dark:border-gray-600 transition-colors duration-300 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 id="create-role-modal-title" class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-300">Create New Role</h2>
                <button x-on:click="open = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <label for="new-role-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role Name</label>
                <input wire:model="newRoleName" type="text" id="new-role-name" placeholder="Enter role name..." 
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 transition-all duration-200" 
                    aria-label="New role name">
                @error('newRoleName')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Permissions</label>
                <div class="space-y-2 max-h-64 overflow-y-auto bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600/50">
                    @foreach($allPermissions as $permission)
                        <label class="flex items-center space-x-3 p-2 hover:bg-gray-100 dark:hover:bg-gray-600/50 rounded-lg transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" wire:model="newRolePermissions" value="{{ $permission }}" 
                                   class="rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500 dark:focus:ring-purple-400 bg-white dark:bg-gray-700" 
                                   aria-label="Assign {{ ucfirst(str_replace('_', ' ', $permission)) }} permission">
                            <span class="text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $permission)) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                <button x-on:click="open = false" 
                    class="w-full sm:w-auto px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-500 font-medium transition-colors duration-300" 
                    aria-label="Cancel">
                    Cancel
                </button>
                <button wire:click="createRole" 
                    class="w-full sm:w-auto px-4 py-2 bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white rounded-xl font-medium transition-colors duration-300 shadow-lg hover:shadow-purple-500/25" 
                    aria-label="Create role">
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
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center z-50 p-4" 
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="activity-modal-title" 
         style="display: none;">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-90" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" 
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-lg shadow-2xl border border-gray-200 dark:border-gray-600 transition-colors duration-300 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 id="activity-modal-title" class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-300">
                    <i class="fas fa-history text-purple-500 dark:text-purple-400 mr-2"></i>
                    User Activity Log
                </h2>
                <button x-on:click="open = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <template x-for="log in activity" :key="log.created_at">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600/50">
                        <p x-text="log.description" class="text-sm text-gray-900 dark:text-white mb-2 font-medium"></p>
                        <p x-text="log.created_at" class="text-xs text-gray-500 dark:text-gray-400"></p>
                    </div>
                </template>
                <div x-show="!activity.length" class="text-center py-8">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">No activity recorded</p>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button x-on:click="open = false" 
                    class="px-4 py-2 bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white rounded-xl font-medium transition-colors duration-300" 
                    aria-label="Close">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Tooltip -->
    <div x-data="{ tooltipX: 0, tooltipY: 0 }" 
         x-show="tooltip" 
         x-transition:opacity.duration.200ms 
         class="fixed bg-gray-800 dark:bg-gray-700 text-white text-sm px-3 py-2 rounded-lg shadow-lg pointer-events-none z-40" 
         x-text="tooltip"
         x-init="
            document.addEventListener('mousemove', (e) => {
                tooltipX = e.clientX;
                tooltipY = e.clientY - 40;
            });
         "
         :style="`left: ${tooltipX}px; top: ${tooltipY}px`"
         style="display: none;"></div>

    <!-- Pagination -->
    <div class="mt-6 bg-white dark:bg-gray-700/30 p-4 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
        {{ $users->links('pagination::tailwind') }}
    </div>
</div>