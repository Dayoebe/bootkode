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
use App\Traits\HasWallet; // Import the trait
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity, HasWallet; 

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
        'bank_code',
        'account_number',
        'account_name',
        'account_verified',
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
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
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

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function blogComments()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function blogReactions()
    {
        return $this->hasMany(BlogReaction::class);
    }

    public function blogBookmarks()
    {
        return $this->blogReactions()->where('type', 'bookmark');
    }

    public function mockInterviews()
    {
        return $this->hasMany(MockInterview::class);
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
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 'user_id', 'course_id')
            ->withTimestamps();
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


    public function resumeProfile()
    {
        return $this->hasOne(ResumeProfile::class);
    }

    // Helper method to get or create resume profile
    public function getOrCreateResumeProfile()
    {
        if (!$this->resumeProfile) {
            return $this->resumeProfile()->create([
                'full_name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone_number,
                'location' => $this->getFullAddressAttribute(),
                'professional_summary' => $this->bio,
            ]);
        }

        return $this->resumeProfile;
    }

    // Resume-related helper methods
    public function hasCompleteResume()
    {
        $resume = $this->resumeProfile;
        if (!$resume)
            return false;

        return $resume->canBeExported();
    }

    public function getResumeCompletionPercentage()
    {
        $resume = $this->resumeProfile;
        return $resume ? $resume->getCompletionPercentage() : 0;
    }

    public function getResumeQualityScore()
    {
        $resume = $this->resumeProfile;
        return $resume ? $resume->getQualityScore() : 0;
    }











































    /**
     * Gamification relationships and methods
     */
    public function gamificationData()
    {
        return $this->hasOne(GamificationData::class);
    }

    public function badges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function storePurchases()
    {
        return $this->hasMany(UserStorePurchase::class);
    }

    public function gamificationTransactions()
    {
        return $this->hasMany(GamificationTransaction::class);
    }

    public function getOrCreateGamificationData()
    {
        if (!$this->gamificationData) {
            $data = $this->gamificationData()->create([
                'last_quest_reset' => now()->startOfDay(),
            ]);
            $data->generateDailyQuests();
            return $data;
        }

        return $this->gamificationData;
    }

    // Quick access methods for gamification stats
    public function getLevel()
    {
        return $this->getOrCreateGamificationData()->level;
    }

    public function getTotalPoints()
    {
        return $this->getOrCreateGamificationData()->total_points;
    }

    public function getCoins()
    {
        return $this->getOrCreateGamificationData()->coins;
    }

    public function getGems()
    {
        return $this->getOrCreateGamificationData()->gems;
    }

    public function getCurrentStreak()
    {
        return $this->getOrCreateGamificationData()->current_streak;
    }

    public function getEnergy()
    {
        $data = $this->getOrCreateGamificationData();
        $data->updateEnergy();
        return $data->energy;
    }

    // Achievement checking method
    public function checkAllAchievements()
    {
        $badges = [];
        $data = $this->getOrCreateGamificationData();

        // Level achievements
        $levelBadges = [
            5 => [
                'badge_type' => 'level',
                'badge_name' => 'Rising Star',
                'badge_description' => 'Reached level 5',
                'badge_icon' => 'fas fa-star',
                'badge_color' => '#F59E0B',
                'rarity' => 'common',
                'points_reward' => 50
            ],
            10 => [
                'badge_type' => 'level',
                'badge_name' => 'Dedicated Learner',
                'badge_description' => 'Reached level 10',
                'badge_icon' => 'fas fa-graduation-cap',
                'badge_color' => '#3B82F6',
                'rarity' => 'rare',
                'points_reward' => 100
            ],
            25 => [
                'badge_type' => 'level',
                'badge_name' => 'Knowledge Seeker',
                'badge_description' => 'Reached level 25',
                'badge_icon' => 'fas fa-search',
                'badge_color' => '#8B5CF6',
                'rarity' => 'epic',
                'points_reward' => 250
            ],
            50 => [
                'badge_type' => 'level',
                'badge_name' => 'Learning Legend',
                'badge_description' => 'Reached level 50',
                'badge_icon' => 'fas fa-crown',
                'badge_color' => '#F59E0B',
                'rarity' => 'legendary',
                'points_reward' => 500
            ],
        ];

        foreach ($levelBadges as $level => $badgeData) {
            if ($data->level >= $level) {
                $badge = UserBadge::awardBadge($this->id, "level_{$level}", $badgeData);
                if ($badge->wasRecentlyCreated) {
                    $badges[] = $badge;
                    $data->addPoints($badgeData['points_reward'], 'achievement');
                }
            }
        }

        // Streak achievements
        $streakBadges = [
            7 => [
                'badge_type' => 'streak',
                'badge_name' => 'Week Warrior',
                'badge_description' => '7-day learning streak',
                'badge_icon' => 'fas fa-fire',
                'badge_color' => '#EF4444',
                'rarity' => 'common',
                'points_reward' => 75
            ],
            30 => [
                'badge_type' => 'streak',
                'badge_name' => 'Month Master',
                'badge_description' => '30-day learning streak',
                'badge_icon' => 'fas fa-calendar-check',
                'badge_color' => '#F59E0B',
                'rarity' => 'rare',
                'points_reward' => 300
            ],
            100 => [
                'badge_type' => 'streak',
                'badge_name' => 'Century Scholar',
                'badge_description' => '100-day learning streak',
                'badge_icon' => 'fas fa-crown',
                'badge_color' => '#8B5CF6',
                'rarity' => 'legendary',
                'points_reward' => 1000
            ],
        ];

        foreach ($streakBadges as $days => $badgeData) {
            if ($data->longest_streak >= $days) {
                $badge = UserBadge::awardBadge($this->id, "streak_{$days}", $badgeData);
                if ($badge->wasRecentlyCreated) {
                    $badges[] = $badge;
                    $data->addPoints($badgeData['points_reward'], 'achievement');
                }
            }
        }

        // Course completion achievements
        $completedCourses = $this->courses()->wherePivot('progress', 100)->count();
        $courseBadges = [
            1 => [
                'badge_type' => 'completion',
                'badge_name' => 'First Steps',
                'badge_description' => 'Completed your first course',
                'badge_icon' => 'fas fa-baby',
                'badge_color' => '#10B981',
                'rarity' => 'common',
                'points_reward' => 100
            ],
            5 => [
                'badge_type' => 'completion',
                'badge_name' => 'Course Collector',
                'badge_description' => 'Completed 5 courses',
                'badge_icon' => 'fas fa-books',
                'badge_color' => '#3B82F6',
                'rarity' => 'rare',
                'points_reward' => 500
            ],
            10 => [
                'badge_type' => 'completion',
                'badge_name' => 'Learning Expert',
                'badge_description' => 'Completed 10 courses',
                'badge_icon' => 'fas fa-user-graduate',
                'badge_color' => '#8B5CF6',
                'rarity' => 'epic',
                'points_reward' => 1000
            ],
        ];

        foreach ($courseBadges as $count => $badgeData) {
            if ($completedCourses >= $count) {
                $badge = UserBadge::awardBadge($this->id, "courses_{$count}", $badgeData);
                if ($badge->wasRecentlyCreated) {
                    $badges[] = $badge;
                    $data->addPoints($badgeData['points_reward'], 'achievement');
                }
            }
        }

        // Quiz performance achievements
        $averageScore = $this->getAverageQuizScore();
        $quizBadges = [
            80 => [
                'badge_type' => 'performance',
                'badge_name' => 'Quiz Master',
                'badge_description' => '80%+ average quiz score',
                'badge_icon' => 'fas fa-brain',
                'badge_color' => '#3B82F6',
                'rarity' => 'rare',
                'points_reward' => 200
            ],
            90 => [
                'badge_type' => 'performance',
                'badge_name' => 'Quiz Champion',
                'badge_description' => '90%+ average quiz score',
                'badge_icon' => 'fas fa-trophy',
                'badge_color' => '#F59E0B',
                'rarity' => 'epic',
                'points_reward' => 400
            ],
            95 => [
                'badge_type' => 'performance',
                'badge_name' => 'Quiz Legend',
                'badge_description' => '95%+ average quiz score',
                'badge_icon' => 'fas fa-crown',
                'badge_color' => '#8B5CF6',
                'rarity' => 'legendary',
                'points_reward' => 800
            ],
        ];

        foreach ($quizBadges as $score => $badgeData) {
            if ($averageScore >= $score) {
                $badge = UserBadge::awardBadge($this->id, "quiz_score_{$score}", $badgeData);
                if ($badge->wasRecentlyCreated) {
                    $badges[] = $badge;
                    $data->addPoints($badgeData['points_reward'], 'achievement');
                }
            }
        }

        return $badges;
    }

    // Helper method to calculate average quiz score
    private function getAverageQuizScore()
    {
        return \DB::table('student_answers')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->where('student_answers.user_id', $this->id)
            ->whereNotNull('student_answers.submitted_at')
            ->selectRaw('AVG((student_answers.points_earned / assessments.max_score) * 100) as avg_score')
            ->value('avg_score') ?? 0;
    }

    // Method to handle activity completion with gamification rewards
    public function completeActivity($activityType, $activityData = [])
    {
        $gamificationService = new \App\Services\GamificationService();

        switch ($activityType) {
            case 'lesson':
                return $gamificationService->handleLessonCompletion($this, $activityData['lesson'] ?? null);
            case 'quiz':
                return $gamificationService->handleQuizCompletion(
                    $this,
                    $activityData['assessment'] ?? null,
                    $activityData['score'] ?? 0,
                    $activityData['passed'] ?? false
                );
            case 'daily_login':
                return $gamificationService->handleDailyLogin($this);
            case 'game':
                return $gamificationService->handleGameCompletion(
                    $this,
                    $activityData['game_id'],
                    $activityData['score']
                );
            default:
                return ['success' => false, 'message' => 'Unknown activity type'];
        }
    }

    // Get user's gamification summary for dashboard
    public function getGamificationSummary()
    {
        $data = $this->getOrCreateGamificationData();
        $data->updateEnergy();

        return [
            'level' => $data->level,
            'total_points' => $data->total_points,
            'coins' => $data->coins,
            'gems' => $data->gems,
            'energy' => $data->energy,
            'current_streak' => $data->current_streak,
            'longest_streak' => $data->longest_streak,
            'progress_to_next_level' => $data->progress_percentage,
            'badges_count' => $this->badges()->count(),
            'rank' => $this->getGlobalRank(),
            'recent_achievements' => $this->badges()->latest()->limit(3)->get(),
            'active_quests' => $data->daily_quests ?? [],
        ];
    }

    // Get user's rank among all users
    private function getGlobalRank()
    {
        return GamificationData::where('total_points', '>', $this->getTotalPoints())->count() + 1;
    }

    // Check if user can afford something
    public function canAfford($coins = 0, $gems = 0)
    {
        $data = $this->getOrCreateGamificationData();
        return $data->coins >= $coins && $data->gems >= $gems;
    }

    // Spend currency with validation
    public function spendCurrency($coins = 0, $gems = 0, $purpose = 'purchase')
    {
        if (!$this->canAfford($coins, $gems)) {
            return false;
        }

        $data = $this->getOrCreateGamificationData();

        if ($coins > 0) {
            $data->spendCoins($coins, $purpose);
        }

        if ($gems > 0) {
            $data->spendGems($gems, $purpose);
        }

        return true;
    }

    // Get user's equipped items
    public function getEquippedItems()
    {
        return $this->storePurchases()
            ->where('is_equipped', true)
            ->with('item')
            ->get()
            ->groupBy('item.item_type');
    }

    // Equip/unequip store items
    public function toggleEquipItem($purchaseId)
    {
        $purchase = $this->storePurchases()->find($purchaseId);
        if (!$purchase) {
            return false;
        }

        // Unequip other items of the same type (for avatars, themes, etc.)
        if (in_array($purchase->item->item_type, ['avatar', 'theme'])) {
            $this->storePurchases()
                ->whereHas('item', function ($q) use ($purchase) {
                    $q->where('item_type', $purchase->item->item_type);
                })
                ->update(['is_equipped' => false]);
        }

        $purchase->update(['is_equipped' => !$purchase->is_equipped]);
        return true;
    }

    // Get formatted energy status
    public function getEnergyStatus()
    {
        $data = $this->getOrCreateGamificationData();
        $data->updateEnergy();

        $timeToFullEnergy = (100 - $data->energy) * 5; // 5 minutes per energy point
        $nextEnergyIn = 5 - ($data->energy_last_updated->diffInMinutes(now()) % 5);

        return [
            'current' => $data->energy,
            'max' => 100,
            'percentage' => $data->energy,
            'time_to_full' => $timeToFullEnergy,
            'next_energy_in' => $nextEnergyIn,
            'is_full' => $data->energy >= 100,
        ];
    }

    // Get user's game statistics
    public function getGameStats()
    {
        $data = $this->getOrCreateGamificationData();
        $gameScores = $data->game_scores ?? [];

        $stats = [
            'total_games_played' => 0,
            'best_scores' => $gameScores,
            'favorite_game' => null,
            'total_game_time' => 0,
        ];

        // Calculate stats from transactions
        $gameTransactions = $this->gamificationTransactions()
            ->where('source', 'like', 'game_%')
            ->get();

        $stats['total_games_played'] = $gameTransactions->count();

        // Find favorite game (most played)
        $gameCounts = $gameTransactions->groupBy('source')->map->count();
        if ($gameCounts->isNotEmpty()) {
            $favoriteGameSource = $gameCounts->keys()->sortByDesc(function ($key) use ($gameCounts) {
                return $gameCounts[$key];
            })->first();
            $stats['favorite_game'] = str_replace('game_', '', $favoriteGameSource);
        }

        return $stats;
    }

    // Daily quest completion tracking
    public function updateQuestProgress($questType, $amount = 1)
    {
        $data = $this->getOrCreateGamificationData();
        return $data->updateQuestProgress($questType, $amount);
    }

    // Get achievement progress for specific categories
    public function getAchievementProgress()
    {
        $data = $this->getOrCreateGamificationData();

        return [
            'level_progress' => [
                'current' => $data->level,
                'next_milestone' => $this->getNextLevelMilestone($data->level),
                'progress' => $data->progress_percentage,
            ],
            'streak_progress' => [
                'current' => $data->current_streak,
                'longest' => $data->longest_streak,
                'next_milestone' => $this->getNextStreakMilestone($data->current_streak),
            ],
            'course_progress' => [
                'completed' => $this->courses()->wherePivot('progress', 100)->count(),
                'next_milestone' => $this->getNextCourseMilestone($this->courses()->wherePivot('progress', 100)->count()),
            ],
            'quiz_performance' => [
                'average_score' => round($this->getAverageQuizScore(), 1),
                'next_milestone' => $this->getNextScoreMilestone($this->getAverageQuizScore()),
            ],
        ];
    }

    private function getNextLevelMilestone($currentLevel)
    {
        $milestones = [5, 10, 25, 50, 100];
        foreach ($milestones as $milestone) {
            if ($currentLevel < $milestone) {
                return $milestone;
            }
        }
        return null;
    }

    private function getNextStreakMilestone($currentStreak)
    {
        $milestones = [7, 30, 100, 365];
        foreach ($milestones as $milestone) {
            if ($currentStreak < $milestone) {
                return $milestone;
            }
        }
        return null;
    }

    private function getNextCourseMilestone($completedCourses)
    {
        $milestones = [1, 5, 10, 25, 50];
        foreach ($milestones as $milestone) {
            if ($completedCourses < $milestone) {
                return $milestone;
            }
        }
        return null;
    }

    private function getNextScoreMilestone($averageScore)
    {
        $milestones = [80, 90, 95, 98];
        foreach ($milestones as $milestone) {
            if ($averageScore < $milestone) {
                return $milestone;
            }
        }
        return null;
    }

}