<div class="min-h-screen bg-gray-800 py-8" wire:poll.5000ms="pollTickets">
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Enhanced Header -->
        <div
            class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-2xl overflow-hidden mb-8">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60"
                viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg
                fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="4"
                /%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

            <div class="relative p-8">
                <div class="flex items-center">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl mr-6 shadow-lg">
                        <i class="fas fa-question-circle text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">Help & Support Center</h1>
                        <p class="text-blue-100 text-lg">Find answers, submit tickets, and track your requests</p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center">
                            <i class="fas fa-book-open text-white/80 text-xl mr-3"></i>
                            <div>
                                <p class="text-white/80 text-sm">Knowledge Base</p>
                                <p class="text-white text-lg font-semibold">{{ $faqs->count() }} Articles</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center">
                            <i class="fas fa-ticket-alt text-white/80 text-xl mr-3"></i>
                            <div>
                                <p class="text-white/80 text-sm">Active Tickets</p>
                                <p class="text-white text-lg font-semibold">
                                    {{ $tickets->where('status', '!=', 'resolved')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-white/80 text-xl mr-3"></i>
                            <div>
                                <p class="text-white/80 text-sm">Avg Response</p>
                                <p class="text-white text-lg font-semibold">2-4 Hours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Tab Container - MOVED x-data HERE -->
        <div x-data="{
            activeTab: @entangle('activeTab').live,
            openFaq: null,
            openTicket: null,
            tabs: [
                { id: 'faqs', label: 'Knowledge Base', icon: 'fas fa-book-open', count: {{ $faqs->count() }} },
                { id: 'submit_ticket', label: 'New Ticket', icon: 'fas fa-plus-circle', count: null },
                { id: 'ticket_history', label: 'My Tickets', icon: 'fas fa-history', count: {{ $tickets->total() }} }
            ]
        }" class="mb-8">

            <!-- Enhanced Tabs Navigation -->
            <div class="mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-2">
                    <nav class="flex space-x-2" aria-label="Tabs">
                        <template x-for="tab in tabs" :key="tab.id">
                            <button @click="activeTab = tab.id"
                                :class="{
                                    'bg-blue-600 text-white shadow-lg transform scale-105': activeTab === tab.id,
                                    'text-gray-600 hover:text-gray-800 hover:bg-gray-50': activeTab !== tab.id
                                }"
                                class="relative py-4 px-6 font-medium text-sm rounded-xl border border-transparent flex items-center transition-all duration-300 ease-out flex-1 justify-center">
                                <i :class="tab.icon" class="mr-3 text-lg"></i>
                                <span x-text="tab.label"></span>
                                <span x-show="tab.count !== null"
                                    :class="{
                                        'bg-white/20 text-white': activeTab === tab
                                            .id,
                                        'bg-blue-100 text-blue-800': activeTab !== tab.id
                                    }"
                                    class="ml-2 px-2 py-1 text-xs rounded-full font-medium transition-colors duration-300"
                                    x-text="tab.count">
                                </span>
                                <!-- Active indicator -->
                                <div x-show="activeTab === tab.id"
                                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-white rounded-full"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-0"
                                    x-transition:enter-end="opacity-100 scale-100">
                                </div>
                            </button>
                        </template>
                    </nav>
                </div>
            </div>

            <!-- FAQs Tab -->
            <div x-show="activeTab === 'faqs'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                <!-- Search Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Knowledge Base</h2>
                            <p class="text-gray-600 mt-1">Find answers to common questions</p>
                        </div>
                        <div class="relative w-full md:w-80">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input wire:model.live.debounce.300ms="searchFaq" type="text"
                                placeholder="Search articles..."
                                class="pl-12 w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <div wire:loading.delay wire:target="searchFaq"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <i class="fas fa-circle-notch fa-spin text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ List -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @forelse($faqs as $index => $faq)
                            <div class="transition-all duration-200 hover:bg-gray-50">
                                <div class="flex justify-between items-center p-6 cursor-pointer"
                                    @click="openFaq = openFaq === {{ $faq->id }} ? null : {{ $faq->id }}">
                                    <div class="flex items-start flex-1">
                                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-4 mt-1 flex-shrink-0">
                                            <i class="fas fa-question text-sm"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 pr-4">{{ $faq->question }}
                                            </h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ Str::limit(strip_tags($faq->answer), 120) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <i class="fas transition-transform duration-300 text-blue-600"
                                            :class="{
                                                'fa-chevron-up rotate-180': openFaq ===
                                                    {{ $faq->id }},
                                                'fa-chevron-down': openFaq !== {{ $faq->id }}
                                            }"></i>
                                    </div>
                                </div>
                                <div x-show="openFaq === {{ $faq->id }}"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 max-h-0"
                                    x-transition:enter-end="opacity-100 max-h-screen"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 max-h-screen"
                                    x-transition:leave-end="opacity-0 max-h-0" class="px-6 pb-6 overflow-hidden">
                                    <div class="bg-blue-50 rounded-xl p-4 border-l-4 border-blue-400">
                                        <div class="prose prose-sm max-w-none text-gray-700">
                                            {!! nl2br(e($faq->answer)) !!}
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            Last updated {{ $faq->updated_at->diffForHumans() }}
                                        </div>
                                        <div class="flex space-x-2">
                                            <button
                                                class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                                                <i class="fas fa-thumbs-up mr-1"></i> Helpful
                                            </button>
                                            <button
                                                class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                                <i class="fas fa-share mr-1"></i> Share
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <div
                                    class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                                    <i class="fas fa-search fa-2x text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">No articles found</h3>
                                <p class="text-gray-500 mb-6">Try adjusting your search terms or browse all articles.
                                </p>
                                <button wire:click="$set('searchFaq', '')"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Clear Search
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Submit Ticket Tab -->
            <div x-show="activeTab === 'submit_ticket'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Submit New Ticket</h2>
                    <p class="text-gray-600 mt-1">Describe your issue and we'll get back to you soon</p>
                </div>

                <form wire:submit.prevent="submitTicket" class="p-6">
                    <div class="space-y-6">
                        <!-- Subject Field -->
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2 text-blue-600"></i>Subject *
                            </label>
                            <input wire:model="subject" type="text" id="subject"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('subject') border-red-500 @enderror"
                                placeholder="Brief description of your issue">
                            @error('subject')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Description Field -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2 text-blue-600"></i>Description *
                            </label>
                            <textarea wire:model="description" id="description" rows="6"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('description') border-red-500 @enderror"
                                placeholder="Please provide detailed information about your issue, including steps to reproduce if applicable"></textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-paperclip mr-2 text-blue-600"></i>Attachment (optional)
                            </label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-400 transition-colors duration-200 @error('attachment') border-red-500 @enderror">
                                <div class="space-y-2 text-center">
                                    <div
                                        class="bg-blue-50 rounded-full p-3 w-16 h-16 mx-auto flex items-center justify-center mb-4">
                                        <i class="fas fa-cloud-upload-alt text-blue-500 text-2xl"></i>
                                    </div>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="attachment"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 px-2 py-1">
                                            <span>Upload a file</span>
                                            <input wire:model="attachment" type="file" id="attachment"
                                                class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF up to 2MB</p>
                                    @if ($attachment)
                                        <div class="mt-2 text-sm text-green-600 bg-green-50 rounded-lg p-2">
                                            <i
                                                class="fas fa-check-circle mr-1"></i>{{ $attachment->getClientOriginalName() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('attachment')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Priority Selector (Optional Enhancement) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-exclamation-triangle mr-2 text-blue-600"></i>Priority
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative">
                                    <input type="radio" name="priority" value="low" class="sr-only peer">
                                    <div
                                        class="p-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition-all duration-200">
                                        <div class="text-center">
                                            <i class="fas fa-arrow-down text-green-500 text-lg mb-1"></i>
                                            <p class="text-sm font-medium text-gray-700">Low</p>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="priority" value="medium" class="sr-only peer"
                                        checked>
                                    <div
                                        class="p-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:border-yellow-300 transition-all duration-200">
                                        <div class="text-center">
                                            <i class="fas fa-minus text-yellow-500 text-lg mb-1"></i>
                                            <p class="text-sm font-medium text-gray-700">Medium</p>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="priority" value="high" class="sr-only peer">
                                    <div
                                        class="p-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition-all duration-200">
                                        <div class="text-center">
                                            <i class="fas fa-arrow-up text-red-500 text-lg mb-1"></i>
                                            <p class="text-sm font-medium text-gray-700">High</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex space-x-4">
                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 inline-flex justify-center items-center px-6 py-4 border border-transparent text-base font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                            <span wire:loading.remove>
                                <i class="fas fa-paper-plane mr-3"></i> Submit Ticket
                            </span>
                            <span wire:loading>
                                <i class="fas fa-circle-notch fa-spin mr-3"></i> Processing...
                            </span>
                        </button>
                        <button type="button" @click="activeTab = 'faqs'"
                            class="px-6 py-4 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <i class="fas fa-book-open mr-2"></i> Check FAQs First
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ticket History Tab -->
            <div x-show="activeTab === 'ticket_history'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                <!-- Search and Filter Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">My Support Tickets</h2>
                            <p class="text-gray-600 mt-1">Track and manage your support requests</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input wire:model.live.debounce.300ms="ticketSearch" type="text"
                                    placeholder="Search tickets..."
                                    class="pl-12 w-full sm:w-80 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>
                            <select wire:model.live="ticketStatusFilter"
                                class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="all">All Statuses</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tickets List -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <div class="transition-all duration-200 hover:bg-gray-50">
                                <div class="flex justify-between items-start p-6 cursor-pointer"
                                    @click="openTicket = openTicket === {{ $ticket->id }} ? null : {{ $ticket->id }}">
                                    <div class="flex-1">
                                        <div class="flex items-center flex-wrap gap-3 mb-3">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                @if ($ticket->status === 'open') bg-yellow-100 text-yellow-800
                                                @elseif($ticket->status === 'in_progress') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800 @endif">
                                                <i
                                                    class="fas 
                                                    @if ($ticket->status === 'open') fa-clock
                                                    @elseif($ticket->status === 'in_progress') fa-cog fa-spin
                                                    @else fa-check @endif mr-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            <span class="text-sm text-gray-500 flex items-center">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ $ticket->created_at->format('M d, Y \a\t g:i A') }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                #{{ $ticket->id }}
                                            </span>
                                        </div>

                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $ticket->subject }}
                                        </h3>
                                        <p class="text-gray-600 mb-3">{{ Str::limit($ticket->description, 150) }}</p>

                                        <div class="flex items-center space-x-4">
                                            @if ($ticket->attachment)
                                                <a href="{{ Storage::url($ticket->attachment) }}" target="_blank"
                                                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 transition-colors"
                                                    onclick="event.stopPropagation();">
                                                    <i class="fas fa-paperclip mr-1"></i> View Attachment
                                                </a>
                                            @endif
                                            @if ($ticket->response)
                                                <span class="inline-flex items-center text-sm text-green-600">
                                                    <i class="fas fa-reply mr-1"></i> Response Available
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4 flex flex-col items-center">
                                        <i class="fas transition-transform duration-300 text-blue-600 text-lg mb-2"
                                            :class="{
                                                'fa-chevron-up': openTicket ===
                                                    {{ $ticket->id }},
                                                'fa-chevron-down': openTicket !==
                                                    {{ $ticket->id }}
                                            }"></i>
                                        @if ($ticket->status !== 'resolved')
                                            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Expanded Ticket Details -->
                                <div x-show="openTicket === {{ $ticket->id }}"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 max-h-0"
                                    x-transition:enter-end="opacity-100 max-h-screen"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 max-h-screen"
                                    x-transition:leave-end="opacity-0 max-h-0"
                                    class="overflow-hidden border-t border-gray-200 bg-gray-50">
                                    <div class="p-6">
                                        <!-- Original Message -->
                                        <div class="bg-white rounded-xl p-4 mb-4 border-l-4 border-blue-400">
                                            <div class="flex items-start">
                                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 mb-2">Your Message</h4>
                                                    <p class="text-gray-700 whitespace-pre-wrap">
                                                        {{ $ticket->description }}</p>
                                                    <p class="text-xs text-gray-500 mt-2">
                                                        {{ $ticket->created_at->format('F j, Y \a\t g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Support Response -->
                                        @if ($ticket->response)
                                            <div
                                                class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border-l-4 border-green-400">
                                                <div class="flex items-start">
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-green-800 mb-2">Support Team
                                                            Response
                                                        </h4>
                                                        <p class="text-green-700 whitespace-pre-wrap">
                                                            {{ $ticket->response }}</p>
                                                        <div class="flex items-center justify-between mt-3">
                                                            @if ($ticket->responder)
                                                                <p class="text-xs text-green-600">
                                                                    <i class="fas fa-user-circle mr-1"></i>
                                                                    Responded by {{ $ticket->responder->name }}
                                                                </p>
                                                            @endif
                                                            <p class="text-xs text-green-600">
                                                                <i class="fas fa-clock mr-1"></i>
                                                                {{ $ticket->updated_at->format('F j, Y \a\t g:i A') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div
                                                class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl p-4 border-l-4 border-orange-400">
                                                <div class="flex items-center">
                                                    <div class="bg-orange-100 rounded-full p-2 mr-3">
                                                        <i class="fas fa-clock text-orange-600"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-orange-800 mb-1">Awaiting
                                                            Response
                                                        </h4>
                                                        <p class="text-sm text-orange-700">Our support team will
                                                            respond to
                                                            your ticket within 2-4 hours during business hours.</p>
                                                        <div class="flex items-center mt-2">
                                                            <div class="flex space-x-1">
                                                                <div
                                                                    class="w-2 h-2 bg-orange-400 rounded-full animate-bounce">
                                                                </div>
                                                                <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce"
                                                                    style="animation-delay: 0.1s"></div>
                                                                <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce"
                                                                    style="animation-delay: 0.2s"></div>
                                                            </div>
                                                            <span
                                                                class="ml-2 text-xs text-orange-600">Processing...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Action Buttons -->
                                        <div class="flex flex-wrap gap-3 mt-4">
                                            @if ($ticket->status === 'resolved')
                                                <button
                                                    class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    Ticket Resolved
                                                </button>
                                            @else
                                                <button
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                                    <i class="fas fa-comment mr-2"></i>
                                                    Add Comment
                                                </button>
                                            @endif

                                            <button
                                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                                <i class="fas fa-share mr-2"></i>
                                                Share Ticket
                                            </button>

                                            @if ($ticket->status !== 'resolved')
                                                <button
                                                    class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                                    <i class="fas fa-times mr-2"></i>
                                                    Close Ticket
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Ticket Timeline (Optional Enhancement) -->
                                        <div class="mt-6 border-t border-gray-200 pt-4">
                                            <h5 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                                <i class="fas fa-history mr-2"></i>
                                                Ticket Timeline
                                            </h5>
                                            <div class="space-y-3">
                                                <div class="flex items-center text-sm">
                                                    <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
                                                    <span class="text-gray-600">Ticket created</span>
                                                    <span
                                                        class="ml-auto text-gray-500">{{ $ticket->created_at->format('M j, g:i A') }}</span>
                                                </div>
                                                @if ($ticket->response)
                                                    <div class="flex items-center text-sm">
                                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                                        <span class="text-gray-600">Support team responded</span>
                                                        <span
                                                            class="ml-auto text-gray-500">{{ $ticket->updated_at->format('M j, g:i A') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <div
                                    class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                                    <i class="fas fa-ticket-alt fa-2x text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">No tickets found</h3>
                                <p class="text-gray-500 mb-6">You haven't submitted any support tickets yet.</p>
                                <button @click="activeTab = 'submit_ticket'"
                                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Submit Your First Ticket
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Enhanced Pagination -->
                @if ($tickets->hasPages())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                <span class="font-medium">{{ $tickets->firstItem() ?? 0 }}</span>
                                -
                                <span class="font-medium">{{ $tickets->lastItem() ?? 0 }}</span>
                                of
                                <span class="font-medium">{{ $tickets->total() }}</span>
                                tickets
                            </div>
                            <div class="flex items-center space-x-2">
                                {{ $tickets->links('custom.pagination') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div> <!-- End of main x-data container -->

        <!-- Loading State Overlay -->
        <div wire:loading.delay wire:target="submitTicket,pollTickets"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 shadow-2xl max-w-sm w-full mx-4">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-circle-notch fa-spin fa-2x text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Processing...</h3>
                    <p class="text-gray-600">Please wait while we process your request.</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Notification Toast -->
        <div x-data="{ show: false, message: '', type: 'success' }"
            @notify.window="show = true; message = $event.detail[0]; type = $event.detail[1]; setTimeout(() => show = false, 5000)"
            x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full" class="fixed top-4 right-4 z-50 max-w-sm">
            <div :class="{
                'bg-green-500': type === 'success',
                'bg-red-500': type === 'error',
                'bg-blue-500': type === 'info',
                'bg-yellow-500': type === 'warning'
            }"
                class="rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i :class="{
                            'fas fa-check-circle': type === 'success',
                            'fas fa-exclamation-circle': type === 'error',
                            'fas fa-info-circle': type === 'info',
                            'fas fa-exclamation-triangle': type === 'warning'
                        }"
                            class="text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="font-medium" x-text="message"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button @click="show = false" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Enhanced Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-resize textareas
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });

            // Enhanced file upload handling
            document.getElementById('attachment')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Show file preview for images
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // You can add image preview logic here
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });

            // Smooth scroll to top when switching tabs
            window.addEventListener('tab-changed', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Auto-refresh tickets every 30 seconds if on ticket history tab
            setInterval(() => {
                if (document.querySelector('[x-show="activeTab === \'ticket_history\'"]')?.style.display !==
                    'none') {
                    window.Livewire.find('help-support')?.call('pollTickets');
                }
            }, 30000);
        });

        // Enhanced Alpine.js directives
        document.addEventListener('alpine:init', () => {
            Alpine.directive('tooltip', (el, {
                expression
            }, {
                evaluate
            }) => {
                const tooltip = evaluate(expression);
                el.setAttribute('title', tooltip);

                // You can enhance this with a custom tooltip library
            });

            Alpine.magic('clipboard', () => {
                return {
                    copy(text) {
                        navigator.clipboard.writeText(text).then(() => {
                            // Show success message
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: ['Copied to clipboard!', 'success']
                            }));
                        });
                    }
                };
            });
        });

        // Service Worker for offline functionality (optional)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {
                // Service worker registration failed
            });
        }
    </script>

    <!-- Additional CSS for enhanced animations -->
    <style>
        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInScale {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-slide-in-up {
            animation: slideInUp 0.3s ease-out;
        }

        .animate-fade-in-scale {
            animation: fadeInScale 0.2s ease-out;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Enhanced focus styles */
        .enhanced-focus:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }

        /* Glassmorphism effect */
        .glass-effect {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Improved hover effects */
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Loading skeleton animation */
        @keyframes skeleton-loading {
            0% {
                background-position: -200px 0;
            }

            100% {
                background-position: calc(200px + 100%) 0;
            }
        }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200px 100%;
            animation: skeleton-loading 1.5s infinite;
        }


        /* Mobile optimizations */
        @media (max-width: 640px) {
            .mobile-padding {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .mobile-text {
                font-size: 0.875rem;
            }
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            .print-friendly {
                background: white !important;
                color: black !important;
                box-shadow: none !important;
            }
        }
    </style>
</div>
