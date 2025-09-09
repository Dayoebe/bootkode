<?php

namespace App\Livewire\Newsletter\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterInteraction;

class CampaignReports extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedCampaign = null;
    public $campaignDetails = [];

    public function selectCampaign($campaignId)
    {
        $this->selectedCampaign = NewsletterCampaign::with('interactions.subscriber')->find($campaignId);
        
        if ($this->selectedCampaign) {
            $this->loadCampaignDetails();
        }
    }

    public function closeCampaignDetails()
    {
        $this->selectedCampaign = null;
        $this->campaignDetails = [];
    }
    // In CampaignReports.php, update the loadCampaignDetails method
protected function loadCampaignDetails()
{
    if (!$this->selectedCampaign) return;

    $interactions = $this->selectedCampaign->interactions;
    
    // Count different types of interactions
    $sent = $interactions->where('type', NewsletterInteraction::TYPE_SEND)->count();
    $opens = $interactions->where('type', NewsletterInteraction::TYPE_OPEN)->count();
    $clicks = $interactions->where('type', NewsletterInteraction::TYPE_CLICK)->count();
    $bounces = $interactions->where('type', NewsletterInteraction::TYPE_BOUNCE)->count();
    $unsubscribes = $interactions->where('type', NewsletterInteraction::TYPE_UNSUBSCRIBE)->count();
    
    // Get failed sends with error messages
    $failedSends = $interactions->where('type', NewsletterInteraction::TYPE_SEND)
        ->where('status', NewsletterInteraction::STATUS_FAILED);
        
    $failed = $failedSends->count();
    $failureReasons = $failedSends->pluck('error_message')->filter()->countBy();
            
    // Get successful sends
    $successfulSends = $interactions->where('type', NewsletterInteraction::TYPE_SEND)
        ->where('status', NewsletterInteraction::STATUS_COMPLETED)
        ->count();

    $this->campaignDetails = [
        'sent' => $sent,
        'opens' => $opens,
        'clicks' => $clicks,
        'bounces' => $bounces,
        'unsubscribes' => $unsubscribes,
        'failed' => $failed,
        'successful' => $successfulSends,
        'open_rate' => $sent > 0 ? round(($opens / $sent) * 100, 2) : 0,
        'click_rate' => $sent > 0 ? round(($clicks / $sent) * 100, 2) : 0,
        'bounce_rate' => $sent > 0 ? round(($bounces / $sent) * 100, 2) : 0,
        'unsubscribe_rate' => $sent > 0 ? round(($unsubscribes / $sent) * 100, 2) : 0,
        'failure_reasons' => $failureReasons,
    ];
}
    public function getCampaignsProperty()
    {
        $query = NewsletterCampaign::campaigns()
            ->whereIn('status', ['sent', 'sending', 'cancelled'])
            ->withCount(['interactions as sent_count' => function ($query) {
                $query->where('type', NewsletterInteraction::TYPE_SEND);
            }])
            ->withCount(['interactions as open_count' => function ($query) {
                $query->where('type', NewsletterInteraction::TYPE_OPEN);
            }])
            ->withCount(['interactions as click_count' => function ($query) {
                $query->where('type', NewsletterInteraction::TYPE_CLICK);
            }])
            ->withCount(['interactions as bounce_count' => function ($query) {
                $query->where('type', NewsletterInteraction::TYPE_BOUNCE);
            }])
            ->withCount(['interactions as unsubscribe_count' => function ($query) {
                $query->where('type', NewsletterInteraction::TYPE_UNSUBSCRIBE);
            }]);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%');
        }

        return $query->orderBy('sent_at', 'desc')->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.newsletter.partials.campaign-reports', [
            'campaigns' => $this->campaigns,
        ]);
    }
}