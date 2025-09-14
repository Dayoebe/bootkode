{{-- resources/views/livewire/marketplace/partial/content/content-modals.blade.php --}}

<!-- Modern Create Discount Code Modal -->
@if($showCreateModal)
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[95vh] overflow-y-auto shadow-2xl border border-slate-200">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-slate-200 px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 rounded-xl">
                            <i class="fas fa-tags text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900">Create Discount Code</h3>
                            <p class="text-sm text-slate-600">Set up a new promotional discount for your marketplace</p>
                        </div>
                    </div>
                    <button wire:click="closeCreateModal" 
                            class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <form wire:submit.prevent="createDiscountCode" class="p-8 space-y-6">
                <!-- Code Generation Section -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">
                        <i class="fas fa-tag mr-2"></i>
                        Discount Code
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="flex space-x-3">
                        <input wire:model="code" 
                               type="text" 
                               placeholder="Enter unique code (e.g., SAVE20)"
                               class="flex-1 px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-lg">
                        <button type="button" 
                                wire:click="generateCode"
                                class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-sm">
                            <i class="fas fa-magic mr-2"></i>
                            Generate
                        </button>
                    </div>
                    @error('code') 
                        <p class="text-red-600 text-sm mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p> 
                    @enderror
                    <p class="text-xs text-slate-500 mt-2">Use a memorable code that customers can easily type</p>
                </div>

                <!-- Discount Configuration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                            <i class="fas fa-cogs mr-2"></i>
                            Discount Type
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <select wire:model.live="type" 
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium">
                            <option value="percentage">Percentage Discount (%)</option>
                            <option value="fixed">Fixed Amount (₦)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                            <i class="fas fa-calculator mr-2"></i>
                            Discount Value
                            <span class="text-red-500 ml-1">*</span>
                            <span class="text-xs text-slate-500 ml-1">
                                {{ $type === 'percentage' ? '(1-100%)' : '(₦ Amount)' }}
                            </span>
                        </label>
                        <div class="relative">
                            <input wire:model="value" 
                                   type="number" 
                                   step="{{ $type === 'percentage' ? '1' : '100' }}" 
                                   min="0"
                                   max="{{ $type === 'percentage' ? '100' : '1000000' }}"
                                   placeholder="{{ $type === 'percentage' ? '20' : '5000' }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 text-sm font-medium">
                                    {{ $type === 'percentage' ? '%' : '₦' }}
                                </span>
                            </div>
                        </div>
                        @error('value') 
                            <p class="text-red-600 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p> 
                        @enderror
                    </div>
                </div>

                <!-- Usage Conditions -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                    <h4 class="text-sm font-semibold text-amber-800 mb-4 flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Usage Conditions
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Minimum Order Amount (₦)
                            </label>
                            <input wire:model="minAmount" 
                                   type="number" 
                                   step="100" 
                                   min="0"
                                   placeholder="e.g., 10000"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <p class="text-xs text-slate-500 mt-1">Leave empty for no minimum requirement</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Uses per Customer
                            </label>
                            <input wire:model="usesPerUser" 
                                   type="number" 
                                   min="1" 
                                   max="100"
                                   placeholder="1"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <p class="text-xs text-slate-500 mt-1">How many times each customer can use this code</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Total Usage Limit
                        </label>
                        <input wire:model="maxUses" 
                               type="number" 
                               min="1"
                               placeholder="Leave empty for unlimited uses"
                               class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <p class="text-xs text-slate-500 mt-1">Maximum total uses across all customers</p>
                    </div>
                </div>

                <!-- Validity Period -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h4 class="text-sm font-semibold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-calendar mr-2"></i>
                        Validity Period
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Valid From
                            </label>
                            <input wire:model="validFrom" 
                                   type="datetime-local"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-slate-500 mt-1">Leave empty to start immediately</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Valid Until
                            </label>
                            <input wire:model="validUntil" 
                                   type="datetime-local"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-slate-500 mt-1">Leave empty for no expiry</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">
                        <i class="fas fa-align-left mr-2"></i>
                        Internal Description
                    </label>
                    <textarea wire:model="description" 
                              rows="3"
                              placeholder="Internal notes about this discount code (not visible to customers)"
                              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                    <p class="text-xs text-slate-500 mt-1">This helps you remember the purpose of this code</p>
                </div>

                <!-- Activation Status -->
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-xl">
                    <input wire:model="isActive" 
                           type="checkbox" 
                           id="isActive"
                           class="rounded border-slate-300 text-green-600 focus:ring-green-500 h-5 w-5">
                    <label for="isActive" class="ml-3 flex items-center cursor-pointer">
                        <i class="fas fa-power-off mr-2 text-green-600"></i>
                        <span class="text-sm font-medium text-green-900">Activate code immediately after creation</span>
                    </label>
                </div>

                <!-- Preview Section -->
                @if($code && $value)
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center">
                            <i class="fas fa-eye mr-2"></i>
                            Code Preview
                        </h4>
                        <div class="bg-white border border-slate-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-mono text-lg font-bold text-slate-900">{{ $code }}</span>
                                    <p class="text-sm text-slate-600 mt-1">
                                        Get {{ $type === 'percentage' ? $value.'%' : '₦'.number_format($value, 0) }} off your order
                                        @if($minAmount) 
                                            (minimum ₦{{ number_format($minAmount, 0) }})
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                        {{ $type === 'percentage' ? $value.'% OFF' : '₦'.number_format($value, 0).' OFF' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-slate-200">
                    <button type="button" 
                            wire:click="closeCreateModal"
                            class="px-6 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                        <i class="fas fa-plus mr-2"></i>
                        Create Discount Code
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Modern Review Moderation Modal -->
@if($showModerationModal && $selectedReview)
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200">
            <!-- Modal Header -->
            <div class="bg-slate-50 border-b border-slate-200 px-8 py-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-100 rounded-xl">
                            <i class="fas fa-{{ $moderationAction === 'delete' ? 'trash' : 'gavel' }} text-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900">
                                {{ $moderationAction === 'delete' ? 'Delete Review' : 'Reject Review' }}
                            </h3>
                            <p class="text-sm text-slate-600">This action requires your attention</p>
                        </div>
                    </div>
                    <button wire:click="closeModerationModal" 
                            class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-8">
                <!-- Review Preview -->
                <div class="mb-6 p-6 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-slate-600 font-semibold text-sm">
                                {{ $selectedReview->user ? strtoupper(substr($selectedReview->user->name, 0, 2)) : 'UN' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center mb-2">
                                <div class="flex mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-{{ $i <= $selectedReview->rating ? 'yellow' : 'slate' }}-400 text-sm"></i>
                                    @endfor
                                </div>
                                <span class="text-sm font-semibold text-slate-900">{{ $selectedReview->rating }}/5</span>
                            </div>
                            <p class="text-sm font-medium text-slate-900 mb-1">{{ $selectedReview->user->name ?? 'Anonymous User' }}</p>
                            
                            @if($selectedReview->title)
                                <h5 class="font-semibold text-slate-900 mb-2">{{ $selectedReview->title }}</h5>
                            @endif
                            
                            @if($selectedReview->comment)
                                <p class="text-sm text-slate-700 mb-2">{{ Str::limit($selectedReview->comment, 150) }}</p>
                            @endif
                            
                            <p class="text-xs text-slate-500">
                                Review for: {{ $selectedReview->reviewable->title ?? 'Unknown Item' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                @if($moderationAction === 'reject' || $moderationAction === 'delete')
                    <div class="mb-6">
                        <label for="moderationReason" class="block text-sm font-semibold text-slate-700 mb-3">
                            <i class="fas fa-comment-alt mr-2"></i>
                            {{ $moderationAction === 'reject' ? 'Rejection' : 'Deletion' }} Reason
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <textarea wire:model="moderationReason" 
                                  id="moderationReason"
                                  rows="4"
                                  placeholder="Please explain why this review is being {{ $moderationAction === 'reject' ? 'rejected' : 'deleted' }}..."
                                  class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-500 focus:border-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-500 resize-none"></textarea>
                        @error('moderationReason') 
                            <p class="text-red-600 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p> 
                        @enderror
                    </div>
                @endif

                <!-- Warning Message -->
                <div class="mb-6 p-4 bg-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-50 border border-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-200 rounded-xl">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-{{ $moderationAction === 'delete' ? 'red' : 'amber' }}-500 mr-3 mt-0.5"></i>
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
                            class="px-6 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </button>
                    
                    @if($moderationAction === 'reject')
                        <button wire:click="rejectReview"
                                class="px-6 py-3 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i>
                            Reject Review
                        </button>
                    @elseif($moderationAction === 'delete')
                        <button wire:click="deleteReview"
                                class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Permanently
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif