
{{-- resources/views/livewire/blog/admin-blog-settings.blade.php --}}
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $isSeoMode ? 'SEO Settings' : 'Blog Settings' }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            {{ $isSeoMode ? 'Configure SEO and search engine optimization' : 'Configure your blog preferences and behavior' }}
        </p>
    </div>

    {{-- Tab Navigation --}}
    @if(!$isSeoMode)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6">
            <nav class="flex border-b border-gray-200 dark:border-gray-700">
                <button wire:click="setActiveTab('general')" 
                        class="px-6 py-3 font-medium text-sm border-b-2 {{ $activeTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    General
                </button>
                <button wire:click="setActiveTab('seo')" 
                        class="px-6 py-3 font-medium text-sm border-b-2 {{ $activeTab === 'seo' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    SEO
                </button>
                <button wire:click="setActiveTab('social')" 
                        class="px-6 py-3 font-medium text-sm border-b-2 {{ $activeTab === 'social' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Social
                </button>
                <button wire:click="setActiveTab('email')" 
                        class="px-6 py-3 font-medium text-sm border-b-2 {{ $activeTab === 'email' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Email
                </button>
            </nav>
        </div>
    @endif

    <form wire:submit.prevent="save">
        {{-- General Settings --}}
        @if($activeTab === 'general' || $isSeoMode)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 {{ $isSeoMode ? '' : 'mb-6' }}">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ $isSeoMode ? 'SEO Configuration' : 'General Settings' }}
                </h3>
                
                <div class="space-y-6">
                    @if(!$isSeoMode)
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Blog Title</label>
                                <input type="text" 
                                       wire:model="blog_title" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('blog_title')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Posts Per Page</label>
                                <input type="number" 
                                       wire:model="posts_per_page" 
                                       min="6" max="50"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('posts_per_page')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Blog Description</label>
                            <textarea wire:model="blog_description" 
                                      rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            @error('blog_description')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h4 class="font-medium text-gray-900 dark:text-white">Comment Settings</h4>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="allow_guest_comments" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allow guest comments</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="auto_approve_comments" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Auto-approve comments</span>
                                </label>
                            </div>
                            
                            <div class="space-y-3">
                                <h4 class="font-medium text-gray-900 dark:text-white">Display Settings</h4>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="show_author_bio" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show author bio</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="show_reading_time" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show reading time</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="enable_reactions" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable post reactions</span>
                                </label>
                            </div>
                        </div>
                    @endif
                    
                    {{-- SEO Settings (shown in both modes) --}}
                    <div class="border-t {{ $isSeoMode ? '' : 'border-gray-200 dark:border-gray-700' }} pt-6">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-4">SEO Defaults</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Meta Title</label>
                                <input type="text" 
                                       wire:model="default_meta_title" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       placeholder="Default title for all pages">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Meta Description</label>
                                <textarea wire:model="default_meta_description" 
                                          rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                          placeholder="Default description for search engines"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Keywords</label>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @foreach($default_meta_keywords as $index => $keyword)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm flex items-center">
                                            {{ $keyword }}
                                            <button type="button" 
                                                    wire:click="removeKeyword({{ $index }})" 
                                                    class="ml-2 text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                                <div class="flex">
                                    <input type="text" 
                                           wire:model="newKeyword" 
                                           wire:keydown.enter.prevent="addKeyword"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                           placeholder="Add keyword...">
                                    <button type="button" 
                                            wire:click="addKeyword" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Save Button --}}
        <div class="flex justify-end">
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-save mr-2"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>
