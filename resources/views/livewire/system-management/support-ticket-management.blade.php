<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div
        class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-ticket-alt mr-2"></i> Support Ticket Management
        </h1>
    </div>
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tickets..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex-1">
            <select wire:model.live="statusFilter"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($tickets as $ticket)
                    <tr class="animate__animated animate__fadeInUp">
                        <td class="px-6 py-4">{{ $ticket->user->name }}</td>
                        <td class="px-6 py-4">{{ $ticket->subject }}</td>
                        <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</td>
                        <td class="px-6 py-4">
                            <div x-data="{ response: '' }">
                                <input x-model="response" type="text" placeholder="Enter response..."
                                    class="px-2 py-1 border border-gray-300 rounded-md">
                                <button wire:click="updateStatus({{ $ticket->id }}, 'resolved')"
                                    x-bind:disabled="!response" class="text-green-600 mr-2 disabled:opacity-50">
                                    <i class="fas fa-check"></i> Resolve
                                </button>
                                @if ($ticket->attachment_url)
                                    <a href="{{ $ticket->attachment_url }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-paperclip"></i> View Attachment
                                    </a>
                                @endif
                                <button wire:click="updateStatus({{ $ticket->id }}, 'in_progress')"
                                    class="text-blue-600">
                                    <i class="fas fa-cog"></i> In Progress
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $tickets->links() }}</div>
    </div>
</div>
