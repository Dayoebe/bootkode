<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulkEnrollmentBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'name',
        'description',
        'file_path',
        'original_filename',
        'status',
        'total_records',
        'processed_records',
        'successful_enrollments',
        'failed_enrollments',
        'errors',
        'courses',
        'settings',
        'started_at',
        'completed_at',
        'created_by'
    ];

    protected $casts = [
        'errors' => 'array',
        'courses' => 'array',
        'settings' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_records' => 'integer',
        'processed_records' => 'integer',
        'successful_enrollments' => 'integer',
        'failed_enrollments' => 'integer'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            if (!$batch->status) {
                $batch->status = 'pending';
            }
        });
    }

    // Relationships
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'bulk_batch_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function start()
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now()
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function fail($error = null)
    {
        $errors = $this->errors ?? [];
        if ($error) {
            $errors[] = [
                'message' => $error,
                'timestamp' => now()->toISOString()
            ];
        }

        $this->update([
            'status' => 'failed',
            'errors' => $errors,
            'completed_at' => now()
        ]);
    }

    public function addError($error, $row = null)
    {
        $errors = $this->errors ?? [];
        $errors[] = [
            'message' => $error,
            'row' => $row,
            'timestamp' => now()->toISOString()
        ];

        $this->update(['errors' => $errors]);
    }

    public function updateProgress($processed, $successful, $failed)
    {
        $this->update([
            'processed_records' => $processed,
            'successful_enrollments' => $successful,
            'failed_enrollments' => $failed
        ]);
    }

    public function getProgressPercentage()
    {
        if ($this->total_records == 0) {
            return 0;
        }

        return round(($this->processed_records / $this->total_records) * 100, 1);
    }

    public function getSuccessRate()
    {
        if ($this->processed_records == 0) {
            return 0;
        }

        return round(($this->successful_enrollments / $this->processed_records) * 100, 1);
    }

    public function getDurationInMinutes()
    {
        if (!$this->started_at) {
            return 0;
        }

        $endTime = $this->completed_at ?? now();
        return $this->started_at->diffInMinutes($endTime);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = $this->getDurationInMinutes();
        
        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes > 0) {
            return $hours . 'h ' . $remainingMinutes . 'm';
        }

        return $hours . 'h';
    }

    public function getCourseNamesAttribute()
    {
        if (empty($this->courses)) {
            return 'No courses selected';
        }

        $courseIds = $this->courses;
        $courses = Course::whereIn('id', $courseIds)->pluck('title');
        
        return $courses->join(', ');
    }
}