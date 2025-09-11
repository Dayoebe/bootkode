{{-- Enhanced Settings Partial --}}
<div class="space-y-6">
    <form wire:submit.prevent="save">
        <!-- General Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">General Settings</h3>
                    <p class="text-sm text-gray-600 mt-1">Basic page management configuration</p>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Default Template</label>
                        <select wire:model="defaultTemplate" 
                                class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($templateOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('defaultTemplate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700">Default Status</label>
                        <select wire:model="defaultStatus" 
                                class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                        @error('defaultStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="autoGenerateSlugs" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Auto-generate URL slugs</span>
                            <p class="text-sm text-gray-500">Automatically create SEO-friendly URLs from page titles</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="enableSeoAnalysis" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable SEO analysis</span>
                            <p class="text-sm text-gray-500">Show SEO suggestions and analysis when creating pages</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="enableVersioning" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable page versioning</span>
                            <p class="text-sm text-gray-500">Keep track of page changes and allow rollbacks</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Performance Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Performance Settings</h3>
                    <p class="text-sm text-gray-600 mt-1">Optimize your site's performance and loading speed</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model="cachePages" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700">Cache published pages</span>
                        <p class="text-sm text-gray-500">Improve loading speed by caching published pages</p>
                    </div>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model="optimizeImages" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700">Optimize images automatically</span>
                        <p class="text-sm text-gray-500">Compress and optimize uploaded images</p>
                    </div>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model="minifyCss" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700">Minify CSS</span>
                        <p class="text-sm text-gray-500">Reduce CSS file sizes for faster loading</p>
                    </div>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model="minifyJs" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700">Minify JavaScript</span>
                        <p class="text-sm text-gray-500">Reduce JavaScript file sizes for faster loading</p>
                    </div>
                </label>
                
                <div class="border-t border-gray-200 pt-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="enableCdn" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable CDN</span>
                            <p class="text-sm text-gray-500">Use a content delivery network for static assets</p>
                        </div>
                    </label>
                    
                    @if($enableCdn)
                        <div class="mt-3 ml-7">
                            <label class="block text-sm font-medium text-gray-700 mb-1">CDN URL</label>
                            <input type="url" 
                                   wire:model="cdnUrl"
                                   class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="https://cdn.example.com">
                            @error('cdnUrl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">SEO Settings</h3>
                    <p class="text-sm text-gray-600 mt-1">Search engine optimization configuration</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model="autoGenerateMetaDescriptions" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700">Auto-generate meta descriptions</span>
                        <p class="text-sm text-gray-500">Create meta descriptions from page content when empty</p>
                    </div>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model="autoGenerateOgImages" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700">Auto-generate OG images</span>
                        <p class="text-sm text-gray-500">Create social media images from page content</p>
                    </div>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model="enableBreadcrumbs" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700">Enable breadcrumbs</span>
                        <p class="text-sm text-gray-500">Show navigation breadcrumbs on pages</p>
                    </div>
                </label>
                
                <div class="border-t border-gray-200 pt-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="enableSitemap" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Generate sitemap automatically</span>
                            <p class="text-sm text-gray-500">Create and update sitemap.xml automatically</p>
                        </div>
                    </label>
                    
                    @if($enableSitemap)
                        <div class="mt-3 ml-7">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Update Frequency</label>
                            <select wire:model="sitemapFrequency" 
                                    class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="always">Always</option>
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                                <option value="never">Never</option>
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Content Settings</h3>
                    <p class="text-sm text-gray-600 mt-1">Configure content features and options</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="enableComments" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable comments</span>
                            <p class="text-sm text-gray-500">Allow comments on pages</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="enableSocialSharing" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Social sharing buttons</span>
                            <p class="text-sm text-gray-500">Show social media sharing buttons</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="enableReadingTime" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Reading time estimates</span>
                            <p class="text-sm text-gray-500">Display estimated reading time</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="enableTableOfContents" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Table of contents</span>
                            <p class="text-sm text-gray-500">Auto-generate table of contents</p>
                        </div>
                    </label>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Revisions</label>
                    <input type="number" 
                           wire:model="maxRevisions"
                           min="1" max="100"
                           class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('maxRevisions') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <p class="text-sm text-gray-500 mt-1">Number of page revisions to keep for history</p>
                </div>
            </div>
        </div>

        <!-- Analytics & Tracking -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Analytics & Tracking</h3>
                    <p class="text-sm text-gray-600 mt-1">Configure tracking and analytics services</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
                    <input type="text" 
                           wire:model="googleAnalyticsId"
                           class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="G-XXXXXXXXXX">
                    @error('googleAnalyticsId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Google Tag Manager ID</label>
                    <input type="text" 
                           wire:model="googleTagManagerId"
                           class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="GTM-XXXXXXX">
                    @error('googleTagManagerId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook Pixel ID</label>
                    <input type="text" 
                           wire:model="facebookPixelId"
                           class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="123456789012345">
                    @error('facebookPixelId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Email Notifications</h3>
                    <p class="text-sm text-gray-600 mt-1">Configure when to send email notifications</p>
                </div>
                <button type="button" 
                        wire:click="testEmailSettings"
                        class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-lg transition-colors">
                    <i class="fas fa-paper-plane mr-1"></i>Test Email
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notification Email</label>
                    <input type="email" 
                           wire:model="notificationEmail"
                           class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="admin@example.com">
                    @error('notificationEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="emailOnPagePublished" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Page published</span>
                            <p class="text-sm text-gray-500">Send email when a page is published</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="emailOnPageUpdated" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Page updated</span>
                            <p class="text-sm text-gray-500">Send email when a page is updated</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">System Information</h3>
                    <p class="text-sm text-gray-600 mt-1">Current system status and configuration</p>
                </div>
                <div class="flex space-x-2">
                    <button type="button" 
                            wire:click="clearCache"
                            class="text-sm bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1 rounded-lg transition-colors">
                        <i class="fas fa-trash mr-1"></i>Clear Cache
                    </button>
                    <button type="button" 
                            wire:click="optimizeDatabase"
                            class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded-lg transition-colors">
                        <i class="fas fa-database mr-1"></i>Optimize DB
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($systemInfo as $key => $value)
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="text-xs text-gray-500 uppercase tracking-wide">
                            {{ str_replace('_', ' ', $key) }}
                        </div>
                        <div class="text-sm font-medium text-gray-900 mt-1">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="resetToDefaults"
                            wire:confirm="Are you sure you want to reset all settings to defaults?"
                            class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-undo mr-2"></i>Reset to Defaults
                    </button>
                    
                    <button type="button" 
                            wire:click="exportSettings"
                            class="text-sm bg-purple-100 hover:bg-purple-200 text-purple-800 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-download mr-2"></i>Export Settings
                    </button>
                </div>
                
                <button type="submit" 
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </div>
    </form>
</div>