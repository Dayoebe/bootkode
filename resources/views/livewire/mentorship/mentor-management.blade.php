<div class="min-h-screen bg-themed-primary p-6 transition-colors duration-300">
    <!-- Header -->
    <div
        class="bg-themed-secondary rounded-2xl shadow-lg p-6 mb-6 border border-themed-primary transition-colors duration-300">
        <h1 class="text-3xl font-bold text-themed-primary mb-2 transition-colors duration-300">Mentor Management</h1>
        <p class="text-themed-secondary transition-colors duration-300">Approve and manage mentors</p>
    </div>
    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div
            class="bg-themed-secondary rounded-xl shadow-lg p-4 border border-themed-primary transition-colors duration-300">
            <div class="text-2xl font-bold text-themed-primary transition-colors duration-300">{{ $stats['total'] }}
            </div>
            <div class="text-sm text-themed-secondary transition-colors duration-300">Total Mentors</div>
        </div>
        <div
            class="bg-yellow-100/50 dark:bg-yellow-900/30 rounded-xl shadow-lg p-4 border border-yellow-200/50 dark:border-yellow-800 transition-colors duration-300">
            <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-300 transition-colors duration-300">
                {{ $stats['pending'] }}</div>
            <div class="text-sm text-yellow-700 dark:text-yellow-400 transition-colors duration-300">Pending</div>
        </div>
        <div
            class="bg-green-100/50 dark:bg-green-900/30 rounded-xl shadow-lg p-4 border border-green-200/50 dark:border-green-800 transition-colors duration-300">
            <div class="text-2xl font-bold text-green-800 dark:text-green-300 transition-colors duration-300">
                {{ $stats['active'] }}</div>
            <div class="text-sm text-green-700 dark:text-green-400 transition-colors duration-300">Active</div>
        </div>
        <div
            class="bg-red-100/50 dark:bg-red-900/30 rounded-xl shadow-lg p-4 border border-red-200/50 dark:border-red-800 transition-colors duration-300">
            <div class="text-2xl font-bold text-red-800 dark:text-red-300 transition-colors duration-300">
                {{ $stats['inactive'] }}</div>
            <div class="text-sm text-red-700 dark:text-red-400 transition-colors duration-300">Inactive</div>
        </div>
        <div
            class="bg-accent-primary/20 rounded-xl shadow-lg p-4 border border-accent-primary/30 transition-colors duration-300">
            <div class="text-2xl font-bold text-accent-primary transition-colors duration-300">
                {{ $stats['total_mentorships'] }}</div>
            <div class="text-sm text-accent-primary/80 transition-colors duration-300">Mentorships</div>
        </div>
        <div
            class="bg-purple-100/50 dark:bg-purple-900/30 rounded-xl shadow-lg p-4 border border-purple-200/50 dark:border-purple-800 transition-colors duration-300">
            <div class="text-2xl font-bold text-purple-800 dark:text-purple-300 transition-colors duration-300">
                {{ $stats['active_mentorships'] }}</div>
            <div class="text-sm text-purple-700 dark:text-purple-400 transition-colors duration-300">Active</div>
        </div>
    </div>

    <!-- Tabs -->
    <div
        class="bg-themed-secondary rounded-xl shadow-lg p-2 mb-6 border border-themed-primary transition-colors duration-300">
        <div class="flex flex-wrap gap-2">
            <button wire:click="setActiveTab('pending')"
                class="px-4 py-2 rounded-lg transition-colors duration-300 {{ $activeTab === 'pending' ? 'bg-accent-primary text-white' : 'text-themed-primary hover:bg-themed-tertiary' }}">
                Pending Applications
            </button>
            <button wire:click="setActiveTab('active')"
                class="px-4 py-2 rounded-lg transition-colors duration-300 {{ $activeTab === 'active' ? 'bg-accent-primary text-white' : 'text-themed-primary hover:bg-themed-tertiary' }}">
                Active Mentors
            </button>
            <button wire:click="setActiveTab('inactive')"
                class="px-4 py-2 rounded-lg transition-colors duration-300 {{ $activeTab === 'inactive' ? 'bg-accent-primary text-white' : 'text-themed-primary hover:bg-themed-tertiary' }}">
                Inactive Mentors
            </button>
            <button wire:click="setActiveTab('all')"
                class="px-4 py-2 rounded-lg transition-colors duration-300 {{ $activeTab === 'all' ? 'bg-accent-primary text-white' : 'text-themed-primary hover:bg-themed-tertiary' }}">
                All Mentors
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div
        class="bg-themed-secondary rounded-xl shadow-lg p-4 mb-6 border border-themed-primary transition-colors duration-300">
        <div class="grid md:grid-cols-3 gap-4">
            <input wire:model.live.debounce.300ms="searchTerm" type="text" placeholder="Search mentors..."
                class="px-4 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary placeholder-themed-tertiary focus:ring-2 focus:ring-accent-primary focus:border-transparent transition-colors duration-300">
            <select wire:model.live="experienceFilter"
                class="px-4 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-primary focus:border-transparent transition-colors duration-300">
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
            <div
                class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 flex-1">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-accent-primary to-accent-secondary rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($mentor->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-themed-primary transition-colors duration-300">
                                {{ $mentor->user->name }}</h3>
                            <p class="text-sm text-themed-secondary transition-colors duration-300">
                                {{ $mentor->user->email }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span
                                    class="text-xs bg-accent-primary/20 text-accent-primary px-2 py-1 rounded border border-accent-primary/30 transition-colors duration-300">{{ $mentor->experience_label }}</span>
                                <span
                                    class="text-xs text-themed-secondary transition-colors duration-300">{{ $mentor->years_experience }}+
                                    years</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="viewMentor({{ $mentor->id }})"
                            class="px-4 py-2 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                        @if(!$mentor->is_verified)
                            <button wire:click="approveMentor({{ $mentor->id }})"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                            <button wire:click="rejectMentor({{ $mentor->id }})"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                        @elseif($mentor->is_available)
                            <button wire:click="suspendMentor({{ $mentor->id }})"
                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-pause mr-1"></i>Suspend
                            </button>
                        @else
                            <button wire:click="reactivateMentor({{ $mentor->id }})"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded-lg transition-colors">
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