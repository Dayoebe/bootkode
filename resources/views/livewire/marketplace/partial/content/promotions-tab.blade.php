{{-- resources/views/livewire/marketplace/partial/content/promotions-tab.blade.php --}}

<!-- Enhanced Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <i class="fas fa-tags text-blue-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-slate-600">Total Codes</p>
                <p class="text-2xl font-bold text-slate-900">{{ $promotionStats['total_codes'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-green-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-600">Active</p>
                <p class="text-2xl font-bold text-green-700">{{ $promotionStats['active_codes'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-red-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-lg">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-600">Expired</p>
                <p class="text-2xl font-bold text-red-700">{{ $promotionStats['expired_codes'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-purple-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
                <i class="fas fa-chart-line text-purple-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-purple-600">Uses</p>
                <p class="text-2xl font-bold text-purple-700">{{ number_format($promotionStats['total_uses'] ?? 0) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-emerald-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center">
            <div class="p-2 bg-emerald-100 rounded-lg">
                <i class="fas fa-money-bill-wave text-emerald-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-emerald-600">Savings</p>
                <p class="text-lg font-bold text-emerald-700">₦{{ number_format($promotionStats['total_savings'] ?? 0, 0) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-amber-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center">
            <div class="p-2 bg-amber-100 rounded-lg">
                <i class="fas fa-percentage text-amber-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-amber-600">Conv. Rate</p>
                <p class="text-2xl font-bold text-amber-700">{{ number_format($promotionStats['conversion_rate'] ?? 0, 1) }}%</p>
            </div>
        </div>
    </div>
</div>

<!-- Modern Filters -->
<div class="bg-white border border-slate-200 rounded-xl p-6 mb-6 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-400"></i>
            </div>
            <input wire:model.live.debounce.300ms="promotionSearch" 
                   type="text" 
                   placeholder="Search discount codes or descriptions..." 
                   class="block w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        
        <div class="flex flex-wrap gap-3">
            <select wire:model.live="statusFilter" 
                    class="px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium min-w-[120px]">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
            </select>

            <select wire:model.live="typeFilter" 
                    class="px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium min-w-[140px]">
                <option value="all">All Types</option>
                <option value="percentage">Percentage</option>
                <option value="fixed">Fixed Amount</option>
            </select>

            <button wire:click="openCreateModal" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                <i class="fas fa-plus mr-2"></i>
                New Code
            </button>
        </div>
    </div>
</div>

<!-- Discount Codes List -->
@if($discountCodes && $discountCodes->count() > 0)
    <div class="space-y-4">
        @foreach($discountCodes as $code)
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4 mb-4">
                            <!-- Code Badge -->
                            <div class="relative">
                                <span class="px-4 py-2 bg-slate-900 text-white font-mono text-lg font-bold rounded-xl">
                                    {{ $code->code }}
                                </span>
                                @if($code->is_active && (!$code->valid_until || $code->valid_until >= now()))
                                    <span class="absolute -top-2 -right-2 w-4 h-4 bg-green-500 rounded-full animate-pulse"></span>
                                @endif
                            </div>

                            <!-- Type and Value -->
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                    @if($code->type === 'percentage')
                                        {{ $code->value }}% OFF
                                    @else
                                        ₦{{ number_format($code->value, 0) }} OFF
                                    @endif
                                </span>
                                
                                <span class="px-3 py-1 bg-{{ $code->is_active && (!$code->valid_until || $code->valid_until >= now()) ? 'green' : 'red' }}-100 
                                           text-{{ $code->is_active && (!$code->valid_until || $code->valid_until >= now()) ? 'green' : 'red' }}-800 
                                           text-sm font-semibold rounded-full">
                                    @if($code->is_active && (!$code->valid_until || $code->valid_until >= now()))
                                        <i class="fas fa-check-circle mr-1"></i>Active
                                    @elseif(!$code->is_active)
                                        <i class="fas fa-pause-circle mr-1"></i>Paused
                                    @else
                                        <i class="fas fa-times-circle mr-1"></i>Expired
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Code Details -->
                        <div class="space-y-3">
                            @if($code->description)
                                <p class="text-slate-700 font-medium">{{ $code->description }}</p>
                            @endif

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-500">Usage:</span>
                                    <span class="font-semibold text-slate-900 ml-1">
                                        {{ number_format($code->used_count) }}
                                        @if($code->max_uses)
                                            / {{ number_format($code->max_uses) }}
                                        @else
                                            / Unlimited
                                        @endif
                                    </span>
                                </div>

                                @if($code->min_amount)
                                    <div>
                                        <span class="text-slate-500">Min. Order:</span>
                                        <span class="font-semibold text-slate-900 ml-1">₦{{ number_format($code->min_amount, 0) }}</span>
                                    </div>
                                @endif

                                <div>
                                    <span class="text-slate-500">Per User:</span>
                                    <span class="font-semibold text-slate-900 ml-1">{{ $code->uses_per_user }} use(s)</span>
                                </div>

                                <div>
                                    <span class="text-slate-500">Created:</span>
                                    <span class="font-semibold text-slate-900 ml-1">{{ $code->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <!-- Validity Period -->
                            @if($code->valid_from || $code->valid_until)
                                <div class="flex items-center space-x-4 text-sm p-3 bg-slate-50 rounded-lg">
                                    <i class="fas fa-calendar text-slate-500"></i>
                                    <div>
                                        @if($code->valid_from)
                                            <span class="text-slate-600">From: <span class="font-semibold">{{ $code->valid_from->format('M d, Y g:i A') }}</span></span>
                                        @endif
                                        @if($code->valid_from && $code->valid_until)
                                            <span class="mx-2 text-slate-400">•</span>
                                        @endif
                                        @if($code->valid_until)
                                            <span class="text-slate-600">Until: <span class="font-semibold">{{ $code->valid_until->format('M d, Y g:i A') }}</span></span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Usage Progress Bar -->
                            @if($code->max_uses)
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-600">Usage Progress</span>
                                        <span class="text-slate-900 font-semibold">
                                            {{ round(($code->used_count / $code->max_uses) * 100, 1) }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ min(($code->used_count / $code->max_uses) * 100, 100) }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-2 ml-6">
                        <button wire:click="toggleCodeStatus({{ $code->id }})"
                                class="px-4 py-2 {{ $code->is_active ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700' }} 
                                       text-white text-sm rounded-xl transition-colors font-medium">
                            <i class="fas fa-{{ $code->is_active ? 'pause' : 'play' }} mr-2"></i>
                            {{ $code->is_active ? 'Pause' : 'Activate' }}
                        </button>
                        
                        <button wire:click="editCode({{ $code->id }})"
                                class="px-4 py-2 border border-slate-300 text-slate-700 text-sm rounded-xl hover:bg-slate-50 transition-colors font-medium">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </button>
                        
                        <button wire:click="deleteCode({{ $code->id }})" 
                                onclick="return confirm('Are you sure you want to delete this discount code?')"
                                class="px-4 py-2 border border-red-300 text-red-700 text-sm rounded-xl hover:bg-red-50 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button>

                        <!-- Copy Code Button -->
                        <button onclick="navigator.clipboard.writeText('{{ $code->code }}'); 
                                       this.innerHTML = '<i class=\'fas fa-check mr-2\'></i>Copied!'; 
                                       setTimeout(() => this.innerHTML = '<i class=\'fas fa-copy mr-2\'></i>Copy Code', 2000)"
                                class="px-4 py-2 bg-slate-600 text-white text-sm rounded-xl hover:bg-slate-700 transition-colors font-medium">
                            <i class="fas fa-copy mr-2"></i>
                            Copy Code
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Enhanced Empty State -->
    <div class="text-center py-16 bg-white border border-slate-200 rounded-2xl shadow-sm">
        <div class="mx-auto w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-tags text-slate-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-slate-900 mb-3">
            @if($promotionSearch || $statusFilter !== 'all' || $typeFilter !== 'all')
                No discount codes match your filters
            @else
                No discount codes created yet
            @endif
        </h3>
        <p class="text-slate-500 max-w-md mx-auto mb-6">
            @if($promotionSearch || $statusFilter !== 'all' || $typeFilter !== 'all')
                Try adjusting your search criteria to find the discount codes you're looking for.
            @else
                Create your first discount code to start offering promotions to your customers.
            @endif
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @if($promotionSearch || $statusFilter !== 'all' || $typeFilter !== 'all')
                <button wire:click="$set('promotionSearch', ''); $set('statusFilter', 'all'); $set('typeFilter', 'all')" 
                        class="px-6 py-3 border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors font-medium">
                    <i class="fas fa-refresh mr-2"></i>Clear Filters
                </button>
            @endif
            <button wire:click="openCreateModal" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium shadow-sm hover:shadow-md">
                <i class="fas fa-plus mr-2"></i>Create Your First Code
            </button>
        </div>
    </div>
@endif