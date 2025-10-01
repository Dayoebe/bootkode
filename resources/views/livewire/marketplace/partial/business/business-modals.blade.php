{{-- resources/views/livewire/marketplace/partial/modals/business-modals.blade.php --}}

<!-- Notes Modal -->
@if($showNoteModal && $selectedOrder)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 flex items-center justify-center p-4 transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-sticky-note mr-2 text-blue-600 dark:text-blue-400"></i>
                    Notes for Order #{{ $selectedOrder->order_number }}
                </h3>
                <button wire:click="closeNoteModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Order Info -->
            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg transition-colors duration-300">
                <div class="flex items-center space-x-3">
                    @if($selectedOrder->item->getPrimaryImage())
                        <img src="{{ asset('storage/' . $selectedOrder->item->getPrimaryImage()) }}" 
                             alt="{{ $selectedOrder->item->title }}" 
                             class="w-12 h-12 object-cover rounded">
                    @else
                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $selectedOrder->item->title }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedOrder->customer->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $selectedOrder->formatted_vendor_earning }}</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="saveNotes">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Notes</label>
                    <textarea wire:model="vendorNotes" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300"
                        placeholder="Add private notes about this order (only visible to you)..."></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">These notes are private and only visible to you.</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="closeNoteModal"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300">
                        <i class="fas fa-save mr-2"></i>
                        Save Notes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Withdrawal Modal -->
@if($showWithdrawalModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 flex items-center justify-center p-4 transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-green-600 dark:text-green-400"></i>
                    Request Withdrawal
                </h3>
                <button wire:click="closeWithdrawalModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Available Balance Info -->
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg transition-colors duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-green-900 dark:text-green-300">Available Balance</h4>
                        <p class="text-sm text-green-700 dark:text-green-400">Ready for withdrawal</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">₦{{ number_format($availableBalance, 2) }}</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="requestWithdrawal">
                <!-- Amount -->
                <div class="mb-4">
                    <label for="withdrawalAmount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Withdrawal Amount (₦) <span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <input wire:model="withdrawalAmount" type="number" step="0.01" min="1000" max="{{ $availableBalance }}" 
                           id="withdrawalAmount" placeholder="Enter amount"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-green-500 focus:border-green-500 transition-colors duration-300">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum: ₦1,000 | Maximum: ₦{{ number_format($availableBalance, 2) }}</p>
                    @error('withdrawalAmount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Bank Selection -->
                <div class="mb-4">
                    <label for="selectedBankId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Bank <span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <select wire:model="selectedBankId" id="selectedBankId"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-green-500 focus:border-green-500 transition-colors duration-300">
                        <option value="">Select your bank</option>
                        @foreach($availableBanks as $bank)
                            <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                        @endforeach
                    </select>
                    @error('selectedBankId') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Account Details Info -->
                <div class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg transition-colors duration-300">
                    <p class="text-sm text-blue-800 dark:text-blue-300 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        We'll use the account details from your profile. Make sure they're up to date.
                    </p>
                </div>

                <!-- Processing Info -->
                <div class="mb-6 p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg transition-colors duration-300">
                    <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Processing Information</h5>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <li class="flex items-center">
                            <i class="fas fa-dollar-sign mr-2 text-gray-400 dark:text-gray-500"></i>
                            No additional fees deducted
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-2 text-gray-400 dark:text-gray-500"></i>
                            Processing time: 1-3 business days
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-gray-400 dark:text-gray-500"></i>
                            Secure bank transfer via Paystack
                        </li>
                    </ul>
                </div>

                <!-- Amount Summary -->
                @if($withdrawalAmount)
                    <div class="mb-6 p-4 bg-gradient-to-r from-green-50 dark:from-green-900/20 to-blue-50 dark:to-blue-900/20 border border-green-200 dark:border-green-800 rounded-lg transition-colors duration-300">
                        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Withdrawal Summary</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount to withdraw:</span>
                                <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($withdrawalAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Processing fee:</span>
                                <span class="font-medium text-green-600 dark:text-green-400">₦0.00</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-600 pt-2 flex justify-between">
                                <span class="font-medium text-gray-900 dark:text-white">You will receive:</span>
                                <span class="font-bold text-green-600 dark:text-green-400">₦{{ number_format($withdrawalAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Remaining balance:</span>
                                <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($availableBalance - $withdrawalAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="closeWithdrawalModal"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-colors duration-300 flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Request Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif