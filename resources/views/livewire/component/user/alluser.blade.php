<div class="p-6 bg-white rounded-lg shadow-md animate__animated animate__fadeIn" x-data="{ tooltip: '' }">
    <!-- Flash Message -->
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
            <select wire:model="roleFilter" class="p-2 border rounded-md w-full" aria-describedby="role-filter-help"
                id="role-filter">
                <option value="">All Roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            <span id="role-filter-help" class="sr-only">Filter users by role</span>
        </div>
        <div class="relative w-full md:w-1/4">
            <input x-data x-init="flatpickr($el, { mode: 'range', dateFormat: 'Y-m-d' })" 
                   wire:model="lastLoginStart" wire:model.debounce.500ms="lastLoginEnd" 
                   placeholder="Last login range..." 
                   class="p-2 border rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   aria-describedby="login-range-help">
            <span id="login-range-help" class="sr-only">Filter users by last login date range</span>
        </div>
        <select wire:model="perPage" class="p-2 border rounded-md w-full md:w-1/6" aria-label="Items per page">
            <option value="10">10 per page</option>
            <option value="20">20 per page</option>
            <option value="50">50 per page</option>
        </select>
        <button wire:click="exportCsv"
            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300"
            aria-label="Export users to CSV">
            <i class="fas fa-download mr-2"></i> Export CSV
        </button>
    </div>
    <div x-data="{ open: false, activity: {} }" x-on:open-user-activity.window="open = true; activity = $event.detail">
        <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                <h2 class="text-lg font-semibold mb-4">User Activity</h2>
                <div x-text="activity.enrollments ? activity.enrollments.map(e => e.course_title + ': ' + e.progress + '%').join('<br>')"></div>
                <p>Certificates: <span x-text="activity.certificates"></span></p>
                <p>Recent Activity: <span x-text="activity.recent_activity.join('<br>')"></span></p>
                <button x-on:click="open = false" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Close</button>
            </div>
        </div>
    </div>

    <!-- Responsive Table -->
    <div class="overflow-x-auto">
        <table wire:poll.10s class="min-w-full divide-y divide-gray-200 table-auto" aria-label="All Users Table">
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                <tr>
                    <th wire:click="sortBy('name')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer"
                        aria-sort="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Name @if ($sortField === 'name')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('email')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hidden md:table-cell"
                        aria-sort="{{ $sortField === 'email' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Email @if ($sortField === 'email')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('role')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer"
                        aria-sort="{{ $sortField === 'role' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Role @if ($sortField === 'role')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('email_verified_at')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer"
                        aria-sort="{{ $sortField === 'email_verified_at' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Verified @if ($sortField === 'email_verified_at')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('enrollments_count')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer"
                        x-on:mouseover="tooltip = 'Total courses registered'" x-on:mouseout="tooltip = ''"
                        aria-sort="{{ $sortField === 'enrollments_count' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Courses @if ($sortField === 'enrollments_count')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('completed_enrollments_count')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hidden md:table-cell"
                        x-on:mouseover="tooltip = 'Courses fully completed'" x-on:mouseout="tooltip = ''"
                        aria-sort="{{ $sortField === 'completed_enrollments_count' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Completed @if ($sortField === 'completed_enrollments_count')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('in_progress_enrollments_count')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hidden md:table-cell"
                        x-on:mouseover="tooltip = 'Courses in progress'" x-on:mouseout="tooltip = ''"
                        aria-sort="{{ $sortField === 'in_progress_enrollments_count' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        In Progress @if ($sortField === 'in_progress_enrollments_count')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('last_login_at')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hidden lg:table-cell"
                        aria-sort="{{ $sortField === 'last_login_at' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Last Login @if ($sortField === 'last_login_at')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('is_active')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer"
                        aria-sort="{{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Active @if ($sortField === 'is_active')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition duration-150 cursor-pointer"
                        wire:click="viewUser({{ $user->id }})">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($user->getRoleNames()->first() ?? 'N/A') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <i class="fas fa-check-circle {{ $user->email_verified_at ? 'text-green-500' : 'text-red-500' }}"
                                aria-label="{{ $user->email_verified_at ? 'Verified' : 'Not verified' }}"></i>
                            <span class="sr-only">{{ $user->email_verified_at ? 'Yes' : 'No' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->enrollments_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            {{ $user->completed_enrollments_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            {{ $user->in_progress_enrollments_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <i class="fas fa-circle {{ $user->is_active ? 'text-green-500' : 'text-red-500' }}"
                                aria-label="{{ $user->is_active ? 'Active' : 'Inactive' }}"></i>
                            <span class="sr-only">{{ $user->is_active ? 'Yes' : 'No' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @can('edit-users')
                                <a href="{{ route('user.edit', $user->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900"
                                    aria-label="Edit user {{ $user->name }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                            @can('view-user-activity')
                                <a href="{{ route('user.activity', $user->id) }}"
                                    class="ml-4 text-gray-600 hover:text-gray-900"
                                    aria-label="View activity for {{ $user->name }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


<!-- Tooltip -->
<div x-show="tooltip" class="fixed bg-gray-800 text-white text-sm px-2 py-1 rounded shadow-lg" x-text="tooltip"
style="z-index: 1000;"></div>

<!-- Pagination -->
<div class="mt-6">
    {{ $users->links('pagination::tailwind') }}
</div>






<div class="mb-6">
    <canvas id="roleChart" class="w-full h-64"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', () => {
            const ctx = document.getElementById('roleChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($roles),
                    datasets: [{
                        label: 'Users by Role',
                        data: @json($this->getRoleStats()->values()),
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
</div>
</div>
