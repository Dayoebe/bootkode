<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'section_id',
        'lesson_id',
        'title',
        'slug',
        'description',
        'type',
        'pass_percentage',
        'estimated_duration_minutes',
        'deadline',
        'project_type',
        'required_skills',
        'deliverables',
        'resources',
        'is_mandatory',
        'weight',
        'allows_collaboration',
        'evaluation_criteria',
        'due_date',
        'max_score',
        'instructions',
        'order'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'due_date' => 'datetime',
        'required_skills' => 'array',
        'deliverables' => 'array',
        'resources' => 'array',
        'is_mandatory' => 'boolean',
        'allows_collaboration' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'assessment_id')->orderBy('order');
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assessment) {
            $assessment->order = $assessment->order ?? $assessment->section->assessments()->count() + 1;
            $assessment->slug = Str::slug($assessment->title);
        });

        static::updating(function ($assessment) {
            if ($assessment->isDirty('title')) {
                $assessment->slug = Str::slug($assessment->title);
            }
        });
    }

    public function getIsQuizAttribute()
    {
        return $this->type === 'quiz';
    }

    /**
     * Get student's latest attempt for this assessment
     */
    public function getStudentLatestAttempt($userId)
    {
        return $this->studentAnswers()
            ->where('user_id', $userId)
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    /**
     * Get student's attempt results
     */
    public function getStudentResults($userId, $attemptNumber = null)
    {
        $query = $this->studentAnswers()
            ->where('user_id', $userId)
            ->with('question');

        if ($attemptNumber) {
            $query->where('attempt_number', $attemptNumber);
        } else {
            // Get latest attempt
            $latestAttempt = $this->getStudentLatestAttempt($userId);
            if ($latestAttempt) {
                $query->where('attempt_number', $latestAttempt->attempt_number);
            }
        }

        $answers = $query->get();

        if ($answers->isEmpty()) {
            return null;
        }

        $totalQuestions = $this->questions()->count();
        $correctAnswers = $answers->where('is_correct', true)->count();
        $totalPoints = $answers->sum('points_earned');
        $maxPoints = $this->questions()->sum('points');

        $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;

        return [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answers->count(),
            'correct_answers' => $correctAnswers,
            'total_points' => $totalPoints,
            'max_points' => $maxPoints,
            'percentage' => $percentage,
            'passed' => $percentage >= $this->pass_percentage,
            'attempt_number' => $answers->first()->attempt_number ?? 1,
            'submitted_at' => $answers->first()->submitted_at ?? null,
            'answers' => $answers->keyBy('question_id')
        ];
    }

    /**
     * Check if student has completed this assessment
     */
    public function isCompletedByStudent($userId)
    {
        return $this->studentAnswers()
            ->where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->exists();
    }

    /**
     * Check if student has passed this assessment
     */
    public function isPassedByStudent($userId)
    {
        $results = $this->getStudentResults($userId);
        return $results ? $results['passed'] : false;
    }

    /**
     * Get next attempt number for a student
     */
    public function getNextAttemptNumber($userId)
    {
        $lastAttempt = $this->studentAnswers()
            ->where('user_id', $userId)
            ->max('attempt_number');

        return ($lastAttempt ?? 0) + 1;
    }

    /**
     * Get assessment statistics
     */
    public function getStatsAttribute()
    {
        $totalAttempts = $this->studentAnswers()
            ->distinct('user_id', 'attempt_number')
            ->count();

        $passedAttempts = $this->studentAnswers()
            ->select('user_id', 'attempt_number')
            ->selectRaw('SUM(points_earned) as total_points')
            ->groupBy('user_id', 'attempt_number')
            ->havingRaw('(SUM(points_earned) / ?) * 100 >= ?', [
                $this->questions()->sum('points'),
                $this->pass_percentage
            ])
            ->count();

        $averageScore = $this->studentAnswers()
            ->select('user_id', 'attempt_number')
            ->selectRaw('SUM(points_earned) as total_points')
            ->groupBy('user_id', 'attempt_number')
            ->get()
            ->avg('total_points');

        $maxPoints = $this->questions()->sum('points');

        return [
            'total_attempts' => $totalAttempts,
            'passed_attempts' => $passedAttempts,
            'pass_rate' => $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0,
            'average_score' => $averageScore ? round($averageScore, 1) : 0,
            'average_percentage' => $maxPoints > 0 && $averageScore ? round(($averageScore / $maxPoints) * 100, 1) : 0
        ];
    }

    /**
     * Scope for published assessments
     */
    public function scopePublished($query)
    {
        return $query->whereHas('lesson.section.course', function ($q) {
            $q->where('is_published', true)->where('is_approved', true);
        });
    }

    /**
     * Scope for mandatory assessments
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->estimated_duration_minutes) {
            return 'No time limit';
        }

        $hours = floor($this->estimated_duration_minutes / 60);
        $minutes = $this->estimated_duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }
}