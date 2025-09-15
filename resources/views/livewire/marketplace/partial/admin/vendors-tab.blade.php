{{-- resources/views/livewire/marketplace/admin/partials/vendors-tab.blade.php --}}
<div class="space-y-4">
    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-center">
        <div class="relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search vendors..."
                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>

        <select wire:model.live="vendorStatus"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="suspended">Suspended</option>
        </select>

        <div class="ml-auto flex gap-2">
            <button wire:click="exportVendors"
                class="px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                <i class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Vendors Table -->
    @if(isset($users) && $users->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Commission</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($user->profile_picture)
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover"
                                                    src="{{ asset('storage/' . $user->profile_picture) }}"
                                                    alt="{{ $user->name }}">
                                            </div>
                                        @else
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                    <span
                                                        class="text-purple-600 font-medium">{{ substr($user->name, 0, 2) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->marketplace_items_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₦{{ number_format($user->total_earnings ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $metadata = $user->metadata ?? [];
                                        $commission = $metadata['vendor_commission_rate'] ?? 80;
                                    @endphp
                                    {{ $commission }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $metadata = $user->metadata ?? [];
                                        $isApproved = isset($metadata['vendor_approved']) && $metadata['vendor_approved'];
                                        $isRejected = isset($metadata['vendor_rejected']) && $metadata['vendor_rejected'];
                                        $isSuspended = isset($metadata['vendor_suspended']) && $metadata['vendor_suspended'];
                                        $isPending = !$isApproved && !$isRejected && $user->role === 'student';
                                    @endphp

                                    @if($isSuspended)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Suspended
                                        </span>
                                    @elseif($isApproved)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @elseif($isRejected)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @elseif($isPending)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center gap-2 justify-end">
                                        @if($isPending)
                                            <button wire:click="openApprovalModal({{ $user->id }})"
                                                class="text-green-600 hover:text-green-900" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button wire:click="openRejectionModal({{ $user->id }})"
                                                class="text-red-600 hover:text-red-900" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($isApproved && !$isSuspended)
                                            <button wire:click="suspendVendor({{ $user->id }})"
                                                onclick="confirm('Suspend this vendor?') || event.stopImmediatePropagation()"
                                                class="text-orange-600 hover:text-orange-900" title="Suspend">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        @elseif($isSuspended)
                                            <button wire:click="reactivateVendor({{ $user->id }})"
                                                onclick="confirm('Reactivate this vendor?') || event.stopImmediatePropagation()"
                                                class="text-green-600 hover:text-green-900" title="Reactivate">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
            <i class="fas fa-user-tie text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No vendors found</h3>
            <p class="text-gray-500">
                @if($vendorStatus === 'pending')
                    No pending vendor applications at this time.
                @elseif($search)
                    No vendors match your search criteria.
                @else
                    Vendor applications will appear here.
                @endif
            </p>
        </div>
    @endif
</div>