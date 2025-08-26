<div x-data="courseBuilderManager()" x-init="init()">
    <div class="bg-gray-800 p-4 sm:p-6 lg:p-8 rounded-2xl">
        <!-- Polling Indicator -->
        <div x-show="showPollingIndicator" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full" class="fixed top-4 right-4 z-50">
            <div class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2">
                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                <span class="text-sm">Updates available</span>
                <button @click="refreshData()" class="text-white hover:text-blue-200 ml-2">
                    <i class="fas fa-sync-alt text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Toolbar -->
        <livewire:course-management.course-builder.toolbar :course="$course" wire:key="toolbar-{{ $course->id }}" />

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
            <!-- Course Outline Sidebar -->
            <div class="lg:col-span-1">
                <livewire:course-management.course-builder.course-outline :course="$course" :activeSectionId="$activeSectionId"
                    :activeLessonId="$activeContentId" wire:key="outline-{{ $course->id }}-{{ $activeSectionId }}" />
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-3">
                <div class="animate__animated animate__fadeIn">
                    @if ($activeContentType === 'lesson' && $activeContentId)
                        <livewire:course-management.course-builder.lesson-editor :lessonId="$activeContentId"
                            wire:key="lesson-editor-{{ $activeContentId }}" />
                    @else
                        <!-- Empty State -->
                        <div class="bg-gray-800 rounded-xl border-2 border-dashed border-gray-700 p-12 text-center">
                            <div class="max-w-md mx-auto">
                                <div
                                    class="w-20 h-20 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-book-open text-3xl text-blue-400"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-white mb-3">Get Started</h3>
                                @if ($course->sections->count() === 0)
                                    <p class="text-gray-400 mb-4">
                                        Create your first section to start building your course content.
                                    </p>
                                    <button
                                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Your First Section
                                    </button>
                                @elseif($course->sections->sum(fn($section) => $section->lessons->count()) === 0)
                                    <p class="text-gray-400 mb-4">
                                        Your course has sections but no lessons yet. Add your first lesson to begin.
                                    </p>
                                    <button
                                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Your First Lesson
                                    </button>
                                @else
                                    <p class="text-gray-400">
                                        Select a lesson from the course outline to begin editing its content.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Panel (Optional) -->
    <div x-show="showSettings" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="fixed bottom-4 right-4 bg-gray-800 border border-gray-700 rounded-lg p-4 shadow-xl z-40">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-white font-medium">Real-time Settings</h4>
            <button @click="showSettings = false" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-gray-300">Auto-refresh</span>
                <button @click="togglePolling()" :class="pollingEnabled ? 'bg-green-600' : 'bg-gray-600'"
                    class="w-10 h-5 rounded-full relative transition-colors">
                    <div :class="pollingEnabled ? 'translate-x-5' : 'translate-x-1'"
                        class="w-3 h-3 bg-white rounded-full transform transition-transform absolute top-1"></div>
                </button>
            </div>
            <div class="text-xs text-gray-400">
                Last activity: <span x-text="formatTime(lastActivity)"></span>
            </div>
        </div>
    </div>

    <!-- Floating Settings Button -->
    <button @click="showSettings = !showSettings"
        class="fixed bottom-4 left-4 bg-gray-700 hover:bg-gray-600 text-white w-10 h-10 rounded-full 
                   flex items-center justify-center shadow-lg transition-colors z-30">
        <i class="fas fa-cog"></i>
    </button>

    <script>
        function courseBuilderManager() {
            return {
                pollingEnabled: @js($enablePolling ?? true),
                lastActivity: Date.now(),
                showPollingIndicator: false,
                showSettings: false,
                pollingInterval: null,
                activityTimeout: null,

                init() {
                    this.setupPolling();
                    this.setupActivityTracking();
                    this.setupLivewireListeners();
                },

                setupPolling() {
                    if (this.pollingEnabled) {
                        // Poll every 30 seconds
                        this.pollingInterval = setInterval(() => {
                            this.checkForUpdates();
                        }, 30000);
                    }
                },

                setupActivityTracking() {
                    // Track various user activities
                    const activities = ['click', 'keydown', 'scroll', 'mousemove'];

                    activities.forEach(event => {
                        document.addEventListener(event, this.throttle(() => {
                            this.recordActivity();
                        }, 5000)); // Throttle to once per 5 seconds
                    });

                    // Track form inputs specifically
                    document.addEventListener('input', (e) => {
                        if (e.target.matches('input, textarea, select')) {
                            this.recordActivity();
                        }
                    });
                },

                setupLivewireListeners() {
                    // Listen for Livewire events
                    Livewire.on('course-data-updated', () => {
                        this.showUpdateIndicator();
                    });

                    // Track activity on Livewire actions
                    document.addEventListener('livewire:init', () => {
                        Livewire.hook('morph.updated', () => {
                            this.recordActivity();
                        });
                    });
                },

                recordActivity() {
                    this.lastActivity = Date.now();

                    // Signal to Livewire components
                    this.$wire.dispatch('user-activity');

                    // Clear any pending activity timeout
                    if (this.activityTimeout) {
                        clearTimeout(this.activityTimeout);
                    }

                    // Hide update indicator after activity
                    this.showPollingIndicator = false;
                },

                checkForUpdates() {
                    // Only check if user hasn't been active recently
                    const timeSinceActivity = Date.now() - this.lastActivity;

                    if (timeSinceActivity > 30000) { // 30 seconds
                        this.$wire.pollForUpdates();
                    }
                },

                showUpdateIndicator() {
                    // Don't show if user is currently active
                    const timeSinceActivity = Date.now() - this.lastActivity;

                    if (timeSinceActivity > 10000) { // 10 seconds
                        this.showPollingIndicator = true;
                    }
                },

                refreshData() {
                    this.showPollingIndicator = false;
                    this.$wire.refreshCourse();
                    this.recordActivity();
                },

                togglePolling() {
                    this.pollingEnabled = !this.pollingEnabled;

                    if (this.pollingEnabled) {
                        this.setupPolling();
                    } else {
                        if (this.pollingInterval) {
                            clearInterval(this.pollingInterval);
                        }
                    }

                    // Signal to Livewire
                    this.$wire.dispatch('toggle-polling', this.pollingEnabled);
                },

                formatTime(timestamp) {
                    const now = Date.now();
                    const diff = Math.floor((now - timestamp) / 1000);

                    if (diff < 60) return `${diff}s ago`;
                    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
                    return `${Math.floor(diff / 3600)}h ago`;
                },

                // Utility function for throttling
                throttle(func, delay) {
                    let timeoutId;
                    let lastExecTime = 0;

                    return function(...args) {
                        const currentTime = Date.now();

                        if (currentTime - lastExecTime > delay) {
                            func.apply(this, args);
                            lastExecTime = currentTime;
                        } else {
                            clearTimeout(timeoutId);
                            timeoutId = setTimeout(() => {
                                func.apply(this, args);
                                lastExecTime = Date.now();
                            }, delay - (currentTime - lastExecTime));
                        }
                    };
                }
            };
        }
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate__fadeIn {
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Smooth transitions for all interactive elements */
        .transition-all {
            transition: all 0.3s ease;
        }

        /* Polling indicator animations */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }
    </style>

</div>
