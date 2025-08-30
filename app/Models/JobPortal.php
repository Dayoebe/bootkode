<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobPortal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_portal'; // Explicitly set table name

    // Employment Types
    const EMPLOYMENT_FULL_TIME = 'full-time';
    const EMPLOYMENT_PART_TIME = 'part-time';
    const EMPLOYMENT_CONTRACT = 'contract';
    const EMPLOYMENT_TEMPORARY = 'temporary';
    const EMPLOYMENT_INTERNSHIP = 'internship';
    const EMPLOYMENT_FREELANCE = 'freelance';

    // Work Types
    const WORK_ON_SITE = 'on-site';
    const WORK_REMOTE = 'remote';
    const WORK_HYBRID = 'hybrid';

    // Experience Levels
    const EXPERIENCE_ENTRY = 'entry';
    const EXPERIENCE_JUNIOR = 'junior';
    const EXPERIENCE_MID = 'mid';
    const EXPERIENCE_SENIOR = 'senior';
    const EXPERIENCE_EXECUTIVE = 'executive';
    const EXPERIENCE_DIRECTOR = 'director';

    // Job Status
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_EXPIRED = 'expired';
    const STATUS_FILLED = 'filled';
    const STATUS_CANCELLED = 'cancelled';

    // Application Status
    const APPLICATION_PENDING = 'pending';
    const APPLICATION_REVIEWING = 'reviewing';
    const APPLICATION_SHORTLISTED = 'shortlisted';
    const APPLICATION_INTERVIEWED = 'interviewed';
    const APPLICATION_OFFERED = 'offered';
    const APPLICATION_HIRED = 'hired';
    const APPLICATION_REJECTED = 'rejected';

    // Premium Features
    const PREMIUM_FEATURES = [
        'featured_listing',
        'urgent_hiring',
        'highlight_job',
        'premium_placement',
        'ai_screening',
        'video_interviews',
        'advanced_analytics',
        'social_media_boost',
        'candidate_matching',
        'priority_support'
    ];

    protected $fillable = [
        'posted_by',
        'title',
        'slug',
        'description',
        'requirements',
        'responsibilities',
        'benefits',
        'company_description',
        'company_name',
        'company_logo',
        'company_website',
        'company_size',
        'company_industry',
        'company_social_links',
        'employment_type',
        'work_type',
        'experience_level',
        'category',
        'skills_required',
        'tags',
        'location',
        'country',
        'state',
        'city',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'salary_negotiable',
        'hide_salary',
        'application_method',
        'application_email',
        'application_url',
        'application_phone',
        'application_instructions',
        'required_documents',
        'application_deadline',
        'start_date',
        'positions_available',
        'is_premium',
        'is_featured',
        'is_urgent',
        'highlight_job',
        'premium_features',
        'featured_until',
        'premium_until',
        'status',
        'is_public',
        'auto_expire',
        'expires_at',
        'views_count',
        'applications_count',
        'shortlisted_count',
        'interview_count',
        'hired_count',
        'application_conversion_rate',
        'ai_keywords',
        'ai_match_score',
        'screening_questions',
        'enable_ai_screening',
        'auto_rejection_criteria',
        'meta_title',
        'meta_description',
        'structured_data',
        'custom_fields',
        'interview_process',
        'allow_remote_interview',
        'video_interview_settings',
        'referral_bonus',
        'diversity_hiring',
        'diversity_preferences',
        'internal_notes',
        'hiring_manager',
        'team_members',
        'department',
        'job_code',
        'priority_score',
        'integration_settings',
        'sync_with_external',
        'external_job_id',
        'webhook_urls',
    ];

    protected $casts = [
        'company_social_links' => 'array',
        'skills_required' => 'array',
        'tags' => 'array',
        'required_documents' => 'array',
        'premium_features' => 'array',
        'ai_keywords' => 'array',
        'screening_questions' => 'array',
        'auto_rejection_criteria' => 'array',
        'structured_data' => 'array',
        'custom_fields' => 'array',
        'interview_process' => 'array',
        'video_interview_settings' => 'array',
        'diversity_preferences' => 'array',
        'internal_notes' => 'array',
        'team_members' => 'array',
        'integration_settings' => 'array',
        'webhook_urls' => 'array',
        'application_deadline' => 'datetime',
        'start_date' => 'datetime',
        'expires_at' => 'datetime',
        'featured_until' => 'datetime',
        'premium_until' => 'datetime',
        'is_premium' => 'boolean',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'highlight_job' => 'boolean',
        'is_public' => 'boolean',
        'auto_expire' => 'boolean',
        'salary_negotiable' => 'boolean',
        'hide_salary' => 'boolean',
        'enable_ai_screening' => 'boolean',
        'allow_remote_interview' => 'boolean',
        'diversity_hiring' => 'boolean',
        'sync_with_external' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'ai_match_score' => 'decimal:2',
        'application_conversion_rate' => 'decimal:2',
    ];

    protected $appends = [
        'employment_type_label',
        'work_type_label',
        'experience_level_label',
        'status_label',
        'salary_range_formatted',
        'company_logo_url',
        'is_expired',
        'days_until_deadline',
        'application_rate',
        'is_recently_posted',
        'location_formatted'
    ];

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = $job->generateUniqueSlug($job->title);
            }
            
            if (empty($job->expires_at)) {
                $job->expires_at = now()->addDays(30);
            }
        });

        static::updating(function ($job) {
            if ($job->isDirty('title')) {
                $job->slug = $job->generateUniqueSlug($job->title);
            }
        });

        static::saved(function ($job) {
            $job->updateJobCategoryCount();
            $job->generateAIKeywords();
        });
    }

    // Relationships
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    public function saves()
    {
        return $this->hasMany(JobSave::class, 'job_id');
    }

    public function category_relation()
    {
        return $this->belongsTo(JobCategory::class, 'category', 'slug');
    }

    public function pendingApplications()
    {
        return $this->applications()->where('status', self::APPLICATION_PENDING);
    }

    public function shortlistedApplications()
    {
        return $this->applications()->where('status', self::APPLICATION_SHORTLISTED);
    }

    public function interviewedApplications()
    {
        return $this->applications()->where('status', self::APPLICATION_INTERVIEWED);
    }

    public function hiredApplications()
    {
        return $this->applications()->where('status', self::APPLICATION_HIRED);
    }

    // Slug generation
    public function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    // Status Methods
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && $this->is_public && !$this->is_expired;
    }

    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isExpiredStatus()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isFilled()
    {
        return $this->status === self::STATUS_FILLED;
    }

    public function activate()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'is_public' => true
        ]);
    }

    public function deactivate()
    {
        $this->update(['status' => self::STATUS_PAUSED]);
    }

    public function markAsFilled()
    {
        $this->update(['status' => self::STATUS_FILLED]);
    }

    // Premium Features Methods
    public function hasPremiumFeature($feature)
    {
        if (!$this->is_premium) {
            return false;
        }

        return in_array($feature, $this->premium_features ?? []);
    }

    public function enablePremiumFeature($feature, $duration = null)
    {
        if (!in_array($feature, self::PREMIUM_FEATURES)) {
            return false;
        }

        $features = $this->premium_features ?? [];
        if (!in_array($feature, $features)) {
            $features[] = $feature;
        }

        $updateData = [
            'premium_features' => $features,
            'is_premium' => true
        ];

        if ($duration) {
            $updateData['premium_until'] = now()->addDays($duration);
        }

        if ($feature === 'featured_listing') {
            $updateData['is_featured'] = true;
            $updateData['featured_until'] = now()->addDays($duration ?? 7);
        }

        $this->update($updateData);
        return true;
    }

    public function isPremiumActive()
    {
        return $this->is_premium && (!$this->premium_until || $this->premium_until->isFuture());
    }

    public function isFeaturedActive()
    {
        return $this->is_featured && (!$this->featured_until || $this->featured_until->isFuture());
    }

    // Application Management
    public function acceptApplication($applicationId, $notes = null)
    {
        $application = $this->applications()->find($applicationId);
        if ($application) {
            $application->update([
                'status' => self::APPLICATION_SHORTLISTED,
                'feedback' => $notes ? ['notes' => $notes] : null
            ]);
            
            $this->increment('shortlisted_count');
            return true;
        }
        return false;
    }

    public function rejectApplication($applicationId, $reason = null)
    {
        $application = $this->applications()->find($applicationId);
        if ($application) {
            $application->update([
                'status' => self::APPLICATION_REJECTED,
                'rejection_reason' => $reason
            ]);
            return true;
        }
        return false;
    }

    // Analytics Methods
    public function calculateConversionRate()
    {
        if ($this->views_count == 0) {
            return 0;
        }

        return round(($this->applications_count / $this->views_count) * 100, 2);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getAnalytics()
    {
        return [
            'views' => $this->views_count,
            'applications' => $this->applications_count,
            'shortlisted' => $this->shortlisted_count,
            'interviewed' => $this->interview_count,
            'hired' => $this->hired_count,
            'conversion_rate' => $this->application_conversion_rate,
            'saves' => $this->saves()->count(),
            'application_sources' => $this->getApplicationSources(),
            'daily_views' => $this->getDailyViews(),
            'application_timeline' => $this->getApplicationTimeline()
        ];
    }

    private function getApplicationSources()
    {
        return ['direct' => 100]; // Placeholder - implement based on tracking needs
    }

    private function getDailyViews()
    {
        return []; // Placeholder - implement analytics tracking
    }

    private function getApplicationTimeline()
    {
        return []; // Placeholder - implement timeline tracking
    }

    // AI and Matching
    public function generateAIKeywords()
    {
        $text = implode(' ', [
            $this->title,
            $this->description,
            implode(' ', $this->skills_required ?? []),
            $this->category
        ]);

        $keywords = $this->extractKeywords($text);
        $this->updateQuietly(['ai_keywords' => $keywords]);
    }

    public function calculateMatchScore($userProfile)
    {
        $score = 0;
        $maxScore = 100;

        // Skills matching (40 points)
        $userSkills = $userProfile['skills'] ?? [];
        $requiredSkills = $this->skills_required ?? [];
        
        if (!empty($requiredSkills) && !empty($userSkills)) {
            $matchingSkills = array_intersect(array_map('strtolower', $userSkills), array_map('strtolower', $requiredSkills));
            $score += (count($matchingSkills) / count($requiredSkills)) * 40;
        }

        // Experience level matching (25 points)
        $userExperience = $userProfile['experience_level'] ?? '';
        if ($userExperience === $this->experience_level) {
            $score += 25;
        } elseif ($this->isExperienceLevelCompatible($userExperience)) {
            $score += 15;
        }

        // Location matching (15 points)
        $userLocation = $userProfile['location'] ?? '';
        if ($this->work_type === self::WORK_REMOTE) {
            $score += 15;
        } elseif (stripos($userLocation, $this->city) !== false || stripos($userLocation, $this->state) !== false) {
            $score += 15;
        }

        // Industry/Category matching (20 points)
        $userIndustry = $userProfile['industry'] ?? '';
        if (stripos($userIndustry, $this->category) !== false || stripos($this->category, $userIndustry) !== false) {
            $score += 20;
        }

        return min($score, $maxScore);
    }

    // Helper Methods
    private function extractKeywords($text)
    {
        $commonWords = ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'as', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'should', 'could', 'can', 'may', 'might', 'must', 'shall', 'this', 'that', 'these', 'those'];
        
        $words = str_word_count(strtolower($text), 1);
        $words = array_filter($words, function($word) use ($commonWords) {
            return strlen($word) > 3 && !in_array($word, $commonWords);
        });

        $wordCounts = array_count_values($words);
        arsort($wordCounts);

        return array_keys(array_slice($wordCounts, 0, 20));
    }

    private function isExperienceLevelCompatible($userLevel)
    {
        $hierarchy = [
            self::EXPERIENCE_ENTRY => 1,
            self::EXPERIENCE_JUNIOR => 2,
            self::EXPERIENCE_MID => 3,
            self::EXPERIENCE_SENIOR => 4,
            self::EXPERIENCE_EXECUTIVE => 5,
            self::EXPERIENCE_DIRECTOR => 6
        ];

        $jobLevel = $hierarchy[$this->experience_level] ?? 3;
        $candidateLevel = $hierarchy[$userLevel] ?? 3;

        return abs($jobLevel - $candidateLevel) <= 1;
    }

    private function updateJobCategoryCount()
    {
        $category = JobCategory::where('slug', $this->category)->first();
        if ($category) {
            $category->update([
                'jobs_count' => self::where('category', $this->category)->where('status', self::STATUS_ACTIVE)->count()
            ]);
        }
    }

    // Accessors
    public function employmentTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->employment_type) {
                self::EMPLOYMENT_FULL_TIME => 'Full Time',
                self::EMPLOYMENT_PART_TIME => 'Part Time',
                self::EMPLOYMENT_CONTRACT => 'Contract',
                self::EMPLOYMENT_TEMPORARY => 'Temporary',
                self::EMPLOYMENT_INTERNSHIP => 'Internship',
                self::EMPLOYMENT_FREELANCE => 'Freelance',
                default => 'Full Time'
            }
        );
    }

    public function workTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->work_type) {
                self::WORK_ON_SITE => 'On-site',
                self::WORK_REMOTE => 'Remote',
                self::WORK_HYBRID => 'Hybrid',
                default => 'On-site'
            }
        );
    }

    public function experienceLevelLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->experience_level) {
                self::EXPERIENCE_ENTRY => 'Entry Level',
                self::EXPERIENCE_JUNIOR => 'Junior Level',
                self::EXPERIENCE_MID => 'Mid Level',
                self::EXPERIENCE_SENIOR => 'Senior Level',
                self::EXPERIENCE_EXECUTIVE => 'Executive',
                self::EXPERIENCE_DIRECTOR => 'Director',
                default => 'Mid Level'
            }
        );
    }

    public function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                self::STATUS_DRAFT => 'Draft',
                self::STATUS_ACTIVE => 'Active',
                self::STATUS_PAUSED => 'Paused',
                self::STATUS_EXPIRED => 'Expired',
                self::STATUS_FILLED => 'Filled',
                self::STATUS_CANCELLED => 'Cancelled',
                default => 'Unknown'
            }
        );
    }

    public function salaryRangeFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->hide_salary || (!$this->salary_min && !$this->salary_max)) {
                    return $this->salary_negotiable ? 'Negotiable' : 'Not disclosed';
                }

                $currency = $this->salary_currency === 'NGN' ? '₦' : $this->salary_currency . ' ';
                $period = $this->salary_period === 'monthly' ? '/month' : '/' . $this->salary_period;

                if ($this->salary_min && $this->salary_max) {
                    return $currency . number_format($this->salary_min) . ' - ' . $currency . number_format($this->salary_max) . $period;
                } elseif ($this->salary_min) {
                    return 'From ' . $currency . number_format($this->salary_min) . $period;
                } elseif ($this->salary_max) {
                    return 'Up to ' . $currency . number_format($this->salary_max) . $period;
                }

                return 'Not disclosed';
            }
        );
    }

    public function companyLogoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->company_logo) {
                    return Storage::url($this->company_logo);
                }
                
                return 'https://ui-avatars.com/api/?name=' . urlencode($this->company_name) . '&color=7F9CF5&background=EBF4FF&size=80';
            }
        );
    }

    public function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->expires_at && $this->expires_at->isPast()
        );
    }

    public function daysUntilDeadline(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->application_deadline) {
                    return null;
                }

                $days = now()->diffInDays($this->application_deadline, false);
                
                if ($days < 0) {
                    return 'Expired';
                } elseif ($days === 0) {
                    return 'Today';
                } elseif ($days === 1) {
                    return '1 day left';
                } else {
                    return $days . ' days left';
                }
            }
        );
    }

    public function applicationRate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->views_count > 0 ? round(($this->applications_count / $this->views_count) * 100, 1) : 0
        );
    }

    public function isRecentlyPosted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at->isAfter(now()->subDays(7))
        );
    }

    public function locationFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->work_type === self::WORK_REMOTE) {
                    return 'Remote';
                }

                $parts = array_filter([$this->city, $this->state]);
                return implode(', ', $parts) ?: $this->location;
            }
        );
    }

    // Helper methods for status colors and icons
    public function getStatusColor()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'green',
            self::STATUS_DRAFT => 'yellow',
            self::STATUS_PAUSED => 'orange',
            self::STATUS_EXPIRED => 'red',
            self::STATUS_FILLED => 'blue',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray'
        };
    }

    public function getExperienceColor()
    {
        return match ($this->experience_level) {
            self::EXPERIENCE_ENTRY => 'green',
            self::EXPERIENCE_JUNIOR => 'blue',
            self::EXPERIENCE_MID => 'indigo',
            self::EXPERIENCE_SENIOR => 'purple',
            self::EXPERIENCE_EXECUTIVE => 'red',
            self::EXPERIENCE_DIRECTOR => 'gray',
            default => 'gray'
        };
    }

    public function getWorkTypeIcon()
    {
        return match ($this->work_type) {
            self::WORK_REMOTE => '🏠',
            self::WORK_HYBRID => '🔄',
            self::WORK_ON_SITE => '🏢',
            default => '🏢'
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('is_public', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function ($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true)
                    ->where(function ($q) {
                        $q->whereNull('premium_until')
                          ->orWhere('premium_until', '>', now());
                    });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where(function ($q) use ($location) {
            $q->where('city', 'like', "%{$location}%")
              ->orWhere('state', 'like', "%{$location}%")
              ->orWhere('location', 'like', "%{$location}%");
        });
    }

    public function scopeRemote($query)
    {
        return $query->where('work_type', self::WORK_REMOTE);
    }

    public function scopeBySalaryRange($query, $min = null, $max = null)
    {
        if ($min) {
            $query->where('salary_min', '>=', $min);
        }
        
        if ($max) {
            $query->where('salary_max', '<=', $max);
        }

        return $query;
    }

    public function scopeByExperience($query, $level)
    {
        return $query->where('experience_level', $level);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('application_deadline', '<=', now()->addDays($days))
                    ->where('application_deadline', '>', now());
    }

    public function scopeWithApplications($query)
    {
        return $query->where('applications_count', '>', 0);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    // Search scope with full-text search
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->whereFullText(['title', 'description', 'company_name'], $term)
              ->orWhere('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('company_name', 'like', "%{$term}%")
              ->orWhereJsonContains('skills_required', $term)
              ->orWhereJsonContains('tags', $term);
        });
    }
}