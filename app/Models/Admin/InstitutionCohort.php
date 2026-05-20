<?php

namespace App\Models\Admin;

use App\Models\Core\Institution;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InstitutionCohort extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'name',
        'slug',
        'description',
        'status',
        'starts_at',
        'ends_at',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'metadata' => 'array',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'archived' => 'Archived',
    ];

    protected static function booted(): void
    {
        static::creating(function (InstitutionCohort $cohort) {
            $cohort->slug ??= $cohort->generateUniqueSlug($cohort->name);
            $cohort->status ??= 'active';
        });

        static::updating(function (InstitutionCohort $cohort) {
            if ($cohort->isDirty('name') && ! $cohort->isDirty('slug')) {
                $cohort->slug = $cohort->generateUniqueSlug($cohort->name);
            }
        });
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(InstitutionUser::class, 'institution_cohort_user', 'institution_cohort_id', 'institution_user_id')
            ->withPivot(['assigned_by', 'joined_at'])
            ->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'institution_cohort_courses', 'institution_cohort_id', 'course_id')
            ->withPivot(['assigned_by', 'assigned_at', 'due_at'])
            ->withTimestamps();
    }

    public function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name) ?: 'cohort';
        $baseSlug = $slug;
        $count = 1;

        while (static::where('institution_id', $this->institution_id)
            ->where('slug', $slug)
            ->when($this->exists, fn ($query) => $query->where('id', '!=', $this->id))
            ->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }

    public function memberUserIds()
    {
        return $this->members()
            ->where('institution_users.status', 'active')
            ->pluck('institution_users.user_id');
    }

    public function getCompletionStats(): array
    {
        $userIds = $this->memberUserIds();
        $courseIds = $this->courses()->pluck('courses.id');
        $expected = $userIds->count() * $courseIds->count();

        if ($expected === 0) {
            return [
                'expected' => 0,
                'enrolled' => 0,
                'completed' => 0,
                'average_progress' => 0,
                'completion_rate' => 0,
            ];
        }

        $enrollments = CourseEnrollment::whereIn('user_id', $userIds)
            ->whereIn('course_id', $courseIds);

        $completed = (clone $enrollments)->where(function ($query) {
            $query->where('is_completed', true)
                ->orWhere('progress_percentage', '>=', 100)
                ->orWhereNotNull('completed_at');
        })->count();

        return [
            'expected' => $expected,
            'enrolled' => (clone $enrollments)->count(),
            'completed' => $completed,
            'average_progress' => round((float) ((clone $enrollments)->avg('progress_percentage') ?? 0), 1),
            'completion_rate' => round(($completed / $expected) * 100, 1),
        ];
    }

    public function getCompletionRateAttribute(): float
    {
        return $this->getCompletionStats()['completion_rate'];
    }

    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
