<div>
    <!-- Main Content -->
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-users-cog mr-2 text-blue-600"></i>
                        User Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Manage all users and their roles in the platform</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <button wire:click="createUser" type="button"
                        class="flex-shrink-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add User
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">{{ $users->firstItem() }}</span> to <span
                        class="font-medium">{{ $users->lastItem() }}</span> of <span
                        class="font-medium">{{ $users->total() }}</span> users
                </div>
                <div class="flex items-center">
                    <label for="perPage" class="mr-2 text-sm text-gray-600">Per page:</label>
                    <select wire:model.live="perPage" id="perPage"
                        class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Joined
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if ($user->isSuperAdmin()) bg-purple-100 text-purple-800
                                        @elseif($user->isAcademyAdmin()) bg-blue-100 text-blue-800
                                        @elseif($user->isInstructor()) bg-green-100 text-green-800
                                        @elseif($user->isMentor()) bg-yellow-100 text-yellow-800
                                        @elseif($user->isContentEditor()) bg-indigo-100 text-indigo-800
                                        @elseif($user->isAffiliateAmbassador()) bg-pink-100 text-pink-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->email_verified_at)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1.5"></i>
                                            Verified
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-circle mr-1.5"></i>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
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
                                        <button wire:click="editUser({{ $user->id }})"
                                            class="text-blue-600 hover:text-blue-900 flex items-center transition-colors duration-200"
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-users-slash text-4xl text-gray-400 mb-4"></i>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if ($search)
                                                No users found matching "{{ $search }}"
                                            @else
                                                Get started by creating your first user.
                                            @endif
                                        </p>
                                        @if ($search)
                                            <div class="mt-3">
                                                <button wire:click="$set('search', '')" type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- User Modal -->
    @if ($showUserModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-transition.opacity
            @keydown.escape.window="show = false; $wire.closeModalAndReset()">

            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="show = false; $wire.closeModalAndReset()"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative"
                    @click.stop>

                    <!-- Loading overlay for the modal content -->
                    <div wire:loading.delay class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg z-50">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-spinner fa-spin text-blue-600 text-4xl mb-3"></i>
                            <p class="text-blue-700 text-lg font-semibold">Processing...</p>
                        </div>
                    </div>

                    <!-- Close button -->
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModalAndReset"
                            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times h-6 w-6"></i>
                        </button>
                    </div>

                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            @if ($editMode)
                                <i class="fas fa-user-edit text-blue-600"></i>
                            @else
                                <i class="fas fa-user-plus text-blue-600"></i>
                            @endif
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $editMode ? 'Edit User' : 'Create New User' }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    {{ $editMode ? 'Update the user information below.' : 'Fill in the information below to create a new user.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveUser" class="mt-6 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-user mr-1 text-gray-500"></i> Full Name *
                            </label>
                            <input type="text" id="name" wire:model="name" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter full name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-envelope mr-1 text-gray-500"></i> Email Address *
                            </label>
                            <input type="email" id="email" wire:model="email" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter email address">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-user-tag mr-1 text-gray-500"></i> Role *
                            </label>
                            <select id="role" wire:model="role" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Select a role</option>
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        @if (!$editMode)
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-lock mr-1 text-gray-500"></i> Password *
                                </label>
                                <input type="password" id="password" wire:model="password" required
                                    autocomplete="new-password"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Enter password">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-lock mr-1 text-gray-500"></i> Confirm Password *
                                </label>
                                <input type="password" id="password_confirmation" wire:model="password_confirmation"
                                    required autocomplete="new-password"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Confirm password">
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @else
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Leave password fields empty to keep the current password unchanged.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Email Verification Option -->
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="sendVerificationEmail" type="checkbox"
                                        wire:model="sendVerificationEmail"
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="sendVerificationEmail" class="font-medium text-blue-900">
                                        <i class="fas fa-envelope mr-1"></i>
                                        @if ($editMode)
                                            Send email verification (only if email changed)
                                        @else
                                            Send email verification to new user
                                        @endif
                                    </label>
                                    <p class="text-blue-700 mt-1">
                                        @if ($editMode)
                                            If the email address is changed, checking this will reset email verification
                                            and send a new verification email.
                                        @else
                                            The user will receive an email to verify their email address. Uncheck to
                                            mark as verified immediately.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if ($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        @foreach ($errors->all() as $error)
                                            <p class="text-sm text-red-700">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <!-- Progress bar - only show when loading -->
                            @if($saveProgress > 0 && $saveProgress < 100)
                                <div class="col-span-2 mb-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                             style="width: {{ $saveProgress }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">Saving... {{ $saveProgress }}%</p>
                                </div>
                            @endif
                            
                            <button type="submit" wire:loading.attr="disabled" wire:target="saveUser"
                                class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="saveUser">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ $editMode ? 'Update User' : 'Create User' }}
                                </span>
                                <span wire:loading wire:target="saveUser">
                                    <i class="fas fa-circle-notch fa-spin mr-2"></i>
                                    Processing...
                                </span>
                            </button>
                            
                            <button type="button" wire:click="closeModalAndReset"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
