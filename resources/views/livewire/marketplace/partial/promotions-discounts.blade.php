{{-- resources/views/livewire/marketplace/partial/promotions-discounts.blade.php --}}
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Promotions & Discounts</h2>
                <p class="text-gray-600">Create and manage discount codes and promotional campaigns</p>
            </div>
            
            <button wire:click="openCreateModal" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Discount Code
            </button>
        </div>

        <!-- Stats Overview -->
        <div class="mt-6 grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
            <div class="p-4 bg-purple-50 rounded-lg">
                <div class="text-lg font-semibold text-purple-600">{{ $stats['total_codes'] }}</div>
                <div class="text-xs text-purple-600">Total Codes</div>
            </div>
            <div class="p-4 bg-green-50 rounded-lg">
                <div class="text-lg font-semibold text-green-600">{{ $stats['active_codes'] }}</div>
                <div class="text-xs text-green-600">Active</div>
            </div>
            <div class="p-4 bg-red-50 rounded-lg">
                <div class="text-lg font-semibold text-red-600">{{ $stats['expired_codes'] }}</div>
                <div class="text-xs text-red-600">Expired</div>
            </div>
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="text-lg font-semibold text-blue-600">{{ $stats['total_uses'] }}</div>
                <div class="text-xs text-blue-600">Total Uses</div>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg">
                <div class="text-lg font-semibold text-yellow-600">₦{{ number_format($stats['total_savings']) }}</div>
                <div class="text-xs text-yellow-600">Total Savings</div>
            </div>
            <div class="p-4 bg-indigo-50 rounded-lg">
                <div class="text-lg font-semibold text-indigo-600">{{ $stats['conversion_rate'] }}%</div>
                <div class="text-xs text-indigo-600">Conversion</div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <button wire:click="setActiveTab('codes')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'codes' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Discount Codes
                </button>
                <button wire:click="setActiveTab('campaigns')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'campaigns' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Campaigns
                </button>
                <button wire:click="setActiveTab('analytics')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'analytics' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Analytics
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'codes')
                <!-- Filters -->
                <div class="mb-6 flex flex-wrap gap-3">
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" 
                               type="text" 
                               placeholder="Search codes..." 
                               class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    
                    <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>

                    <select wire:model.live="typeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="all">All Types</option>
                        <option value="percentage">Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>

                <!-- Discount Codes List -->
                @if($discountCodes->count() > 0)
                    <div class="space-y-4">
                        @foreach($discountCodes as $code)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-mono font-semibold text-purple-600">{{ $code->code }}</h3>
                                            
                                            <span class="px-2 py-1 bg-{{ $code->type === 'percentage' ? 'blue' : 'green' }}-100 text-{{ $code->type === 'percentage' ? 'blue' : 'green' }}-800 text-xs font-medium rounded-full">
                                                {{ $code->type === 'percentage' ? $code->value . '%' : '₦' . number_format($code->value) }} OFF
                                            </span>
                                            
                                            @if($code->is_active)
                                                @if($code->valid_until && $code->valid_until < now())
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                                        Expired
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                        Active
                                                    </span>
                                                @endif
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($code->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $code->description }}</p>
                                        @endif
                                        
                                        <div class="mt-2 flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                            <span><i class="fas fa-calendar mr-1"></i>
                                                {{ $code->valid_from ? $code->valid_from->format('M d') : 'No start' }} - 
                                                {{ $code->valid_until ? $code->valid_until->format('M d, Y') : 'No expiry' }}
                                            </span>
                                            
                                            <span><i class="fas fa-users mr-1"></i>
                                                {{ $code->used_count }}/{{ $code->max_uses ?? '∞' }} uses
                                            </span>
                                            
                                            @if($code->min_amount)
                                                <span><i class="fas fa-tag mr-1"></i>
                                                    Min: ₦{{ number_format($code->min_amount) }}
                                                </span>
                                            @endif
                                            
                                            <span><i class="fas fa-user mr-1"></i>
                                                {{ $code->uses_per_user }} per user
                                            </span>
                                        </div>

                                        <!-- Usage Progress -->
                                        @if($code->max_uses)
                                            <div class="mt-3">
                                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                    <span>Usage</span>
                                                    <span>{{ $code->used_count }}/{{ $code->max_uses }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($code->used_count / $code->max_uses) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button onclick="navigator.clipboard.writeText('{{ $code->code }}')"
                                                class="p-2 text-gray-400 hover:text-purple-600 rounded">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        
                                        <button wire:click="toggleCodeStatus({{ $code->id }})"
                                                class="p-2 text-gray-400 hover:text-{{ $code->is_active ? 'red' : 'green' }}-600 rounded">
                                            <i class="fas fa-{{ $code->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                        
                                        <button wire:click="deleteCode({{ $code->id }})"
                                                onclick="confirm('Delete this discount code?') || event.stopImmediatePropagation()"
                                                class="p-2 text-gray-400 hover:text-red-600 rounded">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-percentage text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No discount codes found</h3>
                        <p class="text-gray-500 mb-6">Create your first discount code to start offering promotions.</p>
                        <button wire:click="openCreateModal"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create Discount Code
                        </button>
                    </div>
                @endif

            @elseif($activeTab === 'campaigns')
                <div class="text-center py-12">
                    <i class="fas fa-bullhorn text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Marketing Campaigns</h3>
                    <p class="text-gray-500">Campaign management features coming soon</p>
                </div>

            @elseif($activeTab === 'analytics')
                <div class="text-center py-12">
                    <i class="fas fa-chart-line text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Promotion Analytics</h3>
                    <p class="text-gray-500">Detailed analytics and reporting coming soon</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Discount Code Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-lg w-full p-6 max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Create Discount Code</h3>
                    <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form wire:submit.prevent="createDiscountCode" class="space-y-4">
                    <!-- Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount Code *</label>
                        <div class="flex space-x-2">
                            <input wire:model="code" 
                                   type="text" 
                                   placeholder="e.g., SAVE20"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <button type="button" 
                                    wire:click="generateCode"
                                    class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                Generate
                            </button>
                        </div>
                        @error('code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Type and Value -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select wire:model="type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₦)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Value * {{ $type === 'percentage' ? '(%)' : '(₦)' }}
                            </label>
                            <input wire:model="value" 
                                   type="number" 
                                   step="0.01" 
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            @error('value') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Conditions -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min Amount (₦)</label>
                            <input wire:model="minAmount" 
                                   type="number" 
                                   step="0.01" 
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uses per User</label>
                            <input wire:model="usesPerUser" 
                                   type="number" 
                                   min="1" 
                                   max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- Usage Limits -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Usage Limit</label>
                        <input wire:model="maxUses" 
                               type="number" 
                               min="1"
                               placeholder="Leave empty for unlimited"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Validity Period -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valid From</label>
                            <input wire:model="validFrom" 
                                   type="datetime-local"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label>
                            <input wire:model="validUntil" 
                                   type="datetime-local"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="description" 
                                  rows="2"
                                  placeholder="Internal description of this discount code"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <input wire:model="isActive" 
                               type="checkbox" 
                               id="isActive"
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <label for="isActive" class="ml-2 text-sm text-gray-700">
                            Activate immediately
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" 
                                wire:click="closeCreateModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Create Code
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>