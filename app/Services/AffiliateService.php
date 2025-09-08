<?php 

// app/Services/AffiliateService.php
namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Affiliate;
use App\Models\Referral;
use App\Models\ReferralTransaction;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliateService
{
    private WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Register a new user with referral tracking
     */
    public function registerWithReferral(array $userData, string $referralCode = null): User
    {
        DB::beginTransaction();
        
        try {
            // Create the user first
            $user = User::create($userData);

            // Handle referral if code provided
            if ($referralCode) {
                $affiliate = Affiliate::where('referral_code', $referralCode)
                    ->where('status', Affiliate::STATUS_ACTIVE)
                    ->first();

                if ($affiliate) {
                    // Update user with referral info
                    $user->update([
                        'referred_by' => $affiliate->user_id,
                        'referral_source' => 'affiliate'
                    ]);

                    // Create referral record
                    Referral::create([
                        'affiliate_id' => $affiliate->id,
                        'referred_user_id' => $user->id,
                        'status' => Referral::STATUS_PENDING,
                        'metadata' => [
                            'referral_code' => $referralCode,
                            'registered_at' => now(),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent()
                        ]
                    ]);

                    // Update affiliate stats
                    $affiliate->increment('total_referrals');
                }
            }

            DB::commit();
            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Affiliate registration error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process affiliate commission after course purchase
     */
    public function processCommission(User $buyer, Course $course, float $coursePrice, WalletTransaction $purchaseTransaction): array
    {
        if (!$buyer->wasReferred()) {
            return [
                'success' => true,
                'message' => 'No affiliate commission - user not referred',
                'commission_paid' => 0
            ];
        }

        DB::beginTransaction();
        
        try {
            $referral = $buyer->referralRecord;
            
            if (!$referral || !$referral->affiliate->isActive()) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Invalid or inactive affiliate'
                ];
            }

            $affiliate = $referral->affiliate;
            
            // Calculate commission
            $revenueSplit = $course->getOrCreateRevenueSplit();
            $platformShare = ($coursePrice * $revenueSplit->platform_percentage) / 100; // 20% of course price
            $commissionRate = $affiliate->commission_rate; // 30% by default
            $commissionAmount = ($platformShare * $commissionRate) / 100; // 30% of platform's 20%

            // Create referral transaction record
            $referralTransaction = ReferralTransaction::create([
                'referral_id' => $referral->id,
                'course_id' => $course->id,
                'wallet_transaction_id' => $purchaseTransaction->id,
                'course_price' => $coursePrice,
                'platform_share' => $platformShare,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'status' => ReferralTransaction::STATUS_PENDING,
                'metadata' => [
                    'processed_at' => now(),
                    'buyer_id' => $buyer->id,
                    'course_title' => $course->title
                ]
            ]);

            // Credit affiliate wallet
            $affiliateWallet = $affiliate->user->getOrCreateWallet();
            $affiliateWallet->credit(
                $commissionAmount,
                'referral_commission',
                "Referral commission: {$buyer->name} purchased {$course->title}",
                $referralTransaction,
                [
                    'referral_id' => $referral->id,
                    'course_id' => $course->id,
                    'buyer_id' => $buyer->id,
                    'commission_rate' => $commissionRate
                ]
            );

            // Mark commission as paid
            $referralTransaction->markAsPaid();

            // Update referral statistics
            $referral->recordPurchase($coursePrice, $commissionAmount);

            // Update affiliate total earnings
            $affiliate->increment('total_earned', $commissionAmount);

            DB::commit();

            Log::info('Affiliate commission processed', [
                'affiliate_id' => $affiliate->id,
                'buyer_id' => $buyer->id,
                'course_id' => $course->id,
                'commission_amount' => $commissionAmount
            ]);

            return [
                'success' => true,
                'message' => 'Commission processed successfully',
                'commission_paid' => $commissionAmount,
                'affiliate_id' => $affiliate->id,
                'referral_id' => $referral->id
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Commission processing error: ' . $e->getMessage(), [
                'buyer_id' => $buyer->id,
                'course_id' => $course->id
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to process commission: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create new affiliate account
     */
    public function createAffiliate(User $user, float $commissionRate = 30.00): Affiliate
    {
        if ($user->affiliate) {
            return $user->affiliate;
        }

        return Affiliate::create([
            'user_id' => $user->id,
            'commission_rate' => $commissionRate,
            'status' => Affiliate::STATUS_ACTIVE,
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
    }

    /**
     * Get affiliate analytics
     */
    public function getAffiliateAnalytics(Affiliate $affiliate, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        // Commission trends
        $dailyCommissions = ReferralTransaction::where('status', ReferralTransaction::STATUS_PAID)
            ->whereHas('referral', function($query) use ($affiliate) {
                $query->where('affiliate_id', $affiliate->id);
            })
            ->where('paid_at', '>=', $startDate)
            ->selectRaw('DATE(paid_at) as date, SUM(commission_amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top performing courses
        $topCourses = ReferralTransaction::where('status', ReferralTransaction::STATUS_PAID)
            ->whereHas('referral', function($query) use ($affiliate) {
                $query->where('affiliate_id', $affiliate->id);
            })
            ->with('course')
            ->selectRaw('course_id, SUM(commission_amount) as total_commission, COUNT(*) as sales_count')
            ->groupBy('course_id')
            ->orderBy('total_commission', 'desc')
            ->limit(5)
            ->get();

        // Recent activity
        $recentActivity = $affiliate->referrals()
            ->with(['referredUser', 'transactions' => function($q) {
                $q->latest()->limit(3);
            }])
            ->orderBy('last_purchase_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'daily_commissions' => $dailyCommissions,
            'top_courses' => $topCourses,
            'recent_activity' => $recentActivity,
            'period_stats' => [
                'total_commission' => $dailyCommissions->sum('total'),
                'total_sales' => $dailyCommissions->sum('count'),
                'average_daily' => $dailyCommissions->avg('total'),
                'best_day' => $dailyCommissions->sortByDesc('total')->first()
            ]
        ];
    }

    /**
     * Get system-wide affiliate statistics (for admin)
     */
    public function getSystemStats(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        return [
            'total_affiliates' => Affiliate::active()->count(),
            'total_referrals' => Referral::count(),
            'active_referrals' => Referral::active()->count(),
            'total_commissions_paid' => ReferralTransaction::paid()
                ->where('paid_at', '>=', $startDate)
                ->sum('commission_amount'),
            'pending_commissions' => ReferralTransaction::pending()->sum('commission_amount'),
            'top_affiliates' => Affiliate::active()
                ->topPerformers(5)
                ->with('user')
                ->get(),
            'recent_transactions' => ReferralTransaction::paid()
                ->with(['referral.affiliate.user', 'course'])
                ->latest('paid_at')
                ->limit(10)
                ->get()
        ];
    }

    /**
     * Validate referral code
     */
    public function validateReferralCode(string $code): array
    {
        $affiliate = Affiliate::where('referral_code', $code)
            ->where('status', Affiliate::STATUS_ACTIVE)
            ->with('user')
            ->first();

        if (!$affiliate) {
            return [
                'valid' => false,
                'message' => 'Invalid or inactive referral code'
            ];
        }

        return [
            'valid' => true,
            'affiliate' => $affiliate,
            'referrer_name' => $affiliate->user->name,
            'commission_rate' => $affiliate->commission_rate
        ];
    }

    /**
     * Generate marketing assets for affiliate
     */
    public function generateMarketingAssets(Affiliate $affiliate): array
    {
        $baseUrl = config('app.url');
        $referralLink = $affiliate->referral_link;
        
        return [
            'referral_link' => $referralLink,
            'short_link' => $this->createShortLink($referralLink),
            'qr_code_url' => "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($referralLink),
            'social_media' => [
                'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($referralLink),
                'twitter' => "https://twitter.com/intent/tweet?url=" . urlencode($referralLink) . "&text=" . urlencode("Join BootKode and learn to code!"),
                'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($referralLink),
                'whatsapp' => "https://wa.me/?text=" . urlencode("Check out BootKode: " . $referralLink)
            ],
            'email_template' => $this->generateEmailTemplate($affiliate),
            'banner_images' => [
                'large' => $baseUrl . '/affiliate-banners/large/' . $affiliate->referral_code,
                'medium' => $baseUrl . '/affiliate-banners/medium/' . $affiliate->referral_code,
                'small' => $baseUrl . '/affiliate-banners/small/' . $affiliate->referral_code
            ]
        ];
    }

    /**
     * Create shortened referral link
     */
    private function createShortLink(string $url): string
    {
        // In production, integrate with bit.ly or similar service
        // For now, return the original URL
        return $url;
    }

    /**
     * Generate email marketing template
     */
    private function generateEmailTemplate(Affiliate $affiliate): string
    {
        return "Hi there!\n\n" .
               "I wanted to share something amazing with you - BootKode!\n\n" .
               "It's a comprehensive coding education platform where you can learn programming from scratch or advance your existing skills.\n\n" .
               "What I love about BootKode:\n" .
               "✓ Expert instructors and industry professionals\n" .
               "✓ Practical, hands-on courses\n" .
               "✓ Flexible learning at your own pace\n" .
               "✓ Real-world projects and certifications\n\n" .
               "Use my referral link to get started: {$affiliate->referral_link}\n\n" .
               "Trust me, if you're looking to level up your coding skills, this is the place to be!\n\n" .
               "Best regards,\n" .
               "{$affiliate->user->name}";
    }
}