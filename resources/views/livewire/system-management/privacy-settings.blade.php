<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-pink-600 p-6 rounded-2xl shadow-xl text-white mb-8">
        <h1 class="text-3xl font-bold flex items-center">
            <i class="fas fa-lock mr-3"></i>
            Privacy Settings
        </h1>
        <p class="text-red-100 mt-2">Manage your privacy, security, and data preferences</p>
    </div>

    <div class="space-y-8">
        <!-- Profile Privacy -->
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary">
                <div class="flex items-center">
                    <i class="fas fa-user-shield text-blue-600 dark:text-blue-400 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Profile Privacy</h3>
                        <p class="text-sm text-themed-secondary">Control who can see your profile and information</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveProfilePrivacy" class="p-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-3">
                            Profile Visibility
                        </label>
                        <div class="space-y-2">
                            @foreach($this->getProfileVisibilityOptions() as $value => $label)
                                <label class="flex items-center cursor-pointer group p-3 rounded-lg hover:bg-themed-tertiary transition-colors">
                                    <input wire:model="profile_visibility" type="radio" value="{{ $value }}"
                                           class="h-4 w-4 text-blue-600 border-themed-primary focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-themed-primary group-hover:font-medium transition-all">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="font-medium text-themed-primary border-b border-themed-primary pb-2">Activity Information</h4>
                            
                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="show_online_status" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-themed-primary rounded focus:ring-blue-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Show online status</span>
                                    <p class="text-xs text-themed-secondary">Let others see when you're online</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="show_last_seen" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-themed-primary rounded focus:ring-blue-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Show last seen</span>
                                    <p class="text-xs text-themed-secondary">Display when you were last active</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="searchable_profile" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-themed-primary rounded focus:ring-blue-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Searchable profile</span>
                                    <p class="text-xs text-themed-secondary">Allow others to find you in search</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <h4 class="font-medium text-themed-primary border-b border-themed-primary pb-2">Learning Information</h4>
                            
                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="show_learning_progress" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-themed-primary rounded focus:ring-blue-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Show learning progress</span>
                                    <p class="text-xs text-themed-secondary">Display your course progress to others</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="show_achievements" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-themed-primary rounded focus:ring-blue-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Show achievements</span>
                                    <p class="text-xs text-themed-secondary">Display your badges and certificates</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="show_courses_enrolled" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-themed-primary rounded focus:ring-blue-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Show enrolled courses</span>
                                    <p class="text-xs text-themed-secondary">Let others see what you're learning</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="show_contact_info" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-themed-primary rounded focus:ring-blue-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Show contact information</span>
                                    <p class="text-xs text-themed-secondary">Display email and phone publicly</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-themed-primary flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Profile Privacy</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Communication Privacy -->
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary">
                <div class="flex items-center">
                    <i class="fas fa-comments text-green-600 dark:text-green-400 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Communication Privacy</h3>
                        <p class="text-sm text-themed-secondary">Control how others can contact and interact with you</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveCommunicationPrivacy" class="p-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-3">
                            Who can send you direct messages?
                        </label>
                        <div class="space-y-2">
                            @foreach($this->getDirectMessageOptions() as $value => $label)
                                <label class="flex items-center cursor-pointer group p-3 rounded-lg hover:bg-themed-tertiary transition-colors">
                                    <input wire:model="allow_direct_messages" type="radio" value="{{ $value }}"
                                           class="h-4 w-4 text-green-600 border-themed-primary focus:ring-green-500">
                                    <span class="ml-3 text-sm text-themed-primary group-hover:font-medium transition-all">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="allow_course_messages" type="checkbox"
                                       class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Allow course messages</span>
                                    <p class="text-xs text-themed-secondary">Receive messages related to your courses</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="allow_instructor_contact" type="checkbox"
                                       class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Allow instructor contact</span>
                                    <p class="text-xs text-themed-secondary">Let instructors contact you directly</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="show_email_to_instructors" type="checkbox"
                                       class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Show email to instructors</span>
                                    <p class="text-xs text-themed-secondary">Share your email with course instructors</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="allow_peer_collaboration" type="checkbox"
                                       class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Allow peer collaboration</span>
                                    <p class="text-xs text-themed-secondary">Participate in group projects and discussions</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-themed-primary flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Communication Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Data & Analytics Privacy -->
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Data & Analytics Privacy</h3>
                        <p class="text-sm text-themed-secondary">Control how your data is collected and used</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveDataPrivacy" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="data_collection_consent" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-themed-primary rounded focus:ring-purple-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Data collection consent</span>
                                <p class="text-xs text-themed-secondary">Allow collection of learning analytics</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="analytics_tracking" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-themed-primary rounded focus:ring-purple-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Analytics tracking</span>
                                <p class="text-xs text-themed-secondary">Track usage patterns for improvements</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="personalized_recommendations" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-themed-primary rounded focus:ring-purple-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Personalized recommendations</span>
                                <p class="text-xs text-themed-secondary">Use data to suggest relevant courses</p>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="marketing_analytics" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-themed-primary rounded focus:ring-purple-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Marketing analytics</span>
                                <p class="text-xs text-themed-secondary">Use data for marketing purposes</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="third_party_integrations" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-themed-primary rounded focus:ring-purple-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Third-party integrations</span>
                                <p class="text-xs text-themed-secondary">Share data with integrated services</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-themed-primary flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Data Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Settings -->
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-red-600 dark:text-red-400 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Security Settings</h3>
                        <p class="text-sm text-themed-secondary">Manage your account security and notifications</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveSecuritySettings" class="p-6">
                <div class="space-y-6">
                    <!-- Two-Factor Authentication -->
                    <div class="border border-themed-primary rounded-lg p-4 bg-themed-tertiary">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-themed-primary">Two-Factor Authentication</h4>
                                <p class="text-sm text-themed-secondary">Add an extra layer of security to your account</p>
                            </div>
                            <div>
                                @if($two_factor_enabled)
                                    <button type="button" wire:click="disable2FA" 
                                            class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-4 py-2 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                                        <i class="fas fa-times mr-2"></i>Disable
                                    </button>
                                @else
                                    <button type="button" wire:click="enable2FA" 
                                            class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-4 py-2 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Enable
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Security Notifications -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="font-medium text-themed-primary border-b border-themed-primary pb-2">Security Notifications</h4>
                            
                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="login_notifications" type="checkbox"
                                       class="h-4 w-4 text-red-600 border-themed-primary rounded focus:ring-red-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Login notifications</span>
                                    <p class="text-xs text-themed-secondary">Get notified of new login attempts</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="suspicious_activity_alerts" type="checkbox"
                                       class="h-4 w-4 text-red-600 border-themed-primary rounded focus:ring-red-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Suspicious activity alerts</span>
                                    <p class="text-xs text-themed-secondary">Alert me of unusual account activity</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="password_change_notifications" type="checkbox"
                                       class="h-4 w-4 text-red-600 border-themed-primary rounded focus:ring-red-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Password change notifications</span>
                                    <p class="text-xs text-themed-secondary">Notify when password is changed</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="data_export_notifications" type="checkbox"
                                       class="h-4 w-4 text-red-600 border-themed-primary rounded focus:ring-red-500">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Data export notifications</span>
                                    <p class="text-xs text-themed-secondary">Notify when data exports are ready</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-themed-primary flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Security Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Export & Account Management -->
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary">
                <div class="flex items-center">
                    <i class="fas fa-download text-themed-secondary mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Data Export & Account Management</h3>
                        <p class="text-sm text-themed-secondary">Export your data or manage your account</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Data Export -->
                    <div class="border border-blue-300 dark:border-blue-700 rounded-lg p-4 bg-blue-100/10 dark:bg-blue-900/10">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-file-export text-blue-600 dark:text-blue-400 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-medium text-themed-primary">Export Your Data</h4>
                                <p class="text-sm text-themed-secondary mt-1">
                                    Download a copy of all your personal data, including course progress, certificates, and profile information.
                                </p>
                                <button wire:click="exportData" 
                                        class="mt-3 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-4 py-2 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors text-sm font-medium">
                                    <i class="fas fa-download mr-2"></i>Request Data Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Account Deletion -->
                    <div class="border border-red-300 dark:border-red-700 rounded-lg p-4 bg-red-100/10 dark:bg-red-900/10">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-medium text-red-700 dark:text-red-300">Delete Account</h4>
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                                    Permanently delete your account and all associated data. This action cannot be undone.
                                </p>
                                <button wire:click="deleteAccount" 
                                        class="mt-3 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                                    <i class="fas fa-trash mr-2"></i>Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Privacy Summary -->
                <div class="mt-6 pt-6 border-t border-themed-primary">
                    <div class="bg-themed-tertiary p-4 rounded-lg">
                        <h4 class="font-medium text-themed-primary mb-3 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-2"></i>
                            Your Privacy Settings Summary
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-themed-secondary">
                            <div class="space-y-1">
                                <p><span class="font-medium text-themed-primary">Profile visibility:</span> {{ ucfirst(str_replace('_', ' ', $profile_visibility)) }}</p>
                                <p><span class="font-medium text-themed-primary">Direct messages:</span> {{ ucfirst(str_replace('_', ' ', $allow_direct_messages)) }}</p>
                                <p><span class="font-medium text-themed-primary">Data collection:</span> {{ $data_collection_consent ? '✓ Enabled' : '✗ Disabled' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p><span class="font-medium text-themed-primary">Two-factor auth:</span> {{ $two_factor_enabled ? '✓ Enabled' : '✗ Disabled' }}</p>
                                <p><span class="font-medium text-themed-primary">Marketing emails:</span> {{ $marketing_communications ? '✓ Enabled' : '✗ Disabled' }}</p>
                                <p><span class="font-medium text-themed-primary">Data retention:</span> {{ ucfirst(str_replace('_', ' ', $data_retention_period)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>