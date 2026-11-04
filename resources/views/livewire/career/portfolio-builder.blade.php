<div class="space-y-6">
    <x-ui.page-header
        title="Project portfolio"
        description="Keep work private while it is in progress, then publish only the projects you choose."
    >
        <x-slot:actions>
            <button type="button" class="bk-button bk-button-primary" wire:click="$set('showForm', true)">
                Add project
            </button>
            <button type="button" class="bk-button bk-button-secondary" wire:click="exportPortfolio">
                Export CSV
            </button>
        </x-slot:actions>
    </x-ui.page-header>

    <div aria-live="polite" aria-atomic="true">
        @if(session('message'))
            <div class="bk-feedback" role="status">{{ session('message') }}</div>
        @endif
        @error('project')
            <div class="bk-feedback bk-feedback-error" role="alert">{{ $message }}</div>
        @enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Portfolio summary">
        <x-ui.panel><p class="text-sm text-content-muted">Projects</p><p class="mt-1 text-2xl font-bold text-content">{{ $totalProjects }}</p></x-ui.panel>
        <x-ui.panel><p class="text-sm text-content-muted">Completed</p><p class="mt-1 text-2xl font-bold text-content">{{ $completedProjects }}</p></x-ui.panel>
        <x-ui.panel><p class="text-sm text-content-muted">In progress</p><p class="mt-1 text-2xl font-bold text-content">{{ $inProgressProjects }}</p></x-ui.panel>
        <x-ui.panel><p class="text-sm text-content-muted">Published</p><p class="mt-1 text-2xl font-bold text-content">{{ collect($portfolios)->where('visibility', 'public')->count() }}</p></x-ui.panel>
    </div>

    <x-ui.panel title="Showcase privacy">
        <p class="text-sm leading-6 text-content-muted">New projects are private. Published projects appear only on your slug-based showcase, and only while your account profile visibility is Public. BootKode does not provide a public portfolio directory.</p>
        @php($publishedProject = collect($portfolios)->firstWhere('visibility', 'public'))
        @if($publishedProject && auth()->user()->profile_visibility === 'public')
            <a class="bk-button bk-button-secondary mt-4" href="{{ route('portfolio.public', $publishedProject->slug) }}" target="_blank" rel="noopener">View public showcase</a>
        @elseif($publishedProject)
            <p class="mt-3 text-sm font-semibold text-content">A project is marked published, but the showcase stays unavailable until Profile visibility is set to Public.</p>
        @endif
    </x-ui.panel>

    @if($showForm)
        <x-ui.panel :title="$editingProjectId ? 'Edit project' : 'Create project'">
            <form wire:submit="saveProject" class="space-y-6" novalidate>
                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label for="portfolio-title" class="mb-2 block text-sm font-semibold text-content">Project title *</label>
                        <input id="portfolio-title" class="bk-input" wire:model="title" type="text" maxlength="255" aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}" aria-describedby="portfolio-title-error">
                        @error('title')<p id="portfolio-title-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-technologies" class="mb-2 block text-sm font-semibold text-content">Technologies and tools *</label>
                        <input id="portfolio-technologies" class="bk-input" wire:model="technologies" type="text" maxlength="1000" placeholder="Laravel, Livewire, MySQL" aria-invalid="{{ $errors->has('technologies') ? 'true' : 'false' }}" aria-describedby="portfolio-technologies-help portfolio-technologies-error">
                        <p id="portfolio-technologies-help" class="mt-1 text-sm text-content-muted">Separate items with commas.</p>
                        @error('technologies')<p id="portfolio-technologies-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-category" class="mb-2 block text-sm font-semibold text-content">Category *</label>
                        <select id="portfolio-category" class="bk-input" wire:model="category" aria-invalid="{{ $errors->has('category') ? 'true' : 'false' }}" aria-describedby="portfolio-category-error">
                            <option value="">Choose a category</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')<p id="portfolio-category-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-status" class="mb-2 block text-sm font-semibold text-content">Status *</label>
                        <select id="portfolio-status" class="bk-input" wire:model="status" aria-invalid="{{ $errors->has('status') ? 'true' : 'false' }}" aria-describedby="portfolio-status-error">
                            <option value="completed">Completed</option>
                            <option value="in-progress">In progress</option>
                            <option value="planning">Planning</option>
                            <option value="on-hold">On hold</option>
                        </select>
                        @error('status')<p id="portfolio-status-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-demo-url" class="mb-2 block text-sm font-semibold text-content">Demo URL</label>
                        <input id="portfolio-demo-url" class="bk-input" wire:model="project_url" type="url" inputmode="url" maxlength="255" placeholder="https://example.com" aria-invalid="{{ $errors->has('project_url') ? 'true' : 'false' }}" aria-describedby="portfolio-demo-url-error">
                        @error('project_url')<p id="portfolio-demo-url-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-source-url" class="mb-2 block text-sm font-semibold text-content">Source-code URL</label>
                        <input id="portfolio-source-url" class="bk-input" wire:model="source_code_url" type="url" inputmode="url" maxlength="2048" placeholder="https://github.com/name/project" aria-invalid="{{ $errors->has('source_code_url') ? 'true' : 'false' }}" aria-describedby="portfolio-source-url-error">
                        @error('source_code_url')<p id="portfolio-source-url-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-client" class="mb-2 block text-sm font-semibold text-content">Client or context</label>
                        <input id="portfolio-client" class="bk-input" wire:model="client_name" type="text" maxlength="255" aria-invalid="{{ $errors->has('client_name') ? 'true' : 'false' }}" aria-describedby="portfolio-client-error">
                        @error('client_name')<p id="portfolio-client-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-image" class="mb-2 block text-sm font-semibold text-content">Project image</label>
                        <input id="portfolio-image" class="bk-input" wire:model="image" type="file" accept="image/png,image/jpeg,image/gif,image/webp" aria-describedby="portfolio-image-help portfolio-image-error">
                        <p id="portfolio-image-help" class="mt-1 text-sm text-content-muted">Optional image, up to 2 MiB. Existing images stay unchanged unless replaced.</p>
                        @error('image')<p id="portfolio-image-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-start-date" class="mb-2 block text-sm font-semibold text-content">Start date</label>
                        <input id="portfolio-start-date" class="bk-input" wire:model="start_date" type="date" aria-invalid="{{ $errors->has('start_date') ? 'true' : 'false' }}" aria-describedby="portfolio-start-date-error">
                        @error('start_date')<p id="portfolio-start-date-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="portfolio-end-date" class="mb-2 block text-sm font-semibold text-content">End date</label>
                        <input id="portfolio-end-date" class="bk-input" wire:model="end_date" type="date" aria-invalid="{{ $errors->has('end_date') ? 'true' : 'false' }}" aria-describedby="portfolio-end-date-error">
                        @error('end_date')<p id="portfolio-end-date-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="portfolio-description" class="mb-2 block text-sm font-semibold text-content">Project description *</label>
                    <textarea id="portfolio-description" class="bk-input min-h-36" wire:model="description" maxlength="5000" aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}" aria-describedby="portfolio-description-error"></textarea>
                    @error('description')<p id="portfolio-description-error" class="bk-field-error mt-1">{{ $message }}</p>@enderror
                </div>

                <fieldset class="rounded-lg border border-line p-4" aria-describedby="portfolio-visibility-error">
                    <legend class="px-1 text-sm font-semibold text-content">Visibility *</legend>
                    <div class="mt-2 grid gap-3 sm:grid-cols-2">
                        <label class="flex min-h-11 cursor-pointer items-start gap-3 rounded-lg border border-line p-3">
                            <input wire:model="visibility" type="radio" value="private" class="mt-1">
                            <span><span class="block font-semibold text-content">Private</span><span class="block text-sm text-content-muted">Only you can see this project.</span></span>
                        </label>
                        <label class="flex min-h-11 cursor-pointer items-start gap-3 rounded-lg border border-line p-3">
                            <input wire:model="visibility" type="radio" value="public" class="mt-1">
                            <span><span class="block font-semibold text-content">Publish</span><span class="block text-sm text-content-muted">Show it on your public showcase.</span></span>
                        </label>
                    </div>
                    @error('visibility')<p id="portfolio-visibility-error" class="bk-field-error mt-2">{{ $message }}</p>@enderror
                </fieldset>

                <div class="flex flex-wrap justify-end gap-3">
                    <button type="button" class="bk-button bk-button-secondary" wire:click="resetForm">Cancel</button>
                    <button type="submit" class="bk-button bk-button-primary" wire:loading.attr="disabled" wire:target="saveProject">
                        <span wire:loading.remove wire:target="saveProject">{{ $editingProjectId ? 'Save changes' : 'Create project' }}</span>
                        <span wire:loading wire:target="saveProject">Saving…</span>
                    </button>
                </div>
            </form>
        </x-ui.panel>
    @endif

    <x-ui.panel title="Your projects">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_12rem_12rem]">
            <div>
                <label for="portfolio-search" class="mb-2 block text-sm font-semibold text-content">Search projects</label>
                <input id="portfolio-search" class="bk-input" wire:model.live.debounce.300ms="searchTerm" type="search" maxlength="100">
            </div>
            <div>
                <label for="portfolio-filter" class="mb-2 block text-sm font-semibold text-content">Filter status</label>
                <select id="portfolio-filter" class="bk-input" wire:model.live="filterStatus">
                    <option value="">All statuses</option>
                    <option value="completed">Completed</option>
                    <option value="in-progress">In progress</option>
                    <option value="planning">Planning</option>
                    <option value="on-hold">On hold</option>
                </select>
            </div>
            <div>
                <label for="portfolio-sort" class="mb-2 block text-sm font-semibold text-content">Sort projects</label>
                <select id="portfolio-sort" class="bk-input" wire:model.live="sortBy">
                    <option value="sort_order">Showcase order</option>
                    <option value="created_at">Date created</option>
                    <option value="title">Title</option>
                    <option value="status">Status</option>
                </select>
            </div>
        </div>

        <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($portfolios as $project)
                <article wire:key="portfolio-project-{{ $project->id }}" class="flex h-full flex-col rounded-lg border border-line bg-surface p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <h3 class="text-lg font-bold text-content">{{ $project->title }}</h3>
                        <span class="rounded-full border border-line px-3 py-1 text-xs font-semibold text-content-muted">
                            {{ $project->visibility === 'public' ? 'Published' : 'Private' }}
                        </span>
                    </div>

                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-content-muted">{{ $project->description }}</p>
                    <p class="mt-3 text-xs font-semibold text-content">{{ $project->status_label }} · {{ $project->category_label }}</p>

                    @if($project->formatted_technologies)
                        <ul class="mt-4 flex flex-wrap gap-2" aria-label="Technologies and tools">
                            @foreach($project->formatted_technologies as $technology)
                                <li class="rounded-full bg-surface-subtle px-3 py-1 text-xs font-medium text-content">{{ $technology }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-auto flex flex-wrap gap-2 pt-5">
                        <button type="button" class="bk-button bk-button-secondary" wire:click="editProject({{ $project->id }})">Edit</button>
                        <button type="button" class="bk-button bk-button-secondary" wire:click="moveProject({{ $project->id }}, 'up')" aria-label="Move {{ $project->title }} earlier">Move up</button>
                        <button type="button" class="bk-button bk-button-secondary" wire:click="moveProject({{ $project->id }}, 'down')" aria-label="Move {{ $project->title }} later">Move down</button>
                        <button type="button" class="bk-button bk-button-secondary" wire:click="duplicateProject({{ $project->id }})">Duplicate privately</button>
                        <button type="button" class="bk-button bk-button-danger" wire:click="deleteProject({{ $project->id }})" wire:confirm="Delete this project?">Delete</button>
                        @if($project->visibility === 'public' && auth()->user()->profile_visibility === 'public')
                            <a class="bk-button bk-button-secondary" href="{{ route('portfolio.public', $project->slug) }}" target="_blank" rel="noopener">Public view</a>
                        @endif
                    </div>
                </article>
            @empty
                <x-ui.empty-state
                    class="md:col-span-2 xl:col-span-3"
                    title="No projects match this view"
                    description="Create a private project or clear the search and filters."
                >
                    <button type="button" class="bk-button bk-button-primary" wire:click="$set('showForm', true)">Create a project</button>
                </x-ui.empty-state>
            @endforelse
        </div>
    </x-ui.panel>
</div>
