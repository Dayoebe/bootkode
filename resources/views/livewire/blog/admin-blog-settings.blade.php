
{{-- resources/views/livewire/blog/admin-blog-settings.blade.php --}}
<div>
    <div class="mb-8">
        <h1 class=" font-bold text-content dark:text-white bk-page-title">
            {{ $isSeoMode ? 'SEO Settings' : 'Blog Settings' }}
        </h1>
        <p class="text-content-muted dark:text-content-muted">
            {{ $isSeoMode ? 'Configure SEO and search engine optimization' : 'Configure your blog preferences and behavior' }}
        </p>
    </div>

    {{-- Tab Navigation --}}
    @if(!$isSeoMode)
        <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm mb-6">
            <nav class="flex border-b border-line dark:border-gray-700">
                <button wire:click="setActiveTab('general')"
                        class="px-6 py-3 font-medium text-sm border-b-2 {{ $activeTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    General
                </button>
                <button wire:click="setActiveTab('seo')"
                        class="px-6 py-3 font-medium text-sm border-b-2 {{ $activeTab === 'seo' ? 'border-blue-500 text-blue-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    SEO
                </button>
                <button wire:click="setActiveTab('social')"
                        class="px-6 py-3 font-medium text-sm border-b-2 {{ $activeTab === 'social' ? 'border-blue-500 text-blue-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    Social
                </button>
                <button wire:click="setActiveTab('email')"
                        class="px-6 py-3 font-medium text-sm border-b-2 {{ $activeTab === 'email' ? 'border-blue-500 text-blue-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    Email
                </button>
            </nav>
        </div>
    @endif

    <form wire:submit.prevent="save">
        {{-- General Settings --}}
        @if($activeTab === 'general' || $isSeoMode)
            <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm p-6 {{ $isSeoMode ? '' : 'mb-6' }}">
                <h3 class="text-lg font-medium text-content dark:text-white mb-4">
                    {{ $isSeoMode ? 'SEO Configuration' : 'General Settings' }}
                </h3>

                <div class="space-y-6">
                    @if(!$isSeoMode)
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Blog Title</label>
                                <input aria-label="Blog title" type="text"
                                       wire:model="blog_title"
                                       class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('blog_title')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Posts Per Page</label>
                                <input aria-label="Posts per page" type="number"
                                       wire:model="posts_per_page"
                                       min="6" max="50"
                                       class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('posts_per_page')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Blog Description</label>
                            <textarea aria-label="Blog description" wire:model="blog_description"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            @error('blog_description')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h4 class="font-medium text-content dark:text-white">Comment Settings</h4>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="allow_guest_comments" class="rounded border-line text-brand focus:ring-brand/30">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Allow guest comments</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="auto_approve_comments" class="rounded border-line text-brand focus:ring-brand/30">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Auto-approve comments</span>
                                </label>
                            </div>

                            <div class="space-y-3">
                                <h4 class="font-medium text-content dark:text-white">Display Settings</h4>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="show_author_bio" class="rounded border-line text-brand focus:ring-brand/30">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Show author bio</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="show_reading_time" class="rounded border-line text-brand focus:ring-brand/30">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Show reading time</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="enable_reactions" class="rounded border-line text-brand focus:ring-brand/30">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Enable post reactions</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    {{-- SEO Settings (shown in both modes) --}}
                    <div class="border-t {{ $isSeoMode ? '' : 'border-gray-200 dark:border-gray-700' }} pt-6">
                        <h4 class="font-medium text-content dark:text-white mb-4">SEO Defaults</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Default Meta Title</label>
                                <input aria-label="Default title for all pages" type="text"
                                       wire:model="default_meta_title"
                                       class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       placeholder="Default title for all pages">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Default Meta Description</label>
                                <textarea aria-label="Default description for search engines" wire:model="default_meta_description"
                                          rows="3"
                                          class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                          placeholder="Default description for search engines"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Default Keywords</label>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @foreach($default_meta_keywords as $index => $keyword)
                                        <span class="px-3 py-1 bg-brand/10 text-brand rounded-full text-sm flex items-center">
                                            {{ $keyword }}
                                            <button type="button"
                                                    wire:click="removeKeyword({{ $index }})"
                                                    class="ml-2 text-brand hover:text-brand" aria-label="Close">
                                                <i aria-hidden="true" class="fas fa-times"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                                <div class="flex">
                                    <input aria-label="Add keyword" type="text"
                                           wire:model="newKeyword"
                                           wire:keydown.enter.prevent="addKeyword"
                                           class="flex-1 px-4 py-2 border border-line rounded-l-lg focus:ring-2 focus:ring-brand/30 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                           placeholder="Add keyword...">
                                    <button type="button"
                                            wire:click="addKeyword"
                                            class="px-4 py-2 bg-action text-action-foreground rounded-r-lg hover:bg-action">
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
                    class="px-6 py-3 bg-action text-action-foreground rounded-lg hover:bg-action transition-colors font-medium">
                <i aria-hidden="true" class="fas fa-save mr-2"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>
