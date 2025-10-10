<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Document Settings</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Configure document management and publishing preferences</p>
            </div>
            
            <button 
                wire:click="resetToDefaults"
                class="text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors flex items-center space-x-2"
            >
                <i class="fas fa-undo"></i>
                <span>Reset to Defaults</span>
            </button>
        </div>
    </div>

    <!-- General Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-cog text-indigo-600 dark:text-indigo-400 mr-2"></i>
                General Settings
            </h3>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Auto Publish -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            wire:model="generalSettings.auto_publish"
                            type="checkbox" 
                            id="auto_publish"
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700"
                        >
                    </div>
                    <div class="ml-3">
                        <label for="auto_publish" class="font-medium text-gray-700 dark:text-gray-300">Auto Publish</label>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Automatically publish documents after creation</p>
                    </div>
                </div>

                <!-- Require Review -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            wire:model="generalSettings.require_review"
                            type="checkbox" 
                            id="require_review"
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700"
                        >
                    </div>
                    <div class="ml-3">
                        <label for="require_review" class="font-medium text-gray-700 dark:text-gray-300">Require Review</label>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Documents need approval before publishing</p>
                    </div>
                </div>

                <!-- Allow Comments -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            wire:model="generalSettings.allow_comments"
                            type="checkbox" 
                            id="allow_comments"
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700"
                        >
                    </div>
                    <div class="ml-3">
                        <label for="allow_comments" class="font-medium text-gray-700 dark:text-gray-300">Allow Comments</label>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Enable comments on published documents</p>
                    </div>
                </div>

                <!-- Enable Versioning -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            wire:model="generalSettings.enable_versioning"
                            type="checkbox" 
                            id="enable_versioning"
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700"
                        >
                    </div>
                    <div class="ml-3">
                        <label for="enable_versioning" class="font-medium text-gray-700 dark:text-gray-300">Enable Versioning</label>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Track document version history</p>
                    </div>
                </div>

                <!-- Default Visibility -->
                <div class="md:col-span-2">
                    <label for="default_visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Default Visibility
                    </label>
                    <select 
                        wire:model="generalSettings.default_visibility"
                        id="default_visibility"
                        class="w-full md:w-1/2 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300"
                    >
                        @foreach($visibilityOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Default visibility for new documents</p>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button 
                    wire:click="saveGeneralSettings"
                    class="bg-indigo-600 dark:bg-indigo-500 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-save"></i>
                    <span wire:loading.remove wire:target="saveGeneralSettings">Save General Settings</span>
                    <span wire:loading wire:target="saveGeneralSettings" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-shield-alt text-green-600 dark:text-green-400 mr-2"></i>
                Security Settings
            </h3>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Max File Size -->
                <div>
                    <label for="max_file_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Maximum File Size (MB)
                    </label>
                    <input 
                        wire:model="securitySettings.max_file_size"
                        type="number" 
                        id="max_file_size"
                        min="1"
                        max="100"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300"
                    >
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Maximum size for file uploads (1-100 MB)</p>
                </div>

                <!-- Allowed File Types -->
                <div>
                    <label for="allowed_file_types" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Allowed File Types
                    </label>
                    <input 
                        wire:model="securitySettings.allowed_file_types"
                        type="text" 
                        id="allowed_file_types"
                        placeholder="pdf,doc,docx,txt,md"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                    >
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Comma-separated list of allowed file extensions</p>
                </div>
            </div>

            <!-- Allowed Types Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 transition-colors duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-300">Supported File Types</h4>
                        <div class="mt-2 text-sm text-blue-800 dark:text-blue-300">
                            <p>Recommended file types: <code class="bg-blue-100 dark:bg-blue-900/40 px-1 py-0.5 rounded">pdf, doc, docx, txt, md, xls, xlsx, ppt, pptx</code></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button 
                    wire:click="saveSecuritySettings"
                    class="bg-green-600 dark:bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-save"></i>
                    <span wire:loading.remove wire:target="saveSecuritySettings">Save Security Settings</span>
                    <span wire:loading wire:target="saveSecuritySettings" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-bell text-yellow-600 dark:text-yellow-400 mr-2"></i>
                Notification Settings
            </h3>
        </div>

        <div class="p-6 space-y-6">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        wire:model="notificationSettings.enable_notifications"
                        type="checkbox" 
                        id="enable_notifications"
                        class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700"
                    >
                </div>
                <div class="ml-3">
                    <label for="enable_notifications" class="font-medium text-gray-700 dark:text-gray-300">Enable Email Notifications</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Receive email notifications for document activities</p>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3 transition-colors duration-300">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">You will receive notifications for:</p>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 dark:text-green-400 mr-2"></i>
                        New document submissions for review
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 dark:text-green-400 mr-2"></i>
                        Document approval/rejection status
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 dark:text-green-400 mr-2"></i>
                        Comments on your documents
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 dark:text-green-400 mr-2"></i>
                        Document version updates
                    </li>
                </ul>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button 
                    wire:click="saveNotificationSettings"
                    class="bg-yellow-600 dark:bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 dark:hover:bg-yellow-600 transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-save"></i>
                    <span wire:loading.remove wire:target="saveNotificationSettings">Save Notification Settings</span>
                    <span wire:loading wire:target="saveNotificationSettings" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Backup Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-database text-purple-600 dark:text-purple-400 mr-2"></i>
                Backup & Retention Settings
            </h3>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Backup Frequency -->
                <div>
                    <label for="backup_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Backup Frequency
                    </label>
                    <select 
                        wire:model="backupSettings.backup_frequency"
                        id="backup_frequency"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300"
                    >
                        @foreach($backupFrequencyOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">How often to backup documents</p>
                </div>

                <!-- Retention Period -->
                <div>
                    <label for="retention_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Retention Period (Days)
                    </label>
                    <input 
                        wire:model="backupSettings.retention_period"
                        type="number" 
                        id="retention_period"
                        min="30"
                        max="3650"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300"
                    >
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">How long to keep document backups (30-3650 days)</p>
                </div>
            </div>

            <!-- Backup Info -->
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4 transition-colors duration-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-purple-900 dark:text-purple-300">Backup Information</h4>
                        <div class="mt-2 text-sm text-purple-800 dark:text-purple-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Automated backups run according to the selected frequency</li>
                                <li>Backups include document content, metadata, and attachments</li>
                                <li>Old backups are automatically deleted after retention period</li>
                                <li>Manual backups can be triggered at any time</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                <button 
                    wire:click="triggerManualBackup"
                    class="bg-gray-600 dark:bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-download"></i>
                    <span>Trigger Manual Backup</span>
                </button>

                <button 
                    wire:click="saveBackupSettings"
                    class="bg-purple-600 dark:bg-purple-500 text-white px-6 py-2 rounded-lg hover:bg-purple-700 dark:hover:bg-purple-600 transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-save"></i>
                    <span wire:loading.remove wire:target="saveBackupSettings">Save Backup Settings</span>
                    <span wire:loading wire:target="saveBackupSettings" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-2"></i>
                System Information
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 transition-colors duration-300">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Document Management Version</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">v2.5.0</div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 transition-colors duration-300">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Storage Used</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">2.3 GB</div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 transition-colors duration-300">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Last Backup</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">2 hours ago</div>
                </div>
            </div>
        </div>
    </div>
</div>