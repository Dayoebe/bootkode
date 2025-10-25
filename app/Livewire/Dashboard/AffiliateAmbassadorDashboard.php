<?php

namespace App\Livewire\Dashboard;

use App\Models\Core\User;
use App\Models\Marketplace\Affiliate;
use App\Models\Marketplace\Referral;
use App\Models\Marketplace\ReferralTransaction;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate Dashboard'])]
class AffiliateAmbassadorDashboard extends Component
{
    public $selectedTimeframe = '30days';
    public $selectedMetric = 'earnings';
    
    public $showWidgets = [
        'overview_stats' => true,
        'earnings_analytics' => true,
        'referral_performance' => true,
        'top_referrals' => true,
        'commission_breakdown' => true,
        'marketing_tools' => true,
        'recent_transactions' => true,
        'leaderboard' => true,
    ];

    protected $listeners = [
        'refreshDashboard' => 'loadAllData',
        'timeframeChanged' => 'updateTimeframe',
        'metricChanged' => 'updateMetric',
    ];

    public function mount()
    {
        $user = Auth::user();
        if (!$user->isAffiliateAmbassador()) {
            redirect()->route($user->getDashboardRouteName());
        }

        // Ensure user has affiliate record
        if (!$user->affiliate) {
            $user->applyForAffiliate();
        }
    }

    public function updateTimeframe($timeframe)
    {
        $this->selectedTimeframe = $timeframe;
    }

    public function updateMetric($metric)
    {
        $this->selectedMetric = $metric;
    }

    #[Computed]
    public function overviewStats()
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        $timeframe = $this->getTimeframeQuery();
        
        if (!$affiliate) {
            return $this->getEmptyStats();
        }
        
        return [
            'total_referrals' => $affiliate->total_referrals,
            'active_referrals' => $affiliate->active_referrals,
            'pending_referrals' => $affiliate->referrals()->pending()->count(),
            'total_earnings' => $affiliate->total_earned,
            'pending_earnings' => $this->getPendingEarnings($affiliate),
            'monthly_earnings' => $this->getMonthlyEarnings($affiliate),
            'commission_rate' => $affiliate->commission_rate,
            'conversion_rate' => $this->getConversionRate($affiliate),
            'avg_referral_value' => $this->getAverageReferralValue($affiliate),
            'clicks_this_period' => $this->getClicksThisPeriod($affiliate, $timeframe),
            'tier_status' => $this->getTierStatus($affiliate),
            'next_tier_requirement' => $this->getNextTierRequirement($affiliate),
        ];
    }

    #[Computed]
    public function earningsAnalytics()
    {
        $affiliate = Auth::user()->affiliate;
        if (!$affiliate) return [];
        
        $days = $this->getTimeframeDays();
        
        // Daily earnings for the selected timeframe
        $dailyEarnings = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $earnings = $this->getDailyEarnings($affiliate, $date);
            
            $dailyEarnings[] = [
                'date' => $date->format('M j'),
                'earnings' => $earnings,
                'referrals' => $this->getDailyReferrals($affiliate, $date),
            ];
        }
        
        // Monthly breakdown
        $monthlyBreakdown = $this->getMonthlyBreakdown($affiliate);
        
        return [
            'daily_earnings' => $dailyEarnings,
            'monthly_breakdown' => $monthlyBreakdown,
            'top_earning_courses' => $this->getTopEarningCourses($affiliate),
            'earnings_forecast' => $this->getEarningsForecast($affiliate),
        ];
    }

    #[Computed]
    public function referralPerformance()
    {
        $affiliate = Auth::user()->affiliate;
        if (!$affiliate) return collect();
        
        return $affiliate->referrals()
            ->with(['referredUser', 'transactions'])
            ->latest()
            ->take(20)
            ->get()
            ->map(function($referral) {
                return [
                    'id' => $referral->id,
                    'user_name' => $referral->referredUser->name,
                    'user_email' => $referral->referredUser->email,
                    'joined_date' => $referral->created_at,
                    'status' => $referral->status,
                    'total_spent' => $referral->total_spent,
                    'commission_earned' => $referral->total_commission_earned,
                    'courses_purchased' => $referral->courses_purchased,
                    'last_purchase' => $referral->last_purchase_at,
                    'lifetime_value' => $this->calculateLifetimeValue($referral),
                    'activity_score' => $this->calculateActivityScore($referral),
                ];
            });
    }

    #[Computed]
    public function topReferrals()
    {
        $affiliate = Auth::user()->affiliate;
        if (!$affiliate) return collect();
        
        return $affiliate->referrals()
            ->with('referredUser')
            ->where('status', 'active')
            ->orderByDesc('total_commission_earned')
            ->take(10)
            ->get()
            ->map(function($referral) {
                return [
                    'user_name' => $referral->referredUser->name,
                    'commission_earned' => $referral->total_commission_earned,
                    'courses_purchased' => $referral->courses_purchased,
                    'total_spent' => $referral->total_spent,
                    'join_date' => $referral->created_at,
                ];
            });
    }

    #[Computed]
    public function commissionBreakdown()
    {
        $affiliate = Auth::user()->affiliate;
        if (!$affiliate) return [];
        
        $transactions = ReferralTransaction::where('referral_id', $affiliate->id)
            ->where('status', 'paid')
            ->with(['referral.referredUser'])
            ->get();
        
        // Group by course categories or types
        $courseBreakdown = $transactions->groupBy(function($transaction) {
            return $transaction->course_id ? 
                Course::find($transaction->course_id)?->category?->name ?? 'General' : 
                'Direct Referral';
        })->map(function($group, $category) {
            return [
                'category' => $category,
                'total_commission' => $group->sum('commission_amount'),
                'transaction_count' => $group->count(),
                'avg_commission' => $group->avg('commission_amount'),
            ];
        })->sortByDesc('total_commission');
        
        return [
            'by_category' => $courseBreakdown,
            'by_month' => $this->getMonthlyCommissionBreakdown($affiliate),
            'payment_methods' => $this->getPaymentMethodBreakdown($affiliate),
        ];
    }

    #[Computed]
    public function marketingTools()
    {
        $affiliate = Auth::user()->affiliate;
        if (!$affiliate) return [];
        
        return [
            'referral_link' => $affiliate->referral_link,
            'referral_code' => $affiliate->referral_code,
            'qr_code_url' => $this->generateQRCode($affiliate->referral_link),
            'banner_links' => $this->getBannerLinks($affiliate),
            'email_templates' => $this->getEmailTemplates($affiliate),
            'social_media_posts' => $this->getSocialMediaTemplates($affiliate),
            'tracking_pixels' => $this->getTrackingPixels($affiliate),
        ];
    }

    #[Computed]
    public function recentTransactions()
    {
        $affiliate = Auth::user()->affiliate;
        if (!$affiliate) return collect();
        
        return ReferralTransaction::where('referral_id', $affiliate->id)
            ->with(['referral.referredUser'])
            ->latest()
            ->take(15)
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'user_name' => $transaction->referral->referredUser->name ?? 'Unknown',
                    'amount' => $transaction->purchase_amount,
                    'commission' => $transaction->commission_amount,
                    'status' => $transaction->status,
                    'course_title' => $transaction->course_id ? 
                        Course::find($transaction->course_id)?->title : 'Direct Referral',
                    'created_at' => $transaction->created_at,
                    'paid_at' => $transaction->paid_at,
                ];
            });
    }

    #[Computed]
    public function leaderboard()
    {
        return Affiliate::active()
            ->topPerformers(10)
            ->with('user')
            ->get()
            ->map(function($affiliate, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $affiliate->user->name,
                    'total_earned' => $affiliate->total_earned,
                    'total_referrals' => $affiliate->total_referrals,
                    'is_current_user' => $affiliate->user_id === auth()->id(),
                ];
            });
    }

    // Helper Methods
    private function getTimeframeQuery()
    {
        return match ($this->selectedTimeframe) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            '12months' => now()->subMonths(12),
            default => now()->subDays(30),
        };
    }

    private function getTimeframeDays()
    {
        return match ($this->selectedTimeframe) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            '12months' => 365,
            default => 30,
        };
    }

    private function getEmptyStats()
    {
        return array_fill_keys([
            'total_referrals', 'active_referrals', 'pending_referrals',
            'total_earnings', 'pending_earnings', 'monthly_earnings',
            'commission_rate', 'conversion_rate', 'avg_referral_value',
            'clicks_this_period', 'tier_status', 'next_tier_requirement'
        ], 0);
    }

    private function getPendingEarnings(Affiliate $affiliate)
    {
        return ReferralTransaction::where('referral_id', $affiliate->id)
            ->where('status', 'pending')
            ->sum('commission_amount');
    }

    private function getMonthlyEarnings(Affiliate $affiliate)
    {
        return ReferralTransaction::where('referral_id', $affiliate->id)
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->sum('commission_amount');
    }

    private function getConversionRate(Affiliate $affiliate)
    {
        $clicks = $this->getTotalClicks($affiliate);
        $conversions = $affiliate->total_referrals;
        
        return $clicks > 0 ? round(($conversions / $clicks) * 100, 2) : 0;
    }

    private function getAverageReferralValue(Affiliate $affiliate)
    {
        $totalSpent = $affiliate->referrals()->sum('total_spent');
        $totalReferrals = $affiliate->total_referrals;
        
        return $totalReferrals > 0 ? round($totalSpent / $totalReferrals, 2) : 0;
    }

    private function getClicksThisPeriod(Affiliate $affiliate, $timeframe)
    {
        // This would come from a clicks tracking table
        // For now, estimate based on referrals
        return $affiliate->referrals()->where('created_at', '>=', $timeframe)->count() * 10;
    }

    private function getTierStatus(Affiliate $affiliate)
    {
        $earnings = $affiliate->total_earned;
        
        if ($earnings >= 100000) return 'Diamond';
        if ($earnings >= 50000) return 'Platinum';
        if ($earnings >= 25000) return 'Gold';
        if ($earnings >= 10000) return 'Silver';
        return 'Bronze';
    }

    private function getNextTierRequirement(Affiliate $affiliate)
    {
        $earnings = $affiliate->total_earned;
        
        if ($earnings >= 100000) return 0; // Already at top tier
        if ($earnings >= 50000) return 100000 - $earnings;
        if ($earnings >= 25000) return 50000 - $earnings;
        if ($earnings >= 10000) return 25000 - $earnings;
        return 10000 - $earnings;
    }

    private function getDailyEarnings(Affiliate $affiliate, $date)
    {
        return ReferralTransaction::where('referral_id', $affiliate->id)
            ->where('status', 'paid')
            ->whereDate('paid_at', $date)
            ->sum('commission_amount');
    }

    private function getDailyReferrals(Affiliate $affiliate, $date)
    {
        return $affiliate->referrals()
            ->whereDate('created_at', $date)
            ->count();
    }

    private function getMonthlyBreakdown(Affiliate $affiliate)
    {
        return ReferralTransaction::where('referral_id', $affiliate->id)
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(12))
            ->selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, SUM(commission_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'period' => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                    'earnings' => $item->total,
                ];
            });
    }

    private function getTopEarningCourses(Affiliate $affiliate)
    {
        return ReferralTransaction::where('referral_id', $affiliate->id)
            ->where('status', 'paid')
            ->whereNotNull('course_id')
            ->selectRaw('course_id, SUM(commission_amount) as total_commission, COUNT(*) as sales')
            ->groupBy('course_id')
            ->orderByDesc('total_commission')
            ->take(5)
            ->get()
            ->map(function($item) {
                $course = Course::find($item->course_id);
                return [
                    'course_title' => $course?->title ?? 'Unknown Course',
                    'total_commission' => $item->total_commission,
                    'sales_count' => $item->sales,
                ];
            });
    }

    private function calculateLifetimeValue(Referral $referral)
    {
        // Calculate projected lifetime value based on current spending patterns
        $monthsSinceJoined = $referral->created_at->diffInMonths(now());
        if ($monthsSinceJoined == 0) return $referral->total_spent;
        
        $monthlySpending = $referral->total_spent / $monthsSinceJoined;
        return $monthlySpending * 24; // 2-year projection
    }

    private function calculateActivityScore(Referral $referral)
    {
        $score = 0;
        
        // Recent activity (last 30 days)
        if ($referral->last_purchase_at && $referral->last_purchase_at->gt(now()->subDays(30))) {
            $score += 40;
        }
        
        // Purchase frequency
        if ($referral->courses_purchased >= 5) $score += 30;
        elseif ($referral->courses_purchased >= 2) $score += 20;
        elseif ($referral->courses_purchased >= 1) $score += 10;
        
        // Spending amount
        if ($referral->total_spent >= 50000) $score += 30;
        elseif ($referral->total_spent >= 20000) $score += 20;
        elseif ($referral->total_spent >= 5000) $score += 10;
        
        return min($score, 100);
    }

    // Additional helper methods for marketing tools
    private function generateQRCode($url)
    {
        // This would integrate with a QR code service
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($url);
    }

    private function getBannerLinks(Affiliate $affiliate)
    {
        return [
            '728x90' => asset('images/banners/leaderboard.jpg') . '?ref=' . $affiliate->referral_code,
            '300x250' => asset('images/banners/medium-rectangle.jpg') . '?ref=' . $affiliate->referral_code,
            '160x600' => asset('images/banners/skyscraper.jpg') . '?ref=' . $affiliate->referral_code,
        ];
    }

    private function getEmailTemplates(Affiliate $affiliate)
    {
        return [
            'welcome' => "Join BootKode Academy and start your coding journey today! Use my referral link: {$affiliate->referral_link}",
            'course_recommendation' => "I've been learning amazing skills at BootKode Academy. Check it out: {$affiliate->referral_link}",
            'success_story' => "BootKode Academy transformed my career! Join me: {$affiliate->referral_link}",
        ];
    }

    private function getSocialMediaTemplates(Affiliate $affiliate)
    {
        return [
            'twitter' => "🚀 Level up your coding skills with @BootKodeAcademy! Join me: {$affiliate->referral_link} #CodingJourney #TechEducation",
            'facebook' => "I've been learning so much at BootKode Academy! If you're interested in coding, check it out: {$affiliate->referral_link}",
            'linkedin' => "Expanding my technical skills with BootKode Academy. Excellent courses for career growth: {$affiliate->referral_link}",
        ];
    }

    private function getTotalClicks(Affiliate $affiliate)
    {
        // This would come from analytics tracking
        return $affiliate->total_referrals * 15; // Estimated
    }

    private function getTrackingPixels(Affiliate $affiliate)
    {
        return [
            'facebook' => "<script>/* Facebook Pixel Code for {$affiliate->referral_code} */</script>",
            'google' => "<script>/* Google Analytics Code for {$affiliate->referral_code} */</script>",
        ];
    }

    // Mock implementations for remaining methods
    private function getEarningsForecast($affiliate) { return []; }
    private function getMonthlyCommissionBreakdown($affiliate) { return []; }
    private function getPaymentMethodBreakdown($affiliate) { return []; }

    public function copyToClipboard($text)
    {
        $this->dispatch('copy-to-clipboard', text: $text);
        $this->dispatch('notify', type: 'success', message: 'Copied to clipboard!');
    }

    public function render()
    {
        return view('livewire.dashboard.affiliate-ambassador-dashboard');
    }
}