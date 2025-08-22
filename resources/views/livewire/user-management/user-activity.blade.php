<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white">
                    <i class="fas fa-history mr-2"></i> User Activity
                </h1>
                <p class="text-gray-400 mt-2">Monitor user actions across the platform</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search activities..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900">
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <label for="userFilter" class="block text-sm font-medium text-gray-700">Filter by User</label>
            <select wire:model.live="userFilter" id="userFilter"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Users</option>
                @foreach($users as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label for="actionFilter" class="block text-sm font-medium text-gray-700">Filter by Action</label>
            <select wire:model.live="actionFilter" id="actionFilter"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Actions</option>
                @foreach($actionTypes as $action)
                    <option value="{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($activities->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($activities as $activity)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200 animate__animated animate__fadeInUp">
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
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $activity->causer ? $activity->causer->name : 'System' }}
                                    <span class="text-gray-500">{{ $activity->description }}</span>
                                </p>
                                <p class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                                @if($activity->event)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($activity->event) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $activities->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No activities found.</p>
            </div>
        @endif
    </div>
</div>