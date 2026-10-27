<div class="mx-auto max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8" wire:poll.8s>
    <x-ui.page-header
        title="Course Content Studio"
        description="Import a bounded, attributed curriculum subset or generate original course material. Every result stays an editable draft until reviewed and approved." />

    @if($notice !== '')
        <div class="bk-feedback" role="status" aria-live="polite">{{ $notice }}</div>
    @endif
    @error('request')<p class="bk-field-error" role="alert">{{ $message }}</p>@enderror
    <x-ui.panel title="BootKode AI">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 space-y-2 text-sm text-content-muted">
                <p class="font-semibold text-content">{{ $aiProviderName }} · {{ $aiStatus['ready'] ? 'Configured' : 'Setup required' }}</p>
                <p>Generate course drafts, lesson explanations, quizzes and project briefs, or work through a coding problem with the mentor.</p>
                <p><a href="{{ route('ai.coding-mentor') }}" class="text-action underline">Open AI Coding Mentor</a></p>
                @if(auth()->user()->hasAnyRole(['super_admin', 'academy_admin']))
                    <p id="ai-connection-help">Check the live connection with a small test request. Course and learner data are excluded.</p>
                @endif
            </div>
            @if(auth()->user()->hasAnyRole(['super_admin', 'academy_admin']))
                <x-secondary-button type="button" wire:click="checkAiConnection" wire:loading.attr="disabled" wire:target="checkAiConnection" aria-describedby="ai-connection-help">
                    <span wire:loading.remove wire:target="checkAiConnection">Check AI connection</span>
                    <span wire:loading wire:target="checkAiConnection">Checking connection…</span>
                </x-secondary-button>
            @endif
        </div>
        <div role="status" aria-live="polite" aria-atomic="true" class="mt-3 text-sm text-content">
            @if($connectionCheck !== [])
                <p>{{ $connectionCheck['message'] }}</p>
                @if($connectionCheck['success'])<p class="mt-1 text-xs text-content-muted">Checked {{ $connectionCheck['checked_at'] }} · {{ $connectionCheck['duration_ms'] }} ms. This verifies the connection at that time; generated content still needs review.</p>@endif
            @endif
        </div>
    </x-ui.panel>
    <div role="status" aria-live="polite" class="sr-only">
        @if(session()->has('message')){{ session('message') }}@endif
    </div>

    <section class="grid items-start gap-6 lg:grid-cols-2" aria-label="Course content workflows">
        <x-ui.panel title="Import licensed curriculum">
            <p class="mb-4 text-sm text-content-muted">
                Supported source: freeCodeCamp's reviewed English Algorithms block. The importer retains the source revision and BSD 3-Clause attribution. It excludes tests, starter code, solutions and external media.
            </p>
            <p class="mb-4 text-sm"><a class="text-action underline" href="https://github.com/freeCodeCamp/freeCodeCamp/tree/main/curriculum/challenges/english/blocks" target="_blank" rel="noopener noreferrer">Browse other freeCodeCamp blocks externally</a></p>
            <form wire:submit="startImport" class="space-y-4">
                <div>
                    <x-input-label for="importPath" class="block font-semibold text-sm text-content">Curriculum block path</x-input-label>
                    <x-text-input id="importPath" wire:model="importPath" class="bk-input mt-1 w-full" aria-describedby="import-path-help {{ $errors->has('importPath') ? 'import-path-error' : '' }}" aria-invalid="{{ $errors->has('importPath') ? 'true' : 'false' }}" />
                    <p id="import-path-help" class="mt-1 text-xs text-content-muted">Example: curriculum/challenges/english/blocks/algorithms</p>
                    @error('importPath')<p id="import-path-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-input-label for="importCategoryId" class="block font-semibold text-sm text-content">Course category</x-input-label>
                    <select id="importCategoryId" wire:model="importCategoryId" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('importCategoryId') ? 'import-category-error' : '' }}" aria-invalid="{{ $errors->has('importCategoryId') ? 'true' : 'false' }}">
                        <option value="">Choose an existing category</option>
                        @foreach($categories as $category)<option value="{{ $category['id'] }}">{{ $category['name'] }}</option>@endforeach
                    </select>
                    @error('importCategoryId')<p id="import-category-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-input-label for="importLimit" class="block font-semibold text-sm text-content">Maximum lessons (1–12)</x-input-label>
                    <x-text-input id="importLimit" type="number" min="1" max="12" wire:model="importLimit" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('importLimit') ? 'import-limit-error' : '' }}" aria-invalid="{{ $errors->has('importLimit') ? 'true' : 'false' }}" />
                    @error('importLimit')<p id="import-limit-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <x-secondary-button type="button" class="w-full" wire:click="previewImport" wire:loading.attr="disabled" wire:target="previewImport">
                    <span wire:loading.remove wire:target="previewImport">Inspect source and licence</span>
                    <span wire:loading wire:target="previewImport">Inspecting source…</span>
                </x-secondary-button>
                @error('source')<p class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                @if($importPreview !== [])
                    <div class="rounded-md border border-line bg-surface-subtle p-3 text-sm" role="status">
                        <p class="font-semibold text-content">{{ $importPreview['original_title'] }} · {{ count($importPreview['lessons']) }} lessons</p>
                        <p class="mt-1 text-content-muted">Publisher: {{ $importPreview['original_author'] }} · Licence: {{ $importPreview['license_id'] }} · Revision: {{ substr($importPreview['revision'], 0, 12) }}</p>
                        <p class="mt-1 break-all text-content-muted">Licence text: {{ $importPreview['license_url'] }}</p>
                        <p class="mt-1 break-words text-content-muted">Attribution: {{ $importPreview['attribution'] }}</p>
                        <p class="mt-1 text-content-muted">Content type: text-only challenge descriptions; tests, seed code, solutions and media are excluded.</p>
                        <ul class="mt-2 list-inside list-disc text-content">
                            @foreach($importPreview['lessons'] as $lesson)
                                <li class="py-1"><span class="font-medium">{{ $lesson['title'] }}</span><p class="text-xs text-content-muted">{{ $lesson['preview'] }}</p></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <label class="flex items-start gap-3 text-sm text-content">
                    <input type="checkbox" wire:model="createSourceRevision" class="mt-1">
                    <span>Create a separate draft if this source block has changed since its last import.</span>
                </label>
                @error('import')<p class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                <x-primary-button type="submit" class="w-full" wire:loading.attr="disabled" wire:target="startImport">
                    <span wire:loading.remove wire:target="startImport">Queue licensed import</span>
                    <span wire:loading wire:target="startImport">Queueing import…</span>
                </x-primary-button>
            </form>
        </x-ui.panel>

        <x-ui.panel title="Generate a course with AI">
            @unless($aiStatus['ready'])
                <p class="mb-4 rounded-md border border-warning/40 bg-warning/10 p-3 text-sm text-content" role="status">
                    AI generation is unavailable until the provider is enabled and configured on the server.
                </p>
            @endunless
            <form wire:submit="startGeneration" class="space-y-4">
                <div>
                    <x-input-label for="generationTopic" class="block font-semibold text-sm text-content">Topic or working title</x-input-label>
                    <x-text-input id="generationTopic" wire:model="generationTopic" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('generationTopic') ? 'generation-topic-error' : '' }}" aria-invalid="{{ $errors->has('generationTopic') ? 'true' : 'false' }}" />
                    @error('generationTopic')<p id="generation-topic-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-input-label for="generationCategoryId" class="block font-semibold text-sm text-content">Skill category</x-input-label>
                    <select id="generationCategoryId" wire:model="generationCategoryId" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('generationCategoryId') ? 'generation-category-error' : '' }}" aria-invalid="{{ $errors->has('generationCategoryId') ? 'true' : 'false' }}">
                        <option value="">Choose an existing category</option>
                        @foreach($categories as $category)<option value="{{ $category['id'] }}">{{ $category['name'] }}</option>@endforeach
                    </select>
                    @error('generationCategoryId')<p id="generation-category-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-input-label for="targetAudience" class="block font-semibold text-sm text-content">Target audience</x-input-label>
                    <x-text-input id="targetAudience" wire:model="targetAudience" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('targetAudience') ? 'target-audience-error' : '' }}" aria-invalid="{{ $errors->has('targetAudience') ? 'true' : 'false' }}" />
                    @error('targetAudience')<p id="target-audience-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="skillLevel" class="block font-semibold text-sm text-content">Skill level</x-input-label>
                        <select id="skillLevel" wire:model="skillLevel" class="bk-input mt-1 w-full">
                            <option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="language" class="block font-semibold text-sm text-content">Course language</x-input-label>
                        <x-text-input id="language" wire:model="language" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('language') ? 'language-error' : '' }}" aria-invalid="{{ $errors->has('language') ? 'true' : 'false' }}" />
                        @error('language')<p id="language-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="durationMinutes" class="block font-semibold text-sm text-content">Duration in minutes</x-input-label>
                        <x-text-input id="durationMinutes" type="number" min="30" max="2400" wire:model="durationMinutes" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('durationMinutes') ? 'duration-error' : '' }}" aria-invalid="{{ $errors->has('durationMinutes') ? 'true' : 'false' }}" />
                        @error('durationMinutes')<p id="duration-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="moduleCount" class="block font-semibold text-sm text-content">Modules (2–5)</x-input-label>
                        <x-text-input id="moduleCount" type="number" min="2" max="5" wire:model="moduleCount" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('moduleCount') ? 'module-count-error' : '' }}" aria-invalid="{{ $errors->has('moduleCount') ? 'true' : 'false' }}" />
                        @error('moduleCount')<p id="module-count-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="lessonsPerModule" class="block font-semibold text-sm text-content">Lessons per module (2–4)</x-input-label>
                        <x-text-input id="lessonsPerModule" type="number" min="2" max="4" wire:model="lessonsPerModule" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('lessonsPerModule') ? 'lessons-per-module-error' : '' }}" aria-invalid="{{ $errors->has('lessonsPerModule') ? 'true' : 'false' }}" />
                        @error('lessonsPerModule')<p id="lessons-per-module-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                </div>
                <fieldset>
                    <legend class="block font-semibold text-sm text-content">Learning objectives (at least two)</legend>
                    @foreach($learningObjectives as $index => $objective)
                        <div class="mt-2 flex gap-2" wire:key="course-objective-{{ $index }}">
                            <textarea id="learningObjective{{ $index }}" wire:model="learningObjectives.{{ $index }}" class="bk-input mt-1 w-full" rows="2" aria-label="Learning objective {{ $index + 1 }}" aria-describedby="{{ $errors->has('learningObjectives.'.$index) ? 'objective-error-'.$index : '' }}" aria-invalid="{{ $errors->has('learningObjectives.'.$index) ? 'true' : 'false' }}"></textarea>
                            @if(count($learningObjectives) > 2)
                                <x-secondary-button type="button"  wire:click="removeObjective({{ $index }})" aria-label="Remove learning objective {{ $index + 1 }}">Remove</x-secondary-button>
                            @endif
                        </div>
                    @endforeach
                    @foreach($learningObjectives as $index => $objective)
                        @error('learningObjectives.'.$index)<p id="objective-error-{{ $index }}" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                    @endforeach
                    @if(count($learningObjectives) < 8)
                        <x-secondary-button type="button" class="mt-2" wire:click="addObjective">Add objective</x-secondary-button>
                    @endif
                    @error('learningObjectives')<p class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                    @error('learningObjectives.*')<p class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </fieldset>
                <div>
                    <x-input-label for="teachingStyle" class="block font-semibold text-sm text-content">Preferred teaching style</x-input-label>
                    <x-text-input id="teachingStyle" wire:model="teachingStyle" class="bk-input mt-1 w-full" aria-describedby="{{ $errors->has('teachingStyle') ? 'teaching-style-error' : '' }}" aria-invalid="{{ $errors->has('teachingStyle') ? 'true' : 'false' }}" />
                    @error('teachingStyle')<p id="teaching-style-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-input-label for="practicalRequirements" class="block font-semibold text-sm text-content">Practical project or assignment requirements (optional)</x-input-label>
                    <textarea id="practicalRequirements" wire:model="practicalRequirements" class="bk-input mt-1 w-full" rows="3" aria-describedby="{{ $errors->has('practicalRequirements') ? 'practical-requirements-error' : '' }}" aria-invalid="{{ $errors->has('practicalRequirements') ? 'true' : 'false' }}"></textarea>
                    @error('practicalRequirements')<p id="practical-requirements-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-input-label for="assessmentRequirements" class="block font-semibold text-sm text-content">Assessment requirements (optional)</x-input-label>
                    <textarea id="assessmentRequirements" wire:model="assessmentRequirements" class="bk-input mt-1 w-full" rows="2" aria-describedby="{{ $errors->has('assessmentRequirements') ? 'assessment-requirements-error' : '' }}" aria-invalid="{{ $errors->has('assessmentRequirements') ? 'true' : 'false' }}"></textarea>
                    @error('assessmentRequirements')<p id="assessment-requirements-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center gap-3 text-sm text-content">
                    <input type="checkbox" wire:model="includeAssessments"> Generate a module quiz for each module
                </label>
                <div>
                    <x-input-label for="additionalInstructions" class="block font-semibold text-sm text-content">Additional instructions (optional)</x-input-label>
                    <textarea id="additionalInstructions" wire:model="additionalInstructions" class="bk-input mt-1 w-full" rows="3" aria-describedby="{{ $errors->has('additionalInstructions') ? 'additional-instructions-error' : '' }}" aria-invalid="{{ $errors->has('additionalInstructions') ? 'true' : 'false' }}"></textarea>
                    @error('additionalInstructions')<p id="additional-instructions-error" class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                @error('generation')<p class="bk-field-error" role="alert">{{ $message }}</p>@enderror
                <x-primary-button type="submit" class="w-full" :disabled="!$aiStatus['ready']" wire:loading.attr="disabled" wire:target="startGeneration">
                    <span wire:loading.remove wire:target="startGeneration">Queue AI generation</span>
                    <span wire:loading wire:target="startGeneration">Queueing generation…</span>
                </x-primary-button>
                <p class="text-xs text-content-muted">Content is generated in bounded stages, usage-limited, and flagged for human verification. It is not reviewed or ready to publish automatically.</p>
            </form>
        </x-ui.panel>
    </section>

    @error('expansion')<p class="bk-field-error" role="alert">{{ $message }}</p>@enderror
    <x-ui.panel title="Recent content runs">
        @if($runs->isEmpty())
            <x-ui.empty-state title="No content runs yet" description="Queued imports and generations will appear here with their review status." />
        @else
            <div class="max-h-[32rem] overflow-auto rounded-lg border border-line" role="region" aria-label="Recent course content runs" tabindex="0">
                <table class="min-w-full divide-y divide-line text-left text-sm">
                    <thead class="sticky top-0 bg-surface-subtle text-content-muted">
                        <tr><th scope="col" class="px-3 py-3">Course / request</th><th scope="col" class="px-3 py-3">Source</th><th scope="col" class="px-3 py-3">Status</th><th scope="col" class="px-3 py-3">Updated</th><th scope="col" class="px-3 py-3">Action</th></tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach($runs as $run)
                            <tr>
                                <td class="px-3 py-3">
                                    <span class="font-medium text-content">{{ $run->course?->title ?? ($run->metadata['topic'] ?? $run->metadata['original_title'] ?? 'Course request') }}</span>
                                    <p class="mt-1 text-xs text-content-muted">Category: {{ $run->course?->category?->name ?? collect($categories)->firstWhere('id', $run->metadata['category_id'] ?? null)['name'] ?? 'Unavailable category' }}</p>
                                    @if($run->failure_reason)<p class="mt-1 max-w-md text-xs text-danger" role="status">{{ $run->failure_reason }}</p>@endif
                                    @if(isset($run->metadata['lesson_count']))
                                        <p class="mt-1 text-xs text-content-muted">{{ $run->metadata['module_count'] ?? $run->metadata['section_count'] ?? 0 }} modules · {{ $run->metadata['lesson_count'] }} lessons · {{ $run->metadata['assessment_count'] ?? 0 }} assessments</p>
                                    @endif
                                    @if($run->metadata['review_flags'] ?? false)
                                        <p class="mt-1 text-xs text-content-muted">Review: {{ implode(' ', $run->metadata['review_flags']) }}</p>
                                    @endif
                                    @if($run->course || !empty($run->metadata['validation_issues']))
                                        <details class="mt-2 max-w-lg text-xs">
                                            <summary class="cursor-pointer font-semibold text-action">Validation and quality review</summary>
                                            @if($run->course)
                                                <p class="mt-2">Quality status: {{ \Illuminate\Support\Str::headline($run->course->quality_status ?? 'not_checked') }}</p>
                                                @unless($run->course->quality_last_checked_at)<p class="mt-1 text-content-muted">Run quality checks to record the current draft's issues.</p>@endunless
                                                <ul class="mt-1 list-inside list-disc">
                                                    @foreach($run->course->quality_issues ?? [] as $issue)<li>{{ $issue['message'] }}</li>@endforeach
                                                </ul>
                                            @endif
                                            @foreach($run->metadata['validation_issues'] ?? [] as $field => $issues)
                                                <p class="mt-1">{{ $field }}: {{ implode(' ', $issues) }}</p>
                                            @endforeach
                                        </details>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-content-muted">{{ match($run->source_type) { 'ai' => 'BootKode AI', 'ai_expansion' => 'AI draft expansion', default => $run->metadata['source'] ?? 'Licensed repository' } }}</td>
                                <td class="px-3 py-3">
                                    <span role="status" class="inline-flex rounded-full border border-line px-2 py-1 text-xs">{{ ucfirst($run->status) }}</span>
                                    @if($run->status === 'processing' && $run->source_type === 'ai')
                                        <span class="block text-xs text-content-muted">{{ count($run->stage_data['modules'] ?? []) }} of {{ $run->metadata['requested_modules'] ?? 0 }} modules completed</span>
                                    @endif
                                    @if($run->status === 'processing' && $run->source_type === 'ai_expansion')
                                        <span class="block text-xs text-content-muted">{{ count($run->stage_data['generated'] ?? []) }} of {{ count($run->stage_data['groups'] ?? []) }} sections completed</span>
                                    @endif
                                    @if($run->metadata['license_id'] ?? null)
                                        <span class="block text-xs text-content-muted">{{ $run->metadata['license_id'] }} · attribution retained</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-content-muted">{{ $run->updated_at->diffForHumans() }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex min-w-40 flex-col items-start gap-2">
                                        @if($run->course)
                                            <a class="text-action underline" href="{{ route('course-builder', ['course' => $run->course_id]) }}">Open draft editor</a>
                                            <x-secondary-button wire:click="reviewRun({{ $run->id }})" wire:loading.attr="disabled">Run quality checks</x-secondary-button>
                                            @if($run->source_type === 'repository' && in_array($run->status, ['completed', 'partial']) && !$run->course->is_published && !$run->course->is_approved)
                                                <x-secondary-button wire:click="startExpansion({{ $run->course_id }})" wire:confirm="Expand this draft with AI explanations and quizzes? Original text and attribution are retained. Existing learner history prevents this action." wire:loading.attr="disabled">Expand imported draft</x-secondary-button>
                                            @endif
                                        @endif
                                        @if($run->status === 'failed' && in_array($run->source_type, ['repository', 'ai', 'ai_expansion']))
                                            <x-secondary-button wire:click="retryRun({{ $run->id }})" wire:loading.attr="disabled">Retry saved stages</x-secondary-button>
                                        @elseif($run->status === 'conflict')
                                            <span class="max-w-xs text-xs text-content-muted">Review the recorded conflict. Existing edits have been preserved.</span>
                                        @elseif(!$run->course)
                                            <span class="text-content-muted">Waiting for worker</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.panel>
</div>
