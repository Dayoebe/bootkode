<?php

namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Lesson;
use App\Models\Assessment\Assessment;
use App\Models\Credentials\GamificationData;
use App\Models\GamificationTransaction;
use App\Models\Credentials\UserBadge;
use App\Models\Assessment\LearningSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    const POINTS_LESSON_COMPLETION = 25;
    const POINTS_QUIZ_COMPLETION = 50;
    const POINTS_COURSE_COMPLETION = 200;
    const POINTS_DAILY_LOGIN = 10;
    const POINTS_STREAK_BONUS = 5;
    const POINTS_PERFECT_QUIZ = 100;

    const COINS_LESSON_COMPLETION = 5;
    const COINS_QUIZ_COMPLETION = 10;
    const COINS_COURSE_COMPLETION = 50;
    const COINS_DAILY_LOGIN = 2;

    const GEMS_COURSE_COMPLETION = 3;
    const GEMS_PERFECT_QUIZ = 1;
    const GEMS_ACHIEVEMENT = 2;

    const ENERGY_COST_LESSON = 10;
    const ENERGY_COST_QUIZ = 15;
    const ENERGY_REGEN_RATE = 1; // per 5 minutes

    /**
     * Handle lesson completion with full gamification rewards
     */
    public function handleLessonCompletion(User $user, Lesson $lesson = null)
    {
        DB::beginTransaction();
        
        try {
            $gamificationData = $user->getOrCreateGamificationData();
            
            // Check if user has enough energy
            $gamificationData->updateEnergy();
            if ($gamificationData->energy < self::ENERGY_COST_LESSON) {
                return [
                    'success' => false,
                    'message' => 'Not enough energy to complete lesson',
                    'energy_needed' => self::ENERGY_COST_LESSON,
                    'current_energy' => $gamificationData->energy
                ];
            }

            // Consume energy
            $gamificationData->consumeEnergy(self::ENERGY_COST_LESSON);

            // Award base points and coins
            $pointsEarned = self::POINTS_LESSON_COMPLETION;
            $coinsEarned = self::COINS_LESSON_COMPLETION;

            // Streak bonus
            $streakBonus = $gamificationData->current_streak * self::POINTS_STREAK_BONUS;
            $pointsEarned += $streakBonus;

            // Add rewards
            $gamificationData->addPoints($pointsEarned, 'lesson_completion');
            $gamificationData->addCoins($coinsEarned, 'lesson_completion');

            // Update daily quest progress
            $questsCompleted = $gamificationData->updateQuestProgress('complete_lessons', 1);

            // Update study streak
            $gamificationData->updateStudyStreak();

            // Check for achievements
            $newAchievements = $user->checkAllAchievements();

            // Create learning session record
            if ($lesson) {
                LearningSession::create([
                    'user_id' => $user->id,
                    'course_id' => $lesson->section->course_id ?? null,
                    'lesson_id' => $lesson->id,
                    'session_type' => 'lesson',
                    'duration_minutes' => $lesson->duration_minutes ?? 15,
                    'points_earned' => $pointsEarned,
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'points_earned' => $pointsEarned,
                'coins_earned' => $coinsEarned,
                'streak_bonus' => $streakBonus,
                'new_achievements' => $newAchievements,
                'quests_completed' => $questsCompleted,
                'energy_remaining' => $gamificationData->fresh()->energy,
                'level_up' => $this->checkLevelUp($gamificationData),
            ];

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in lesson completion gamification', [
                'user_id' => $user->id,
                'lesson_id' => $lesson?->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to process gamification rewards'
            ];
        }
    }

    /**
     * Handle quiz/assessment completion with performance-based rewards
     */
    public function handleQuizCompletion(User $user, Assessment $assessment = null, $score = 0, $passed = false)
    {
        DB::beginTransaction();
        
        try {
            $gamificationData = $user->getOrCreateGamificationData();
            
            // Check energy
            $gamificationData->updateEnergy();
            if ($gamificationData->energy < self::ENERGY_COST_QUIZ) {
                return [
                    'success' => false,
                    'message' => 'Not enough energy to take quiz',
                    'energy_needed' => self::ENERGY_COST_QUIZ
                ];
            }

            // Consume energy
            $gamificationData->consumeEnergy(self::ENERGY_COST_QUIZ);

            // Calculate rewards based on performance
            $pointsEarned = self::POINTS_QUIZ_COMPLETION;
            $coinsEarned = self::COINS_QUIZ_COMPLETION;
            $gemsEarned = 0;

            // Performance bonuses
            if ($score >= 95) {
                $pointsEarned += self::POINTS_PERFECT_QUIZ;
                $gemsEarned += self::GEMS_PERFECT_QUIZ;
                $coinsEarned += 20; // Perfect score bonus
            } elseif ($score >= 90) {
                $pointsEarned += 50;
                $coinsEarned += 15;
            } elseif ($score >= 80) {
                $pointsEarned += 25;
                $coinsEarned += 10;
            }

            // First attempt bonus
            if ($assessment && $this->isFirstAttempt($user, $assessment)) {
                $pointsEarned += 25;
                $coinsEarned += 5;
            }

            // Streak bonus
            $streakBonus = $gamificationData->current_streak * self::POINTS_STREAK_BONUS;
            $pointsEarned += $streakBonus;

            // Award points and currency
            $gamificationData->addPoints($pointsEarned, 'quiz_completion');
            $gamificationData->addCoins($coinsEarned, 'quiz_completion');
            if ($gemsEarned > 0) {
                $gamificationData->addGems($gemsEarned, 'perfect_quiz');
            }

            // Update quest progress
            $questsCompleted = $gamificationData->updateQuestProgress('complete_assessments', 1);
            if ($passed) {
                $questsCompleted = array_merge($questsCompleted, 
                    $gamificationData->updateQuestProgress('pass_assessments', 1));
            }

            // Update study streak
            $gamificationData->updateStudyStreak();

            // Check achievements
            $newAchievements = $user->checkAllAchievements();

            // Create learning session
            if ($assessment) {
                LearningSession::create([
                    'user_id' => $user->id,
                    'course_id' => $assessment->course_id,
                    'assessment_id' => $assessment->id,
                    'session_type' => 'assessment',
                    'duration_minutes' => $assessment->estimated_duration_minutes ?? 20,
                    'points_earned' => $pointsEarned,
                    'performance_score' => $score,
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'points_earned' => $pointsEarned,
                'coins_earned' => $coinsEarned,
                'gems_earned' => $gemsEarned,
                'streak_bonus' => $streakBonus,
                'performance_bonus' => $pointsEarned - self::POINTS_QUIZ_COMPLETION - $streakBonus,
                'new_achievements' => $newAchievements,
                'quests_completed' => $questsCompleted,
                'level_up' => $this->checkLevelUp($gamificationData),
            ];

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in quiz completion gamification', [
                'user_id' => $user->id,
                'assessment_id' => $assessment?->id,
                'error' => $e->getMessage()
            ]);
            
            return ['success' => false, 'message' => 'Failed to process quiz rewards'];
        }
    }

    /**
     * Handle daily login rewards and streak maintenance
     */
    public function handleDailyLogin(User $user)
    {
        $gamificationData = $user->getOrCreateGamificationData();
        
        // Check if already logged in today
        if ($gamificationData->last_login_date && 
            $gamificationData->last_login_date->isToday()) {
            return ['success' => false, 'message' => 'Already received daily login bonus'];
        }

        DB::beginTransaction();
        
        try {
            // Award daily login rewards
            $pointsEarned = self::POINTS_DAILY_LOGIN;
            $coinsEarned = self::COINS_DAILY_LOGIN;

            // Consecutive login bonus
            if ($gamificationData->last_login_date && 
                $gamificationData->last_login_date->isYesterday()) {
                $pointsEarned += 10; // Consecutive day bonus
                $coinsEarned += 3;
            }

            $gamificationData->addPoints($pointsEarned, 'daily_login');
            $gamificationData->addCoins($coinsEarned, 'daily_login');
            
            // Update login tracking
            $gamificationData->update(['last_login_date' => now()]);

            // Reset daily quests if needed
            $gamificationData->checkAndResetDailyQuests();

            // Restore some energy
            $gamificationData->updateEnergy();
            $gamificationData->energy = min(100, $gamificationData->energy + 20);
            $gamificationData->save();

            DB::commit();

            return [
                'success' => true,
                'points_earned' => $pointsEarned,
                'coins_earned' => $coinsEarned,
                'energy_restored' => 20,
                'daily_quests' => $gamificationData->daily_quests,
            ];

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in daily login gamification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return ['success' => false, 'message' => 'Failed to process daily login'];
        }
    }

    /**
     * Handle game completion (mini-games within platform)
     */
    public function handleGameCompletion(User $user, $gameId, $score)
    {
        DB::beginTransaction();
        
        try {
            $gamificationData = $user->getOrCreateGamificationData();
            
            // Check energy
            $gamificationData->updateEnergy();
            if ($gamificationData->energy < 5) {
                return ['success' => false, 'message' => 'Not enough energy to play game'];
            }

            $gamificationData->consumeEnergy(5);

            // Calculate rewards based on score
            $pointsEarned = max(5, floor($score / 100)); // Minimum 5 points
            $coinsEarned = max(1, floor($score / 500)); // Minimum 1 coin

            // High score bonus
            $gameScores = $gamificationData->game_scores ?? [];
            $previousBest = $gameScores[$gameId] ?? 0;
            
            if ($score > $previousBest) {
                $gameScores[$gameId] = $score;
                $gamificationData->update(['game_scores' => $gameScores]);
                
                // New high score bonus
                $pointsEarned += 15;
                $coinsEarned += 3;
            }

            $gamificationData->addPoints($pointsEarned, "game_{$gameId}");
            $gamificationData->addCoins($coinsEarned, "game_{$gameId}");

            // Update quest progress
            $questsCompleted = $gamificationData->updateQuestProgress('play_games', 1);

            DB::commit();

            return [
                'success' => true,
                'points_earned' => $pointsEarned,
                'coins_earned' => $coinsEarned,
                'new_high_score' => $score > $previousBest,
                'quests_completed' => $questsCompleted,
            ];

        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'Failed to process game completion'];
        }
    }

    /**
     * Check if user leveled up and handle level up rewards
     */
    private function checkLevelUp(GamificationData $gamificationData)
    {
        $currentLevel = $gamificationData->level;
        $newLevel = $this->calculateLevel($gamificationData->total_points);
        
        if ($newLevel > $currentLevel) {
            $gamificationData->update(['level' => $newLevel]);
            
            // Level up rewards
            $levelUpRewards = $this->getLevelUpRewards($newLevel);
            
            if ($levelUpRewards['coins'] > 0) {
                $gamificationData->addCoins($levelUpRewards['coins'], 'level_up');
            }
            
            if ($levelUpRewards['gems'] > 0) {
                $gamificationData->addGems($levelUpRewards['gems'], 'level_up');
            }

            // Restore energy on level up
            $gamificationData->update(['energy' => 100]);

            return [
                'leveled_up' => true,
                'old_level' => $currentLevel,
                'new_level' => $newLevel,
                'rewards' => $levelUpRewards,
            ];
        }

        return ['leveled_up' => false];
    }

    /**
     * Calculate user level based on total points
     */
    private function calculateLevel($totalPoints)
    {
        // Exponential level curve: Level = floor(sqrt(points / 100))
        return max(1, floor(sqrt($totalPoints / 100)));
    }

    /**
     * Get level up rewards based on new level
     */
    private function getLevelUpRewards($level)
    {
        $baseCoins = 50;
        $baseGems = 2;
        
        // Escalating rewards for higher levels
        $coinsReward = $baseCoins + ($level * 10);
        $gemsReward = $baseGems + floor($level / 5);
        
        // Special milestone rewards
        if ($level % 10 === 0) {
            $coinsReward *= 2;
            $gemsReward *= 2;
        }

        return [
            'coins' => $coinsReward,
            'gems' => $gemsReward,
            'title' => $this->getLevelTitle($level),
        ];
    }

    /**
     * Get title based on level
     */
    private function getLevelTitle($level)
    {
        return match(true) {
            $level >= 100 => 'Grandmaster',
            $level >= 75 => 'Expert',
            $level >= 50 => 'Advanced',
            $level >= 25 => 'Intermediate',
            $level >= 10 => 'Apprentice',
            default => 'Novice'
        };
    }

    /**
     * Check if this is user's first attempt at assessment
     */
    private function isFirstAttempt(User $user, Assessment $assessment)
    {
        return !DB::table('student_answers')
            ->where('user_id', $user->id)
            ->where('assessment_id', $assessment->id)
            ->exists();
    }

    /**
     * Get comprehensive analytics for dashboard
     */
    public function getAnalyticsData(User $user, $timeframe = '7d')
    {
        $cacheKey = "analytics_{$user->id}_{$timeframe}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $timeframe) {
            $days = $this->getTimeframeDays($timeframe);
            $startDate = now()->subDays($days);

            return [
                'learning_sessions' => $this->getLearningSessionsData($user, $startDate),
                'performance_trends' => $this->getPerformanceTrends($user, $startDate),
                'engagement_metrics' => $this->getEngagementMetrics($user, $startDate),
                'streak_analysis' => $this->getStreakAnalysis($user, $startDate),
                'goal_progress' => $this->getGoalProgress($user),
                'recommendations' => $this->generatePersonalizedRecommendations($user),
            ];
        });
    }

    private function getLearningSessionsData(User $user, $startDate)
    {
        return LearningSession::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as sessions,
                SUM(duration_minutes) as total_minutes,
                SUM(points_earned) as points_earned,
                AVG(performance_score) as avg_performance
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPerformanceTrends(User $user, $startDate)
    {
        return DB::table('student_answers')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->where('student_answers.user_id', $user->id)
            ->where('student_answers.created_at', '>=', $startDate)
            ->whereNotNull('student_answers.submitted_at')
            ->selectRaw('
                DATE(student_answers.created_at) as date,
                AVG((student_answers.points_earned / assessments.max_score) * 100) as avg_score,
                COUNT(DISTINCT student_answers.assessment_id) as assessments_taken
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getEngagementMetrics(User $user, $startDate)
    {
        $sessions = LearningSession::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->get();

        return [
            'total_sessions' => $sessions->count(),
            'avg_session_duration' => $sessions->avg('duration_minutes'),
            'longest_session' => $sessions->max('duration_minutes'),
            'favorite_time' => $this->getFavoriteStudyTime($sessions),
            'peak_performance_day' => $this->getPeakPerformanceDay($sessions),
        ];
    }

    private function getStreakAnalysis(User $user, $startDate)
    {
        $gamificationData = $user->getOrCreateGamificationData();
        
        $activityDays = [];
        $currentDate = now();
        
        for ($i = 0; $i < 30; $i++) {
            $date = $currentDate->copy()->subDays($i);
            $hasActivity = LearningSession::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->exists();
                
            $activityDays[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'active' => $hasActivity,
                'intensity' => $hasActivity ? $this->getDayIntensity($user, $date) : 0,
            ];
        }

        return [
            'current_streak' => $gamificationData->current_streak,
            'longest_streak' => $gamificationData->longest_streak,
            'activity_days' => array_reverse($activityDays),
            'streak_prediction' => $this->predictStreakContinuation($activityDays),
        ];
    }

    private function getDayIntensity(User $user, $date)
    {
        $totalMinutes = LearningSession::where('user_id', $user->id)
            ->whereDate('created_at', $date)
            ->sum('duration_minutes');
            
        return min(100, ($totalMinutes / 120) * 100); // 2 hours = 100% intensity
    }

    private function getGoalProgress(User $user)
    {
        $gamificationData = $user->getOrCreateGamificationData();
        
        return [
            'daily_quests' => $gamificationData->daily_quests ?? [],
            'weekly_goals' => $this->getWeeklyGoals($user),
            'monthly_targets' => $this->getMonthlyTargets($user),
        ];
    }

    private function generatePersonalizedRecommendations(User $user)
    {
        $recentSessions = LearningSession::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->with(['course', 'lesson'])
            ->get();

        $recommendations = [];

        // Study time recommendation
        $avgDailyTime = $recentSessions->avg('duration_minutes') ?? 0;
        if ($avgDailyTime < 30) {
            $recommendations['study'] = 'Try to study for at least 30 minutes daily to build momentum';
        } elseif ($avgDailyTime > 120) {
            $recommendations['study'] = 'Great dedication! Consider taking breaks to avoid burnout';
        } else {
            $recommendations['study'] = 'Perfect study rhythm! Keep maintaining this consistency';
        }

        // Course recommendation
        $mostStudiedCourse = $recentSessions->groupBy('course_id')
            ->sortByDesc->count()
            ->first()?->first()?->course;
            
        if ($mostStudiedCourse) {
            $recommendations['course'] = "Continue with {$mostStudiedCourse->title} - you're making great progress!";
        }

        // Challenge recommendation
        $gamificationData = $user->getOrCreateGamificationData();
        if ($gamificationData->current_streak >= 7) {
            $recommendations['challenge'] = 'Challenge: Reach a 14-day streak for exclusive rewards!';
        } else {
            $recommendations['challenge'] = 'Challenge: Build a 7-day learning streak!';
        }

        return $recommendations;
    }

    private function getTimeframeDays($timeframe)
    {
        return match($timeframe) {
            '24h' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 7
        };
    }

    private function getFavoriteStudyTime($sessions)
    {
        $hourCounts = $sessions->groupBy(function($session) {
            return $session->created_at->hour;
        });

        $favoriteHour = $hourCounts->sortByDesc->count()->keys()->first();
        
        return $favoriteHour ? sprintf('%02d:00', $favoriteHour) : 'Not enough data';
    }

    private function getPeakPerformanceDay($sessions)
    {
        $dayAverages = $sessions->groupBy(function($session) {
            return $session->created_at->dayOfWeek;
        })->map(function($daySessions) {
            return $daySessions->avg('performance_score');
        });

        $bestDay = $dayAverages->sortDesc()->keys()->first();
        
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        return $bestDay !== null ? $dayNames[$bestDay] : 'Not enough data';
    }

    private function predictStreakContinuation($activityDays)
    {
        $recentActivity = array_slice($activityDays, -7);
        $activeCount = count(array_filter($recentActivity, fn($day) => $day['active']));
        
        return [
            'likelihood' => min(100, ($activeCount / 7) * 100),
            'confidence' => $activeCount >= 5 ? 'high' : ($activeCount >= 3 ? 'medium' : 'low'),
            'recommendation' => $activeCount >= 5 ? 'streak_likely' : 'needs_consistency'
        ];
    }

    private function getWeeklyGoals(User $user)
    {
        $startOfWeek = now()->startOfWeek();
        
        $weeklyStats = LearningSession::where('user_id', $user->id)
            ->where('created_at', '>=', $startOfWeek)
            ->selectRaw('
                COUNT(*) as sessions,
                SUM(duration_minutes) as total_minutes,
                COUNT(DISTINCT course_id) as courses_studied
            ')
            ->first();

        return [
            'study_time' => [
                'current' => $weeklyStats->total_minutes ?? 0,
                'goal' => 300, // 5 hours
                'progress' => min(100, (($weeklyStats->total_minutes ?? 0) / 300) * 100)
            ],
            'lessons' => [
                'current' => $weeklyStats->sessions ?? 0,
                'goal' => 10,
                'progress' => min(100, (($weeklyStats->sessions ?? 0) / 10) * 100)
            ],
            'courses' => [
                'current' => $weeklyStats->courses_studied ?? 0,
                'goal' => 3,
                'progress' => min(100, (($weeklyStats->courses_studied ?? 0) / 3) * 100)
            ]
        ];
    }

    private function getMonthlyTargets(User $user)
    {
        $startOfMonth = now()->startOfMonth();
        
        $monthlyStats = [
            'courses_completed' => $user->courses()
                ->wherePivot('updated_at', '>=', $startOfMonth)
                ->wherePivot('progress', 100)
                ->count(),
            'total_study_time' => LearningSession::where('user_id', $user->id)
                ->where('created_at', '>=', $startOfMonth)
                ->sum('duration_minutes'),
            'badges_earned' => $user->badges()
                ->where('created_at', '>=', $startOfMonth)
                ->count(),
        ];

        return [
            'courses' => [
                'current' => $monthlyStats['courses_completed'],
                'goal' => 2,
                'progress' => min(100, ($monthlyStats['courses_completed'] / 2) * 100)
            ],
            'study_time' => [
                'current' => $monthlyStats['total_study_time'],
                'goal' => 1200, // 20 hours
                'progress' => min(100, ($monthlyStats['total_study_time'] / 1200) * 100)
            ],
            'achievements' => [
                'current' => $monthlyStats['badges_earned'],
                'goal' => 3,
                'progress' => min(100, ($monthlyStats['badges_earned'] / 3) * 100)
            ]
        ];
    }

    /**
     * Generate real-time leaderboard data
     */
    public function getLeaderboardData($limit = 10)
    {
        return Cache::remember("leaderboard_top_{$limit}", 300, function () use ($limit) {
            return GamificationData::with('user')
                ->orderBy('total_points', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($data, $index) {
                    return [
                        'rank' => $index + 1,
                        'user' => [
                            'id' => $data->user->id,
                            'name' => $data->user->name,
                            'avatar' => $data->user->profile_picture,
                        ],
                        'points' => $data->total_points,
                        'level' => $data->level,
                        'streak' => $data->current_streak,
                        'badges_count' => $data->user->badges()->count(),
                        'recent_activity' => LearningSession::where('user_id', $data->user_id)
                            ->where('created_at', '>=', now()->subDays(7))
                            ->count(),
                    ];
                });
        });
    }

    /**
     * Award special event rewards
     */
    public function awardSpecialEvent(User $user, $eventType, $multiplier = 1)
    {
        $gamificationData = $user->getOrCreateGamificationData();
        
        $rewards = match($eventType) {
            'weekend_warrior' => ['points' => 100, 'coins' => 25, 'gems' => 2],
            'night_owl' => ['points' => 50, 'coins' => 15, 'gems' => 1],
            'early_bird' => ['points' => 50, 'coins' => 15, 'gems' => 1],
            'marathon_session' => ['points' => 200, 'coins' => 50, 'gems' => 5],
            'perfect_week' => ['points' => 500, 'coins' => 100, 'gems' => 10],
            default => ['points' => 25, 'coins' => 5, 'gems' => 0]
        };

        $finalRewards = [
            'points' => $rewards['points'] * $multiplier,
            'coins' => $rewards['coins'] * $multiplier,
            'gems' => $rewards['gems'] * $multiplier,
        ];

        DB::beginTransaction();
        
        try {
            $gamificationData->addPoints($finalRewards['points'], "event_{$eventType}");
            $gamificationData->addCoins($finalRewards['coins'], "event_{$eventType}");
            
            if ($finalRewards['gems'] > 0) {
                $gamificationData->addGems($finalRewards['gems'], "event_{$eventType}");
            }

            DB::commit();
            
            return [
                'success' => true,
                'event_type' => $eventType,
                'rewards' => $finalRewards,
                'message' => $this->getEventMessage($eventType)
            ];

        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'Failed to award event rewards'];
        }
    }

    private function getEventMessage($eventType)
    {
        return match($eventType) {
            'weekend_warrior' => 'Weekend Warrior bonus! Learning on weekends shows true dedication!',
            'night_owl' => 'Night Owl bonus! Late night learning sessions earn extra rewards!',
            'early_bird' => 'Early Bird bonus! Morning study sessions boost your brain power!',
            'marathon_session' => 'Marathon Session bonus! Long study sessions show commitment!',
            'perfect_week' => 'Perfect Week bonus! You studied every day this week!',
            default => 'Special event bonus earned!'
        };
    }

    /**
     * Export analytics data for external use
     */
    public function exportAnalytics(User $user, $format = 'json')
    {
        $data = [
            'user_id' => $user->id,
            'export_date' => now()->toISOString(),
            'gamification_summary' => $user->getGamificationSummary(),
            'learning_history' => LearningSession::where('user_id', $user->id)
                ->with(['course:id,title', 'lesson:id,title'])
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get(),
            'achievement_history' => $user->badges()->orderBy('created_at', 'desc')->get(),
            'performance_metrics' => $this->getPerformanceMetrics($user),
            'time_analytics' => $this->getTimeAnalytics($user),
        ];

        return match($format) {
            'csv' => $this->convertToCSV($data),
            'pdf' => $this->generatePDFReport($data),
            default => $data
        };
    }

    private function getPerformanceMetrics(User $user)
    {
        return [
            'total_study_time' => LearningSession::where('user_id', $user->id)->sum('duration_minutes'),
            'average_session_duration' => LearningSession::where('user_id', $user->id)->avg('duration_minutes'),
            'completion_rate' => $this->calculateCompletionRate($user),
            'improvement_trend' => $this->calculateImprovementTrend($user),
            'subject_strengths' => $this->getSubjectStrengths($user),
        ];
    }

    private function getTimeAnalytics(User $user)
    {
        $sessions = LearningSession::where('user_id', $user->id)
            ->selectRaw('
                HOUR(created_at) as hour,
                DAYOFWEEK(created_at) as day_of_week,
                AVG(duration_minutes) as avg_duration,
                COUNT(*) as session_count
            ')
            ->groupBy('hour', 'day_of_week')
            ->get();

        return [
            'peak_hours' => $sessions->groupBy('hour')->map->avg('avg_duration')->sortDesc()->take(3),
            'peak_days' => $sessions->groupBy('day_of_week')->map->sum('session_count')->sortDesc(),
            'study_patterns' => $this->analyzeStudyPatterns($sessions),
        ];
    }

    /**
     * Real-time notification system for achievements
     */
    public function broadcastAchievement(User $user, $achievement)
    {
        // This would integrate with Laravel Broadcasting
        broadcast(new \App\Events\AchievementEarned($user, $achievement))->toOthers();
        
        // Store notification for user
        $user->notifications()->create([
            'type' => 'achievement',
            'data' => $achievement,
            'read_at' => null,
        ]);
        
        return true;
    }

    /**
     * Calculate dynamic difficulty adjustment
     */
    public function calculateDifficultyAdjustment(User $user)
    {
        $recentPerformance = DB::table('student_answers')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->where('student_answers.user_id', $user->id)
            ->where('student_answers.created_at', '>=', now()->subDays(7))
            ->avg(DB::raw('(student_answers.points_earned / assessments.max_score) * 100'));

        if ($recentPerformance >= 90) {
            return 'increase'; // Make content more challenging
        } elseif ($recentPerformance <= 60) {
            return 'decrease'; // Make content easier
        }
        
        return 'maintain'; // Keep current difficulty
    }

    /**
     * Generate personalized learning path
     */
    public function generateLearningPath(User $user)
    {
        $completedCourses = $user->courses()->wherePivot('progress', 100)->pluck('courses.id');
        $currentCourses = $user->enrollments()->where('progress', '<', 100)->with('course')->get();
        
        $weakAreas = $this->identifyWeakAreas($user);
        $strengths = $this->identifyStrengths($user);
        
        return [
            'next_recommended_courses' => $this->getRecommendedCourses($user, $completedCourses),
            'focus_areas' => $weakAreas,
            'build_on_strengths' => $strengths,
            'estimated_completion_time' => $this->estimateCompletionTime($currentCourses),
            'difficulty_adjustment' => $this->calculateDifficultyAdjustment($user),
        ];
    }

    private function identifyWeakAreas(User $user)
    {
        return DB::table('student_answers')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->join('courses', 'assessments.course_id', '=', 'courses.id')
            ->where('student_answers.user_id', $user->id)
            ->selectRaw('
                courses.title,
                AVG((student_answers.points_earned / assessments.max_score) * 100) as avg_score
            ')
            ->groupBy('courses.id', 'courses.title')
            ->having('avg_score', '<', 75)
            ->orderBy('avg_score')
            ->limit(3)
            ->get();
    }

    private function identifyStrengths(User $user)
    {
        return DB::table('student_answers')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->join('courses', 'assessments.course_id', '=', 'courses.id')
            ->where('student_answers.user_id', $user->id)
            ->selectRaw('
                courses.title,
                AVG((student_answers.points_earned / assessments.max_score) * 100) as avg_score
            ')
            ->groupBy('courses.id', 'courses.title')
            ->having('avg_score', '>=', 85)
            ->orderBy('avg_score', 'desc')
            ->limit(3)
            ->get();
    }
}