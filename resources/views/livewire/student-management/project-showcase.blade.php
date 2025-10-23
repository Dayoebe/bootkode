<div class="bg-themed-secondary p-6 rounded-lg shadow-xl text-themed-primary max-w-7xl mx-auto my-8 animate__animated animate__fadeIn border border-themed-primary transition-colors duration-300">
    <h2 class="text-3xl font-extrabold text-themed-primary mb-6 border-b border-themed-secondary pb-4">
        <i class="fas fa-project-diagram text-accent-themed-primary mr-2"></i> Project Showcase
    </h2>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 mb-6">
        <div class="flex-1 min-w-[200px]">
            <label for="course-filter" class="block text-sm font-medium text-themed-secondary mb-2">Filter by Course</label>
            <select wire:model.live="selectedCourseId" id="course-filter" 
                class="w-full px-4 py-2 bg-themed-tertiary border border-themed-secondary rounded-xl text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label for="status-filter" class="block text-sm font-medium text-themed-secondary mb-2">Filter by Status</label>
            <select wire:model.live="filterStatus" id="status-filter" 
                class="w-full px-4 py-2 bg-themed-tertiary border border-themed-secondary rounded-xl text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="submitted">Submitted</option>
                <option value="graded">Graded</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-themed-secondary mb-2">Search Projects</label>
            <input type="text" wire:model.debounce.500ms="searchTerm" id="search" placeholder="Search by title or description..."
                   class="w-full px-4 py-2 bg-themed-tertiary border border-themed-secondary rounded-xl text-themed-primary placeholder-themed-tertiary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
        </div>
    </div>

    <!-- Project Gallery -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($projects as $project)
            <div wire:key="project-{{ $project->id }}" class="bg-themed-tertiary p-4 rounded-xl shadow-md hover:shadow-lg hover:border-accent-themed-primary transition-all duration-300 border border-themed-secondary animate__animated animate__fadeInUp">
                @if ($project->submissions->where('is_featured')->count())
                    <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                        <i class="fas fa-star mr-1"></i> Featured
                    </div>
                @endif
                <h3 class="text-xl font-semibold text-accent-themed-primary">{{ $project->title }}</h3>
                <p class="text-themed-secondary mt-2">{{ Str::limit($project->description, 100) }}</p>
                <div class="mt-3">
                    <span class="inline-block bg-accent-themed-primary/20 text-accent-themed-primary px-2 py-1 rounded-full text-sm border border-accent-themed-primary/30">
                        {{ $project->course->title }}
                    </span>
                    @if ($project->submissions->count())
                        <span class="inline-block bg-green-500/20 text-green-400 px-2 py-1 rounded-full text-sm ml-2 border border-green-500/30">
                            {{ $project->submissions->first()->status }}
                        </span>
                    @else
                        <span class="inline-block bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded-full text-sm ml-2 border border-yellow-500/30">
                            Not Submitted
                        </span>
                    @endif
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('project.show', $project->slug) }}" 
                        class="px-3 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-lg hover:text-white transition-colors duration-300">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    @if (!$project->submissions->count())
                        <button wire:click="openSubmissionForm({{ $project->id }})" 
                            class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-300">
                            <i class="fas fa-upload mr-1"></i> Submit
                        </button>
                    @endif
                    @if (Auth::user()->hasRole('instructor') && $project->submissions->count())
                        <button wire:click="toggleFeatured({{ $project->submissions->first()->id }})" 
                            class="px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors duration-300">
                            <i class="fas fa-star mr-1"></i> {{ $project->submissions->first()->is_featured ? 'Unfeature' : 'Feature' }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-5xl text-themed-tertiary mb-4"></i>
                <p class="text-themed-secondary text-lg">No projects found.</p>
            </div>
        @endforelse
    </div>

    <!-- Submission Form Modal -->
    <div x-show="showSubmissionForm" x-cloak 
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 animate__animated animate__fadeIn transition-colors duration-300">
        <div class="bg-themed-secondary p-6 rounded-xl max-w-lg w-full border border-themed-primary">
            <h3 class="text-2xl font-semibold text-themed-primary mb-4">Submit Project: {{ $selectedProject?->title }}</h3>
            <form wire:submit.prevent="submitProject" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-themed-secondary mb-2">Title</label>
                    <input type="text" wire:model.defer="submissionTitle" 
                        class="w-full px-4 py-2 bg-themed-tertiary border border-themed-secondary rounded-xl text-themed-primary placeholder-themed-tertiary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
                    @error('submissionTitle') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-themed-secondary mb-2">Description</label>
                    <textarea wire:model.defer="submissionDescription" rows="4" 
                        class="w-full px-4 py-2 bg-themed-tertiary border border-themed-secondary rounded-xl text-themed-primary placeholder-themed-tertiary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300"></textarea>
                    @error('submissionDescription') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-themed-secondary mb-2">Upload Files (max 5, 10MB each)</label>
                    <input type="file" wire:model="files" multiple 
                        class="w-full px-4 py-2 bg-themed-tertiary border border-themed-secondary rounded-xl text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
                    @error('files.*') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showSubmissionForm = false" 
                        class="px-4 py-2 bg-themed-tertiary text-themed-primary rounded-lg hover:bg-themed-secondary transition-colors duration-300">Cancel</button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-300">
                        <i class="fas fa-upload mr-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        [x-cloak] { display: none; }
        
        /* Theme transition support */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</div>