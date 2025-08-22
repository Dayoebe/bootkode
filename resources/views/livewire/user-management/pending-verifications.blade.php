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
                   wire:model="createdAtStart" wire:model.debounce.500ms="createdAtEnd" 
                   placeholder="Registration date range..." 
                   class="p-2 border rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   aria-describedby="created-at-range-help">
            <span id="created-at-range-help" class="sr-only">Filter users by registration date range</span>
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
        <button wire:click="export"
            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300"
            aria-label="Export users">
            <i class="fas fa-download mr-2"></i> Export
        </button>
    </div>
    <button wire:click="bulkSendReminders"
        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300 disabled:opacity-50"
        x-bind:disabled="!selectedUsers.length" aria-label="Send verification reminders to selected users">
        <i class="fas fa-envelope mr-2"></i> Send Reminders
    </button>
    <!-- Bulk Actions -->
    <div class="mb-4 flex space-x-4">
        <button wire:click="bulkVerify"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300 disabled:opacity-50"
            x-bind:disabled="!selectedUsers.length" aria-label="Verify selected users">
            <i class="fas fa-check-circle mr-2"></i> Verify Selected
        </button>
    </div>

    <!-- Responsive Table -->
    <div class="overflow-x-auto">
        <table wire:poll.10s class="min-w-full divide-y divide-gray-200 table-auto"
            aria-label="Pending Verifications Table">
            <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        <input type="checkbox" x-model="selectedUsers" x-bind:value="[]"
                            x-on:change="selectedUsers = selectedUsers.length ? [] : @json($users->pluck('id')->toArray())"
                            aria-label="Select all users">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Avatar</th>
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
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Roles</th>
                    <th wire:click="sortBy('created_at')"
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hidden md:table-cell"
                        aria-sort="{{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                        Registered @if ($sortField === 'created_at')
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
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition duration-150 animate__animated animate__fadeIn">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" x-model="selectedUsers" value="{{ $user->id }}"
                                aria-label="Select user {{ $user->name }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="{{ $user->profile_picture ?? asset('images/default-avatar.png') }}"
                                alt="Avatar of {{ $user->name }}" class="w-8 h-8 rounded-full object-cover"
                                loading="lazy">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @foreach ($user->getRoleNames() as $role)
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $role === 'super_admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}"
                                    x-on:mouseover="tooltip = 'Role: {{ ucfirst($role) }}'"
                                    x-on:mouseout="tooltip = ''">{{ ucfirst($role) }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            {{ $user->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="verifyUser({{ $user->id }})"
                                class="text-green-600 hover:text-green-900"
                                aria-label="Verify user {{ $user->name }}">
                                <i class="fas fa-check-circle"></i>
                            </button>
                            <button wire:click="sendVerificationReminder({{ $user->id }})"
                                class="ml-4 text-blue-600 hover:text-blue-900"
                                aria-label="Send verification reminder to {{ $user->name }}">
                                <i class="fas fa-envelope"></i>
                            </button>
                            <button wire:click="openUserDetailsModal({{ $user->id }})"
                                class="ml-4 text-gray-600 hover:text-gray-900"
                                aria-label="View details for {{ $user->name }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No unverified users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- User Details Modal -->
    <div x-data="{ open: false, details: {} }" x-on:open-user-details-modal.window="open = true; details = $event.detail"
        x-on:close-user-details-modal.window="open = false" x-show="open"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 animate__animated animate__fadeIn"
        role="dialog" aria-modal="true" aria-labelledby="user-details-modal-title" tabindex="-1">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg overflow-y-auto max-h-[80vh]">
            <h2 id="user-details-modal-title" class="text-lg font-semibold mb-4">User Details</h2>
            <div class="space-y-4">
                <p><strong>Name:</strong> <span x-text="details.name"></span></p>
                <p><strong>Email:</strong> <span x-text="details.email"></span></p>
                <p><strong>Roles:</strong> <span x-text="details.roles.join(', ')"></span></p>
                <div>
                    <strong>Enrollments:</strong>
                    <ul class="list-disc pl-5">
                        <template x-for="enrollment in details.enrollments" :key="enrollment.course_title">
                            <li
                                x-text="enrollment.course_title + ': ' + enrollment.progress_percentage + '% (' + (enrollment.is_completed ? 'Completed' : 'In Progress') + ')'">
                            </li>
                        </template>
                    </ul>
                </div>
                <div>
                    <strong>Recent Activity:</strong>
                    <ul class="list-disc pl-5">
                        <template x-for="log in details.activity" :key="log.created_at">
                            <p x-text="log.description + ' (' + log.created_at + ')'" class="text-sm text-gray-600">
                            </p>
                        </template>
                    </ul>
                </div>
            </div>
            <button x-on:click="open = false"
                class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                aria-label="Close">Close</button>
        </div>
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
                            label: 'Unverified Users by Role',
                            data: @json($this->getRoleStats()->values()),
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    </div>
</div>
