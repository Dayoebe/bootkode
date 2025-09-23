<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden">
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
            <p class="text-gray-700 dark:text-gray-300 text-sm">Processing verifications...</p>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Pending Verifications</h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300 mt-1">Manage unverified user accounts</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-2">
            <div class="px-3 py-2 bg-orange-100 dark:bg-orange-500/20 border border-orange-200 dark:border-orange-500/30 rounded-lg">
                <span class="text-xs sm:text-sm text-orange-700 dark:text-orange-300 font-medium">{{ $users->total() }} Unverified</span>
            </div>
        </div>
    </div>

    <!-- Simple Search and Filters -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <!-- Search -->
        <div class="lg:col-span-2">
            <label for="search-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Search Users</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" id="search-input" placeholder="Search by name or email..."
                    class="w-full p-3 pl-10 pr-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 dark:focus:ring-orange-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 transition-all duration-200 text-sm"
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
                <select wire:model.live="roleFilter" id="role-filter" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-orange-500 dark:focus:ring-orange-400" aria-describedby="role-filter-help">
                    <option value="">All Roles</option>
                    @foreach ($roles as $role)
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
                <select wire:model.live="perPage" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-orange-500 dark:focus:ring-orange-400" aria-label="Items per page">
                    <option value="10">10 per page</option>
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-6 bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
            <i class="fas fa-tasks mr-2 text-orange-500 dark:text-orange-400"></i>
            Bulk Actions
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Verify Selected -->
            <button wire:click="bulkVerify"
                class="px-4 py-3 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-green-500/25 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-check-circle mr-2"></i> Verify Selected
            </button>

            <!-- Send Reminders -->
            <button wire:click="bulkSendReminders"
                class="px-4 py-3 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-envelope mr-2"></i> Send Reminders
            </button>

            <!-- Selected Count -->
            <div class="flex items-center justify-center px-4 py-3 bg-orange-100 dark:bg-orange-500/20 border border-orange-200 dark:border-orange-500/30 rounded-xl">
                <span class="text-sm text-orange-700 dark:text-orange-300 font-medium">
                    <span id="selected-count">0</span> selected
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
                            <input type="checkbox" value="{{ $user->id }}" 
                                class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 dark:focus:ring-orange-400 bg-white dark:bg-gray-700" 
                                aria-label="Select user {{ $user->name }}">
                            <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                        alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-gray-900 dark:text-white font-medium text-base break-words">{{ $user->name }}</h4>
                                <p class="text-gray-500 dark:text-gray-400 text-sm break-all">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="ml-3 flex items-center">
                            <i class="fas fa-clock text-orange-500 text-sm" title="Pending verification"></i>
                        </div>
                    </div>
                    
                    <!-- User Info -->
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Roles:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($user->getRoleNames() as $role)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $role === 'super_admin' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-500/30' : 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30' }}">
                                        {{ ucfirst($role) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Registered:</p>
                            <p class="text-xs text-gray-900 dark:text-white font-medium">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last Login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex justify-end space-x-2">
                        <button wire:click="verifyUser({{ $user->id }})" 
                            class="px-3 py-2 bg-green-100 dark:bg-green-500/20 hover:bg-green-200 dark:hover:bg-green-500/30 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-lg text-xs font-medium transition-colors duration-300" 
                            aria-label="Verify user {{ $user->name }}">
                            <i class="fas fa-check-circle mr-1"></i> Verify
                        </button>
                        <button wire:click="sendVerificationReminder({{ $user->id }})" 
                            class="px-3 py-2 bg-blue-100 dark:bg-blue-500/20 hover:bg-blue-200 dark:hover:bg-blue-500/30 border border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-medium transition-colors duration-300" 
                            aria-label="Send verification reminder to {{ $user->name }}">
                            <i class="fas fa-envelope mr-1"></i> Remind
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-check text-gray-400 dark:text-gray-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">All users are verified!</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm">No pending verifications found</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table (hidden on small screens) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full" aria-label="Pending Verifications Table">
                <thead class="bg-gradient-to-r from-orange-500 to-red-600 dark:from-orange-600 dark:to-red-700 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            <input type="checkbox" id="select-all"
                                class="rounded border-white/30 text-orange-600 focus:ring-orange-500 bg-white/10"
                                aria-label="Select all users">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Avatar</th>
                        <th wire:click="sortBy('name')" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-orange-600 dark:hover:bg-orange-700 transition-colors duration-200"
                            aria-sort="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <div class="flex items-center">
                                Name 
                                @if ($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('email')" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-orange-600 dark:hover:bg-orange-700 transition-colors duration-200"
                            aria-sort="{{ $sortField === 'email' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <div class="flex items-center">
                                Email 
                                @if ($sortField === 'email')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Roles</th>
                        <th wire:click="sortBy('created_at')" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-orange-600 dark:hover:bg-orange-700 transition-colors duration-200"
                            aria-sort="{{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <div class="flex items-center">
                                Registered 
                                @if ($sortField === 'created_at')
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
                                <input type="checkbox" value="{{ $user->id }}"
                                    class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 dark:focus:ring-orange-400 bg-white dark:bg-gray-700"
                                    aria-label="Select user {{ $user->name }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    @if($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                            alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold">
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
                                    @foreach ($user->getRoleNames() as $role)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $role === 'super_admin' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-500/30' : 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30' }}">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center space-x-3">
                                    <button wire:click="verifyUser({{ $user->id }})" 
                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 transition-colors duration-200" 
                                        aria-label="Verify user {{ $user->name }}">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                    <button wire:click="sendVerificationReminder({{ $user->id }})" 
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-200" 
                                        aria-label="Send verification reminder to {{ $user->name }}">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-user-check text-gray-400 dark:text-gray-500 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">All users are verified!</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm">No pending verifications found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 bg-white dark:bg-gray-700/30 p-4 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
        {{ $users->links('pagination::tailwind') }}
    </div>
</div>

<script>
// Handle checkbox selection
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#select-all)');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateSelectedCount() {
        const checked = document.querySelectorAll('input[type="checkbox"]:not(#select-all):checked').length;
        selectedCountSpan.textContent = checked;
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    updateSelectedCount();
});
</script>