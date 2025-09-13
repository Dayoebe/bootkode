{{-- resources/views/livewire/marketplace/partial/vendor-applications.blade.php --}}
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Vendor Applications</h2>
                <p class="text-gray-600">Manage marketplace vendor applications and permissions</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex items-center space-x-6 text-sm">
                <div class="text-center">
                    <div class="text-lg font-semibold text-yellow-600">{{ $totalPending }}</div>
                    <div class="text-gray-500">Pending</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-green-600">{{ $totalApproved }}</div>
                    <div class="text-gray-500">Approved</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-red-600">{{ $totalRejected }}</div>
                    <div class="text-gray-500">Rejected</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-orange-600">{{ $totalSuspended }}</div>
                    <div class="text-gray-500">Suspended</div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="mt-4 flex flex-wrap gap-3">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Search users..." 
                       class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <select wire:model.live="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="all">All Users</option>
                <option value="pending">Pending Applications</option>
                <option value="approved">Approved Vendors</option>
                <option value="rejected">Rejected Applications</option>
                <option value="suspended">Suspended Vendors</option>
            </select>
        </div>
    </div>

    <!-- Users List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($users->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- User Avatar -->
                                <div class="flex-shrink-0">
                                    @if($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                             alt="{{ $user->name }}" 
                                             class="w-16 h-16 object-cover rounded-full">
                                    @else
                                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-purple-600 font-medium text-lg">
                                                {{ substr($user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- User Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                                {{ $user->name }}
                                                @if($user->is_vendor)
                                                    <span class="ml-2 inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                        <i class="fas fa-store mr-1"></i>
                                                        Vendor
                                                    </span>
                                                @endif
                                                @if($user->is_suspended)
                                                    <span class="ml-2 inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                                        <i class="fas fa-ban mr-1"></i>
                                                        Suspended
                                                    </span>
                                                @endif
                                                @if($user->is_rejected)
                                                    <span class="ml-2 inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                                                        <i class="fas fa-times mr-1"></i>
                                                        Rejected
                                                    </span>
                                                @endif
                                            </h3>
                                            
                                            <div class="mt-1 space-y-1 text-sm text-gray-500">
                                                <p><span class="font-medium">Email:</span> {{ $user->email }}</p>
                                                <p><span class="font-medium">Role:</span> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                                                <p><span class="font-medium">Joined:</span> {{ $user->created_at->format('M d, Y') }}</p>
                                                @if($user->last_login_at)
                                                    <p><span class="font-medium">Last active:</span> {{ $user->last_login_at->diffForHumans() }}</p>
                                                @endif
                                            </div>

                                            <!-- Bio if available -->
                                            @if($user->bio)
                                                <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                                    <p class="text-sm text-gray-700">{{ Str::limit($user->bio, 150) }}</p>
                                                </div>
                                            @endif

                                            <!-- Vendor Stats -->
                                            @if($user->is_vendor)
                                                <div class="mt-3 grid grid-cols-2 md:grid-cols-5 gap-4 p-3 bg-green-50 rounded-lg">
                                                    <div class="text-center">
                                                        <div class="text-sm font-semibold text-green-900">{{ $user->vendor_stats['total_items'] }}</div>
                                                        <div class="text-xs text-green-600">Total Items</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-sm font-semibold text-green-900">{{ $user->vendor_stats['published_items'] }}</div>
                                                        <div class="text-xs text-green-600">Published</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-sm font-semibold text-green-900">{{ $user->vendor_stats['pending_items'] }}</div>
                                                        <div class="text-xs text-green-600">Pending</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-sm font-semibold text-green-900">{{ $user->vendor_stats['total_orders'] }}</div>
                                                        <div class="text-xs text-green-600">Orders</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-sm font-semibold text-green-900">₦{{ number_format($user->vendor_stats['total_earnings'], 0) }}</div>
                                                        <div class="text-xs text-green-600">Earnings</div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Rejection Reason -->
                                            @if($user->is_rejected && isset($user->metadata['vendor_rejection_reason']))
                                                <div class="mt-3 p-3 bg-red-50 rounded-lg">
                                                    <p class="text-sm text-red-900">
                                                        <span class="font-medium">Rejection reason:</span> 
                                                        {{ $user->metadata['vendor_rejection_reason'] }}
                                                    </p>
                                                    <p class="text-xs text-red-600 mt-1">
                                                        Rejected on {{ \Carbon\Carbon::parse($user->metadata['vendor_rejected_at'])->format('M d, Y') }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="mt-4 flex flex-wrap items-center gap-2">
                                        @if(!$user->is_vendor && !$user->is_rejected)
                                            <button wire:click="openApprovalModal({{ $user->id }})"
                                                    class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-2"></i>
                                                Approve as Vendor
                                            </button>
                                            
                                            <button wire:click="openRejectionModal({{ $user->id }})"
                                                    class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-times mr-2"></i>
                                                Reject Application
                                            </button>
                                        @endif

                                        @if($user->is_vendor && !$user->is_suspended)
                                            <button wire:click="suspendVendor({{ $user->id }})"
                                                    onclick="confirm('Are you sure you want to suspend this vendor? This will hide all their listings.') || event.stopImmediatePropagation()"
                                                    class="inline-flex items-center px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-colors">
                                                <i class="fas fa-ban mr-2"></i>
                                                Suspend Vendor
                                            </button>
                                        @endif

                                        @if($user->is_vendor && $user->is_suspended)
                                            <button wire:click="reactivateVendor({{ $user->id }})"
                                                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-play mr-2"></i>
                                                Reactivate Vendor
                                            </button>
                                        @endif
                                        
                                        <a href="mailto:{{ $user->email }}" 
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-envelope mr-2"></i>
                                            Contact User
                                        </a>

                                        @if($user->is_vendor)
                                            <a href="{{ route('marketplace.vendor.public', $user) }}" 
                                               class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                                <i class="fas fa-external-link-alt mr-2"></i>
                                                View Profile
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i class="fas fa-user-plus text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if($status === 'pending')
                        No pending applications
                    @elseif($status === 'approved')
                        No approved vendors
                    @elseif($status === 'rejected')
                        No rejected applications
                    @elseif($status === 'suspended')
                        No suspended vendors
                    @else
                        No users found
                    @endif
                </h3>
                <p class="text-gray-500">
                    @if($search)
                        Try adjusting your search criteria.
                    @else
                        @if($status === 'pending')
                            New vendor applications will appear here.
                        @else
                            Users matching your criteria will appear here.
                        @endif
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Approval Modal -->
    @if($showApprovalModal && $selectedUser)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Approve Vendor Application</h3>
                    <button wire:click="closeApprovalModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="flex items-center space-x-3 mb-3">
                        @if($selectedUser->profile_picture)
                            <img src="{{ asset('storage/' . $selectedUser->profile_picture) }}" 
                                 alt="{{ $selectedUser->name }}" 
                                 class="w-12 h-12 object-cover rounded-full">
                        @else
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-medium">
                                    {{ substr($selectedUser->name, 0, 2) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900">{{ $selectedUser->name }}</p>
                            <p class="text-sm text-gray-500">{{ $selectedUser->email }}</p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600">
                        This will allow {{ $selectedUser->name }} to create and sell items on the marketplace.
                    </p>
                </div>
                
                <div class="mb-4">
                    <label for="commissionRate" class="block text-sm font-medium text-gray-700 mb-2">
                        Vendor Commission Rate (%)
                    </label>
                    <input wire:model="commissionRate" 
                           type="number" 
                           min="0" 
                           max="100" 
                           step="5"
                           id="commissionRate"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Vendor will receive {{ $commissionRate }}% of sales, platform gets {{ 100 - $commissionRate }}%
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeApprovalModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="approveVendor"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Approve Vendor
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Rejection Modal -->
    @if($showRejectionModal && $selectedUser)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Reject Vendor Application</h3>
                    <button wire:click="closeRejectionModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="flex items-center space-x-3 mb-3">
                        @if($selectedUser->profile_picture)
                            <img src="{{ asset('storage/' . $selectedUser->profile_picture) }}" 
                                 alt="{{ $selectedUser->name }}" 
                                 class="w-12 h-12 object-cover rounded-full">
                        @else
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-medium">
                                    {{ substr($selectedUser->name, 0, 2) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900">{{ $selectedUser->name }}</p>
                            <p class="text-sm text-gray-500">{{ $selectedUser->email }}</p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-4">
                        Please provide a reason for rejecting this vendor application. This will be sent to the user.
                    </p>
                </div>
                
                <div class="mb-4">
                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-2">
                        Rejection Reason *
                    </label>
                    <textarea wire:model="rejectionReason" 
                              id="rejectionReason"
                              rows="4"
                              placeholder="Please explain why this application is being rejected..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                    @error('rejectionReason') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeRejectionModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="rejectVendor"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                            @if(empty($rejectionReason)) disabled @endif>
                        Reject Application
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>