<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white">Profile Management</h1>
        <p class="mt-2 text-gray-400">View and update your profile information</p>
    </div>

    <!-- Tab Navigation -->
    <div class="flex justify-center mb-8">
        <div class="inline-flex rounded-xl bg-gray-800 p-1 shadow-inner">
            <button wire:click="viewProfile"
                class="px-6 py-3 rounded-lg font-semibold transition-all duration-300
                       {{ $currentTab === 'view' ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-user-circle mr-2"></i> View Profile
            </button>
            <button wire:click="editProfile"
                class="px-6 py-3 rounded-lg font-semibold transition-all duration-300
                       {{ $currentTab === 'edit' ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-user-edit mr-2"></i> Edit Profile
            </button>
        </div>
    </div>

    <!-- Content Area -->
    <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        @if ($currentTab === 'view')
            <!-- View Profile Content -->

        @elseif ($currentTab === 'edit')
            <!-- Edit Profile Content -->
            <form wire:submit.prevent="updateProfile" class="p-6 sm:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-700/50 p-6 rounded-xl shadow-inner">
                        <h3 class="text-lg font-medium text-gray-300 mb-4">Basic Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Full Name</label>
                                <input type="text" id="name" wire:model="name"
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                                @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                                <input type="email" id="email" wire:model="email"
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                                @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-700/50 p-6 rounded-xl shadow-inner">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-300">Password Update</h3>
                            <button type="button" wire:click="togglePasswordFields"
                                    class="text-sm font-medium {{ $showPasswordFields ? 'text-purple-400' : 'text-blue-400' }} hover:underline">
                                {{ $showPasswordFields ? 'Cancel' : 'Change Password' }}
                            </button>
                        </div>

                        @if ($showPasswordFields)
                            <div class="space-y-4" x-data x-transition>
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1">Current Password</label>
                                    <input type="password" id="current_password" wire:model="current_password"
                                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                                    @error('current_password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                                    <input type="password" id="password" wire:model="password"
                                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                                    @error('password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                                    <input type="password" id="password_confirmation" wire:model="password_confirmation"
                                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-3 p-4 bg-gray-800/50 rounded-lg">
                                <i class="fas fa-lock text-gray-400 text-xl"></i>
                                <p class="text-gray-300">Password last changed: 
                                    <span class="font-medium">{{ $user->updated_at->diffForHumans() }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                    <button type="button" wire:click="viewProfile"
                            class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-md flex items-center">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>