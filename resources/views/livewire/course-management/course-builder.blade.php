<div x-data="simpleBuilder()" x-init="init()" class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Loading Overlay -->
    <div x-show="isLoading" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center gap-4 shadow-xl">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="text-gray-800 dark:text-white">Loading...</span>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 max-w-full">
        <!-- Toolbar -->
        <livewire:course-management.course-builder.toolbar :course="$course" wire:key="toolbar-{{ $course->id }}" />

        <!-- Main Content Grid - Responsive -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 mt-6">
            <!-- Course Outline Sidebar -->
            <div class="xl:col-span-1 order-2 xl:order-1">
                <div class="sticky top-6">
                    <livewire:course-management.course-builder.course-outline :course="$course" :activeSectionId="$activeSectionId"
                        :activeLessonId="$activeContentId" wire:key="outline-{{ $course->id }}-{{ $activeSectionId }}" />
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="xl:col-span-3 order-1 xl:order-2 min-w-0">
                <div class="animate__animated animate__fadeIn">
                    @if ($activeContentType === 'lesson' && $activeContentId)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <livewire:course-management.course-builder.lesson-editor :lessonId="$activeContentId"
                                wire:key="lesson-editor-{{ $activeContentId }}" />
                        </div>
                    @else
                        <!-- Enhanced Empty State -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 lg:p-12 text-center transition-colors duration-300">
                            <div class="max-w-md mx-auto">
                                <div class="w-20 h-20 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-book-open text-3xl text-blue-400"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3 transition-colors duration-300">Get Started</h3>
                                @if ($course->sections->count() === 0)
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-300">
                                        Create your first section to start building your course content.
                                    </p>
                                @elseif($course->sections->sum(fn($section) => $section->lessons->count()) === 0)
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-300">
                                        Your course has sections but no lessons yet. Add your first lesson to begin.
                                    </p>
                                @else
                                    <p class="text-gray-600 dark:text-gray-400 transition-colors duration-300">
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

    <script>
        function simpleBuilder() {
            return {
                isLoading: false,
                
                init() {
                    this.setupLivewireListeners();
                },

                setupLivewireListeners() {
                    Livewire.on('show-loading', () => {
                        this.isLoading = true;
                    });

                    Livewire.on('hide-loading', () => {
                        this.isLoading = false;
                    });

                    Livewire.on('lesson-selected', () => {
                        // Handle lesson selection
                    });
                }
            };
        }
    </script>
</div>