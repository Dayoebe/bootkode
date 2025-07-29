<div>
    @section('dashboard-title', 'Super Admin Dashboard')

    <!-- The main dashboard content structure -->
    <div>
        <!-- Main Content Area -->
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            @if(request()->routeIs('profile.view') || request()->routeIs('profile.edit'))
                <livewire:component.profile-management />
            @else
                <!-- Your existing dashboard content -->
                <livewire:component.user-management />
            @endif
        </div>
    </div>

    <!-- Toast Notification Component (remains here as it's global for the dashboard) -->
    <div x-data="{
        show: false,
        message: '',
        type: 'success',
        timeout: null,
        showNotification(event) {
            this.message = event.detail[0] || event.detail.message || 'Action completed';
            this.type = event.detail[1] || event.detail.type || 'success';
            this.show = true;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.show = false;
            }, 4000);
            components
        }
    }" @notify.window="showNotification($event)" x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50"
        style="display: none;">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i x-show="type === 'success'" class="fas fa-check-circle h-6 w-6 text-green-400"></i>
                    <i x-show="type === 'error'" class="fas fa-times-circle h-6 w-6 text-red-400"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900" x-text="message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="show = false"
                        class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                // Listen for events from the UserManagement component to close the modal
                Livewire.on('userSaved', () => {
                    // You might want to delay the notification or modal close if needed
                    // For now, UserManagement handles its own modal closing via closeModalAndReset()
                    // If you want the parent to close it, UserManagement would dispatch an event
                    // like 'closeUserModal' and SuperAdminDashboard would listen to it.
                    // For now, the UserManagement component's closeModalAndReset is called directly
                    // by the userSaved event within UserManagement itself.
                    // This script block might become less necessary if modal closing is fully
                    // self-contained within the UserManagement component.
                    // However, if you want the parent to react to 'userSaved' (e.g., refresh other data)
                    // you can keep this listener.
                });

                Livewire.on('userSaveFailed', () => {
                    // Keep modal open on failure for user to retry
                });
            });
        </script>
    @endpush --}}
</div>
