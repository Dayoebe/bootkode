<div class="p-4 sm:p-6 bg-themed-secondary rounded-lg shadow-md animate__animated animate__fadeIn transition-colors duration-300 max-w-full overflow-hidden">
    <!-- Header -->
    <div class="bg-themed-secondary backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg lg:shadow-2xl border border-themed-primary overflow-hidden mb-6 lg:mb-8 transition-colors duration-300">
        <div class="relative z-10 p-6 lg:p-8">
            <h1 class="text-3xl font-bold text-themed-primary transition-colors duration-300">
                <i class="fas fa-history mr-2"></i> User Activity
            </h1>
            <p class="text-themed-secondary mt-2 transition-colors duration-300">Monitor user actions across the platform</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-4 p-3 sm:p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 sm:p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl animate__animated animate__fadeIn transition-colors duration-300">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 bg-themed-primary bg-opacity-50 flex items-center justify-center z-50 transition-colors duration-300">
        <div class="bg-themed-secondary p-6 rounded-xl shadow-2xl border border-themed-primary transition-colors duration-300">
            <i class="fas fa-spinner fa-spin text-accent-themed-primary text-3xl mb-2 block mx-auto" aria-label="Loading"></i>
            <p class="text-themed-primary text-sm">Loading activities...</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="flex-1">
            <label for="userFilter" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Filter by User</label>
            <div class="relative">
                <select wire:model.live="userFilter" id="userFilter"
                        class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="">All Users</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>
        <div class="flex-1">
            <label for="actionFilter" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Filter by Action</label>
            <div class="relative">
                <select wire:model.live="actionFilter" id="actionFilter"
                        class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="">All Actions</option>
                    @foreach($actionTypes as $action)
                        <option value="{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>
        <div class="flex-1">
            <label for="perPage" class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Per Page</label>
            <div class="relative">
                <select wire:model.live="perPage" id="perPage"
                        class="w-full p-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary transition-all duration-200 text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-themed-secondary text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="bg-themed-secondary rounded-xl border border-themed-primary backdrop-blur-sm transition-colors duration-300 overflow-hidden shadow-lg">
        @if($activities->count() > 0)
            <div class="divide-y divide-themed-primary">
                @foreach($activities as $activity)
                    <div class="p-6 hover:bg-themed-tertiary transition-colors duration-200 animate__animated animate__fadeInUp">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($activity->causer && $activity->causer->profile_picture)
                                    <img src="{{ asset('storage/' . $activity->causer->profile_picture) }}"
                                         class="h-12 w-12 rounded-full object-cover border-2 border-accent-themed-primary/20">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary flex items-center justify-center text-white text-xl font-bold">
                                        {{ $activity->causer ? strtoupper(substr($activity->causer->name, 0, 1)) : '?' }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">
                                    {{ $activity->causer ? $activity->causer->name : 'System' }}
                                    <span class="text-themed-secondary">{{ $activity->description }}</span>
                                </p>
                                <div class="flex items-center mt-1 space-x-3">
                                    <p class="text-xs text-themed-secondary">{{ $activity->created_at->diffForHumans() }}</p>
                                    @if($activity->event)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-themed-primary/10 text-accent-themed-primary border border-accent-themed-primary/20 transition-colors duration-300">
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
            <div class="px-4 py-3 bg-themed-tertiary border-t border-themed-primary transition-colors duration-300">
                {{ $activities->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-history text-themed-secondary text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-themed-primary mb-2 transition-colors duration-300">No activities found</h3>
                <p class="text-themed-secondary transition-colors duration-300">
                    @if ($search || $userFilter || $actionFilter)
                        Try adjusting your search filters
                    @else
                        No activities have been recorded yet
                    @endif
                </p>
                @if ($search || $userFilter || $actionFilter)
                    <div class="mt-4">
                        <button wire:click="$set('search', '')" wire:click="$set('userFilter', '')" wire:click="$set('actionFilter', '')" 
                            class="inline-flex items-center px-3 py-2 border border-themed-primary shadow-sm text-sm leading-4 font-medium rounded-lg text-themed-primary bg-themed-secondary hover:bg-themed-tertiary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-themed-primary transition-colors duration-200">
                            Clear all filters
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>