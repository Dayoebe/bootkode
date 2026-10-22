<?php 

// app/Services/AffiliateService.php
namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Marketplace\Affiliate;
use App\Models\Marketplace\Referral;
use App\Models\Marketplace\ReferralTransaction;
use App\Models\Marketplace\WalletTransaction;
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
    public function registerWithReferral(array $userData, ?string $referralCode = null): User
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

        try {
            return DB::transaction(function () use ($buyer, $course, $coursePrice, $purchaseTransaction) {
                $debit = WalletTransaction::with('wallet')->lockForUpdate()->findOrFail($purchaseTransaction->id);
                if ($debit->type !== 'debit' || $debit->status !== 'completed' || $debit->category !== WalletTransaction::CATEGORY_COURSE_PURCHASE
                    || $debit->wallet->currency !== 'NGN' || (int) $debit->wallet->user_id !== (int) $buyer->id
                    || $debit->transactionable_type !== $course->getMorphClass() || (int) $debit->transactionable_id !== (int) $course->id
                    || (int) round((float) $debit->amount * 100) !== (int) round($coursePrice * 100)) {
                    throw new \DomainException('A matching completed course debit is required.');
                }
                $existing = ReferralTransaction::where('wallet_transaction_id', $debit->id)->first();
                if ($existing) {
                    return ['success' => $existing->isPaid(), 'commission_paid' => (float) $existing->commission_amount, 'message' => 'Commission already recorded.'];
                }
                $referral = Referral::where('referred_user_id', $buyer->id)->whereHas('affiliate', fn ($query) => $query->where('user_id', $buyer->referred_by))->lockForUpdate()->first();
                if (!$referral) throw new \DomainException('No matching referral exists.');
                $affiliate = Affiliate::lockForUpdate()->findOrFail($referral->affiliate_id);
                if (!$affiliate->isActive() || (int) $affiliate->user_id === (int) $buyer->id) throw new \DomainException('The referral is not eligible.');
                $split = $debit->metadata['revenue_split'] ?? [];
                if (!isset($split['platform_amount'], $split['instructor_amount'])) throw new \DomainException('Historical debit has no verified split snapshot.');
                $platformMinor = (int) round((float) $split['platform_amount'] * 100);
                $instructorMinor = (int) round((float) $split['instructor_amount'] * 100);
                if ($platformMinor < 0 || $instructorMinor < 0 || $platformMinor + $instructorMinor !== (int) round((float) $debit->amount * 100)
                    || (float) $affiliate->commission_rate < 0 || (float) $affiliate->commission_rate > 100) throw new \DomainException('Invalid commission allocation.');
                $amount = round($platformMinor * (float) $affiliate->commission_rate / 100) / 100;
                $transaction = ReferralTransaction::create([
                    'referral_id' => $referral->id, 'course_id' => $course->id, 'wallet_transaction_id' => $debit->id,
                    'course_price' => $debit->amount, 'platform_share' => $platformMinor / 100,
                    'commission_rate' => $affiliate->commission_rate, 'commission_amount' => $amount,
                    'status' => ReferralTransaction::STATUS_PENDING, 'metadata' => ['buyer_id' => $buyer->id, 'course_title' => $course->title],
                ]);
                if ($amount > 0) {
                    $wallet = $affiliate->user->getOrCreateWallet();
                    if ($wallet->currency !== $debit->wallet->currency) throw new \DomainException('Affiliate settlement currency does not match.');
                    $credit = $wallet->credit($amount, 'referral_commission', 'Referral commission: '.$course->title, $transaction, ['course_id' => $course->id, 'buyer_id' => $buyer->id]);
                    $transaction->update(['metadata' => array_merge($transaction->metadata ?? [], ['credit_transaction_id' => $credit->id])]);
                }
                $transaction->markAsPaid();
                $referral->recordPurchase((float) $debit->amount, $amount);
                $affiliate->increment('total_earned', $amount);
                return ['success' => true, 'commission_paid' => $amount, 'affiliate_id' => $affiliate->id, 'referral_id' => $referral->id, 'message' => 'Commission recorded.'];
            });
        } catch (\Throwable $exception) {
            Log::warning('Affiliate settlement refused', ['exception' => $exception::class, 'course_id' => $course->id]);
            return ['success' => false, 'commission_paid' => 0, 'message' => 'Commission could not be settled; administrator review is required.'];
        }
    }

    /**
     * Create new affiliate account
     */
    public function createAffiliate(User $user, float $commissionRate = 30.00): Affiliate
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'academy_admin']), 403);
        validator(['rate' => $commissionRate], ['rate' => 'numeric|between:0,100'])->validate();
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
        $days = max(1, min(366, $days));
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
        $days = max(1, min(366, $days));
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
            'banner_images' => collect(['large', 'medium', 'small'])->mapWithKeys(fn ($size) => [$size => route('affiliate.banner', ['size' => $size, 'code' => $affiliate->referral_code])])->all()
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
        return "Hi there!\n\nExplore BootKode's published courses and practical technology resources.\n\n"
            . "Learn. Build. Grow.\n\nMy referral link: {$affiliate->referral_link}\n\n{$affiliate->user->name}";
    }
}
