<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-bullhorn mr-2"></i> Announcements
        </h1>
        <p class="text-gray-400 mt-2">View platform and course announcements</p>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-8 animate__animated animate__fadeInUp">
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search announcements..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <select wire:model.live="courseFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'all'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'all', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'all' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-list mr-2"></i> All Announcements
                </button>
                <button @click="activeTab = 'my_courses'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'my_courses', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'my_courses' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-graduation-cap mr-2"></i> My Courses
                </button>
            </nav>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="bg-white shadow rounded-lg p-6 animate__animated animate__fadeInUp">
        <div class="space-y-6">
            @forelse($announcements as $announcement)
                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $announcement->content }}</p>
                            <p class="text-xs text-gray-400">
                                Published by {{ $announcement->user->name }}
                                on {{ $announcement->published_at->format('M d, Y') }}
                                @if($announcement->course)
                                    for {{ $announcement->course->title }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No announcements found.</p>
            @endforelse
            <div class="mt-4">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</div>
