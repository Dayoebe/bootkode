<div>
    <!-- Moderation Header -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="bg-red-100 dark:bg-red-900/20 p-3 rounded-lg border border-red-200 dark:border-red-800">
                <i class="fas fa-shield-alt text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Community Moderation</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Monitor and manage community content and activities</p>
            </div>
        </div>
    </div>

    <!-- Moderation Tabs -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8 px-6 overflow-x-auto">
                @foreach([
                    'overview' => ['label' => 'Overview', 'icon' => 'fas fa-chart-bar'],
                    'threads' => ['label' => 'Forum Threads', 'icon' => 'fas fa-comments'],
                    'replies' => ['label' => 'Forum Replies', 'icon' => 'fas fa-reply'],
                    'groups' => ['label' => 'Study Groups', 'icon' => 'fas fa-user-friends'],
                    'challenges' => ['label' => 'Code Challenges', 'icon' => 'fas fa-trophy'],
                    'events' => ['label' => 'Live Events', 'icon' => 'fas fa-video'],
                    'feedback' => ['label' => 'Feedback', 'icon' => 'fas fa-feedback']
                ] as $tab => $tabData)
                <button wire:click="setActiveTab('{{ $tab }}')" 
                        class="{{ $activeTab === $tab ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }} 
                               whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                    <i class="{{ $tabData['icon'] }} mr-2"></i>
                    {{ $tabData['label'] }}
                </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'overview')
                <!-- Overview Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg mr-4">
                                <i class="fas fa-comments text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_threads'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">Forum Threads</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">{{ $stats['total_replies'] }} replies</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg mr-4">
                                <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_groups'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">Study Groups</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">{{ $stats['total_events'] }} events</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="bg-orange-100 dark:bg-orange-900/30 p-3 rounded-lg mr-4">
                                <i class="fas fa-trophy text-orange-600 dark:text-orange-400"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_challenges'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">Code Challenges</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">{{ $stats['pending_feedback'] }} pending feedback</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Threads -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Forum Threads</h3>
                        <div class="space-y-3">
                            @foreach($recent_threads as $thread)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ Str::limit($thread->title, 40) }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">By {{ $thread->user->name }} • {{ $thread->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($thread->is_pinned)
                                    <i class="fas fa-thumbtack text-blue-500 dark:text-blue-400"></i>
                                    @endif
                                    @if($thread->is_locked)
                                    <i class="fas fa-lock text-red-500 dark:text-red-400"></i>
                                    @endif
                                    <button wire:click="moderateItem('thread', {{ $thread->id }}, 'view')" 
                                            class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recent Groups -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Study Groups</h3>
                        <div class="space-y-3">
                            @foreach($recent_groups as $group)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ Str::limit($group->name, 40) }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">By {{ $group->creator->name }} • {{ $group->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="bg-{{ $group->status === 'active' ? 'green' : 'yellow' }}-100 dark:bg-{{ $group->status === 'active' ? 'green' : 'yellow' }}-900/20 
                                               text-{{ $group->status === 'active' ? 'green' : 'yellow' }}-800 dark:text-{{ $group->status === 'active' ? 'green' : 'yellow' }}-300 
                                               text-xs px-2 py-1 rounded-full border border-{{ $group->status === 'active' ? 'green' : 'yellow' }}-200 dark:border-{{ $group->status === 'active' ? 'green' : 'yellow' }}-800">
                                        {{ ucfirst($group->status) }}
                                    </span>
                                    <button wire:click="moderateItem('study_group', {{ $group->id }}, 'view')" 
                                            class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Pending Feedback -->
                @if($pending_feedback->count() > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pending Feedback</h3>
                    <div class="space-y-3">
                        @foreach($pending_feedback as $feedback)
                        <div class="flex items-center justify-between p-4 border border-red-200 dark:border-red-800 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $feedback->subject }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($feedback->message, 100) }}</p>
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">From {{ $feedback->user->name }} • {{ $feedback->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-{{ $feedback->priority === 'high' ? 'red' : ($feedback->priority === 'medium' ? 'yellow' : 'gray') }}-100 dark:bg-{{ $feedback->priority === 'high' ? 'red' : ($feedback->priority === 'medium' ? 'yellow' : 'gray') }}-900/20 
                                           text-{{ $feedback->priority === 'high' ? 'red' : ($feedback->priority === 'medium' ? 'yellow' : 'gray') }}-800 dark:text-{{ $feedback->priority === 'high' ? 'red' : ($feedback->priority === 'medium' ? 'yellow' : 'gray') }}-300 
                                           text-xs px-2 py-1 rounded-full font-medium border border-{{ $feedback->priority === 'high' ? 'red' : ($feedback->priority === 'medium' ? 'yellow' : 'gray') }}-200 dark:border-{{ $feedback->priority === 'high' ? 'red' : ($feedback->priority === 'medium' ? 'yellow' : 'gray') }}-800">
                                    {{ ucfirst($feedback->priority) }}
                                </span>
                                <button wire:click="setTab('feedback')" 
                                        class="bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Review
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            @elseif($activeTab === 'threads')
                <!-- Forum Threads Moderation -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Forum Threads</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $threads->total() }} total threads</p>
                    </div>

                    @foreach($threads as $thread)
                    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $thread->title }}</h4>
                                    @if($thread->is_pinned)
                                    <span class="bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 text-xs px-2 py-1 rounded-full border border-blue-200 dark:border-blue-800">Pinned</span>
                                    @endif
                                    @if($thread->is_locked)
                                    <span class="bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300 text-xs px-2 py-1 rounded-full border border-red-200 dark:border-red-800">Locked</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($thread->content, 150) }}</p>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                    <span><i class="fas fa-user mr-1"></i>{{ $thread->user->name }}</span>
                                    <span><i class="fas fa-folder mr-1"></i>{{ $thread->category->name }}</span>
                                    <span><i class="fas fa-eye mr-1"></i>{{ $thread->views }} views</span>
                                    <span><i class="fas fa-comments mr-1"></i>{{ $thread->replies_count }} replies</span>
                                    <span><i class="fas fa-clock mr-1"></i>{{ $thread->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if(!$thread->is_pinned)
                                <button wire:click="moderateItem('thread', {{ $thread->id }}, 'pin')" 
                                        class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                    Pin
                                </button>
                                @else
                                <button wire:click="moderateItem('thread', {{ $thread->id }}, 'unpin')" 
                                        class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                    Unpin
                                </button>
                                @endif

                                @if(!$thread->is_locked)
                                <button wire:click="moderateItem('thread', {{ $thread->id }}, 'lock')" 
                                        class="bg-yellow-600 dark:bg-yellow-500 hover:bg-yellow-700 dark:hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                    Lock
                                </button>
                                @else
                                <button wire:click="moderateItem('thread', {{ $thread->id }}, 'unlock')" 
                                        class="bg-yellow-600 dark:bg-yellow-500 hover:bg-yellow-700 dark:hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                    Unlock
                                </button>
                                @endif

                                <button wire:click="moderateItem('thread', {{ $thread->id }}, 'delete')" 
                                        class="bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $threads->links() }}
                    </div>
                </div>

            @elseif($activeTab === 'replies')
                <!-- Forum Replies Moderation -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Forum Replies</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $replies->total() }} total replies</p>
                    </div>

                    @foreach($replies as $reply)
                    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Reply to: {{ $reply->thread->title }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($reply->content, 200) }}</p>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                    <span><i class="fas fa-user mr-1"></i>{{ $reply->user->name }}</span>
                                    <span><i class="fas fa-clock mr-1"></i>{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="moderateItem('reply', {{ $reply->id }}, 'delete')" 
                                        class="bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $replies->links() }}
                    </div>
                </div>

            @elseif($activeTab === 'groups')
                <!-- Study Groups Moderation -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Study Groups</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $groups->total() }} total groups</p>
                    </div>

                    @foreach($groups as $group)
                    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $group->name }}</h4>
                                    <span class="bg-{{ $group->status === 'active' ? 'green' : 'yellow' }}-100 dark:bg-{{ $group->status === 'active' ? 'green' : 'yellow' }}-900/20 
                                               text-{{ $group->status === 'active' ? 'green' : 'yellow' }}-800 dark:text-{{ $group->status === 'active' ? 'green' : 'yellow' }}-300 
                                               text-xs px-2 py-1 rounded-full border border-{{ $group->status === 'active' ? 'green' : 'yellow' }}-200 dark:border-{{ $group->status === 'active' ? 'green' : 'yellow' }}-800">
                                        {{ ucfirst($group->status) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($group->description, 150) }}</p>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                    <span><i class="fas fa-user mr-1"></i>{{ $group->creator->name }}</span>
                                    <span><i class="fas fa-users mr-1"></i>{{ $group->members_count }} members</span>
                                    @if($group->course)
                                    <span><i class="fas fa-book mr-1"></i>{{ $group->course->title }}</span>
                                    @endif
                                    <span><i class="fas fa-clock mr-1"></i>{{ $group->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($group->status === 'active')
                                <button wire:click="moderateItem('study_group', {{ $group->id }}, 'deactivate')" 
                                        class="bg-yellow-600 dark:bg-yellow-500 hover:bg-yellow-700 dark:hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                    Deactivate
                                </button>
                                @else
                                <button wire:click="moderateItem('study_group', {{ $group->id }}, 'activate')" 
                                        class="bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                    Activate
                                </button>
                                @endif
                                <button wire:click="moderateItem('study_group', {{ $group->id }}, 'delete')" 
                                        class="bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $groups->links() }}
                    </div>
                </div>

            @elseif($activeTab === 'challenges')
                <!-- Code Challenges Moderation -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Code Challenges</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $challenges->total() }} total challenges</p>
                    </div>

                    @foreach($challenges as $challenge)
                    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $challenge->title }}</h4>
                                    <span class="bg-{{ $challenge->difficulty === 'easy' ? 'green' : ($challenge->difficulty === 'medium' ? 'yellow' : 'red') }}-100 dark:bg-{{ $challenge->difficulty === 'easy' ? 'green' : ($challenge->difficulty === 'medium' ? 'yellow' : 'red') }}-900/20 
                                               text-{{ $challenge->difficulty === 'easy' ? 'green' : ($challenge->difficulty === 'medium' ? 'yellow' : 'red') }}-800 dark:text-{{ $challenge->difficulty === 'easy' ? 'green' : ($challenge->difficulty === 'medium' ? 'yellow' : 'red') }}-300 
                                               text-xs px-2 py-1 rounded-full border border-{{ $challenge->difficulty === 'easy' ? 'green' : ($challenge->difficulty === 'medium' ? 'yellow' : 'red') }}-200 dark:border-{{ $challenge->difficulty === 'easy' ? 'green' : ($challenge->difficulty === 'medium' ? 'yellow' : 'red') }}-800">
                                        {{ ucfirst($challenge->difficulty) }}
                                    </span>
                                    @if(!$challenge->is_active)
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs px-2 py-1 rounded-full border border-gray-200 dark:border-gray-600">Inactive</span>
                                    @endif
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($challenge->description, 150) }}</p>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                    <span><i class="fas fa-user mr-1"></i>{{ $challenge->creator->name }}</span>
                                    <span><i class="fas fa-trophy mr-1"></i>{{ $challenge->points }} points</span>
                                    <span><i class="fas fa-code mr-1"></i>{{ $challenge->submissions_count }} submissions</span>
                                    <span><i class="fas fa-clock mr-1"></i>{{ $challenge->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($challenge->is_active)
                                <button wire:click="moderateItem('challenge', {{ $challenge->id }}, 'deactivate')" 
                                        class="bg-yellow-600 dark:bg-yellow-500 hover:bg-yellow-700 dark:hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                    Deactivate
                                </button>
                                @else
                                <button wire:click="moderateItem('challenge', {{ $challenge->id }}, 'activate')" 
                                        class="bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                    Activate
                                </button>
                                @endif
                                <button wire:click="moderateItem('challenge', {{ $challenge->id }}, 'delete')" 
                                        class="bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $challenges->links() }}
                    </div>
                </div>

            @elseif($activeTab === 'events')
                <!-- Live Events Moderation -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Live Events</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $events->total() }} total events</p>
                    </div>

                    @foreach($events as $event)
                    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $event->title }}</h4>
                                    <span class="bg-{{ $event->status === 'live' ? 'red' : ($event->status === 'scheduled' ? 'green' : 'gray') }}-100 dark:bg-{{ $event->status === 'live' ? 'red' : ($event->status === 'scheduled' ? 'green' : 'gray') }}-900/20 
                                               text-{{ $event->status === 'live' ? 'red' : ($event->status === 'scheduled' ? 'green' : 'gray') }}-800 dark:text-{{ $event->status === 'live' ? 'red' : ($event->status === 'scheduled' ? 'green' : 'gray') }}-300 
                                               text-xs px-2 py-1 rounded-full border border-{{ $event->status === 'live' ? 'red' : ($event->status === 'scheduled' ? 'green' : 'gray') }}-200 dark:border-{{ $event->status === 'live' ? 'red' : ($event->status === 'scheduled' ? 'green' : 'gray') }}-800">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($event->description, 150) }}</p>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                    <span><i class="fas fa-user mr-1"></i>{{ $event->host->name }}</span>
                                    <span><i class="fas fa-tag mr-1"></i>{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</span>
                                    <span><i class="fas fa-calendar mr-1"></i>{{ $event->scheduled_at->format('M j, Y g:i A') }}</span>
                                    <span><i class="fas fa-users mr-1"></i>{{ $event->attendees_count }} attendees</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($event->status === 'scheduled')
                                <button wire:click="moderateItem('event', {{ $event->id }}, 'cancel')" 
                                        class="bg-yellow-600 dark:bg-yellow-500 hover:bg-yellow-700 dark:hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                    Cancel
                                </button>
                                @endif
                                <button wire:click="moderateItem('event', {{ $event->id }}, 'delete')" 
                                        class="bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $events->links() }}
                    </div>
                </div>

            @elseif($activeTab === 'feedback')
                <!-- Feedback Moderation -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Community Feedback</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $feedback->total() }} total feedback items</p>
                    </div>

                    @foreach($feedback as $item)
                    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $item->subject }}</h4>
                                    <span class="bg-{{ $item->status_color }}-100 dark:bg-{{ $item->status_color }}-900/20 text-{{ $item->status_color }}-800 dark:text-{{ $item->status_color }}-300 
                                               text-xs px-2 py-1 rounded-full border border-{{ $item->status_color }}-200 dark:border-{{ $item->status_color }}-800">
                                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                    </span>
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs px-2 py-1 rounded-full border border-gray-200 dark:border-gray-600">
                                        {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                                    </span>
                                    <span class="bg-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-100 dark:bg-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-900/20 
                                               text-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-800 dark:text-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-300 
                                               text-xs px-2 py-1 rounded-full border border-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-200 dark:border-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-800">
                                        {{ ucfirst($item->priority) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($item->message, 200) }}</p>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                    <span><i class="fas fa-user mr-1"></i>{{ $item->user->name }}</span>
                                    <span><i class="fas fa-clock mr-1"></i>{{ $item->created_at->diffForHumans() }}</span>
                                    @if($item->assignedTo)
                                    <span><i class="fas fa-user-check mr-1"></i>Assigned to {{ $item->assignedTo->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <select wire:change="updateFeedbackStatus({{ $item->id }}, $event.target.value)"
                                        class="text-sm px-3 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg">
                                    <option value="open" {{ $item->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $item->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $item->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $item->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>
                        
                        @if($item->admin_response)
                        <div class="mt-3 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 dark:border-green-600 p-3 rounded-r-lg">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-reply text-green-600 dark:text-green-400 mr-2"></i>
                                <span class="font-medium text-green-800 dark:text-green-300 text-sm">Admin Response</span>
                                <span class="text-green-600 dark:text-green-400 text-xs ml-2">{{ $item->responded_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-green-700 dark:text-green-200 text-sm">{{ Str::limit($item->admin_response, 100) }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $feedback->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Moderation Action Modal -->
    @if($showModerationModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 z-50 flex items-center justify-center animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 animate__animated animate__zoomIn">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Confirm Moderation Action</h3>
                    <button wire:click="$set('showModerationModal', false)" 
                            class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="mb-6">
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        Are you sure you want to <strong>{{ $moderationAction }}</strong> this 
                        <strong>{{ $selectedItem['type'] ?? '' }}</strong>?
                    </p>
                    
                    @if(in_array($moderationAction, ['delete', 'lock', 'deactivate']))
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason (optional)</label>
                        <textarea wire:model="moderationReason" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:border-transparent text-sm"
                                  placeholder="Provide a reason for this action..."></textarea>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-4">
                    <button wire:click="$set('showModerationModal', false)"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm">
                        Cancel
                    </button>
                    <button wire:click="executeModerationAction"
                            class="bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                        Confirm {{ ucfirst($moderationAction) }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>