{{-- resources/views/livewire/marketplace/partial/marketplace-settings.blade.php --}}
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Marketplace Settings</h2>
                <p class="text-gray-600">Configure marketplace behavior and policies</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-4 text-center text-sm">
                <div>
                    <div class="text-lg font-semibold text-purple-600">{{ $stats['total_items'] }}</div>
                    <div class="text-gray-500">Total Items</div>
                </div>
                <div>
                    <div class="text-lg font-semibold text-green-600">{{ $stats['total_vendors'] }}</div>
                    <div class="text-gray-500">Active Vendors</div>
                </div>
                <div>
                    <div class="text-lg font-semibold text-blue-600">₦{{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="text-gray-500">Total Revenue</div>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveSettings" class="space-y-6">
        <!-- General Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="platformCommission" class="block text-sm font-medium text-gray-700 mb-2">
                        Platform Commission (%)
                    </label>
                    <input wire:model="platformCommission" 
                           type="number" 
                           min="0" 
                           max="100" 
                           step="0.1"
                           id="platformCommission"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Platform keeps {{ $platformCommission }}%, vendors get {{ 100 - $platformCommission }}%</p>
                    @error('platformCommission') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="defaultCurrency" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Currency
                    </label>
                    <select wire:model="defaultCurrency" 
                            id="defaultCurrency"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        @foreach($currencies as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="minimumPrice" class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Item Price (₦)
                    </label>
                    <input wire:model="minimumPrice" 
                           type="number" 
                           min="0" 
                           step="0.01"
                           id="minimumPrice"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('minimumPrice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="maximumPrice" class="block text-sm font-medium text-gray-700 mb-2">
                        Maximum Item Price (₦)
                    </label>
                    <input wire:model="maximumPrice" 
                           type="number" 
                           min="100" 
                           step="0.01"
                           id="maximumPrice"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('maximumPrice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="flex items-center">
                    <input wire:model="autoApproveInstructors" 
                           type="checkbox" 
                           id="autoApproveInstructors"
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="autoApproveInstructors" class="ml-2 text-sm text-gray-700">
                        Auto-approve items from instructors
                    </label>
                </div>

                <div class="flex items-center">
                    <input wire:model="requireAdminApproval" 
                           type="checkbox" 
                           id="requireAdminApproval"
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="requireAdminApproval" class="ml-2 text-sm text-gray-700">
                        Require admin approval for all new items
                    </label>
                </div>

                <div class="flex items-center">
                    <input wire:model="enableReviews" 
                           type="checkbox" 
                           id="enableReviews"
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="enableReviews" class="ml-2 text-sm text-gray-700">
                        Enable customer reviews and ratings
                    </label>
                </div>

                <div class="flex items-center">
                    <input wire:model="enablePromotions" 
                           type="checkbox" 
                           id="enablePromotions"
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="enablePromotions" class="ml-2 text-sm text-gray-700">
                        Enable promotions and discount codes
                    </label>
                </div>
            </div>
        </div>

        <!-- File Upload Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">File Upload Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="maxThumbnailSize" class="block text-sm font-medium text-gray-700 mb-2">
                        Max Thumbnail Size (KB)
                    </label>
                    <input wire:model="maxThumbnailSize" 
                           type="number" 
                           min="100" 
                           max="10240"
                           id="maxThumbnailSize"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('maxThumbnailSize') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="maxImageCount" class="block text-sm font-medium text-gray-700 mb-2">
                        Max Images per Item
                    </label>
                    <input wire:model="maxImageCount" 
                           type="number" 
                           min="1" 
                           max="50"
                           id="maxImageCount"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('maxImageCount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="maxFileSize" class="block text-sm font-medium text-gray-700 mb-2">
                        Max File Size (KB)
                    </label>
                    <input wire:model="maxFileSize" 
                           type="number" 
                           min="100" 
                           max="102400"
                           id="maxFileSize"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('maxFileSize') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Allowed File Types -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Allowed File Types
                </label>
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($allowedFileTypes as $type)
                        <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                            {{ $type }}
                            <button type="button" 
                                    wire:click="removeFileType('{{ $type }}')"
                                    class="ml-2 text-purple-600 hover:text-purple-800">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </span>
                    @endforeach
                </div>
                
                <div class="flex space-x-2">
                    <input wire:model="newFileType" 
                           type="text" 
                           placeholder="e.g., mp4, avi"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <button type="button" 
                            wire:click="addFileType"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Add
                    </button>
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
            
            <div class="space-y-4">
                <div>
                    <label for="marketplaceTitle" class="block text-sm font-medium text-gray-700 mb-2">
                        Marketplace Title
                    </label>
                    <input wire:model="marketplaceTitle" 
                           type="text" 
                           id="marketplaceTitle"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('marketplaceTitle') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="marketplaceDescription" class="block text-sm font-medium text-gray-700 mb-2">
                        Marketplace Description
                    </label>
                    <textarea wire:model="marketplaceDescription" 
                              id="marketplaceDescription"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                    @error('marketplaceDescription') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="marketplaceKeywords" class="block text-sm font-medium text-gray-700 mb-2">
                        SEO Keywords (comma separated)
                    </label>
                    <textarea wire:model="marketplaceKeywords" 
                              id="marketplaceKeywords"
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                    @error('marketplaceKeywords') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Email Notification Settings</h3>
            
            <div class="space-y-4">
                <div class="flex items-center">
                    <input wire:model="emailNewOrders" 
                           type="checkbox" 
                           id="emailNewOrders"
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="emailNewOrders" class="ml-2 text-sm text-gray-700">
                        Email admins about new orders
                    </label>
                </div>

                <div class="flex items-center">
                    <input wire:model="emailItemApprovals" 
                           type="checkbox" 
                           id="emailItemApprovals"
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="emailItemApprovals" class="ml-2 text-gray-700">
                        Email admins about items needing approval
                    </label>
                </div>

                <div class="flex items-center">
                    <input wire:model="emailPayouts" 
                           type="checkbox" 
                           id="emailPayouts"
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="emailPayouts" class="ml-2 text-sm text-gray-700">
                        Email vendors about payouts
                    </label>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <button type="button" 
                        wire:click="resetToDefaults"
                        onclick="confirm('Are you sure you want to reset all settings to defaults?') || event.stopImmediatePropagation()"
                        class="px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors">
                    Reset to Defaults
                </button>
                
                <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Save Settings
                </button>
            </div>
        </div>
    </form>
</div>