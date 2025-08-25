<div class="px-4 sm:px-6 lg:px-8 py-8" wire:poll.5000ms="pollTickets">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 rounded-2xl shadow-lg text-white mb-8 transition-all duration-300 hover:shadow-xl">
        <div class="flex items-center">
            <div class="bg-white/20 p-3 rounded-full mr-4">
                <i class="fas fa-question-circle text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-white">Help & Support Center</h1>
                <p class="text-blue-100 mt-2">Find answers, submit tickets, and track your requests</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-1 border border-gray-200">
            <nav class="flex space-x-1" aria-label="Tabs">
                <button @click="activeTab = 'faqs'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': activeTab === 'faqs', 'text-gray-500 hover:text-gray-700 border-transparent': activeTab !== 'faqs' }"
                        class="py-3 px-6 font-medium text-sm rounded-lg border flex items-center transition-all duration-200">
                    <i class="fas fa-book-open mr-2"></i> Knowledge Base
                </button>
                <button @click="activeTab = 'submit_ticket'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': activeTab === 'submit_ticket', 'text-gray-500 hover:text-gray-700 border-transparent': activeTab !== 'submit_ticket' }"
                        class="py-3 px-6 font-medium text-sm rounded-lg border flex items-center transition-all duration-200">
                    <i class="fas fa-plus-circle mr-2"></i> New Ticket
                </button>
                <button @click="activeTab = 'ticket_history'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': activeTab === 'ticket_history', 'text-gray-500 hover:text-gray-700 border-transparent': activeTab !== 'ticket_history' }"
                        class="py-3 px-6 font-medium text-sm rounded-lg border flex items-center transition-all duration-200">
                    <i class="fas fa-history mr-2"></i> My Tickets
                </button>
            </nav>
        </div>
    </div>

    <!-- FAQs -->
    <div x-show="activeTab === 'faqs'" x-transition.opacity.duration.300ms class="bg-white shadow rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Frequently Asked Questions</h2>
            <div class="relative w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input wire:model.live.debounce.300ms="searchFaq" type="text" placeholder="Search FAQs..."
                       class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <div x-data="{ openFaq: null }" class="space-y-4">
            @forelse($faqs as $faq)
                <div class="border border-gray-200 rounded-lg overflow-hidden transition-all duration-200 hover:shadow-sm">
                    <div class="flex justify-between items-center p-4 bg-gray-50 cursor-pointer" 
                         @click="openFaq = openFaq === '{{ $faq->id }}' ? null : '{{ $faq->id }}'">
                        <h3 class="text-lg font-medium text-gray-800">{{ $faq->question }}</h3>
                        <div class="flex-shrink-0 ml-4">
                            <i class="fas text-blue-600 transition-transform duration-300" 
                               :class="{ 'fa-chevron-up': openFaq === '{{ $faq->id }}', 'fa-chevron-down': openFaq !== '{{ $faq->id }}' }"></i>
                        </div>
                    </div>
                    <div x-show="openFaq === '{{ $faq->id }}'" x-transition class="p-4 bg-white border-t border-gray-200 text-gray-600">
                        {{ $faq->answer }}
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <i class="fas fa-search fa-2x text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No FAQs found matching your search.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Submit Ticket -->
    <div x-show="activeTab === 'submit_ticket'" x-transition.opacity.duration.300ms class="bg-white shadow rounded-xl p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Submit a New Support Request</h2>
        <form wire:submit.prevent="submitTicket">
            <div class="space-y-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input wire:model="subject" type="text" id="subject"
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="Briefly describe your issue">
                    @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" id="description" rows="5"
                              class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                              placeholder="Please provide detailed information about your issue"></textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Attachment (optional)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input wire:model="attachment" type="file" id="attachment" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, PDF up to 2MB</p>
                        </div>
                    </div>
                    @error('attachment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-8">
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition duration-200">
                    <span wire:loading.remove><i class="fas fa-paper-plane mr-2"></i> Submit Request</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Ticket History -->
    <div x-show="activeTab === 'ticket_history'" x-transition.opacity.duration.300ms class="bg-white shadow rounded-xl p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">My Support Tickets</h2>
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input wire:model.live.debounce.300ms="ticketSearch" type="text" placeholder="Search tickets..."
                       class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <select wire:model.live="ticketStatusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
        </div>
        
        <div x-data="{ openTicket: null }" class="space-y-4">
            @forelse($tickets as $ticket)
                <div class="border border-gray-200 rounded-lg overflow-hidden transition-all duration-200 hover:shadow-sm">
                    <div class="flex justify-between items-start p-4 bg-gray-50 cursor-pointer" 
                         @click="openTicket = openTicket === '{{ $ticket->id }}' ? null : '{{ $ticket->id }}'">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($ticket->status === 'open') bg-yellow-100 text-yellow-800
                                    @elseif($ticket->status === 'in_progress') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                <span class="ml-3 text-sm text-gray-500">{{ $ticket->created_at->format('M d, Y') }}</span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mt-2">{{ $ticket->subject }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($ticket->description, 100) }}</p>
                            @if($ticket->attachment)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($ticket->attachment) }}" target="_blank"
                                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-paperclip mr-1"></i> Attachment
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <i class="fas text-blue-600 transition-transform duration-300" 
                               :class="{ 'fa-chevron-up': openTicket === '{{ $ticket->id }}', 'fa-chevron-down': openTicket !== '{{ $ticket->id }}' }"></i>
                        </div>
                    </div>
                    <div x-show="openTicket === '{{ $ticket->id }}'" x-transition class="p-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">{{ $ticket->description }}</p>
                        @if($ticket->response)
                            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user-headset text-blue-500 text-lg"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Support Response</h4>
                                        <p class="mt-1 text-sm text-blue-700">{{ $ticket->response }}</p>
                                        @if($ticket->responder)
                                            <p class="mt-2 text-xs text-blue-600">By: {{ $ticket->responder->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-clock mr-2"></i> Your ticket is awaiting response from our support team
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <i class="fas fa-ticket-alt fa-2x text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No tickets found.</p>
                    <button @click="activeTab = 'submit_ticket'" class="mt-4 text-blue-600 hover:text-blue-800 font-medium">
                        Submit your first ticket
                    </button>
                </div>
            @endforelse
        </div>
        
        @if($tickets->hasPages())
            <div class="mt-6 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $tickets->firstItem() }}</span>
                            to <span class="font-medium">{{ $tickets->lastItem() }}</span>
                            of <span class="font-medium">{{ $tickets->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    // Alpine.js is already initialized by Livewire
});
</script>