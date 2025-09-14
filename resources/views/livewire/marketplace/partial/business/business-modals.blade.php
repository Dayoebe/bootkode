{{-- resources/views/livewire/marketplace/partial/modals/business-modals.blade.php --}}

<!-- Notes Modal -->
@if($showNoteModal && $selectedOrder)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-sticky-note mr-2 text-purple-600"></i>
                    Notes for Order #{{ $selectedOrder->order_number }}
                </h3>
                <button wire:click="closeNoteModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Order Info -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    @if($selectedOrder->item->getPrimaryImage())
                        <img src="{{ asset('storage/' . $selectedOrder->item->getPrimaryImage()) }}" 
                             alt="{{ $selectedOrder->item->title }}" 
                             class="w-12 h-12 object-cover rounded">
                    @else
                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $selectedOrder->item->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $selectedOrder->customer->name }}</p>
                        <p class="text-xs text-gray-500">{{ $selectedOrder->formatted_vendor_earning }}</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="saveNotes">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Notes</label>
                    <textarea wire:model="vendorNotes" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Add private notes about this order (only visible to you)..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">These notes are private and only visible to you.</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="closeNoteModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
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
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>
                    Request Withdrawal
                </h3>
                <button wire:click="closeWithdrawalModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Available Balance Info -->
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-green-900">Available Balance</h4>
                        <p class="text-sm text-green-700">Ready for withdrawal</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600">₦{{ number_format($availableBalance, 2) }}</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="requestWithdrawal">
                <!-- Amount -->
                <div class="mb-4">
                    <label for="withdrawalAmount" class="block text-sm font-medium text-gray-700 mb-2">
                        Withdrawal Amount (₦) <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="withdrawalAmount" type="number" step="0.01" min="1000" max="{{ $availableBalance }}" 
                           id="withdrawalAmount" placeholder="Enter amount"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Minimum: ₦1,000 | Maximum: ₦{{ number_format($availableBalance, 2) }}</p>
                    @error('withdrawalAmount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Bank Selection -->
                <div class="mb-4">
                    <label for="selectedBankId" class="block text-sm font-medium text-gray-700 mb-2">
                        Bank <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="selectedBankId" id="selectedBankId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="">Select your bank</option>
                        @foreach($availableBanks as $bank)
                            <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                        @endforeach
                    </select>
                    @error('selectedBankId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Account Details Info -->
                <div class="mb-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        We'll use the account details from your profile. Make sure they're up to date.
                    </p>
                </div>

                <!-- Processing Info -->
                <div class="mb-6 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <h5 class="text-sm font-medium text-gray-900 mb-2">Processing Information</h5>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li class="flex items-center">
                            <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>
                            No additional fees deducted
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-2 text-gray-400"></i>
                            Processing time: 1-3 business days
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-gray-400"></i>
                            Secure bank transfer via Paystack
                        </li>
                    </ul>
                </div>

                <!-- Amount Summary -->
                @if($withdrawalAmount)
                    <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Withdrawal Summary</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount to withdraw:</span>
                                <span class="font-medium text-gray-900">₦{{ number_format($withdrawalAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Processing fee:</span>
                                <span class="font-medium text-green-600">₦0.00</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 flex justify-between">
                                <span class="font-medium text-gray-900">You will receive:</span>
                                <span class="font-bold text-green-600">₦{{ number_format($withdrawalAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Remaining balance:</span>
                                <span class="font-medium text-gray-900">₦{{ number_format($availableBalance - $withdrawalAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="closeWithdrawalModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Request Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif