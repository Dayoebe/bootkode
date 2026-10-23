<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-lg border border-themed-primary bg-themed-secondary p-6 shadow-sm transition-colors duration-300">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-brand">Mentorship workflow</p>
                <h{{ $embedded ? 2 : 1 }} class="text-2xl font-bold text-themed-primary">Code reviews</h{{ $embedded ? 2 : 1 }}>
                <p class="mt-1 max-w-3xl text-sm text-themed-secondary">
                    Submit repositories or snippets, review against a rubric, track revisions, approve work, and keep certificate or project evidence attached.
                </p>
            </div>
            <button type="button" wire:click="createReview"
                @if($activeMentorships->isEmpty()) disabled @endif
                class="inline-flex items-center justify-center gap-2 rounded-lg bk-button bk-button-primary px-4 py-3 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50">
                <i aria-hidden="true" class="fas fa-plus"></i>
                Request Review
            </button>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded-lg border border-themed-primary bg-themed-tertiary/40 p-4">
                <p class="text-xs font-medium uppercase text-themed-secondary">Total</p>
                <p class="mt-1 text-2xl font-bold text-themed-primary">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                <p class="text-xs font-medium uppercase text-amber-700 dark:text-amber-300">Pending</p>
                <p class="mt-1 text-2xl font-bold text-amber-800 dark:text-amber-100">{{ $stats['pending'] }}</p>
            </div>
            <div class="rounded-lg border border-brand bg-brand/10 p-4 dark:border-brand dark:bg-action/20">
                <p class="text-xs font-medium uppercase text-brand dark:text-brand">In Review</p>
                <p class="mt-1 text-2xl font-bold text-brand dark:text-brand">{{ $stats['in_review'] }}</p>
            </div>
            <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-800 dark:bg-orange-900/20">
                <p class="text-xs font-medium uppercase text-orange-700 dark:text-orange-300">Revisions</p>
                <p class="mt-1 text-2xl font-bold text-orange-800 dark:text-orange-100">{{ $stats['revision_requested'] }}</p>
            </div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                <p class="text-xs font-medium uppercase text-emerald-700 dark:text-emerald-300">Approved</p>
                <p class="mt-1 text-2xl font-bold text-emerald-800 dark:text-emerald-100">{{ $stats['approved'] }}</p>
            </div>
            <div class="rounded-lg border border-themed-primary bg-themed-tertiary/40 p-4">
                <p class="text-xs font-medium uppercase text-themed-secondary">Avg Score</p>
                <p class="mt-1 text-2xl font-bold text-themed-primary">{{ $stats['average_score'] ?: 0 }}%</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(420px,0.95fr)]">
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-5 shadow-sm transition-colors duration-300">
            <div class="flex flex-col gap-3">
                <h3 class="text-lg font-semibold text-themed-primary">Review Queue</h3>
                <div class="grid gap-2 sm:grid-cols-3 lg:w-full lg:max-w-[680px]">
                    <input aria-label="Search title, repo, learner" type="search" wire:model.live.debounce.300ms="search"
                        class="rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary placeholder-themed-tertiary focus:border-brand focus:ring-brand"
                        placeholder="Search title, repo, learner">
                    <select aria-label="Status filter" wire:model.live="statusFilter"
                        class="rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary focus:border-brand focus:ring-brand">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_review">In review</option>
                        <option value="revision_requested">Revision requested</option>
                        <option value="completed">Completed</option>
                        <option value="declined">Declined</option>
                    </select>
                    <select aria-label="Priority filter" wire:model.live="priorityFilter"
                        class="rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary focus:border-brand focus:ring-brand">
                        <option value="">All priorities</option>
                        <option value="urgent">Urgent</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($reviews as $review)
                    @php
                        $statusMeta = match ($review->status) {
                            'pending' => ['Pending', 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200'],
                            'in_review' => ['In review', 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-800 dark:bg-sky-900/20 dark:text-sky-200'],
                            'revision_requested' => ['Revision requested', 'border-orange-200 bg-orange-50 text-orange-800 dark:border-orange-800 dark:bg-orange-900/20 dark:text-orange-200'],
                            'completed' => ['Completed', 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200'],
                            default => [ucfirst(str_replace('_', ' ', $review->status)), 'border-themed-primary bg-themed-tertiary text-themed-secondary'],
                        };
                        $isSelected = $selectedReview?->id === $review->id;
                        $canStart = (auth()->user()->isSuperAdmin() || auth()->user()->isAcademyAdmin() || auth()->id() === $review->mentorship?->mentor_id) && $review->status === 'pending';
                    @endphp

                    <div class="rounded-lg border p-4 transition {{ $isSelected ? 'border-brand bg-brand/5' : 'border-themed-primary bg-themed-secondary hover:border-brand/60' }}">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusMeta[1] }}">
                                        {{ $statusMeta[0] }}
                                    </span>
                                    <span class="rounded-full border border-themed-primary bg-themed-tertiary px-2.5 py-1 text-xs font-semibold text-themed-secondary">
                                        {{ ucfirst($review->priority) }}
                                    </span>
                                    @if($review->approval_status === 'approved')
                                        <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200">
                                            Approved evidence
                                        </span>
                                    @endif
                                </div>
                                <button type="button" wire:click="selectReview({{ $review->id }})"
                                    class="mt-2 block max-w-full truncate text-left text-lg font-semibold text-themed-primary hover:text-brand">
                                    {{ $review->title }}
                                </button>
                                <p class="mt-1 line-clamp-2 text-sm text-themed-secondary">{{ $review->description }}</p>
                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-themed-secondary">
                                    <span><i aria-hidden="true" class="fas fa-user mr-1"></i>{{ $review->requester?->name ?? 'Unknown learner' }}</span>
                                    <span><i aria-hidden="true" class="fas fa-code-branch mr-1"></i>{{ $review->revisions_count }} revision{{ $review->revisions_count === 1 ? '' : 's' }}</span>
                                    <span><i aria-hidden="true" class="fas fa-comments mr-1"></i>{{ $review->comments_count }} comment{{ $review->comments_count === 1 ? '' : 's' }}</span>
                                    <span><i aria-hidden="true" class="fas fa-clock mr-1"></i>{{ optional($review->requested_at)->diffForHumans() ?? 'No date' }}</span>
                                </div>
                                @if($review->technologies)
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach(array_slice($review->technologies, 0, 5) as $tech)
                                            <span class="rounded-md bg-brand/10 px-2 py-1 text-xs font-medium text-brand">{{ $tech }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="flex shrink-0 gap-2">
                                @if($canStart)
                                    <button type="button" wire:click="startReview({{ $review->id }})"
                                        class="rounded-lg bg-action px-3 py-2 text-xs font-semibold text-action-foreground transition hover:bg-action">
                                        Start
                                    </button>
                                @endif
                                <button type="button" wire:click="selectReview({{ $review->id }})"
                                    class="rounded-lg border border-themed-primary px-3 py-2 text-xs font-semibold text-themed-primary transition hover:border-brand hover:text-brand">
                                    Open
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-themed-primary py-12 text-center">
                        <i aria-hidden="true" class="fas fa-code text-4xl text-themed-tertiary"></i>
                        <h3 class="mt-3 text-lg font-semibold text-themed-primary">No code reviews found</h3>
                        <p class="mt-1 text-sm text-themed-secondary">Submitted repositories and snippets will appear here.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-5">
                {{ $reviews->links() }}
            </div>
        </div>

        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-5 shadow-sm transition-colors duration-300">
            @if($selectedReview)
                @php
                    $latestSnippet = data_get($selectedReview->code_snippets, '0.code');
                    $approvalLabel = match ($selectedReview->approval_status) {
                        'approved' => 'Approved',
                        'needs_revision' => 'Needs revision',
                        default => 'Pending approval',
                    };
                @endphp

                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-brand">Review Workspace</p>
                        <h3 class="text-xl font-bold text-themed-primary">{{ $selectedReview->title }}</h3>
                        <p class="mt-1 text-sm text-themed-secondary">
                            {{ $selectedReview->requester?->name ?? 'Learner' }} with {{ $selectedReview->mentorship?->mentor?->name ?? 'mentor' }}
                        </p>
                    </div>
                    <div class="text-left lg:text-right">
                        <p class="text-sm font-semibold text-themed-primary">{{ $approvalLabel }}</p>
                        <p class="text-2xl font-bold text-brand">
                            {{ $selectedReview->rubric_total_score !== null ? $selectedReview->rubric_total_score . '%' : 'No score' }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-themed-primary p-4">
                        <p class="text-xs font-semibold uppercase text-themed-secondary">Repository</p>
                        @if($selectedReview->repository_url)
                            <a href="{{ $selectedReview->repository_url }}" target="_blank" class="mt-1 block break-all text-sm font-semibold text-brand hover:text-brand">
                                {{ $selectedReview->repository_url }}
                            </a>
                        @else
                            <p class="mt-1 text-sm text-themed-secondary">Snippet submission</p>
                        @endif
                        <div class="mt-2 text-xs text-themed-secondary">
                            <span>Branch: {{ $selectedReview->branch_name ?: 'main' }}</span>
                            @if($selectedReview->commit_hash)
                                <span class="ml-2">Commit: {{ $selectedReview->commit_hash }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-lg border border-themed-primary p-4">
                        <p class="text-xs font-semibold uppercase text-themed-secondary">Review Need</p>
                        <p class="mt-1 text-sm text-themed-primary">{{ $selectedReview->learner_goal ?: $selectedReview->specific_questions ?: 'General review requested.' }}</p>
                        @if($selectedReview->pull_request_url)
                            <a href="{{ $selectedReview->pull_request_url }}" target="_blank" class="mt-2 block break-all text-sm font-semibold text-brand hover:text-brand">
                                Pull request
                            </a>
                        @endif
                    </div>
                </div>

                @if($selectedReview->files_to_review)
                    <div class="mt-4 rounded-lg border border-themed-primary p-4">
                        <p class="text-xs font-semibold uppercase text-themed-secondary">Files to Review</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($selectedReview->files_to_review as $file)
                                <span class="rounded-md bg-themed-tertiary px-2 py-1 text-xs font-medium text-themed-primary">{{ $file }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($latestSnippet)
                    <details class="mt-4 rounded-lg border border-themed-primary">
                        <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-themed-primary">View submitted snippet</summary>
                        <pre class="max-h-80 overflow-auto border-t border-themed-primary bg-slate-950 p-4 text-xs text-slate-100"><code>{{ $latestSnippet }}</code></pre>
                    </details>
                @endif

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-themed-primary p-4">
                        <p class="text-sm font-semibold text-themed-primary">Certificate Evidence</p>
                        @if($selectedReview->certificate_evidence)
                            <p class="mt-2 text-sm text-themed-primary">{{ data_get($selectedReview->certificate_evidence, 'title', 'Certificate evidence') }}</p>
                            @if(data_get($selectedReview->certificate_evidence, 'url'))
                                <a href="{{ data_get($selectedReview->certificate_evidence, 'url') }}" target="_blank" class="mt-1 block break-all text-sm text-brand">Open evidence</a>
                            @endif
                            @if(data_get($selectedReview->certificate_evidence, 'notes'))
                                <p class="mt-1 text-xs text-themed-secondary">{{ data_get($selectedReview->certificate_evidence, 'notes') }}</p>
                            @endif
                        @else
                            <p class="mt-2 text-sm text-themed-secondary">No certificate evidence attached.</p>
                        @endif
                    </div>
                    <div class="rounded-lg border border-themed-primary p-4">
                        <p class="text-sm font-semibold text-themed-primary">Project Evidence</p>
                        @if($selectedReview->project_evidence)
                            <p class="mt-2 text-sm text-themed-primary">{{ data_get($selectedReview->project_evidence, 'title', 'Project evidence') }}</p>
                            @if(data_get($selectedReview->project_evidence, 'url'))
                                <a href="{{ data_get($selectedReview->project_evidence, 'url') }}" target="_blank" class="mt-1 block break-all text-sm text-brand">Open evidence</a>
                            @endif
                            @if(data_get($selectedReview->project_evidence, 'notes'))
                                <p class="mt-1 text-xs text-themed-secondary">{{ data_get($selectedReview->project_evidence, 'notes') }}</p>
                            @endif
                        @else
                            <p class="mt-2 text-sm text-themed-secondary">No project evidence attached.</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 border-t border-themed-primary pt-5">
                    <div class="flex items-center justify-between">
                        <h4 class="text-base font-semibold text-themed-primary">Revision History</h4>
                        @if($selectedCanRevise)
                            <button type="button" wire:click="openRevisionModal"
                                class="rounded-lg bg-orange-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-orange-700">
                                Submit Revision
                            </button>
                        @endif
                    </div>
                    <div class="mt-3 space-y-3">
                        @forelse($selectedReview->revisions as $revision)
                            <div class="rounded-lg border border-themed-primary p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-themed-primary">Revision #{{ $revision->revision_number }}</p>
                                    <p class="text-xs text-themed-secondary">{{ optional($revision->submitted_at)->format('M j, Y g:i A') }}</p>
                                </div>
                                <p class="mt-1 text-sm text-themed-secondary">{{ $revision->notes ?: 'No notes supplied.' }}</p>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-themed-secondary">
                                    @if($revision->repository_url)<span>Repo updated</span>@endif
                                    @if($revision->pull_request_url)<span>PR attached</span>@endif
                                    @if($revision->commit_hash)<span>Commit {{ $revision->commit_hash }}</span>@endif
                                    @if($revision->language)<span>{{ $revision->language }}</span>@endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-themed-secondary">No revision history yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6 border-t border-themed-primary pt-5">
                    <h4 class="text-base font-semibold text-themed-primary">Mentor Comments</h4>
                    <div class="mt-3 space-y-3">
                        @forelse($selectedReview->comments as $comment)
                            <div class="rounded-lg border border-themed-primary p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-themed-primary">{{ $comment->user?->name ?? 'Reviewer' }}</p>
                                    <span class="rounded-full bg-themed-tertiary px-2 py-1 text-xs text-themed-secondary">{{ ucfirst(str_replace('_', ' ', $comment->type)) }}</span>
                                </div>
                                <p class="mt-2 whitespace-pre-line text-sm text-themed-secondary">{{ $comment->body }}</p>
                                <p class="mt-2 text-xs text-themed-tertiary">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-themed-secondary">No comments yet.</p>
                        @endforelse
                    </div>

                    @if($selectedCanManage)
                        <form wire:submit.prevent="saveComment" class="mt-4">
                            <textarea aria-label="Add a comment or implementation note" wire:model="mentorComment" rows="3"
                                class="w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary placeholder-themed-tertiary focus:border-brand focus:ring-brand"
                                placeholder="Add a comment or implementation note"></textarea>
                            @error('mentorComment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            <button type="submit" class="mt-2 rounded-lg border border-themed-primary px-3 py-2 text-xs font-semibold text-themed-primary hover:border-brand hover:text-brand">
                                Add Comment
                            </button>
                        </form>
                    @endif
                </div>

                @if($selectedCanReview)
                    <div class="mt-6 border-t border-themed-primary pt-5">
                        <div class="flex items-center justify-between">
                            <h4 class="text-base font-semibold text-themed-primary">Rubric Review</h4>
                            <p class="text-sm font-semibold text-brand">{{ \App\Models\Mentorship\CodeReview::calculateRubricTotal($rubricScores) }}%</p>
                        </div>

                        <div class="mt-4 space-y-4">
                            @foreach($rubricItems as $key => $item)
                                <div class="rounded-lg border border-themed-primary p-4">
                                    <div class="grid gap-3 md:grid-cols-[1fr_96px]">
                                        <div>
                                            <label class="text-sm font-semibold text-themed-primary">{{ $item['label'] }}</label>
                                            <p class="mt-1 text-xs text-themed-secondary">{{ $item['hint'] }}</p>
                                        </div>
                                        <input type="number" min="0" max="5" wire:model.live="rubricScores.{{ $key }}"
                                            class="h-10 rounded-lg border border-themed-primary bg-themed-secondary px-3 text-sm text-themed-primary focus:border-brand focus:ring-brand">
                                    </div>
                                    <textarea aria-label="Rubric note" wire:model="rubricNotes.{{ $key }}" rows="2"
                                        class="mt-3 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary placeholder-themed-tertiary focus:border-brand focus:ring-brand"
                                        placeholder="Rubric note"></textarea>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <label class="text-sm font-semibold text-themed-primary">Summary Feedback</label>
                            <textarea aria-label="Detailed mentor feedback" wire:model="reviewFeedback" rows="5"
                                class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary placeholder-themed-tertiary focus:border-brand focus:ring-brand"
                                placeholder="Detailed mentor feedback"></textarea>
                            @error('reviewFeedback') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-themed-primary">Suggestions</label>
                                @foreach($suggestions as $index => $suggestion)
                                    <div class="mt-2 flex">
                                        <input aria-label="Suggestion" type="text" wire:model="suggestions.{{ $index }}"
                                            class="min-w-0 flex-1 rounded-l-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary placeholder-themed-tertiary focus:border-brand focus:ring-brand"
                                            placeholder="Suggestion">
                                        <button type="button" wire:click="removeSuggestion({{ $index }})" class="rounded-r-lg bg-red-600 px-3 text-white" aria-label="Remove suggestion">
                                            <i aria-hidden="true" class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addSuggestion" class="mt-2 rounded-lg border border-themed-primary px-3 py-2 text-xs font-semibold text-themed-primary hover:border-brand">
                                    Add Suggestion
                                </button>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-themed-primary">Improvement Areas</label>
                                @foreach($improvementAreas as $index => $area)
                                    <div class="mt-2 flex">
                                        <input aria-label="Area to improve" type="text" wire:model="improvementAreas.{{ $index }}"
                                            class="min-w-0 flex-1 rounded-l-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary placeholder-themed-tertiary focus:border-brand focus:ring-brand"
                                            placeholder="Area to improve">
                                        <button type="button" wire:click="removeImprovementArea({{ $index }})" class="rounded-r-lg bg-red-600 px-3 text-white" aria-label="Remove improvement area">
                                            <i aria-hidden="true" class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addImprovementArea" class="mt-2 rounded-lg border border-themed-primary px-3 py-2 text-xs font-semibold text-themed-primary hover:border-brand">
                                    Add Area
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div class="rounded-lg border border-themed-primary p-4">
                                <label class="text-sm font-semibold text-themed-primary">Certificate Evidence</label>
                                <input aria-label="Certificate title or request" type="text" wire:model="certificateEvidenceTitle" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Certificate title or request">
                                <input aria-label="Evidence URL" type="url" wire:model="certificateEvidenceUrl" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Evidence URL">
                                <textarea aria-label="Certificate notes" wire:model="certificateEvidenceNotes" rows="2" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Certificate notes"></textarea>
                            </div>
                            <div class="rounded-lg border border-themed-primary p-4">
                                <label class="text-sm font-semibold text-themed-primary">Project Evidence</label>
                                <input aria-label="Portfolio/project title" type="text" wire:model="projectEvidenceTitle" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Portfolio/project title">
                                <input aria-label="Project URL" type="url" wire:model="projectEvidenceUrl" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Project URL">
                                <textarea aria-label="Project notes" wire:model="projectEvidenceNotes" rows="2" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Project notes"></textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-sm font-semibold text-themed-primary">Approval Notes</label>
                            <textarea aria-label="Decision notes" wire:model="approvalNotes" rows="3"
                                class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary placeholder-themed-tertiary focus:border-brand focus:ring-brand"
                                placeholder="Decision notes"></textarea>
                        </div>

                        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-end">
                            <button type="button" wire:click="requestRevision"
                                class="rounded-lg border border-orange-300 px-4 py-2 text-sm font-semibold text-orange-700 transition hover:bg-orange-50 dark:border-orange-700 dark:text-orange-200 dark:hover:bg-orange-900/20">
                                Request Revision
                            </button>
                            <button type="button" wire:click="approveReview"
                                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                Approve Review
                            </button>
                        </div>
                    </div>
                @endif
            @else
                <div class="flex min-h-[520px] flex-col items-center justify-center rounded-lg border border-dashed border-themed-primary text-center">
                    <i aria-hidden="true" class="fas fa-code-branch text-5xl text-themed-tertiary"></i>
                    <h3 class="mt-4 text-lg font-semibold text-themed-primary">Open a review</h3>
                    <p class="mt-1 max-w-sm text-sm text-themed-secondary">Select a submitted work item to see the repository, comments, revision history, rubric, and approval evidence.</p>
                </div>
            @endif
        </div>
    </div>

    @if($showReviewModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModal()" role="dialog" aria-modal="true" aria-label="Code Reviews dialog" tabindex="-1">
            <div class="max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-lg border border-themed-primary bg-themed-secondary shadow-sm">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-themed-primary bg-themed-secondary p-5">
                    <div>
                        <h3 class="text-xl font-bold text-themed-primary">Request Code Review</h3>
                        <p class="text-sm text-themed-secondary">Attach a repo, paste a snippet, or use both.</p>
                    </div>
                    <button aria-label="Close dialog" type="button" wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary">
                        <i aria-hidden="true" class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="submitReview" class="space-y-5 p-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Active Mentorship</label>
                            <select aria-label="Mentorship id" wire:model="mentorshipId" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                                <option value="">Select mentorship</option>
                                @foreach($activeMentorships as $mentorship)
                                    <option value="{{ $mentorship->id }}">
                                        {{ $mentorship->mentee?->name ?? 'Learner' }} / {{ $mentorship->mentor?->name ?? 'Mentor' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mentorshipId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Priority</label>
                            <select aria-label="Priority" wire:model="priority" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Review Title</label>
                            <input aria-label="API authentication review" type="text" wire:model="reviewTitle" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="API authentication review">
                            @error('reviewTitle') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Language or Stack</label>
                            <input aria-label="Laravel, React, Python" type="text" wire:model="language" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Laravel, React, Python">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-themed-primary">Description</label>
                        <textarea aria-label="What should the mentor review?" wire:model="reviewDescription" rows="3" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="What should the mentor review?"></textarea>
                        @error('reviewDescription') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-themed-primary">Technologies</label>
                        @foreach($technologies as $index => $technology)
                            <div class="mt-2 flex">
                                <input aria-label="Technology" type="text" wire:model="technologies.{{ $index }}" class="min-w-0 flex-1 rounded-l-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Technology">
                                <button type="button" wire:click="removeTechnology({{ $index }})" class="rounded-r-lg bg-red-600 px-3 text-white" aria-label="Remove technology">
                                    <i aria-hidden="true" class="fas fa-minus"></i>
                                </button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addTechnology" class="mt-2 rounded-lg border border-themed-primary px-3 py-2 text-xs font-semibold text-themed-primary hover:border-brand">
                            Add Technology
                        </button>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Repository URL</label>
                            <input aria-label="https://github.com/user/repo" type="url" wire:model="repositoryUrl" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="https://github.com/user/repo">
                            @error('repositoryUrl') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Pull Request URL</label>
                            <input aria-label="https://github.com/user/repo/pull/1" type="url" wire:model="pullRequestUrl" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="https://github.com/user/repo/pull/1">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Branch</label>
                            <input aria-label="main" type="text" wire:model="branchName" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="main">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Commit Hash</label>
                            <input aria-label="Optional" type="text" wire:model="commitHash" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Optional">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Files to Review</label>
                            <textarea aria-label="app/Http/Controllers/AuthController.php" wire:model="filesToReviewText" rows="4" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="app/Http/Controllers/AuthController.php"></textarea>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Learner Goal</label>
                            <textarea aria-label="What should this prove for the learner?" wire:model="learnerGoal" rows="4" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="What should this prove for the learner?"></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-themed-primary">Code Snippet</label>
                        <textarea aria-label="Paste code here if there is no repo, or include the key snippet for context" wire:model="codeSnippet" rows="8" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 font-mono text-sm text-themed-primary" placeholder="Paste code here if there is no repo, or include the key snippet for context."></textarea>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-themed-primary">Specific Questions</label>
                        <textarea aria-label="Security, test coverage, architecture, naming, performance" wire:model="specificQuestions" rows="3" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Security, test coverage, architecture, naming, performance..."></textarea>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg border border-themed-primary p-4">
                            <label class="text-sm font-semibold text-themed-primary">Certificate Evidence</label>
                            <input aria-label="Certificate title or milestone" type="text" wire:model="certificateEvidenceTitle" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Certificate title or milestone">
                            <input aria-label="Evidence URL" type="url" wire:model="certificateEvidenceUrl" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Evidence URL">
                            <textarea aria-label="Evidence notes" wire:model="certificateEvidenceNotes" rows="2" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Evidence notes"></textarea>
                        </div>
                        <div class="rounded-lg border border-themed-primary p-4">
                            <label class="text-sm font-semibold text-themed-primary">Project Evidence</label>
                            <input aria-label="Portfolio project title" type="text" wire:model="projectEvidenceTitle" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Portfolio project title">
                            <input aria-label="Project URL" type="url" wire:model="projectEvidenceUrl" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Project URL">
                            <textarea aria-label="Project notes" wire:model="projectEvidenceNotes" rows="2" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="Project notes"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-themed-primary pt-5">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-themed-primary px-4 py-2 text-sm font-semibold text-themed-primary hover:bg-themed-tertiary">Cancel</button>
                        <button type="submit" class="rounded-lg bk-button bk-button-primary px-4 py-2 text-sm font-semibold  ">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showRevisionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModal()" role="dialog" aria-modal="true" aria-label="Code Reviews dialog" tabindex="-1">
            <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-lg border border-themed-primary bg-themed-secondary shadow-sm">
                <div class="flex items-center justify-between border-b border-themed-primary p-5">
                    <h3 class="text-xl font-bold text-themed-primary">Submit Revision</h3>
                    <button aria-label="Close dialog" type="button" wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary">
                        <i aria-hidden="true" class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form wire:submit.prevent="submitRevision" class="space-y-4 p-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Repository URL</label>
                            <input aria-label="Repository url" type="url" wire:model="repositoryUrl" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                            @error('repositoryUrl') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Pull Request URL</label>
                            <input aria-label="Pull request url" type="url" wire:model="pullRequestUrl" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Branch</label>
                            <input aria-label="Branch name" type="text" wire:model="branchName" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-themed-primary">Commit Hash</label>
                            <input aria-label="Commit hash" type="text" wire:model="commitHash" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-themed-primary">Files to Review</label>
                        <textarea aria-label="Files to review text" wire:model="filesToReviewText" rows="3" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary"></textarea>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-themed-primary">Revised Snippet</label>
                        <textarea aria-label="Code snippet" wire:model="codeSnippet" rows="8" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 font-mono text-sm text-themed-primary"></textarea>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-themed-primary">Revision Notes</label>
                        <textarea aria-label="What changed?" wire:model="revisionNotes" rows="3" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary" placeholder="What changed?"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-themed-primary pt-5">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-themed-primary px-4 py-2 text-sm font-semibold text-themed-primary hover:bg-themed-tertiary">Cancel</button>
                        <button type="submit" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">Submit Revision</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
