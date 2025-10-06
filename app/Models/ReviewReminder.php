<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReviewReminder extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'reminder_count',
        'last_reminded_at',
        'completed_at',
        'unsubscribed',
        'unsubscribed_at'
    ];

    protected $casts = [
        'last_reminded_at' => 'datetime',
        'completed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'unsubscribed' => 'boolean'
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
        // Don't send if unsubscribed
        if ($this->unsubscribed) {
            return false;
        }

        // Don't send if already completed (reviewed)
        if ($this->completed_at) {
            return false;
        }

        // Max 3 reminders
        if ($this->reminder_count >= 3) {
            return false;
        }

        // Check time since last reminder
        if ($this->last_reminded_at) {
            $daysSinceLastReminder = $this->last_reminded_at->diffInDays(now());
            
            // Reminder schedule: First reminder after 7 days, second after 14 days, third after 30 days
            $requiredDays = match($this->reminder_count) {
                0 => 7,
                1 => 14,
                2 => 30,
                default => 999
            };

            return $daysSinceLastReminder >= $requiredDays;
        }

        return true;
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
            'unsubscribed_at' => now()
        ]);
    }

    /**
     * Get next reminder date
     */
    public function getNextReminderDate(): ?Carbon
    {
        if (!$this->shouldSendReminder()) {
            return null;
        }

        if (!$this->last_reminded_at) {
            return now();
        }

        $daysToAdd = match($this->reminder_count) {
            0 => 7,
            1 => 14,
            2 => 30,
            default => null
        };

        return $daysToAdd ? $this->last_reminded_at->addDays($daysToAdd) : null;
    }
}