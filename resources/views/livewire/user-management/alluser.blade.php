<div class="p-4 sm:p-6 bg-themed-secondary rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden" 
     x-data="{ tooltip: '', mouseX: 0, mouseY: 0 }"
     @mousemove="mouseX = $event.clientX; mouseY = $event.clientY">

    <!-- Flash Message -->
    @if (session('error'))
        <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-themed-primary bg-opacity-50 flex items-center justify-center z-50 transition-colors duration-300">
        <div class="bg-themed-secondary p-6 rounded-xl shadow-2xl border border-themed-primary transition-colors duration-300">
            <i class="fas fa-spinner fa-spin accent-themed-primary text-3xl mb-2 block mx-auto" aria-label="Loading"></i>
            <p class="text-themed-primary text-sm">Loading users...</p>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-themed-primary transition-colors duration-300">All Users</h1>
            <p class="text-sm sm:text-base text-themed-secondary transition-colors duration-300 mt-1">Manage and view all system users</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-2">
            <div class="px-3 py-2 bg-accent-themed-primary/10 border border-accent-themed-primary/20 rounded-lg">
                <span class="text-xs sm:text-sm text-accent-themed-primary font-medium">{{ $users->total() }} Total Users</span>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4 mb-6">
        <!-- Search -->
        <div class="lg:col-span-2">
            <label for="search-input" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Search Users</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" id="search-input" placeholder="Search by name or email..."
                    class="w-full p-3 pl-10 pr-4 border border-themed-primary rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-all duration-200 text-sm"
                    aria-describedby="search-help">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-themed-tertiary text-sm"></i>
                </div>
            </div>
            <span id="search-help" class="sr-only">Search users by name or email</span>
        </div>

        <!-- Role Filter -->
        <div>
            <label for="role-filter" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Role</label>
            <div class="relative">
                <select wire:model.live="roleFilter" id="role-filter" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary" aria-describedby="role-filter-help">
                    <option value="">All Roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-tertiary text-sm"></i>
                </div>
            </div>
            <span id="role-filter-help" class="sr-only">Filter users by role</span>
        </div>

        <!-- Last Login Start -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Last Login From</label>
            <input type="date" wire:model.live="lastLoginStart" 
                class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
        </div>

        <!-- Last Login End -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Last Login To</label>
            <input type="date" wire:model.live="lastLoginEnd" 
                class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
        </div>

        <!-- Per Page -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Per Page</label>
            <div class="relative">
                <select wire:model.live="perPage" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary" aria-label="Items per page">
                    <option value="10">10 per page</option>
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-tertiary text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Export Button -->
        <div class="flex items-end">
            <button wire:click="exportCsv" wire:loading.attr="disabled"
                class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-green-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="Export users to CSV">
                <i class="fas fa-download mr-2"></i> 
                <span wire:loading.remove>Export CSV</span>
                <span wire:loading>Exporting...</span>
            </button>
        </div>
    </div>

    <!-- Statistics Chart -->
    <div class="mb-6 bg-themed-secondary p-4 sm:p-6 rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
        <h3 class="text-lg font-semibold text-themed-primary mb-4 flex items-center">
            <i class="fas fa-chart-bar accent-themed-primary mr-2"></i>
            Users by Role
        </h3>
        <div class="w-full h-48 sm:h-64">
            <canvas id="roleChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Responsive Table -->
    <div class="bg-themed-secondary rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300 overflow-hidden">
        <!-- Mobile Cards (visible on small screens) -->
        <div class="block lg:hidden">
            @forelse($users as $user)
                <div class="border-b border-themed-primary p-4 hover:bg-themed-tertiary transition-colors duration-300">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-themed-primary font-medium text-base break-words">{{ $user->name }}</h4>
                            <p class="text-themed-secondary text-sm break-all">{{ $user->email }}</p>
                        </div>
                        <div class="flex items-center space-x-2 ml-3">
                            <i class="fas fa-check-circle {{ $user->email_verified_at ? 'text-green-500' : 'text-red-500' }} text-sm"
                                aria-label="{{ $user->email_verified_at ? 'Verified' : 'Not verified' }}"></i>
                            <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }} text-sm"
                                aria-label="{{ $user->is_active ? 'Active' : 'Inactive' }}"></i>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                                {{ ucfirst($user->getRoleNames()->first() ?? 'N/A') }}
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-themed-secondary">{{ $user->enrollments_count }} courses</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-xs text-themed-secondary">
                        <span>Last login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                        <div class="flex space-x-2">
                            @can('edit-users')
                                <a href="{{ route('user.edit', $user->id) }}" class="text-accent-themed-primary hover:text-accent-themed-secondary" onclick="event.stopPropagation()" aria-label="Edit user {{ $user->name }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                            @can('view-user-activity')
                                <button wire:click="viewUser({{ $user->id }})" class="text-themed-secondary hover:text-themed-primary" aria-label="View activity for {{ $user->name }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-themed-secondary text-2xl"></i>
                    </div>
                    <p class="text-themed-secondary text-lg font-medium">No users found</p>
                    <p class="text-themed-secondary text-sm">Try adjusting your search filters</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table (hidden on small screens) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full" aria-label="All Users Table">
                <thead class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary text-white">
                    <tr>
                        <th wire:click="sortBy('name')"
                            class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-accent-themed-primary/80 transition-colors duration-200"
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
                        <th wire:click="sortBy('email')"
                            class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-accent-themed-primary/80 transition-colors duration-200"
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
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Verified
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Courses
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Completed
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            In Progress
                        </th>
                        <th wire:click="sortBy('last_login_at')"
                            class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-accent-themed-primary/80 transition-colors duration-200"
                            aria-sort="{{ $sortField === 'last_login_at' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <div class="flex items-center">
                                Last Login 
                                @if ($sortField === 'last_login_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Active
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                    @forelse($users as $user)
                        <tr class="hover:bg-themed-tertiary transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-themed-primary">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-themed-secondary break-all">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                                    {{ ucfirst($user->getRoleNames()->first() ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <i class="fas fa-check-circle {{ $user->email_verified_at ? 'text-green-500' : 'text-red-500' }}"
                                    aria-label="{{ $user->email_verified_at ? 'Verified' : 'Not verified' }}"></i>
                                <span class="sr-only">{{ $user->email_verified_at ? 'Yes' : 'No' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-themed-primary">{{ $user->enrollments_count }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-themed-primary">{{ $user->completed_enrollments_count }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-themed-primary">{{ $user->in_progress_enrollments_count }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-themed-secondary">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }}"
                                    aria-label="{{ $user->is_active ? 'Active' : 'Inactive' }}"></i>
                                <span class="sr-only">{{ $user->is_active ? 'Yes' : 'No' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    @can('edit-users')
                                        <a href="{{ route('user.edit', $user->id) }}" 
                                           class="text-accent-themed-primary hover:text-accent-themed-secondary transition-colors duration-200" 
                                           aria-label="Edit user {{ $user->name }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('view-user-activity')
                                        <button wire:click="viewUser({{ $user->id }})" 
                                           class="text-themed-secondary hover:text-themed-primary transition-colors duration-200" 
                                           aria-label="View activity for {{ $user->name }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-themed-secondary text-2xl"></i>
                                    </div>
                                    <p class="text-themed-secondary text-lg font-medium">No users found</p>
                                    <p class="text-themed-secondary text-sm">Try adjusting your search filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tooltip -->
    <div x-show="tooltip" 
         x-transition:opacity.duration.200ms 
         class="fixed bg-themed-primary text-white text-sm px-3 py-2 rounded-lg shadow-lg pointer-events-none z-40" 
         x-text="tooltip"
         :style="'left: ' + (mouseX + 10) + 'px; top: ' + (mouseY - 40) + 'px'"
         style="display: none;">
    </div>

    <!-- Pagination -->
    <div class="mt-6 bg-themed-secondary p-4 rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
        {{ $users->links('pagination::tailwind') }}
    </div>

    <!-- Chart Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chartInstance = null;

    function initializeChart() {
        const ctx = document.getElementById('roleChart');
        if (!ctx) return;

        if (chartInstance) {
            chartInstance.destroy();
        }

        const isDark = document.documentElement.classList.contains('dark');
        const root = document.documentElement;
        const bgPrimary = getComputedStyle(root).getPropertyValue('--bg-primary').trim();
        const accentPrimary = getComputedStyle(root).getPropertyValue('--accent-primary').trim();
        
        const roles = @json($roles);
        const roleStats = @json(array_values($roleStats));
        
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: roles.map(role => role.charAt(0).toUpperCase() + role.slice(1).replace(/_/g, ' ')),
                datasets: [{
                    label: 'Users by Role',
                    data: roleStats,
                    backgroundColor: `rgba(${accentPrimary}, 0.6)`,
                    borderColor: `rgba(${accentPrimary}, 1)`,
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { 
                        ticks: { 
                            color: `rgb(${getComputedStyle(root).getPropertyValue('--text-secondary').trim()})`,
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            color: `rgba(${getComputedStyle(root).getPropertyValue('--border-primary').trim()}, 0.1)`
                        }
                    },
                    y: { 
                        beginAtZero: true,
                        ticks: { color: `rgb(${getComputedStyle(root).getPropertyValue('--text-secondary').trim()})` },
                        grid: {
                            color: `rgba(${getComputedStyle(root).getPropertyValue('--border-primary').trim()}, 0.1)`
                        }
                    }
                }
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initializeChart);

    // Reinitialize when Livewire updates
    if (window.Livewire) {
        Livewire.hook('element.updated', (el) => {
            if (el.querySelector('#roleChart')) {
                setTimeout(initializeChart, 50);
            }
        });
    }

    // Handle theme changes
    new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                setTimeout(initializeChart, 100);
            }
        });
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
</script>
</div>