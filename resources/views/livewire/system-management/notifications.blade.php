<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8" wire:poll.5000ms="pollNotifications">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        <i class="fas fa-bell mr-2"></i> Notifications
                    </h1>
                    <p class="text-purple-100 mt-2">Stay updated with your platform activity</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Search notifications..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900">
                    </div>
                    <button wire:click="markAllAsRead" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <span wire:loading.remove><i class="fas fa-check-double mr-2"></i> Mark All Read</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="typeFilter" class="block text-sm font-medium text-themed-primary mb-2">Filter by Type</label>
                <select wire:model.live="typeFilter" id="typeFilter"
                    class="mt-1 block w-full border-themed-primary bg-themed-secondary rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-themed-primary">
                    <option value="">All Types</option>
                    @foreach ($notificationTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="statusFilter" class="block text-sm font-medium text-themed-primary mb-2">Filter by Status</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                    class="mt-1 block w-full border-themed-primary bg-themed-secondary rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-themed-primary">
                    <option value="all">All</option>
                    <option value="read">Read</option>
                    <option value="unread">Unread</option>
                </select>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-themed-secondary shadow overflow-hidden sm:rounded-xl border border-themed-primary">
            @if ($notifications->count() > 0)
                <div class="divide-y divide-themed-primary">
                    @foreach ($notifications as $notification)
                        <div
                            class="p-6 hover:bg-themed-tertiary transition-colors duration-200 animate__animated animate__fadeInUp {{ !$notification->read_at ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                                        <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} text-indigo-600 dark:text-indigo-400"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-themed-primary {{ $notification->read_at ? 'opacity-75' : 'font-semibold' }}">
                                        {{ $notification->data['message'] ?? 'No message' }}
                                    </p>
                                    <p class="text-xs text-themed-secondary">{{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if (!$notification->read_at)
                                        <button wire:click="markAsRead('{{ $notification->id }}')"
                                            class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors p-2 rounded-lg hover:bg-indigo-100/50 dark:hover:bg-indigo-900/20">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <a href="{{ $notification->data['action_url'] ?? '#' }}"
                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors p-2 rounded-lg hover:bg-indigo-100/50 dark:hover:bg-indigo-900/20">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button wire:click="delete('{{ $notification->id }}')"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors p-2 rounded-lg hover:bg-red-100/50 dark:hover:bg-red-900/20">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-themed-primary">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-4">
                        <i class="fas fa-bell text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                    </div>
                    <p class="text-themed-secondary text-lg">No notifications found.</p>
                    <p class="text-themed-secondary text-sm mt-1">You're all caught up!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine.js Toast Container -->
    <div x-data="{ toasts: [] }" class="fixed top-4 right-4 space-y-2 z-50">
        <template x-for="(toast, index) in toasts" :key="index">
            <div class="bg-themed-secondary shadow-lg rounded-xl p-4 max-w-sm animate__animated animate__fadeInRight border border-themed-primary"
                :class="{ 'border-l-4 border-l-green-500': toast.type === 'success', 'border-l-4 border-l-red-500': toast.type === 'error' }"
                x-show="toast.show" x-transition:leave="animate__animated animate__fadeOutRight"
                x-bind:style="'animation-delay: ' + (index * 0.2) + 's;'" @click="toasts.splice(index, 1)" class="cursor-pointer hover:shadow-xl transition-shadow">
                <div class="flex items-center">
                    <i :class="{ 'fas fa-check-circle text-green-500': toast.type === 'success', 'fas fa-exclamation-circle text-red-500': toast.type === 'error' }" class="mr-3"></i>
                    <p class="text-sm text-themed-primary" x-text="toast.message"></p>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('notify', (message, type) => {
                const toastContainer = document.querySelector('[x-data*="toasts"]');
                if (toastContainer && toastContainer.__x) {
                    const toast = {
                        message: message,
                        type: type,
                        show: true
                    };
                    toastContainer.__x.$data.toasts.push(toast);
                    setTimeout(() => {
                        const idx = toastContainer.__x.$data.toasts.indexOf(toast);
                        if (idx > -1) {
                            toastContainer.__x.$data.toasts.splice(idx, 1);
                        }
                    }, 5000);
                }
            });
        });
    </script>
</div>