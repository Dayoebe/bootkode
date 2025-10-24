<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Institution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'institution_type',
        'logo',
        'website',
        'description',
        'status',
        'license_type',
        'max_users',
        'current_users',
        'license_start_date',
        'license_end_date',
        'admin_user_id',
        'billing_email',
        'billing_address',
        'settings',
        'whitelabel_settings',
        'api_key',
        'total_courses_accessed',
        'total_certificates_issued',
        'created_by',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'license_start_date' => 'datetime',
        'license_end_date' => 'datetime',
        'approved_at' => 'datetime',
        'settings' => 'array',
        'whitelabel_settings' => 'array',
        'max_users' => 'integer',
        'current_users' => 'integer',
        'total_courses_accessed' => 'integer',
        'total_certificates_issued' => 'integer'
    ];

    const INSTITUTION_TYPES = [
        'university' => 'University',
        'college' => 'College',
        'school' => 'School',
        'training_center' => 'Training Center',
        'corporate' => 'Corporate',
        'government' => 'Government',
        'non_profit' => 'Non-Profit',
        'other' => 'Other'
    ];

    const LICENSE_TYPES = [
        'basic' => 'Basic (100 users)',
        'standard' => 'Standard (500 users)',
        'premium' => 'Premium (1000 users)',
        'enterprise' => 'Enterprise (Unlimited)',
        'custom' => 'Custom'
    ];

    const STATUSES = [
        'pending' => 'Pending Approval',
        'active' => 'Active',
        'suspended' => 'Suspended',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($institution) {
            if (!$institution->slug) {
                $institution->slug = $institution->generateUniqueSlug($institution->name);
            }
            
            if (!$institution->api_key) {
                $institution->api_key = 'inst_' . Str::random(32);
            }

            if (!$institution->status) {
                $institution->status = 'pending';
            }

            // Set default whitelabel settings
            if (!$institution->whitelabel_settings) {
                $institution->whitelabel_settings = [
                    'platform_name' => $institution->name,
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#1E40AF',
                    'logo_url' => null,
                    'favicon_url' => null,
                    'custom_domain' => null,
                    'hide_powered_by' => false,
                    'custom_css' => null,
                    'email_template_header' => null,
                    'email_template_footer' => null
                ];
            }

            // Set default settings
            if (!$institution->settings) {
                $institution->settings = [
                    'allow_self_registration' => true,
                    'require_approval' => false,
                    'auto_enroll_courses' => [],
                    'notification_settings' => [
                        'new_user_notifications' => true,
                        'course_completion_notifications' => true,
                        'certificate_notifications' => true
                    ],
                    'branding' => [
                        'show_institution_name' => true,
                        'show_institution_logo' => true
                    ]
                ];
            }
        });

        static::updating(function ($institution) {
            if ($institution->isDirty('name') && !$institution->isDirty('slug')) {
                $institution->slug = $institution->generateUniqueSlug($institution->name);
            }
        });
    }

    // Relationships
    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function users()
    {
        return $this->hasMany(InstitutionUser::class);
    }

    public function enrollments()
    {
        return $this->hasManyThrough(
            CourseEnrollment::class,
            InstitutionUser::class,
            'institution_id',
            'user_id',
            'id',
            'user_id'
        );
    }

    public function bulkEnrollments()
    {
        return $this->hasMany(BulkEnrollmentBatch::class);
    }

    public function licenseHistory()
    {
        return $this->hasMany(InstitutionLicenseHistory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods
    public function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    // Status methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }
    public function isExpired()
    {
        return $this->status === 'expired' || ($this->license_end_date && $this->license_end_date->isPast());
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
    }

    public function suspend($reason = null)
    {
        $this->update(['status' => 'suspended']);
        
        // Log suspension reason
        if ($reason) {
            $this->licenseHistory()->create([
                'action' => 'suspended',
                'reason' => $reason,
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        }
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    // License management
    public function hasValidLicense()
    {
        if (!$this->license_start_date || !$this->license_end_date) {
            return false;
        }

        return $this->license_start_date->isPast() && 
               $this->license_end_date->isFuture() && 
               $this->isActive();
    }

    public function getDaysUntilExpiry()
    {
        if (!$this->license_end_date) {
            return null;
        }

        return now()->diffInDays($this->license_end_date, false);
    }

    public function isNearExpiry($days = 30)
    {
        $daysUntilExpiry = $this->getDaysUntilExpiry();
        return $daysUntilExpiry !== null && $daysUntilExpiry <= $days && $daysUntilExpiry > 0;
    }

    public function canAddMoreUsers()
    {
        if ($this->license_type === 'enterprise') {
            return true;
        }

        return $this->current_users < $this->max_users;
    }

    public function getUserCapacityPercentage()
    {
        if ($this->license_type === 'enterprise' || $this->max_users == 0) {
            return 0;
        }

        return round(($this->current_users / $this->max_users) * 100, 1);
    }

    public function updateUserCount()
    {
        $this->update([
            'current_users' => $this->users()->count()
        ]);
    }

    // Analytics methods
    public function getCourseCompletionRate()
    {
        $totalEnrollments = $this->enrollments()->count();
        if ($totalEnrollments === 0) {
            return 0;
        }

        $completedEnrollments = $this->enrollments()->where('is_completed', true)->count();
        return round(($completedEnrollments / $totalEnrollments) * 100, 1);
    }

    public function getAverageProgressPercentage()
    {
        return $this->enrollments()->avg('progress_percentage') ?? 0;
    }

    public function getMostPopularCourses($limit = 5)
    {
        return Course::whereIn('id', 
            $this->enrollments()
                ->select('course_id')
                ->groupBy('course_id')
                ->orderByRaw('COUNT(*) DESC')
                ->limit($limit)
                ->pluck('course_id')
        )->get();
    }

    public function getActiveUsersCount($days = 30)
    {
        return $this->users()
            ->whereHas('user', function($q) use ($days) {
                $q->where('last_login_at', '>=', now()->subDays($days));
            })
            ->count();
    }

    public function getMonthlyStats()
    {
        $startOfMonth = now()->startOfMonth();
        
        return [
            'new_enrollments' => $this->enrollments()
                ->where('enrolled_at', '>=', $startOfMonth)
                ->count(),
            'completed_courses' => $this->enrollments()
                ->where('completed_at', '>=', $startOfMonth)
                ->whereNotNull('completed_at')
                ->count(),
            'certificates_issued' => Certificate::whereIn('user_id',
                $this->users()->pluck('user_id')
            )->where('issued_date', '>=', $startOfMonth)->count(),
            'active_users' => $this->getActiveUsersCount(30)
        ];
    }

    // Whitelabel methods
    public function updateWhitelabelSetting($key, $value)
    {
        $settings = $this->whitelabel_settings ?? [];
        $settings[$key] = $value;
        $this->update(['whitelabel_settings' => $settings]);
    }

    public function getWhitelabelSetting($key, $default = null)
    {
        return $this->whitelabel_settings[$key] ?? $default;
    }

    public function hasCustomDomain()
    {
        return !empty($this->getWhitelabelSetting('custom_domain'));
    }

    public function getCustomDomainUrl()
    {
        $domain = $this->getWhitelabelSetting('custom_domain');
        return $domain ? 'https://' . $domain : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('license_end_date', '<=', now()->addDays($days))
                    ->where('license_end_date', '>', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('institution_type', $type);
    }

    // Accessors
    public function getInstitutionTypeNameAttribute()
    {
        return self::INSTITUTION_TYPES[$this->institution_type] ?? ucfirst($this->institution_type);
    }

    public function getLicenseTypeNameAttribute()
    {
        return self::LICENSE_TYPES[$this->license_type] ?? ucfirst($this->license_type);
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'pending' => 'yellow',
            'suspended' => 'red',
            'expired' => 'gray',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]);
        
        return implode(', ', $parts);
    }

    public function getLicenseStatusAttribute()
    {
        if (!$this->hasValidLicense()) {
            return 'Invalid';
        }

        $daysUntilExpiry = $this->getDaysUntilExpiry();
        
        if ($daysUntilExpiry <= 0) {
            return 'Expired';
        } elseif ($daysUntilExpiry <= 30) {
            return 'Expiring Soon';
        } else {
            return 'Active';
        }
    }

    // Static methods
    public static function getTypeStatistics()
    {
        return static::selectRaw('institution_type, COUNT(*) as count')
            ->groupBy('institution_type')
            ->pluck('count', 'institution_type')
            ->toArray();
    }

    public static function getStatusStatistics()
    {
        return static::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public static function getTotalActiveUsers()
    {
        return InstitutionUser::whereHas('institution', function($q) {
            $q->where('status', 'active');
        })->count();
    }

    public static function getTotalRevenue()
    {
        // This would connect to your billing system
        // For now, return a placeholder
        return static::active()->count() * 100; // $100 per active institution
    }
}