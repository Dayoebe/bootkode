<?php 
// UPDATED JOBS: SendNewsletterCampaign.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterInteraction;

class SendNewsletterCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    public function __construct(NewsletterCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle()
    {
        $this->campaign->markAsSending();

        // Get recipients
        $recipients = $this->getRecipients();
        $this->campaign->update(['total_recipients' => $recipients->count()]);

        // Create send interactions
        foreach ($recipients as $subscriber) {
            NewsletterInteraction::createSendRecord($this->campaign->id, $subscriber->id);
        }

        // Dispatch batched jobs
        $this->dispatchBatchedJobs();
    }

    private function getRecipients()
    {
        $query = NewsletterSubscriber::active();

        if ($this->campaign->recipient_filters) {
            foreach ($this->campaign->recipient_filters as $filter) {
                if ($filter['type'] === 'tag') {
                    $query->whereJsonContains('tags', $filter['value']);
                }
            }
        }

        return $query->get();
    }

    private function dispatchBatchedJobs()
    {
        $throttleLimit = NewsletterCampaign::getSetting('throttle_limit', 100);
        $throttleDelay = NewsletterCampaign::getSetting('throttle_delay', 60);

        $pendingSends = NewsletterInteraction::where('campaign_id', $this->campaign->id)
            ->pendingSends()
            ->pluck('id');

        $chunks = $pendingSends->chunk($throttleLimit);

        foreach ($chunks as $index => $chunk) {
            $delay = $index * $throttleDelay;
            SendNewsletterBatch::dispatch($this->campaign, $chunk->toArray())
                ->delay(now()->addSeconds($delay));
        }
    }
}
