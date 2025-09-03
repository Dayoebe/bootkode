<div class="w-full min-h-screen bg-gray-50 overflow-x-hidden">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center py-4 md:py-6 space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="bg-indigo-600 p-3 rounded-lg animate__animated animate__fadeInLeft">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                        <div class="animate__animated animate__fadeInUp">
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Content & Documentation Management</h1>
                            <p class="text-gray-600 mt-1 text-sm md:text-base">Create, manage, and organize educational content</p>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="flex items-center space-x-4 md:space-x-6 animate__animated animate__fadeInRight overflow-x-auto pb-2 md:pb-0 w-full md:w-auto">
                        <div class="text-center min-w-[70px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-indigo-600">{{ $stats['total_documents'] }}</div>
                            <div class="text-xs text-gray-500">Documents</div>
                        </div>
                        <div class="text-center min-w-[70px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-green-600">{{ $stats['total_videos'] }}</div>
                            <div class="text-xs text-gray-500">Videos</div>
                        </div>
                        <div class="text-center min-w-[70px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-blue-600">{{ $stats['total_materials'] }}</div>
                            <div class="text-xs text-gray-500">Materials</div>
                        </div>
                        <div class="text-center min-w-[70px] md:min-w-0">
                            <div class="text-xl md:text-2xl font-bold text-orange-600">{{ $stats['pending_reviews'] }}</div>
                            <div class="text-xs text-gray-500">Pending</div>
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
                            'learning-materials' => ['label' => 'Materials', 'icon' => 'fas fa-book', 'full_label' => 'Learning Materials'],
                            'video-library' => ['label' => 'Videos', 'icon' => 'fas fa-video', 'full_label' => 'Video Library'],
                            'documentation' => ['label' => 'Docs', 'icon' => 'fas fa-file-alt', 'full_label' => 'Documentation'],
                            'localization' => ['label' => 'Localize', 'icon' => 'fas fa-language', 'full_label' => 'Localization'],
                            'moderation' => ['label' => 'Moderate', 'icon' => 'fas fa-shield-alt', 'full_label' => 'Content Moderation'],
                            'all-documents' => ['label' => 'All Docs', 'icon' => 'fas fa-file-alt', 'full_label' => 'All Documents'],
                            'create-document' => ['label' => 'Create', 'icon' => 'fas fa-plus-circle', 'full_label' => 'Create Document'],
                            'categories' => ['label' => 'Categories', 'icon' => 'fas fa-tags', 'full_label' => 'Document Categories'],
                            'reviews' => ['label' => 'Reviews', 'icon' => 'fas fa-star', 'full_label' => 'Document Reviews'],
                            'settings' => ['label' => 'Settings', 'icon' => 'fas fa-cog', 'full_label' => 'Document Settings'],
                        ] as $tab => $tabData)
                        <button
                            wire:click="setActiveTab('{{ $tab }}')"
                            class="{{ $activeTab === $tab 
                                ? 'border-indigo-500 text-indigo-600 bg-indigo-50' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' 
                            }} whitespace-nowrap py-2 md:py-3 px-3 md:px-4 border-b-2 font-medium text-xs md:text-sm flex items-center rounded-t-lg transition-all duration-200"
                            title="{{ $tabData['full_label'] }}"
                        >
                            <i class="{{ $tabData['icon'] }} mr-1 md:mr-2"></i>
                            <span class="hidden sm:inline">{{ $tabData['label'] }}</span>
                            
                            @if($activeTab === $tab)
                                <span class="ml-1 md:ml-2 bg-indigo-500 text-white text-xs px-1 md:px-2 py-0.5 md:py-1 rounded-full animate__animated animate__fadeInUp animate__faster hidden md:inline">
                                    Active
                                </span>
                            @endif

                            <!-- Add notification badges for specific tabs -->
                            @if($tab === 'reviews' && $stats['pending_reviews'] > 0)
                                <span class="ml-1 md:ml-2 bg-red-500 text-white text-xs px-1 md:px-2 py-0.5 md:py-1 rounded-full">
                                    {{ $stats['pending_reviews'] }}
                                </span>
                            @endif
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Content Area with Loading Animation -->
        <div class="px-4 sm:px-6 lg:px-8 py-4 overflow-x-auto" wire:loading.class="opacity-50 pointer-events-none">
            <div class="animate__animated animate__fadeIn w-full min-w-0">
                @if($activeTab === 'learning-materials')
                    @livewire('content.partial.learning-materials')
                @elseif($activeTab === 'video-library')
                    @livewire('content.partial.video-library')
                @elseif($activeTab === 'documentation')
                    @livewire('content.partial.documentation')
                @elseif($activeTab === 'localization')
                    @livewire('content.partial.localization')
                @elseif($activeTab === 'moderation')
                    @livewire('content.partial.content-moderation')
                @elseif($activeTab === 'all-documents')
                    @livewire('content.partial.all-documents')
                @elseif($activeTab === 'create-document')
                    @livewire('content.partial.create-document')
                @elseif($activeTab === 'categories')
                    @livewire('content.partial.document-categories')
                @elseif($activeTab === 'reviews')
                    @livewire('content.partial.document-reviews')
                @elseif($activeTab === 'settings')
                    @livewire('content.partial.document-settings')
                @endif
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 shadow-xl animate__animated animate__fadeIn">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
                    <span class="text-gray-700">Loading content...</span>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-sm">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('warning'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
                 x-transition:enter="animate__animated animate__slideInRight"
                 x-transition:leave="animate__animated animate__slideOutRight"
                 class="fixed top-4 right-4 bg-yellow-500 text-white px-4 py-2 md:px-6 md:py-3 rounded-lg shadow-lg z-50 max-w-xs md:max-w-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="text-sm">{{ session('warning') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>