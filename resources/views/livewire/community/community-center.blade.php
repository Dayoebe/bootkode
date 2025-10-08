<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-600 dark:bg-blue-500 p-3 rounded-lg animate__animated animate__fadeInLeft">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="animate__animated animate__fadeInUp">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Community Center</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Connect, collaborate, and learn with others</p>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="hidden md:flex items-center space-x-6 animate__animated animate__fadeInRight">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\ForumThread::count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Discussions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ \App\Models\CommunityActivity::studyGroups()->active()->count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Study Groups</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ \App\Models\CommunityActivity::codeChallenges()->active()->count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Challenges</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                @foreach([
                        'forums' => ['label' => 'Discussion Forums', 'icon' => 'fas fa-comments', 'roles' => []],
                        'study-groups' => ['label' => 'Study Groups', 'icon' => 'fas fa-user-friends', 'roles' => []],
                        'code-challenges' => ['label' => 'Code Challenges', 'icon' => 'fas fa-trophy', 'roles' => []],
                        'live-events' => ['label' => 'Live Events', 'icon' => 'fas fa-video', 'roles' => []],
                        'moderation' => [
                            'label' => 'Community Moderation',
                            'icon' => 'fas fa-shield-alt',
                            'roles' => [App\Models\User::ROLE_ACADEMY_ADMIN, App\Models\User::ROLE_SUPER_ADMIN]
                        ],
                        'feedback' => [
                            'label' => 'Feedback System',
                            'icon' => 'fas fa-comments',
                            'roles' => [App\Models\User::ROLE_STUDENT, App\Models\User::ROLE_INSTRUCTOR]
                        ],
                    ] as $tab => $tabData)
                        @if(empty($tabData['roles']) || in_array($user->role, $tabData['roles']))
                            <button
                                wire:click="setActiveTab('{{ $tab }}')"
                                class="{{ $activeTab === $tab 
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' 
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' 
                                }} whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center rounded-t-lg transition-all duration-200"
                            >
                                <i class="{{ $tabData['icon'] }} mr-2"></i>
                                {{ $tabData['label'] }}
                                
                                @if($activeTab === $tab)
                                    <span class="ml-2 bg-blue-500 dark:bg-blue-600 text-white text-xs px-2 py-1 rounded-full animate__animated animate__fadeInUp animate__faster">
                                        Active
                                    </span>
                                @endif
                            </button>
                        @endif
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Content Area with Loading Animation -->
    <div class="px-4 sm:px-6 lg:px-8 py-6" wire:loading.class="opacity-50 pointer-events-none">
        <div class="animate__animated animate__fadeIn">
            @if($activeTab === 'forums')
                @livewire('community.partial.forums')
            @elseif($activeTab === 'study-groups')
                @livewire('community.partial.study-groups')
            @elseif($activeTab === 'code-challenges')
                @livewire('community.partial.code-challenges')
            @elseif($activeTab === 'live-events')
                @livewire('community.partial.live-events')
            @elseif($activeTab === 'moderation')
                @livewire('community.partial.moderation')
            @elseif($activeTab === 'feedback')
                @livewire('community.partial.feedback')
            @endif
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl animate__animated animate__fadeIn">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 dark:border-blue-400"></div>
                <span class="text-gray-700 dark:text-gray-300">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
             x-transition:enter="animate__animated animate__slideInRight"
             x-transition:leave="animate__animated animate__slideOutRight"
             class="fixed top-4 right-4 bg-green-500 dark:bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
             x-transition:enter="animate__animated animate__slideInRight"
             x-transition:leave="animate__animated animate__slideOutRight"
             class="fixed top-4 right-4 bg-red-500 dark:bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif
</div>