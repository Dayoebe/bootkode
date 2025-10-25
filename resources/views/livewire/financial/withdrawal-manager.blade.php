<!-- resources/views/livewire/financial/withdrawal-manager.blade.php -->
<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Withdrawal Management</h1>
        <p class="text-gray-600">Request and track your earnings withdrawals</p>
    </div>

    <!-- Available Balance Card -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100">Available for Withdrawal</p>
                <p class="text-3xl font-bold">{{ $formattedBalance }}</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>
        @if($availableBalance >= 1000)
            <div class="mt-4">
                <button wire:click="openWithdrawalForm" class="bg-white text-green-600 py-2 px-6 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Request Withdrawal
                </button>
            </div>
        @else
            <div class="mt-4">
                <p class="text-green-100 text-sm">Minimum withdrawal amount is ₦1,000</p>
            </div>
        @endif
    </div>

    <!-- Withdrawal History -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Withdrawal History</h3>
        </div>
        <div class="overflow-x-auto">
            @if($withdrawals->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($withdrawals as $withdrawal)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $withdrawal->requested_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ $withdrawal->formatted_amount }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>
                                        <p class="font-medium">{{ $withdrawal->account_name }}</p>
                                        <p class="text-gray-500">{{ $withdrawal->account_number }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $withdrawal->status_color }}-100 text-{{ $withdrawal->status_color }}-800">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($withdrawal->isPending())
                                        <button class="text-red-600 hover:text-red-900">Cancel</button>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $withdrawals->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No withdrawals yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Your withdrawal requests will appear here.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Withdrawal Form Modal -->
    @if($showWithdrawalForm)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeWithdrawalForm"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="requestWithdrawal">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Request Withdrawal</h3>
                                    <div class="mt-4 space-y-4">
                                        <!-- Amount -->
                                        <div>
                                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount (₦)</label>
                                            <input
                                                type="number"
                                                id="amount"
                                                wire:model="amount"
                                                class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                placeholder="Enter amount"
                                                min="1000"
                                                max="{{ $availableBalance }}"
                                                step="0.01"
                                            >
                                            @error('amount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <!-- Bank Selection -->
                                        <div>
                                            <label for="bankCode" class="block text-sm font-medium text-gray-700">Bank</label>
                                            <select
                                                id="bankCode"
                                                wire:model="bankCode"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                            >
                                                <option value="">Select Bank</option>
                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('bankCode') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <!-- Account Number -->
                                        <div>
                                            <label for="accountNumber" class="block text-sm font-medium text-gray-700">Account Number</label>
                                            <input
                                                type="text"
                                                id="accountNumber"
                                                wire:model="accountNumber"
                                                class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                placeholder="Enter 10-digit account number"
                                                maxlength="10"
                                            >
                                            @error('accountNumber') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <!-- Account Name (Auto-resolved) -->
                                        <div>
                                            <label for="accountName" class="block text-sm font-medium text-gray-700">Account Name</label>
                                            <input
                                                type="text"
                                                id="accountName"
                                                wire:model="accountName"
                                                class="mt-1 shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                                placeholder="Account name will be resolved automatically"
                                                readonly
                                            >
                                            @if($isResolvingAccount)
                                                <p class="mt-2 text-sm text-blue-600">Resolving account details...</p>
                                            @endif
                                            @error('accountName') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Submit Request
                            </button>
                            <button type="button" wire:click="closeWithdrawalForm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
