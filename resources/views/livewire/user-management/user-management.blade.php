<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300 flex items-center">
                    <i class="fas fa-users-cog mr-2 text-blue-600 dark:text-blue-400"></i>
                    User Management
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Manage all users and their roles in the platform</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200 text-sm">
                </div>
                <button wire:click="createUser" type="button"
                    class="flex-shrink-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add User
                </button>
            </div>
        </div>
    </div>

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
            <p class="text-gray-700 dark:text-gray-300 text-sm">Processing users...</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300 overflow-hidden shadow-lg">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-600/50">
            <div class="text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">
                Showing <span class="font-medium">{{ $users->firstItem() }}</span> to <span
                    class="font-medium">{{ $users->lastItem() }}</span> of <span
                    class="font-medium">{{ $users->total() }}</span> users
            </div>
            <div class="flex items-center">
                <label for="perPage" class="mr-2 text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Per page:</label>
                <select wire:model.live="perPage" id="perPage"
                    class="border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600/50">
                <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-blue-600 dark:to-indigo-700 text-white">
                    <tr>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            User
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Role
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Joined
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-700/30 divide-y divide-gray-200 dark:divide-gray-600/50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full overflow-hidden">
                                        @if($user->profile_picture)
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                                alt="Avatar of {{ $user->name }}" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-r from-blue-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if ($user->hasRole('super_admin')) bg-pink-100 dark:bg-pink-500/20 text-pink-800 dark:text-pink-300 border border-pink-200 dark:border-pink-500/30
                                    @elseif ($user->hasRole('academy_admin')) bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30
                                    @elseif ($user->hasRole('instructor')) bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-500/30
                                    @elseif ($user->hasRole('mentor')) bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-500/30
                                    @elseif ($user->hasRole('content_editor')) bg-indigo-100 dark:bg-indigo-500/20 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30
                                    @elseif ($user->hasRole('affiliate_ambassador')) bg-pink-100 dark:bg-pink-500/20 text-pink-800 dark:text-pink-300 border border-pink-200 dark:border-pink-500/30
                                    @else bg-gray-100 dark:bg-gray-500/20 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-500/30 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->email_verified_at)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-500/30">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        Verified
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-500/30">
                                        <i class="fas fa-exclamation-circle mr-1.5"></i>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    @if (!$user->hasVerifiedEmail())
                                        <button wire:click="resendVerificationEmail({{ $user->id }})"
                                            class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300 flex items-center transition-colors duration-200"
                                            title="Resend Verification Email">
                                            <i class="fas fa-paper-plane mr-1"></i>
                                            <span class="hidden sm:inline">Resend</span>
                                        </button>
                                        <button wire:click="markAsVerified({{ $user->id }})"
                                            wire:confirm="Are you sure you want to mark this user's email as verified?"
                                            class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 flex items-center transition-colors duration-200"
                                            title="Mark as Verified">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <span class="hidden sm:inline">Verify</span>
                                        </button>
                                    @endif
                                    <button wire:click="editUser({{ $user->id }})"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 flex items-center transition-colors duration-200"
                                        title="Edit User">
                                        <i class="fas fa-edit mr-1"></i>
                                        <span class="hidden sm:inline">Edit</span>
                                    </button>
                                    <button wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="Are you sure you want to delete this user? This action cannot be undone."
                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 flex items-center transition-colors duration-200"
                                        title="Delete User">
                                        <i class="fas fa-trash-alt mr-1"></i>
                                        <span class="hidden sm:inline">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 transition-colors duration-300">
                                        <i class="fas fa-users-slash text-gray-400 dark:text-gray-500 text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">No users found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300 mt-1">
                                        @if ($search)
                                            No users found matching "{{ $search }}"
                                        @else
                                            Get started by creating your first user.
                                        @endif
                                    </p>
                                    @if ($search)
                                        <div class="mt-3">
                                            <button wire:click="$set('search', '')" type="button"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-200">
                                                Clear search
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
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-600/50 transition-colors duration-300 sm:px-6">
            {{ $users->links() }}
        </div>
    </div>

    <!-- User Modal -->
    <div x-data="{ modalOpen: @entangle('showUserModal'), editMode: @entangle('editMode') }" x-show="modalOpen" x-cloak x-transition:enter="animate__animated animate__fadeIn"
        x-transition:leave="animate__animated animate__fadeOut" class="fixed z-50 inset-0 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true" wire:ignore.self>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="modalOpen" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                aria-hidden="true"></div>

            <!-- Centering trick -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-gray-200 dark:border-gray-600">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white transition-colors duration-300">
                            <i class="fas fa-user-{{ $editMode ? 'edit' : 'plus' }} mr-2 text-blue-600 dark:text-blue-400"></i>
                            {{ $editMode ? 'Edit User' : 'Create New User' }}
                        </h3>
                        <button type="button" @click="modalOpen = false" wire:click="closeModalAndReset"
                            class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 transition-colors duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    @if ($errors->any())
                        <div
                            class="bg-red-50 dark:bg-red-500/20 border-l-4 border-red-400 dark:border-red-500 p-4 mb-4 rounded animate__animated animate__shakeX">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400 dark:text-red-300"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Validation Errors</h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
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
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-300">Name</label>
                                <input wire:model="name" type="text" id="name"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-300">Email</label>
                                <input wire:model="email" type="email" id="email"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-300">Role</label>
                                <select wire:model="role" id="role"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                                    @foreach ($roles as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div x-show="!editMode || password">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-300">Password</label>
                                <input wire:model="password" type="password" id="password"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-show="!editMode || password">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-300">Confirm Password</label>
                                <input wire:model="password_confirmation" type="password" id="password_confirmation"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Send Verification Email -->
                            <div x-show="!editMode">
                                <label class="inline-flex items-center">
                                    <input wire:model="sendVerificationEmail" type="checkbox"
                                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-400 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 transition-colors duration-200">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Send verification email? <i
                                            class="fas fa-envelope ml-1 text-blue-600 dark:text-blue-400"></i></span>
                                </label>
                            </div>

                            <!-- Create Another -->
                            <div x-show="!editMode">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="createAnother"
                                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-400 shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 transition-colors duration-200">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Create another user after this? <i
                                            class="fas fa-plus-circle ml-1 text-blue-600 dark:text-blue-400"></i></span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <!-- Progress bar -->
                            @if ($saveProgress > 0 && $saveProgress < 100)
                                <div class="col-span-2 mb-4">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                        <div class="bg-blue-600 dark:bg-blue-500 h-2.5 rounded-full transition-all duration-300"
                                            style="width: {{ $saveProgress }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Saving... {{ $saveProgress }}%</p>
                                </div>
                            @endif

                            <button type="submit" wire:loading.attr="disabled" wire:target="saveUser"
                                class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 dark:bg-blue-600 text-base font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 sm:col-start-2 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
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
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 sm:mt-0 sm:col-start-1 sm:text-sm transition-colors duration-200">
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