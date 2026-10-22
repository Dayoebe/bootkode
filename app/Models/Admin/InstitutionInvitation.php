<?php

namespace App\Models\Admin;

use App\Models\Core\Institution;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InstitutionInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'user_id',
        'name',
        'email',
        'role',
        'department',
        'token',
        'status',
        'invited_by',
        'invited_at',
        'expires_at',
        'accepted_at',
        'accepted_user_id',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public const ROLES = [
        'admin' => 'School Admin',
        'instructor' => 'Instructor',
        'student' => 'Student',
        'observer' => 'Observer',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'revoked' => 'Revoked',
        'expired' => 'Expired',
    ];

    protected static function booted(): void
    {
        static::creating(function (InstitutionInvitation $invitation) {
            $invitation->token ??= Str::random(48);
            $invitation->status ??= 'pending';
            $invitation->invited_at ??= now();
            $invitation->expires_at ??= now()->addDays(14);
        });
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function invitee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function acceptedUser()
    {
        return $this->belongsTo(User::class, 'accepted_user_id');
    }

    public function institutionUser()
    {
        return $this->hasOne(InstitutionUser::class, 'user_id', 'user_id')
            ->where('institution_id', $this->institution_id);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && ! $this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function accept(?User $user = null): InstitutionUser
    {
        $user ??= auth()->user();
        abort_unless($user, 403);
        return \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            $invitation = static::lockForUpdate()->findOrFail($this->id);
            abort_unless((int) $invitation->user_id === (int) $user->id
                && strcasecmp($invitation->email, $user->email) === 0, 403);
            abort_unless($invitation->isPending(), 410, 'This invitation is no longer pending.');
            $institution = Institution::lockForUpdate()->findOrFail($invitation->institution_id);
            $membership = InstitutionUser::withTrashed()->where('institution_id', $institution->id)->where('user_id', $user->id)->first();
            if (!$membership || $membership->trashed() || $membership->status !== 'active') {
                $institution->updateUserCount();
                $institution->ensureCanAddUsers();
            }
            $membership ??= new InstitutionUser(['institution_id' => $institution->id, 'user_id' => $user->id]);
            if ($membership->trashed()) {
                $membership->restore();
            }
            $membership->fill(['role' => $invitation->role, 'department' => $invitation->department,
                'status' => 'active', 'joined_at' => now(), 'added_by' => $invitation->invited_by])->save();
            $invitation->update(['status' => 'accepted', 'accepted_at' => now(), 'accepted_user_id' => $user->id]);
            $institution->updateUserCount();
            $this->refresh();

            return $membership;
        });
    }

    public function revoke(): void
    {
        $this->update(['status' => 'revoked']);

        if ($this->institutionUser && $this->institutionUser->status === 'pending') {
            $this->institutionUser->delete();
        }
    }

    public function markExpired(): void
    {
        if ($this->status === 'pending') {
            $this->update(['status' => 'expired']);
        }
    }

    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
