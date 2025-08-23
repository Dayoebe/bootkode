<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'achievement_type',
        'achievement_name',
        'achievement_description',
        'achievement_icon',
        'achievement_value',
        'earned_at',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check and award achievements for a user
     */
    public static function checkAndAwardAchievements($userId)
    {
        $user = User::find($userId);
        if (!$user) return;

        $achievements = [];

        // Streak achievements
        $streak = LearningSession::getStudyStreak($userId);
        $achievements = array_merge($achievements, self::checkStreakAchievements($userId, $streak));

        // Course completion achievements
        $completedCourses = $user->courses()->wherePivot('progress', 100)->count();
        $achievements = array_merge($achievements, self::checkCompletionAchievements($userId, $completedCourses));

        // Lesson completion achievements
        $completedLessons = $user->completedLessons()->count();
        $achievements = array_merge($achievements, self::checkLessonAchievements($userId, $completedLessons));

        // Assessment achievements
        $averageScore = self::getAverageAssessmentScore($user);
        $achievements = array_merge($achievements, self::checkAssessmentAchievements($userId, $averageScore));

        // Study time achievements
        $totalStudyTime = LearningSession::where('user_id', $userId)->sum('duration_minutes');
        $achievements = array_merge($achievements, self::checkStudyTimeAchievements($userId, $totalStudyTime));

        return $achievements;
    }

    private static function checkStreakAchievements($userId, $streak)
    {
        $achievements = [];
        $streakMilestones = [
            3 => ['name' => 'Getting Started', 'icon' => '🌱', 'description' => '3-day learning streak'],
            7 => ['name' => 'Week Warrior', 'icon' => '🔥', 'description' => '7-day learning streak'],
            14 => ['name' => 'Two Week Champion', 'icon' => '⚡', 'description' => '14-day learning streak'],
            30 => ['name' => 'Month Master', 'icon' => '🏆', 'description' => '30-day learning streak'],
            60 => ['name' => 'Consistency King', 'icon' => '👑', 'description' => '60-day learning streak'],
            100 => ['name' => 'Century Scholar', 'icon' => '💯', 'description' => '100-day learning streak'],
            365 => ['name' => 'Year-Long Learner', 'icon' => '🌟', 'description' => '365-day learning streak'],
        ];

        foreach ($streakMilestones as $days => $achievement) {
            if ($streak >= $days && !self::hasAchievement($userId, 'streak', $achievement['name'])) {
                $achievements[] = self::awardAchievement($userId, 'streak', $achievement['name'], $achievement['description'], $achievement['icon'], $days);
            }
        }

        return $achievements;
    }

    private static function checkCompletionAchievements($userId, $completedCourses)
    {
        $achievements = [];
        $completionMilestones = [
            1 => ['name' => 'First Steps', 'icon' => '🎯', 'description' => 'Completed your first course'],
            3 => ['name' => 'Learning Momentum', 'icon' => '📈', 'description' => 'Completed 3 courses'],
            5 => ['name' => 'Knowledge Seeker', 'icon' => '📚', 'description' => 'Completed 5 courses'],
            10 => ['name' => 'Learning Expert', 'icon' => '🎓', 'description' => 'Completed 10 courses'],
            20 => ['name' => 'Course Conqueror', 'icon' => '🏅', 'description' => 'Completed 20 courses'],
            50 => ['name' => 'Learning Legend', 'icon' => '🌟', 'description' => 'Completed 50 courses'],
        ];

        foreach ($completionMilestones as $count => $achievement) {
            if ($completedCourses >= $count && !self::hasAchievement($userId, 'course_completion', $achievement['name'])) {
                $achievements[] = self::awardAchievement($userId, 'course_completion', $achievement['name'], $achievement['description'], $achievement['icon'], $count);
            }
        }

        return $achievements;
    }

    private static function checkLessonAchievements($userId, $completedLessons)
    {
        $achievements = [];
        $lessonMilestones = [
            10 => ['name' => 'Lesson Learner', 'icon' => '📖', 'description' => 'Completed 10 lessons'],
            50 => ['name' => 'Study Star', 'icon' => '⭐', 'description' => 'Completed 50 lessons'],
            100 => ['name' => 'Century Student', 'icon' => '💯', 'description' => 'Completed 100 lessons'],
            250 => ['name' => 'Knowledge Collector', 'icon' => '🧠', 'description' => 'Completed 250 lessons'],
            500 => ['name' => 'Learning Machine', 'icon' => '🤖', 'description' => 'Completed 500 lessons'],
            1000 => ['name' => 'Master Scholar', 'icon' => '👨‍🎓', 'description' => 'Completed 1000 lessons'],
        ];

        foreach ($lessonMilestones as $count => $achievement) {
            if ($completedLessons >= $count && !self::hasAchievement($userId, 'lesson_completion', $achievement['name'])) {
                $achievements[] = self::awardAchievement($userId, 'lesson_completion', $achievement['name'], $achievement['description'], $achievement['icon'], $count);
            }
        }

        return $achievements;
    }

    private static function checkAssessmentAchievements($userId, $averageScore)
    {
        $achievements = [];
        $scoreMilestones = [
            70 => ['name' => 'Good Student', 'icon' => '👍', 'description' => '70%+ average score'],
            80 => ['name' => 'High Achiever', 'icon' => '🎯', 'description' => '80%+ average score'],
            90 => ['name' => 'Excellence Seeker', 'icon' => '💎', 'description' => '90%+ average score'],
            95 => ['name' => 'Near Perfect', 'icon' => '🏆', 'description' => '95%+ average score'],
            98 => ['name' => 'Perfectionist', 'icon' => '👑', 'description' => '98%+ average score'],
        ];

        foreach ($scoreMilestones as $score => $achievement) {
            if ($averageScore >= $score && !self::hasAchievement($userId, 'assessment_score', $achievement['name'])) {
                $achievements[] = self::awardAchievement($userId, 'assessment_score', $achievement['name'], $achievement['description'], $achievement['icon'], $score);
            }
        }

        return $achievements;
    }

    private static function checkStudyTimeAchievements($userId, $totalMinutes)
    {
        $achievements = [];
        $totalHours = $totalMinutes / 60;
        $timeMilestones = [
            10 => ['name' => 'Time Keeper', 'icon' => '⏰', 'description' => '10+ hours of study time'],
            25 => ['name' => 'Dedicated Learner', 'icon' => '📚', 'description' => '25+ hours of study time'],
            50 => ['name' => 'Study Champion', 'icon' => '🏆', 'description' => '50+ hours of study time'],
            100 => ['name' => 'Time Master', 'icon' => '⏳', 'description' => '100+ hours of study time'],
            250 => ['name' => 'Learning Marathon', 'icon' => '🏃‍♂️', 'description' => '250+ hours of study time'],
            500 => ['name' => 'Study Sage', 'icon' => '🧙‍♂️', 'description' => '500+ hours of study time'],
        ];

        foreach ($timeMilestones as $hours => $achievement) {
            if ($totalHours >= $hours && !self::hasAchievement($userId, 'study_time', $achievement['name'])) {
                $achievements[] = self::awardAchievement($userId, 'study_time', $achievement['name'], $achievement['description'], $achievement['icon'], $hours);
            }
        }

        return $achievements;
    }

    private static function hasAchievement($userId, $type, $name)
    {
        return self::where('user_id', $userId)
            ->where('achievement_type', $type)
            ->where('achievement_name', $name)
            ->exists();
    }

    private static function awardAchievement($userId, $type, $name, $description, $icon, $value)
    {
        return self::create([
            'user_id' => $userId,
            'achievement_type' => $type,
            'achievement_name' => $name,
            'achievement_description' => $description,
            'achievement_icon' => $icon,
            'achievement_value' => $value,
            'earned_at' => now(),
        ]);
    }

    private static function getAverageAssessmentScore($user)
    {
        return \DB::table('student_answers')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->where('student_answers.user_id', $user->id)
            ->whereNotNull('student_answers.submitted_at')
            ->avg(\DB::raw('student_answers.points_earned / assessments.max_score * 100')) ?? 0;
    }

    /**
     * Get recent achievements for user
     */
    public static function getRecentAchievements($userId, $limit = 5)
    {
        return self::where('user_id', $userId)
            ->orderBy('earned_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($achievement) {
                return [
                    'title' => $achievement->achievement_name,
                    'description' => $achievement->achievement_description,
                    'icon' => $achievement->achievement_icon,
                    'earned_at' => $achievement->earned_at,
                ];
            });
    }
}