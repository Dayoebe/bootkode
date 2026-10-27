<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-brand-ink   p-6 rounded-lg shadow-sm text-white mb-8">
        <h1 class=" font-bold flex items-center bk-page-title">
            <i aria-hidden="true" class="fas fa-bell mr-3"></i>
            Notification Preferences
        </h1>
        <p class="text-brand mt-2">Control how and when you receive notifications from the platform</p>
    </div>

    <div class="space-y-8">
        <!-- Email Notifications -->
        <div class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center">
                    <i aria-hidden="true" class="fas fa-envelope text-brand dark:text-brand mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Email Notifications</h3>
                        <p class="text-sm text-themed-secondary">Choose which emails you'd like to receive. Test delivery is unavailable.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="enableAllEmail"
                        class="text-xs px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                        Enable All
                    </button>
                    <button wire:click="disableAllEmail"
                        class="text-xs px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                        Disable All
                    </button>
                    <button wire:click="testNotification('email')"
                        class="text-xs px-3 py-1 bg-brand/10 dark:bg-action/30 text-brand dark:text-brand rounded-full hover:bg-indigo-200 dark:hover:bg-action/50 transition-colors">
                        Test Email
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="saveEmailNotifications" class="p-6">
                <fieldset class="mb-6 space-y-3 rounded-lg border border-themed-primary p-4">
                    <legend class="px-1 font-medium text-themed-primary">Saved alerts and private replies</legend>
                    <p class="text-sm text-themed-secondary">These emails are optional. New job matches and community replies remain available in your portal. Email delivery follows your quiet hours and weekend settings.</p>
                    @unless(app(\App\Services\PortalNotificationService::class)->emailAvailable())
                        <p class="text-sm text-themed-secondary">Email delivery is currently unavailable. Your choices will be saved.</p>
                    @endunless
                    <label class="flex min-h-11 items-center gap-3 text-themed-primary"><input type="checkbox" wire:model="email_job_alerts" aria-describedby="job-email-error" aria-invalid="{{ $errors->has('email_job_alerts') ? 'true' : 'false' }}" class="rounded border-themed-primary text-brand focus:ring-brand/30">Email me saved job matches</label>
                    <label class="flex min-h-11 items-center gap-3 text-themed-primary"><input type="checkbox" wire:model="email_community_feedback_replies" aria-describedby="community-email-error" aria-invalid="{{ $errors->has('email_community_feedback_replies') ? 'true' : 'false' }}" class="rounded border-themed-primary text-brand focus:ring-brand/30">Email me community feedback replies</label>
                    <x-input-error id="job-email-error" :messages="$errors->get('email_job_alerts')" />
                    <x-input-error id="community-email-error" :messages="$errors->get('email_community_feedback_replies')" />
                </fieldset>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Learning & Courses -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-themed-primary border-b border-themed-primary pb-2">Learning & Courses</h4>

                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="email_course_updates" type="checkbox"
                                    class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Course updates</span>
                                    <p class="text-xs text-themed-secondary">New content, announcements, and changes</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="email_assignment_reminders" type="checkbox"
                                    class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Assignment reminders</span>
                                    <p class="text-xs text-themed-secondary">Deadlines and upcoming tasks</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="email_certificate_notifications" type="checkbox"
                                    class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Certificate notifications</span>
                                    <p class="text-xs text-themed-secondary">Certificate approvals and updates</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="email_achievement_notifications" type="checkbox"
                                    class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Achievement notifications</span>
                                    <p class="text-xs text-themed-secondary">Badges, milestones, and accomplishments</p>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-3 pt-4">
                            <h4 class="font-medium text-themed-primary border-b border-themed-primary pb-2">Communication & Social</h4>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="email_announcement_notifications" type="checkbox"
                                    class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Announcements</span>
                                    <p class="text-xs text-themed-secondary">Platform updates and important news</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="email_forum_replies" type="checkbox"
                                    class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Forum replies</span>
                                    <p class="text-xs text-themed-secondary">Responses to your forum posts</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="email_direct_messages" type="checkbox"
                                    class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Direct messages</span>
                                    <p class="text-xs text-themed-secondary">Private messages from other users</p>
                                </div>
                            </label>

                            <label class="flex items-center cursor-pointer group">
                                <input wire:model="email_instructor_replies" type="checkbox"
                                    class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                                <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                    <span class="text-sm font-medium text-themed-primary">Instructor replies</span>
                                    <p class="text-xs text-themed-secondary">When instructors respond to your reviews</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Marketing & Updates -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-themed-primary border-b border-themed-primary pb-2">Marketing & Updates</h4>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="email_system_updates" type="checkbox"
                                class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">System updates</span>
                                <p class="text-xs text-themed-secondary">Feature releases and maintenance notices</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="email_marketing_updates" type="checkbox"
                                class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Marketing updates</span>
                                <p class="text-xs text-themed-secondary">Promotional content and special offers</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="email_weekly_digest" type="checkbox"
                                class="h-4 w-4 text-brand border-themed-primary rounded focus:ring-brand/30">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Weekly digest</span>
                                <p class="text-xs text-themed-secondary">Summary of your activity and progress</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-action hover:bg-action dark:bg-action dark:hover:bg-action text-action-foreground px-6 py-2 rounded-lg transition-colors duration-200 font-medium">
                        <span wire:loading.remove><i aria-hidden="true" class="fas fa-save mr-2"></i>Save Email Settings</span>
                        <span wire:loading><i aria-hidden="true" class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Push Notifications -->
        <div class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center">
                    <i aria-hidden="true" class="fas fa-mobile-alt text-green-600 dark:text-green-400 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Push Notifications</h3>
                        <p class="text-sm text-themed-secondary">Saved preferences only; browser and mobile push delivery is not connected.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="enableAllPush"
                        class="text-xs px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                        Enable All
                    </button>
                    <button wire:click="disableAllPush"
                        class="text-xs px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                        Disable All
                    </button>
                    <button wire:click="testNotification('push')"
                        class="text-xs px-3 py-1 bg-brand/10 dark:bg-action/30 text-brand dark:text-brand rounded-full hover:bg-indigo-200 dark:hover:bg-action/50 transition-colors">
                        Test Push
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="savePushNotifications" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Learning & Courses -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-themed-primary border-b border-themed-primary pb-2">Learning & Courses</h4>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="push_course_updates" type="checkbox"
                                class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Course updates</span>
                                <p class="text-xs text-themed-secondary">New content and announcements</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="push_assignment_reminders" type="checkbox"
                                class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Assignment reminders</span>
                                <p class="text-xs text-themed-secondary">Upcoming deadlines and tasks</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="push_certificate_notifications" type="checkbox"
                                class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Certificate notifications</span>
                                <p class="text-xs text-themed-secondary">Certificate status updates</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="push_achievement_notifications" type="checkbox"
                                class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Achievement notifications</span>
                                <p class="text-xs text-themed-secondary">Badges and milestones</p>
                            </div>
                        </label>
                    </div>

                    <!-- Communication -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-themed-primary border-b border-themed-primary pb-2">Communication</h4>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="push_announcement_notifications" type="checkbox"
                                class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Announcements</span>
                                <p class="text-xs text-themed-secondary">Platform updates and news</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="push_forum_replies" type="checkbox"
                                class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Forum replies</span>
                                <p class="text-xs text-themed-secondary">Forum post responses</p>
                            </div>
                        </label>

                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="push_direct_messages" type="checkbox"
                                class="h-4 w-4 text-green-600 border-themed-primary rounded focus:ring-green-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Direct messages</span>
                                <p class="text-xs text-themed-secondary">Private messages from users</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium">
                        <span wire:loading.remove><i aria-hidden="true" class="fas fa-save mr-2"></i>Save Push Settings</span>
                        <span wire:loading><i aria-hidden="true" class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Frequency Settings -->
        <div class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary">
                <div class="flex items-center">
                    <i aria-hidden="true" class="fas fa-clock text-orange-600 dark:text-orange-400 mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Frequency & Timing</h3>
                        <p class="text-sm text-themed-secondary">Control when and how often you receive notifications</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="saveFrequencySettings" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Digest Frequency -->
                    <div>
                        <label for="digest_frequency" class="block text-sm font-medium text-themed-primary mb-2">
                            Digest frequency
                        </label>
                        <select wire:model="digest_frequency" id="digest_frequency"
                            class="block w-full border-themed-primary bg-themed-secondary rounded-lg focus:ring-orange-500 focus:border-orange-500 text-themed-primary">
                            @foreach($this->getDigestFrequencyOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-themed-secondary mt-1">How often to receive summary emails</p>
                    </div>

                    <!-- Reminder Frequency -->
                    <div>
                        <label for="reminder_frequency" class="block text-sm font-medium text-themed-primary mb-2">
                            Reminder frequency
                        </label>
                        <select wire:model="reminder_frequency" id="reminder_frequency"
                            class="block w-full border-themed-primary bg-themed-secondary rounded-lg focus:ring-orange-500 focus:border-orange-500 text-themed-primary">
                            @foreach($this->getReminderFrequencyOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-themed-secondary mt-1">When to remind about deadlines</p>
                    </div>

                    <!-- Quiet Hours Start -->
                    <div>
                        <label for="quiet_hours_start" class="block text-sm font-medium text-themed-primary mb-2">
                            Quiet hours start
                        </label>
                        <input wire:model="quiet_hours_start" type="time" id="quiet_hours_start" aria-describedby="quiet-start-error" aria-invalid="{{ $errors->has('quiet_hours_start') ? 'true' : 'false' }}"
                            class="block w-full border-themed-primary bg-themed-secondary rounded-lg focus:ring-orange-500 focus:border-orange-500 text-themed-primary">
                        <p class="text-xs text-themed-secondary mt-1">Saved job-match and community-reply emails wait until quiet hours end.</p>
                        <x-input-error id="quiet-start-error" :messages="$errors->get('quiet_hours_start')" />
                    </div>

                    <!-- Quiet Hours End -->
                    <div>
                        <label for="quiet_hours_end" class="block text-sm font-medium text-themed-primary mb-2">
                            Quiet hours end
                        </label>
                        <input wire:model="quiet_hours_end" type="time" id="quiet_hours_end" aria-describedby="quiet-end-error" aria-invalid="{{ $errors->has('quiet_hours_end') ? 'true' : 'false' }}"
                            class="block w-full border-themed-primary bg-themed-secondary rounded-lg focus:ring-orange-500 focus:border-orange-500 text-themed-primary">
                    </div>

                    <x-input-error id="quiet-end-error" :messages="$errors->get('quiet_hours_end')" />

                    <!-- Weekend Notifications -->
                    <div class="md:col-span-2">
                        <label class="flex items-center cursor-pointer group">
                            <input wire:model="weekend_notifications" type="checkbox"
                                class="h-4 w-4 text-orange-600 border-themed-primary rounded focus:ring-orange-500">
                            <div class="ml-3 group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium text-themed-primary">Weekend notifications</span>
                                <p class="text-xs text-themed-secondary">Allow notifications on weekends</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-orange-600 hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 font-medium">
                        <span wire:loading.remove><i aria-hidden="true" class="fas fa-save mr-2"></i>Save Frequency Settings</span>
                        <span wire:loading><i aria-hidden="true" class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Notification Summary -->
        <div class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary overflow-hidden">
            <div class="px-6 py-4 border-b border-themed-primary">
                <div class="flex items-center">
                    <i aria-hidden="true" class="fas fa-info-circle text-themed-secondary mr-3 text-lg"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">Current Settings Summary</h3>
                        <p class="text-sm text-themed-secondary">Overview of your notification preferences</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Email Summary -->
                    <div class="bg-brand/10 dark:bg-action/20 p-4 rounded-lg border border-brand dark:border-brand">
                        <h4 class="font-medium text-brand dark:text-brand mb-2 flex items-center">
                            <i aria-hidden="true" class="fas fa-envelope mr-2"></i>
                            Email Notifications
                        </h4>
                        <div class="text-sm text-brand dark:text-brand space-y-1">
                            <p>Course updates: {{ $email_course_updates ? '✓ Enabled' : '✗ Disabled' }}</p>
                            <p>Assignments: {{ $email_assignment_reminders ? '✓ Enabled' : '✗ Disabled' }}</p>
                            <p>Certificates: {{ $email_certificate_notifications ? '✓ Enabled' : '✗ Disabled' }}</p>
                            <p>Weekly digest: {{ $email_weekly_digest ? '✓ Enabled' : '✗ Disabled' }}</p>
                        </div>
                    </div>

                    <!-- Push Summary -->
                    <div class="bg-green-100/30 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                        <h4 class="font-medium text-green-900 dark:text-green-100 mb-2 flex items-center">
                            <i aria-hidden="true" class="fas fa-mobile-alt mr-2"></i>
                            Push Notifications
                        </h4>
                        <div class="text-sm text-green-800 dark:text-green-200 space-y-1">
                            <p>Course updates: {{ $push_course_updates ? '✓ Enabled' : '✗ Disabled' }}</p>
                            <p>Assignments: {{ $push_assignment_reminders ? '✓ Enabled' : '✗ Disabled' }}</p>
                            <p>Achievements: {{ $push_achievement_notifications ? '✓ Enabled' : '✗ Disabled' }}</p>
                            <p>Messages: {{ $push_direct_messages ? '✓ Enabled' : '✗ Disabled' }}</p>
                        </div>
                    </div>

                    <!-- Timing Summary -->
                    <div class="bg-orange-100/30 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-800">
                        <h4 class="font-medium text-orange-900 dark:text-orange-100 mb-2 flex items-center">
                            <i aria-hidden="true" class="fas fa-clock mr-2"></i>
                            Timing Settings
                        </h4>
                        <div class="text-sm text-orange-800 dark:text-orange-200 space-y-1">
                            <p>Digest: {{ ucfirst($digest_frequency) }}</p>
                            <p>Reminders: {{ str_replace('_', ' ', $reminder_frequency) }}</p>
                            <p>Quiet hours: {{ $quiet_hours_start }} - {{ $quiet_hours_end }}</p>
                            <p>Weekends: {{ $weekend_notifications ? '✓ Allowed' : '✗ Blocked' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Global Actions -->
                <div class="mt-6 pt-6 border-t border-themed-primary">
                    <div class="flex flex-wrap gap-3">
                        <button wire:click="enableAllEmail"
                            class="px-4 py-2 bg-brand/10 dark:bg-action/30 text-brand dark:text-brand rounded-lg hover:bg-indigo-200 dark:hover:bg-action/50 transition-colors text-sm font-medium">
                            <i aria-hidden="true" class="fas fa-check-circle mr-2"></i>Enable All Email
                        </button>
                        <button wire:click="enableAllPush"
                            class="px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors text-sm font-medium">
                            <i aria-hidden="true" class="fas fa-check-circle mr-2"></i>Enable All Push
                        </button>
                        <button wire:click="disableAllEmail"
                            class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors text-sm font-medium">
                            <i aria-hidden="true" class="fas fa-times-circle mr-2"></i>Disable All Email
                        </button>
                        <button wire:click="disableAllPush"
                            class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors text-sm font-medium">
                            <i aria-hidden="true" class="fas fa-times-circle mr-2"></i>Disable All Push
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
