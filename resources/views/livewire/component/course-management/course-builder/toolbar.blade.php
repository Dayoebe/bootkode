<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-700 pb-6 mb-8">
    <div class="flex items-center space-x-4 mb-4 sm:mb-0">
        <a href="{{ route('all-course') }}"
            class="px-4 py-2.5 bg-gray-700 text-gray-300 rounded-xl hover:bg-gray-600 transition-colors duration-200 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i> Back to Courses
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                Course Builder: <span class="text-blue-400">{{ $course->title }}</span>
            </h1>
            <div class="flex items-center space-x-4 mt-2 text-sm text-gray-400">
                <span><i class="fas fa-user mr-1"></i> {{ $course->instructor->name }}</span>
                <span><i class="fas fa-calendar mr-1"></i> Updated {{ $course->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Course Stats -->
        <div class="hidden lg:flex items-center space-x-4 text-sm">
            <div class="bg-gray-800 px-3 py-2 rounded-lg">
                <span class="text-gray-400">Progress:</span>
                <span class="text-green-400 font-semibold ml-1">{{ $courseStats['completion_percentage'] }}%</span>
            </div>
            <div class="bg-gray-800 px-3 py-2 rounded-lg">
                <span class="text-gray-400">Duration:</span>
                <span class="text-blue-400 font-semibold ml-1">{{ floor($courseStats['total_duration'] / 60) }}h
                    {{ $courseStats['total_duration'] % 60 }}m</span>
            </div>
        </div>

        <span class="text-gray-400 hidden sm:block">
            Status: <span class="text-{{ $course->is_published ? 'green' : 'yellow' }}-400 font-semibold">
                {{ $course->is_published ? 'Published' : 'Draft' }}
            </span>
        </span>

        <button wire:click="togglePublished" wire:loading.attr="disabled"
            class="px-4 sm:px-6 py-2.5 bg-gradient-to-r {{ $course->is_published ? 'from-red-600 to-pink-600' : 'from-green-600 to-emerald-600' }} text-white rounded-xl font-semibold hover:opacity-90 transition-all duration-300 shadow-lg">
            <i class="fas fa-{{ $course->is_published ? 'eye-slash' : 'eye' }} mr-1"></i>
            {{ $course->is_published ? 'Unpublish' : 'Publish' }}
            <span wire:loading wire:target="togglePublished" class="ml-2">
                <i class="fas fa-spinner fa-spin"></i>
            </span>
        </button>

        <button wire:click="saveContent" wire:loading.attr="disabled"
            class="px-4 sm:px-6 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-semibold hover:opacity-90 transition-all duration-300 shadow-lg">
            <i class="fas fa-save mr-1"></i>
            <span class="hidden sm:inline">Save Changes</span>
            <span class="sm:hidden">Save</span>
            <span wire:loading wire:target="saveContent" class="ml-2">
                <i class="fas fa-spinner fa-spin"></i>
            </span>
        </button>

        <!-- More Actions Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="px-3 py-2.5 bg-gray-700 text-gray-300 rounded-xl hover:bg-gray-600 transition-colors">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl border border-gray-700 z-50">
                <button wire:click="previewCourse" wire:loading.attr="disabled"
                class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 rounded-t-lg">
                <i class="fas fa-eye mr-2"></i> Preview Course
                <span wire:loading wire:target="previewCourse" class="ml-2">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
                <button wire:click="exportCourseOutline" wire:loading.attr="disabled"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">
                    <i class="fas fa-download mr-2"></i> Export Outline
                    <span wire:loading wire:target="exportCourseOutline" class="ml-2">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
                <button wire:click="openCourseSettings" wire:loading.attr="disabled"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 rounded-b-lg">
                    <i class="fas fa-cog mr-2"></i> Course Settings
                    <span wire:loading wire:target="openCourseSettings" class="ml-2">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>

    
</div>
