<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-user-shield text-blue-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Profile Privacy</h3>
                        <p class="text-sm text-gray-600">Control who can see your profile and information</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveProfilePrivacy" class="p-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Profile Visibility
                        </label>
                        <div class="space-y-2">
                            @foreach($this->getProfileVisibilityOptions() as $value => $label)
                                <label class="flex items-center cursor-pointer">
                                    <input wire:model="profile_visibility" type="radio" value="{{ $value }}"
                                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-900 border-b pb-2">Activity Information</h4>
                            
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="show_online_status" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Show online status</span>
                                    <p class="text-xs text-gray-500">Let others see when you're online</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="show_last_seen" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Show last seen</span>
                                    <p class="text-xs text-gray-500">Display when you were last active</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="searchable_profile" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Searchable profile</span>
                                    <p class="text-xs text-gray-500">Allow others to find you in search</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-900 border-b pb-2">Learning Information</h4>
                            
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="show_learning_progress" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Show learning progress</span>
                                    <p class="text-xs text-gray-500">Display your course progress to others</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="show_achievements" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Show achievements</span>
                                    <p class="text-xs text-gray-500">Display your badges and certificates</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="show_courses_enrolled" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Show enrolled courses</span>
                                    <p class="text-xs text-gray-500">Let others see what you're learning</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="show_contact_info" type="checkbox"
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Show contact information</span>
                                    <p class="text-xs text-gray-500">Display email and phone publicly</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Profile Privacy</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Communication Privacy -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-comments text-green-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Communication Privacy</h3>
                        <p class="text-sm text-gray-600">Control how others can contact and interact with you</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveCommunicationPrivacy" class="p-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Who can send you direct messages?
                        </label>
                        <div class="space-y-2">
                            @foreach($this->getDirectMessageOptions() as $value => $label)
                                <label class="flex items-center cursor-pointer">
                                    <input wire:model="allow_direct_messages" type="radio" value="{{ $value }}"
                                           class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                    <span class="ml-3 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="allow_course_messages" type="checkbox"
                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Allow course messages</span>
                                    <p class="text-xs text-gray-500">Receive messages related to your courses</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="allow_instructor_contact" type="checkbox"
                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Allow instructor contact</span>
                                    <p class="text-xs text-gray-500">Let instructors contact you directly</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="show_email_to_instructors" type="checkbox"
                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Show email to instructors</span>
                                    <p class="text-xs text-gray-500">Share your email with course instructors</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="allow_peer_collaboration" type="checkbox"
                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Allow peer collaboration</span>
                                    <p class="text-xs text-gray-500">Participate in group projects and discussions</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Communication Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Data & Analytics Privacy -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar text-purple-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Data & Analytics Privacy</h3>
                        <p class="text-sm text-gray-600">Control how your data is collected and used</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveDataPrivacy" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <label class="flex items-center cursor-pointer">
                            <input wire:model="data_collection_consent" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Data collection consent</span>
                                <p class="text-xs text-gray-500">Allow collection of learning analytics</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="analytics_tracking" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Analytics tracking</span>
                                <p class="text-xs text-gray-500">Track usage patterns for improvements</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="personalized_recommendations" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Personalized recommendations</span>
                                <p class="text-xs text-gray-500">Use data to suggest relevant courses</p>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center cursor-pointer">
                            <input wire:model="marketing_analytics" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Marketing analytics</span>
                                <p class="text-xs text-gray-500">Use data for marketing purposes</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="third_party_integrations" type="checkbox"
                                   class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Third-party integrations</span>
                                <p class="text-xs text-gray-500">Share data with integrated services</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Data Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-red-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Security Settings</h3>
                        <p class="text-sm text-gray-600">Manage your account security and notifications</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveSecuritySettings" class="p-6">
                <div class="space-y-6">
                    <!-- Two-Factor Authentication -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">Two-Factor Authentication</h4>
                                <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                            </div>
                            <div>
                                @if($two_factor_enabled)
                                    <button type="button" wire:click="disable2FA" 
                                            class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition-colors">
                                        <i class="fas fa-times mr-2"></i>Disable
                                    </button>
                                @else
                                    <button type="button" wire:click="enable2FA" 
                                            class="bg-green-100 text-green-700 px-4 py-2 rounded-lg hover:bg-green-200 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Enable
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Security Notifications -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-900 border-b pb-2">Security Notifications</h4>
                            
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="login_notifications" type="checkbox"
                                       class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Login notifications</span>
                                    <p class="text-xs text-gray-500">Get notified of new login attempts</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="suspicious_activity_alerts" type="checkbox"
                                       class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Suspicious activity alerts</span>
                                    <p class="text-xs text-gray-500">Alert me of unusual account activity</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="password_change_notifications" type="checkbox"
                                       class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Password change notifications</span>
                                    <p class="text-xs text-gray-500">Notify when password is changed</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="data_export_notifications" type="checkbox"
                                       class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Data export notifications</span>
                                    <p class="text-xs text-gray-500">Notify when data exports are ready</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Security Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Session Management -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-desktop text-indigo-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Session Management</h3>
                        <p class="text-sm text-gray-600">Manage your active sessions and login preferences</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Session Settings -->
                <form wire:submit.prevent="saveSessionSettings">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="auto_logout_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Auto-logout after
                            </label>
                            <select wire:model="auto_logout_time" id="auto_logout_time"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($this->getAutoLogoutOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="remember_me_enabled" type="checkbox"
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Enable "Remember Me"</span>
                                    <p class="text-xs text-gray-500">Stay logged in on trusted devices</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="session_notifications" type="checkbox"
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Session notifications</span>
                                    <p class="text-xs text-gray-500">Notify about session changes</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" wire:loading.attr="disabled"
                                class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                            <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Session Settings</span>
                            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>

                <!-- Active Sessions -->
                <div class="border-t pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-gray-900">Active Sessions</h4>
                        <button wire:click="terminateAllOtherSessions" 
                                class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Terminate All Other Sessions
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($active_sessions as $session)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-desktop text-gray-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $session['device'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $session['ip_address'] }} • {{ $session['last_activity']->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @if(!($session['is_current'] ?? false))
                                    <button wire:click="terminateSession('{{ $session['id'] }}')" 
                                            class="text-red-600 hover:text-red-700 text-sm">
                                        Terminate
                                    </button>
                                @else
                                    <span class="text-green-600 text-sm font-medium">Current Session</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Rights -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-balance-scale text-orange-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Data Rights & Retention</h3>
                        <p class="text-sm text-gray-600">Control your data and exercise your privacy rights</p>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="saveDataRights" class="p-6">
                <div class="space-y-6">
                    <div>
                        <label for="data_retention_period" class="block text-sm font-medium text-gray-700 mb-2">
                            Data retention period
                        </label>
                        <select wire:model="data_retention_period" id="data_retention_period"
                                class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            @foreach($this->getRetentionPeriodOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="marketing_communications" type="checkbox"
                                       class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Marketing communications</span>
                                    <p class="text-xs text-gray-500">Receive promotional emails and offers</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="data_sharing_consent" type="checkbox"
                                       class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Data sharing consent</span>
                                    <p class="text-xs text-gray-500">Allow sharing anonymized data with partners</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="research_participation" type="checkbox"
                                       class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Research participation</span>
                                    <p class="text-xs text-gray-500">Participate in educational research studies</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Data Rights</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Export & Account Management -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-download text-gray-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Data Export & Account Management</h3>
                        <p class="text-sm text-gray-600">Export your data or manage your account</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Data Export -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-file-export text-blue-600 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Export Your Data</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Download a copy of all your personal data, including course progress, certificates, and profile information.
                                </p>
                                <button wire:click="exportData" 
                                        class="mt-3 bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                                    <i class="fas fa-download mr-2"></i>Request Data Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Account Deletion -->
                    <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-exclamation-triangle text-red-600 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="font-medium text-red-900">Delete Account</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Permanently delete your account and all associated data. This action cannot be undone.
                                </p>
                                <button wire:click="deleteAccount" 
                                        class="mt-3 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                                    <i class="fas fa-trash mr-2"></i>Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Privacy Summary -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Your Privacy Settings Summary
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>
                                <p><span class="font-medium">Profile visibility:</span> {{ ucfirst(str_replace('_', ' ', $profile_visibility)) }}</p>
                                <p><span class="font-medium">Direct messages:</span> {{ ucfirst(str_replace('_', ' ', $allow_direct_messages)) }}</p>
                                <p><span class="font-medium">Data collection:</span> {{ $data_collection_consent ? 'Enabled' : 'Disabled' }}</p>
                            </div>
                            <div>
                                <p><span class="font-medium">Two-factor auth:</span> {{ $two_factor_enabled ? 'Enabled' : 'Disabled' }}</p>
                                <p><span class="font-medium">Marketing emails:</span> {{ $marketing_communications ? 'Enabled' : 'Disabled' }}</p>
                                <p><span class="font-medium">Data retention:</span> {{ ucfirst(str_replace('_', ' ', $data_retention_period)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>