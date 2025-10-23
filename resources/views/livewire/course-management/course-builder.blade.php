<div x-data="simpleBuilder()" x-init="init()" class="min-h-screen bg-themed-primary transition-colors duration-300">
    <!-- Loading Overlay -->
    <div x-show="isLoading" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-themed-secondary rounded-lg p-6 flex items-center gap-4 shadow-xl border border-themed-primary transition-colors duration-300">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-accent-themed-primary"></div>
            <span class="text-themed-primary transition-colors duration-300">Loading...</span>
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
                    <livewire:course-management.course-builder.course-outline 
                        :course="$course" 
                        :activeSectionId="$activeSectionId"
                        :activeLessonId="$activeContentId" 
                        wire:key="outline-{{ $course->id }}" />
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="xl:col-span-3 order-1 xl:order-2 min-w-0">
                @if ($activeContentType === 'lesson' && $activeContentId)
                    <!-- Show Lesson Editor -->
                    <div class="animate__animated animate__fadeIn">
                        <div class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary transition-colors duration-300">
                            <livewire:course-management.course-builder.lesson-editor 
                                :lessonId="$activeContentId"
                                :key="'lesson-editor-' . $activeContentId" />
                        </div>
                    </div>
                @else
                    <!-- Enhanced Empty State -->
                    <div class="animate__animated animate__fadeIn">
                        <div class="bg-themed-secondary rounded-lg border border-themed-primary p-8 lg:p-12 text-center transition-colors duration-300">
                            <div class="max-w-md mx-auto">
                                <div class="w-20 h-20 bg-accent-themed-primary/20 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300">
                                    <i class="fas fa-book-open text-3xl text-accent-themed-primary"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-themed-primary mb-3 transition-colors duration-300">Get Started</h3>
                                @if ($course->sections->count() === 0)
                                    <p class="text-themed-secondary mb-4 transition-colors duration-300">
                                        Create your first section to start building your course content.
                                    </p>
                                @elseif($course->sections->sum(fn($section) => $section->lessons->count()) === 0)
                                    <p class="text-themed-secondary mb-4 transition-colors duration-300">
                                        Your course has sections but no lessons yet. Add your first lesson to begin.
                                    </p>
                                @else
                                    <p class="text-themed-secondary transition-colors duration-300">
                                        Select a lesson from the course outline to begin editing its content.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
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
                        this.isLoading = true;
                        setTimeout(() => {
                            this.isLoading = false;
                        }, 300);
                    });
                }
            };
        }
    </script>
</div>