<div class="p-4 sm:p-6 bg-themed-secondary rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden" 
     x-data="{ tooltip: '', mouseX: 0, mouseY: 0 }"
     @mousemove="mouseX = $event.clientX; mouseY = $event.clientY">

    <!-- Flash Message -->
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
            <p class="text-themed-primary text-sm">Loading users...</p>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary transition-colors duration-300">All Users</h1>
                <p class="mt-1 text-sm sm:text-base text-themed-secondary transition-colors duration-300">Manage and view all system users</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-2 bg-accent-themed-primary/10 border border-accent-themed-primary/20 rounded-lg">
                <span class="text-xs sm:text-sm text-accent-themed-primary font-medium">{{ $users->total() }} Total Users</span>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Search -->
        <div class="lg:col-span-2">
            <label for="search-input" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Search Users</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" id="search-input" placeholder="Search by name or email..."
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
                <select wire:model.live="roleFilter" id="role-filter" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
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

        <!-- Last Login Start -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Login From</label>
            <input type="date" wire:model.live="lastLoginStart" 
                class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
        </div>

        <!-- Last Login End -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Login To</label>
            <input type="date" wire:model.live="lastLoginEnd" 
                class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
        </div>

        <!-- Per Page -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Per Page</label>
            <div class="relative">
                <select wire:model.live="perPage" class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="10">10 per page</option>
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Export Button -->
        <div>
            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">&nbsp;</label>
            <button wire:click="exportCsv" wire:loading.attr="disabled"
                class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition-all duration-300 text-sm shadow-lg hover:shadow-green-500/25 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                <i class="fas fa-download mr-2"></i> 
                <span wire:loading.remove>Export CSV</span>
                <span wire:loading>Exporting...</span>
            </button>
        </div>
    </div>

    <!-- Statistics Chart -->
    <div class="mb-6 bg-themed-secondary p-4 sm:p-6 rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
        <h3 class="text-lg font-bold text-themed-primary mb-4 flex items-center">
            <i class="fas fa-chart-bar mr-2 text-accent-themed-primary"></i>
            Users by Role
        </h3>
        <div class="w-full h-48 sm:h-64">
            <canvas id="roleChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-themed-secondary rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full" aria-label="All Users Table">
                <thead class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary">
                    <tr>
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
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Verified</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Courses</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Completed</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">In Progress</th>
                        <th wire:click="sortBy('last_login_at')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider cursor-pointer hover:opacity-80 transition-opacity duration-200" aria-sort="none">
                            <div class="flex items-center">
                                Last Login 
                                @if ($sortField === 'last_login_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Active</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                    @forelse($users as $user)
                        <tr class="hover:bg-themed-tertiary transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                        @if($user->profile_picture)
                                            {{-- <img src="{{ asset('storage/' . $user->profile_picture) }}"  --}}
                                            
                                            <img src="{{ $user->profile_picture }}"
                                            alt="Avatar" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-accent-themed-primary to-accent-themed-secondary flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-sm font-medium text-themed-primary">{{ $user->name }}</div>
                                </div>
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
                                <i class="fas fa-check-circle {{ $user->email_verified_at ? 'text-green-500' : 'text-red-500' }}"></i>
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
                                <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }}"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1 flex-wrap">
                                    @can('edit-users')
                                        <a href="{{ route('profile.edit', $user->id) }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20 rounded-lg hover:bg-accent-themed-primary/20 transition-colors duration-200 whitespace-nowrap">
                                            <i class="fas fa-edit"></i><span class="hidden sm:inline">Edit</span>
                                        </a>
                                    @endcan
                                    @can('view-user-activity')
                                        <button wire:click="viewUser({{ $user->id }})" class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-themed-tertiary text-themed-primary border border-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors duration-200 whitespace-nowrap">
                                            <i class="fas fa-eye"></i><span class="hidden sm:inline">View</span>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mb-4 transition-colors duration-300">
                                        <i class="fas fa-users text-themed-secondary text-2xl"></i>
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
                <!-- User Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                            @if($user->profile_picture)
                                {{-- <img src="{{ asset('storage/' . $user->profile_picture) }}"  --}}
                                <img src="{{ $user->profile_picture }}"
                                alt="Avatar" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-accent-themed-primary to-accent-themed-secondary flex items-center justify-center text-white font-bold">
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

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <p class="text-xs text-themed-secondary mb-1">Role</p>
                        <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                            {{ ucfirst($user->getRoleNames()->first() ?? 'N/A') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-themed-secondary mb-1">Verified</p>
                        <i class="fas fa-check-circle {{ $user->email_verified_at ? 'text-green-500' : 'text-red-500' }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-themed-secondary">Courses: <span class="text-themed-primary font-bold">{{ $user->enrollments_count }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-themed-secondary">Completed: <span class="text-themed-primary font-bold">{{ $user->completed_enrollments_count }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-themed-secondary">In Progress: <span class="text-themed-primary font-bold">{{ $user->in_progress_enrollments_count }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-themed-secondary">Last Login:</p>
                        <p class="text-xs text-themed-primary font-medium">{{ $user->last_login_at ? $user->last_login_at->format('M d') : 'Never' }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    @can('edit-users')
                        <a href="{{ route('user.edit', $user->id) }}" class="flex-1 text-xs px-3 py-2 bg-accent-themed-primary text-white rounded-lg hover:bg-accent-themed-secondary transition-colors duration-200 font-medium text-center">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    @endcan
                    @can('view-user-activity')
                        <button wire:click="viewUser({{ $user->id }})" class="flex-1 text-xs px-3 py-2 bg-themed-tertiary text-themed-primary border border-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors duration-200 font-medium">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="bg-themed-secondary rounded-xl border border-themed-primary p-8 text-center">
                <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-users text-themed-secondary text-2xl"></i>
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

            const root = document.documentElement;
            const accentPrimary = getComputedStyle(root).getPropertyValue('--accent-primary').trim();
            const textSecondary = getComputedStyle(root).getPropertyValue('--text-secondary').trim();
            const borderPrimary = getComputedStyle(root).getPropertyValue('--border-primary').trim();
            
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
                                color: `rgb(${textSecondary})`,
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: {
                                color: `rgba(${borderPrimary}, 0.1)`
                            }
                        },
                        y: { 
                            beginAtZero: true,
                            ticks: { color: `rgb(${textSecondary})` },
                            grid: {
                                color: `rgba(${borderPrimary}, 0.1)`
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initializeChart);

        if (window.Livewire) {
            Livewire.hook('element.updated', (el) => {
                if (el.querySelector('#roleChart')) {
                    setTimeout(initializeChart, 50);
                }
            });
        }

        new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    setTimeout(initializeChart, 100);
                }
            });
        }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    </script>
</div>