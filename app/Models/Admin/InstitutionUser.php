<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Core\User;

class InstitutionUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'user_id',
        'role',
        'department',
        'employee_id',
        'status',
        'joined_at',
        'left_at',
        'added_by',
        'notes'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime'
    ];

    const ROLES = [
        'admin' => 'Administrator',
        'instructor' => 'Instructor',
        'student' => 'Student',
        'observer' => 'Observer'
    ];

    const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'suspended' => 'Suspended'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($institutionUser) {
            if (!$institutionUser->joined_at) {
                $institutionUser->joined_at = now();
            }
            
            if (!$institutionUser->status) {
                $institutionUser->status = 'active';
            }
        });

        static::created(function ($institutionUser) {
            // Update institution user count
            $institutionUser->institution->updateUserCount();
        });

        static::deleted(function ($institutionUser) {
            // Update institution user count
            $institutionUser->institution->updateUserCount();
        });
    }

    // Relationships
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // Helper methods
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

    public function activate()
    {
        $this->update(['status' => 'active']);
    }

    public function suspend()
    {
        $this->update(['status' => 'suspended']);
    }

    public function deactivate()
    {
        $this->update([
            'status' => 'inactive',
            'left_at' => now()
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    // Accessors
    public function getRoleNameAttribute()
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'pending' => 'yellow',
            'suspended' => 'red',
            default => 'gray'
        };
    }
}