<div class="bg-gray-800 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Edit Profile Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Profile</h1>
            <p class="text-gray-400 mt-2">Update your personal information and security settings</p>
        </div>
        <a href="{{ route('profile.view') }}" 
           class="mt-4 md:mt-0 px-6 py-3 border border-gray-600 text-gray-300 rounded-xl hover:bg-gray-700 transition-colors duration-200 font-medium flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Profile
        </a>
    </div>

    <!-- Main Form -->
    <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Personal Information Section -->
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-user-edit text-blue-400 mr-3"></i> Personal Information
            </h2>
        </div>
        
        <form wire:submit.prevent="updateProfile" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                    <div class="relative">
                        <input type="text" id="name" wire:model="name"
                               class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    </div>
                    @error('name') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" id="email" wire:model="email"
                               class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                    </div>
                    @error('email') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Password Update Section -->
            <div class="pt-6 mt-6 border-t border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-white flex items-center">
                        <i class="fas fa-lock text-yellow-400 mr-3"></i> Password Update
                    </h3>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:underline">Forgot Password?</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-300 mb-2">Current Password</label>
                        <div class="relative">
                            <input type="password" id="current_password" wire:model="current_password"
                                   class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" id="new_password" wire:model="password"
                                   class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" wire:model="password_confirmation"
                                   class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-check-circle text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-end">
                        <div class="w-full">
                            <div class="flex items-center mt-1">
                                <div class="w-1/4">
                                    <div class="h-1 rounded-full bg-red-500"></div>
                                </div>
                                <div class="w-1/4">
                                    <div class="h-1 rounded-full bg-yellow-500"></div>
                                </div>
                                <div class="w-1/4">
                                    <div class="h-1 rounded-full bg-blue-500"></div>
                                </div>
                                <div class="w-1/4">
                                    <div class="h-1 rounded-full bg-green-500"></div>
                                </div>
                                <span class="ml-2 text-xs text-gray-400">Password strength</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-700">
                <a href="{{ route('profile.view') }}"
                   class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200 font-medium">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-md flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="mt-8 bg-gradient-to-r from-red-900/30 to-transparent border border-red-800/50 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-red-800/50">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i> Danger Zone
            </h2>
        </div>
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-white">Delete Account</h3>
                    <p class="text-sm text-gray-400 mt-1">Once you delete your account, there is no going back. Please be certain.</p>
                </div>
                <button class="mt-4 md:mt-0 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i> Delete Account
                </button>
            </div>
        </div>
    </div>
</div>