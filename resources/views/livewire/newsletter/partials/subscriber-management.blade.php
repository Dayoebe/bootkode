{{-- Subscriber Management View --}}
{{-- resources/views/livewire/newsletter/partials/subscriber-management.blade.php --}}
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
            <!-- Search -->
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search subscribers..."
                    class="pl-10 pr-4 py-2 w-full md:w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>

            <!-- Status Filter -->
            <select wire:model.live="statusFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="unsubscribed">Unsubscribed</option>
                <option value="bounced">Bounced</option>
            </select>

            <!-- Tag Filter -->
            <select wire:model.live="tagFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Tags</option>
                @foreach($availableTags as $tag)
                    <option value="{{ $tag }}">{{ $tag }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex space-x-2">
            <button wire:click="$set('showAddModal', true)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Subscriber
            </button>
            <button wire:click="$set('showImportModal', true)"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-upload mr-2"></i>Import CSV
            </button>
            <button wire:click="exportCsv"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if(!empty($selectedSubscribers))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-2 md:space-y-0">
                <span class="text-sm text-blue-700">
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
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subscriber</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subscribed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscribers as $subscriber)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" wire:model.live="selectedSubscribers" value="{{ $subscriber->id }}"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $subscriber->full_name ?: 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $subscriber->email }}</div>
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
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ $tag }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-gray-500">No tags</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $subscriber->subscribed_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button wire:click="editSubscriber({{ $subscriber->id }})"
                                        class="text-blue-600 hover:text-blue-900 text-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="deleteSubscriber({{ $subscriber->id }})"
                                        onclick="return confirm('Are you sure?')"
                                        class="text-red-600 hover:text-red-900 text-sm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                <p>No subscribers found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscribers->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>

    <!-- Add Subscriber Modal -->
    <div x-data="{ show: @entangle('showAddModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-medium mb-4">Add New Subscriber</h3>
                <form wire:submit.prevent="addSubscriber">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" wire:model="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" wire:model="firstName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" wire:model="lastName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tags (comma-separated)</label>
                            <input type="text" wire:model="tags" placeholder="e.g., VIP, Student, Premium"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="unsubscribed">Unsubscribed</option>
                                <option value="bounced">Bounced</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="show = false"
                            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Add Subscriber
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Subscriber Modal -->
    <div x-data="{ show: @entangle('showEditModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-medium mb-4">Edit Subscriber</h3>
                <form wire:submit.prevent="updateSubscriber">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" wire:model="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" wire:model="firstName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" wire:model="lastName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tags (comma-separated)</label>
                            <input type="text" wire:model="tags" placeholder="e.g., VIP, Student, Premium"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="unsubscribed">Unsubscribed</option>
                                <option value="bounced">Bounced</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="show = false"
                            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update Subscriber
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Import Modal -->
    <div x-data="{ show: @entangle('showImportModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto"
        x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-medium mb-4">Import Subscribers</h3>
                <form wire:submit.prevent="importCsv">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                            <input type="file" wire:model="csvFile" accept=".csv,.txt"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('csvFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-sm text-gray-600">
                            <p class="font-medium mb-2">CSV Format Requirements:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Required columns: email</li>
                                <li>Optional columns: first_name, last_name, tags</li>
                                <li>First row should contain column headers</li>
                                <li>Tags should be comma-separated in the tags column</li>
                            </ul>
                        </div>
                        @if($importResults)
                            <div class="bg-gray-50 p-3 rounded-lg">
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
                            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
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