<?php
// UPDATED: Analytics.php
namespace App\Livewire\Newsletter\Partials;

use Livewire\Component;
use App\Models\Admin\NewsletterCampaign;
use App\Models\Admin\NewsletterSubscriber;
use App\Models\Admin\NewsletterInteraction;
use Illuminate\Support\Facades\DB;

class Analytics extends Component
{
    public $selectedCampaign = null;
    public $dateRange = '30';

    public function mount()
    {
        $this->selectedCampaign = NewsletterCampaign::campaigns()
            ->where('status', NewsletterCampaign::STATUS_SENT)
            ->latest()
            ->first()
            ?->id;
    }

    public function getOverviewStatsProperty()
    {
        $query = NewsletterCampaign::campaigns()->where('status', NewsletterCampaign::STATUS_SENT);

        if ($this->dateRange !== 'all') {
            $query->where('sent_at', '>=', now()->subDays($this->dateRange));
        }

        $campaigns = $query->get();

        return [
            'total_campaigns' => $campaigns->count(),
            'total_sent' => $campaigns->sum('sent_count'),
            'total_opens' => $campaigns->sum('open_count'),
            'total_clicks' => $campaigns->sum('click_count'),
            'total_bounces' => $campaigns->sum('bounce_count'),
            'total_unsubscribes' => $campaigns->sum('unsubscribe_count'),
            'avg_open_rate' => $campaigns->count() > 0 ? round($campaigns->avg('open_rate'), 2) : 0,
            'avg_click_rate' => $campaigns->count() > 0 ? round($campaigns->avg('click_rate'), 2) : 0,
        ];
    }

    public function getCampaignStatsProperty()
    {
        if (!$this->selectedCampaign) {
            return null;
        }

        $campaign = NewsletterCampaign::campaigns()->find($this->selectedCampaign);
        if (!$campaign) {
            return null;
        }

        return [
            'campaign' => $campaign,
            'top_clicked_links' => $this->getTopClickedLinks($campaign->id),
        ];
    }

    public function getSubscriberGrowthProperty()
    {
        $days = min($this->dateRange === 'all' ? 365 : $this->dateRange, 365);
        
        return NewsletterSubscriber::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            });
    }

    private function getTopClickedLinks($campaignId)
    {
        return NewsletterInteraction::where('campaign_id', $campaignId)
            ->where('type', NewsletterInteraction::TYPE_CLICK)
            ->select(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.url")) as url'), DB::raw('COUNT(*) as clicks'))
            ->groupBy('url')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get();
    }

    public function getCampaignsForSelectProperty()
    {
        return NewsletterCampaign::campaigns()
            ->where('status', NewsletterCampaign::STATUS_SENT)
            ->orderBy('sent_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.newsletter.partials.analytics', [
            'overviewStats' => $this->overviewStats,
            'campaignStats' => $this->campaignStats,
            'subscriberGrowth' => $this->subscriberGrowth,
            'campaignsForSelect' => $this->campaignsForSelect,
        ]);
    }
}
