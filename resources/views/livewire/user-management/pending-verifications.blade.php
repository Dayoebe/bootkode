<div class="p-4 sm:p-6 bg-themed-secondary rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden">
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
            <p class="text-themed-primary text-sm">Processing verifications...</p>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary transition-colors duration-300">Pending Verifications</h1>
                <p class="mt-1 text-sm sm:text-base text-themed-secondary transition-colors duration-300">Manage unverified user accounts</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-2 bg-orange-100 border border-orange-200 rounded-lg">
                <span class="text-xs sm:text-sm text-orange-700 font-medium">{{ $users->total() }} Unverified</span>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Search -->
        <div class="lg:col-span-2">
            <label for="search-input" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Search Users</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" id="search-input" placeholder="Search by name or email..."
                    class="w-full p-3 pl-10 pr-4 border border-themed-primary rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-all duration-200 text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Role Filter -->
        <div>
            <label for="role-filter" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Role Filter</label>
            <div class="relative">
                <select wire:model.live="roleFilter" id="role-filter" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">All Roles</option>
                    @foreach ($roles as $role)
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
                <select wire:model.live="perPage" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="10">10 per page</option>
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="mb-6 bg-themed-tertiary p-4 sm:p-6 rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
        <h3 class="text-sm font-bold text-themed-primary mb-4 flex items-center">
            <i class="fas fa-tasks mr-2 text-orange-500"></i>
            Bulk Actions
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Verify Selected -->
            <button wire:click="bulkVerify"
                class="px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-green-500/25 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                <i class="fas fa-check-circle mr-2"></i> Verify Selected
            </button>

            <!-- Send Reminders -->
            <button wire:click="bulkSendReminders"
                class="px-4 py-3 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-accent-themed-primary/25 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                <i class="fas fa-envelope mr-2"></i> Send Reminders
            </button>

            <!-- Selected Count -->
            <div class="flex items-center justify-center px-4 py-3 bg-orange-100 border border-orange-200 rounded-xl">
                <span class="text-sm text-orange-700 font-medium">
                    <span id="selected-count">0</span> selected
                </span>
            </div>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-themed-secondary rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full" aria-label="Pending Verifications Table">
                <thead class="bg-gradient-to-r from-orange-500 to-red-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                            <input type="checkbox" id="select-all"
                                class="rounded border-white/30 text-orange-600 focus:ring-orange-500 bg-white/10"
                                aria-label="Select all users">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Avatar</th>
                        <th wire:click="sortBy('name')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider cursor-pointer hover:opacity-80 transition-opacity duration-200" aria-sort="ascending">
                            <div class="flex items-center">
                                Name 
                                @if ($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('email')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider cursor-pointer hover:opacity-80 transition-opacity duration-200" aria-sort="none">
                            <div class="flex items-center">
                                Email 
                                @if ($sortField === 'email')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Roles</th>
                        <th wire:click="sortBy('created_at')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider cursor-pointer hover:opacity-80 transition-opacity duration-200" aria-sort="none">
                            <div class="flex items-center">
                                Registered 
                                @if ($sortField === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                    @forelse($users as $user)
                        <tr class="hover:bg-themed-tertiary transition-colors duration-200 animate__animated animate__fadeIn">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" value="{{ $user->id }}"
                                    class="rounded border-themed-primary text-orange-600 focus:ring-orange-500 bg-themed-secondary"
                                    aria-label="Select user {{ $user->name }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    @if($user->profile_picture)
                                        {{-- <img src="{{ asset('storage/' . $user->profile_picture) }}"  --}}
                                        <img src="{{ $user->profile_picture }}"
                                            alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold">
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->getRoleNames() as $role)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-themed-secondary">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1 flex-wrap">
                                    <button wire:click="verifyUser({{ $user->id }})" 
                                        class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-green-100 text-green-700 border border-green-200 rounded-lg hover:bg-green-200 transition-colors duration-200 whitespace-nowrap">
                                        <i class="fas fa-check-circle"></i><span class="hidden sm:inline">Verify</span>
                                    </button>
                                    <button wire:click="sendVerificationReminder({{ $user->id }})" 
                                        class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20 rounded-lg hover:bg-accent-themed-primary/20 transition-colors duration-200 whitespace-nowrap">
                                        <i class="fas fa-envelope"></i><span class="hidden sm:inline">Remind</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mb-4 transition-colors duration-300">
                                        <i class="fas fa-user-check text-themed-secondary text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-themed-primary transition-colors duration-300">All users are verified!</h3>
                                    <p class="text-sm text-themed-secondary transition-colors duration-300 mt-1">No pending verifications found</p>
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
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <input type="checkbox" value="{{ $user->id }}" 
                            class="rounded border-themed-primary text-orange-600 focus:ring-orange-500 bg-themed-secondary flex-shrink-0"
                            aria-label="Select user {{ $user->name }}">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                            @if($user->profile_picture)
                                {{-- <img src="{{ asset('storage/' . $user->profile_picture) }}"  --}}
                                <img src="{{ $user->profile_picture }}"
                                    alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-themed-primary font-semibold text-sm truncate">{{ $user->name }}</h4>
                            <p class="text-themed-secondary text-xs truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    <i class="fas fa-clock text-orange-500 text-sm flex-shrink-0 ml-2"></i>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <p class="text-xs text-themed-secondary mb-1">Roles:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($user->getRoleNames() as $role)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                                    {{ ucfirst($role) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-themed-secondary">Registered:</p>
                        <p class="text-xs text-themed-primary font-medium">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-themed-secondary">Last Login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button wire:click="verifyUser({{ $user->id }})" 
                        class="flex-1 text-xs px-3 py-2 bg-green-100 text-green-700 border border-green-200 rounded-lg hover:bg-green-200 transition-colors duration-200 font-medium">
                        <i class="fas fa-check-circle mr-1"></i>Verify
                    </button>
                    <button wire:click="sendVerificationReminder({{ $user->id }})" 
                        class="flex-1 text-xs px-3 py-2 bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20 rounded-lg hover:bg-accent-themed-primary/20 transition-colors duration-200 font-medium">
                        <i class="fas fa-envelope mr-1"></i>Remind
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-themed-secondary rounded-xl border border-themed-primary p-8 text-center">
                <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-user-check text-themed-secondary text-2xl"></i>
                </div>
                <h3 class="text-sm font-medium text-themed-primary transition-colors duration-300">All users are verified!</h3>
                <p class="text-sm text-themed-secondary transition-colors duration-300 mt-1">No pending verifications found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6 bg-themed-secondary p-4 rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
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