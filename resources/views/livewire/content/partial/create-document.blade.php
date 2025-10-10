<div class="space-y-6">
    <!-- Header & Progress -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Document</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Step {{ $currentStep }} of {{ $totalSteps }}</p>
            </div>
            
            <div class="flex items-center space-x-2">
                <button wire:click="saveDraft" class="text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Save Draft
                </button>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ round(($currentStep / $totalSteps) * 100) }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 transition-colors duration-300">
                <div class="bg-indigo-600 dark:bg-indigo-500 h-2 rounded-full transition-all duration-300" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
            </div>
        </div>

        <!-- Step Indicators -->
        <div class="mt-6 flex items-center justify-between">
            @for($step = 1; $step <= $totalSteps; $step++)
                <div class="flex items-center {{ $step < $totalSteps ? 'flex-1' : '' }}">
                    <button 
                        wire:click="goToStep({{ $step }})"
                        class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium transition-colors
                            {{ $step <= $currentStep ? 'bg-indigo-600 dark:bg-indigo-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}
                            {{ $step < $currentStep ? 'cursor-pointer hover:bg-indigo-700 dark:hover:bg-indigo-600' : '' }}
                        "
                    >
                        @if($step < $currentStep)
                            <i class="fas fa-check"></i>
                        @else
                            {{ $step }}
                        @endif
                    </button>
                    
                    @if($step < $totalSteps)
                        <div class="flex-1 mx-2 h-0.5 {{ $step < $currentStep ? 'bg-indigo-600 dark:bg-indigo-500' : 'bg-gray-200 dark:bg-gray-700' }} transition-colors duration-300"></div>
                    @endif
                </div>
            @endfor
        </div>

        <!-- Step Labels -->
        <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span class="{{ $currentStep >= 1 ? 'text-indigo-600 dark:text-indigo-400 font-medium' : '' }}">Basic Info</span>
            <span class="{{ $currentStep >= 2 ? 'text-indigo-600 dark:text-indigo-400 font-medium' : '' }}">Content</span>
            <span class="{{ $currentStep >= 3 ? 'text-indigo-600 dark:text-indigo-400 font-medium' : '' }}">Settings</span>
            <span class="{{ $currentStep >= 4 ? 'text-indigo-600 dark:text-indigo-400 font-medium' : '' }}">Review</span>
        </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <form wire:submit.prevent class="space-y-6">
            @if($currentStep === 1)
                <!-- Step 1: Basic Information -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Title *</label>
                            <input 
                                wire:model="title" 
                                type="text" 
                                id="title" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                                placeholder="Enter a descriptive title for your document"
                                required
                            >
                            @error('title') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Type *</label>
                            <select wire:model="type" id="type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" required>
                                <option value="">Select Document Type</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select wire:model="category_id" id="category_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                <option value="">Select Category (Optional)</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty Level *</label>
                            <select wire:model="difficulty_level" id="difficulty_level" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" required>
                                @foreach($difficultyLevels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('difficulty_level') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Visibility</label>
                            <select wire:model="visibility" id="visibility" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                @foreach($visibilities as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('visibility') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep === 2)
                <!-- Step 2: Content -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Document Content</h3>
                    
                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Excerpt/Summary</label>
                        <textarea 
                            wire:model="excerpt" 
                            id="excerpt" 
                            rows="3" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                            placeholder="Brief summary or excerpt of the document content..."
                        ></textarea>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">This will be displayed in search results and document listings.</p>
                        @error('excerpt') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content *</label>
                        <textarea 
                            wire:model="content" 
                            id="content" 
                            rows="15" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                            placeholder="Write your document content here. You can use markdown formatting..."
                            required
                        ></textarea>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Markdown formatting is supported.</p>
                            <span class="text-sm text-gray-400 dark:text-gray-500">{{ strlen($content ?? '') }} characters</span>
                        </div>
                        @error('content') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content Preview -->
                    @if($content)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview:</h4>
                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 max-h-64 overflow-y-auto transition-colors duration-300">
                                <div class="prose prose-sm max-w-none dark:prose-invert">
                                    {!! nl2br(e($content)) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if($currentStep === 3)
                <!-- Step 3: Settings & SEO -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Settings & SEO</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                            <input 
                                wire:model="tags" 
                                type="text" 
                                id="tags" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                                placeholder="tag1, tag2, tag3"
                            >
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Separate tags with commas</p>
                            @error('tags') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SEO Title</label>
                            <input 
                                wire:model="meta_title" 
                                type="text" 
                                id="meta_title" 
                                maxlength="60"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                                placeholder="SEO optimized title"
                            >
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ 60 - strlen($meta_title ?? '') }} characters remaining</p>
                            @error('meta_title') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select wire:model="status" id="status" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SEO Description</label>
                            <textarea 
                                wire:model="meta_description" 
                                id="meta_description" 
                                rows="3" 
                                maxlength="160"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                                placeholder="Brief description for search engines"
                            ></textarea>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ 160 - strlen($meta_description ?? '') }} characters remaining</p>
                            @error('meta_description') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center space-x-3">
                                <input wire:model="featured" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Featured Document</span>
                            </label>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-6">Featured documents appear prominently on the site</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep === 4)
                <!-- Step 4: Review & Publish -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Review & Publish</h3>
                    
                    <!-- Document Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 space-y-4 transition-colors duration-300">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Title:</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $title ?: 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Type:</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $types[$type] ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Category:</label>
                                <p class="text-gray-900 dark:text-gray-100">
                                    {{ $categories->where('id', $category_id)->first()->name ?? 'Uncategorized' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Difficulty:</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $difficultyLevels[$difficulty_level] ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $statuses[$status] ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Visibility:</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $visibilities[$visibility] ?? 'Not set' }}</p>
                            </div>
                        </div>

                        @if($excerpt)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Excerpt:</label>
                                <p class="text-gray-900 dark:text-gray-100 text-sm">{{ $excerpt }}</p>
                            </div>
                        @endif

                        @if($tags)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tags:</label>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach(explode(',', $tags) as $tag)
                                        <span class="inline-block bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs px-2 py-1 rounded">
                                            {{ trim($tag) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Content Length:</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ strlen($content ?? '') }} characters, ~{{ ceil(strlen($content ?? '') / 5) }} words</p>
                        </div>

                        @if($featured)
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-star text-yellow-500 dark:text-yellow-400"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">This document will be featured</span>
                            </div>
                        @endif
                    </div>

                    <!-- Content Preview -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content Preview:</h4>
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 max-h-64 overflow-y-auto transition-colors duration-300">
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                {!! nl2br(e($content ?? '')) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Publish Options -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800 transition-colors duration-300">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2">Publishing Options</h4>
                        <div class="space-y-2 text-sm text-blue-800 dark:text-blue-300">
                            <p>Choose how you want to save your document:</p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li><strong>Save Draft:</strong> Save without publishing (can be edited later)</li>
                                <li><strong>Submit for Review:</strong> Submit to moderators for approval</li>
                                <li><strong>Publish:</strong> Make immediately available to users</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                <div>
                    @if($currentStep > 1)
                        <button 
                            type="button"
                            wire:click="previousStep" 
                            class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors flex items-center space-x-2"
                        >
                            <i class="fas fa-arrow-left"></i>
                            <span>Previous</span>
                        </button>
                    @endif
                </div>

                <div class="flex items-center space-x-3">
                    @if($currentStep < $totalSteps)
                        <button 
                            type="button"
                            wire:click="nextStep" 
                            class="px-6 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors flex items-center space-x-2"
                        >
                            <span>Next</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    @else
                        <!-- Final Step Actions -->
                        <div class="flex items-center space-x-3">
                            <button 
                                type="button"
                                wire:click="saveDraft" 
                                class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors flex items-center space-x-2"
                            >
                                <i class="fas fa-save"></i>
                                <span>Save Draft</span>
                            </button>
                            
                            <button 
                                type="button"
                                wire:click="submitForReview" 
                                class="px-6 py-2 bg-yellow-600 dark:bg-yellow-500 text-white rounded-lg hover:bg-yellow-700 dark:hover:bg-yellow-600 transition-colors flex items-center space-x-2"
                            >
                                <i class="fas fa-paper-plane"></i>
                                <span>Submit for Review</span>
                            </button>
                            
                            <button 
                                type="button"
                                wire:click="saveAndPublish" 
                                class="px-6 py-2 bg-green-600 dark:bg-green-500 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-colors flex items-center space-x-2"
                            >
                                <i class="fas fa-check"></i>
                                <span>Publish Now</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Loading States -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 z-50 flex items-center justify-center transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 dark:border-indigo-400"></div>
                <span class="text-gray-700 dark:text-gray-300">Processing...</span>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-save draft functionality
let autoSaveTimer;

function startAutoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        @this.call('saveDraft');
    }, 30000); // Auto-save every 30 seconds
}

// Listen for content changes
document.addEventListener('input', function(e) {
    if (e.target.matches('#content, #title, #excerpt')) {
        startAutoSave();
    }
});

// Character counting
document.addEventListener('input', function(e) {
    if (e.target.matches('#meta_title')) {
        const remaining = 60 - e.target.value.length;
        const counter = e.target.parentNode.querySelector('.text-gray-500');
        if (counter) {
            counter.textContent = remaining + ' characters remaining';
            counter.className = remaining < 0 ? 'text-red-500 dark:text-red-400 text-sm mt-1' : 'text-gray-500 dark:text-gray-400 text-sm mt-1';
        }
    }
    
    if (e.target.matches('#meta_description')) {
        const remaining = 160 - e.target.value.length;
        const counter = e.target.parentNode.querySelector('.text-gray-500');
        if (counter) {
            counter.textContent = remaining + ' characters remaining';
            counter.className = remaining < 0 ? 'text-red-500 dark:text-red-400 text-sm mt-1' : 'text-gray-500 dark:text-gray-400 text-sm mt-1';
        }
    }
});
</script>