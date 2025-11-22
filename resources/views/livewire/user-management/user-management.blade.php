<div
    class="p-4 sm:p-6 bg-themed-secondary rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary transition-colors duration-300">
                    <i class="fas fa-users-cog mr-2"></i>
                    User Management
                </h1>
                <p class="mt-1 text-sm sm:text-base text-themed-secondary transition-colors duration-300">Manage all
                    users and their roles</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:flex-initial">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-themed-secondary"></i>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..."
                        class="w-full pl-10 pr-3 py-2 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary placeholder-themed-tertiary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary transition-all duration-200 text-sm">
                </div>
                <button wire:click="createUser" type="button"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-accent-themed-primary hover:bg-accent-themed-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-themed-primary transition-all duration-200 whitespace-nowrap">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add User
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div
            class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
                <span class="text-sm sm:text-base">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div
            class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
                <span class="text-sm sm:text-base">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading
        class="fixed inset-0 bg-themed-primary bg-opacity-50 flex items-center justify-center z-50 transition-colors duration-300">
        <div
            class="bg-themed-secondary p-6 rounded-xl shadow-2xl border border-themed-primary transition-colors duration-300">
            <i class="fas fa-spinner fa-spin text-accent-themed-primary text-3xl mb-2 block mx-auto"></i>
            <p class="text-themed-primary text-sm">Processing users...</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Status Filter -->
        <div>
            <label for="statusFilter"
                class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Status</label>
            <div class="relative">
                <select wire:model.live="statusFilter" id="statusFilter"
                    class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="all">All Users</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Per Page -->
        <div>
            <label for="perPage"
                class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Per
                Page</label>
            <div class="relative">
                <select wire:model.live="perPage" id="perPage"
                    class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table - Desktop View -->
    <div
        class="hidden lg:block bg-themed-secondary rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-themed-primary">
                <thead class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Joined
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Verified
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                    @forelse ($users as $user)
                        <tr class="hover:bg-themed-tertiary transition-colors duration-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full overflow-hidden">
                                        @if($user->profile_picture)
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                                alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover"
                                                loading="lazy">
                                        @else
                                            <div
                                                class="w-full h-full bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-themed-primary transition-colors duration-300">
                                            {{ $user->name }}</div>
                                        <div class="text-sm text-themed-secondary transition-colors duration-300">
                                            {{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary transition-colors duration-300">
                                <i class="far fa-calendar-alt mr-1"></i>{{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->email_verified_at)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1.5"></i>Verified
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-exclamation-circle mr-1.5"></i>Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->is_active)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check mr-1.5"></i>Active
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times mr-1.5"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1 flex-wrap">
                                    @if (!$user->hasVerifiedEmail())
                                        <button wire:click="resendVerificationEmail({{ $user->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20 rounded-lg hover:bg-accent-themed-primary/20 transition-colors duration-200 whitespace-nowrap">
                                            <i class="fas fa-paper-plane"></i><span class="hidden sm:inline">Resend</span>
                                        </button>
                                        <button wire:click="markAsVerified({{ $user->id }})" wire:confirm="Mark as verified?"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-green-100 text-green-700 border border-green-200 rounded-lg hover:bg-green-200 transition-colors duration-200 whitespace-nowrap">
                                            <i class="fas fa-check-circle"></i><span class="hidden sm:inline">Verify</span>
                                        </button>
                                    @endif
                                    @if ($user->is_active && !$user->isSuperAdmin())
                                        <button wire:click="deactivateUser({{ $user->id }})" wire:confirm="Deactivate user?"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-red-100 text-red-700 border border-red-200 rounded-lg hover:bg-red-200 transition-colors duration-200 whitespace-nowrap">
                                            <i class="fas fa-user-slash"></i><span class="hidden sm:inline">Deactivate</span>
                                        </button>
                                    @elseif (!$user->is_active)
                                        <button wire:click="activateUser({{ $user->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-200 transition-colors duration-200 whitespace-nowrap">
                                            <i class="fas fa-user-check"></i><span class="hidden sm:inline">Activate</span>
                                        </button>
                                    @endif
                                    <button wire:click="editUser({{ $user->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-accent-themed-primary text-white rounded-lg hover:bg-accent-themed-secondary transition-colors duration-200 whitespace-nowrap">
                                        <i class="fas fa-edit"></i><span class="hidden sm:inline">Edit</span>
                                    </button>
                                    <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Delete user?"
                                        class="inline-flex items-center gap-1 px-3 py-2 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 whitespace-nowrap">
                                        <i class="fas fa-trash-alt"></i><span class="hidden sm:inline">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mb-4 transition-colors duration-300">
                                        <i class="fas fa-users-slash text-themed-secondary text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-themed-primary transition-colors duration-300">No
                                        users found</h3>
                                    <p class="text-sm text-themed-secondary transition-colors duration-300 mt-1">Try
                                        adjusting your search</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Users Cards - Mobile View -->
    <div class="lg:hidden grid grid-cols-1 gap-4">
        @forelse ($users as $user)
            <div
                class="bg-themed-secondary rounded-xl border border-themed-primary p-4 shadow-md hover:shadow-lg transition-shadow duration-300">
                <!-- User Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full overflow-hidden">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Avatar of {{ $user->name }}"
                                    class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-themed-primary font-semibold text-sm truncate">{{ $user->name }}</h4>
                            <p class="text-themed-secondary text-xs truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <p class="text-xs text-themed-secondary mb-1">Role</p>
                        <span
                            class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-themed-secondary mb-1">Joined</p>
                        <p class="text-xs text-themed-primary font-medium">{{ $user->created_at->format('M d') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-themed-secondary mb-1">Verified</p>
                        @if ($user->email_verified_at)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check text-xs mr-1"></i>Yes
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-exclamation text-xs mr-1"></i>No
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-themed-secondary mb-1">Status</p>
                        @if ($user->is_active)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i class="fas fa-check text-xs mr-1"></i>Active
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times text-xs mr-1"></i>Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2">
                    @if (!$user->hasVerifiedEmail())
                        <button wire:click="resendVerificationEmail({{ $user->id }})"
                            class="flex-1 text-xs px-2 py-2 bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20 rounded-lg hover:bg-accent-themed-primary/20 transition-colors duration-200">
                            <i class="fas fa-paper-plane mr-1"></i>Resend
                        </button>
                        <button wire:click="markAsVerified({{ $user->id }})" wire:confirm="Mark as verified?"
                            class="flex-1 text-xs px-2 py-2 bg-green-100 text-green-700 border border-green-200 rounded-lg hover:bg-green-200 transition-colors duration-200">
                            <i class="fas fa-check mr-1"></i>Verify
                        </button>
                    @endif
                    @if ($user->is_active && !$user->isSuperAdmin())
                        <button wire:click="deactivateUser({{ $user->id }})" wire:confirm="Deactivate user?"
                            class="flex-1 text-xs px-2 py-2 bg-red-100 text-red-700 border border-red-200 rounded-lg hover:bg-red-200 transition-colors duration-200">
                            <i class="fas fa-user-slash mr-1"></i>Deactivate
                        </button>
                    @elseif (!$user->is_active)
                        <button wire:click="activateUser({{ $user->id }})"
                            class="flex-1 text-xs px-2 py-2 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-200 transition-colors duration-200">
                            <i class="fas fa-user-check mr-1"></i>Activate
                        </button>
                    @endif
                </div>
                <div class="flex gap-2 mt-2">
                    <button wire:click="editUser({{ $user->id }})"
                        class="flex-1 text-xs px-2 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Delete user?"
                        class="flex-1 text-xs px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-trash-alt mr-1"></i>Delete
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-themed-secondary rounded-xl border border-themed-primary p-8 text-center">
                <div
                    class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-users-slash text-themed-secondary text-2xl"></i>
                </div>
                <h3 class="text-sm font-medium text-themed-primary transition-colors duration-300">No users found</h3>
                <p class="text-sm text-themed-secondary transition-colors duration-300 mt-1">Try adjusting your search</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div
        class="mt-6 bg-themed-secondary p-4 rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
        {{ $users->links('pagination::tailwind') }}
    </div>

    <!-- User Modal - Replace your existing modal section -->
    <div x-data="{ modalOpen: @entangle('showUserModal'), editMode: @entangle('editMode'), generatedPassword: '', showPassword: false }"
        x-show="modalOpen" x-cloak x-transition:enter="animate__animated animate__fadeIn"
        x-transition:leave="animate__animated animate__fadeOut"
        @password-generated.window="generatedPassword = $event.detail; showPassword = true"
        class="fixed z-50 inset-0 overflow-y-auto p-4 sm:p-0" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">

        <div class="flex items-end sm:items-center justify-center min-h-screen">
            <!-- Background overlay -->
            <div x-show="modalOpen" class="fixed inset-0 bg-themed-primary bg-opacity-75 transition-opacity"></div>

            <!-- Modal panel -->
            <div x-show="modalOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="relative bg-themed-secondary rounded-t-2xl sm:rounded-2xl w-full sm:max-w-2xl p-4 sm:p-6 shadow-xl border border-themed-primary max-h-[90vh] overflow-y-auto">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg sm:text-xl font-bold text-themed-primary">
                        <i class="fas fa-user-{{ $editMode ? 'edit' : 'plus' }} mr-2"></i>
                        {{ $editMode ? 'Edit User' : 'Create New User' }}
                    </h3>
                    <button type="button" @click="modalOpen = false" wire:click="closeModalAndReset"
                        class="text-themed-secondary hover:text-themed-primary transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 rounded text-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-red-800">Validation Errors</h3>
                                <ul class="mt-2 list-disc pl-5 space-y-1 text-red-700 text-xs">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Generated Password Display -->
                <div x-show="showPassword && generatedPassword" x-transition
                    class="bg-green-50 border-l-4 border-green-400 p-4 mb-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-key text-green-400"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="font-medium text-green-800">Generated Password</h3>
                            <div class="mt-2 flex items-center gap-2">
                                <code class="bg-green-100 px-3 py-2 rounded text-green-900 font-mono text-sm"
                                    x-text="generatedPassword"></code>
                                <button
                                    @click="navigator.clipboard.writeText(generatedPassword); $dispatch('notify', 'Password copied!')"
                                    class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                            <p class="text-xs text-green-700 mt-2">
                                <i class="fas fa-info-circle"></i> Make sure to copy this password - it won't be shown
                                again!
                            </p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="saveUser" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-themed-primary mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" id="name"
                            class="w-full p-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary transition-all duration-200 text-sm">
                        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-themed-primary mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="email" type="email" id="email"
                            class="w-full p-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary transition-all duration-200 text-sm">
                        @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-themed-primary mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select wire:model="role" id="role"
                                class="w-full p-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary transition-all duration-200 text-sm appearance-none">
                                <option value="">Select a role</option>
                                @foreach ($roles as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                            </div>
                        </div>
                        @error('role') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password Section -->
                    <div x-show="!editMode || !$wire.autoGeneratePassword">
                        <!-- Auto Generate Password Checkbox (Create mode only) -->
                        <div x-show="!editMode" class="mb-3">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input wire:model.live="autoGeneratePassword" type="checkbox"
                                    class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary transition-all duration-200">
                                <span class="text-sm text-themed-secondary">
                                    <i class="fas fa-random mr-1"></i>
                                    Auto-generate secure password
                                </span>
                            </label>
                        </div>

                        <!-- Manual Password Fields -->
                        <div x-show="!$wire.autoGeneratePassword || editMode">
                            <div>
                                <label for="password" class="block text-sm font-medium text-themed-primary mb-2">
                                    Password <span class="text-red-500" x-show="!editMode">*</span>
                                    <span class="text-xs text-themed-secondary" x-show="editMode">(Leave blank to keep
                                        current)</span>
                                </label>
                                <div class="relative">
                                    <input wire:model="password" type="password" id="password"
                                        class="w-full p-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary transition-all duration-200 text-sm">
                                    <button type="button" @click="$wire.generateRandomPassword()"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1 bg-accent-themed-primary text-white rounded text-xs hover:bg-accent-themed-secondary">
                                        <i class="fas fa-random"></i>
                                    </button>
                                </div>
                                @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mt-3">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-themed-primary mb-2">
                                    Confirm Password <span class="text-red-500" x-show="!editMode">*</span>
                                </label>
                                <input wire:model="password_confirmation" type="password" id="password_confirmation"
                                    class="w-full p-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary transition-all duration-200 text-sm">
                                @error('password_confirmation') <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Email Verification Options (Create mode only) -->
                    <div x-show="!editMode"
                        class="space-y-3 bg-themed-tertiary p-4 rounded-lg border border-themed-primary">
                        <h4 class="text-sm font-semibold text-themed-primary flex items-center">
                            <i class="fas fa-envelope-open-text mr-2"></i>
                            Email & Notifications
                        </h4>

                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="markAsVerified"
                                class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary transition-all duration-200">
                            <span class="text-sm text-themed-secondary">
                                <i class="fas fa-check-circle mr-1"></i>
                                Mark email as verified (Recommended)
                            </span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="sendWelcomeEmail"
                                class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary transition-all duration-200">
                            <span class="text-sm text-themed-secondary">
                                <i class="fas fa-paper-plane mr-1"></i>
                                Send welcome email with login credentials
                            </span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="sendVerificationEmail"
                                class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary transition-all duration-200">
                            <span class="text-sm text-themed-secondary">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Send email verification link
                            </span>
                        </label>

                        <label x-show="!editMode" class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="createAnother"
                                class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary transition-all duration-200">
                            <span class="text-sm text-themed-secondary">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Create another user after saving
                            </span>
                        </label>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs text-blue-700 dark:text-blue-300">
                                    <strong>Note:</strong> Users created by admins are automatically activated and can
                                    login immediately.
                                    <span x-show="$wire.markAsVerified">Their email will be pre-verified.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-4 border-t border-themed-primary">
                        <button type="button" @click="modalOpen = false" wire:click="closeModalAndReset"
                            class="flex-1 px-4 py-2 bg-themed-tertiary text-themed-primary rounded-lg hover:bg-themed-primary/20 font-medium transition-colors duration-200 text-sm">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveUser"
                            class="flex-1 px-4 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-lg font-medium transition-colors duration-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="saveUser">
                                <i class="fas fa-save mr-2"></i>{{ $editMode ? 'Update User' : 'Create User' }}
                            </span>
                            <span wire:loading wire:target="saveUser">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</div>