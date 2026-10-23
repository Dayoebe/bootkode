{{-- resources/views/livewire/marketplace/partial/content/content-modals.blade.php --}}

<!-- Modern Create Discount Code Modal -->
@if($showCreateModal)
    <div class="fixed inset-0 bg-slate-900/50  z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Create Discount Code" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showCreateModal&#x27;, false)">
        <div class="bg-surface rounded-lg max-w-2xl w-full max-h-[95vh] overflow-y-auto shadow-sm border border-line">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-surface border-b border-line px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-brand/10 rounded-lg">
                            <i aria-hidden="true" class="fas fa-tags text-brand text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-content">Create Discount Code</h3>
                            <p class="text-sm text-content-muted">Set up a new promotional discount for your marketplace</p>
                        </div>
                    </div>
                    <button wire:click="closeCreateModal"
                            class="p-2 text-content-muted hover:text-content-muted hover:bg-surface-muted rounded-lg transition-all" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="createDiscountCode" class="p-8 space-y-6">
                <!-- Code Generation Section -->
                <div class="bg-surface-muted border border-line rounded-lg p-6">
                    <label class="block text-sm font-semibold text-content mb-3">
                        <i aria-hidden="true" class="fas fa-tag mr-2"></i>
                        Discount Code
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="flex space-x-3">
                        <input aria-label="Enter unique code (e.g., SAVE20)" wire:model="code"
                               type="text"
                               placeholder="Enter unique code (e.g., SAVE20)"
                               class="flex-1 px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-brand font-mono text-lg">
                        <button type="button"
                                wire:click="generateCode"
                                class="px-6 py-3 bg-action text-action-foreground rounded-lg hover:bg-action transition-all duration-200 font-medium shadow-sm">
                            <i aria-hidden="true" class="fas fa-magic mr-2"></i>
                            Generate
                        </button>
                    </div>
                    @error('code')
                        <p class="text-red-600 text-sm mt-2 flex items-center">
                            <i aria-hidden="true" class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                    <p class="text-xs text-content-muted mt-2">Use a memorable code that customers can easily type</p>
                </div>

                <!-- Discount Configuration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-content mb-3">
                            <i aria-hidden="true" class="fas fa-cogs mr-2"></i>
                            Discount Type
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <select aria-label="Type" wire:model.live="type"
                                class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-brand font-medium">
                            <option value="percentage">Percentage Discount (%)</option>
                            <option value="fixed">Fixed Amount (₦)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-content mb-3">
                            <i aria-hidden="true" class="fas fa-calculator mr-2"></i>
                            Discount Value
                            <span class="text-red-500 ml-1">*</span>
                            <span class="text-xs text-content-muted ml-1">
                                {{ $type === 'percentage' ? '(1-100%)' : '(₦ Amount)' }}
                            </span>
                        </label>
                        <div class="relative">
                            <input aria-label="Value" wire:model="value"
                                   type="number"
                                   step="{{ $type === 'percentage' ? '1' : '100' }}"
                                   min="0"
                                   max="{{ $type === 'percentage' ? '100' : '1000000' }}"
                                   placeholder="{{ $type === 'percentage' ? '20' : '5000' }}"
                                   class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-brand">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-content-muted text-sm font-medium">
                                    {{ $type === 'percentage' ? '%' : '₦' }}
                                </span>
                            </div>
                        </div>
                        @error('value')
                            <p class="text-red-600 text-sm mt-2 flex items-center">
                                <i aria-hidden="true" class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Usage Conditions -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                    <h4 class="text-sm font-semibold text-amber-800 mb-4 flex items-center">
                        <i aria-hidden="true" class="fas fa-shield-alt mr-2"></i>
                        Usage Conditions
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-content mb-2">
                                Minimum Order Amount (₦)
                            </label>
                            <input aria-label="e.g., 10000" wire:model="minAmount"
                                   type="number"
                                   step="100"
                                   min="0"
                                   placeholder="e.g., 10000"
                                   class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <p class="text-xs text-content-muted mt-1">Leave empty for no minimum requirement</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content mb-2">
                                Uses per Customer
                            </label>
                            <input aria-label="1" wire:model="usesPerUser"
                                   type="number"
                                   min="1"
                                   max="100"
                                   placeholder="1"
                                   class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <p class="text-xs text-content-muted mt-1">How many times each customer can use this code</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-content mb-2">
                            Total Usage Limit
                        </label>
                        <input aria-label="Leave empty for unlimited uses" wire:model="maxUses"
                               type="number"
                               min="1"
                               placeholder="Leave empty for unlimited uses"
                               class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <p class="text-xs text-content-muted mt-1">Maximum total uses across all customers</p>
                    </div>
                </div>

                <!-- Validity Period -->
                <div class="bg-brand/10 border border-brand rounded-lg p-6">
                    <h4 class="text-sm font-semibold text-brand mb-4 flex items-center">
                        <i aria-hidden="true" class="fas fa-calendar mr-2"></i>
                        Validity Period
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-content mb-2">
                                Valid From
                            </label>
                            <input aria-label="Valid from" wire:model="validFrom"
                                   type="datetime-local"
                                   class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-brand">
                            <p class="text-xs text-content-muted mt-1">Leave empty to start immediately</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content mb-2">
                                Valid Until
                            </label>
                            <input aria-label="Valid until" wire:model="validUntil"
                                   type="datetime-local"
                                   class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-brand">
                            <p class="text-xs text-content-muted mt-1">Leave empty for no expiry</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-content mb-3">
                        <i aria-hidden="true" class="fas fa-align-left mr-2"></i>
                        Internal Description
                    </label>
                    <textarea aria-label="Internal notes about this discount code (not visible to customers)" wire:model="description"
                              rows="3"
                              placeholder="Internal notes about this discount code (not visible to customers)"
                              class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none"></textarea>
                    <p class="text-xs text-content-muted mt-1">This helps you remember the purpose of this code</p>
                </div>

                <!-- Activation Status -->
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                    <input wire:model="isActive"
                           type="checkbox"
                           id="isActive"
                           class="rounded border-line text-green-600 focus:ring-green-500 h-5 w-5">
                    <label for="isActive" class="ml-3 flex items-center cursor-pointer">
                        <i aria-hidden="true" class="fas fa-power-off mr-2 text-green-600"></i>
                        <span class="text-sm font-medium text-green-900">Activate code immediately after creation</span>
                    </label>
                </div>

                <!-- Preview Section -->
                @if($code && $value)
                    <div class="bg-surface-muted border border-line rounded-lg p-6">
                        <h4 class="text-sm font-semibold text-content mb-3 flex items-center">
                            <i aria-hidden="true" class="fas fa-eye mr-2"></i>
                            Code Preview
                        </h4>
                        <div class="bg-surface border border-line rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-mono text-lg font-bold text-content">{{ $code }}</span>
                                    <p class="text-sm text-content-muted mt-1">
                                        Get {{ $type === 'percentage' ? $value.'%' : '₦'.number_format($value, 0) }} off your order
                                        @if($minAmount)
                                            (minimum ₦{{ number_format($minAmount, 0) }})
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 bg-brand/10 text-brand text-sm font-semibold rounded-full">
                                        {{ $type === 'percentage' ? $value.'% OFF' : '₦'.number_format($value, 0).' OFF' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-line">
                    <button type="button"
                            wire:click="closeCreateModal"
                            class="px-6 py-3 border border-line rounded-lg text-content hover:bg-surface-muted transition-colors font-medium">
                        <i aria-hidden="true" class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-action text-action-foreground rounded-lg hover:bg-action transition-all duration-200 font-medium shadow-sm ">
                        <i aria-hidden="true" class="fas fa-plus mr-2"></i>
                        Create Discount Code
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Modern Review Moderation Modal -->
@if($showModerationModal && $selectedReview)
    <div class="fixed inset-0 bg-slate-900/50  z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Moderation modal" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showModerationModal&#x27;, false)">
        <div class="bg-surface rounded-lg max-w-lg w-full shadow-sm border border-line">
            <!-- Modal Header -->
            <div class="bg-surface-muted border-b border-line px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-100 rounded-lg">
                            <i aria-hidden="true" class="fas fa-{{ $moderationAction === 'delete' ? 'trash' : 'gavel' }} text-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-content">
                                {{ $moderationAction === 'delete' ? 'Delete Review' : 'Reject Review' }}
                            </h3>
                            <p class="text-sm text-content-muted">This action requires your attention</p>
                        </div>
                    </div>
                    <button wire:click="closeModerationModal"
                            class="p-2 text-content-muted hover:text-content-muted hover:bg-surface-muted rounded-lg transition-all" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-8">
                <!-- Review Preview -->
                <div class="mb-6 p-6 bg-surface-muted border border-line rounded-lg">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-content-muted font-semibold text-sm">
                                {{ $selectedReview->user ? strtoupper(substr($selectedReview->user->name, 0, 2)) : 'UN' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center mb-2">
                                <div class="flex mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i aria-hidden="true" class="fas fa-star text-{{ $i <= $selectedReview->rating ? 'yellow' : 'slate' }}-400 text-sm"></i>
                                    @endfor
                                </div>
                                <span class="text-sm font-semibold text-content">{{ $selectedReview->rating }}/5</span>
                            </div>
                            <p class="text-sm font-medium text-content mb-1">{{ $selectedReview->user->name ?? 'Anonymous User' }}</p>

                            @if($selectedReview->title)
                                <h5 class="font-semibold text-content mb-2">{{ $selectedReview->title }}</h5>
                            @endif

                            @if($selectedReview->comment)
                                <p class="text-sm text-content mb-2">{{ Str::limit($selectedReview->comment, 150) }}</p>
                            @endif

                            <p class="text-xs text-content-muted">
                                Review for: {{ $selectedReview->reviewable->title ?? 'Unknown Item' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($moderationAction === 'reject' || $moderationAction === 'delete')
                    <div class="mb-6">
                        <label for="moderationReason" class="block text-sm font-semibold text-content mb-3">
                            <i aria-hidden="true" class="fas fa-comment-alt mr-2"></i>
                            {{ $moderationAction === 'reject' ? 'Rejection' : 'Deletion' }} Reason
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <textarea wire:model="moderationReason"
                                  id="moderationReason"
                                  rows="4"
                                  placeholder="Please explain why this review is being {{ $moderationAction === 'reject' ? 'rejected' : 'deleted' }}..."
                                  class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-500 focus:border-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-500 resize-none"></textarea>
                        @error('moderationReason')
                            <p class="text-red-600 text-sm mt-2 flex items-center">
                                <i aria-hidden="true" class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                @endif

                <!-- Warning Message -->
                <div class="mb-6 p-4 bg-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-50 border border-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-200 rounded-lg">
                    <div class="flex items-start">
                        <i aria-hidden="true" class="fas fa-exclamation-triangle text-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-500 mr-3 mt-0.5"></i>
                        <div class="text-sm text-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-800">
                            <p class="font-semibold mb-2">
                                {{ $moderationAction === 'delete' ? 'Permanent Action Warning' : 'Review Rejection Notice' }}
                            </p>
                            @if($moderationAction === 'reject')
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Review will be hidden from public view</li>
                                    <li>Reviewer will be notified with your reason</li>
                                    <li>Action can be reversed by approving later</li>
                                </ul>
                            @else
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Review will be permanently deleted</li>
                                    <li>Item's rating average will be recalculated</li>
                                    <li>This action cannot be undone</li>
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <button wire:click="closeModerationModal"
                            class="px-6 py-3 border border-line rounded-lg text-content hover:bg-surface-muted transition-colors font-medium">
                        <i aria-hidden="true" class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </button>

                    @if($moderationAction === 'reject')
                        <button wire:click="rejectReview"
                                class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors font-medium">
                            <i aria-hidden="true" class="fas fa-times mr-2"></i>
                            Reject Review
                        </button>
                    @elseif($moderationAction === 'delete')
                        <button wire:click="deleteReview"
                                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i aria-hidden="true" class="fas fa-trash mr-2"></i>
                            Delete Permanently
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
