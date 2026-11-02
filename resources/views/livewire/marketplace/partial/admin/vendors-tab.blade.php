{{-- resources/views/livewire/marketplace/admin/partials/vendors-tab.blade.php --}}
<div class="space-y-4">
    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-center">
        <div class="relative">
            <input aria-label="Search vendors" wire:model.live.debounce.300ms="search" type="text" placeholder="Search vendors..."
                class="w-64 pl-10 pr-4 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
            <i aria-hidden="true" class="fas fa-search absolute left-3 top-3 text-content-muted"></i>
        </div>

        <select aria-label="Vendor status" wire:model.live="vendorStatus"
            class="px-3 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="suspended">Suspended</option>
        </select>

        <div class="ml-auto flex gap-2">
            <button wire:click="exportVendors"
                class="px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                <i aria-hidden="true" class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Vendors Table -->
    @if(isset($users) && $users->count() > 0)
        <div class="bg-surface border border-line rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <x-ui.table label="Vendors Tab records"><table class="min-w-full divide-y divide-line">
                    <thead class="bg-surface-muted">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Vendor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Items</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Sales</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Commission</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-surface divide-y divide-line">
                        @foreach($users as $user)
                            <tr class="hover:bg-surface-muted">
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
                                                    class="h-10 w-10 rounded-full bg-brand/10 flex items-center justify-center">
                                                    <span
                                                        class="text-brand font-medium">{{ substr($user->name, 0, 2) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-content">{{ $user->name }}</div>
                                            <div class="text-sm text-content-muted">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                    {{ $user->marketplace_items_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                    ₦{{ number_format($user->total_earnings ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
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
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-muted text-content">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center gap-2 justify-end">
                                        @if($isPending)
                                            <button wire:click="openApprovalModal({{ $user->id }})"
                                                class="text-green-600 hover:text-green-900" title="Approve">
                                                <i aria-hidden="true" class="fas fa-check"></i>
                                            </button>
                                            <button wire:click="openRejectionModal({{ $user->id }})"
                                                class="text-red-600 hover:text-red-900" title="Reject">
                                                <i aria-hidden="true" class="fas fa-times"></i>
                                            </button>
                                        @elseif($isApproved && !$isSuspended)
                                            <button wire:click="suspendVendor({{ $user->id }})"
                                                onclick="confirm('Suspend this vendor?') || event.stopImmediatePropagation()"
                                                class="text-orange-600 hover:text-orange-900" title="Suspend">
                                                <i aria-hidden="true" class="fas fa-pause"></i>
                                            </button>
                                        @elseif($isSuspended)
                                            <button wire:click="reactivateVendor({{ $user->id }})"
                                                onclick="confirm('Reactivate this vendor?') || event.stopImmediatePropagation()"
                                                class="text-green-600 hover:text-green-900" title="Reactivate">
                                                <i aria-hidden="true" class="fas fa-play"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></x-ui.table>
            </div>

            <div class="px-6 py-4 border-t border-line">
                {{ $users->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-surface rounded-lg border border-line">
            <i aria-hidden="true" class="fas fa-user-tie text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-content mb-2">No vendors found</h3>
            <p class="text-content-muted">
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
