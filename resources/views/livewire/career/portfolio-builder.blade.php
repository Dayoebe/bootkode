<div class="min-h-screen bg-themed-primary">
    <!-- Header Section -->
    <div class="bg-themed-secondary shadow-lg border-b border-themed-primary">
        <div class="px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-themed-primary mb-2">Portfolio Builder</h1>
                    <p class="text-xl text-themed-secondary">Create and showcase your professional work</p>
                </div>

                <!-- Portfolio Statistics -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-accent-themed-primary rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $totalProjects }}</div>
                        <div class="text-sm opacity-90">Total Projects</div>
                    </div>
                
                    <div class="bg-green-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $completedProjects }}</div>
                        <div class="text-sm opacity-90">Completed</div>
                    </div>
                
                    <div class="bg-orange-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $inProgressProjects }}</div>
                        <div class="text-sm opacity-90">In Progress</div>
                    </div>
                
                    <div class="bg-red-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $inOnHoldProjects }}</div>
                        <div class="text-sm opacity-90">On Hold</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative animate-fade-in"
                role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative animate-fade-in"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="px-6 py-8">
        <!-- Action Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 space-y-4 lg:space-y-0">
            <div class="flex flex-wrap items-center gap-4">
                <button wire:click="$toggle('showForm')"
                    class="bg-accent-themed-primary text-white px-6 py-3 rounded-xl hover:bg-accent-themed-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ $showForm ? 'Cancel' : 'Add New Project' }}
                </button>

                @if($showBulkActions)
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-themed-secondary">{{ count($bulkSelected) }} selected</span>
                        <button wire:click="bulkDelete"
                            wire:confirm="Are you sure you want to delete the selected projects?"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                            Delete Selected
                        </button>
                        <button wire:click="clearBulkSelection"
                            class="text-themed-secondary hover:text-themed-primary transition-colors text-sm">
                            Clear Selection
                        </button>
                    </div>
                @endif

                <button wire:click="exportPortfolio"
                    class="text-themed-secondary hover:text-themed-primary transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Portfolio
                </button>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative">
                    <input wire:model.live.debounce.300ms="searchTerm" type="text" placeholder="Search projects..."
                        class="pl-10 pr-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary w-64">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-themed-secondary" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select wire:model.live="filterCategory"
                    class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                    <option value="">All Categories</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterStatus"
                    class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="in-progress">In Progress</option>
                    <option value="planning">Planning</option>
                    <option value="on-hold">On Hold</option>
                </select>
            </div>
        </div>

        <!-- View Controls -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <select wire:model.live="sortBy"
                    class="px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary text-sm">
                    <option value="created_at">Sort by Date Created</option>
                    <option value="title">Sort by Title</option>
                    <option value="views_count">Sort by Views</option>
                    <option value="status">Sort by Status</option>
                </select>

                <button wire:click="$toggle('sortDirection')"
                    class="p-2 border border-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors">
                    <svg class="w-4 h-4 text-themed-primary {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>

                @if(count($portfolios) > 0)
                    <button wire:click="selectAllVisible"
                        class="text-sm text-accent-themed-primary hover:text-accent-themed-secondary transition-colors">
                        Select All ({{ count($portfolios) }})
                    </button>
                @endif
            </div>

            <div class="flex items-center space-x-2">
                <span class="text-sm text-themed-secondary">View:</span>
                <button wire:click="$set('viewMode', 'grid')"
                    class="p-2 rounded-lg {{ $viewMode === 'grid' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary border border-themed-primary' }} transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button wire:click="$set('viewMode', 'list')"
                    class="p-2 rounded-lg {{ $viewMode === 'list' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary border border-themed-primary' }} transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <button wire:click="$set('viewMode', 'masonry')"
                    class="p-2 rounded-lg {{ $viewMode === 'masonry' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary border border-themed-primary' }} transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M3 4a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM11 4a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zM11 12a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Project Form Modal -->
        @if($showForm)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
                <div class="bg-themed-secondary rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                    <div class="flex justify-between items-center p-6 border-b border-themed-primary bg-themed-tertiary">
                        <h2 class="text-2xl font-bold text-themed-primary">
                            {{ $editingProjectId ? 'Edit Project' : 'Create New Project' }}
                        </h2>
                        <button wire:click="resetForm" class="text-themed-secondary hover:text-themed-primary transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveProject" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Project Title -->
                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Project Title *</label>
                                    <input wire:model="title" type="text" placeholder="e.g., E-commerce Website Redesign"
                                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-all">
                                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Category & Status -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Category *</label>
                                        <select wire:model="category"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Status *</label>
                                        <select wire:model="status"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                            <option value="completed">Completed</option>
                                            <option value="in-progress">In Progress</option>
                                            <option value="planning">Planning</option>
                                            <option value="on-hold">On Hold</option>
                                        </select>
                                        @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <!-- Project URL & Client -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Project URL</label>
                                        <input wire:model="project_url" type="url" placeholder="https://example.com"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                        @error('project_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Client Name</label>
                                        <input wire:model="client_name" type="text" placeholder="e.g., ABC Company"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                        @error('client_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <!-- Date Range -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Start Date</label>
                                        <input wire:model="start_date" type="date"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                        @error('start_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">End Date</label>
                                        <input wire:model="end_date" type="date"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                        @error('end_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Technologies -->
                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Technologies Used *</label>
                                    <textarea wire:model="technologies"
                                        placeholder="e.g., React, Node.js, MongoDB, Tailwind CSS" rows="3"
                                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"></textarea>
                                    <p class="text-sm text-themed-secondary mt-1">Separate technologies with commas</p>
                                    @error('technologies') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Main Image -->
                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Main Project Image</label>
                                    <div
                                        class="border-2 border-dashed border-themed-primary rounded-xl p-6 text-center hover:border-accent-themed-primary transition-colors bg-themed-tertiary">
                                        <input wire:model="image" type="file" accept="image/*" class="hidden"
                                            id="main-image">
                                        <label for="main-image" class="cursor-pointer">
                                            <svg class="mx-auto h-12 w-12 text-themed-secondary" stroke="currentColor" fill="none"
                                                viewBox="0 0 48 48">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="mt-2 text-sm text-themed-secondary">Click to upload or drag and drop</p>
                                            <p class="text-xs text-themed-tertiary">PNG, JPG, GIF up to 2MB</p>
                                        </label>
                                    </div>
                                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                                    @if ($image)
                                        <div class="mt-4">
                                            <img src="{{ $image->temporaryUrl() }}"
                                                class="w-full h-48 object-cover rounded-xl shadow-md">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Project Description *</label>
                            <textarea wire:model="description"
                                placeholder="Describe your project in detail. What was the goal? What challenges did you face? What was the outcome?"
                                rows="6"
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"></textarea>
                            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-themed-primary">
                            <button type="button" wire:click="resetForm"
                                class="px-6 py-3 border border-themed-primary text-themed-primary rounded-xl hover:bg-themed-tertiary transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-accent-themed-primary text-white px-8 py-3 rounded-xl hover:bg-accent-themed-secondary transition-all font-semibold shadow-lg hover:shadow-xl">
                                <span wire:loading.remove wire:target="saveProject">
                                    {{ $editingProjectId ? 'Update Project' : 'Create Project' }}
                                </span>
                                <span wire:loading wire:target="saveProject">
                                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Projects Display - Grid View -->
        @if(count($portfolios) > 0)
            @if($viewMode === 'grid')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($portfolios as $project)
                        <div wire:key="{{ $project->id }}"
                            class="group bg-themed-secondary rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-themed-primary hover:border-accent-themed-primary relative">
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4 z-10">
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $project->status_label }}
                                </span>
                            </div>

                            <!-- Project Image -->
                            <div class="relative h-48 overflow-hidden cursor-pointer"
                                wire:click="previewProject({{ $project->id }})">
                                <img src="{{ $project->image_url }}" alt="{{ $project->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Content -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-themed-primary group-hover:text-accent-themed-primary transition-colors cursor-pointer line-clamp-2 mb-3"
                                    wire:click="previewProject({{ $project->id }})">
                                    {{ $project->title }}
                                </h3>

                                <p class="text-themed-secondary text-sm mb-4 line-clamp-3">
                                    {{ Str::limit($project->description, 120) }}
                                </p>

                                <div class="flex space-x-2">
                                    <button wire:click="previewProject({{ $project->id }})"
                                        class="flex-1 bg-accent-themed-primary text-white px-4 py-2 rounded-lg hover:bg-accent-themed-secondary transition-colors text-sm font-medium">
                                        View Details
                                    </button>
                                    <button wire:click="editProject({{ $project->id }})"
                                        class="p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($viewMode === 'list')
                <!-- List View -->
                <div class="space-y-4">
                    @foreach ($portfolios as $project)
                        <div wire:key="{{ $project->id }}"
                            class="bg-themed-secondary rounded-xl shadow-md hover:shadow-lg transition-all duration-300 p-6 border border-themed-primary hover:border-accent-themed-primary">
                            <div class="flex flex-col lg:flex-row lg:items-center">
                                <!-- Project Image -->
                                <div class="lg:w-32 lg:h-20 mb-4 lg:mb-0 lg:mr-6">
                                    <img src="{{ $project->thumbnail_url }}" alt="{{ $project->title }}"
                                        class="w-full h-32 lg:h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                                        wire:click="previewProject({{ $project->id }})">
                                </div>

                                <!-- Project Info -->
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-themed-primary hover:text-accent-themed-primary transition-colors cursor-pointer mb-2"
                                        wire:click="previewProject({{ $project->id }})">
                                        {{ $project->title }}
                                    </h3>
                                    <p class="text-themed-secondary text-sm mb-3 line-clamp-2">
                                        {{ Str::limit($project->description, 150) }}
                                    </p>

                                    <!-- Meta Info -->
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-themed-secondary">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $project->duration }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $project->views_count }} views
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col lg:ml-6 space-y-2 mt-4 lg:mt-0">
                                    <button wire:click="previewProject({{ $project->id }})"
                                        class="bg-accent-themed-primary text-white px-4 py-2 rounded-lg hover:bg-accent-themed-secondary transition-colors text-sm font-medium">
                                        View Details
                                    </button>

                                    <div class="flex space-x-2">
                                        <button wire:click="editProject({{ $project->id }})"
                                            class="flex-1 bg-yellow-100 text-yellow-700 px-3 py-2 rounded-lg hover:bg-yellow-200 transition-colors text-sm">
                                            Edit
                                        </button>

                                        <button wire:click="deleteProject({{ $project->id }})"
                                            wire:confirm="Are you sure you want to delete this project?"
                                            class="flex-1 bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200 transition-colors text-sm">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Masonry View -->
                <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6">
                    @foreach ($portfolios as $project)
                        <div wire:key="{{ $project->id }}" class="break-inside-avoid mb-6">
                            <div class="bg-themed-secondary rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-themed-primary hover:border-accent-themed-primary group">
                                <!-- Project Image -->
                                <div class="relative overflow-hidden cursor-pointer"
                                    wire:click="previewProject({{ $project->id }})">
                                    <img src="{{ $project->image_url }}" alt="{{ $project->title }}"
                                        class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-300">

                                    <!-- Status -->
                                    <div class="absolute top-4 right-4">
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                                            {{ $project->status_label }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Project Content -->
                                <div class="p-4">
                                    <h3 class="text-lg font-bold text-themed-primary group-hover:text-accent-themed-primary transition-colors cursor-pointer mb-2 line-clamp-2"
                                        wire:click="previewProject({{ $project->id }})">
                                        {{ $project->title }}
                                    </h3>

                                    <p class="text-themed-secondary text-sm mb-3 line-clamp-3">
                                        {{ Str::limit($project->description, 100) }}
                                    </p>

                                    <!-- Quick Actions -->
                                    <div class="flex space-x-1">
                                        <button wire:click="previewProject({{ $project->id }})"
                                            class="flex-1 bg-accent-themed-primary text-white px-3 py-2 rounded-lg hover:bg-accent-themed-secondary transition-colors text-xs font-medium">
                                            View
                                        </button>
                                        <button wire:click="editProject({{ $project->id }})"
                                            class="p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto w-32 h-32 bg-themed-tertiary rounded-full flex items-center justify-center mb-6">
                    <svg class="w-16 h-16 text-accent-themed-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-themed-primary mb-2">No projects yet</h3>
                <p class="text-themed-secondary mb-6 max-w-md mx-auto">Start building your portfolio by creating your first project. Showcase your work and let your skills shine!</p>
                <button wire:click="$set('showForm', true)"
                    class="bg-accent-themed-primary text-white px-8 py-3 rounded-xl hover:bg-accent-themed-secondary transition-all font-semibold shadow-lg hover:shadow-xl">
                    Create Your First Project
                </button>
            </div>
        @endif
    </div>

    <!-- Project Preview Modal -->
    @if($showPreview && $previewProject)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
            <div class="bg-themed-secondary rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="flex justify-between items-center p-6 border-b border-themed-primary bg-themed-tertiary">
                    <div>
                        <h2 class="text-2xl font-bold text-themed-primary">{{ $previewProject->title }}</h2>
                    </div>
                    <button wire:click="closePreview" class="text-themed-secondary hover:text-themed-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="p-6">
                        <!-- Main Image -->
                        <div class="mb-6">
                            <img src="{{ $previewProject->image_url }}" alt="{{ $previewProject->title }}"
                                class="w-full h-80 object-cover rounded-xl shadow-lg">
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-themed-primary mb-3">Project Description</h3>
                            <div class="prose max-w-none text-themed-primary">
                                {!! nl2br(e($previewProject->description)) !!}
                            </div>
                        </div>

                        <!-- Project Details -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <div class="bg-themed-tertiary rounded-xl p-4">
                                <h4 class="text-sm font-semibold text-themed-primary mb-3">Project Details</h4>
                                <div class="space-y-2 text-sm">
                                    @if($previewProject->client_name)
                                        <div class="flex justify-between">
                                            <span class="text-themed-secondary">Client:</span>
                                            <span class="font-medium text-themed-primary">{{ $previewProject->client_name }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span class="text-themed-secondary">Duration:</span>
                                        <span class="font-medium text-themed-primary">{{ $previewProject->duration }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-themed-secondary">Views:</span>
                                        <span class="font-medium text-themed-primary">{{ $previewProject->views_count }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                @if($previewProject->project_url)
                                    <a href="{{ $previewProject->project_url }}" target="_blank"
                                        class="w-full block text-center bg-accent-themed-primary text-white px-4 py-3 rounded-lg hover:bg-accent-themed-secondary transition-colors font-medium">
                                        🚀 View Live Project
                                    </a>
                                @endif
                                <button wire:click="editProject({{ $previewProject->id }})"
                                    class="w-full bg-yellow-100 text-yellow-700 px-4 py-3 rounded-lg hover:bg-yellow-200 transition-colors font-medium">
                                    ✏️ Edit Project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
</div>