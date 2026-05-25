<?php 
// Job: SendNewsletterBatch.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Admin\NewsletterCampaign;
use App\Models\Admin\NewsletterInteraction; // Add this import
use App\Mail\NewsletterMail;
use App\Services\ObservabilityService;
use Illuminate\Support\Facades\Mail;

class SendNewsletterBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $sendIds;

    public function __construct(NewsletterCampaign $campaign, array $sendIds)
    {
        $this->campaign = $campaign;
        $this->sendIds = $sendIds;
    }
// In SendNewsletterBatch.php
public function handle()
{
    $sends = NewsletterInteraction::with('subscriber')
        ->whereIn('id', $this->sendIds)
        ->where('status', NewsletterInteraction::STATUS_PENDING)
        ->get();

    foreach ($sends as $send) {
        try {
            Mail::to($send->subscriber->email)
                ->send(new NewsletterMail($this->campaign, $send));

            $send->markAsSent();
            $this->campaign->increment('sent_count');

        } catch (\Exception $e) {
            // Store the specific error message
            $send->markAsFailed($e->getMessage());
            
            // Check if it's a bounce
            if ($this->isBounce($e)) {
                $send->markAsBounced();
            }
            
            // Log the error for debugging
            \Log::error('Email sending failed', [
                'subscriber_id' => $send->subscriber->id,
                'email' => $send->subscriber->email,
                'error' => $e->getMessage(),
                'campaign_id' => $this->campaign->id
            ]);

            app(ObservabilityService::class)->recordMailFailure(
                'Newsletter email failed',
                $e->getMessage(),
                [
                    'campaign_id' => $this->campaign->id,
                    'campaign_name' => $this->campaign->name,
                    'subscriber_id' => $send->subscriber->id,
                    'subscriber_email' => $send->subscriber->email,
                    'interaction_id' => $send->id,
                ]
            );
        }
    }

    // Check if campaign is complete
    $this->checkCampaignCompletion();
}
 
    private function isBounce(\Exception $e)
    {
        $bounceIndicators = [
            'mailbox unavailable',
            'user unknown',
            'address not found',
            'recipient address rejected',
        ];

        $message = strtolower($e->getMessage());
        
        foreach ($bounceIndicators as $indicator) {
            if (strpos($message, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }

    private function checkCampaignCompletion()
    {
        // Fixed: Use NewsletterInteraction instead of NewsletterCampaign
        $pendingCount = NewsletterInteraction::where('campaign_id', $this->campaign->id)
            ->where('status', NewsletterInteraction::STATUS_PENDING)
            ->count();

        if ($pendingCount === 0) {
            $this->campaign->markAsSent();
        }
    }
}
