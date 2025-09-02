<div>
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 animate__animated animate__fadeInUp">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Study Groups</h2>
                <p class="text-gray-600">Join collaborative learning sessions with fellow students</p>
            </div>
            
            <button wire:click="$set('showCreateForm', true)" 
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>Create Study Group
            </button>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $studyGroups->total() ?? 0 }}</div>
                <div class="text-sm text-green-700">Total Groups</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $myGroups->count() }}</div>
                <div class="text-sm text-blue-700">My Groups</div>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ \App\Models\CommunityActivity::studyGroups()->active()->sum('participants_count') }}</div>
                <div class="text-sm text-orange-700">Active Members</div>
            </div>
            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                <div class="text-2xl font-bold text-indigo-600">{{ \App\Models\CommunityActivity::studyGroups()->where('start_date', '>', now())->count() }}</div>
                <div class="text-sm text-indigo-700">Upcoming</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 animate__animated animate__fadeInUp animate__delay-1s">
        <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Search study groups...">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <select wire:model.live="statusFilter" 
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="active">Active Groups</option>
                <option value="completed">Completed</option>
                <option value="all">All Status</option>
            </select>
        </div>
    </div>

    <!-- My Study Groups -->
    @if($myGroups->count() > 0)
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6 animate__animated animate__fadeInUp animate__delay-2s">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-star mr-2 text-yellow-500"></i>
                My Study Groups
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($myGroups as $group)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                        <h4 class="font-semibold text-gray-900 mb-2">{{ $group->title }}</h4>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($group->description, 60) }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $group->activeParticipants->count() }} members</span>
                            @if($group->start_date)
                                <span>{{ $group->start_date->format('M j') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- All Study Groups -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($studyGroups as $index => $group)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 animate__animated animate__fadeInUp" 
                 style="animation-delay: {{ ($index % 6) * 0.1 }}s">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $group->title }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $group->description }}</p>
                        </div>
                        
                        <div class="ml-4">
                            <span class="bg-{{ $group->statusColor }}-100 text-{{ $group->statusColor }}-800 px-2 py-1 rounded-full text-xs font-medium">
                                {{ ucfirst($group->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Tags -->
                    @if($group->tags && count($group->tags) > 0)
                        <div class="flex flex-wrap gap-1 mb-4">
                            @foreach(array_slice($group->tags, 0, 3) as $tag)
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ $tag }}</span>
                            @endforeach
                            @if(count($group->tags) > 3)
                                <span class="text-gray-500 text-xs">+{{ count($group->tags) - 3 }} more</span>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user mr-2 w-4"></i>
                            <span>Created by {{ $group->creator->name }}</span>
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-users mr-2 w-4"></i>
                            <span>
                                {{ $group->activeParticipants->count() }}
                                @if($group->max_participants) / {{ $group->max_participants }} @endif
                                participants
                            </span>
                        </div>
                        
                        @if($group->start_date)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2 w-4"></i>
                                <span>{{ $group->formatted_date }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Participants Preview -->
                    @if($group->activeParticipants->count() > 0)
                        <div class="flex items-center mb-4">
                            <div class="flex -space-x-2">
                                @foreach($group->activeParticipants->take(4) as $participant)
                                    <img src="{{ $participant->user->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode($participant->user->name) }}" 
                                         alt="{{ $participant->user->name }}"
                                         class="w-6 h-6 rounded-full border-2 border-white"
                                         title="{{ $participant->user->name }}">
                                @endforeach
                            </div>
                            @if($group->activeParticipants->count() > 4)
                                <span class="ml-2 text-xs text-gray-500">+{{ $group->activeParticipants->count() - 4 }} more</span>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        @php
                            $userParticipation = $group->getUserParticipation();
                        @endphp
                        
                        @if($userParticipation)
                            <button wire:click="leaveGroup({{ $group->id }})" 
                                    class="flex-1 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i>Leave
                            </button>
                        @elseif($group->canJoin())
                            <button wire:click="joinGroup({{ $group->id }})" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-user-plus mr-2"></i>Join
                            </button>
                        @else
                            <div class="flex-1 bg-gray-100 text-gray-500 px-4 py-2 rounded-lg font-medium text-center">
                                @if($group->max_participants && $group->participants_count >= $group->max_participants)
                                    Full
                                @else
                                    Unavailable
                                @endif
                            </div>
                        @endif
                        
                        <button class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow-sm p-12 text-center animate__animated animate__fadeInUp">
                <i class="fas fa-user-friends text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No study groups yet</h3>
                <p class="text-gray-600 mb-6">Create the first study group and start learning together!</p>
                <button wire:click="$set('showCreateForm', true)" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Create Study Group
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($studyGroups->hasPages())
        <div class="mt-6 animate__animated animate__fadeInUp">
            {{ $studyGroups->links() }}
        </div>
    @endif

    <!-- Create Study Group Modal -->
    @if($showCreateForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate__animated animate__fadeIn">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto animate__animated animate__zoomIn">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">Create Study Group</h3>
                        <button wire:click="$set('showCreateForm', false)" 
                                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <form wire:submit.prevent="createStudyGroup" class="p-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                            <input type="text" wire:model="title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="e.g., JavaScript Fundamentals Study Group">
                            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                            <textarea wire:model="description" rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      placeholder="Describe what this study group will focus on, goals, and expectations..."></textarea>
                            @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                <input type="datetime-local" wire:model="startDate" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @error('startDate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="datetime-local" wire:model="endDate" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @error('endDate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants</label>
                                <input type="number" wire:model="maxParticipants" min="2" max="50"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="Leave empty for unlimited">
                                @error('maxParticipants') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                <input type="text" wire:model="tags" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="JavaScript, React, Frontend (comma-separated)">
                                @error('tags') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Requirements/Prerequisites</label>
                            <textarea wire:model="requirements" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      placeholder="Any specific requirements or prerequisites for joining..."></textarea>
                            @error('requirements') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-8">
                        <button type="button" wire:click="$set('showCreateForm', false)" 
                                class="px-6 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-users mr-2"></i>Create Study Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>