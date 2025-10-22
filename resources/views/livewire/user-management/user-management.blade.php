<div
    class="p-4 sm:p-6 bg-themed-secondary rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl font-bold text-themed-primary transition-colors duration-300 flex items-center">
                    <i class="fas fa-users-cog mr-2 accent-themed-primary"></i>
                    User Management
                </h1>
                <p class="mt-1 text-sm text-themed-secondary transition-colors duration-300">Manage all users
                    and their roles in the platform</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-themed-tertiary"></i>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..."
                        class="block w-full pl-10 pr-3 py-2 border border-themed-primary rounded-xl leading-5 bg-themed-secondary placeholder-themed-tertiary text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-all duration-200 text-sm">
                </div>
                <button wire:click="createUser" type="button"
                    class="flex-shrink-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-accent-themed-primary hover:bg-accent-themed-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-themed-primary transition-all duration-200">
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
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div
            class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading
        class="fixed inset-0 bg-themed-primary bg-opacity-50 flex items-center justify-center z-50 transition-colors duration-300">
        <div
            class="bg-themed-secondary p-6 rounded-xl shadow-2xl border border-themed-primary transition-colors duration-300">
            <i class="fas fa-spinner fa-spin accent-themed-primary text-3xl mb-2 block mx-auto"
                aria-label="Loading"></i>
            <p class="text-themed-primary text-sm">Processing users...</p>
        </div>
    </div>

    <!-- Users Table -->
    <div
        class="bg-themed-secondary rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300 overflow-hidden shadow-lg">
        <div class="flex items-center justify-between px-4 py-3 border-b border-themed-primary">
            <div class="text-sm text-themed-secondary transition-colors duration-300">
                Showing <span class="font-medium">{{ $users->firstItem() }}</span> to <span
                    class="font-medium">{{ $users->lastItem() }}</span> of <span
                    class="font-medium">{{ $users->total() }}</span> users
            </div>
            <select wire:model.live="statusFilter"
                class="border-themed-primary rounded-lg shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary transition-colors duration-200">
                <option value="all">All Users</option>
                <option value="active">Active Only</option>
                <option value="inactive">Inactive Only</option>
            </select>
            <div class="flex items-center">

                <label for="perPage"
                    class="mr-2 text-sm text-themed-secondary transition-colors duration-300">Per
                    page:</label>

                <select wire:model.live="perPage" id="perPage"
                    class="border-themed-primary rounded-lg shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary transition-colors duration-200">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>

            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-themed-primary">
                <thead
                    class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary text-white">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            User
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Role
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Joined
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider">
                            Actions
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
                                                class="w-full h-full bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-themed-primary transition-colors duration-300">
                                            {{ $user->name }}
                                        </div>
                                        <div
                                            class="text-sm text-themed-secondary transition-colors duration-300">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if ($user->hasRole('super_admin')) bg-pink-100 text-pink-800 border border-pink-200
                                            @elseif ($user->hasRole('academy_admin')) bg-blue-100 text-blue-800 border border-blue-200
                                            @elseif ($user->hasRole('instructor')) bg-green-100 text-green-800 border border-green-200
                                            @elseif ($user->hasRole('mentor')) bg-yellow-100 text-yellow-800 border border-yellow-200
                                            @elseif ($user->hasRole('content_editor')) bg-indigo-100 text-indigo-800 border border-indigo-200
                                            @elseif ($user->hasRole('affiliate_ambassador')) bg-pink-100 text-pink-800 border border-pink-200
                                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary transition-colors duration-300">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->email_verified_at)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        Verified
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-exclamation-circle mr-1.5"></i>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->is_active)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check mr-1.5"></i>
                                        Active
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times mr-1.5"></i>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    @if (!$user->hasVerifiedEmail())
                                        <button wire:click="resendVerificationEmail({{ $user->id }})"
                                            class="text-orange-600 hover:text-orange-900 flex items-center transition-colors duration-200"
                                            title="Resend Verification Email">
                                            <i class="fas fa-paper-plane mr-1"></i>
                                            <span class="hidden sm:inline">Resend</span>
                                        </button>
                                        <button wire:click="markAsVerified({{ $user->id }})"
                                            wire:confirm="Are you sure you want to mark this user's email as verified?"
                                            class="text-green-600 hover:text-green-900 flex items-center transition-colors duration-200"
                                            title="Mark as Verified">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <span class="hidden sm:inline">Verify</span>
                                        </button>
                                    @endif

                                    <!-- Activate/Deactivate Buttons -->
                                    @if ($user->is_active && !$user->isSuperAdmin())
                                        <button wire:click="deactivateUser({{ $user->id }})"
                                            wire:confirm="Are you sure you want to deactivate this user? They will not be able to log in."
                                            class="text-red-600 hover:text-red-900 flex items-center transition-colors duration-200"
                                            title="Deactivate User">
                                            <i class="fas fa-user-slash mr-1"></i>
                                            <span class="hidden sm:inline">Deactivate</span>
                                        </button>
                                    @elseif (!$user->is_active)
                                        <button wire:click="activateUser({{ $user->id }})"
                                            class="text-emerald-600 hover:text-emerald-900 flex items-center transition-colors duration-200"
                                            title="Activate User">
                                            <i class="fas fa-user-check mr-1"></i>
                                            <span class="hidden sm:inline">Activate</span>
                                        </button>
                                    @endif

                                    <button wire:click="editUser({{ $user->id }})"
                                        class="accent-themed-primary hover:text-accent-themed-secondary flex items-center transition-colors duration-200"
                                        title="Edit User">
                                        <i class="fas fa-edit mr-1"></i>
                                        <span class="hidden sm:inline">Edit</span>
                                    </button>
                                    <button wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="Are you sure you want to delete this user? This action cannot be undone."
                                        class="text-red-600 hover:text-red-900 flex items-center transition-colors duration-200"
                                        title="Delete User">
                                        <i class="fas fa-trash-alt mr-1"></i>
                                        <span class="hidden sm:inline">Delete</span>
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
                                    <h3
                                        class="text-sm font-medium text-themed-primary transition-colors duration-300">
                                        No users found</h3>
                                    <p class="text-sm text-themed-secondary transition-colors duration-300 mt-1">
                                        @if ($search || $statusFilter !== 'all')
                                            No users found
                                            @if ($search) matching "{{ $search }}"@endif
                                            @if ($statusFilter !== 'all') with status "{{ $statusFilter }}"@endif
                                        @else
                                            Get started by creating your first user.
                                        @endif
                                    </p>
                                    @if ($search || $statusFilter !== 'all')
                                        <div class="mt-3">
                                            <button wire:click="$set('search', '')" type="button"
                                                class="inline-flex items-center px-3 py-2 border border-themed-primary shadow-sm text-sm leading-4 font-medium rounded-lg text-themed-primary bg-themed-secondary hover:bg-themed-tertiary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-themed-primary transition-colors duration-200">
                                                Clear filters
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            class="px-4 py-3 bg-themed-tertiary border-t border-themed-primary transition-colors duration-300 sm:px-6">
            {{ $users->links() }}
        </div>
    </div>

    <!-- User Modal -->
    <div x-data="{ modalOpen: @entangle('showUserModal'), editMode: @entangle('editMode') }" x-show="modalOpen" x-cloak
        x-transition:enter="animate__animated animate__fadeIn" x-transition:leave="animate__animated animate__fadeOut"
        class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
        wire:ignore.self>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="modalOpen"
                class="fixed inset-0 bg-themed-primary bg-opacity-75 transition-opacity"
                aria-hidden="true"></div>

            <!-- Centering trick -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-themed-secondary rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-themed-primary">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3
                            class="text-lg leading-6 font-medium text-themed-primary transition-colors duration-300">
                            <i
                                class="fas fa-user-{{ $editMode ? 'edit' : 'plus' }} mr-2 accent-themed-primary"></i>
                            {{ $editMode ? 'Edit User' : 'Create New User' }}
                        </h3>
                        <button type="button" @click="modalOpen = false" wire:click="closeModalAndReset"
                            class="text-themed-tertiary hover:text-themed-secondary transition-colors duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    @if ($errors->any())
                        <div
                            class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 rounded animate__animated animate__shakeX">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Validation Errors</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="saveUser">
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-themed-primary transition-colors duration-300">Name</label>
                                <input wire:model="name" type="text" id="name"
                                    class="mt-1 block w-full border-themed-primary rounded-lg shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary transition-colors duration-200">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-themed-primary transition-colors duration-300">Email</label>
                                <input wire:model="email" type="email" id="email"
                                    class="mt-1 block w-full border-themed-primary rounded-lg shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary transition-colors duration-200">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role"
                                    class="block text-sm font-medium text-themed-primary transition-colors duration-300">Role</label>
                                <select wire:model="role" id="role"
                                    class="mt-1 block w-full border-themed-primary rounded-lg shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary transition-colors duration-200">
                                    @foreach ($roles as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div x-show="!editMode || password">
                                <label for="password"
                                    class="block text-sm font-medium text-themed-primary transition-colors duration-300">Password</label>
                                <input wire:model="password" type="password" id="password"
                                    class="mt-1 block w-full border-themed-primary rounded-lg shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary transition-colors duration-200">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-show="!editMode || password">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-themed-primary transition-colors duration-300">Confirm
                                    Password</label>
                                <input wire:model="password_confirmation" type="password" id="password_confirmation"
                                    class="mt-1 block w-full border-themed-primary rounded-lg shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary transition-colors duration-200">
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Send Verification Email -->
                            <div x-show="!editMode">
                                <label class="inline-flex items-center">
                                    <input wire:model="sendVerificationEmail" type="checkbox"
                                        class="rounded border-themed-primary text-accent-themed-primary shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary transition-colors duration-200">
                                    <span
                                        class="ml-2 text-sm text-themed-secondary transition-colors duration-300">Send
                                        verification email? <i
                                            class="fas fa-envelope ml-1 accent-themed-primary"></i></span>
                                </label>
                            </div>

                            <!-- Create Another -->
                            <div x-show="!editMode">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="createAnother"
                                        class="rounded border-themed-primary text-accent-themed-primary shadow-sm focus:border-accent-themed-primary focus:ring-accent-themed-primary bg-themed-secondary transition-colors duration-200">
                                    <span
                                        class="ml-2 text-sm text-themed-secondary transition-colors duration-300">Create
                                        another user after this? <i
                                            class="fas fa-plus-circle ml-1 accent-themed-primary"></i></span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <!-- Progress bar -->
                            @if ($saveProgress > 0 && $saveProgress < 100)
                                <div class="col-span-2 mb-4">
                                    <div class="w-full bg-themed-tertiary rounded-full h-2.5">
                                        <div class="bg-accent-themed-primary h-2.5 rounded-full transition-all duration-300"
                                            style="width: {{ $saveProgress }}%"></div>
                                    </div>
                                    <p class="text-sm text-themed-secondary mt-1">Saving... {{ $saveProgress }}%
                                    </p>
                                </div>
                            @endif

                            <button type="submit" wire:loading.attr="disabled" wire:target="saveUser"
                                class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-accent-themed-primary text-base font-medium text-white hover:bg-accent-themed-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-themed-primary sm:col-start-2 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                <span wire:loading.remove wire:target="saveUser">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ $editMode ? 'Update User' : 'Create User' }}
                                </span>
                                <span wire:loading wire:target="saveUser">
                                    <i class="fas fa-circle-notch fa-spin mr-2"></i>
                                    Processing...
                                </span>
                            </button>

                            <button type="button" @click="modalOpen = false" wire:click="closeModalAndReset"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-themed-primary shadow-sm px-4 py-2 bg-themed-secondary text-base font-medium text-themed-primary hover:bg-themed-tertiary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-themed-primary sm:mt-0 sm:col-start-1 sm:text-sm transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>