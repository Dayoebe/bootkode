<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 rounded-2xl shadow-xl text-white mb-8">
        <h1 class="text-3xl font-bold flex items-center">
            <i class="fas fa-bell mr-3"></i>
            Notification Preferences
        </h1>
        <p class="text-blue-100 mt-2">Control how and when you receive notifications from the platform</p>
    </div>

    <div class="space-y-8">
        <!-- Email Notifications -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-envelope text-blue-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Email Notifications</h3>
                        <p class="text-sm text-gray-600">Choose which emails you'd like to receive</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="enableAllEmail"
                        class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors">
                        Enable All
                    </button>
                    <button wire:click="disableAllEmail"
                        class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors">
                        Disable All
                    </button>
                    <button wire:click="testNotification('email')"
                        class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                        Test Email
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="saveEmailNotifications" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Learning & Courses -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 border-b pb-2">Learning & Courses</h4>

                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="email_course_updates" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Course updates</span>
                                    <p class="text-xs text-gray-500">New content, announcements, and changes</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="email_assignment_reminders" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Assignment reminders</span>
                                    <p class="text-xs text-gray-500">Deadlines and upcoming tasks</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="email_certificate_notifications" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Certificate notifications</span>
                                    <p class="text-xs text-gray-500">Certificate approvals and updates</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="email_achievement_notifications" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Achievement notifications</span>
                                    <p class="text-xs text-gray-500">Badges, milestones, and accomplishments</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-900 border-b pb-2">Communication & Social</h4>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="email_announcement_notifications" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Announcements</span>
                                    <p class="text-xs text-gray-500">Platform updates and important news</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="email_forum_replies" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Forum replies</span>
                                    <p class="text-xs text-gray-500">Responses to your forum posts</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="email_direct_messages" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Direct messages</span>
                                    <p class="text-xs text-gray-500">Private messages from other users</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Marketing & Updates -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 border-b pb-2">Marketing & Updates</h4>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="email_system_updates" type="checkbox"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">System updates</span>
                                <p class="text-xs text-gray-500">Feature releases and maintenance notices</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="email_marketing_updates" type="checkbox"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Marketing updates</span>
                                <p class="text-xs text-gray-500">Promotional content and special offers</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="email_weekly_digest" type="checkbox"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Weekly digest</span>
                                <p class="text-xs text-gray-500">Summary of your activity and progress</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Email Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Push Notifications -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-mobile-alt text-green-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Push Notifications</h3>
                        <p class="text-sm text-gray-600">Browser and mobile push notifications</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="enableAllPush"
                        class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors">
                        Enable All
                    </button>
                    <button wire:click="disableAllPush"
                        class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors">
                        Disable All
                    </button>
                    <button wire:click="testNotification('push')"
                        class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                        Test Push
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="savePushNotifications" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Learning & Courses -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 border-b pb-2">Learning & Courses</h4>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="push_course_updates" type="checkbox"
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Course updates</span>
                                <p class="text-xs text-gray-500">New content and announcements</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="push_assignment_reminders" type="checkbox"
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Assignment reminders</span>
                                <p class="text-xs text-gray-500">Upcoming deadlines and tasks</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="push_certificate_notifications" type="checkbox"
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Certificate notifications</span>
                                <p class="text-xs text-gray-500">Certificate status updates</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="push_achievement_notifications" type="checkbox"
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Achievement notifications</span>
                                <p class="text-xs text-gray-500">Badges and milestones</p>
                            </div>
                        </label>
                    </div>

                    <!-- Communication -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 border-b pb-2">Communication</h4>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="push_announcement_notifications" type="checkbox"
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Announcements</span>
                                <p class="text-xs text-gray-500">Platform updates and news</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="push_forum_replies" type="checkbox"
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Forum replies</span>
                                <p class="text-xs text-gray-500">Forum post responses</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input wire:model="push_direct_messages" type="checkbox"
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Direct messages</span>
                                <p class="text-xs text-gray-500">Private messages from users</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Push Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- SMS Notifications -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-sms text-purple-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">SMS Notifications</h3>
                        <p class="text-sm text-gray-600">Text message notifications to your phone</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="testNotification('sms')"
                        class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                        Test SMS
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="saveSmsNotifications" class="p-6">
                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input wire:model="sms_enabled" type="checkbox"
                            class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable SMS notifications</span>
                            <p class="text-xs text-gray-500">Allow SMS notifications to be sent to your phone</p>
                        </div>
                    </label>

                    @if($sms_enabled)
                        <div class="ml-7 space-y-4 border-l-2 border-purple-100 pl-4">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model="sms_emergency_only" type="checkbox"
                                    class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Emergency only</span>
                                    <p class="text-xs text-gray-500">Only critical system alerts</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="sms_assignment_deadlines" type="checkbox"
                                    class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Assignment deadlines</span>
                                    <p class="text-xs text-gray-500">Critical deadline reminders</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input wire:model="sms_system_maintenance" type="checkbox"
                                    class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">System maintenance</span>
                                    <p class="text-xs text-gray-500">Planned maintenance notifications</p>
                                </div>
                            </label>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save SMS Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Frequency Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-clock text-orange-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Frequency & Timing</h3>
                        <p class="text-sm text-gray-600">Control when and how often you receive notifications</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="saveFrequencySettings" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Digest Frequency -->
                    <div>
                        <label for="digest_frequency" class="block text-sm font-medium text-gray-700 mb-2">
                            Digest frequency
                        </label>
                        <select wire:model="digest_frequency" id="digest_frequency"
                            class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            @foreach($this->getDigestFrequencyOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">How often to receive summary emails</p>
                    </div>

                    <!-- Reminder Frequency -->
                    <div>
                        <label for="reminder_frequency" class="block text-sm font-medium text-gray-700 mb-2">
                            Reminder frequency
                        </label>
                        <select wire:model="reminder_frequency" id="reminder_frequency"
                            class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            @foreach($this->getReminderFrequencyOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">When to remind about deadlines</p>
                    </div>

                    <!-- Quiet Hours Start -->
                    <div>
                        <label for="quiet_hours_start" class="block text-sm font-medium text-gray-700 mb-2">
                            Quiet hours start
                        </label>
                        <input wire:model="quiet_hours_start" type="time" id="quiet_hours_start"
                            class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                        <p class="text-xs text-gray-500 mt-1">No notifications during quiet hours</p>
                    </div>

                    <!-- Quiet Hours End -->
                    <div>
                        <label for="quiet_hours_end" class="block text-sm font-medium text-gray-700 mb-2">
                            Quiet hours end
                        </label>
                        <input wire:model="quiet_hours_end" type="time" id="quiet_hours_end"
                            class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <!-- Weekend Notifications -->
                    <div class="md:col-span-2">
                        <label class="flex items-center cursor-pointer">
                            <input wire:model="weekend_notifications" type="checkbox"
                                class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700">Weekend notifications</span>
                                <p class="text-xs text-gray-500">Allow notifications on weekends</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Frequency Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Content Preferences -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-language text-indigo-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Content Preferences</h3>
                        <p class="text-sm text-gray-600">Customize notification content and language</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="saveContentPreferences" class="p-6">
                <div class="space-y-6">
                    <!-- Preferred Language -->
                    <div>
                        <label for="preferred_language" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred language for notifications
                        </label>
                        <select wire:model="preferred_language" id="preferred_language"
                            class="block w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($this->getLanguageOptions() as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notification Categories -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Interested Categories</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach(['Technology', 'Business', 'Design', 'Development', 'Marketing', 'Data Science'] as $category)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" value="{{ $category }}" wire:model="notification_categories"
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $category }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Get notifications about courses in these categories</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Content Preferences</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Notification Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-gray-600 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Current Settings Summary</h3>
                        <p class="text-sm text-gray-600">Overview of your notification preferences</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Email Summary -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2 flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            Email Notifications
                        </h4>
                        <div class="text-sm text-blue-700 space-y-1">
                            <p>Course updates: {{ $email_course_updates ? 'Enabled' : 'Disabled' }}</p>
                            <p>Assignments: {{ $email_assignment_reminders ? 'Enabled' : 'Disabled' }}</p>
                            <p>Certificates: {{ $email_certificate_notifications ? 'Enabled' : 'Disabled' }}</p>
                            <p>Weekly digest: {{ $email_weekly_digest ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                    </div>

                    <!-- Push Summary -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-medium text-green-900 mb-2 flex items-center">
                            <i class="fas fa-mobile-alt mr-2"></i>
                            Push Notifications
                        </h4>
                        <div class="text-sm text-green-700 space-y-1">
                            <p>Course updates: {{ $push_course_updates ? 'Enabled' : 'Disabled' }}</p>
                            <p>Assignments: {{ $push_assignment_reminders ? 'Enabled' : 'Disabled' }}</p>
                            <p>Achievements: {{ $push_achievement_notifications ? 'Enabled' : 'Disabled' }}</p>
                            <p>Messages: {{ $push_direct_messages ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                    </div>

                    <!-- Timing Summary -->
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h4 class="font-medium text-orange-900 mb-2 flex items-center">
                            <i class="fas fa-clock mr-2"></i>
                            Timing Settings
                        </h4>
                        <div class="text-sm text-orange-700 space-y-1">
                            <p>Digest: {{ ucfirst($digest_frequency) }}</p>
                            <p>Reminders: {{ str_replace('_', ' ', $reminder_frequency) }}</p>
                            <p>Quiet hours: {{ $quiet_hours_start }} - {{ $quiet_hours_end }}</p>
                            <p>Weekends: {{ $weekend_notifications ? 'Allowed' : 'Blocked' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Global Actions -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="flex flex-wrap gap-3">
                        <button wire:click="enableAllEmail"
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                            <i class="fas fa-check-circle mr-2"></i>Enable All Email
                        </button>
                        <button wire:click="enableAllPush"
                            class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                            <i class="fas fa-check-circle mr-2"></i>Enable All Push
                        </button>
                        <button wire:click="disableAllEmail"
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                            <i class="fas fa-times-circle mr-2"></i>Disable All Email
                        </button>
                        <button wire:click="disableAllPush"
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                            <i class="fas fa-times-circle mr-2"></i>Disable All Push
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>