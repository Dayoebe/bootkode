<?php
// Scheduled Command: SendScheduledNewsletters.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsletterCampaign;
use App\Jobs\SendNewsletterCampaign;

class SendScheduledNewsletters extends Command
{
    protected $signature = 'newsletter:send-scheduled';
    protected $description = 'Send scheduled newsletter campaigns';

    public function handle()
    {
        $campaigns = NewsletterCampaign::scheduledForSending()->get();

        if ($campaigns->isEmpty()) {
            $this->info('No scheduled campaigns to send.');
            return Command::SUCCESS;
        }

        foreach ($campaigns as $campaign) {
            $this->info("Dispatching campaign: {$campaign->name}");
            SendNewsletterCampaign::dispatch($campaign);
        }

        $this->info("Dispatched {$campaigns->count()} campaigns for sending.");
        return Command::SUCCESS;
    }
}