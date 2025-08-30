<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'resume_path',
        'additional_documents',
        'custom_responses',
        'match_score',
        'status',
        'rejection_reason',
        'interview_schedule',
        'feedback',
        'rating',
        'viewed_at',
        'responded_at',
        'activity_log'
    ];

    protected $casts = [
        'additional_documents' => 'array',
        'custom_responses' => 'array',
        'interview_schedule' => 'array',
        'feedback' => 'array',
        'activity_log' => 'array',
        'viewed_at' => 'datetime',
        'responded_at' => 'datetime',
        'match_score' => 'decimal:2',
        'rating' => 'decimal:1'
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'resume_url'
    ];

    // Relationships
    public function job()
    {
        return $this->belongsTo(JobPortal::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function resumeUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->resume_path ? Storage::url($this->resume_path) : null
        );
    }

    public function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                JobPortal::APPLICATION_PENDING => 'Pending Review',
                JobPortal::APPLICATION_REVIEWING => 'Under Review',
                JobPortal::APPLICATION_SHORTLISTED => 'Shortlisted',
                JobPortal::APPLICATION_INTERVIEWED => 'Interviewed',
                JobPortal::APPLICATION_OFFERED => 'Job Offered',
                JobPortal::APPLICATION_HIRED => 'Hired',
                JobPortal::APPLICATION_REJECTED => 'Not Selected',
                default => 'Unknown'
            }
        );
    }

    public function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                JobPortal::APPLICATION_PENDING => 'yellow',
                JobPortal::APPLICATION_REVIEWING => 'blue',
                JobPortal::APPLICATION_SHORTLISTED => 'purple',
                JobPortal::APPLICATION_INTERVIEWED => 'indigo',
                JobPortal::APPLICATION_OFFERED => 'green',
                JobPortal::APPLICATION_HIRED => 'green',
                JobPortal::APPLICATION_REJECTED => 'red',
                default => 'gray'
            }
        );
    }

    // Methods
    public function markAsViewed()
    {
        $this->update(['viewed_at' => now()]);
    }

    public function updateStatus($status, $reason = null)
    {
        $this->update([
            'status' => $status,
            'rejection_reason' => $status === JobPortal::APPLICATION_REJECTED ? $reason : null,
            'responded_at' => now()
        ]);
    }

    public function addActivityLog($activity, $data = [])
    {
        $logs = $this->activity_log ?? [];
        $logs[] = [
            'activity' => $activity,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id()
        ];
        
        $this->update(['activity_log' => $logs]);
    }

    // Scopes
    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopePending($query)
    {
        return $query->where('status', JobPortal::APPLICATION_PENDING);
    }

    public function scopeShortlisted($query)
    {
        return $query->where('status', JobPortal::APPLICATION_SHORTLISTED);
    }

    public function scopeHired($query)
    {
        return $query->where('status', JobPortal::APPLICATION_HIRED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', JobPortal::APPLICATION_REJECTED);
    }
    public function isExpiredStatus()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}