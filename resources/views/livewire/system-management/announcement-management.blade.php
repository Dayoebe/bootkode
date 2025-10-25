<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-bullhorn mr-2"></i> Announcement Management
        </h1>
        <p class="text-emerald-100 mt-2">Create and manage platform announcements</p>
    </div>

    <!-- Form -->
    <div class="bg-themed-secondary shadow rounded-xl p-6 mb-8 animate__animated animate__fadeInUp border border-themed-primary">
        <h3 class="text-lg font-semibold text-themed-primary mb-6 flex items-center">
            <i class="fas fa-plus-circle text-emerald-600 dark:text-emerald-400 mr-2"></i>{{ $editId ? 'Update' : 'Create' }} Announcement
        </h3>

        <form wire:submit.prevent="saveAnnouncement">
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-themed-primary mb-2">
                        <i class="fas fa-heading mr-2 text-emerald-600 dark:text-emerald-400"></i>Title <span class="text-red-600 dark:text-red-400">*</span>
                    </label>
                    <input wire:model="title" type="text" id="title"
                           class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-all duration-200"
                           placeholder="Announcement title">
                    @error('title') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-semibold text-themed-primary mb-2">
                        <i class="fas fa-align-left mr-2 text-emerald-600 dark:text-emerald-400"></i>Content <span class="text-red-600 dark:text-red-400">*</span>
                    </label>
                    <textarea wire:model="content" id="content" rows="6"
                              class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-all duration-200"
                              placeholder="Enter announcement content..."></textarea>
                    @error('content') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <!-- Course Selection -->
                <div>
                    <label for="course_id" class="block text-sm font-semibold text-themed-primary mb-2">
                        <i class="fas fa-book mr-2 text-emerald-600 dark:text-emerald-400"></i>Course (optional)
                    </label>
                    <select wire:model="course_id" id="course_id"
                            class="w-full px-4 py-3 border border-themed-primary bg-themed-secondary rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-all duration-200">
                        <option value="">-- Platform-Wide Announcement --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('course_id') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-themed-primary mb-2">
                        <i class="fas fa-tasks mr-2 text-emerald-600 dark:text-emerald-400"></i>Status <span class="text-red-600 dark:text-red-400">*</span>
                    </label>
                    <select wire:model="status" id="status"
                            class="w-full px-4 py-3 border border-themed-primary bg-themed-secondary rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-all duration-200">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                    @error('status') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                @if($editId)
                    <button type="button" wire:click="$set('editId', null)"
                            class="px-4 py-2 border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors duration-200 font-medium">
                        Cancel
                    </button>
                @endif
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-themed-secondary disabled:opacity-50 transition-colors duration-200">
                    <span wire:loading.remove><i class="fas fa-save mr-2"></i> {{ $editId ? 'Update' : 'Create' }} Announcement</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Announcements List -->
    <div class="bg-themed-secondary shadow rounded-xl border border-themed-primary overflow-hidden">
        <!-- Search and Filter -->
        <div class="p-6 border-b border-themed-primary">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search_announcements" class="block text-sm font-medium text-themed-primary mb-2">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-themed-secondary"></i>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search_announcements"
                               placeholder="Search announcements..."
                               class="w-full pl-10 pr-4 py-2 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary">
                    </div>
                </div>
                <div class="flex-1">
                    <label for="status_filter" class="block text-sm font-medium text-themed-primary mb-2">Status</label>
                    <select wire:model.live="statusFilter" id="status_filter"
                            class="w-full px-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary">
                        <option value="all">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- List -->
        <div class="space-y-0">
            @forelse($announcements as $announcement)
                <div class="p-6 border-b border-themed-primary hover:bg-themed-tertiary transition-colors duration-200 last:border-b-0 animate__animated animate__fadeInUp">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <h3 class="text-lg font-semibold text-themed-primary">{{ $announcement->title }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($announcement->status === 'published') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                    @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @endif">
                                    {{ ucfirst($announcement->status) }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-themed-secondary mb-2">{{ Str::limit($announcement->content, 150) }}</p>
                            
                            <div class="flex items-center flex-wrap gap-3 text-xs text-themed-secondary">
                                <span><i class="fas fa-user mr-1"></i>{{ $announcement->user->name }}</span>
                                <span>•</span>
                                @if($announcement->published_at)
                                    <span><i class="fas fa-calendar mr-1"></i>Published {{ $announcement->published_at->format('M d, Y') }}</span>
                                @else
                                    <span><i class="fas fa-calendar mr-1"></i>Created {{ $announcement->created_at->format('M d, Y') }}</span>
                                @endif
                                @if($announcement->course)
                                    <span>•</span>
                                    <span><i class="fas fa-book mr-1"></i>{{ $announcement->course->title }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2 ml-4">
                            <button wire:click="editAnnouncement({{ $announcement->id }})"
                                    class="p-2 text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 hover:bg-emerald-100/30 dark:hover:bg-emerald-900/20 rounded-lg transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="deleteAnnouncement({{ $announcement->id }})" wire:confirm="Are you sure?"
                                    class="p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-red-100/30 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-themed-secondary mb-4 block opacity-50"></i>
                    <p class="text-themed-secondary text-lg">No announcements found.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
            <div class="p-4 border-t border-themed-primary flex justify-center">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>