<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-bullhorn mr-2"></i> Announcement Management
        </h1>
        <p class="text-gray-400 mt-2">Create and manage platform announcements</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-8 animate__animated animate__fadeInUp">
        <form wire:submit.prevent="saveAnnouncement">
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input wire:model="title" type="text" id="title"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea wire:model="content" id="content" rows="5"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700">Course (optional)</label>
                    <select wire:model="course_id" id="course_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Platform-Wide</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('course_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model="status" id="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <span wire:loading.remove><i class="fas fa-save mr-2"></i> {{ $editId ? 'Update' : 'Create' }} Announcement</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Announcements List -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-4 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search announcements..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <select wire:model.live="statusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
        </div>
        <div class="space-y-4">
            @forelse($announcements as $announcement)
                <div class="border-b border-gray-200 pb-4 animate__animated animate__fadeInUp">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h3>
                            <p class="text-sm text-gray-600">{{ Str::limit($announcement->content, 100) }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $announcement->status === 'published' ? 'Published' : 'Draft' }} by {{ $announcement->user->name }}
                                @if($announcement->published_at)
                                    on {{ $announcement->published_at->format('M d, Y') }}
                                @endif
                                @if($announcement->course)
                                    for {{ $announcement->course->title }}
                                @endif
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="editAnnouncement({{ $announcement->id }})"
                                    class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="deleteAnnouncement({{ $announcement->id }})"
                                    class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No announcements found.</p>
            @endforelse
            <div class="mt-4">{{ $announcements->links() }}</div>
        </div>
    </div>
</div>