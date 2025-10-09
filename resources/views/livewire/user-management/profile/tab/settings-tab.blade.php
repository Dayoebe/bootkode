<!-- Settings Tab -->
<div x-show="activeTab === 'settings'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
    <div class="flex items-center mb-6 sm:mb-8">
        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-gray-500 to-gray-600 dark:from-gray-600 dark:to-gray-700 rounded-xl flex items-center justify-center mr-3 sm:mr-4 shadow-lg">
            <i class="fas fa-cog text-white text-lg sm:text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Account Settings</h2>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300">Manage your account preferences and security</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Account Security -->
        <div class="bg-white dark:bg-gray-800/50 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-700/50 backdrop-blur-sm transition-colors duration-300">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center transition-colors duration-300">
                <i class="fas fa-shield-alt text-green-500 dark:text-green-400 mr-2"></i>
                Account Security
            </h3>

            <div class="space-y-3 sm:space-y-4">
                <!-- Two-Factor Authentication -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Two-Factor Authentication</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">Add extra security to your account</p>
                    </div>
                    <button wire:click="toggle2FA" 
                            class="px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        {{ $user->two_factor_secret ? 'Disable' : 'Enable' }}
                    </button>
                </div>

                <!-- Change Password -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Change Password</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">Update your account password</p>
                    </div>
                    <button wire:click="$set('showPasswordModal', true)" 
                            class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        Change
                    </button>
                </div>

                <!-- Active Sessions -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Active Sessions</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">
                            {{ $activeSessions }} active {{ Str::plural('session', $activeSessions) }}
                        </p>
                    </div>
                    <button wire:click="$set('showSessionsModal', true)" 
                            class="px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        View
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications & Privacy -->
        <div class="bg-white dark:bg-gray-800/50 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-700/50 backdrop-blur-sm transition-colors duration-300">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center transition-colors duration-300">
                <i class="fas fa-bell text-purple-500 dark:text-purple-400 mr-2"></i>
                Notifications & Privacy
            </h3>

            <div class="space-y-3 sm:space-y-4">
                <!-- Email Notifications -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Email Notifications</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">Course updates and announcements</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               wire:model.live="emailNotifications" 
                               wire:change="updateEmailNotifications"
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Certificate Notifications -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Certificate Notifications</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">Certificate approval updates</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               wire:model.live="certificateNotifications" 
                               wire:change="updateCertificateNotifications"
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Profile Visibility -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Profile Visibility</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">Who can see your profile</p>
                    </div>
                    <select wire:model.live="profileVisibility" 
                            wire:change="updateProfileVisibility"
                            class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-xs sm:text-sm rounded-lg px-2 py-1 sm:px-3 sm:py-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                        <option value="students">Students Only</option>
                    </select>
                </div>

                <!-- Show Email -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Show Email Publicly</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">Display email on profile</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               wire:model.live="showEmail" 
                               wire:change="updateShowEmail"
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Data & Privacy -->
        <div class="bg-white dark:bg-gray-800/50 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-700/50 backdrop-blur-sm transition-colors duration-300">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center transition-colors duration-300">
                <i class="fas fa-database text-blue-500 dark:text-blue-400 mr-2"></i>
                Data & Privacy
            </h3>

            <div class="space-y-3 sm:space-y-4">
                <!-- Download Data -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Download Your Data</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">Export your account data</p>
                    </div>
                    <button wire:click="exportData" 
                            class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        <span>Export</span>
                    </button>
                </div>

                <!-- Delete Cache -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Clear Cache</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">Clear local cache data</p>
                    </div>
                    <button wire:click="clearCache" 
                            class="px-3 py-2 sm:px-4 sm:py-2 bg-orange-600 hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        Clear
                    </button>
                </div>

                <!-- Activity Log -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium text-sm sm:text-base transition-colors duration-300">Activity Log</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm transition-colors duration-300">View your account activity</p>
                    </div>
                    <a href="{{ route('user.activity') }}" 
                       class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        View
                    </a>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-red-50 dark:bg-red-500/10 p-4 sm:p-6 rounded-xl border border-red-200 dark:border-red-500/30 backdrop-blur-sm transition-colors duration-300">
            <h3 class="text-lg font-semibold text-red-700 dark:text-red-300 mb-3 sm:mb-4 flex items-center transition-colors duration-300">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mr-2"></i>
                Danger Zone
            </h3>

            <div class="space-y-3 sm:space-y-4">
                <!-- Deactivate Account -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-white dark:bg-red-900/20 rounded-lg border border-red-300 dark:border-red-500/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-red-700 dark:text-red-300 font-medium text-sm sm:text-base transition-colors duration-300">Deactivate Account</p>
                        <p class="text-red-600 dark:text-red-400 text-xs sm:text-sm transition-colors duration-300">Temporarily disable your account</p>
                    </div>
                    <button wire:click="$set('showDeactivateModal', true)" 
                            class="px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        Deactivate
                    </button>
                </div>

                <!-- Delete Account -->
                <div class="flex items-center justify-between p-3 sm:p-4 bg-white dark:bg-red-900/20 rounded-lg border border-red-300 dark:border-red-500/30 transition-colors duration-300">
                    <div class="flex-1">
                        <p class="text-red-700 dark:text-red-300 font-medium text-sm sm:text-base transition-colors duration-300">Delete Account</p>
                        <p class="text-red-600 dark:text-red-400 text-xs sm:text-sm transition-colors duration-300">Permanently delete your account</p>
                    </div>
                    <button wire:click="$set('showDeleteModal', true)" 
                            class="px-3 py-2 sm:px-4 sm:py-2 bg-red-700 hover:bg-red-800 dark:bg-red-800 dark:hover:bg-red-900 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Password Change Modal -->
<div x-show="$wire.showPasswordModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="$wire.showPasswordModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
             @click="$wire.showPasswordModal = false"></div>

        <div x-show="$wire.showPasswordModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-500/20 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-key text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Change Password</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                                <input type="password" 
                                       wire:model="current_password" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                                <input type="password" 
                                       wire:model="new_password" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                                <input type="password" 
                                       wire:model="new_password_confirmation" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="changePassword" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Change Password
                </button>
                <button @click="$wire.showPasswordModal = false" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sessions Modal -->
<div x-show="$wire.showSessionsModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="$wire.showSessionsModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 bg-gray-900 bg-opacity-75"
             @click="$wire.showSessionsModal = false"></div>

        <div x-show="$wire.showSessionsModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-10">
            
            <div class="px-4 pt-5 pb-4 sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-500/20 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-laptop text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Active Sessions</h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">You have <strong>{{ $activeSessions }}</strong> active session(s). You can log out all other devices by clicking the button below.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="logoutOtherSessions" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                    Logout Other Devices
                </button>
                <button @click="$wire.showSessionsModal = false" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Account Modal -->
<div x-show="$wire.showDeactivateModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="$wire.showDeactivateModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 bg-gray-900 bg-opacity-75"
             @click="$wire.showDeactivateModal = false"></div>

        <div x-show="$wire.showDeactivateModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-10">
            
            <div class="px-4 pt-5 pb-4 sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 dark:bg-orange-500/20 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-user-slash text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Deactivate Account</h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Are you sure you want to deactivate your account? You can reactivate it anytime by logging back in.
                            </p>
                            <div class="mt-4 bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-lg p-3">
                                <p class="text-sm text-orange-800 dark:text-orange-300">
                                    <i class="fas fa-info-circle mr-2"></i>Your data will be preserved.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="deactivateAccount" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 sm:ml-3 sm:w-auto sm:text-sm">
                    Yes, Deactivate
                </button>
                <button @click="$wire.showDeactivateModal = false" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div x-show="$wire.showDeleteModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="$wire.showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 bg-gray-900 bg-opacity-75"
             @click="$wire.showDeleteModal = false"></div>

        <div x-show="$wire.showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-10">
            
            <div class="px-4 pt-5 pb-4 sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-500/20 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Delete Account Permanently</h3>
                        <div class="mt-4 space-y-4">
                            <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg p-3">
                                <p class="text-sm text-red-800 dark:text-red-300">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Warning:</strong> This action cannot be undone!
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enter your password</label>
                                <input type="password" 
                                       wire:model="delete_password" 
                                       placeholder="Your password"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white">
                                @error('delete_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type "DELETE" to confirm</label>
                                <input type="text" 
                                       wire:model="delete_confirmation" 
                                       placeholder="DELETE"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white uppercase">
                                @error('delete_confirmation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="deleteAccount" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                    Delete My Account
                </button>
                <button @click="$wire.showDeleteModal = false" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>