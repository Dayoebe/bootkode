<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'jobs_count'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function jobs()
    {
        return $this->hasMany(JobPortal::class, 'category', 'slug');
    }

    public function activeJobs()
    {
        return $this->hasMany(JobPortal::class, 'category', 'slug')->active();
    }

    // Methods
    public function updateJobsCount()
    {
        $this->update([
            'jobs_count' => $this->activeJobs()->count()
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

// JobAlert Model (for future implementation)
class JobAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'keywords',
        'location',
        'category',
        'employment_type',
        'work_type',
        'experience_level',
        'salary_min',
        'salary_max',
        'is_active',
        'frequency',
        'last_sent_at'
    ];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function checkForMatches()
    {
        $query = JobPortal::active();

        if ($this->keywords) {
            $query->search(implode(' ', $this->keywords));
        }

        if ($this->category) {
            $query->byCategory($this->category);
        }

        if ($this->location) {
            $query->byLocation($this->location);
        }

        if ($this->employment_type) {
            $query->byEmploymentType($this->employment_type);
        }

        if ($this->work_type) {
            $query->where('work_type', $this->work_type);
        }

        if ($this->experience_level) {
            $query->byExperience($this->experience_level);
        }

        if ($this->salary_min || $this->salary_max) {
            $query->bySalaryRange($this->salary_min, $this->salary_max);
        }

        // Only get jobs posted since last alert
        if ($this->last_sent_at) {
            $query->where('created_at', '>', $this->last_sent_at);
        }

        return $query->get();
    }

    public function sendAlert($jobs)
    {
        // Logic to send email alert would go here
        $this->update(['last_sent_at' => now()]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('frequency', 'daily')
                    ->where(function ($subq) {
                        $subq->whereNull('last_sent_at')
                            ->orWhere('last_sent_at', '<=', now()->subDay());
                    })
                    ->orWhere('frequency', 'weekly')
                    ->where(function ($subq) {
                        $subq->whereNull('last_sent_at')
                            ->orWhere('last_sent_at', '<=', now()->subWeek());
                    });
            });
    }
}