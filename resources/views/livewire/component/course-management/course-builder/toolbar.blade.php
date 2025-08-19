<div class="bg-gray-900 p-4 sm:p-6 rounded-xl shadow-lg mb-6 animate__animated animate__fadeInDown" role="region"
    aria-label="Course Management Toolbar">
    <!-- Global Notifications -->
    <div id="global-notifications" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Course Title and Status -->
        <div class="flex items-center space-x-3">
            <h2 class="text-xl sm:text-2xl font-bold text-white truncate max-w-[200px] sm:max-w-[300px]">
                {{ $course->title ?? 'Untitled Course' }}
            </h2>
            <span
                class="px-2 py-1 text-xs font-semibold rounded-full 
                         {{ $course->is_published ? 'bg-green-600' : 'bg-red-600' }} text-white">
                {{ $course->is_published ? 'Published' : 'Draft' }}
            </span>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-3">
            <button wire:click="togglePublished"
                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                aria-label="{{ $course->is_published ? 'Unpublish course' : 'Publish course' }}"
                x-on:keydown.enter.prevent="$dispatch('togglePublished')">
                <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-2"></i>
                {{ $course->is_published ? 'Unpublish' : 'Publish' }}
            </button>
            <button wire:click="openSettings"
                class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center"
                aria-label="Open course settings" x-on:keydown.enter.prevent="$dispatch('openSettings')">
                <i class="fas fa-cog mr-2"></i>
                Settings
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
        <div class="bg-gray-800 p-3 rounded-lg flex items-center space-x-2">
            <i class="fas fa-folder-open text-blue-400"></i>
            <div>
                <span class="text-gray-300">Sections</span>
                <span class="block text-lg font-bold text-white">{{ $sectionCount }}</span>
            </div>
        </div>
        <div class="bg-gray-800 p-3 rounded-lg flex items-center space-x-2">
            <i class="fas fa-book text-green-400"></i>
            <div>
                <span class="text-gray-300">Lessons</span>
                <span class="block text-lg font-bold text-white">{{ $lessonCount }}</span>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', () => {
                Livewire.on('notify', ({
                    message,
                    type = 'info'
                }) => {
                    showNotification(message, type);
                });

                function showNotification(message, type) {
                    const notification = document.createElement('div');
                    notification.className = `px-6 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full animate__animated animate__fadeInRight
                        ${type === 'success' ? 'bg-green-600' : 
                          type === 'error' ? 'bg-red-600' : 
                          type === 'warning' ? 'bg-yellow-600' : 
                          'bg-blue-600'}`;
                    notification.setAttribute('role', 'alert');
                    notification.setAttribute('aria-live', 'assertive');

                    notification.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-${
                                type === 'success' ? 'check' : 
                                type === 'error' ? 'exclamation-triangle' : 
                                type === 'warning' ? 'exclamation-circle' : 
                                'info'
                            } mr-2"></i>
                            <span>${message}</span>
                            <button onclick="this.parentElement.parentElement.remove()" 
                                    class="ml-4 text-white hover:text-gray-200" 
                                    aria-label="Close notification">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;

                    const container = document.getElementById('global-notifications');
                    if (container) {
                        container.appendChild(notification);
                        setTimeout(() => {
                            notification.classList.remove('translate-x-full');
                        }, 10);
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.classList.add('translate-x-full', 'animate__fadeOutRight');
                                setTimeout(() => {
                                    if (notification.parentNode) {
                                        notification.remove();
                                    }
                                }, 300);
                            }
                        }, 5000);
                    }
                }
            });
        </script>
    @endpush
</div>
