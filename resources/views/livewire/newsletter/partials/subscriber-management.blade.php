{{-- Subscriber Management View --}}
{{-- resources/views/livewire/newsletter/partials/subscriber-management.blade.php --}}
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
            <!-- Search -->
            <div class="relative">
                <input aria-label="Search subscribers" type="text" wire:model.live.debounce.300ms="search" placeholder="Search subscribers..."
                    class="pl-10 pr-4 py-2 w-full md:w-64 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                <i aria-hidden="true" class="fas fa-search absolute left-3 top-3 text-content-muted"></i>
            </div>

            <!-- Status Filter -->
            <select aria-label="Status filter" wire:model.live="statusFilter"
                class="px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="unsubscribed">Unsubscribed</option>
                <option value="bounced">Bounced</option>
            </select>

            <!-- Tag Filter -->
            <select aria-label="Tag filter" wire:model.live="tagFilter"
                class="px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30">
                <option value="">All Tags</option>
                @foreach($availableTags as $tag)
                    <option value="{{ $tag }}">{{ $tag }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex space-x-2">
            <button wire:click="$set('showAddModal', true)"
                class="bg-action hover:bg-action text-action-foreground px-4 py-2 rounded-lg font-medium transition-colors">
                <i aria-hidden="true" class="fas fa-plus mr-2"></i>Add Subscriber
            </button>
            <button wire:click="$set('showImportModal', true)"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i aria-hidden="true" class="fas fa-upload mr-2"></i>Import CSV
            </button>
            <button wire:click="exportCsv"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i aria-hidden="true" class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if(!empty($selectedSubscribers))
        <div class="bg-brand/10 border border-brand rounded-lg p-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-2 md:space-y-0">
                <span class="text-sm text-brand">
                    {{ count($selectedSubscribers) }} subscriber(s) selected
                </span>
                <div class="flex space-x-2">
                    <button wire:click="bulkAction('activate')"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Activate
                    </button>
                    <button wire:click="bulkAction('unsubscribe')"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Unsubscribe
                    </button>
                    <button wire:click="bulkAction('delete')"
                        onclick="return confirm('Are you sure you want to delete selected subscribers?')"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Subscribers Table -->
    <div class="bg-surface rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <x-ui.table label="Subscriber Management records"><table class="min-w-full divide-y divide-line">
                <thead class="bg-surface-muted">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">
                            <input aria-label="Select all" type="checkbox" wire:model.live="selectAll"
                                class="rounded border-line text-brand focus:ring-brand/30">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                            Subscriber</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                            Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Tags
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                            Subscribed</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-line">
                    @forelse($subscribers as $subscriber)
                        <tr class="hover:bg-surface-muted">
                            <td class="px-6 py-4">
                                <input aria-label="Selected subscribers" type="checkbox" wire:model.live="selectedSubscribers" value="{{ $subscriber->id }}"
                                    class="rounded border-line text-brand focus:ring-brand/30">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-content">
                                        {{ $subscriber->full_name ?: 'N/A' }}
                                    </div>
                                    <div class="text-sm text-content-muted">{{ $subscriber->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $subscriber->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $subscriber->status === 'unsubscribed' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $subscriber->status === 'bounced' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                    {{ ucfirst($subscriber->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($subscriber->tags)
                                        @foreach($subscriber->tags as $tag)
                                            <span class="px-2 py-1 text-xs bg-brand/10 text-brand rounded">{{ $tag }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-content-muted">No tags</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-content-muted">
                                {{ $subscriber->subscribed_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button wire:click="editSubscriber({{ $subscriber->id }})"
                                        class="text-brand hover:text-brand text-sm" title="Edit">
                                        <i aria-hidden="true" class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="deleteSubscriber({{ $subscriber->id }})"
                                        onclick="return confirm('Are you sure?')"
                                        class="text-red-600 hover:text-red-900 text-sm" title="Delete">
                                        <i aria-hidden="true" class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-content-muted">
                                <i aria-hidden="true" class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                <p>No subscribers found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table></x-ui.table>
        </div>

        @if($subscribers->hasPages())
            <div class="px-6 py-3 border-t border-line">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>

    <!-- Add Subscriber Modal -->
    <div x-data="{ show: @entangle('showAddModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-trap.inert.noscroll="show" role="dialog" aria-modal="true" aria-label="Add New Subscriber" tabindex="-1" @keydown.escape.stop="show = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-surface rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-medium mb-4">Add New Subscriber</h3>
                <form wire:submit.prevent="addSubscriber">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Email *</label>
                            <input aria-label="Email" type="email" wire:model="email" required
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">First Name</label>
                            <input aria-label="First name" type="text" wire:model="firstName"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Last Name</label>
                            <input aria-label="Last name" type="text" wire:model="lastName"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Tags (comma-separated)</label>
                            <input aria-label="e.g., VIP, Student, Premium" type="text" wire:model="tags" placeholder="e.g., VIP, Student, Premium"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Status</label>
                            <select aria-label="Status" wire:model="status"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="unsubscribed">Unsubscribed</option>
                                <option value="bounced">Bounced</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="show = false"
                            class="px-4 py-2 text-content border border-line rounded-lg hover:bg-surface-muted">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action">
                            Add Subscriber
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Subscriber Modal -->
    <div x-data="{ show: @entangle('showEditModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-trap.inert.noscroll="show" role="dialog" aria-modal="true" aria-label="Edit Subscriber" tabindex="-1" @keydown.escape.stop="show = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-surface rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-medium mb-4">Edit Subscriber</h3>
                <form wire:submit.prevent="updateSubscriber">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Email *</label>
                            <input aria-label="Email" type="email" wire:model="email" required
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">First Name</label>
                            <input aria-label="First name" type="text" wire:model="firstName"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Last Name</label>
                            <input aria-label="Last name" type="text" wire:model="lastName"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Tags (comma-separated)</label>
                            <input aria-label="e.g., VIP, Student, Premium" type="text" wire:model="tags" placeholder="e.g., VIP, Student, Premium"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Status</label>
                            <select aria-label="Status" wire:model="status"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="unsubscribed">Unsubscribed</option>
                                <option value="bounced">Bounced</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="show = false"
                            class="px-4 py-2 text-content border border-line rounded-lg hover:bg-surface-muted">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action">
                            Update Subscriber
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Import Modal -->
    <div x-data="{ show: @entangle('showImportModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto"
        x-cloak x-trap.inert.noscroll="show" role="dialog" aria-modal="true" aria-label="Import Subscribers" tabindex="-1" @keydown.escape.stop="show = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-surface rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-medium mb-4">Import Subscribers</h3>
                <form wire:submit.prevent="importCsv">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">CSV File</label>
                            <input aria-label="Csv file" type="file" wire:model="csvFile" accept=".csv,.txt"
                                class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent">
                            @error('csvFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-sm text-content-muted">
                            <p class="font-medium mb-2">CSV Format Requirements:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Required columns: email</li>
                                <li>Optional columns: first_name, last_name, tags</li>
                                <li>First row should contain column headers</li>
                                <li>Tags should be comma-separated in the tags column</li>
                            </ul>
                        </div>
                        @if($importResults)
                            <div class="bg-surface-muted p-3 rounded-lg">
                                <p class="text-sm font-medium text-green-600">Import completed:</p>
                                <p class="text-sm">{{ $importResults['imported'] }} imported,
                                    {{ $importResults['skipped'] }} skipped</p>
                                @if(!empty($importResults['errors']))
                                    <details class="mt-2">
                                        <summary class="text-sm text-red-600 cursor-pointer">View errors</summary>
                                        <ul class="text-xs text-red-600 mt-1 space-y-1">
                                            @foreach(array_slice($importResults['errors'], 0, 10) as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                            @if(count($importResults['errors']) > 10)
                                                <li>... and {{ count($importResults['errors']) - 10 }} more errors</li>
                                            @endif
                                        </ul>
                                    </details>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="show = false"
                            class="px-4 py-2 text-content border border-line rounded-lg hover:bg-surface-muted">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Import CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
