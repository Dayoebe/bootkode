<x-app-layout>
    @section('title', 'Learner project showcase')
    @section('description', 'Projects a BootKode learner has explicitly chosen to publish.')

    <div class="mx-auto max-w-6xl space-y-8">
        <x-ui.page-header
            :title="$owner['name'].' — Project showcase'"
            eyebrow="Learner portfolio"
            description="Projects this learner has explicitly chosen to publish."
        />

        @if($owner['bio'] || $owner['occupation'])
            <x-ui.panel title="About the learner">
                @if($owner['occupation'])
                    <p class="font-semibold text-content">{{ $owner['occupation'] }}</p>
                @endif
                @if($owner['bio'])
                    <p class="mt-2 whitespace-pre-line text-content-muted">{{ $owner['bio'] }}</p>
                @endif
            </x-ui.panel>
        @endif

        <section aria-labelledby="published-projects-heading">
            <div class="mb-4">
                <h2 id="published-projects-heading" class="font-display text-xl font-bold text-content sm:text-2xl">Published projects</h2>
                <p class="mt-1 text-sm text-content-muted">Private projects and private account details are never included.</p>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                @foreach($projects as $project)
                    <article class="bk-panel flex h-full flex-col p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <h3 class="text-lg font-bold text-content">{{ $project->title }}</h3>
                            <span class="rounded-full border border-line px-3 py-1 text-xs font-semibold text-content-muted">
                                {{ $project->status_label }}
                            </span>
                        </div>

                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-content-muted">{{ $project->description }}</p>

                        @if($project->formatted_technologies)
                            <ul class="mt-4 flex flex-wrap gap-2" aria-label="Technologies and tools">
                                @foreach($project->formatted_technologies as $technology)
                                    <li class="rounded-full bg-surface-subtle px-3 py-1 text-xs font-medium text-content">{{ $technology }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($project->safe_demo_url || $project->safe_source_code_url)
                            <div class="mt-auto flex flex-wrap gap-2 pt-5">
                                @if($project->safe_demo_url)
                                    <a class="bk-button bk-button-primary" href="{{ $project->safe_demo_url }}" target="_blank" rel="noopener noreferrer nofollow">Open demo</a>
                                @endif
                                @if($project->safe_source_code_url)
                                    <a class="bk-button bk-button-secondary" href="{{ $project->safe_source_code_url }}" target="_blank" rel="noopener noreferrer nofollow">View source code</a>
                                @endif
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
