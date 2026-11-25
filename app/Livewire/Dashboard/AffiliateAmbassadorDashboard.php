<?php

namespace App\Livewire\Dashboard;

use App\Models\Core\User;
use App\Models\Marketplace\Affiliate;
use App\Models\Marketplace\ReferralTransaction;
use App\Models\Learning\Course;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
        if (!$user->canAccessDashboardRole(User::ROLE_AFFILIATE_AMBASSADOR)) {
            return $this->redirectRoute($user->getDashboardRouteName(), navigate: true);
        }

    }

    public function hydrate(): void
    {
        abort_unless(Auth::user()?->canAccessDashboardRole(User::ROLE_AFFILIATE_AMBASSADOR), 403);
    }

    public function updateTimeframe($timeframe)
    {
        validator(['timeframe' => $timeframe], [
            'timeframe' => ['required', Rule::in(['7days', '30days', '90days', '12months'])],
        ])->validate();

        $this->selectedTimeframe = $timeframe;
    }

    public function updateMetric($metric)
    {
        validator(['metric' => $metric], [
            'metric' => ['required', Rule::in(['earnings', 'referrals', 'conversions'])],
        ])->validate();

        $this->selectedMetric = $metric;
    }

    public function updatedSelectedMetric($metric)
    {
        $this->updateMetric($metric);
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
            'conversion_rate' => null,
            'avg_referral_value' => $this->getAverageReferralValue($affiliate),
            'clicks_this_period' => $this->getClicksThisPeriod($affiliate, $timeframe),
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
        
        $transactions = $affiliate->transactions()
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
            'social_media_posts' => $this->getSocialMediaTemplates($affiliate),
        ];
    }

    #[Computed]
    public function recentTransactions()
    {
        $affiliate = Auth::user()->affiliate;
        if (!$affiliate) return collect();
        
        return $affiliate->transactions()
            ->with(['referral.referredUser'])
            ->latest()
            ->take(15)
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'user_name' => $transaction->referral->referredUser->name ?? 'Unknown',
                    'amount' => $transaction->course_price,
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
        $stats = array_fill_keys([
            'total_referrals', 'active_referrals', 'pending_referrals',
            'total_earnings', 'pending_earnings', 'monthly_earnings',
            'commission_rate', 'conversion_rate', 'avg_referral_value',
            'clicks_this_period'
        ], 0);

        $stats['conversion_rate'] = null;
        $stats['clicks_this_period'] = null;

        return $stats;
    }

    private function getPendingEarnings(Affiliate $affiliate)
    {
        return $affiliate->transactions()
            ->where('status', 'pending')
            ->sum('commission_amount');
    }

    private function getMonthlyEarnings(Affiliate $affiliate)
    {
        return $affiliate->transactions()
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->sum('commission_amount');
    }

    private function getAverageReferralValue(Affiliate $affiliate)
    {
        $totalSpent = $affiliate->referrals()->sum('total_spent');
        $totalReferrals = $affiliate->total_referrals;
        
        return $totalReferrals > 0 ? round($totalSpent / $totalReferrals, 2) : 0;
    }

    private function getClicksThisPeriod(Affiliate $affiliate, $timeframe)
    {
        return null;
    }

    private function getDailyEarnings(Affiliate $affiliate, $date)
    {
        return $affiliate->transactions()
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
        return $affiliate->transactions()
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
        return $affiliate->transactions()
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

    private function getSocialMediaTemplates(Affiliate $affiliate)
    {
        return [
            'twitter' => "🚀 Level up your coding skills with @BootKodeAcademy! Join me: {$affiliate->referral_link} #CodingJourney #TechEducation",
            'facebook' => "Explore practical technology learning with BootKode Academy: {$affiliate->referral_link}",
            'linkedin' => "Explore BootKode Academy's practical technology courses: {$affiliate->referral_link}",
        ];
    }

    // Mock implementations for remaining methods
    private function getEarningsForecast($affiliate) { return []; }
    private function getMonthlyCommissionBreakdown($affiliate) { return []; }
    private function getPaymentMethodBreakdown($affiliate) { return []; }

    public function copyToClipboard($text)
    {
        $affiliate = Auth::user()->affiliate;
        abort_unless($affiliate, 404);

        $allowedText = collect([
            $affiliate->referral_link,
            $affiliate->referral_code,
            ...array_values($this->getSocialMediaTemplates($affiliate)),
        ]);
        abort_unless($allowedText->contains(fn ($allowed) => hash_equals((string) $allowed, (string) $text)), 422);

        $this->dispatch('copy-to-clipboard', text: $text);
        $this->dispatch('notify', type: 'success', message: 'Copied to clipboard!');
    }

    public function loadAllData()
    {
        $this->dispatch('dashboard-updated');
    }

    public function render()
    {
        return view('livewire.dashboard.affiliate-ambassador-dashboard');
    }
}
