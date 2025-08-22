<div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" wire:poll.5000ms="pollNotifications">
        <!-- Header -->
        <div
            class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        <i class="fas fa-bell mr-2"></i> Notifications
                    </h1>
                    <p class="text-gray-400 mt-2">Stay updated with your platform activity</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Search notifications..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900">
                    </div>
                    <button wire:click="markAllAsRead" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span wire:loading.remove><i class="fas fa-check-double mr-2"></i> Mark All Read</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="typeFilter" class="block text-sm font-medium text-gray-700">Filter by Type</label>
                <select wire:model.live="typeFilter" id="typeFilter"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach ($notificationTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All</option>
                    <option value="read">Read</option>
                    <option value="unread">Unread</option>
                </select>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            @if ($notifications->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach ($notifications as $notification)
                        <div
                            class="p-6 hover:bg-gray-50 transition-colors duration-200 animate__animated animate__fadeInUp">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <i
                                        class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-900 {{ $notification->read_at ? 'opacity-75' : '' }}">
                                        {{ $notification->data['message'] ?? 'No message' }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if (!$notification->read_at)
                                        <button wire:click="markAsRead('{{ $notification->id }}')"
                                            class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <a href="{{ $notification->data['action_url'] ?? '#' }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button wire:click="delete('{{ $notification->id }}')"
                                        class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="p-6 text-center">
                    <i class="fas fa-bell text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No notifications found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine.js Toast Container -->
    <div x-data="{ toasts: [] }" class="fixed top-4 right-4 space-y-2 z-50">
        <template x-for="(toast, index) in toasts" :key="index">
            <div class="bg-white shadow-lg rounded-lg p-4 max-w-sm animate__animated animate__fadeInRight"
                x-bind:class="{ 'border-l-4 border-green-500': toast.type === 'success', 'border-l-4 border-red-500': toast
                        .type === 'error' }"
                x-show="toast.show" x-transition:leave="animate__animated animate__fadeOutRight"
                x-bind:style="'animation-delay: ' + (index * 0.2) + 's;'" @click="toasts.splice(index, 1)">
                <p class="text-sm text-gray-700" x-text="toast.message"></p>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('notify', (message, type) => {
                Alpine.store('toasts', {
                    toasts: [],
                    push(toast) {
                        if (!this.toasts) this.toasts = [];
                        toast.show = true;
                        this.toasts.push(toast);
                        setTimeout(() => {
                            this.toasts.splice(this.toasts.indexOf(toast), 1);
                        }, 5000);
                    },
                    ...Alpine.store('toasts')
                }).push({
                    message,
                    type
                });
            });
        });
    </script>
</div>
