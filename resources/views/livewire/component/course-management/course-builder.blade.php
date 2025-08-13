<div>
<div class="bg-gray-900 text-white min-h-screen" x-data="courseBuilder()">
    <div class="min-h-screen p-4 sm:p-6 lg:p-8">
        <!-- Toolbar -->
        <livewire:component.course-management.course-builder.toolbar 
            :course="$course" 
            wire:key="toolbar-{{ $course->id }}" />

        <!-- Auto-save indicator (global) -->
        <div id="global-notifications" class="fixed top-4 right-4 z-50 space-y-2"></div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Course Outline Sidebar -->
            <div class="lg:col-span-1 order-2 lg:order-1">
                <livewire:component.course-management.course-builder.course-outline 
                    :course="$course" 
                    :activeLessonId="$activeLessonId"
                    wire:key="course-outline-{{ $course->id }}" />
            </div>

            <!-- Main Editor Area -->
            <div class="lg:col-span-3 order-1 lg:order-2">
                @if ($activeLessonId)
                    <livewire:component.course-management.course-builder.lesson-editor 
                        :lessonId="$activeLessonId"
                        wire:key="lesson-editor-{{ $activeLessonId }}" />

                    @if ($activeQuizId)
                        <livewire:component.course-management.course-builder.quiz-editor 
                            :quizId="$activeQuizId"
                            wire:key="quiz-editor-{{ $activeQuizId }}" />
                    @endif
                @elseif ($activeQuizId)
                    <livewire:component.course-management.course-builder.quiz-editor 
                        :quizId="$activeQuizId"
                        wire:key="quiz-editor-only-{{ $activeQuizId }}" />
                @else
                    <!-- Welcome State -->
                    <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl p-12 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-book-open text-2xl text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Ready to Create Amazing Content?</h3>
                            <p class="text-gray-400 mb-6">Select a lesson from the course outline to start writing your content. You can add text, images, videos, and files all in one place.</p>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="text-blue-400 font-bold text-2xl">{{ $course->sections->count() }}</div>
                                    <div class="text-gray-300">Sections</div>
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="text-green-400 font-bold text-2xl">
                                        {{ $course->sections->sum(function($section) { return $section->lessons->count(); }) }}
                                    </div>
                                    <div class="text-gray-300">Lessons</div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <button wire:click="$dispatch('show-add-section-form')"
                                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-semibold">
                                    <i class="fas fa-plus mr-2"></i> Create Your First Section
                                </button>
                                
                                <div class="grid grid-cols-3 gap-2">
                                    <button wire:click="$dispatch('show-quiz-modal')"
                                        class="flex flex-col items-center space-y-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-question-circle text-xl"></i>
                                        <span class="text-sm">Quiz</span>
                                    </button>
                                    
                                    <button wire:click="$dispatch('show-image-modal')"
                                        class="flex flex-col items-center space-y-2 px-4 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                                        <i class="fas fa-image text-xl"></i>
                                        <span class="text-sm">Image</span>
                                    </button>
                                    
                                    <button wire:click="$dispatch('show-video-modal')"
                                        class="flex flex-col items-center space-y-2 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-video text-xl"></i>
                                        <span class="text-sm">Video</span>
                                    </button>
                                </div>
                            </div>

                            <div class="text-sm text-gray-500 space-y-2 mt-6">
                                <p>💡 <strong>Tip:</strong> Use keyboard shortcuts to work faster</p>
                                <div class="flex justify-center space-x-4 text-xs">
                                    <span><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+S</kbd> Save</span>
                                    <span><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+N</kbd> New Section</span>
                                    <span><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+/</kbd> Search</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>





            @if ($showSettingsModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                x-data="{ activeTab: 'general' }">
                <div class="bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                    <div class="border-b border-gray-700 p-6 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">Course Settings</h3>
                        <button wire:click="closeSettingsModal" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
    
                    <!-- Tabs -->
                    <div class="border-b border-gray-700 px-6">
                        <div class="flex space-x-4">
                            <button @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'border-blue-500 text-blue-400' :
                                    'border-transparent text-gray-400 hover:text-white'"
                                class="py-4 px-2 border-b-2 font-medium text-sm">
                                General
                            </button>
                            <button @click="activeTab = 'access'"
                                :class="activeTab === 'access' ? 'border-blue-500 text-blue-400' :
                                    'border-transparent text-gray-400 hover:text-white'"
                                class="py-4 px-2 border-b-2 font-medium text-sm">
                                Access
                            </button>
                            <button @click="activeTab = 'media'"
                                :class="activeTab === 'media' ? 'border-blue-500 text-blue-400' :
                                    'border-transparent text-gray-400 hover:text-white'"
                                class="py-4 px-2 border-b-2 font-medium text-sm">
                                Media
                            </button>
                        </div>
                    </div>
    
                    <div class="p-6">
                        <!-- General Tab -->
                        <div x-show="activeTab === 'general'" class="space-y-6">
                            <div>
                                <label class="block text-gray-300 mb-2">Course Title*</label>
                                <input type="text" wire:model="course.title"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-blue-500">
                                @error('course.title')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div>
                                <label class="block text-gray-300 mb-2">Description*</label>
                                <textarea wire:model="course.description" rows="4"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-blue-500"></textarea>
                                @error('course.description')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-300 mb-2">Category*</label>
                                    <select wire:model="course.category_id"
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select a category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('course.category_id')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div>
                                    <label class="block text-gray-300 mb-2">Difficulty Level*</label>
                                    <select wire:model="course.difficulty_level"
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-blue-500">
                                        @foreach ($difficultyLevels as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('course.difficulty_level')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <!-- Access Tab -->
                        <div x-show="activeTab === 'access'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-300 mb-2">Price*</label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">$</span>
                                        <input type="number" wire:model="course.price" min="0" step="0.01"
                                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 pl-8 text-white focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    @error('course.price')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="flex items-center">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model="course.is_premium"
                                            class="rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-300">Premium Course</span>
                                    </label>
                                </div>
                            </div>
    
                            <div>
                                <label class="block text-gray-300 mb-2">Course Status</label>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="course.is_published" value="1"
                                            class="rounded-full bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-300">Published</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="course.is_published" value="0"
                                            class="rounded-full bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-300">Draft</span>
                                    </label>
                                </div>
                            </div>
                        </div>
    
                        <!-- Media Tab -->
                        <div x-show="activeTab === 'media'" class="space-y-6">
                            <div>
                                <label class="block text-gray-300 mb-2">Course Thumbnail</label>
                                <div class="flex items-center space-x-6">
                                    @if ($course->thumbnail)
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                                alt="Course thumbnail" class="w-32 h-32 object-cover rounded-lg">
                                            <button wire:click="$set('course.thumbnail', null)"
                                                class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    @endif
    
                                    <div class="flex-1">
                                        <input type="file" wire:model="thumbnail" id="thumbnail-upload"
                                            class="hidden">
                                        <label for="thumbnail-upload"
                                            class="cursor-pointer bg-gray-700 border border-gray-600 rounded-lg px-4 py-8 text-center block hover:bg-gray-600">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-blue-500 mb-2"></i>
                                            <p class="text-gray-300">Click to upload new thumbnail</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG (Max 2MB)</p>
                                        </label>
                                        @error('thumbnail')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        @if ($thumbnail)
                                            <p class="text-green-500 text-sm mt-2">New thumbnail selected:
                                                {{ $thumbnail->getClientOriginalName() }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="border-t border-gray-700 p-6 flex justify-end space-x-3">
                        <button wire:click="closeSettingsModal"
                            class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600">
                            Cancel
                        </button>
                        <button wire:click="saveCourseSettings" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <span wire:loading wire:target="saveCourseSettings">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                            </span>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        @endif 
        </div>

        <!-- Global Media Modals -->
        <livewire:component.course-management.course-builder.media-modals 
            wire:key="media-modals-{{ $course->id }}" />
    </div>

    <!-- JavaScript for enhanced functionality -->
    <script>
        function courseBuilder() {
            return {
                init() {
                    this.initializeKeyboardShortcuts();
                    this.initializeNotifications();
                    this.initializeAutoSave();
                },

                initializeKeyboardShortcuts() {
                    document.addEventListener('keydown', (e) => {
                        // Ctrl/Cmd + S to save
                        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                            e.preventDefault();
                            @this.dispatch('save-content');
                        }

                        // Ctrl/Cmd + N to add new section
                        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                            e.preventDefault();
                            @this.dispatch('show-add-section-form');
                        }

                        // Ctrl/Cmd + / to focus search
                        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                            e.preventDefault();
                            const searchInput = document.querySelector('input[placeholder*="Search"]');
                            if (searchInput) searchInput.focus();
                        }

                        // Escape to close modals
                        if (e.key === 'Escape') {
                            @this.dispatch('close-modals');
                        }
                    });
                },

                initializeNotifications() {
                    // Listen for notification events
                    Livewire.on('notify', (message, type = 'info') => {
                        this.showNotification(message, type);
                    });

                    Livewire.on('clear-message', (data) => {
                        setTimeout(() => {
                            @this.set(data.property, '');
                        }, data.delay || 3000);
                    });
                },

                initializeAutoSave() {
                    let autoSaveTimeout;
                    Livewire.on('schedule-auto-save', () => {
                        clearTimeout(autoSaveTimeout);
                        autoSaveTimeout = setTimeout(() => {
                            @this.dispatch('auto-save');
                        }, 3000);
                    });
                },

                showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `px-6 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full ${
                        type === 'success' ? 'bg-green-600' : 
                        type === 'error' ? 'bg-red-600' : 
                        type === 'warning' ? 'bg-yellow-600' : 
                        'bg-blue-600'
                    }`;
                    
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-${
                                type === 'success' ? 'check' : 
                                type === 'error' ? 'exclamation-triangle' : 
                                type === 'warning' ? 'exclamation-circle' : 
                                'info'
                            } mr-2"></i>
                            <span>${message}</span>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;

                    const container = document.getElementById('global-notifications');
                    container.appendChild(notification);

                    // Animate in
                    setTimeout(() => {
                        notification.classList.remove('translate-x-full');
                    }, 10);

                    // Auto remove after 5 seconds
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.classList.add('translate-x-full');
                            setTimeout(() => {
                                if (notification.parentNode) {
                                    notification.remove();
                                }
                            }, 300);
                        }
                    }, 5000);
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            window.courseBuilderInstance = courseBuilder();
            window.courseBuilderInstance.init();
        });

        // Reinitialize after Livewire navigation
        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => {
                if (window.courseBuilderInstance) {
                    window.courseBuilderInstance.init();
                }
            }, 100);
        });
    </script>

    @push('styles')
        <!-- Trix Editor Styles -->
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

        <style>
            /* Custom Trix Editor Styling for Dark Theme */
            trix-editor {
                background-color: #111827 !important;
                color: #f9fafb !important;
                border: 1px solid #4b5563 !important;
                border-radius: 0.5rem !important;
                min-height: 24rem !important;
                padding: 1.5rem !important;
            }

            trix-editor:focus {
                outline: none !important;
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            }

            /* Hide Trix toolbar since we have our own */
            trix-toolbar {
                display: none !important;
            }

            /* Custom scrollbar */
            .overflow-y-auto::-webkit-scrollbar {
                width: 8px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background: #374151;
                border-radius: 4px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #6B7280;
                border-radius: 4px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #9CA3AF;
            }

            /* Sortable styles */
            .sortable-ghost {
                opacity: 0.4;
                background: rgba(59, 130, 246, 0.1) !important;
            }

            /* Content block animations */
            .content-block {
                transition: all 0.3s ease;
            }

            .content-block:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            }

            /* Keyboard shortcut styling */
            kbd {
                background-color: #374151;
                border: 1px solid #4B5563;
                border-radius: 0.25rem;
                padding: 0.125rem 0.375rem;
                font-size: 0.75rem;
                font-family: ui-monospace, SFMono-Regular, monospace;
            }

            /* Note block styling variants */
            .note-tip {
                background: rgba(59, 130, 246, 0.1);
                border-left: 4px solid #3B82F6;
            }

            .note-warning {
                background: rgba(245, 158, 11, 0.1);
                border-left: 4px solid #F59E0B;
            }

            .note-info {
                background: rgba(16, 185, 129, 0.1);
                border-left: 4px solid #10B981;
            }

            .note-success {
                background: rgba(34, 197, 94, 0.1);
                border-left: 4px solid #22C55E;
            }
        </style>
    @endpush

    @push('scripts')
        <!-- Include Trix Editor -->
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

        <!-- Include SortableJS for drag and drop functionality -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    @endpush
</div>
</div>