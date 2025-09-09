<?php
// NEW COMPONENT: PerformanceOptimization.php
namespace App\Livewire\Newsletter\Partials;

use Livewire\Component;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterInteraction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceOptimization extends Component
{
    public $activeSection = 'deliverability';
    public $dateRange = '30';
    public $selectedMetric = 'open_rate';

    public function setActiveSection($section)
    {
        $this->activeSection = $section;
    }


    // In PerformanceOptimization.php, update the getListHealthProperty method
public function getListHealthProperty()
{
    $totalSubscribers = NewsletterSubscriber::count();
    $activeSubscribers = NewsletterSubscriber::active()->count();

    // Engagement analysis - fixed queries
    $engagedSubscribers = NewsletterSubscriber::active()
        ->whereHas('interactions', function ($query) {
            $query->where('type', NewsletterInteraction::TYPE_OPEN)
                  ->where('created_at', '>=', now()->subDays(30));
        })
        ->count();

    $inactiveSubscribers = NewsletterSubscriber::active()
        ->whereDoesntHave('interactions', function ($query) {
            $query->where('type', NewsletterInteraction::TYPE_OPEN)
                  ->where('created_at', '>=', now()->subDays(90));
        })
        ->count();

    // Growth trends
    $growthData = collect(range(0, 11))->map(function ($monthsAgo) {
        $date = now()->subMonths($monthsAgo);
        return [
            'month' => $date->format('M Y'),
            'new_subscribers' => NewsletterSubscriber::whereBetween('created_at', [
                $date->startOfMonth()->copy(),
                $date->endOfMonth()->copy()
            ])->count(),
            'unsubscribes' => NewsletterSubscriber::where('status', NewsletterSubscriber::STATUS_UNSUBSCRIBED)
                ->whereBetween('unsubscribed_at', [
                    $date->startOfMonth()->copy(),
                    $date->endOfMonth()->copy()
                ])->count(),
        ];
    })->reverse()->values();

    // Subscriber segments
    $segments = [
        'highly_engaged' => NewsletterSubscriber::active()
            ->whereHas('interactions', function ($query) {
                $query->where('type', NewsletterInteraction::TYPE_OPEN)
                      ->where('created_at', '>=', now()->subDays(7));
            })
            ->count(),
        'moderately_engaged' => NewsletterSubscriber::active()
            ->whereHas('interactions', function ($query) {
                $query->where('type', NewsletterInteraction::TYPE_OPEN)
                      ->whereBetween('created_at', [now()->subDays(30), now()->subDays(7)]);
            })
            ->count(),
        'low_engaged' => NewsletterSubscriber::active()
            ->whereHas('interactions', function ($query) {
                $query->where('type', NewsletterInteraction::TYPE_OPEN)
                      ->whereBetween('created_at', [now()->subDays(90), now()->subDays(30)]);
            })
            ->count(),
        'at_risk' => $inactiveSubscribers,
    ];

    return [
        'total_subscribers' => $totalSubscribers,
        'active_subscribers' => $activeSubscribers,
        'engaged_rate' => $activeSubscribers > 0 ? round(($engagedSubscribers / $activeSubscribers) * 100, 2) : 0,
        'inactive_count' => $inactiveSubscribers,
        'growth_data' => $growthData,
        'segments' => $segments,
    ];
}

// Also update the exportInactiveSubscribers method
public function exportInactiveSubscribers()
{
    $inactive = NewsletterSubscriber::active()
        ->whereDoesntHave('interactions', function ($query) {
            $query->where('type', NewsletterInteraction::TYPE_OPEN)
                  ->where('created_at', '>=', now()->subDays(90));
        })
        ->get();

    $csv = "Email,First Name,Last Name,Last Active,Days Inactive\n";
    foreach ($inactive as $subscriber) {
        $lastInteraction = $subscriber->interactions()
            ->where('type', NewsletterInteraction::TYPE_OPEN)
            ->latest()
            ->first();
        
        $lastActive = $lastInteraction ? $lastInteraction->created_at->format('Y-m-d') : 'Never';
        $daysInactive = $lastInteraction ? $lastInteraction->created_at->diffInDays(now()) : 'N/A';
        
        $csv .= "{$subscriber->email},{$subscriber->first_name},{$subscriber->last_name},{$lastActive},{$daysInactive}\n";
    }

    return response()->streamDownload(function () use ($csv) {
        echo $csv;
    }, 'inactive_subscribers_' . now()->format('Y-m-d') . '.csv', [
        'Content-Type' => 'text/csv',
    ]);
}



















    public function getDeliverabilityStatsProperty()
    {
        $days = $this->dateRange === 'all' ? 365 : (int)$this->dateRange;
        $startDate = now()->subDays($days);

        $campaigns = NewsletterCampaign::campaigns()
            ->where('status', NewsletterCampaign::STATUS_SENT)
            ->where('sent_at', '>=', $startDate)
            ->get();

        $totalSent = $campaigns->sum('sent_count');
        $totalBounces = $campaigns->sum('bounce_count');
        $totalUnsubscribes = $campaigns->sum('unsubscribe_count');

        // Get bounce breakdown by reason
        $bounceReasons = NewsletterInteraction::where('type', NewsletterInteraction::TYPE_BOUNCE)
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.reason")) as reason'), DB::raw('COUNT(*) as count'))
            ->groupBy('reason')
            ->orderByDesc('count')
            ->get();

        // Calculate domain reputation (simplified)
        $domainStats = $campaigns->groupBy('from_email')->map(function ($domainCampaigns) {
            $sent = $domainCampaigns->sum('sent_count');
            $bounces = $domainCampaigns->sum('bounce_count');
            return [
                'sent' => $sent,
                'bounces' => $bounces,
                'bounce_rate' => $sent > 0 ? round(($bounces / $sent) * 100, 2) : 0,
            ];
        });

        return [
            'total_sent' => $totalSent,
            'bounce_rate' => $totalSent > 0 ? round(($totalBounces / $totalSent) * 100, 2) : 0,
            'unsubscribe_rate' => $totalSent > 0 ? round(($totalUnsubscribes / $totalSent) * 100, 2) : 0,
            'delivery_rate' => $totalSent > 0 ? round((($totalSent - $totalBounces) / $totalSent) * 100, 2) : 0,
            'bounce_reasons' => $bounceReasons,
            'domain_stats' => $domainStats,
        ];
    }

    public function getSendTimeAnalysisProperty()
    {
        $days = $this->dateRange === 'all' ? 90 : min((int)$this->dateRange, 90);
        $startDate = now()->subDays($days);

        // Get hourly performance data
        $hourlyStats = NewsletterCampaign::campaigns()
            ->where('status', NewsletterCampaign::STATUS_SENT)
            ->where('sent_at', '>=', $startDate)
            ->select(
                DB::raw('HOUR(sent_at) as hour'),
                DB::raw('AVG(CASE WHEN sent_count > 0 THEN (open_count / sent_count) * 100 ELSE 0 END) as avg_open_rate'),
                DB::raw('AVG(CASE WHEN sent_count > 0 THEN (click_count / sent_count) * 100 ELSE 0 END) as avg_click_rate'),
                DB::raw('COUNT(*) as campaign_count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Get daily performance data
        $dailyStats = NewsletterCampaign::campaigns()
            ->where('status', NewsletterCampaign::STATUS_SENT)
            ->where('sent_at', '>=', $startDate)
            ->select(
                DB::raw('DAYOFWEEK(sent_at) as day_of_week'),
                DB::raw('AVG(CASE WHEN sent_count > 0 THEN (open_count / sent_count) * 100 ELSE 0 END) as avg_open_rate'),
                DB::raw('AVG(CASE WHEN sent_count > 0 THEN (click_count / sent_count) * 100 ELSE 0 END) as avg_click_rate'),
                DB::raw('COUNT(*) as campaign_count')
            )
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        return [
            'hourly_stats' => $hourlyStats,
            'daily_stats' => $dailyStats,
            'best_hour' => $hourlyStats->sortByDesc('avg_' . $this->selectedMetric)->first(),
            'best_day' => $dailyStats->sortByDesc('avg_' . $this->selectedMetric)->first(),
        ];
    }


    public function getAbTestingProperty()
    {
        // Get A/B test opportunities based on campaign variations
        $subjectLineVariations = NewsletterCampaign::campaigns()
            ->where('status', NewsletterCampaign::STATUS_SENT)
            ->where('sent_at', '>=', now()->subDays(30))
            ->select('subject', 
                DB::raw('AVG(CASE WHEN sent_count > 0 THEN (open_count / sent_count) * 100 ELSE 0 END) as avg_open_rate'),
                DB::raw('COUNT(*) as usage_count')
            )
            ->groupBy('subject')
            ->having('usage_count', '>', 1)
            ->orderByDesc('avg_open_rate')
            ->limit(10)
            ->get();

        // Send time variations
        $sendTimeVariations = NewsletterCampaign::campaigns()
            ->where('status', NewsletterCampaign::STATUS_SENT)
            ->where('sent_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('HOUR(sent_at) as send_hour'),
                DB::raw('AVG(CASE WHEN sent_count > 0 THEN (open_count / sent_count) * 100 ELSE 0 END) as avg_open_rate'),
                DB::raw('AVG(CASE WHEN sent_count > 0 THEN (click_count / sent_count) * 100 ELSE 0 END) as avg_click_rate'),
                DB::raw('COUNT(*) as campaign_count')
            )
            ->groupBy('send_hour')
            ->having('campaign_count', '>=', 2)
            ->orderByDesc('avg_open_rate')
            ->get();

        return [
            'subject_variations' => $subjectLineVariations,
            'send_time_variations' => $sendTimeVariations,
            'suggested_tests' => $this->generateTestSuggestions(),
        ];
    }

    private function generateTestSuggestions()
    {
        $suggestions = [];

        // Analyze recent campaign performance
        $recentCampaigns = NewsletterCampaign::campaigns()
            ->where('status', NewsletterCampaign::STATUS_SENT)
            ->where('sent_at', '>=', now()->subDays(30))
            ->get();

        $avgOpenRate = $recentCampaigns->avg('open_rate');
        $avgClickRate = $recentCampaigns->avg('click_rate');

        if ($avgOpenRate < 20) {
            $suggestions[] = [
                'type' => 'Subject Line',
                'priority' => 'High',
                'recommendation' => 'Test personalized vs generic subject lines - current open rate is below industry average',
                'test_idea' => 'A: "Hello {{first_name}}, new updates inside" vs B: "Weekly Newsletter Update"'
            ];
        }

        if ($avgClickRate < 3) {
            $suggestions[] = [
                'type' => 'Call-to-Action',
                'priority' => 'High',
                'recommendation' => 'Test different CTA button colors and text - current click rate needs improvement',
                'test_idea' => 'A: Blue "Learn More" button vs B: Orange "Get Started" button'
            ];
        }

        $suggestions[] = [
            'type' => 'Send Time',
            'priority' => 'Medium',
            'recommendation' => 'Test sending at different times to find your audience\'s peak engagement hours',
            'test_idea' => 'A: Tuesday 10:00 AM vs B: Thursday 2:00 PM'
        ];

        $suggestions[] = [
            'type' => 'Content Length',
            'priority' => 'Medium',
            'recommendation' => 'Test short vs long-form content to see what your audience prefers',
            'test_idea' => 'A: Brief summary (200 words) vs B: Detailed content (800+ words)'
        ];

        return collect($suggestions)->sortByDesc(function ($suggestion) {
            return $suggestion['priority'] === 'High' ? 2 : 1;
        })->values();
    }


    public function render()
    {
        return view('livewire.newsletter.partials.performance-optimization', [
            'deliverabilityStats' => $this->deliverabilityStats,
            'sendTimeAnalysis' => $this->sendTimeAnalysis,
            'listHealth' => $this->listHealth,
            'abTesting' => $this->abTesting,
        ]);
    }
}