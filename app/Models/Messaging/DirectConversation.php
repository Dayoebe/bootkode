<?php

namespace App\Models\Messaging;

use App\Models\Core\User;
use App\Models\Learning\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DirectConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'instructor_id',
        'last_message_preview',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DirectMessage::class)->oldest();
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(DirectMessage::class)->latestOfMany();
    }

    public function scopeForParticipant(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $participantQuery) use ($user) {
            $participantQuery
                ->where('student_id', $user->id)
                ->orWhere('instructor_id', $user->id);
        });
    }

    public function isParticipant(User $user): bool
    {
        return (int) $this->student_id === (int) $user->id
            || (int) $this->instructor_id === (int) $user->id;
    }

    public function recipientFor(User $sender): ?User
    {
        if ((int) $this->student_id === (int) $sender->id) {
            return $this->instructor;
        }

        if ((int) $this->instructor_id === (int) $sender->id) {
            return $this->student;
        }

        return null;
    }

    public function otherParticipantFor(User $user): ?User
    {
        return $this->recipientFor($user);
    }
}
