{{-- resources/views/livewire/marketplace/partial/system/settings-tab.blade.php --}}
<div class="space-y-6">
    <form wire:submit.prevent="saveSettings">
        <!-- General Settings -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-6">General Settings</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-content mb-2">Marketplace Title</label>
                    <input aria-label="Marketplace title" type="text"
                           wire:model="marketplaceTitle"
                           class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                    @error('marketplaceTitle')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-content mb-2">Default Currency</label>
                    <select aria-label="Default currency" wire:model="defaultCurrency"
                            class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                        @if(isset($currencies))
                            @foreach($currencies as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        @else
                            <option value="NGN">Nigerian Naira (₦)</option>
                            <option value="USD">US Dollar ($)</option>
                            <option value="EUR">Euro (€)</option>
                            <option value="GBP">British Pound (£)</option>
                        @endif
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-content mb-2">Marketplace Description</label>
                    <textarea aria-label="Enter marketplace description" wire:model="marketplaceDescription"
                              rows="3"
                              class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                              placeholder="Enter marketplace description..."></textarea>
                    @error('marketplaceDescription')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-content mb-2">SEO Keywords</label>
                    <input aria-label="Separate keywords with commas" type="text"
                           wire:model="marketplaceKeywords"
                           placeholder="Separate keywords with commas"
                           class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                    @error('marketplaceKeywords')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Commission & Pricing Settings -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-6">Commission & Pricing</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-content mb-2">Platform Commission (%)</label>
                    <div class="relative">
                        <input aria-label="Platform commission" type="number"
                               wire:model="platformCommission"
                               min="0"
                               max="100"
                               step="0.01"
                               class="w-full border border-line rounded-lg px-3 py-2 pr-8 focus:ring-brand/30 focus:border-brand">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-content-muted text-sm">%</span>
                        </div>
                    </div>
                    @error('platformCommission')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-content-muted mt-1">Current rate: {{ $platformCommission }}% platform, {{ 100 - $platformCommission }}% vendor</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-content mb-2">Minimum Price</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-content-muted sm:text-sm">₦</span>
                        </div>
                        <input aria-label="Minimum price" type="number"
                               wire:model="minimumPrice"
                               min="0"
                               step="100"
                               class="w-full border border-line rounded-lg pl-7 pr-3 py-2 focus:ring-brand/30 focus:border-brand">
                    </div>
                    @error('minimumPrice')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-content mb-2">Maximum Price</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-content-muted sm:text-sm">₦</span>
                        </div>
                        <input aria-label="Maximum price" type="number"
                               wire:model="maximumPrice"
                               min="100"
                               step="1000"
                               class="w-full border border-line rounded-lg pl-7 pr-3 py-2 focus:ring-brand/30 focus:border-brand">
                    </div>
                    @error('maximumPrice')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Vendor Management Settings -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-6">Vendor Management</h3>

            <div class="space-y-6">
                <div class="flex items-center justify-between py-4 border-b border-line">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-content">Auto-approve Instructors</h4>
                        <p class="text-xs text-content-muted mt-1">Automatically approve items from verified instructors without manual review</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="autoApproveInstructors" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-4 border-b border-line">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-content">Require Admin Approval</h4>
                        <p class="text-xs text-content-muted mt-1">All items require admin approval before publishing (overrides auto-approve)</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="requireAdminApproval" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Feature Settings -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-6">Feature Settings</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between py-4 border-b border-line">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-content">Enable Reviews</h4>
                            <p class="text-xs text-content-muted mt-1">Allow customers to review and rate purchased items</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="enableReviews" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-4">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-content">Enable Promotions</h4>
                            <p class="text-xs text-content-muted mt-1">Allow discount codes, coupons, and promotional campaigns</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="enablePromotions" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Upload Settings -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-6">File Upload Settings</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-content mb-2">Max Thumbnail Size (KB)</label>
                    <input aria-label="Max thumbnail size" type="number"
                           wire:model="maxThumbnailSize"
                           min="100"
                           max="10240"
                           class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                    @error('maxThumbnailSize')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-content-muted mt-1">Current: {{ $maxThumbnailSize ?? 2048 }} KB</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-content mb-2">Max Image Count</label>
                    <input aria-label="Max image count" type="number"
                           wire:model="maxImageCount"
                           min="1"
                           max="50"
                           class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                    @error('maxImageCount')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-content-muted mt-1">Per item: {{ $maxImageCount ?? 10 }} images</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-content mb-2">Max File Size (KB)</label>
                    <input aria-label="Max file size" type="number"
                           wire:model="maxFileSize"
                           min="100"
                           max="102400"
                           class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                    @error('maxFileSize')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-content-muted mt-1">Per file: {{ number_format(($maxFileSize ?? 10240) / 1024, 1) }} MB</p>
                </div>
            </div>

            <!-- Allowed File Types -->
            <div>
                <label class="block text-sm font-medium text-content mb-3">Allowed File Types</label>
                <div class="flex flex-wrap gap-2 mb-4">
                    @if(is_array($allowedFileTypes) && count($allowedFileTypes) > 0)
                        @foreach($allowedFileTypes as $index => $type)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-brand/10 text-brand">
                                .{{ $type }}
                                <button type="button"
                                        wire:click="removeFileType('{{ $type }}')"
                                        class="ml-2 text-brand hover:text-brand" aria-label="Close">
                                    <i class="fas fa-times text-xs" aria-hidden="true"></i>
                                </button>
                            </span>
                        @endforeach
                    @else
                        <span class="text-content-muted text-sm">No file types configured</span>
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    <input aria-label="Add new file type (e.g., mp4, pdf, zip)" type="text"
                           wire:model="newFileType"
                           placeholder="Add new file type (e.g., mp4, pdf, zip)"
                           class="flex-1 border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                    <button type="button"
                            wire:click="addFileType"
                            class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action transition-colors">
                        <i class="fas fa-plus mr-1" aria-hidden="true"></i>Add
                    </button>
                </div>
                <p class="text-xs text-content-muted mt-2">Add common file extensions without the dot (e.g., pdf, zip, mp4)</p>
            </div>
        </div>

        <!-- Email Notification Settings -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-6">Email Notifications</h3>

            <div class="space-y-6">
                <div class="flex items-center justify-between py-4 border-b border-line">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-content">New Orders</h4>
                        <p class="text-xs text-content-muted mt-1">Send email notifications to vendors when new orders are placed</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="emailNewOrders" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-4 border-b border-line">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-content">Item Approvals</h4>
                        <p class="text-xs text-content-muted mt-1">Send notifications when items are approved or rejected</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="emailItemApprovals" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between py-4">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-content">Payout Notifications</h4>
                        <p class="text-xs text-content-muted mt-1">Send notifications about vendor payouts and earnings</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="emailPayouts" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-action"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Advanced Settings -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-6">Advanced Settings</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-content mb-3">Cache Settings</h4>
                    <div class="space-y-3">
                        <button type="button"
                                class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-sync mr-2" aria-hidden="true"></i>Clear Marketplace Cache
                        </button>
                        <button type="button"
                                class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-database mr-2" aria-hidden="true"></i>Rebuild Search Index
                        </button>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-content mb-3">Maintenance Mode</h4>
                    <div class="space-y-3">
                        <button type="button"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-tools mr-2" aria-hidden="true"></i>Enable Maintenance Mode
                        </button>
                        <p class="text-xs text-content-muted">This will disable the marketplace for customers while allowing admin access</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center justify-between">
                <div class="flex space-x-3">
                    <button type="button"
                            wire:click="resetToDefaults"
                            wire:confirm="Are you sure you want to reset all settings to defaults? This action cannot be undone."
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-undo mr-2" aria-hidden="true"></i>Reset to Defaults
                    </button>

                    <button type="button"
                            class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action transition-colors">
                        <i class="fas fa-download mr-2" aria-hidden="true"></i>Export Settings
                    </button>
                </div>

                <div class="flex space-x-3">
                    <button type="button"
                            class="px-4 py-2 border border-line text-content rounded-lg hover:bg-surface-muted transition-colors">
                        <i class="fas fa-times mr-2" aria-hidden="true"></i>Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action transition-colors">
                        <i class="fas fa-save mr-2" aria-hidden="true"></i>Save Settings
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Settings Info Panel -->
    <div class="bg-brand/10 rounded-lg border border-brand p-6">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-brand mt-1" aria-hidden="true"></i>
            </div>
            <div>
                <h4 class="text-sm font-medium text-brand mb-2">Settings Information</h4>
                <div class="text-sm text-brand space-y-1">
                    <p><strong>Last Updated:</strong> {{ now()->format('M d, Y h:i A') }}</p>
                    <p><strong>Settings Cache:</strong> 30 days (auto-refresh)</p>
                    <p><strong>Applied To:</strong> All marketplace transactions and interactions</p>
                </div>
                <div class="mt-3">
                    <p class="text-xs text-brand">
                        <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                        Changes to commission rates will only apply to new orders. Existing orders retain their original commission structure.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
