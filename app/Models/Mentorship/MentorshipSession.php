<?php

namespace App\Models\Mentorship;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use Illuminate\Support\Facades\DB;

class MentorshipSession extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_GENERAL = 'general';
    const TYPE_CODE_REVIEW = 'code_review';
    const TYPE_PROJECT_GUIDANCE = 'project_guidance';
    const TYPE_CAREER_ADVICE = 'career_advice';

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_MISSED = 'missed';

    protected $fillable = [
        'mentorship_id',
        'title',
        'description',
        'type',
        'format',
        'status',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',
        'agenda',
        'materials',
        'session_notes',
        'action_items',
        'mentor_feedback',
        'mentee_feedback',
        'mentor_rating',
        'mentee_rating',
        'meeting_link',
        'recording_url',
        'attachments',
        'is_billable',
        'session_cost',
        'payment_status',
        'metadata'
    ];

    protected $casts = [
        'materials' => 'array',
        'action_items' => 'array',
        'attachments' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_billable' => 'boolean',
        'session_cost' => 'decimal:2',
        'mentor_rating' => 'decimal:2',
        'mentee_rating' => 'decimal:2'
    ];

    public function mentorship()
    {
        return $this->belongsTo(Mentorship::class);
    }

    public function start()
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'started_at' => now()
        ]);

        return $this;
    }

    public function complete()
    {
        $duration = $this->started_at ? $this->started_at->diffInMinutes(now()) : 0;

        return $this->completeWithOperations([
            'duration_minutes' => $duration ?: ($this->duration_minutes ?? 60),
        ]);
    }

    public function completeWithOperations(array $data = []): self
    {
        return DB::transaction(function () use ($data) {
            $wasCompleted = $this->status === self::STATUS_COMPLETED;

            $this->update([
                'status' => self::STATUS_COMPLETED,
                'started_at' => $this->started_at ?? ($data['started_at'] ?? now()),
                'ended_at' => $data['ended_at'] ?? now(),
                'duration_minutes' => $data['duration_minutes'] ?? $this->duration_minutes ?? 60,
                'session_notes' => $data['session_notes'] ?? $this->session_notes,
                'action_items' => $data['action_items'] ?? $this->action_items,
                'mentor_feedback' => $data['mentor_feedback'] ?? $this->mentor_feedback,
                'mentee_feedback' => $data['mentee_feedback'] ?? $this->mentee_feedback,
                'mentor_rating' => $data['mentor_rating'] ?? $this->mentor_rating,
                'mentee_rating' => $data['mentee_rating'] ?? $this->mentee_rating,
                'metadata' => array_merge($this->metadata ?? [], $data['metadata'] ?? []),
            ]);

            if (! $wasCompleted) {
                $this->mentorship->mentor->mentorProfile?->increment('total_sessions');
            }

            $this->payoutMentorIfNeeded();

            return $this->refresh();
        });
    }

    public function payoutMentorIfNeeded(): ?WalletTransaction
    {
        if (! $this->is_billable || (float) $this->session_cost <= 0 || $this->payment_status === 'paid') {
            return null;
        }

        $wallet = Wallet::getOrCreateWallet($this->mentorship->mentor_id, Wallet::TYPE_INSTRUCTOR);

        $transaction = $wallet->credit(
            (float) $this->session_cost,
            WalletTransaction::CATEGORY_INSTRUCTOR_EARNING,
            "Mentorship session payout: {$this->title}",
            $this,
            [
                'mentorship_id' => $this->mentorship_id,
                'session_id' => $this->id,
                'mentee_id' => $this->mentorship->mentee_id,
                'mentor_id' => $this->mentorship->mentor_id,
            ]
        );

        $metadata = $this->metadata ?? [];
        data_set($metadata, 'payout.transaction_id', $transaction->id);
        data_set($metadata, 'payout.paid_at', now()->toIso8601String());

        $this->forceFill([
            'payment_status' => 'paid',
            'metadata' => $metadata,
        ])->save();

        return $transaction;
    }
}
