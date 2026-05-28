<?php

namespace App\Livewire\Mentorship;

use App\Models\Mentorship\CodeReview;
use App\Models\Mentorship\CodeReviewComment;
use App\Models\Mentorship\Mentorship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', [
    'title' => 'Code Reviews',
    'description' => 'Submit work, receive mentor review, revise, and attach evidence',
    'icon' => 'fas fa-code',
    'active' => 'mentorship'
])]
class CodeReviews extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $search = '';
    public ?int $selectedReviewId = null;

    public bool $showReviewModal = false;
    public bool $showRevisionModal = false;

    public ?int $mentorshipId = null;
    public string $reviewTitle = '';
    public string $reviewDescription = '';
    public array $technologies = [''];
    public string $repositoryUrl = '';
    public string $branchName = 'main';
    public string $pullRequestUrl = '';
    public string $commitHash = '';
    public string $filesToReviewText = '';
    public string $codeSnippet = '';
    public string $language = '';
    public string $learnerGoal = '';
    public string $specificQuestions = '';
    public string $priority = 'medium';

    public string $reviewFeedback = '';
    public string $mentorComment = '';
    public array $suggestions = [''];
    public array $improvementAreas = [''];
    public array $rubricScores = [];
    public array $rubricNotes = [];
    public string $approvalNotes = '';

    public string $revisionNotes = '';
    public string $certificateEvidenceTitle = '';
    public string $certificateEvidenceUrl = '';
    public string $certificateEvidenceNotes = '';
    public string $projectEvidenceTitle = '';
    public string $projectEvidenceUrl = '';
    public string $projectEvidenceNotes = '';

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->resetRubric();
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['statusFilter', 'priorityFilter', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function createReview($mentorshipId = null): void
    {
        $this->resetReviewForm();
        $this->mentorshipId = $mentorshipId ? (int) $mentorshipId : null;
        $this->showReviewModal = true;
    }

    public function submitReview(): void
    {
        $this->validate([
            'mentorshipId' => 'required|integer|exists:mentorships,id',
            'reviewTitle' => 'required|string|max:255',
            'reviewDescription' => 'required|string|min:20',
            'technologies' => 'required|array|min:1',
            'technologies.*' => 'nullable|string|max:100',
            'repositoryUrl' => 'nullable|url|max:500',
            'pullRequestUrl' => 'nullable|url|max:500',
            'branchName' => 'nullable|string|max:120',
            'commitHash' => 'nullable|string|max:120',
            'filesToReviewText' => 'nullable|string|max:3000',
            'codeSnippet' => 'nullable|string|max:30000',
            'language' => 'nullable|string|max:80',
            'learnerGoal' => 'nullable|string|max:2000',
            'specificQuestions' => 'nullable|string|max:3000',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        if (! trim($this->repositoryUrl) && ! trim($this->codeSnippet)) {
            $this->addError('repositoryUrl', 'Add a repository URL or paste a code snippet.');
            return;
        }

        $mentorship = Mentorship::with(['mentor', 'mentee'])->find($this->mentorshipId);

        if (! $mentorship || ! $this->canUseMentorship($mentorship)) {
            session()->flash('error', 'You cannot submit work for this mentorship.');
            return;
        }

        if (! $mentorship->isActive()) {
            session()->flash('error', 'Code reviews need an active mentorship.');
            return;
        }

        $filesToReview = $this->parseLines($this->filesToReviewText);
        $codeSnippet = trim($this->codeSnippet);
        $submissionType = trim($this->repositoryUrl) ? 'repository' : 'snippet';

        $review = CodeReview::create([
            'mentorship_id' => $mentorship->id,
            'requested_by' => Auth::id(),
            'title' => $this->reviewTitle,
            'description' => $this->reviewDescription,
            'status' => CodeReview::STATUS_PENDING,
            'approval_status' => CodeReview::APPROVAL_PENDING,
            'priority' => $this->priority,
            'submission_type' => $submissionType,
            'technologies' => $this->cleanArray($this->technologies),
            'repository_url' => $this->repositoryUrl ?: null,
            'branch_name' => $this->branchName ?: 'main',
            'pull_request_url' => $this->pullRequestUrl ?: null,
            'commit_hash' => $this->commitHash ?: null,
            'files_to_review' => $filesToReview,
            'specific_questions' => $this->specificQuestions ?: null,
            'learner_goal' => $this->learnerGoal ?: null,
            'language' => $this->language ?: null,
            'code_snippets' => $codeSnippet ? [[
                'language' => $this->language ?: null,
                'code' => $codeSnippet,
                'revision' => 1,
            ]] : null,
            'certificate_evidence' => $this->evidencePayload('certificate'),
            'project_evidence' => $this->evidencePayload('project'),
            'requested_at' => now(),
            'is_urgent' => $this->priority === CodeReview::PRIORITY_URGENT,
        ]);

        $review->recordRevision([
            'repository_url' => $this->repositoryUrl ?: null,
            'branch_name' => $this->branchName ?: 'main',
            'pull_request_url' => $this->pullRequestUrl ?: null,
            'commit_hash' => $this->commitHash ?: null,
            'files_to_review' => $filesToReview,
            'language' => $this->language ?: null,
            'code_snippet' => $codeSnippet ?: null,
            'learner_goal' => $this->learnerGoal ?: null,
            'notes' => 'Initial submission',
        ], Auth::id());

        $this->selectedReviewId = $review->id;
        $this->showReviewModal = false;
        $this->resetReviewForm();
        session()->flash('message', 'Code review submitted. The revision trail has been started.');
    }

    public function selectReview(int $reviewId): void
    {
        $review = $this->findVisibleReview($reviewId);

        if (! $review) {
            session()->flash('error', 'Code review not found.');
            return;
        }

        $this->selectedReviewId = $review->id;
        $this->hydrateReviewWorkspace($review);
    }

    public function startReview(int $reviewId): void
    {
        $review = $this->findVisibleReview($reviewId);

        if (! $review || ! $this->canReviewCode($review)) {
            session()->flash('error', 'You cannot start this review.');
            return;
        }

        $review->startReview(Auth::id());
        $this->selectedReviewId = $review->id;
        $this->hydrateReviewWorkspace($review->refresh());
        session()->flash('message', 'Review started.');
    }

    public function saveComment(): void
    {
        $this->validate([
            'mentorComment' => 'required|string|min:3|max:5000',
        ]);

        $review = $this->selectedReview();

        if (! $review || ! $this->canManageCodeReview($review)) {
            session()->flash('error', 'You cannot comment on this review.');
            return;
        }

        $review->addComment(
            trim($this->mentorComment),
            Auth::id(),
            CodeReviewComment::TYPE_COMMENT,
            [],
            $review->latestRevision?->id
        );

        $this->mentorComment = '';
        session()->flash('message', 'Comment added.');
    }

    public function requestRevision(): void
    {
        $review = $this->selectedReview();

        if (! $review || ! $this->canReviewCode($review)) {
            session()->flash('error', 'You cannot request a revision for this review.');
            return;
        }

        $this->validateReviewDecision();

        $review->requestRevision($this->reviewDecisionPayload(), Auth::id());
        $review->addComment(
            $this->approvalNotes ?: $this->reviewFeedback,
            Auth::id(),
            CodeReviewComment::TYPE_REVISION_REQUEST,
            ['rubric_total_score' => CodeReview::calculateRubricTotal($this->rubricScores)],
            $review->latestRevision?->id
        );

        $this->hydrateReviewWorkspace($review->refresh());
        session()->flash('message', 'Revision requested with rubric feedback.');
    }

    public function approveReview(): void
    {
        $review = $this->selectedReview();

        if (! $review || ! $this->canReviewCode($review)) {
            session()->flash('error', 'You cannot approve this review.');
            return;
        }

        $this->validateReviewDecision();

        $review->approveWithReview($this->reviewDecisionPayload(), Auth::id());
        $review->addComment(
            $this->approvalNotes ?: 'Approved with rubric review.',
            Auth::id(),
            CodeReviewComment::TYPE_APPROVAL,
            ['rubric_total_score' => CodeReview::calculateRubricTotal($this->rubricScores)],
            $review->latestRevision?->id
        );

        $this->hydrateReviewWorkspace($review->refresh());
        session()->flash('message', 'Review approved. Certificate and project evidence are attached.');
    }

    public function openRevisionModal(): void
    {
        $review = $this->selectedReview();

        if (! $review || ! $this->canSubmitRevision($review)) {
            session()->flash('error', 'You cannot submit a revision for this review.');
            return;
        }

        $this->repositoryUrl = $review->repository_url ?? '';
        $this->branchName = $review->branch_name ?: 'main';
        $this->pullRequestUrl = $review->pull_request_url ?? '';
        $this->commitHash = '';
        $this->filesToReviewText = implode(PHP_EOL, $review->files_to_review ?? []);
        $this->language = $review->language ?? '';
        $this->codeSnippet = '';
        $this->learnerGoal = $review->learner_goal ?? '';
        $this->revisionNotes = '';
        $this->showRevisionModal = true;
    }

    public function submitRevision(): void
    {
        $review = $this->selectedReview();

        if (! $review || ! $this->canSubmitRevision($review)) {
            session()->flash('error', 'You cannot submit a revision for this review.');
            return;
        }

        $this->validate([
            'repositoryUrl' => 'nullable|url|max:500',
            'pullRequestUrl' => 'nullable|url|max:500',
            'branchName' => 'nullable|string|max:120',
            'commitHash' => 'nullable|string|max:120',
            'filesToReviewText' => 'nullable|string|max:3000',
            'codeSnippet' => 'nullable|string|max:30000',
            'language' => 'nullable|string|max:80',
            'learnerGoal' => 'nullable|string|max:2000',
            'revisionNotes' => 'nullable|string|max:3000',
        ]);

        if (! trim($this->repositoryUrl) && ! trim($this->codeSnippet)) {
            $this->addError('repositoryUrl', 'Add a repository URL or paste the revised code snippet.');
            return;
        }

        $revision = $review->recordRevision([
            'repository_url' => $this->repositoryUrl ?: null,
            'branch_name' => $this->branchName ?: 'main',
            'pull_request_url' => $this->pullRequestUrl ?: null,
            'commit_hash' => $this->commitHash ?: null,
            'files_to_review' => $this->parseLines($this->filesToReviewText),
            'language' => $this->language ?: null,
            'code_snippet' => trim($this->codeSnippet) ?: null,
            'learner_goal' => $this->learnerGoal ?: null,
            'notes' => $this->revisionNotes ?: 'Learner submitted a revised version.',
        ], Auth::id());

        $review->addComment(
            'Submitted revision #' . $revision->revision_number . ($this->revisionNotes ? ': ' . $this->revisionNotes : '.'),
            Auth::id(),
            CodeReviewComment::TYPE_COMMENT,
            [],
            $revision->id
        );

        $this->showRevisionModal = false;
        $this->hydrateReviewWorkspace($review->refresh());
        session()->flash('message', 'Revision submitted. The review is back in the queue.');
    }

    public function resetReviewForm(): void
    {
        $this->reset([
            'mentorshipId',
            'reviewTitle',
            'reviewDescription',
            'repositoryUrl',
            'pullRequestUrl',
            'commitHash',
            'filesToReviewText',
            'codeSnippet',
            'language',
            'learnerGoal',
            'specificQuestions',
            'certificateEvidenceTitle',
            'certificateEvidenceUrl',
            'certificateEvidenceNotes',
            'projectEvidenceTitle',
            'projectEvidenceUrl',
            'projectEvidenceNotes',
        ]);

        $this->technologies = [''];
        $this->priority = CodeReview::PRIORITY_MEDIUM;
        $this->branchName = 'main';
    }

    public function closeModal(): void
    {
        $this->showReviewModal = false;
        $this->showRevisionModal = false;
        $this->resetReviewForm();
    }

    public function addTechnology(): void
    {
        $this->technologies[] = '';
    }

    public function removeTechnology(int $index): void
    {
        unset($this->technologies[$index]);
        $this->technologies = array_values($this->technologies ?: ['']);
    }

    public function addSuggestion(): void
    {
        $this->suggestions[] = '';
    }

    public function removeSuggestion(int $index): void
    {
        unset($this->suggestions[$index]);
        $this->suggestions = array_values($this->suggestions ?: ['']);
    }

    public function addImprovementArea(): void
    {
        $this->improvementAreas[] = '';
    }

    public function removeImprovementArea(int $index): void
    {
        unset($this->improvementAreas[$index]);
        $this->improvementAreas = array_values($this->improvementAreas ?: ['']);
    }

    #[On('review-created')]
    #[On('review-updated')]
    #[On('review-completed')]
    public function refreshReviews(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $baseQuery = $this->visibleReviewsQuery($user);

        $query = (clone $baseQuery)
            ->with(['mentorship.mentor', 'mentorship.mentee', 'requester', 'reviewer', 'approver'])
            ->withCount(['revisions', 'comments']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('repository_url', 'like', $search)
                    ->orWhere('pull_request_url', 'like', $search)
                    ->orWhere('language', 'like', $search)
                    ->orWhereHas('requester', fn (Builder $q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
            });
        }

        $selectedReview = $this->selectedReviewId
            ? $this->findVisibleReview($this->selectedReviewId)
            : null;

        $activeMentorships = $this->activeMentorships($user);

        return view('livewire.mentorship.code-reviews', [
            'reviews' => $query->orderByDesc('updated_at')->paginate(10),
            'activeMentorships' => $activeMentorships,
            'selectedReview' => $selectedReview,
            'rubricItems' => CodeReview::rubricItems(),
            'stats' => $this->stats((clone $baseQuery)),
            'selectedCanReview' => $selectedReview ? $this->canReviewCode($selectedReview) : false,
            'selectedCanManage' => $selectedReview ? $this->canManageCodeReview($selectedReview) : false,
            'selectedCanRevise' => $selectedReview ? $this->canSubmitRevision($selectedReview) : false,
        ]);
    }

    private function selectedReview(): ?CodeReview
    {
        return $this->selectedReviewId ? $this->findVisibleReview($this->selectedReviewId) : null;
    }

    private function findVisibleReview(int $reviewId): ?CodeReview
    {
        return $this->visibleReviewsQuery(Auth::user())
            ->with([
                'mentorship.mentor',
                'mentorship.mentee',
                'requester',
                'reviewer',
                'approver',
                'latestRevision',
                'revisions.user',
                'comments.user',
                'comments.revision',
            ])
            ->find($reviewId);
    }

    private function visibleReviewsQuery($user): Builder
    {
        $query = CodeReview::query();

        if ($user->isSuperAdmin() || $user->isAcademyAdmin()) {
            return $query;
        }

        return $query->whereHas('mentorship', function (Builder $builder) use ($user) {
            $builder->where('mentor_id', $user->id)
                ->orWhere('mentee_id', $user->id);
        });
    }

    private function activeMentorships($user)
    {
        $query = Mentorship::with(['mentor', 'mentee'])
            ->where('status', Mentorship::STATUS_ACTIVE);

        if (! $user->isSuperAdmin() && ! $user->isAcademyAdmin()) {
            $query->where(function (Builder $builder) use ($user) {
                $builder->where('mentor_id', $user->id)
                    ->orWhere('mentee_id', $user->id);
            });
        }

        return $query->orderByDesc('accepted_at')->get();
    }

    private function stats(Builder $query): array
    {
        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', CodeReview::STATUS_PENDING)->count(),
            'in_review' => (clone $query)->where('status', CodeReview::STATUS_IN_REVIEW)->count(),
            'revision_requested' => (clone $query)->where('status', CodeReview::STATUS_REVISION_REQUESTED)->count(),
            'approved' => (clone $query)->where('approval_status', CodeReview::APPROVAL_APPROVED)->count(),
            'average_score' => round((float) (clone $query)->whereNotNull('rubric_total_score')->avg('rubric_total_score'), 1),
        ];
    }

    private function hydrateReviewWorkspace(CodeReview $review): void
    {
        $this->reviewFeedback = $review->review_feedback ?? '';
        $this->approvalNotes = $review->approval_notes ?? '';
        $this->suggestions = $review->suggestions ?: [''];
        $this->improvementAreas = $review->improvement_areas ?: [''];
        $this->rubricScores = array_merge($this->defaultRubricScores(), $review->rubric_scores ?? []);
        $this->rubricNotes = array_merge($this->defaultRubricNotes(), $review->rubric_notes ?? []);
        $this->certificateEvidenceTitle = data_get($review->certificate_evidence, 'title', '');
        $this->certificateEvidenceUrl = data_get($review->certificate_evidence, 'url', '');
        $this->certificateEvidenceNotes = data_get($review->certificate_evidence, 'notes', '');
        $this->projectEvidenceTitle = data_get($review->project_evidence, 'title', '');
        $this->projectEvidenceUrl = data_get($review->project_evidence, 'url', '');
        $this->projectEvidenceNotes = data_get($review->project_evidence, 'notes', '');
    }

    private function validateReviewDecision(): void
    {
        $this->validate([
            'reviewFeedback' => 'required|string|min:20|max:10000',
            'rubricScores.*' => 'required|integer|min:0|max:5',
            'rubricNotes.*' => 'nullable|string|max:1000',
            'suggestions' => 'nullable|array',
            'suggestions.*' => 'nullable|string|max:1000',
            'improvementAreas' => 'nullable|array',
            'improvementAreas.*' => 'nullable|string|max:1000',
            'approvalNotes' => 'nullable|string|max:3000',
            'certificateEvidenceTitle' => 'nullable|string|max:255',
            'certificateEvidenceUrl' => 'nullable|url|max:500',
            'certificateEvidenceNotes' => 'nullable|string|max:2000',
            'projectEvidenceTitle' => 'nullable|string|max:255',
            'projectEvidenceUrl' => 'nullable|url|max:500',
            'projectEvidenceNotes' => 'nullable|string|max:2000',
        ]);
    }

    private function reviewDecisionPayload(): array
    {
        $scores = collect(CodeReview::rubricItems())
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => max(0, min(5, (int) ($this->rubricScores[$key] ?? 0)))])
            ->all();

        return [
            'feedback' => $this->reviewFeedback,
            'suggestions' => $this->cleanArray($this->suggestions),
            'improvement_areas' => $this->cleanArray($this->improvementAreas),
            'rubric_scores' => $scores,
            'rubric_notes' => array_filter($this->rubricNotes, fn ($value) => trim((string) $value) !== ''),
            'approval_notes' => $this->approvalNotes ?: null,
            'certificate_evidence' => $this->evidencePayload('certificate'),
            'project_evidence' => $this->evidencePayload('project'),
        ];
    }

    private function canUseMentorship(Mentorship $mentorship): bool
    {
        $user = Auth::user();

        return $user->isSuperAdmin()
            || $user->isAcademyAdmin()
            || $user->id === $mentorship->mentor_id
            || $user->id === $mentorship->mentee_id;
    }

    private function canManageCodeReview(CodeReview $review): bool
    {
        $user = Auth::user();

        return $user->isSuperAdmin()
            || $user->isAcademyAdmin()
            || $user->id === $review->requested_by
            || $user->id === $review->mentorship->mentor_id;
    }

    private function canReviewCode(CodeReview $review): bool
    {
        $user = Auth::user();

        return $user->isSuperAdmin()
            || $user->isAcademyAdmin()
            || $user->id === $review->mentorship->mentor_id;
    }

    private function canSubmitRevision(CodeReview $review): bool
    {
        $user = Auth::user();

        return $review->status === CodeReview::STATUS_REVISION_REQUESTED
            && (
                $user->isSuperAdmin()
                || $user->isAcademyAdmin()
                || $user->id === $review->requested_by
                || $user->id === $review->mentorship->mentee_id
            );
    }

    private function resetRubric(): void
    {
        $this->rubricScores = $this->defaultRubricScores();
        $this->rubricNotes = $this->defaultRubricNotes();
    }

    private function defaultRubricScores(): array
    {
        return collect(CodeReview::rubricItems())->keys()->mapWithKeys(fn ($key) => [$key => 0])->all();
    }

    private function defaultRubricNotes(): array
    {
        return collect(CodeReview::rubricItems())->keys()->mapWithKeys(fn ($key) => [$key => ''])->all();
    }

    private function parseLines(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function cleanArray(array $values): array
    {
        return collect($values)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();
    }

    private function evidencePayload(string $type): ?array
    {
        $payload = $type === 'certificate'
            ? [
                'title' => $this->certificateEvidenceTitle,
                'url' => $this->certificateEvidenceUrl,
                'notes' => $this->certificateEvidenceNotes,
            ]
            : [
                'title' => $this->projectEvidenceTitle,
                'url' => $this->projectEvidenceUrl,
                'notes' => $this->projectEvidenceNotes,
            ];

        $payload = array_filter($payload, fn ($value) => trim((string) $value) !== '');

        return $payload ?: null;
    }
}
