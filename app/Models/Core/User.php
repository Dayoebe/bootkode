<?php

namespace App\Models\Core;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasWallet;
use App\Traits\HasAffiliate;
use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasWallet, HasAffiliate, HasActivityLogs;

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ACADEMY_ADMIN = 'academy_admin';
    const ROLE_INSTRUCTOR = 'instructor';
    const ROLE_MENTOR = 'mentor';
    const ROLE_CONTENT_EDITOR = 'content_editor';
    const ROLE_AFFILIATE_AMBASSADOR = 'affiliate_ambassador';
    const ROLE_STUDENT = 'student';

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
        'referred_by',
        'referral_source',
        'marketplace_items',
        'profile_visibility',
        'show_email_publicly',
        'deactivated_at',
        'favorite_courses',
        'timezone',
        'language',
        'is_profile_public',
        'show_phone_publicly',
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
        'skills' => 'array',
        'favorite_courses' => 'array',
        'is_active' => 'boolean',
        'receive_course_updates' => 'boolean',
        'receive_certificate_notifications' => 'boolean',
        'show_email_publicly' => 'boolean',
        'deactivated_at' => 'datetime',
        'is_profile_public' => 'boolean',
        'show_phone_publicly' => 'boolean',
    ];

    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role) {
                $user->syncRoles([$user->role]);
            }
        });

        static::updated(function ($user) {
            if ($user->isDirty('role') && $user->role) {
                $user->syncRoles([$user->role]);
            }
        });
    }

    // ============================================
    // ROLE CHECKING METHODS
    // ============================================

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
        return !$this->isStudent();
    }

    // ============================================
    // MARKETPLACE & VENDOR RELATIONSHIPS
    // ============================================

    public function marketplaceItems()
    {
        return $this->hasMany(\App\Models\Marketplace\MarketplaceItem::class, 'vendor_id');
    }

    public function customerOrders()
    {
        return $this->hasMany(\App\Models\Marketplace\MarketplaceOrder::class, 'customer_id');
    }

    public function vendorOrders()
    {
        return $this->hasMany(\App\Models\Marketplace\MarketplaceOrder::class, 'vendor_id');
    }

    public function isVendor(): bool
    {
        return $this->marketplaceItems()->exists() || $this->canManageCourses();
    }

    public function getVendorOrderStats()
    {
        return [
            'total_orders' => $this->vendorOrders()->count(),
            'pending_orders' => $this->vendorOrders()->where('status', \App\Models\Marketplace\MarketplaceOrder::STATUS_PENDING)->count(),
            'completed_orders' => $this->vendorOrders()->where('status', \App\Models\Marketplace\MarketplaceOrder::STATUS_COMPLETED)->count(),
            'total_earnings' => $this->vendorOrders()->paid()->sum('vendor_earning'),
        ];
    }

    // ============================================
    // LEARNING & COURSES RELATIONSHIPS
    // ============================================

    public function courses()
    {
        return $this->belongsToMany(\App\Models\Learning\Course::class, 'course_user')
            ->withTimestamps()
            ->withPivot(['last_accessed_at', 'progress']);
    }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Learning\Course::class, 'course_enrollments', 'user_id', 'course_id')
            ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(\App\Models\Learning\CourseEnrollment::class);
    }

    public function completedLessons()
    {
        return $this->belongsToMany(\App\Models\Learning\Lesson::class, 'lesson_user')
            ->withTimestamps()
            ->withPivot(['completed_at']);
    }

    public function offlineNotes()
    {
        return $this->hasMany(\App\Models\Learning\OfflineNote::class);
    }

    public function wishlists()
    {
        return $this->hasMany(\App\Models\Career\Wishlist::class);
    }

    public function savedResources()
    {
        return $this->hasMany(\App\Models\Learning\SavedResource::class);
    }

    public function downloadedContent()
    {
        return $this->hasMany(\App\Models\Learning\DownloadableContent::class);
    }

    public function hasCompletedCourse(\App\Models\Learning\Course $course): bool
    {
        $totalLessons = $course->sections()->with('lessons')->get()->sum(function ($section) {
            return $section->lessons->count();
        });

        $completedLessons = $this->completedLessons()
            ->whereIn('lessons.id', $course->sections()->with('lessons')->get()->flatMap->lessons->pluck('id'))
            ->count();

        return $completedLessons >= $totalLessons;
    }

    // ============================================
    // CREDENTIALS & CERTIFICATES
    // ============================================

    public function certificates()
    {
        return $this->hasMany(\App\Models\Credentials\Certificate::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Learning\CourseReview::class, 'user_id');
    }

    // ============================================
    // CONTENT & PUBLISHING
    // ============================================

    public function blogPosts()
    {
        return $this->hasMany(\App\Models\Content\BlogPost::class, 'author_id');
    }

    public function blogComments()
    {
        return $this->hasMany(\App\Models\Content\BlogComment::class);
    }

    public function blogReactions()
    {
        return $this->hasMany(\App\Models\Content\BlogReaction::class);
    }

    public function blogBookmarks()
    {
        return $this->blogReactions()->where('type', 'bookmark');
    }

    public function createdPages()
    {
        return $this->hasMany(\App\Models\Content\Page::class, 'created_by');
    }

    public function portfolios()
    {
        return $this->hasMany(\App\Models\Content\Portfolio::class);
    }

    public function resumeProfile()
    {
        return $this->hasOne(\App\Models\Content\ResumeProfile::class);
    }

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

    public function hasCompleteResume()
    {
        $resume = $this->resumeProfile;
        return $resume ? $resume->canBeExported() : false;
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

    // ============================================
    // MENTORSHIP & INTERVIEWS
    // ============================================

    public function mockInterviews()
    {
        return $this->hasMany(\App\Models\Mentorship\Mentorship\MockInterview::class);
    }

    public function interviewQuestions()
    {
        return $this->hasMany(\App\Models\Mentorship\Mentorship\InterviewQuestion::class, 'created_by');
    }

    public function questionSets()
    {
        return $this->hasMany(\App\Models\Mentorship\Mentorship\InterviewQuestionSet::class, 'created_by');
    }

    // ============================================
    // GAMIFICATION
    // ============================================

    public function gamificationData()
    {
        return $this->hasOne(\App\Models\Credentials\GamificationData::class);
    }

    public function badges()
    {
        return $this->hasMany(\App\Models\Credentials\UserBadge::class);
    }

    public function storePurchases()
    {
        return $this->hasMany(\App\Models\Marketplace\UserStorePurchase::class);
    }

    public function gamificationTransactions()
    {
        return $this->hasMany(\App\Models\Credentials\GamificationTransaction::class);
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

    public function canAfford($coins = 0, $gems = 0)
    {
        $data = $this->getOrCreateGamificationData();
        return $data->coins >= $coins && $data->gems >= $gems;
    }

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

    public function getEquippedItems()
    {
        return $this->storePurchases()
            ->where('is_equipped', true)
            ->with('item')
            ->get()
            ->groupBy('item.item_type');
    }

    public function toggleEquipItem($purchaseId)
    {
        $purchase = $this->storePurchases()->find($purchaseId);
        if (!$purchase) {
            return false;
        }

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

    public function getEnergyStatus()
    {
        $data = $this->getOrCreateGamificationData();
        $data->updateEnergy();

        $timeToFullEnergy = (100 - $data->energy) * 5;
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

        $gameTransactions = $this->gamificationTransactions()
            ->where('source', 'like', 'game_%')
            ->get();

        $stats['total_games_played'] = $gameTransactions->count();

        $gameCounts = $gameTransactions->groupBy('source')->map->count();
        if ($gameCounts->isNotEmpty()) {
            $favoriteGameSource = $gameCounts->keys()->sortByDesc(function ($key) use ($gameCounts) {
                return $gameCounts[$key];
            })->first();
            $stats['favorite_game'] = str_replace('game_', '', $favoriteGameSource);
        }

        return $stats;
    }

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

    private function getGlobalRank()
    {
        return \App\Models\Credentials\GamificationData::where('total_points', '>', $this->getTotalPoints())->count() + 1;
    }

    // ============================================
    // AFFILIATE & REFERRAL
    // ============================================

    public function affiliate()
    {
        return $this->hasOne(\App\Models\Marketplace\Affiliate::class);
    }

    public function referralRecord()
    {
        return $this->hasOne(\App\Models\Marketplace\Referral::class, 'referred_user_id');
    }

    public function isAffiliate(): bool
    {
        return $this->affiliate()->exists();
    }

    public function wasReferred(): bool
    {
        return $this->referralRecord()->exists();
    }

    public function canBecomeAffiliate(): bool
    {
        return $this->hasRole([
            self::ROLE_INSTRUCTOR,
            self::ROLE_AFFILIATE_AMBASSADOR,
            self::ROLE_SUPER_ADMIN
        ]);
    }

    public function applyForAffiliate(array $additionalData = []): ?\App\Models\Marketplace\Affiliate
    {
        if (!$this->canBecomeAffiliate() || $this->affiliate) {
            return null;
        }

        $isAutoApproved = $this->hasRole([self::ROLE_INSTRUCTOR, self::ROLE_SUPER_ADMIN]);

        return \App\Models\Marketplace\Affiliate::create([
            'user_id' => $this->id,
            'commission_rate' => 30.00,
            'status' => $isAutoApproved ? \App\Models\Marketplace\Affiliate::STATUS_ACTIVE : \App\Models\Marketplace\Affiliate::STATUS_INACTIVE,
            'approved_at' => $isAutoApproved ? now() : null,
            'approved_by' => $isAutoApproved ? $this->id : null,
            'metadata' => array_merge([
                'application_date' => now(),
                'auto_approved' => $isAutoApproved,
                'user_role' => $this->role
            ], $additionalData)
        ]);
    }

    public function getAffiliateBalance(): float
    {
        if (!$this->isAffiliate()) {
            return 0;
        }

        $commissionTransactions = $this->getCommissionTransactions();
        return $commissionTransactions->where('type', 'credit')->sum('amount') -
            $commissionTransactions->where('type', 'debit')->sum('amount');
    }

    public function getMonthlyAffiliatePerformance(int $months = 12): array
    {
        if (!$this->isAffiliate()) {
            return [];
        }

        $startDate = now()->subMonths($months);

        return \App\Models\Marketplace\ReferralTransaction::whereHas('referral', function ($query) {
            $query->where('affiliate_id', $this->affiliate->id);
        })
            ->where('status', \App\Models\Marketplace\ReferralTransaction::STATUS_PAID)
            ->where('paid_at', '>=', $startDate)
            ->selectRaw('
                YEAR(paid_at) as year,
                MONTH(paid_at) as month,
                SUM(commission_amount) as total_commission,
                COUNT(*) as total_sales,
                COUNT(DISTINCT course_id) as unique_courses
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => "{$item->year}-" . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'month_name' => date('F Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                    'total_commission' => $item->total_commission,
                    'total_sales' => $item->total_sales,
                    'unique_courses' => $item->unique_courses,
                    'formatted_commission' => '₦' . number_format($item->total_commission, 2)
                ];
            })
            ->toArray();
    }

    public function getAffiliateStats(): array
    {
        if (!$this->isAffiliate()) {
            return [
                'formatted_total_earned' => '₦0.00',
                'total_referrals' => 0,
                'active_referrals' => 0,
                'pending_commissions' => 0,
                'referral_link' => '#',
            ];
        }

        $affiliate = $this->affiliate;

        return [
            'formatted_total_earned' => $affiliate->formatted_total_earned,
            'total_referrals' => $affiliate->total_referrals,
            'active_referrals' => $affiliate->active_referrals,
            'pending_commissions' => $this->getPendingCommissions(),
            'referral_link' => $affiliate->referral_link,
        ];
    }

    public function getPendingCommissions(): float
    {
        if (!$this->isAffiliate()) {
            return 0.0;
        }

        return \App\Models\Marketplace\ReferralTransaction::whereHas('referral', function ($q) {
            $q->where('affiliate_id', $this->affiliate->id);
        })
            ->where('status', \App\Models\Marketplace\ReferralTransaction::STATUS_PENDING)
            ->sum('commission_amount');
    }

    public function getCommissionTransactions(int $limit = 10)
    {
        if (!$this->isAffiliate()) {
            return collect();
        }

        return \App\Models\Marketplace\ReferralTransaction::whereHas('referral', function ($q) {
            $q->where('affiliate_id', $this->affiliate->id);
        })
            ->with(['course', 'referral.referredUser'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return (object) [
                    'description' => "Commission from {$transaction->course->title}",
                    'formatted_amount' => '₦' . number_format($transaction->commission_amount, 2),
                    'created_at' => $transaction->created_at,
                ];
            });
    }

    public function getReferredUsersActivity(int $limit = 5)
    {
        if (!$this->isAffiliate()) {
            return collect();
        }

        return \App\Models\Marketplace\Referral::where('affiliate_id', $this->affiliate->id)
            ->with('referredUser')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($referral) {
                return (object) [
                    'referredUser' => $referral->referredUser,
                    'status' => $referral->status,
                    'formatted_total_spent' => $referral->formatted_total_spent,
                    'formatted_commission_earned' => $referral->formatted_commission_earned,
                ];
            });
    }

    // ============================================
    // GENERAL ACCOUNT HELPERS
    // ============================================

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

    public function getFullAddressAttribute()
    {
        $parts = [
            $this->address_street,
            $this->address_city,
            $this->address_state,
            $this->address_country,
            $this->address_postal_code
        ];
        return implode(', ', array_filter($parts));
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth?->age;
    }

    public function shouldReceiveEmailNotification(string $notificationType): bool
    {
        $preferences = $this->notification_preferences ?? [];

        return match ($notificationType) {
            'course_update' => $preferences['email_course_updates'] ?? $this->receive_course_updates ?? true,
            'certificate_update' => $preferences['email_certificate_notifications'] ?? $this->receive_certificate_notifications ?? true,
            'instructor_reply' => $preferences['email_instructor_replies'] ?? true,
            'course_review' => true,
            'support_ticket' => true,
            'feedback_response' => true,
            'announcement' => true,
            'system_status' => true,
            default => true,
        };
    }

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

    public function activateAccount()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivateAccount()
    {
        $this->update(['is_active' => false]);
    }

    public function isCourseFavorited($courseId)
    {
        $favorites = $this->favorite_courses ?? [];

        if (is_string($favorites)) {
            $favorites = json_decode($favorites, true) ?? [];
        }

        return in_array((int) $courseId, $favorites);
    }

    public function addFavoriteCourse($courseId)
    {
        $favorites = $this->favorite_courses ?? [];

        if (is_string($favorites)) {
            $favorites = json_decode($favorites, true) ?? [];
        }

        $courseId = (int) $courseId;
        if (!in_array($courseId, $favorites)) {
            $favorites[] = $courseId;
            $this->favorite_courses = $favorites;
            $this->save();
        }
    }

    public function removeFavoriteCourse($courseId)
    {
        $favorites = $this->favorite_courses ?? [];

        if (is_string($favorites)) {
            $favorites = json_decode($favorites, true) ?? [];
        }

        $courseId = (int) $courseId;
        $favorites = array_filter($favorites, function ($id) use ($courseId) {
            return (int) $id !== $courseId;
        });

        $this->favorite_courses = array_values($favorites);
        $this->save();
    }

    // ============================================
// ACTIVITY LOGGING
// ============================================

    public function logCustomActivity(string $description, array $properties = [], string $event = 'custom')
    {
        return \App\Models\System\ActivityLog::create([
            'log_name' => 'user',
            'event' => $event,
            'description' => $description,
            'subject_id' => $this->id,
            'subject_type' => static::class,
            'causer_id' => $this->id,
            'causer_type' => static::class,
            'properties' => $properties,
        ]);
    }

    public function activities()
    {
        return $this->hasMany(\App\Models\System\ActivityLog::class, 'causer_id')
            ->where('causer_type', static::class);
    }

    public function getRecentActivities($limit = 10)
    {
        return $this->activities()
            ->latest()
            ->limit($limit)
            ->get();
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAllExcept($query, User $user)
    {
        return $query->where('id', '!=', $user->id);
    }

    public static function getActiveCount()
    {
        return static::where('is_active', true)->count();
    }

    public static function getInactiveCount()
    {
        return static::where('is_active', false)->count();
    }

    public static function getRecentlyDeactivated($days = 7)
    {
        return static::where('is_active', false)
            ->where('deactivated_at', '>=', now()->subDays($days))
            ->get();
    }

    public function canBeDeleted(): bool
    {
        return !($this->hasRole(self::ROLE_SUPER_ADMIN) && $this->id === 1);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail());
    }
}