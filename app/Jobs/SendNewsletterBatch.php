<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Admin\NewsletterCampaign;
use App\Models\Admin\NewsletterInteraction;
use App\Services\ObservabilityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNewsletterBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public bool $failOnTimeout = true;

    protected $campaign;

    protected $sendIds;

    public function __construct(NewsletterCampaign $campaign, array $sendIds)
    {
        $this->campaign = $campaign;
        $this->sendIds = $sendIds;
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [60, 180];
    }

    public function handle(): void
    {
        // Split older serialized multi-recipient payloads without an unbounded SMTP loop.
        if (count($this->sendIds) > 1) {
            foreach (array_unique($this->sendIds) as $id) {
                self::dispatch($this->campaign, [$id]);
            }

            return;
        }
        $send = DB::transaction(function () {
            $send = NewsletterInteraction::with(['subscriber', 'campaign'])->where('campaign_id', $this->campaign->id)
                ->where('type', NewsletterInteraction::TYPE_SEND)->whereKey($this->sendIds[0] ?? 0)->lockForUpdate()->first();
            if (! $send) {
                return null;
            }
            if ($send->status === 'delivery_unknown') {
                throw new \RuntimeException('Newsletter delivery needs operator review before another send.');
            }
            if ($send->status !== NewsletterInteraction::STATUS_PENDING) {
                return null;
            }
            if (! $send->subscriber?->isActive() || $send->campaign->status === NewsletterCampaign::STATUS_CANCELLED) {
                $send->update(['status' => 'cancelled']);

                return null;
            }
            $send->update(['status' => 'processing', 'data' => array_merge($send->data ?? [], ['processing_started_at' => now()->toISOString()])]);

            return $send;
        });
        if (! $send) {
            return;
        }
        try {
            Mail::to($send->subscriber->email)->send(new NewsletterMail($send->campaign, $send));
        } catch (Throwable $exception) {
            $send->update(['status' => 'delivery_unknown', 'error_message' => 'Transport failed or acknowledgement was lost. Check provider delivery before retrying.']);
            app(ObservabilityService::class)->recordMailFailure('Newsletter delivery requires review', 'Transport acknowledgement unavailable.', ['interaction_id' => $send->id, 'campaign_id' => $send->campaign_id, 'exception_type' => $exception::class]);
            throw new \RuntimeException('Newsletter delivery requires operator review.');
        }
        DB::transaction(function () use ($send): void {
            $locked = NewsletterInteraction::whereKey($send->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'processing') {
                $locked->markAsSent(); // This method already increments the campaign exactly once.
            }
        });
        if (! NewsletterInteraction::where('campaign_id', $this->campaign->id)->where('type', NewsletterInteraction::TYPE_SEND)
            ->whereIn('status', ['pending', 'processing', 'delivery_unknown'])->exists()) {
            $this->campaign->refresh()->markAsSent();
        }
    }

    public function failed(?Throwable $exception): void
    {
        NewsletterInteraction::where('campaign_id', $this->campaign->id)->whereIn('id', $this->sendIds)
            ->where('status', 'processing')->update(['status' => 'delivery_unknown', 'error_message' => 'Worker stopped before delivery was confirmed. Review provider delivery before retrying.']);
    }
}
