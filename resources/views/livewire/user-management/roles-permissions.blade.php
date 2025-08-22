<div class="p-6 bg-white rounded-lg shadow-md animate__animated animate__fadeIn" x-data="{ tooltip: '', selectedUsers: [] }">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md animate__animated animate__fadeIn">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md animate__animated animate__fadeIn">
            {{ session('error') }}
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-gray-100 bg-opacity-50 flex items-center justify-center z-50">
        <i class="fas fa-spinner fa-spin text-blue-500 text-3xl" aria-label="Loading"></i>
    </div>

    <!-- Search and Filters -->
    <div class="flex flex-col md:flex-row justify-between mb-6 space-y-4 md:space-y-0 md:space-x-4">
        <div class="relative w-full md:w-1/3">
            <input wire:model.debounce.300ms="search" type="text" placeholder="Search by name or email..." 
                   class="p-2 border rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   aria-describedby="search-help" id="search-input">
            <span id="search-help" class="sr-only">Search users by name or email</span>
        </div>
        <div class="relative w-full md:w-1/4">
            <select wire:model="roleFilter" class="p-2 border rounded-md w-full" aria-describedby="role-filter-help" id="role-filter">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            <span id="role-filter-help" class="sr-only">Filter users by role</span>
        </div>
        <div class="relative w-full md:w-1/6">
            <select wire:model="perPage" class="p-2 border rounded-md w-full" aria-label="Items per page">
                <option value="10">10 per page</option>
                <option value="20">20 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
        <div class="relative w-full md:w-1/6">
            <select wire:model="exportFormat" class="p-2 border rounded-md w-full" aria-label="Export format">
                <option value="csv">Export as CSV</option>
                <option value="json">Export as JSON</option>
            </select>
        </div>
        <button wire:click="export" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300" aria-label="Export users">
            <i class="fas fa-download mr-2"></i> Export
        </button>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-4 flex space-x-4 items-center">
        <select wire:model="bulkRoleAction" class="p-2 border rounded-md" aria-label="Bulk action type">
            <option value="assign">Assign Role</option>
            <option value="remove">Remove Role</option>
        </select>
        <select wire:model="bulkRole" class="p-2 border rounded-md" aria-label="Bulk role">
            <option value="">Select Role</option>
            @foreach($allRoles as $role)
                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <button wire:click="bulkRoleAction" 
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300 disabled:opacity-50" 
                x-bind:disabled="!selectedUsers.length || !$wire.bulkRole" 
                aria-label="Apply bulk role action">
            <i class="fas fa-user-tag mr-2"></i> Apply
        </button>
        <button wire:click="openCreateRoleModal" class="bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600 transition duration-300" aria-label="Create new role">
            <i class="fas fa-plus mr-2"></i> Create New Role
        </button>
    </div>

    <!-- Responsive Table -->
    <div class="overflow-x-auto">
        <table wire:poll.10s class="min-w-full divide-y divide-gray-200 table-auto" aria-label="Roles and Permissions Table">
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        <input type="checkbox" x-model="selectedUsers" x-bind:value="[]" 
                              x-on:change="selectedUsers = selectedUsers.length ? [] : @json($users->pluck('id')->toArray())" 
                              aria-label="Select all users">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Avatar</th>
                    <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer" 
                        aria-sort="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Name @if($sortField === 'name') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </th>
                    <th wire:click="sortBy('email')" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hidden md:table-cell" 
                        aria-sort="{{ $sortField === 'email' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Email @if($sortField === 'email') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider hidden md:table-cell">Permissions</th>
                    <th wire:click="sortBy('is_active')" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer" 
                        aria-sort="{{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Active @if($sortField === 'is_active') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition duration-150 animate__animated animate__fadeIn">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" x-model="selectedUsers" value="{{ $user->id }}" aria-label="Select user {{ $user->name }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="{{ $user->profile_picture ?? asset('images/default-avatar.png') }}" 
                                 alt="Avatar of {{ $user->name }}" class="w-8 h-8 rounded-full object-cover" loading="lazy">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @foreach($user->getRoleNames() as $role)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $role === 'super_admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}" x-on:mouseover="tooltip = 'Role: {{ ucfirst($role) }}'" x-on:mouseout="tooltip = ''">{{ ucfirst($role) }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->getAllPermissions()->pluck('name') as $permission)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800" x-on:mouseover="tooltip = 'Permission: {{ ucfirst(str_replace('_', ' ', $permission)) }}'" x-on:mouseout="tooltip = ''">{{ ucfirst(str_replace('_', ' ', $permission)) }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }}" aria-label="{{ $user->is_active ? 'Active' : 'Inactive' }}"></i>
                            <span class="sr-only">{{ $user->is_active ? 'Yes' : 'No' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="openRoleModal({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900" aria-label="Edit roles for {{ $user->name }}">
                                <i class="fas fa-user-tag"></i>
                            </button>
                            <button wire:click="openActivityModal({{ $user->id }})" class="ml-4 text-gray-600 hover:text-gray-900" aria-label="View activity for {{ $user->name }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Role Assignment Modal -->
    <div x-data="{ open: false }" x-on:open-role-modal.window="open = true" x-on:close-role-modal.window="open = false" 
         x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 animate__animated animate__fadeIn" role="dialog" aria-modal="true" aria-labelledby="role-modal-title" tabindex="-1">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 id="role-modal-title" class="text-lg font-semibold mb-4">Assign Roles</h2>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($allRoles as $role)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}" 
                               class="form-checkbox h-5 w-5 text-blue-600" 
                               aria-label="Assign {{ ucfirst($role) }} role">
                        <span>{{ ucfirst($role) }}</span>
                    </label>
                @endforeach
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button x-on:click="open = false" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400" aria-label="Cancel">Cancel</button>
                <button wire:click="saveRoles" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" aria-label="Save roles">Save</button>
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div x-data="{ open: false }" x-on:open-create-role-modal.window="open = true" x-on:close-create-role-modal.window="open = false" 
         x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 animate__animated animate__fadeIn" role="dialog" aria-modal="true" aria-labelledby="create-role-modal-title" tabindex="-1">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
            <h2 id="create-role-modal-title" class="text-lg font-semibold mb-4">Create New Role</h2>
            <input wire:model="newRoleName" type="text" placeholder="Role Name" class="p-2 border rounded-md w-full mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="New role name">
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($allPermissions as $permission)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="newRolePermissions" value="{{ $permission }}" 
                               class="form-checkbox h-5 w-5 text-blue-600" 
                               aria-label="Assign {{ ucfirst(str_replace('_', ' ', $permission)) }} permission">
                        <span>{{ ucfirst(str_replace('_', ' ', $permission)) }}</span>
                    </label>
                @endforeach
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button x-on:click="open = false" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400" aria-label="Cancel">Cancel</button>
                <button wire:click="createRole" class="bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600" aria-label="Create role">Create</button>
            </div>
        </div>
    </div>

    <!-- Activity Log Modal -->
    <div x-data="{ open: false, activity: [] }" x-on:open-activity-modal.window="open = true; activity = $event.detail.activity" x-on:close-activity-modal.window="open = false" 
         x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 animate__animated animate__fadeIn" role="dialog" aria-modal="true" aria-labelledby="activity-modal-title" tabindex="-1">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg overflow-y-auto max-h-[80vh]">
            <h2 id="activity-modal-title" class="text-lg font-semibold mb-4">User Activity Log</h2>
            <div class="space-y-2">
                <template x-for="log in activity" :key="log.created_at">
                    <p x-text="log.description + ' (' + log.created_at + ')'" class="text-sm text-gray-600"></p>
                </template>
            </div>
            <button x-on:click="open = false" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" aria-label="Close">Close</button>
        </div>
    </div>

    <!-- Tooltip -->
    <div x-show="tooltip" class="fixed bg-gray-800 text-white text-sm px-2 py-1 rounded shadow-lg" x-text="tooltip" style="z-index: 1000;"></div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links('pagination::tailwind') }}
    </div>
</div>