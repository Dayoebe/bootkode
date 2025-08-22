<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-comment-dots mr-2"></i> Feedback Management
        </h1>
    </div>
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search feedback..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex-1">
            <select wire:model.live="statusFilter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="open">Open</option>
                <option value="responded">Responded</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($feedbacks as $feedback)
                    <tr class="animate__animated animate__fadeInUp">
                        <td class="px-6 py-4">{{ $feedback->user->name }}</td>
                        <td class="px-6 py-4">{{ ucfirst($feedback->category) }}</td>
                        <td class="px-6 py-4">{{ $feedback->course?->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-1 text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $feedback->rating >= $i ? '' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ Str::limit($feedback->message, 50) }}</td>
                        <!-- In <td> for actions, add: -->
@if($feedback->attachment_url)
<a href="{{ $feedback->attachment_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 mr-2">
    <i class="fas fa-paperclip"></i> View Attachment
</a>
@endif
                        <td class="px-6 py-4">
                            <div x-data="{ response: '' }">
                                <input x-model="response" type="text" placeholder="Enter response..."
                                       class="px-2 py-1 border border-gray-300 rounded-md mb-2"
                                       @input="$wire.set('response', $event.target.value)">
                                <button wire:click="respond({{ $feedback->id }})"
                                        x-bind:disabled="!response"
                                        class="text-green-600 mr-2 disabled:opacity-50">
                                    <i class="fas fa-reply"></i> Respond
                                </button>
                                <button wire:click="close({{ $feedback->id }})" class="text-red-600">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $feedbacks->links() }}</div>
    </div>
</div>