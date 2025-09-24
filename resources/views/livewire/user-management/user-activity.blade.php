<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white">
                    <i class="fas fa-history mr-2"></i> User Activity
                </h1>
                <p class="text-gray-200 dark:text-gray-300 mt-2">Monitor user actions across the platform</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search activities..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors duration-200">
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-4 p-3 sm:p-4 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-700 dark:text-green-300 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 sm:p-4 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-white dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-50 flex items-center justify-center z-50 transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-600 transition-colors duration-300">
            <i class="fas fa-spinner fa-spin text-blue-500 dark:text-blue-400 text-3xl mb-2 block mx-auto" aria-label="Loading"></i>
            <p class="text-gray-700 dark:text-gray-300 text-sm">Loading activities...</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="flex-1">
            <label for="userFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Filter by User</label>
            <select wire:model.live="userFilter" id="userFilter"
                    class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                <option value="">All Users</option>
                @foreach($users as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label for="actionFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Filter by Action</label>
            <select wire:model.live="actionFilter" id="actionFilter"
                    class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                <option value="">All Actions</option>
                @foreach($actionTypes as $action)
                    <option value="{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label for="perPage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Per Page</label>
            <select wire:model.live="perPage" id="perPage"
                    class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
            </select>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="bg-white dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300 overflow-hidden shadow-lg">
        @if($activities->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-600/50">
                @foreach($activities as $activity)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200 animate__animated animate__fadeInUp">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($activity->causer && $activity->causer->profile_picture)
                                    <img src="{{ asset('storage/' . $activity->causer->profile_picture) }}"
                                         class="h-12 w-12 rounded-full object-cover border-2 border-blue-500/20">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-pink-600 flex items-center justify-center text-white text-xl font-bold">
                                        {{ $activity->causer ? strtoupper(substr($activity->causer->name, 0, 1)) : '?' }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">
                                    {{ $activity->causer ? $activity->causer->name : 'System' }}
                                    <span class="text-gray-500 dark:text-gray-400">{{ $activity->description }}</span>
                                </p>
                                <div class="flex items-center mt-1 space-x-3">
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                    @if($activity->event)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                                            {{ ucfirst($activity->event) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-600/50 transition-colors duration-300">
                {{ $activities->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-history text-gray-400 dark:text-gray-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-300">No activities found</h3>
                <p class="text-gray-500 dark:text-gray-400 transition-colors duration-300">
                    @if ($search || $userFilter || $actionFilter)
                        Try adjusting your search filters
                    @else
                        No activities have been recorded yet
                    @endif
                </p>
                @if ($search || $userFilter || $actionFilter)
                    <div class="mt-4">
                        <button wire:click="$set('search', '')" wire:click="$set('userFilter', '')" wire:click="$set('actionFilter', '')" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-200">
                            Clear all filters
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>