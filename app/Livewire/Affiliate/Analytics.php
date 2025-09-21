<?php

// app/Livewire/Affiliate/Analytics.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use App\Services\AffiliateService;
use App\Models\ReferralTransaction;
use App\Models\Referral;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate Analytics'])]
class Analytics extends Component
{
    public $selectedPeriod = '30';
    public $chartType = 'commission';

    private AffiliateService $affiliateService;

    public function boot(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function mount()
    {
        $user = auth()->user();
        
        if (!$user->isAffiliate()) {
            return redirect()->route('affiliate.dashboard');
        }
    }

    public function setPeriod($period)
    {
        $this->selectedPeriod = $period;
    }

    public function setChartType($type)
    {
        $this->chartType = $type;
    }

    public function render()
    {
        $user = auth()->user();
        $affiliate = $user->affiliate;
        $days = (int) $this->selectedPeriod;
        $analytics = $this->affiliateService->getAffiliateAnalytics($affiliate, $days);

        // Get conversion funnel data
        $conversionData = $this->getConversionFunnelData($affiliate->id, $days);
        
        // Get traffic source breakdown
        $trafficSources = $this->getTrafficSourceData($affiliate->id, $days);

        return view('livewire.affiliate.analytics', [
            'analytics' => $analytics,
            'conversionData' => $conversionData,
            'trafficSources' => $trafficSources,
            'chartData' => $this->getChartData($affiliate->id, $days)
        ]);
    }

    private function getConversionFunnelData($affiliateId, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $totalClicks = 100; // This would come from analytics/tracking system
        $registrations = Referral::where('affiliate_id', $affiliateId)
            ->where('created_at', '>=', $startDate)
            ->count();
        
        $purchases = ReferralTransaction::whereHas('referral', function($q) use ($affiliateId) {
                $q->where('affiliate_id', $affiliateId);
            })
            ->where('created_at', '>=', $startDate)
            ->count();

        return [
            'clicks' => $totalClicks,
            'registrations' => $registrations,
            'purchases' => $purchases,
            'click_to_register_rate' => $totalClicks > 0 ? round(($registrations / $totalClicks) * 100, 2) : 0,
            'register_to_purchase_rate' => $registrations > 0 ? round(($purchases / $registrations) * 100, 2) : 0,
            'overall_conversion_rate' => $totalClicks > 0 ? round(($purchases / $totalClicks) * 100, 2) : 0
        ];
    }

    private function getTrafficSourceData($affiliateId, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        // This would typically come from your analytics system
        return Referral::where('affiliate_id', $affiliateId)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function($referral) {
                return $referral->metadata['source'] ?? 'Direct';
            })
            ->map(function($group, $source) {
                return [
                    'source' => $source,
                    'count' => $group->count(),
                    'percentage' => 0 // Will be calculated in view
                ];
            })
            ->values()
            ->toArray();
    }

    private function getChartData($affiliateId, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        if ($this->chartType === 'commission') {
            return ReferralTransaction::whereHas('referral', function($q) use ($affiliateId) {
                    $q->where('affiliate_id', $affiliateId);
                })
                ->where('status', ReferralTransaction::STATUS_PAID)
                ->where('paid_at', '>=', $startDate)
                ->selectRaw('DATE(paid_at) as date, SUM(commission_amount) as value')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            return Referral::where('affiliate_id', $affiliateId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as value')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }
    }
}