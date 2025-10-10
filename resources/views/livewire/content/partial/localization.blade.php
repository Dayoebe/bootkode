<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Content Localization</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage translations and multi-language content</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <button 
                    wire:click="exportTranslations"
                    class="bg-green-600 dark:bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-download"></i>
                    <span>Export Translations</span>
                </button>
                
                <button 
                    wire:click="importTranslations"
                    class="bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-upload"></i>
                    <span>Import Translations</span>
                </button>
            </div>
        </div>

        <!-- Language Overview -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Translation Progress</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($translationProgress as $contentType => $languages)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 transition-colors duration-300">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3 capitalize">{{ str_replace('_', ' ', $contentType) }}</h4>
                        <div class="space-y-2">
                            @foreach($languages as $lang => $progress)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $availableLanguages[$lang] ?? $lang }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $lang }})</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-20 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $progress }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="selectedLanguage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Source Language</label>
                <select wire:model.live="selectedLanguage" id="selectedLanguage" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                    @foreach($availableLanguages as $code => $name)
                        <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="selectedContentType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content Type</label>
                <select wire:model.live="selectedContentType" id="selectedContentType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                    @foreach($contentTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Content</label>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    id="search"
                    placeholder="Search by title..." 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                >
            </div>
            
            <div class="flex items-end">
                <button 
                    wire:click="$refresh" 
                    class="w-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2"
                >
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Content List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $contentTypes[$selectedContentType] }} in {{ $availableLanguages[$selectedLanguage] }}
            </h3>
        </div>

        @if($content->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($content as $item)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $item->title }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        {{ $availableLanguages[$item->language] ?? $item->language }}
                                    </span>
                                </div>
                                
                                @if($item->description ?? $item->excerpt ?? $item->content)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-3">
                                        {{ Str::limit($item->description ?? $item->excerpt ?? $item->content, 150) }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                    <span>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $item->creator->name ?? 'Unknown' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $item->created_at->format('M j, Y') }}
                                    </span>
                                    @if($item->updated_at && $item->updated_at->ne($item->created_at))
                                        <span>
                                            <i class="fas fa-edit mr-1"></i>
                                            Updated {{ $item->updated_at->format('M j, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-6">
                                <div class="flex items-center space-x-2">
                                    <!-- Translation Status for each language -->
                                    @foreach(['es', 'fr', 'de', 'pt'] as $lang)
                                        @if($lang !== $selectedLanguage)
                                            <button 
                                                wire:click="openTranslationModal({{ $item->id }}, '{{ $item->content_type }}', '{{ $lang }}')"
                                                class="flex items-center space-x-1 px-2 py-1 rounded text-xs font-medium transition-colors
                                                    {{ rand(0, 1) ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900/40' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                                                title="Translate to {{ $availableLanguages[$lang] }}"
                                            >
                                                <span>{{ strtoupper($lang) }}</span>
                                                @if(rand(0, 1))
                                                    <i class="fas fa-check text-xs"></i>
                                                @else
                                                    <i class="fas fa-plus text-xs"></i>
                                                @endif
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($content->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $content->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <i class="fas fa-language text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No content found</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    @if($search)
                        No content matches your search criteria.
                    @else
                        No {{ strtolower($contentTypes[$selectedContentType]) }} available in {{ $availableLanguages[$selectedLanguage] }}.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Translation Modal -->
    @if($showTranslationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Translate to {{ $availableLanguages[$targetLanguage] ?? $targetLanguage }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Original content: {{ $selectedContent->title ?? '' }}
                            </p>
                        </div>
                        <button wire:click="closeTranslationModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form wire:submit="saveTranslation" class="space-y-6">
                        <!-- Original Content -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 transition-colors duration-300">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Original Content</h4>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Title:</label>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $selectedContent->title ?? '' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Content:</label>
                                    <p class="text-gray-900 dark:text-gray-100 text-sm line-clamp-3">
                                        {{ Str::limit($selectedContent->content ?? $selectedContent->description ?? '', 300) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Translation Form -->
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="translatedTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Translated Title *
                                </label>
                                <input 
                                    wire:model="translatedTitle" 
                                    type="text" 
                                    id="translatedTitle" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" 
                                    required
                                >
                                @error('translatedTitle') 
                                    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label for="translatedContent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Translated Content *
                                </label>
                                <textarea 
                                    wire:model="translatedContent" 
                                    id="translatedContent" 
                                    rows="8" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" 
                                    required
                                ></textarea>
                                @error('translatedContent') 
                                    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <!-- Translation Tips -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 transition-colors duration-300">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-300">Translation Tips</h4>
                                    <div class="mt-2 text-sm text-blue-800 dark:text-blue-300">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Keep the tone and style consistent with the original</li>
                                            <li>Preserve formatting, links, and special characters</li>
                                            <li>Consider cultural context and local expressions</li>
                                            <li>Review grammar and spelling before saving</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button 
                                type="button" 
                                wire:click="autoTranslate" 
                                class="bg-yellow-600 dark:bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 dark:hover:bg-yellow-600 transition-colors flex items-center space-x-2"
                            >
                                <i class="fas fa-magic"></i>
                                <span>Auto Translate</span>
                            </button>
                            
                            <div class="flex space-x-3">
                                <button 
                                    type="button" 
                                    wire:click="closeTranslationModal" 
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 flex items-center transition-colors"
                                >
                                    <span wire:loading.remove wire:target="saveTranslation">Save Translation</span>
                                    <span wire:loading wire:target="saveTranslation" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Saving...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>