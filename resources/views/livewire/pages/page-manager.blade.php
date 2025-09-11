<div class="w-full min-h-screen bg-gray-50 overflow-x-hidden">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center py-4 md:py-6 space-y-4 md:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="bg-indigo-600 p-3 rounded-lg animate__animated animate__fadeInLeft">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div class="animate__animated animate__fadeInUp">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Page Management</h1>
                        <p class="text-gray-600 mt-1 text-sm md:text-base">Create and manage SEO-friendly pages</p>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="flex items-center space-x-4 md:space-x-6 animate__animated animate__fadeInRight overflow-x-auto pb-2 md:pb-0 w-full md:w-auto">
                    <div class="text-center min-w-[70px] md:min-w-0">
                        <div class="text-xl md:text-2xl font-bold text-indigo-600">{{ $stats['total_pages'] }}</div>
                        <div class="text-xs text-gray-500">Total</div>
                    </div>
                    <div class="text-center min-w-[70px] md:min-w-0">
                        <div class="text-xl md:text-2xl font-bold text-green-600">{{ $stats['published_pages'] }}</div>
                        <div class="text-xs text-gray-500">Published</div>
                    </div>
                    <div class="text-center min-w-[70px] md:min-w-0">
                        <div class="text-xl md:text-2xl font-bold text-yellow-600">{{ $stats['draft_pages'] }}</div>
                        <div class="text-xs text-gray-500">Drafts</div>
                    </div>
                    <div class="text-center min-w-[70px] md:min-w-0">
                        <div class="text-xl md:text-2xl font-bold text-blue-600">{{ $stats['scheduled_pages'] }}</div>
                        <div class="text-xs text-gray-500">Scheduled</div>
                    </div>
                    <div class="text-center min-w-[70px] md:min-w-0">
                        <div class="text-xl md:text-2xl font-bold text-purple-600">{{ number_format($stats['total_views']) }}</div>
                        <div class="text-xs text-gray-500">Views</div>
                    </div>
                    <button 
                        wire:click="refreshStats"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100 transition-all duration-200"
                        title="Refresh Statistics"
                    >
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-2 md:space-x-4 overflow-x-auto pb-1">
                @foreach([
                    'all-pages' => ['label' => 'All Pages', 'icon' => 'fas fa-list'],
                    'create-page' => ['label' => 'Create Page', 'icon' => 'fas fa-plus-circle'],
                    'analytics' => ['label' => 'Analytics', 'icon' => 'fas fa-chart-line'],
                    'templates' => ['label' => 'Templates', 'icon' => 'fas fa-palette'],
                    'media' => ['label' => 'Media', 'icon' => 'fas fa-photo-video'],
                    'seo' => ['label' => 'SEO Manager', 'icon' => 'fas fa-search'],
                    'settings' => ['label' => 'Settings', 'icon' => 'fas fa-cog'],
                ] as $tab => $tabData)
                    <button
                        wire:click="setActiveTab('{{ $tab }}')"
                        class="{{ $activeTab === $tab 
                            ? 'border-indigo-500 text-indigo-600 bg-indigo-50' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' 
                        }} whitespace-nowrap py-2 md:py-3 px-3 md:px-4 border-b-2 font-medium text-xs md:text-sm flex items-center rounded-t-lg transition-all duration-200"
                    >
                        <i class="{{ $tabData['icon'] }} mr-1 md:mr-2"></i>
                        <span class="hidden sm:inline">{{ $tabData['label'] }}</span>
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        @switch($activeTab)
            @case('all-pages')
                <livewire:pages.partials.pages-list />
                @break
                
            @case('create-page')
                <livewire:pages.partials.create-page />
                @break
                
            @case('analytics')
                <livewire:pages.partials.analytics />
                @break
                
            @case('templates')
                <livewire:pages.partials.templates />
                @break
                
            @case('media')
                <livewire:pages.partials.media-manager />
                @break
                
            @case('seo')
                <livewire:pages.partials.seo-manager />
                @break
                
            @case('settings')
                <livewire:pages.partials.settings />
                @break
                
            @default
                <livewire:pages.partials.pages-list />
        @endswitch
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 z-40 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl animate__animated animate__fadeIn">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
                <span class="text-gray-700">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div x-data="{ notifications: [] }" 
         @notify.window="notifications.push({id: Date.now(), type: $event.detail.type, message: $event.detail.message}); setTimeout(() => { notifications.shift() }, 5000)"
         class="fixed top-4 right-4 z-50 space-y-2">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-show="true" 
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i x-show="notification.type === 'success'" class="fas fa-check-circle text-green-400"></i>
                            <i x-show="notification.type === 'error'" class="fas fa-exclamation-circle text-red-400"></i>
                            <i x-show="notification.type === 'warning'" class="fas fa-exclamation-triangle text-yellow-400"></i>
                            <i x-show="notification.type === 'info'" class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900" x-text="notification.message"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>