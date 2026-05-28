<div x-data="toolbarManager()" x-init="init()"
    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6 transition-colors duration-300"
    role="region" aria-label="Course Management Toolbar">

    <!-- Global Notifications with Queue -->
    <div id="global-notifications" class="fixed top-4 right-4 z-50 space-y-2 max-w-md">
        <template x-for="notification in notificationQueue" :key="notification.id">
            <div x-show="notification.visible" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 class="px-6 py-3 rounded-lg shadow-lg text-white"
                 :class="getNotificationClasses(notification.type)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i :class="getNotificationIcon(notification.type)" class="mr-3"></i>
                        <span class="flex-1" x-text="notification.message"></span>
                    </div>
                    <button @click="dismissNotification(notification.id)" 
                            class="ml-4 text-white hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-white/10">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Progress bar for actions in progress -->
                <div x-show="notification.progress !== undefined" class="mt-2">
                    <div class="w-full bg-white/20 rounded-full h-1">
                        <div class="bg-white h-1 rounded-full transition-all duration-300" 
                             :style="`width: ${notification.progress}%`"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Data Conflict Warning -->
    <div x-show="showConflictWarning" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mt-1"></i>
            <div class="flex-1">
                <h4 class="font-medium text-red-800 dark:text-red-200 mb-1">Course Data Conflict</h4>
                <p class="text-sm text-red-700 dark:text-red-300 mb-3">
                    Another user has modified this course. Your current changes might conflict with theirs.
                </p>
                <div class="flex gap-2">
                    <button @click="resolveConflict('keep')" 
                            class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors">
                        Keep My Changes
                    </button>
                    <button @click="resolveConflict('reload')" 
                            class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700 transition-colors">
                        Reload Course
                    </button>
                </div>
            </div>
            <button @click="showConflictWarning = false" 
                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Course Title and Status -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="bg-purple-600 dark:bg-purple-700 p-2 rounded-lg">
                    <i class="fas fa-book-open text-white"></i>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white truncate max-w-[180px] sm:max-w-[280px] transition-colors duration-300">
                    {{ $course->title ?? 'Untitled Course' }}
                </h2>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full transition-all duration-300 flex items-center border
                    {{ $course->is_published ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-700' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-700' }}"
                    aria-live="polite"
                    wire:key="status-{{ $course->id }}-{{ $course->is_published ? 'published' : 'draft' }}">
                    <span class="w-2 h-2 rounded-full mr-2 {{ $course->is_published ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    {{ $course->is_published ? 'Published' : 'Draft' }}
                </span>

                <!-- Pricing Badge -->
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full flex items-center border
                    {{ $course->is_free ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-700' :
    ($course->is_premium ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700' :
        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-700') }} transition-colors duration-300">
                    <i class="fas {{ $course->is_free ? 'fa-gift' : ($course->is_premium ? 'fa-crown' : 'fa-dollar-sign') }} mr-2"></i>
                    {{ $course->formatted_price }}
                </span>

                <!-- Save Status Indicator -->
                <span x-show="saveStatus !== 'saved'" 
                      class="px-3 py-1.5 text-xs font-semibold rounded-full flex items-center border transition-all duration-300"
                      :class="saveStatus === 'saving' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-700' :
                              saveStatus === 'error' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-700' :
                              'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300 border-gray-200 dark:border-gray-700'">
                    <i class="mr-2" :class="saveStatus === 'saving' ? 'fas fa-spinner fa-spin' : 'fas fa-exclamation-triangle'"></i>
                    <span x-text="saveStatus === 'saving' ? 'Saving...' : saveStatus === 'error' ? 'Save Failed' : 'Unsaved'"></span>
                </span>
            </div>
        </div>

        <!-- Enhanced Actions -->
        <div class="flex items-center gap-2">
            <!-- Auto-save Toggle -->
            <button @click="toggleAutoSave()" :class="autoSaveEnabled ? 'bg-green-600' : 'bg-gray-400'"
                    class="px-3 py-2 text-white rounded-lg transition-colors duration-300 text-sm flex items-center gap-2"
                    :title="autoSaveEnabled ? 'Auto-save enabled' : 'Auto-save disabled'">
                <i class="fas fa-bolt"></i>
                <span class="hidden sm:inline">Auto</span>
            </button>

            <a href="{{ route('courses.preview', $course) }}" target="_blank"
                class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 
                transition-all duration-300 flex items-center shadow-lg hover:shadow-purple-500/20">
                <i class="fas fa-eye mr-2"></i>
                Preview Course
            </a>

            <!-- Pricing Button with Loading State -->
            <button @click="openPricingModal()" :disabled="isPerformingAction" 
                    class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 
                    transition-all duration-300 flex items-center shadow-lg hover:shadow-purple-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-tag mr-2" :class="{ 'fa-spinner fa-spin': isPerformingAction && actionType === 'pricing' }"></i>
                Pricing
            </button>

            <!-- Enhanced Publish/Unpublish Button -->
            <button @click="handlePublishToggle()" :disabled="isPerformingAction"
                    class="px-4 py-2.5 bg-gradient-to-r transition-all duration-300 flex items-center shadow-lg disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg"
                    :class="course.is_published ? 'from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 hover:shadow-blue-500/20' : 'from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 hover:shadow-gray-500/20'"
                    wire:key="toggle-btn-{{ $course->id }}-{{ $course->is_published ? 1 : 0 }}">

                <template x-if="!isPerformingAction || actionType !== 'publish'">
                    <div class="flex items-center">
                        <i class="mr-2" :class="course.is_published ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        <span x-text="course.is_published ? 'Unpublish' : 'Publish'"></span>
                    </div>
                </template>

                <template x-if="isPerformingAction && actionType === 'publish'">
                    <div class="flex items-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-text="'Processing...'"></span>
                    </div>
                </template>
            </button>
        </div>
    </div>

    <!-- Enhanced Stats -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl flex items-center space-x-3 border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-500 transition-all duration-300"
            wire:key="sections-count-{{ $sectionCount }}" wire:transition>
            <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                <i class="fas fa-folder-open text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider transition-colors duration-300">Sections</span>
                <span class="block text-xl font-bold text-gray-800 dark:text-white transition-colors duration-300">{{ $sectionCount }}</span>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl flex items-center space-x-3 border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-500 transition-all duration-300"
            wire:key="lessons-count-{{ $lessonCount }}" wire:transition>
            <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
                <i class="fas fa-book text-green-600 dark:text-green-400 text-lg"></i>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider transition-colors duration-300">Lessons</span>
                <span class="block text-xl font-bold text-gray-800 dark:text-white transition-colors duration-300">{{ $lessonCount }}</span>
            </div>
        </div>
    </div>

    <!-- Enhanced Pricing Modal -->
    @if($showPricingModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 border border-gray-200 dark:border-gray-700 shadow-2xl transition-colors duration-300">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center transition-colors duration-300">
                        <i class="fas fa-tag text-purple-600 dark:text-purple-400 mr-2"></i>
                        Course Pricing
                    </h3>
                    <button @click="closePricingModal()"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Loading state for modal -->
                <div x-show="modalLoading" class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 dark:text-gray-400">Updating pricing...</p>
                </div>

                <form @submit.prevent="handlePricingUpdate()" x-show="!modalLoading" class="space-y-5">
                    <!-- Free Course Toggle -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg transition-colors duration-300">
                        <div class="flex items-center">
                            <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg mr-3">
                                <i class="fas fa-gift text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <label for="modal_is_free"
                                    class="block text-sm font-medium text-gray-800 dark:text-gray-200 transition-colors duration-300">
                                    Free Course
                                </label>
                                <p class="text-xs text-gray-600 dark:text-gray-400 transition-colors duration-300">Accessible to everyone</p>
                            </div>
                        </div>
                        <div class="relative inline-block w-12 h-6">
                            <input type="checkbox" wire:model.live="is_free" id="modal_is_free" class="sr-only">
                            <div class="w-12 h-6 rounded-full transition-all duration-300 ease-in-out"
                                :class="$wire.is_free ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 rounded-full transition-transform duration-300 ease-in-out bg-white"
                                :class="{ 'transform translate-x-6': $wire.is_free }"></div>
                        </div>
                    </div>

                    <!-- Premium Toggle -->
                    <div x-show="!$wire.is_free" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg transition-colors duration-300">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 dark:bg-yellow-900/30 p-2 rounded-lg mr-3">
                                <i class="fas fa-crown text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                            <div>
                                <label for="modal_is_premium"
                                    class="block text-sm font-medium text-gray-800 dark:text-gray-200 transition-colors duration-300">
                                    Premium Course
                                </label>
                                <p class="text-xs text-gray-600 dark:text-gray-400 transition-colors duration-300">Special features</p>
                            </div>
                        </div>
                        <div class="relative inline-block w-12 h-6">
                            <input type="checkbox" wire:model="is_premium" id="modal_is_premium" class="sr-only">
                            <div class="w-12 h-6 rounded-full transition-all duration-300 ease-in-out"
                                :class="$wire.is_premium ? 'bg-yellow-500' : 'bg-gray-300 dark:bg-gray-600'"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 rounded-full transition-transform duration-300 ease-in-out bg-white"
                                :class="{ 'transform translate-x-6': $wire.is_premium }"></div>
                        </div>
                    </div>

                    <!-- Price Input -->
                    <div x-show="!$wire.is_free">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Course Price ($)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400">$</span>
                            </div>
                            <input type="number" wire:model="price" min="0" step="0.01"
                                class="w-full pl-8 pr-4 py-2.5 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300">
                        </div>
                        @error('price')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1 flex items-center transition-colors duration-300">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="closePricingModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition-colors duration-300">
                            Cancel
                        </button>
                        <button type="submit" :disabled="isPerformingAction"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                            text-white rounded-lg transition-all duration-300 disabled:opacity-50 flex items-center justify-center">
                            <span x-show="!isPerformingAction || actionType !== 'pricing'" class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i> Update
                            </span>
                            <span x-show="isPerformingAction && actionType === 'pricing'" class="flex items-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function toolbarManager() {
            return {
                // State management
                saveStatus: 'saved', // 'saved', 'saving', 'error', 'unsaved'
                autoSaveEnabled: true,
                isPerformingAction: false,
                actionType: '', // 'publish', 'pricing', etc.
                modalLoading: false,
                
                // Conflict resolution
                showConflictWarning: false,
                courseChecksum: null,
                
                // Notifications
                notificationQueue: [],
                nextNotificationId: 1,
                
                // Course data (reactive)
                course: @js($course->only(['id', 'is_published', 'is_free', 'is_premium', 'price'])),

                init() {
                    this.setupEventListeners();
                    this.setupAutoSave();
                    this.initializeCourseChecksum();
                },

                setupEventListeners() {
                    // Listen for Livewire events
                    Livewire.on('pricing-updated', (data) => {
                        this.handlePricingUpdated(data);
                    });

                    Livewire.on('publish-status-changed', (data) => {
                        this.handlePublishStatusChanged(data);
                    });

                    Livewire.on('course-conflict-detected', () => {
                        this.showConflictWarning = true;
                    });

                    Livewire.on('show-notification', (data) => {
                        this.showNotification(data.message, data.type, data.duration);
                    });

                    // Handle form changes
                    document.addEventListener('input', (e) => {
                        if (e.target.closest('[data-auto-save]')) {
                            this.markUnsaved();
                            this.scheduleAutoSave();
                        }
                    });
                },

                setupAutoSave() {
                    this.autoSaveInterval = setInterval(() => {
                        if (this.autoSaveEnabled && this.saveStatus === 'unsaved') {
                            this.performAutoSave();
                        }
                    }, 30000); // Auto-save every 30 seconds
                },

                initializeCourseChecksum() {
                    this.courseChecksum = this.generateChecksum(this.course);
                },

                generateChecksum(data) {
                    return btoa(JSON.stringify(data));
                },

                // Action handlers
                async handlePublishToggle() {
                    if (this.isPerformingAction) return;
                    
                    this.isPerformingAction = true;
                    this.actionType = 'publish';
                    
                    try {
                        const action = this.course.is_published ? 'unpublish' : 'publish';
                        this.showNotification(`${action === 'publish' ? 'Publishing' : 'Unpublishing'} course...`, 'info');
                        
                        await this.$wire.call('togglePublished');
                        
                    } catch (error) {
                        this.showNotification('Failed to update publish status', 'error');
                        console.error('Publish toggle failed:', error);
                    } finally {
                        this.isPerformingAction = false;
                        this.actionType = '';
                    }
                },

                async handlePricingUpdate() {
                    if (this.isPerformingAction) return;
                    
                    this.isPerformingAction = true;
                    this.actionType = 'pricing';
                    this.modalLoading = true;
                    
                    try {
                        await this.$wire.call('updatePricing');
                    } catch (error) {
                        this.showNotification('Failed to update pricing', 'error');
                        console.error('Pricing update failed:', error);
                    } finally {
                        this.isPerformingAction = false;
                        this.actionType = '';
                        this.modalLoading = false;
                    }
                },

                // Event handlers
                handlePricingUpdated(data) {
                    this.course = { ...this.course, ...data };
                    this.closePricingModal();
                    this.showNotification('Pricing updated successfully', 'success');
                    this.updateChecksum();
                },

                handlePublishStatusChanged(data) {
                    this.course = { ...this.course, ...data };
                    const message = data.is_published ? 'Course published successfully' : 'Course unpublished successfully';
                    this.showNotification(message, 'success');
                    this.updateChecksum();
                },

                updateChecksum() {
                    this.courseChecksum = this.generateChecksum(this.course);
                },

                // Modal management
                openPricingModal() {
                    if (this.isPerformingAction) return;
                    this.$wire.set('showPricingModal', true);
                },

                closePricingModal() {
                    this.$wire.set('showPricingModal', false);
                    this.modalLoading = false;
                },

                // Save state management
                markUnsaved() {
                    if (this.saveStatus === 'saved') {
                        this.saveStatus = 'unsaved';
                    }
                },

                markSaving() {
                    this.saveStatus = 'saving';
                },

                markSaved() {
                    this.saveStatus = 'saved';
                },

                markError() {
                    this.saveStatus = 'error';
                },

                async performAutoSave() {
                    if (!this.autoSaveEnabled || this.isPerformingAction) return;
                    
                    this.markSaving();
                    
                    try {
                        await this.$wire.call('autoSave');
                        this.markSaved();
                    } catch (error) {
                        this.markError();
                        console.error('Auto-save failed:', error);
                    }
                },

                scheduleAutoSave() {
                    clearTimeout(this.autoSaveTimeout);
                    this.autoSaveTimeout = setTimeout(() => {
                        if (this.autoSaveEnabled) {
                            this.performAutoSave();
                        }
                    }, 5000); // 5 second delay
                },

                toggleAutoSave() {
                    this.autoSaveEnabled = !this.autoSaveEnabled;
                    this.showNotification(
                        `Auto-save ${this.autoSaveEnabled ? 'enabled' : 'disabled'}`, 
                        'info', 
                        2000
                    );
                },

                // Conflict resolution
                async resolveConflict(action) {
                    this.showConflictWarning = false;
                    
                    switch (action) {
                        case 'keep':
                            // Force save current changes
                            await this.$wire.call('forceSave');
                            this.showNotification('Your changes have been saved', 'success');
                            break;
                        case 'reload':
                            // Reload course data
                            await this.$wire.call('refreshCourse');
                            this.showNotification('Course data reloaded', 'info');
                            break;
                    }
                },

                // Notification system
                showNotification(message, type = 'info', duration = 5000, progress = undefined) {
                    const notification = {
                        id: this.nextNotificationId++,
                        message,
                        type,
                        visible: true,
                        progress
                    };
                    
                    this.notificationQueue.push(notification);
                    
                    // Auto-dismiss after duration
                    if (duration > 0) {
                        setTimeout(() => {
                            this.dismissNotification(notification.id);
                        }, duration);
                    }
                    
                    // Limit queue size
                    if (this.notificationQueue.length > 3) {
                        this.notificationQueue.shift();
                    }
                },

                dismissNotification(id) {
                    const index = this.notificationQueue.findIndex(n => n.id === id);
                    if (index > -1) {
                        this.notificationQueue[index].visible = false;
                        setTimeout(() => {
                            this.notificationQueue.splice(index, 1);
                        }, 300); // Wait for exit animation
                    }
                },

                getNotificationClasses(type) {
                    const classes = {
                        success: 'bg-gradient-to-r from-green-600 to-green-700',
                        error: 'bg-gradient-to-r from-red-600 to-red-700',
                        warning: 'bg-gradient-to-r from-yellow-600 to-yellow-700',
                        info: 'bg-gradient-to-r from-blue-600 to-blue-700'
                    };
                    return classes[type] || classes.info;
                },

                getNotificationIcon(type) {
                    const icons = {
                        success: 'fas fa-check-circle',
                        error: 'fas fa-exclamation-triangle',
                        warning: 'fas fa-exclamation-circle',
                        info: 'fas fa-info-circle'
                    };
                    return icons[type] || icons.info;
                }
            };
        }
    </script>
</div>
