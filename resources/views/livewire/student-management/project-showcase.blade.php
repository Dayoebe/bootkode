<div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white max-w-7xl mx-auto my-8 animate__animated animate__fadeIn">
    <h2 class="text-3xl font-extrabold text-white mb-6 border-b border-gray-700 pb-4">
        <i class="fas fa-project-diagram text-teal-400 mr-2"></i> Project Showcase
    </h2>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 mb-6">
        <div class="flex-1 min-w-[200px]">
            <label for="course-filter" class="block text-sm font-medium text-gray-300 mb-2">Filter by Course</label>
            <select wire:model.live="selectedCourseId" id="course-filter" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-teal-500">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label for="status-filter" class="block text-sm font-medium text-gray-300 mb-2">Filter by Status</label>
            <select wire:model.live="filterStatus" id="status-filter" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-teal-500">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="submitted">Submitted</option>
                <option value="graded">Graded</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search Projects</label>
            <input type="text" wire:model.debounce.500ms="searchTerm" id="search" placeholder="Search by title or description..."
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-teal-500">
        </div>
    </div>

    <!-- Project Gallery -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($projects as $project)
            <div wire:key="project-{{ $project->id }}" class="bg-gray-700 p-4 rounded-xl shadow-md hover:shadow-lg transition-all animate__animated animate__fadeInUp">
                @if ($project->submissions->where('is_featured')->count())
                    <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                        <i class="fas fa-star mr-1"></i> Featured
                    </div>
                @endif
                <h3 class="text-xl font-semibold text-teal-400">{{ $project->title }}</h3>
                <p class="text-gray-300 mt-2">{{ Str::limit($project->description, 100) }}</p>
                <div class="mt-3">
                    <span class="inline-block bg-indigo-600/20 text-indigo-400 px-2 py-1 rounded-full text-sm">
                        {{ $project->course->title }}
                    </span>
                    @if ($project->submissions->count())
                        <span class="inline-block bg-green-600/20 text-green-400 px-2 py-1 rounded-full text-sm ml-2">
                            {{ $project->submissions->first()->status }}
                        </span>
                    @else
                        <span class="inline-block bg-yellow-600/20 text-yellow-400 px-2 py-1 rounded-full text-sm ml-2">
                            Not Submitted
                        </span>
                    @endif
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('project.show', $project->slug) }}" class="px-3 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    @if (!$project->submissions->count())
                        <button wire:click="openSubmissionForm({{ $project->id }})" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-upload mr-1"></i> Submit
                        </button>
                    @endif
                    @if (Auth::user()->hasRole('instructor') && $project->submissions->count())
                        <button wire:click="toggleFeatured({{ $project->submissions->first()->id }})" class="px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-star mr-1"></i> {{ $project->submissions->first()->is_featured ? 'Unfeature' : 'Feature' }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-gray-400 text-center">No projects found.</div>
        @endforelse
    </div>

    <!-- Submission Form Modal -->
    <div x-show="showSubmissionForm" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate__animated animate__fadeIn">
        <div class="bg-gray-800 p-6 rounded-xl max-w-lg w-full">
            <h3 class="text-2xl font-semibold text-white mb-4">Submit Project: {{ $selectedProject?->title }}</h3>
            <form wire:submit.prevent="submitProject" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Title</label>
                    <input type="text" wire:model.defer="submissionTitle" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-green-500">
                    @error('submissionTitle') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea wire:model.defer="submissionDescription" rows="4" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-green-500"></textarea>
                    @error('submissionDescription') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Upload Files (max 5, 10MB each)</label>
                    <input type="file" wire:model="files" multiple class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-xl text-white">
                    @error('files.*') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showSubmissionForm = false" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-upload mr-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none; }
</style>