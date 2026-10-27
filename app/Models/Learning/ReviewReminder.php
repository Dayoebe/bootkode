<?php

namespace App\Models\Learning;

use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model; // UPDATED

class ReviewReminder extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'reminder_count',
        'last_reminded_at',
        'completed_at',
        'unsubscribed',
        'unsubscribed_at',
    ];

    protected $casts = [
        'reminder_count' => 'integer',
        'last_reminded_at' => 'datetime',
        'completed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'unsubscribed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if reminder should be sent
     */
    public function shouldSendReminder(): bool
    {
        $due = $this->getNextReminderDate();

        return $due && $due->lte(now());
    }

    /**
     * Mark reminder as sent
     */
    public function markAsSent()
    {
        $this->increment('reminder_count');
        $this->update(['last_reminded_at' => now()]);
    }

    /**
     * Mark as completed (student left review)
     */
    public function markAsCompleted()
    {
        $this->update(['completed_at' => now()]);
    }

    /**
     * Unsubscribe from reminders
     */
    public function unsubscribe()
    {
        $this->update([
            'unsubscribed' => true,
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * Get next reminder date
     */
    public function getNextReminderDate(): ?Carbon
    {
        $service = app(\App\Services\ReviewReminderService::class);
        $enrollment = $service->eligibleEnrollments()->where('user_id', $this->user_id)->where('course_id', $this->course_id)->first();

        return $enrollment && $this->user ? $service->nextDue($this->user, $enrollment, $this) : null;
    }
}
