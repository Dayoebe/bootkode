<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ACADEMY_ADMIN = 'academy_admin';
    const ROLE_INSTRUCTOR = 'instructor';
    const ROLE_MENTOR = 'mentor';
    const ROLE_CONTENT_EDITOR = 'content_editor';
    const ROLE_AFFILIATE_AMBASSADOR = 'affiliate_ambassador';
    const ROLE_STUDENT = 'student';


    public static function getRoles()
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ACADEMY_ADMIN,
            self::ROLE_INSTRUCTOR,
            self::ROLE_MENTOR,
            self::ROLE_CONTENT_EDITOR,
            self::ROLE_AFFILIATE_AMBASSADOR,
            self::ROLE_STUDENT,
        ];
    }

    // Activity Logging Configuration


    protected static $logOnlyDirty = true; // Only log changed attributes
    protected static $submitEmptyLogs = false; // Don't log if no changes

    protected static function booted()
    {
        static::saved(function ($user) {
            // Sync the role column with Spatie roles
            if ($user->isDirty('role')) {
                $user->syncRoles([$user->role]);
            }
        });
    }
    // Role checking methods
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isAcademyAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ACADEMY_ADMIN);
    }

    public function isInstructor(): bool
    {
        return $this->hasRole(self::ROLE_INSTRUCTOR);
    }

    public function isMentor(): bool
    {
        return $this->hasRole(self::ROLE_MENTOR);
    }

    public function isContentEditor(): bool
    {
        return $this->hasRole(self::ROLE_CONTENT_EDITOR);
    }

    public function isAffiliateAmbassador(): bool
    {
        return $this->hasRole(self::ROLE_AFFILIATE_AMBASSADOR);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(self::ROLE_STUDENT);
    }
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'date_of_birth',
        'phone_number',
        'bio',
        'profile_picture',
        'address_street',
        'address_city',
        'address_state',
        'address_country',
        'address_postal_code',
        'occupation',
        'skills',
        'education_level',
        'social_links',
        'is_active',
        'last_login_at',
        'email_verified_at',
        'provider',
        'provider_id',
        'receive_course_updates',
        'receive_certificate_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'last_login_at' => 'datetime',
        'social_links' => 'array',
        'is_active' => 'boolean',
        'receive_course_updates' => 'boolean',
        'receive_certificate_notifications' => 'boolean',
    ];


    // Relationships
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withTimestamps()
            ->withPivot(['last_accessed_at']);
    }

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
            ->withTimestamps()
            ->withPivot(['completed_at']);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class, 'user_id');
    }
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }
    public function savedResources()
    {
        return $this->hasMany(SavedResource::class);
    }

    public function downloadedContent()
    {
        return $this->hasMany(DownloadableContent::class);
    }

    public function offlineNotes()
    {
        return $this->hasMany(OfflineNote::class);
    }
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
    public function hasCompletedCourse(Course $course): bool
    {
        $totalLessons = $course->sections()->with('lessons')->get()->sum(function ($section) {
            return $section->lessons->count();
        });

        $completedLessons = $this->completedLessons()
            ->whereIn('lessons.id', $course->sections()->with('lessons')->get()->flatMap->lessons->pluck('id'))
            ->count();

        return $completedLessons >= $totalLessons;
    }

    // Activity Logging Configuration (log all fillable attributes for "every activities" on model changes)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user') // Correct method: useLogName() to set log name
            ->logFillable() // Log all fillable attributes (enables logging "every" change)
            ->logOnlyDirty() // Only log changed attributes
            ->dontSubmitEmptyLogs(); // Skip if no changes
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "User {$this->name} has been {$eventName} by " . (auth()->user()?->name ?? 'System');
    }

    // Custom method for manual logging of non-model activities (e.g., login, view)
    public function logCustomActivity(string $description, array $properties = [])
    {
        activity()
            ->causedBy(auth()->user() ?? $this) // Who caused it
            ->performedOn($this) // On this user
            ->withProperties($properties) // Extra data (e.g., IP, device)
            ->log($description);
    }

    // Address Accessor
    public function getFullAddressAttribute()
    {
        $parts = [
            $this->address_street,
            $this->address_city,
            // ... (rest of your getFullAddressAttribute code, truncated in query)
        ];
        return implode(', ', array_filter($parts));
    }

    // Check if user should receive email notification based on preferences
    public function shouldReceiveEmailNotification(string $notificationType): bool
    {
        return match ($notificationType) {
            'course_update' => $this->receive_course_updates,
            'certificate_update' => $this->receive_certificate_notifications,
            'support_ticket' => true, // From previous
            'feedback_response' => true, // Add this
            'announcement' => true, // Add this
            'system_status' => true, // Add this
            default => true, // System notifications always sent
        };
    }

    protected static $logAttributes = ['name', 'email', 'role'];
    protected static $logName = 'user';



    // Age Calculation
    public function getAgeAttribute()
    {
        return $this->date_of_birth?->age;
    }

    // Social Links Helpers
    public function setSocialLink($platform, $url)
    {
        $links = $this->social_links ?? [];
        $links[$platform] = $url;
        $this->social_links = $links;
    }

    public function getSocialLink($platform)
    {
        return $this->social_links[$platform] ?? null;
    }

    // Account Status Helpers
    public function activateAccount()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivateAccount()
    {
        $this->update(['is_active' => false]);
    }

    public function getDashboardRouteName(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'super_admin.dashboard',
            self::ROLE_ACADEMY_ADMIN => 'academy_admin.dashboard',
            self::ROLE_INSTRUCTOR => 'instructor.dashboard',
            self::ROLE_MENTOR => 'mentor.dashboard',
            self::ROLE_CONTENT_EDITOR => 'content_editor.dashboard',
            self::ROLE_AFFILIATE_AMBASSADOR => 'affiliate_ambassador.dashboard',
            default => 'student.dashboard',
        };
    }

    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAllExcept($query, User $user)
    {
        return $query->where('id', '!=', $user->id);
    }

    public function canBeDeleted(): bool
    {
        return !($this->hasRole(self::ROLE_SUPER_ADMIN) && $this->id === 1);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail());
    }
    // Role
public function canManageCertificates(): bool
{
    return $this->isSuperAdmin() || $this->isAcademyAdmin() || $this->isInstructor();
}

public function canApproveAllCertificates(): bool
{
    return $this->isSuperAdmin() || $this->isAcademyAdmin();
}

public function canManageUsers(): bool
{
    return $this->isSuperAdmin() || $this->isAcademyAdmin();
}

public function canManageCourses(): bool
{
    return !$this->isStudent(); // Everyone except students
}

}