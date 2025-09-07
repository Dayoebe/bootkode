<!-- resources/views/livewire/financial/admin/financial-settings.blade.php -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Financial Settings</h1>
        <p class="text-gray-600">Configure platform financial parameters</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Commission Rate (%)</label>
                    <input wire:model="commissionRate" type="number" min="0" max="100" class="mt-1 w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Minimum Withdrawal (₦)</label>
                    <input wire:model="minimumWithdrawal" type="number" min="100" class="mt-1 w-full rounded-md border-gray-300">
                </div>
            </div>
            
            <div class="flex items-center">
                <input wire:model="autoApproveWithdrawals" type="checkbox" class="rounded border-gray-300">
                <label class="ml-2 text-sm text-gray-700">Auto-approve withdrawals under ₦10,000</label>
            </div>
            
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Save Settings
            </button>
        </form>
    </div>
</div>
