
<!-- resources/views/livewire/financial/admin/paystack-settings.blade.php -->
<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Paystack Settings</h1>
        <p class="text-gray-600">Configure payment gateway integration</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form wire:submit.prevent="save" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Public Key</label>
                <input wire:model="publicKey" type="text" class="mt-1 w-full rounded-md border-gray-300">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Secret Key</label>
                <input wire:model="secretKey" type="password" class="mt-1 w-full rounded-md border-gray-300">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Webhook URL (Read-only)</label>
                <input value="{{ $webhookUrl }}" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly>
            </div>
            
            <div class="flex items-center">
                <input wire:model="testMode" type="checkbox" class="rounded border-gray-300">
                <label class="ml-2 text-sm text-gray-700">Enable Test Mode</label>
            </div>
            
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Save Settings
                </button>
                <button type="button" wire:click="testConnection" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Test Connection
                </button>
            </div>
        </form>
    </div>
</div>