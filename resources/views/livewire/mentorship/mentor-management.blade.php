<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Mentor Management</h1>
        <p class="text-gray-600 dark:text-gray-400">Approve and manage mentors</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Mentors</div>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl shadow-lg p-4">
            <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-300">{{ $stats['pending'] }}</div>
            <div class="text-sm text-yellow-600 dark:text-yellow-400">Pending</div>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl shadow-lg p-4">
            <div class="text-2xl font-bold text-green-800 dark:text-green-300">{{ $stats['active'] }}</div>
            <div class="text-sm text-green-600 dark:text-green-400">Active</div>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl shadow-lg p-4">
            <div class="text-2xl font-bold text-red-800 dark:text-red-300">{{ $stats['inactive'] }}</div>
            <div class="text-sm text-red-600 dark:text-red-400">Inactive</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl shadow-lg p-4">
            <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ $stats['total_mentorships'] }}</div>
            <div class="text-sm text-blue-600 dark:text-blue-400">Mentorships</div>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl shadow-lg p-4">
            <div class="text-2xl font-bold text-purple-800 dark:text-purple-300">{{ $stats['active_mentorships'] }}
            </div>
            <div class="text-sm text-purple-600 dark:text-purple-400">Active</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-2 mb-6">
        <div class="flex flex-wrap gap-2">
            <button wire:click="setActiveTab('pending')"
                class="px-4 py-2 rounded-lg {{ $activeTab === 'pending' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
                Pending Applications
            </button>
            <button wire:click="setActiveTab('active')"
                class="px-4 py-2 rounded-lg {{ $activeTab === 'active' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
                Active Mentors
            </button>
            <button wire:click="setActiveTab('inactive')"
                class="px-4 py-2 rounded-lg {{ $activeTab === 'inactive' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
                Inactive Mentors
            </button>
            <button wire:click="setActiveTab('all')"
                class="px-4 py-2 rounded-lg {{ $activeTab === 'all' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
                All Mentors
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 mb-6">
        <div class="grid md:grid-cols-3 gap-4">
            <input wire:model.live.debounce.300ms="searchTerm" type="text" placeholder="Search mentors..."
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <select wire:model.live="experienceFilter"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">All Experience Levels</option>
                <option value="junior">Junior</option>
                <option value="mid">Mid Level</option>
                <option value="senior">Senior</option>
                <option value="expert">Expert</option>
            </select>
        </div>
    </div>

    <!-- Mentors List -->
    <div class="space-y-4">
        @foreach($mentors as $mentor)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 flex-1">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($mentor->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 dark:text-white">{{ $mentor->user->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $mentor->user->email }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span
                                    class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $mentor->experience_label }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $mentor->years_experience }}+
                                    years</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="viewMentor({{ $mentor->id }})"
                            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                        @if(!$mentor->is_verified)
                            <button wire:click="approveMentor({{ $mentor->id }})"
                                class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                            <button wire:click="rejectMentor({{ $mentor->id }})"
                                class="px-4 py-2 bg-red-600 dark:bg-red-500 text-white rounded-lg hover:bg-red-700 dark:hover:bg-red-600">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                        @elseif($mentor->is_available)
                            <button wire:click="suspendMentor({{ $mentor->id }})"
                                class="px-4 py-2 bg-orange-600 dark:bg-orange-500 text-white rounded-lg hover:bg-orange-700 dark:hover:bg-orange-600">
                                <i class="fas fa-pause mr-1"></i>Suspend
                            </button>
                        @else
                            <button wire:click="reactivateMentor({{ $mentor->id }})"
                                class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600">
                                <i class="fas fa-play mr-1"></i>Reactivate
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $mentors->links() }}
</div>