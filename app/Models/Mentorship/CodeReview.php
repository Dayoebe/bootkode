<?php

namespace App\Models\Mentorship;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Core\User;

class CodeReview extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_REVISION_REQUESTED = 'revision_requested';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DECLINED = 'declined';

    const APPROVAL_PENDING = 'pending';
    const APPROVAL_NEEDS_REVISION = 'needs_revision';
    const APPROVAL_APPROVED = 'approved';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'mentorship_id',
        'requested_by',
        'reviewed_by',
        'title',
        'description',
        'status',
        'priority',
        'submission_type',
        'technologies',
        'repository_url',
        'branch_name',
        'commit_hash',
        'pull_request_url',
        'files_to_review',
        'specific_questions',
        'learner_goal',
        'code_snippets',
        'language',
        'review_feedback',
        'suggestions',
        'code_quality_score',
        'improvement_areas',
        'rubric_scores',
        'rubric_notes',
        'rubric_total_score',
        'approval_status',
        'approval_notes',
        'approved_at',
        'approved_by',
        'revision_count',
        'last_revision_at',
        'certificate_evidence',
        'project_evidence',
        'requested_at',
        'started_review_at',
        'completed_at',
        'estimated_review_time',
        'actual_review_time',
        'attachments',
        'is_urgent'
    ];

    protected $casts = [
        'technologies' => 'array',
        'files_to_review' => 'array',
        'code_snippets' => 'array',
        'suggestions' => 'array',
        'improvement_areas' => 'array',
        'rubric_scores' => 'array',
        'rubric_notes' => 'array',
        'certificate_evidence' => 'array',
        'project_evidence' => 'array',
        'attachments' => 'array',
        'is_urgent' => 'boolean',
        'code_quality_score' => 'decimal:2',
        'rubric_total_score' => 'decimal:2',
        'revision_count' => 'integer',
        'requested_at' => 'datetime',
        'started_review_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'last_revision_at' => 'datetime'
    ];

    public function mentorship()
    {
        return $this->belongsTo(Mentorship::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function revisions()
    {
        return $this->hasMany(CodeReviewRevision::class)->orderByDesc('revision_number');
    }

    public function latestRevision()
    {
        return $this->hasOne(CodeReviewRevision::class)->latestOfMany('revision_number');
    }

    public function comments()
    {
        return $this->hasMany(CodeReviewComment::class)->latest();
    }

    public static function rubricItems(): array
    {
        return [
            'requirements' => [
                'label' => 'Requirements',
                'hint' => 'Solves the requested task and handles edge cases.',
            ],
            'correctness' => [
                'label' => 'Correctness',
                'hint' => 'Code behaves reliably and avoids regressions.',
            ],
            'maintainability' => [
                'label' => 'Maintainability',
                'hint' => 'Readable, cohesive, and easy to extend.',
            ],
            'testing' => [
                'label' => 'Testing',
                'hint' => 'Useful tests or verification evidence are present.',
            ],
            'security' => [
                'label' => 'Security',
                'hint' => 'Input, auth, data, and dependency risks are considered.',
            ],
        ];
    }

    public static function calculateRubricTotal(array $scores): float
    {
        $max = count(static::rubricItems()) * 5;
        $earned = collect(static::rubricItems())
            ->keys()
            ->sum(fn ($key) => max(0, min(5, (int) ($scores[$key] ?? 0))));

        return $max > 0 ? round(($earned / $max) * 100, 2) : 0.0;
    }

    public function startReview($reviewerId = null)
    {
        $this->update([
            'status' => self::STATUS_IN_REVIEW,
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'started_review_at' => now()
        ]);

        return $this;
    }

    public function complete($feedback, $suggestions = [], $score = null)
    {
        $reviewTime = $this->started_review_at 
            ? $this->started_review_at->diffInMinutes(now())
            : null;
        $approvalStatus = $score !== null && (float) $score >= 7
            ? self::APPROVAL_APPROVED
            : self::APPROVAL_NEEDS_REVISION;

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'review_feedback' => $feedback,
            'suggestions' => $suggestions,
            'code_quality_score' => $score,
            'rubric_total_score' => $score !== null ? round(((float) $score / 10) * 100, 2) : null,
            'approval_status' => $approvalStatus,
            'approved_at' => $approvalStatus === self::APPROVAL_APPROVED ? now() : null,
            'approved_by' => $approvalStatus === self::APPROVAL_APPROVED ? ($this->reviewed_by ?? auth()->id()) : null,
            'actual_review_time' => $reviewTime
        ]);

        return $this;
    }

    public function recordRevision(array $data, ?int $userId = null): CodeReviewRevision
    {
        $revisionNumber = ((int) $this->revisions()->max('revision_number')) + 1;

        $revision = $this->revisions()->create([
            'user_id' => $userId ?? auth()->id(),
            'revision_number' => $revisionNumber,
            'repository_url' => $data['repository_url'] ?? $this->repository_url,
            'branch_name' => $data['branch_name'] ?? $this->branch_name,
            'pull_request_url' => $data['pull_request_url'] ?? $this->pull_request_url,
            'commit_hash' => $data['commit_hash'] ?? $this->commit_hash,
            'files_to_review' => $data['files_to_review'] ?? $this->files_to_review,
            'language' => $data['language'] ?? $this->language,
            'code_snippet' => $data['code_snippet'] ?? null,
            'learner_goal' => $data['learner_goal'] ?? $this->learner_goal,
            'notes' => $data['notes'] ?? null,
            'submitted_at' => $data['submitted_at'] ?? now(),
        ]);

        $this->update([
            'repository_url' => $revision->repository_url,
            'branch_name' => $revision->branch_name ?: 'main',
            'pull_request_url' => $revision->pull_request_url,
            'commit_hash' => $revision->commit_hash,
            'files_to_review' => $revision->files_to_review,
            'language' => $revision->language,
            'learner_goal' => $revision->learner_goal,
            'code_snippets' => $revision->code_snippet ? [[
                'language' => $revision->language,
                'code' => $revision->code_snippet,
                'revision' => $revision->revision_number,
            ]] : $this->code_snippets,
            'status' => self::STATUS_PENDING,
            'approval_status' => self::APPROVAL_PENDING,
            'revision_count' => $revisionNumber,
            'last_revision_at' => $revision->submitted_at,
            'completed_at' => null,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return $revision;
    }

    public function addComment(string $body, ?int $userId = null, string $type = CodeReviewComment::TYPE_COMMENT, array $metadata = [], ?int $revisionId = null, ?string $rubricKey = null): CodeReviewComment
    {
        return $this->comments()->create([
            'code_review_revision_id' => $revisionId,
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'rubric_key' => $rubricKey,
            'body' => $body,
            'metadata' => $metadata ?: null,
        ]);
    }

    public function requestRevision(array $reviewData, ?int $reviewerId = null): self
    {
        $rubricScores = $reviewData['rubric_scores'] ?? [];
        $total = static::calculateRubricTotal($rubricScores);

        $this->update([
            'status' => self::STATUS_REVISION_REQUESTED,
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'review_feedback' => $reviewData['feedback'] ?? $this->review_feedback,
            'suggestions' => $reviewData['suggestions'] ?? $this->suggestions,
            'improvement_areas' => $reviewData['improvement_areas'] ?? $this->improvement_areas,
            'rubric_scores' => $rubricScores,
            'rubric_notes' => $reviewData['rubric_notes'] ?? [],
            'rubric_total_score' => $total,
            'code_quality_score' => round($total / 10, 2),
            'approval_status' => self::APPROVAL_NEEDS_REVISION,
            'approval_notes' => $reviewData['approval_notes'] ?? null,
            'started_review_at' => $this->started_review_at ?? now(),
        ]);

        return $this;
    }

    public function approveWithReview(array $reviewData, ?int $reviewerId = null): self
    {
        $rubricScores = $reviewData['rubric_scores'] ?? [];
        $total = static::calculateRubricTotal($rubricScores);
        $reviewTime = $this->started_review_at
            ? $this->started_review_at->diffInMinutes(now())
            : null;

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'started_review_at' => $this->started_review_at ?? now(),
            'completed_at' => now(),
            'review_feedback' => $reviewData['feedback'] ?? null,
            'suggestions' => $reviewData['suggestions'] ?? [],
            'improvement_areas' => $reviewData['improvement_areas'] ?? [],
            'rubric_scores' => $rubricScores,
            'rubric_notes' => $reviewData['rubric_notes'] ?? [],
            'rubric_total_score' => $total,
            'code_quality_score' => round($total / 10, 2),
            'approval_status' => self::APPROVAL_APPROVED,
            'approval_notes' => $reviewData['approval_notes'] ?? null,
            'approved_at' => now(),
            'approved_by' => $reviewerId ?? auth()->id(),
            'certificate_evidence' => $reviewData['certificate_evidence'] ?? $this->certificate_evidence,
            'project_evidence' => $reviewData['project_evidence'] ?? $this->project_evidence,
            'actual_review_time' => $reviewTime,
        ]);

        return $this;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true)->orWhere('priority', self::PRIORITY_URGENT);
    }
}
