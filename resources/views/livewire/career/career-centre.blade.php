<div class="space-y-6">
    <x-ui.page-header title="Career Centre" description="Connect reviewed evidence to real opportunities. Employers make their own hiring decisions.">
        <x-slot:actions><a href="{{ route('user.job') }}" class="bk-button bk-button-secondary">Job portal</a><a href="{{ route('search.job') }}" class="bk-button bk-button-secondary">External vacancies</a></x-slot:actions>
    </x-ui.page-header>
    <p role="status" aria-live="polite" class="text-content-secondary">{{ $notice }}</p>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.panel title="Current reviewed evidence"><p class="text-2xl font-bold text-content">{{ $verifiedCount }}</p><p class="mt-2 text-sm text-content-secondary">Evidence items, not an employability score.</p></x-ui.panel>
        <x-ui.panel title="Prepare your application"><div class="flex flex-wrap gap-2"><a class="bk-button bk-button-secondary" href="{{ route('resume.builder') }}">My CV</a><a class="bk-button bk-button-secondary" href="{{ route('portfolio.show') }}">Portfolio</a><a class="bk-button bk-button-secondary" href="{{ route('user.interview') }}">Interview practice</a></div></x-ui.panel>
        <x-ui.panel title="Career guidance"><a class="bk-button bk-button-secondary" href="{{ route('mentorship.my-mentorships') }}">My mentorships</a><p class="mt-2 text-sm text-content-secondary">Mentorship uses the existing booking workflow. Designated career counselling is not yet available.</p></x-ui.panel>
    </div>

    <x-ui.panel title="Explore opportunities">
        <p class="mb-4 text-sm text-content-secondary">Browse public vacancies even when your evidence does not yet meet their requirements. Closed vacancies are labelled.</p>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach(['search' => 'Job title', 'location' => 'Location', 'industry' => 'Industry'] as $field => $label)
                <div><x-input-label :for="'career-'.$field" :value="$label" /><x-text-input :id="'career-'.$field" wire:model.live.debounce.400ms="{{ $field }}" class="mt-1 w-full" maxlength="150" :aria-describedby="'career-'.$field.'-error'" /><x-input-error :id="'career-'.$field.'-error'" :messages="$errors->get($field)" /></div>
            @endforeach
            <div><x-input-label for="career-work-type" value="Work arrangement" /><select id="career-work-type" class="bk-input mt-1 w-full" wire:model.live="workType"><option value="">All arrangements</option>@foreach(['remote' => 'Remote', 'hybrid' => 'Hybrid', 'on-site' => 'On-site'] as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
            <div><x-input-label for="career-employment-type" value="Employment type" /><select id="career-employment-type" class="bk-input mt-1 w-full" wire:model.live="employmentType"><option value="">All employment types</option>@foreach(['full-time', 'part-time', 'contract', 'temporary', 'internship', 'freelance'] as $value)<option value="{{ $value }}">{{ ucfirst($value) }}</option>@endforeach</select></div>
        </div>
        <div class="mt-5 grid gap-3 md:grid-cols-2">
            @forelse($jobs as $vacancy)
                <article wire:key="career-job-{{ $vacancy->id }}" class="min-w-0 rounded-lg border border-themed-primary p-4">
                    <h3 class="break-words font-bold text-content">{{ $vacancy->title }}</h3><p class="mt-1 text-sm text-content-secondary">{{ $vacancy->company_name }} · {{ $vacancy->location }} · {{ $vacancy->work_type_label }}</p>
                    <p class="mt-2 text-sm text-content-secondary">{{ !$vacancy->isActive() || $vacancy->application_deadline?->lte(now()) ? 'Applications closed' : 'Applications open' }}</p>
                    <x-secondary-button class="mt-3" wire:click="selectJob({{ $vacancy->id }})">Review opportunity<span class="sr-only">: {{ $vacancy->title }}</span></x-secondary-button>
                </article>
            @empty<p class="text-content-secondary">No public vacancies match these filters.</p>@endforelse
        </div>
        <div class="mt-4">{{ $jobs->links() }}</div>
    </x-ui.panel>

    @if($job && $eligibility)
        <x-ui.panel id="career-opportunity" tabindex="-1" :title="$job->title">
            <p class="text-content-secondary">{{ $job->company_name }} · {{ $job->location }} · {{ $job->employment_type_label }}</p>
            <p class="mt-3 whitespace-pre-line break-words text-content-secondary">{{ $job->description }}</p>
            <h3 class="mt-5 text-lg font-bold text-content">{{ $eligibility['label'] }}</h3><p class="mt-2 text-content-secondary">{{ $eligibility['explanation'] }}</p>
            @if($eligibility['reviewed'])<p class="mt-2 text-sm text-content-secondary">{{ $eligibility['mandatory_met'] }} of {{ $eligibility['mandatory_total'] }} mandatory requirements supported. Preferred requirements do not block applications.</p>@endif
            <ul class="mt-4 space-y-3">
                @foreach($eligibility['requirements'] as $requirement)
                    <li class="rounded-lg border border-themed-primary p-3"><p class="font-semibold text-content">{{ $requirement['label'] }} — {{ $requirement['mandatory'] ? 'Mandatory' : 'Preferred' }} · {{ $requirement['met'] ? 'Supported' : 'Evidence needed' }}</p><p class="mt-1 text-sm text-content-secondary">{{ $requirement['reason'] }}</p>@if($requirement['minimum_years'] !== null)<p class="mt-1 text-sm text-content-secondary">Minimum experience: {{ $requirement['minimum_years'] }} years.</p>@endif<details class="mt-2 text-sm text-content-secondary"><summary>Vacancy evidence</summary><p class="mt-2 whitespace-pre-line break-words">{{ $requirement['excerpt'] }}</p></details></li>
                @endforeach
            </ul>
            <div class="mt-5 flex flex-wrap gap-3">
                @php($existingApplication = $applications->firstWhere('job_id', $job->id))
                @if($existingApplication)
                    <p class="font-semibold text-content">Recorded application: {{ $existingApplication->status_label }}</p>
                @elseif($eligibility['can_apply'])
                    @if($job->application_method === 'internal')<a href="{{ route('user.job', ['apply' => $job->id]) }}" class="bk-button bk-button-primary">Prepare application</a>
                    @elseif($job->application_destination)<a href="{{ $job->application_destination }}" class="bk-button bk-button-primary" @if($job->application_method === 'external_link') target="_blank" rel="noopener noreferrer" @endif>Continue to application destination</a><p class="text-sm text-content-secondary">Opening an external destination does not confirm a submitted application.</p>
                    @else<p class="text-content-secondary">Application details are unavailable.</p>@endif
                @else<a href="#career-evidence" class="bk-button bk-button-secondary">Review my evidence</a>@endif
            </div>
            @if($learningOptions)
                <h3 class="mt-6 text-lg font-bold text-content">Learning options for your gaps</h3><p class="mt-2 text-sm text-content-secondary">These published courses explicitly mention the skill. Review their scope and prerequisites; course completion alone does not establish job eligibility.</p>
                @foreach($learningOptions as $option)
                    <div class="mt-4 border-t border-themed-primary pt-4"><h4 class="font-bold text-content">{{ $option['skill'] }}</h4>
                        @forelse($option['courses'] as $course)<p class="mt-2"><a class="font-semibold text-brand underline" href="{{ $course['url'] }}">{{ $course['title'] }}</a><span class="ml-2 text-sm text-content-secondary">{{ ucfirst($course['difficulty'] ?? '') }}</span></p>@if($course['prerequisites'])<p class="mt-1 text-sm text-content-secondary">Prerequisites: {{ implode('; ', $course['prerequisites']) }}</p>@endif
                        @empty<p class="mt-2 text-sm text-content-secondary">No suitable published course was found for this skill. An automatic course request or generated pathway is not yet available here.</p>@endforelse
                    </div>
                @endforeach
            @endif
        </x-ui.panel>
    @endif

    <x-ui.panel id="career-evidence" title="Your evidence and review requests">
        <p class="mb-4 text-sm text-content-secondary">Self-reported skills and uploaded CV text do not establish eligibility. Submit a specific source for human review. A certificate supports its named credential, not every competency in a field.</p>
        <div class="space-y-3">
            @forelse($evidence as $item)
                <article wire:key="evidence-{{ $item->id }}" class="rounded-lg border border-themed-primary p-3"><h3 class="break-words font-bold text-content">{{ $item->label }}</h3><p class="mt-1 text-sm text-content-secondary">{{ $kinds[$item->kind] }} · {{ ucfirst($item->review_status) }}{{ $item->review_status === 'verified' && !app(\App\Services\Career\CompetencyEvidenceService::class)->usable($item) ? ' — expired or source no longer valid' : '' }}</p>@if($item->review_reason)<p class="mt-2 break-words text-sm text-content-secondary">{{ $item->review_reason }}</p>@endif
                    @if($item->review_status !== 'pending')<x-secondary-button class="mt-3" wire:click="requestReassessment({{ $item->id }})" wire:confirm="Request reassessment? This evidence will become pending and stop supporting eligibility until reviewed again.">Request reassessment</x-secondary-button>@endif
                </article>
            @empty<p class="text-sm text-content-secondary">No evidence requests recorded yet.</p>@endforelse
        </div>
        <form wire:submit="submitEvidence" class="mt-6 space-y-4" novalidate>
            <h3 class="font-bold text-content">Submit evidence for review</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div><x-input-label for="evidence-kind" value="Requirement type" /><select id="evidence-kind" wire:model.live="claim.kind" class="bk-input mt-1 w-full" aria-describedby="evidence-kind-error">@foreach($kinds as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select><x-input-error id="evidence-kind-error" :messages="$errors->get('claim.kind')" /></div>
                <div><x-input-label for="evidence-label" value="Skill or requirement name" /><x-text-input id="evidence-label" wire:model="claim.label" class="mt-1 w-full" maxlength="120" required aria-describedby="evidence-label-help evidence-label-error" /><p id="evidence-label-help" class="mt-1 text-sm text-content-secondary">Use the name in the vacancy. For a BootKode certificate, use its exact course title.</p><x-input-error id="evidence-label-error" :messages="$errors->get('claim.label')" /></div>
                <div><x-input-label for="evidence-source" value="Evidence source" /><select id="evidence-source" wire:model.live="claim.source_type" class="bk-input mt-1 w-full"><option value="external">External qualification, experience or other evidence</option><option value="assessment">A finalized passing assessment</option><option value="certificate">An approved BootKode certificate</option><option value="portfolio">A completed portfolio project</option></select></div>
                @if($claim['source_type'] !== 'external')
                    <div><x-input-label for="evidence-source-id" value="Your saved source" /><select id="evidence-source-id" wire:model="claim.source_id" class="bk-input mt-1 w-full" aria-describedby="evidence-source-id-error"><option value="">Select a source</option>
                        @if($claim['source_type'] === 'assessment')@foreach($assessmentSources as $source)<option value="{{ $source->id }}">Assessment attempt {{ $source->id }} · {{ $source->ended_at->format('M j, Y') }} · {{ ($source->metadata['results']['passed'] ?? false) && !($source->metadata['results']['pending_review'] ?? true) ? 'Passed' : 'Not a finalized pass' }}</option>@endforeach
                        @elseif($claim['source_type'] === 'certificate')@foreach($certificateSources as $source)<option value="{{ $source->id }}">{{ $source->course?->title }}</option>@endforeach
                        @else @foreach($portfolioSources as $source)<option value="{{ $source->id }}">{{ $source->title }}</option>@endforeach @endif
                    </select><x-input-error id="evidence-source-id-error" :messages="$errors->get('claim.source_id')" /></div>
                @endif
                @if($claim['kind'] === 'experience')<div><x-input-label for="evidence-years" value="Years of relevant experience" /><x-text-input id="evidence-years" wire:model="claim.years_experience" type="number" min="0" max="80" step="0.1" class="mt-1 w-full" aria-describedby="evidence-years-error" /><x-input-error id="evidence-years-error" :messages="$errors->get('claim.years_experience')" /></div>@endif
            </div>
            <div><x-input-label for="evidence-reference" value="Source details for the reviewer" /><textarea id="evidence-reference" wire:model="claim.reference" class="bk-input mt-1 w-full" rows="3" minlength="10" maxlength="2000" aria-describedby="evidence-reference-help evidence-reference-error" required></textarea><p id="evidence-reference-help" class="mt-1 text-sm text-content-secondary">Explain what the source demonstrates and how it can be checked. Include an issuer or reference when relevant. Do not include passwords, identity documents or confidential client information. Only you and platform reviewers see this request.</p><x-input-error id="evidence-reference-error" :messages="$errors->get('claim.reference')" /></div>
            <x-primary-button wire:loading.attr="disabled" wire:target="submitEvidence">Request evidence review</x-primary-button>
        </form>
    </x-ui.panel>

    <x-ui.panel title="AI career guidance">
        <p class="text-sm text-content-secondary">{{ $aiConfigured ? 'The configured AI provider can offer suggestions.' : 'AI setup is required; evidence review and vacancy browsing remain available.' }} Guidance is not verified experience, an eligibility decision or a promise of work.</p>
        <form wire:submit="askAdvisor" class="mt-4 space-y-4" novalidate>
            <div><x-input-label for="career-question" value="Your career question" /><textarea id="career-question" wire:model="question" class="bk-input mt-1 w-full" rows="3" maxlength="2000" aria-describedby="career-question-error" required></textarea><x-input-error id="career-question-error" :messages="$errors->get('question')" /></div>
            <label class="flex min-h-11 items-start gap-3 text-sm text-content-secondary"><input type="checkbox" wire:model="shareContext" class="mt-1 rounded text-action focus:ring-action" aria-describedby="career-context-error" /><span>For this request, share my learning goal, self-reported starting level/skills, reviewed evidence labels, approved course names, portfolio titles and internal application count with the configured AI provider. Private evidence references, CV files, assessment answers and counselling records are excluded.</span></label><x-input-error id="career-context-error" :messages="$errors->get('shareContext')" />
            <x-primary-button :disabled="!$aiConfigured" wire:loading.attr="disabled" wire:target="askAdvisor">Ask career advisor</x-primary-button><p wire:loading wire:target="askAdvisor" role="status" class="text-sm text-content-secondary">Preparing guidance from your permitted context…</p>
        </form>
        @if($aiError)<p role="alert" class="mt-4 text-content-secondary">{{ $aiError }}</p>@endif
        @if($advice)<div role="status" class="mt-5 whitespace-pre-wrap break-words border-t border-themed-primary pt-4 text-content-secondary">{{ $advice }}</div>@endif
    </x-ui.panel>

    <x-ui.panel title="Recorded internal applications">
        @forelse($applications as $application)<p class="border-t border-themed-primary py-3 text-content-secondary">{{ $application->job?->title ?? 'Historical vacancy' }} · {{ $application->status_label }} · {{ $application->created_at->format('M j, Y') }}</p>@empty<p class="text-content-secondary">No internal applications recorded. Visits to provider sites are not counted as submitted applications.</p>@endforelse
    </x-ui.panel>
</div>
