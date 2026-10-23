{{-- resources/views/livewire/marketplace/partial/modals/business-modals.blade.php --}}

<!-- Notes Modal -->
@if($showNoteModal && $selectedOrder)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 flex items-center justify-center p-4 transition-colors duration-300" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Note modal" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showNoteModal&#x27;, false)">
        <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-md w-full p-6 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-content dark:text-white flex items-center">
                    <i class="fas fa-sticky-note mr-2 text-brand dark:text-brand"></i>
                    Notes for Order #{{ $selectedOrder->order_number }}
                </h3>
                <button wire:click="closeNoteModal" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-content-muted transition-colors duration-300" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Order Info -->
            <div class="mb-4 p-3 bg-surface-muted dark:bg-gray-700 rounded-lg transition-colors duration-300">
                <div class="flex items-center space-x-3">
                    @if($selectedOrder->item->getPrimaryImage())
                        <img src="{{ asset('storage/' . $selectedOrder->item->getPrimaryImage()) }}"
                             alt="{{ $selectedOrder->item->title }}"
                             class="w-12 h-12 object-cover rounded">
                    @else
                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                            <i class="fas fa-image text-content-muted dark:text-content-muted"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="font-medium text-content dark:text-white">{{ $selectedOrder->item->title }}</h4>
                        <p class="text-sm text-content-muted dark:text-content-muted">{{ $selectedOrder->customer->name }}</p>
                        <p class="text-xs text-content-muted dark:text-content-muted">{{ $selectedOrder->formatted_vendor_earning }}</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="saveNotes">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Your Notes</label>
                    <textarea aria-label="Add private notes about this order (only visible to you)" wire:model="vendorNotes" rows="4"
                        class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300"
                        placeholder="Add private notes about this order (only visible to you)..."></textarea>
                    <p class="text-xs text-content-muted dark:text-content-muted mt-1">These notes are private and only visible to you.</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="closeNoteModal"
                        class="px-4 py-2 border border-line dark:border-gray-600 rounded-lg text-content dark:text-gray-300 hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
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
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 flex items-center justify-center p-4 transition-colors duration-300" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Request Withdrawal" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showWithdrawalModal&#x27;, false)">
        <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-content dark:text-white flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-green-600 dark:text-green-400"></i>
                    Request Withdrawal
                </h3>
                <button wire:click="closeWithdrawalModal" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-content-muted transition-colors duration-300" aria-label="Close">
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
                    <label for="withdrawalAmount" class="block text-sm font-medium text-content dark:text-gray-300 mb-2">
                        Withdrawal Amount (₦) <span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <input wire:model="withdrawalAmount" type="number" step="0.01" min="1000" max="{{ $availableBalance }}"
                           id="withdrawalAmount" placeholder="Enter amount"
                           class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-green-500 focus:border-green-500 transition-colors duration-300">
                    <p class="text-xs text-content-muted dark:text-content-muted mt-1">Minimum: ₦1,000 | Maximum: ₦{{ number_format($availableBalance, 2) }}</p>
                    @error('withdrawalAmount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Bank Selection -->
                <div class="mb-4">
                    <label for="selectedBankId" class="block text-sm font-medium text-content dark:text-gray-300 mb-2">
                        Bank <span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <select wire:model="selectedBankId" id="selectedBankId"
                            class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-green-500 focus:border-green-500 transition-colors duration-300">
                        <option value="">Select your bank</option>
                        @foreach($availableBanks as $bank)
                            <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                        @endforeach
                    </select>
                    @error('selectedBankId') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Account Details Info -->
                <div class="mb-6 p-3 bg-brand/10 dark:bg-action/20 border border-brand dark:border-brand rounded-lg transition-colors duration-300">
                    <p class="text-sm text-brand dark:text-brand flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        We'll use the account details from your profile. Make sure they're up to date.
                    </p>
                </div>

                <!-- Processing Info -->
                <div class="mb-6 p-3 bg-surface-muted dark:bg-gray-700 border border-line dark:border-gray-600 rounded-lg transition-colors duration-300">
                    <h5 class="text-sm font-medium text-content dark:text-white mb-2">Processing Information</h5>
                    <ul class="text-xs text-content-muted dark:text-content-muted space-y-1">
                        <li class="flex items-center">
                            <i class="fas fa-dollar-sign mr-2 text-content-muted dark:text-content-muted"></i>
                            No additional fees deducted
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-2 text-content-muted dark:text-content-muted"></i>
                            Processing time: 1-3 business days
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-content-muted dark:text-content-muted"></i>
                            Secure bank transfer via Paystack
                        </li>
                    </ul>
                </div>

                <!-- Amount Summary -->
                @if($withdrawalAmount)
                    <div class="mb-6 p-4 bg-brand-ink     border border-green-200 dark:border-green-800 rounded-lg transition-colors duration-300">
                        <h5 class="text-sm font-medium text-content dark:text-white mb-2">Withdrawal Summary</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-content-muted dark:text-content-muted">Amount to withdraw:</span>
                                <span class="font-medium text-content dark:text-white">₦{{ number_format($withdrawalAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-content-muted dark:text-content-muted">Processing fee:</span>
                                <span class="font-medium text-green-600 dark:text-green-400">₦0.00</span>
                            </div>
                            <div class="border-t border-line dark:border-gray-600 pt-2 flex justify-between">
                                <span class="font-medium text-content dark:text-white">You will receive:</span>
                                <span class="font-bold text-green-600 dark:text-green-400">₦{{ number_format($withdrawalAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-content-muted dark:text-content-muted">Remaining balance:</span>
                                <span class="font-medium text-content dark:text-white">₦{{ number_format($availableBalance - $withdrawalAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="closeWithdrawalModal"
                        class="px-4 py-2 border border-line dark:border-gray-600 rounded-lg text-content dark:text-gray-300 hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
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
