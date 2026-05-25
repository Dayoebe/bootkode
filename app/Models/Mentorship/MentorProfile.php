<?php

namespace App\Models\Mentorship;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\CarbonInterface;
use App\Models\Core\User;
class MentorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_available',
        'bio',
        'specializations',
        'skills',
        'industries',
        'languages',
        'experience_level',
        'years_experience',
        'hourly_rate',
        'offers_free_sessions',
        'max_mentees',
        'current_mentees',
        'availability_schedule',
        'timezone',
        'communication_preferences',
        'mentoring_approach',
        'certifications',
        'linkedin_profile',
        'github_profile',
        'portfolio_url',
        'rating',
        'total_reviews',
        'total_mentees',
        'total_sessions',
        'is_verified',
        'verified_at',
        'achievements'
    ];

    protected $casts = [
        'specializations' => 'array',
        'skills' => 'array',
        'industries' => 'array',
        'languages' => 'array',
        'availability_schedule' => 'array',
        'communication_preferences' => 'array',
        'certifications' => 'array',
        'achievements' => 'array',
        'is_available' => 'boolean',
        'offers_free_sessions' => 'boolean',
        'is_verified' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'verified_at' => 'datetime'
    ];

    protected $appends = [
        'experience_label',
        'availability_status',
        'rating_stars',
        'success_rate',
        'response_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentorships()
    {
        return $this->hasMany(Mentorship::class, 'mentor_id', 'user_id');
    }

    public function activeMentorships()
    {
        return $this->mentorships()->active();
    }

    public function experienceLabelAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->experience_level) {
                'junior' => 'Junior Level',
                'mid' => 'Mid Level',
                'senior' => 'Senior Level',
                'expert' => 'Expert Level',
                default => 'Unspecified'
            }
        );
    }

    public function availabilityStatusAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->is_available) {
                    return 'Unavailable';
                }

                if ($this->current_mentees >= $this->max_mentees) {
                    return 'Fully Booked';
                }

                return 'Available';
            }
        );
    }

    public function ratingStarsAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                $rating = $this->rating;
                $stars = str_repeat('★', floor($rating));
                $halfStar = ($rating - floor($rating)) >= 0.5 ? '☆' : '';
                $emptyStars = str_repeat('☆', 5 - strlen($stars) - strlen($halfStar));
                
                return $stars . $halfStar . $emptyStars;
            }
        );
    }

    public function canAcceptMentees()
    {
        return $this->is_available && $this->current_mentees < $this->max_mentees;
    }

    public function normalizedAvailabilitySchedule(): array
    {
        return collect($this->availability_schedule ?? [])
            ->map(function ($slot) {
                return [
                    'day' => strtolower((string) ($slot['day'] ?? '')),
                    'start' => (string) ($slot['start'] ?? ''),
                    'end' => (string) ($slot['end'] ?? ''),
                ];
            })
            ->filter(fn ($slot) => $slot['day'] && $slot['start'] && $slot['end'] && $slot['start'] < $slot['end'])
            ->values()
            ->all();
    }

    public function isAvailableForSlot(CarbonInterface $start, int $durationMinutes): bool
    {
        if (! $this->is_available) {
            return false;
        }

        $schedule = $this->normalizedAvailabilitySchedule();
        if (empty($schedule)) {
            return true;
        }

        $day = strtolower($start->format('l'));
        $slotStart = $start->format('H:i');
        $slotEnd = $start->copy()->addMinutes($durationMinutes)->format('H:i');

        return collect($schedule)->contains(function ($slot) use ($day, $slotStart, $slotEnd) {
            return $slot['day'] === $day
                && $slot['start'] <= $slotStart
                && $slot['end'] >= $slotEnd;
        });
    }

    public function availabilitySummary(): array
    {
        return collect($this->normalizedAvailabilitySchedule())
            ->groupBy('day')
            ->map(fn ($slots) => $slots->map(fn ($slot) => "{$slot['start']} - {$slot['end']}")->implode(', '))
            ->all();
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->whereColumn('current_mentees', '<', 'max_mentees');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
