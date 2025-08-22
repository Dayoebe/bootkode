<div class="bg-gradient-to-r from-gray-900 to-gray-800 p-4 sm:p-6 rounded-xl shadow-lg mb-6 animate__animated animate__fadeInDown" 
    role="region" aria-label="Course Management Toolbar">

    <!-- Global Notifications -->
    <div id="global-notifications" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Course Title and Status -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="bg-purple-700 p-2 rounded-lg">
                    <i class="fas fa-book-open text-white"></i>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-white truncate max-w-[180px] sm:max-w-[280px]">
                    {{ $course->title ?? 'Untitled Course' }}
                </h2>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full transition-all duration-300 flex items-center
                    {{ $course->is_published ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}"
                    aria-live="polite"
                    wire:key="status-{{ $course->id }}-{{ $course->is_published ? 'published' : 'draft' }}">
                    <span class="w-2 h-2 rounded-full mr-2 {{ $course->is_published ? 'bg-green-400' : 'bg-red-400' }}"></span>
                    {{ $course->is_published ? 'Published' : 'Draft' }}
                </span>
                
                <!-- Pricing Badge -->
                <span class="px-3 py-1.5 text-xs font-semibold rounded-full flex items-center border
                    {{ $course->is_free ? 'bg-green-500/20 text-green-400 border-green-500/30' : 
                       ($course->is_premium ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' : 
                       'bg-blue-500/20 text-blue-400 border-blue-500/30') }}">
                    <i class="fas {{ $course->is_free ? 'fa-gift' : ($course->is_premium ? 'fa-crown' : 'fa-dollar-sign') }} mr-2"></i>
                    {{ $course->formatted_price }}
                </span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
            <!-- Pricing Button -->
            <button wire:click="openPricingModal"
                class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 
                transition-all duration-300 flex items-center shadow-lg hover:shadow-purple-500/20">
                <i class="fas fa-tag mr-2"></i>
                Pricing
            </button>

            <!-- Publish/Unpublish Button -->
            <button wire:click="togglePublished"
                class="px-4 py-2.5 bg-gradient-to-r {{ $course->is_published ? 'from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700' : 'from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800' }} 
                text-white rounded-lg transition-all duration-300 flex items-center shadow-lg {{ $course->is_published ? 'hover:shadow-blue-500/20' : 'hover:shadow-gray-500/20' }}
                disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="{{ $course->is_published ? 'Unpublish course' : 'Publish course' }}"
                wire:loading.attr="disabled" wire:target="togglePublished"
                wire:key="toggle-btn-{{ $course->id }}-{{ $course->is_published ? 1 : 0 }}">

                <span wire:loading.remove wire:target="togglePublished" class="flex items-center">
                    <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-2"></i>
                    {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                </span>

                <span wire:loading wire:target="togglePublished" class="flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Processing...
                </span>
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div class="bg-gray-800/50 p-4 rounded-xl flex items-center space-x-3 animate__animated animate__fadeIn border border-gray-700 hover:border-purple-500/30 transition-colors"
            wire:key="sections-count-{{ $sectionCount }}" wire:transition>
            <div class="bg-blue-500/20 p-3 rounded-lg">
                <i class="fas fa-folder-open text-blue-400 text-lg"></i>
            </div>
            <div>
                <span class="text-gray-400 text-xs uppercase tracking-wider">Sections</span>
                <span class="block text-xl font-bold text-white">{{ $sectionCount }}</span>
            </div>
        </div>
        
        <div class="bg-gray-800/50 p-4 rounded-xl flex items-center space-x-3 animate__animated animate__fadeIn border border-gray-700 hover:border-green-500/30 transition-colors"
            wire:key="lessons-count-{{ $lessonCount }}" wire:transition>
            <div class="bg-green-500/20 p-3 rounded-lg">
                <i class="fas fa-book text-green-400 text-lg"></i>
            </div>
            <div>
                <span class="text-gray-400 text-xs uppercase tracking-wider">Lessons</span>
                <span class="block text-xl font-bold text-white">{{ $lessonCount }}</span>
            </div>
        </div>
    </div>

    <!-- Pricing Modal -->
    @if($showPricingModal)
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate__animated animate__fadeIn">
        <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 border border-gray-700 shadow-2xl">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-700">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-tag text-purple-400 mr-2"></i>
                    Course Pricing
                </h3>
                <button wire:click="closePricingModal" class="text-gray-400 hover:text-white transition-colors p-1 rounded-full hover:bg-gray-700">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
    
            <div class="space-y-5">
                <!-- Free Course Toggle -->
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                    <div class="flex items-center">
                        <div class="bg-green-500/20 p-2 rounded-lg mr-3">
                            <i class="fas fa-gift text-green-400"></i>
                        </div>
                        <div>
                            <label for="modal_is_free" class="block text-sm font-medium text-gray-200">
                                Free Course
                            </label>
                            <p class="text-xs text-gray-400">Accessible to everyone</p>
                        </div>
                    </div>
                    <div class="relative inline-block w-12 h-6">
                        <input type="checkbox" wire:model.live="is_free" id="modal_is_free"
                            class="sr-only">
                        <div class="w-12 h-6 rounded-full transition-all duration-300 ease-in-out 
                            {{ $is_free ? 'bg-green-500' : 'bg-gray-600' }}"></div>
                        <div class="absolute left-1 top-1 w-4 h-4 rounded-full transition-transform duration-300 ease-in-out 
                            {{ $is_free ? 'transform translate-x-6 bg-white' : 'bg-gray-400' }}"></div>
                    </div>
                </div>
    
                <!-- Premium Toggle -->
                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg" x-show="!$wire.is_free">
                    <div class="flex items-center">
                        <div class="bg-yellow-500/20 p-2 rounded-lg mr-3">
                            <i class="fas fa-crown text-yellow-400"></i>
                        </div>
                        <div>
                            <label for="modal_is_premium" class="block text-sm font-medium text-gray-200">
                                Premium Course
                            </label>
                            <p class="text-xs text-gray-400">Special features</p>
                        </div>
                    </div>
                    <div class="relative inline-block w-12 h-6">
                        <input type="checkbox" wire:model="is_premium" id="modal_is_premium"
                            class="sr-only">
                        <div class="w-12 h-6 rounded-full transition-all duration-300 ease-in-out 
                            {{ $is_premium ? 'bg-yellow-500' : 'bg-gray-600' }}"></div>
                        <div class="absolute left-1 top-1 w-4 h-4 rounded-full transition-transform duration-300 ease-in-out 
                            {{ $is_premium ? 'transform translate-x-6 bg-white' : 'bg-gray-400' }}"></div>
                    </div>
                </div>
    
                <!-- Price Input -->
                <div x-show="!$wire.is_free" class="animate__animated animate__fadeIn">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Course Price ($)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">$</span>
                        </div>
                        <input type="number" wire:model="price" min="0" step="0.01"
                            class="w-full pl-8 pr-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    @error('price')
                        <span class="text-red-400 text-sm mt-1 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
    
                <!-- Action Buttons -->
                <div class="flex gap-3 pt-2">
                    <button wire:click="closePricingModal"
                        class="flex-1 px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="updatePricing" wire:loading.attr="disabled"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                        text-white rounded-lg transition-all duration-300 disabled:opacity-50 flex items-center justify-center">
                        <span wire:loading.remove wire:target="updatePricing" class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i> Update
                        </span>
                        <span wire:loading wire:target="updatePricing" class="flex items-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Updating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                        ${type === 'success' ? 'bg-gradient-to-r from-green-600 to-green-700' : 
                          type === 'error' ? 'bg-gradient-to-r from-red-600 to-red-700' : 
                          type === 'warning' ? 'bg-gradient-to-r from-yellow-600 to-yellow-700' : 
                          'bg-gradient-to-r from-blue-600 to-blue-700'}`;
                    notification.setAttribute('role', 'alert');
                    notification.setAttribute('aria-live', 'assertive');

                    notification.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-${
                                type === 'success' ? 'check-circle' : 
                                type === 'error' ? 'exclamation-triangle' : 
                                type === 'warning' ? 'exclamation-circle' : 
                                'info-circle'
                            } mr-3"></i>
                            <span class="flex-1">${message}</span>
                            <button onclick="this.parentElement.parentElement.remove()" 
                                    class="ml-4 text-white hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-white/10" 
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