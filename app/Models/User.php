<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // This is crucial: 'role' must be fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Define constants for roles for better maintainability and use in dropdowns
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ACADEMY_ADMIN = 'academy_admin';
    public const ROLE_INSTRUCTOR = 'instructor';
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_CONTENT_EDITOR = 'content_editor';
    public const ROLE_AFFILIATE_AMBASSADOR = 'affiliate_ambassador';
    public const ROLE_STUDENT = 'student';

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasRoleIn(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if the user is an Academy Admin.
     */
    public function isAcademyAdmin(): bool
    {
        return $this->role === self::ROLE_ACADEMY_ADMIN;
    }

    /**
     * Check if the user is an Instructor.
     */
    public function isInstructor(): bool
    {
        return $this->role === self::ROLE_INSTRUCTOR;
    }

    /**
     * Check if the user is a Mentor.
     */
    public function isMentor(): bool
    {
        return $this->role === self::ROLE_MENTOR;
    }

    /**
     * Check if the user is a Content Editor.
     */
    public function isContentEditor(): bool
    {
        return $this->role === self::ROLE_CONTENT_EDITOR;
    }

    /**
     * Check if the user is an Affiliate/Ambassador.
     */
    public function isAffiliateAmbassador(): bool
    {
        return $this->role === self::ROLE_AFFILIATE_AMBASSADOR;
    }

    /**
     * Check if the user is a Student.
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Get the dashboard route name based on the user's role.
     */
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

    /**
     * Scope a query to filter users by role.
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get all users except the current one.
     */
    public function scopeAllExcept($query, User $user)
    {
        return $query->where('id', '!=', $user->id);
    }

    /**
     * Check if user can be deleted.
     */
    public function canBeDeleted(): bool
    {
        // Prevent deleting the first super admin (assuming ID 1 is the initial super admin)
        return !($this->isSuperAdmin() && $this->id === 1);
    }

    /**
     * Send email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail());
    }
}