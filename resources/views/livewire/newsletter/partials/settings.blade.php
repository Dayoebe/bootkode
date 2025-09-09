{{-- Settings View --}}
{{-- resources/views/livewire/newsletter/partials/settings.blade.php --}}
<div class="space-y-6">
    <form wire:submit.prevent="saveSettings" class="space-y-6">
        <!-- Email Configuration -->
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Email Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default From Name</label>
                    <input 
                        type="text" 
                        wire:model="settings.default_from_name" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('settings.default_from_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default From Email</label>
                    <input 
                        type="email" 
                        wire:model="settings.default_from_email" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('settings.default_from_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button 
                    type="button"
                    wire:click="testEmailConfiguration"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                >
                    <i class="fas fa-paper-plane mr-2"></i>Send Test Email
                </button>
            </div>
        </div>

        <!-- Sending Configuration -->
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sending Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Throttle Limit</label>
                    <input 
                        type="number" 
                        wire:model="settings.throttle_limit" 
                        min="1" 
                        max="1000"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <p class="text-xs text-gray-500 mt-1">Number of emails to send per batch</p>
                    @error('settings.throttle_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Throttle Delay (seconds)</label>
                    <input 
                        type="number" 
                        wire:model="settings.throttle_delay" 
                        min="1" 
                        max="300"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <p class="text-xs text-gray-500 mt-1">Delay between sending batches</p>
                    @error('settings.throttle_delay') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Unsubscribe Page -->
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Unsubscribe Page Content</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Title</label>
                    <input 
                        type="text" 
                        wire:model="settings.unsubscribe_page_content.title" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('settings.unsubscribe_page_content.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmation Message</label>
                    <textarea 
                        wire:model="settings.unsubscribe_page_content.message" 
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ></textarea>
                    @error('settings.unsubscribe_page_content.message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resubscribe Text</label>
                    <input 
                        type="text" 
                        wire:model="settings.unsubscribe_page_content.resubscribe_text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('settings.unsubscribe_page_content.resubscribe_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button 
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
            >
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
