<?php

namespace App\Jobs;

use App\Models\Admin\NewsletterCampaign;
use App\Models\Admin\NewsletterInteraction;
use App\Models\Admin\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class SendNewsletterCampaign implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 600;

    public int $uniqueFor = 3600;

    public bool $failOnTimeout = true;

    protected $campaign;

    public function __construct(NewsletterCampaign $campaign)
    {
        $this->campaign = $campaign;
        $this->onConnection('bulk-enrollment')->onQueue('bulk-enrollment')->afterCommit();
    }

    public function uniqueId(): string
    {
        return 'newsletter-campaign-'.$this->campaign->id;
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->uniqueId()))->releaseAfter(30)->expireAfter(900)];
    }

    public function backoff(): array
    {
        return [60, 180];
    }

    public function handle(): void
    {
        $this->campaign->refresh();
        if (in_array($this->campaign->status, [NewsletterCampaign::STATUS_SENT, NewsletterCampaign::STATUS_CANCELLED], true)) {
            return;
        }
        $this->campaign->markAsSending();
        $query = NewsletterSubscriber::active();
        foreach ($this->campaign->recipient_filters ?? [] as $filter) {
            if (($filter['type'] ?? null) === 'tag') {
                $query->whereJsonContains('tags', $filter['value']);
            }
        }
        $query->select('id')->chunkById(100, function ($subscribers): void {
            foreach ($subscribers as $subscriber) {
                NewsletterInteraction::firstOrCreate([
                    'campaign_id' => $this->campaign->id, 'subscriber_id' => $subscriber->id,
                    'type' => NewsletterInteraction::TYPE_SEND,
                ], ['status' => NewsletterInteraction::STATUS_PENDING]);
            }
        });
        $this->campaign->update(['total_recipients' => NewsletterInteraction::where('campaign_id', $this->campaign->id)->where('type', NewsletterInteraction::TYPE_SEND)->count()]);
        $limit = max(1, min(100, (int) NewsletterCampaign::getSetting('throttle_limit', 100)));
        $delay = max(1, min(3600, (int) NewsletterCampaign::getSetting('throttle_delay', 60)));
        $index = 0;
        NewsletterInteraction::where('campaign_id', $this->campaign->id)->pendingSends()->chunkById(100, function ($sends) use ($limit, $delay, &$index): void {
            foreach ($sends as $send) {
                SendNewsletterBatch::dispatch($this->campaign, [$send->id])->delay(now()->addSeconds(intdiv($index++, $limit) * $delay));
            }
        });
        if ($this->campaign->total_recipients === 0) {
            $this->campaign->markAsSent();
        }
    }
}
