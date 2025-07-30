<div>
    @section('dashboard-title', 'Super Admin Dashboard')

    <!-- Main Content Area -->
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <!-- Dynamically load components based on the current route name -->
        @if (request()->routeIs('super_admin.dashboard'))
            <!-- Default dashboard content -->
            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold text-gray-800">Super Admin Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back! Select an option from the sidebar.</p>
            </div>
        @elseif(request()->routeIs('profile.view'))
            <livewire:profile-management />
        @elseif(request()->routeIs('course_management.all_courses'))
            <livewire:course-management.all-courses />
        @elseif(request()->routeIs('course_management.create_course'))
            <livewire:course-management.create-course />
        @elseif(request()->routeIs('course_management.course_categories'))
            <livewire:component.course-management.course-categories />
        @else
            <!-- Fallback content -->
            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold text-gray-800">Page Not Found</h1>
                <p class="text-gray-600 mt-2">The requested page could not be found.</p>
            </div>
        @endif
    </div>

   
    

    <!-- Success/Error Toast Notification -->
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
        }
    }" 
    @notify.window="showNotification($event)" 
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100" 
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50"
    style="display: none;">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <template x-if="type === 'success'">
                        <i class="fas fa-check-circle h-6 w-6 text-green-400"></i>
                    </template>
                    <template x-if="type === 'error'">
                        <i class="fas fa-times-circle h-6 w-6 text-red-400"></i>
                    </template>
                    <template x-if="type === 'warning'">
                        <i class="fas fa-exclamation-circle h-6 w-6 text-yellow-400"></i>
                    </template>
                    <template x-if="type === 'info'">
                        <i class="fas fa-info-circle h-6 w-6 text-blue-400"></i>
                    </template>
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
</div>