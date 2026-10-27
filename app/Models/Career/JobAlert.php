<?php

namespace App\Models\Career;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User;

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
        'last_sent_at', 'salary_currency', 'filter_hash'
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
    public function matchingJobs(bool $unseenOnly = true)
    {
        $query = JobPortal::active();
        if ($this->keywords) $query->search(implode(' ', $this->keywords));
        if ($this->category) $query->byCategory($this->category);
        if ($this->location) $query->byLocation($this->location);
        if ($this->employment_type) $query->byEmploymentType($this->employment_type);
        if ($this->work_type) $query->where('work_type', $this->work_type);
        if ($this->experience_level) $query->byExperience($this->experience_level);
        if ($this->salary_min !== null || $this->salary_max !== null) {
            $query->where('hide_salary', false)->where('salary_currency', $this->salary_currency)
                ;
            if ($this->salary_min !== null) $query->where('salary_min', '>=', $this->salary_min);
            if ($this->salary_max !== null) $query->where('salary_max', '<=', $this->salary_max);
        }
        $query->where(function ($q) { $q->whereNull('application_deadline')->orWhere('application_deadline', '>=', now()); });
        if ($unseenOnly) $query->whereNotExists(function ($q) { $q->selectRaw('1')->from('job_alert_deliveries')->where('job_alert_id', $this->id)->whereColumn('job_id', 'job_portal.id'); });
        return $query;
    }
    public function checkForMatches() { return $this->matchingJobs()->get(); }
    public function sendAlert($jobs = null) { return app(\App\Services\JobAlertService::class)->deliver($this->id); }

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
