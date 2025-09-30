<div x-data="lessonEditorManager()" x-init="init()">
    <!-- Auto-save Status Indicator -->
    <div x-show="showAutoSaveStatus" x-transition.opacity class="fixed top-4 right-4 z-40">
        <div class="px-3 py-2 rounded-lg shadow-lg text-sm font-medium"
             :class="autoSaveStatus === 'saving' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                    autoSaveStatus === 'saved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                    'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'">
            <div x-show="autoSaveStatus === 'saving'" class="flex items-center gap-2">
                <div class="w-3 h-3 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                Saving...
            </div>
            <div x-show="autoSaveStatus === 'saved'" class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                All changes saved
            </div>
            <div x-show="autoSaveStatus === 'error'" class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                Save failed - <button @click="retrySave()" class="underline">retry</button>
            </div>
        </div>
    </div>

    <div class="space-y-4" wire:key="lesson-editor-{{ $lessonId }}">

        <!-- Success Message -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity 
                 class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700 p-4 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
                <button @click="show = false" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Lesson Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white transition-colors duration-300 truncate">
                        <i class="fas fa-book-open text-blue-600 dark:text-blue-400 mr-2"></i>
                        {{ $lesson->title ?? 'Lesson Editor' }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300 truncate">
                        Section: <span class="text-blue-600 dark:text-blue-300">{{ $lesson->section->title ?? 'No Section' }}</span>
                    </p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button @click="performQuickSave()" :disabled="isSaving" 
                        class="px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg flex items-center gap-2 transition-colors duration-300 text-sm">
                        <i class="fas fa-bolt" x-show="!isSaving"></i>
                        <i class="fas fa-spinner fa-spin" x-show="isSaving"></i>
                        <span class="hidden sm:inline">Quick Save</span>
                    </button>
                    <button wire:click="saveLesson" wire:loading.attr="disabled"
                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg flex items-center gap-2 transition-colors duration-300 text-sm">
                        <i class="fas fa-save" wire:loading.remove></i>
                        <i class="fas fa-spinner fa-spin" wire:loading></i>
                        <span wire:loading.remove class="hidden sm:inline">Save All</span>
                        <span wire:loading class="hidden sm:inline">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-4 px-4 overflow-x-auto">
                    <button @click="switchTab('content')" :disabled="isSaving"
                        :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'content', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'content' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i class="fas fa-align-left"></i> 
                        <span class="hidden sm:inline">Content</span>
                        <div x-show="hasUnsavedContent && (titleChanged || descriptionChanged || contentChanged)" class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </button>
                    <button @click="switchTab('media')" :disabled="isSaving"
                        :class="{ 'border-pink-500 text-pink-600 dark:text-pink-400': activeTab === 'media', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'media' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i class="fas fa-photo-video"></i> 
                        <span class="hidden sm:inline">Media</span>
                        <div x-show="hasUnsavedContent && mediaChanged" class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </button>
                    <button @click="switchTab('settings')" :disabled="isSaving"
                        :class="{ 'border-green-500 text-green-600 dark:text-green-400': activeTab === 'settings', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'settings' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i class="fas fa-cog"></i> 
                        <span class="hidden sm:inline">Settings</span>
                        <div x-show="hasUnsavedContent && settingsChanged" class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </button>
                    <button @click="switchTab('assessment')" :disabled="isSaving"
                        :class="{ 'border-purple-500 text-purple-600 dark:text-purple-400': activeTab === 'assessment', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'assessment' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i class="fas fa-clipboard-check"></i> 
                        <span class="hidden sm:inline">Assessment</span>
                    </button>
                </nav>
            </div>

            <!-- Content Tab -->
            <div x-show="activeTab === 'content'" class="p-4 space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                            Lesson Title * 
                            <span x-show="titleChanged" class="text-orange-500 text-xs">(unsaved)</span>
                        </label>
                        <input type="text" wire:model.live.debounce.1000ms="title" @input="markFieldChanged('title')"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300 text-sm"
                            :class="{ 'border-orange-400': titleChanged }">
                        @error('title')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                            URL Slug *
                            <span x-show="slugChanged" class="text-orange-500 text-xs">(unsaved)</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" wire:model="slug" @input="markFieldChanged('slug')"
                                class="flex-1 px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300 text-sm"
                                :class="{ 'border-orange-400': slugChanged }">
                            <button wire:click="generateSlug" wire:loading.attr="disabled" :disabled="isSaving"
                                class="px-3 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg disabled:opacity-50 transition-colors duration-300 flex-shrink-0 text-sm"
                                title="Generate from title">
                                <i class="fas fa-sync-alt" wire:loading.remove></i>
                                <i class="fas fa-spinner fa-spin" wire:loading></i>
                            </button>
                        </div>
                        @error('slug')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                        Description
                        <span x-show="descriptionChanged" class="text-orange-500 text-xs">(unsaved)</span>
                    </label>
                    <textarea wire:model.live.debounce.1000ms="description" @input="markFieldChanged('description')" rows="3"
                        class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300 text-sm"
                        :class="{ 'border-orange-400': descriptionChanged }"
                        placeholder="Brief description of this lesson..."></textarea>
                    @error('description')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror>
                </div>

                <!-- Content Editor - CRITICAL FIX HERE -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-300">
                            Lesson Content
                            <span x-show="contentChanged" class="text-orange-500 text-xs">(unsaved changes)</span>
                        </label>
                        <div class="flex gap-1 text-xs">
                            <button @click="insertTemplate('intro')" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-play mr-1"></i>Intro
                            </button>
                            <button @click="insertTemplate('objectives')" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-bullseye mr-1"></i>Goals
                            </button>
                            <button @click="insertTemplate('summary')" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-clipboard-list mr-1"></i>Summary
                            </button>
                        </div>
                    </div>
                    
                    <!-- CRITICAL: wire:ignore prevents re-rendering, longer debounce gives more typing time -->
                    <div wire:ignore class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden shadow-sm"
                         :class="{ 'border-orange-400': contentChanged }">
                        <trix-editor 
                            x-ref="trixEditor" 
                            input="trix-{{ $lessonId }}"
                            @trix-change="handleTrixChange($event)"
                            placeholder="Start writing your lesson content here..."
                            class="trix-content bg-white dark:bg-gray-700 text-gray-900 dark:text-white min-h-[300px] p-3 transition-colors duration-300 text-sm"
                            aria-label="Lesson Content Editor">
                        </trix-editor>
                    </div>
                    
                    <!-- Hidden input for Livewire binding -->
                    <input type="hidden" id="trix-{{ $lessonId }}" value="{{ $content }}">
                    
                    @error('content')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Media Tab -->
            <div x-show="activeTab === 'media'" class="p-4 space-y-4">
                <!-- YouTube Video -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-video text-red-600 dark:text-red-500"></i> YouTube Video
                    </h3>
                    <div class="flex gap-2">
                        <input type="url" wire:model="video_url" @input="markFieldChanged('media')" 
                               placeholder="https://www.youtube.com/watch?v=..."
                            class="flex-1 px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300 text-sm">
                        @if ($video_url)
                            <button type="button" onclick="window.open('{{ $video_url }}', '_blank')"
                                class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300 text-sm flex-shrink-0">
                                Preview
                            </button>
                        @endif
                    </div>
                    @error('video_url')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Rest of media uploads... -->
            </div>

            <!-- Settings and Assessment tabs remain the same... -->
        </div>
    </div>

    <script>
        function lessonEditorManager() {
            return {
                activeTab: 'content',
                autoSaveStatus: 'saved',
                showAutoSaveStatus: false,
                isSaving: false,
                hasUnsavedContent: false,
                titleChanged: false,
                slugChanged: false,
                descriptionChanged: false,
                contentChanged: false,
                settingsChanged: false,
                mediaChanged: false,
                autoSaveTimeout: null,
                trixInitialized: false,
                lastSavedContent: '',
                
                init() {
                    this.setupEventListeners();
                    this.initializeTrixEditor();
                },
                
                setupEventListeners() {
                    // Listen for auto-save completion
                    Livewire.on('lesson-autosaved', () => {
                        this.handleSuccessfulSave();
                    });
                    
                    Livewire.on('lesson-saved', () => {
                        this.handleSuccessfulSave();
                    });
                    
                    // Warn before leaving with unsaved changes
                    window.addEventListener('beforeunload', (e) => {
                        if (this.hasUnsavedContent) {
                            e.preventDefault();
                            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                        }
                    });
                },
                
                initializeTrixEditor() {
                    this.$nextTick(() => {
                        const editor = this.$refs.trixEditor;
                        if (editor && !this.trixInitialized) {
                            // Store initial content
                            this.lastSavedContent = editor.editor?.getDocument().toString() || '';
                            this.trixInitialized = true;
                            
                            // Prevent file attachments
                            editor.addEventListener('trix-file-accept', (e) => {
                                e.preventDefault();
                            });
                        }
                    });
                },
                
                // Handle Trix changes with proper debouncing
                handleTrixChange(event) {
                    const newContent = event.target.editor.getDocument().toString();
                    
                    // Only mark as changed if content actually differs
                    if (newContent !== this.lastSavedContent) {
                        this.markFieldChanged('content');
                        
                        // Update Livewire without triggering re-render
                        this.updateLivewireContent(event.target.value);
                    }
                },
                
                updateLivewireContent(content) {
                    // Debounce the Livewire update
                    clearTimeout(this.contentUpdateTimeout);
                    this.contentUpdateTimeout = setTimeout(() => {
                        @this.set('content', content, false); // false = don't trigger re-render
                    }, 500);
                },
                
                switchTab(tab) {
                    if (!this.isSaving) {
                        this.activeTab = tab;
                    }
                },
                
                markFieldChanged(field) {
                    this.hasUnsavedContent = true;
                    this[field + 'Changed'] = true;
                    this.scheduleAutoSave();
                },
                
                scheduleAutoSave() {
                    clearTimeout(this.autoSaveTimeout);
                    this.autoSaveTimeout = setTimeout(() => {
                        this.performAutoSave();
                    }, 5000); // Auto-save after 5 seconds of inactivity
                },
                
                async performAutoSave() {
                    if (this.isSaving || !this.hasUnsavedContent) return;
                    
                    this.autoSaveStatus = 'saving';
                    this.showAutoSaveStatus = true;
                    
                    try {
                        // Get current Trix content before saving
                        if (this.$refs.trixEditor) {
                            const currentContent = this.$refs.trixEditor.value;
                            await @this.set('content', currentContent, false);
                        }
                        
                        const result = await @this.call('autoSave');
                        
                        if (result) {
                            this.handleSuccessfulSave();
                            // Update last saved content
                            if (this.$refs.trixEditor) {
                                this.lastSavedContent = this.$refs.trixEditor.editor.getDocument().toString();
                            }
                        } else {
                            this.autoSaveStatus = 'error';
                            setTimeout(() => this.showAutoSaveStatus = false, 3000);
                        }
                    } catch (error) {
                        console.error('Auto-save error:', error);
                        this.autoSaveStatus = 'error';
                        setTimeout(() => this.showAutoSaveStatus = false, 3000);
                    }
                },
                
                async performQuickSave() {
                    if (this.isSaving) return;
                    
                    this.isSaving = true;
                    this.autoSaveStatus = 'saving';
                    this.showAutoSaveStatus = true;
                    
                    try {
                        // Get current Trix content
                        if (this.$refs.trixEditor) {
                            const currentContent = this.$refs.trixEditor.value;
                            await @this.set('content', currentContent, false);
                        }
                        
                        const result = await @this.call('quickSave');
                        
                        if (result) {
                            this.handleSuccessfulSave();
                            if (this.$refs.trixEditor) {
                                this.lastSavedContent = this.$refs.trixEditor.editor.getDocument().toString();
                            }
                        } else {
                            this.autoSaveStatus = 'error';
                            setTimeout(() => this.showAutoSaveStatus = false, 3000);
                        }
                    } catch (error) {
                        console.error('Quick save error:', error);
                        this.autoSaveStatus = 'error';
                        setTimeout(() => this.showAutoSaveStatus = false, 3000);
                    } finally {
                        this.isSaving = false;
                    }
                },
                
                handleSuccessfulSave() {
                    this.autoSaveStatus = 'saved';
                    this.hasUnsavedContent = false;
                    this.titleChanged = false;
                    this.slugChanged = false;
                    this.descriptionChanged = false;
                    this.contentChanged = false;
                    this.settingsChanged = false;
                    this.mediaChanged = false;
                    
                    setTimeout(() => {
                        this.showAutoSaveStatus = false;
                    }, 2000);
                },
                
                async retrySave() {
                    await this.performQuickSave();
                },
                
                insertTemplate(type) {
                    const templates = {
                        intro: '<h2>Introduction</h2><p>Welcome to this lesson. In this section, we will explore...</p>',
                        objectives: '<h2>Learning Objectives</h2><ul><li>Understand the key concepts</li><li>Apply the knowledge practically</li><li>Evaluate the outcomes</li></ul>',
                        summary: '<h2>Summary</h2><p>In this lesson, we covered the following key points:</p><ul><li>Key point 1</li><li>Key point 2</li><li>Key point 3</li></ul>'
                    };
                    
                    if (this.$refs.trixEditor && templates[type]) {
                        this.$refs.trixEditor.editor.insertHTML(templates[type]);
                        this.markFieldChanged('content');
                    }
                }
            };
        }
    </script>
    
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Remove file attachment button from Trix toolbar
                document.addEventListener('trix-before-initialize', function(event) {
                    event.target.toolbarElement.querySelector('[data-trix-action="attachFiles"]')?.remove();
                });
            });
        </script>
    @endpush
</div>