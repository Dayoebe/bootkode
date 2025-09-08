<?php 

// app/Listeners/ProcessAffiliateCommission.php
namespace App\Listeners;

use App\Events\CoursePurchased;
use App\Services\AffiliateService;
use Illuminate\Support\Facades\Log;

class ProcessAffiliateCommission
{
    public function __construct(
        private AffiliateService $affiliateService
    ) {}

    public function handle(CoursePurchased $event): void
    {
        try {
            $result = $this->affiliateService->processCommission(
                $event->buyer,
                $event->course,
                $event->amount,
                $event->purchaseTransaction
            );

            if ($result['success'] && $result['commission_paid'] > 0) {
                Log::info('Affiliate commission processed successfully', [
                    'buyer_id' => $event->buyer->id,
                    'course_id' => $event->course->id,
                    'commission_amount' => $result['commission_paid']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process affiliate commission', [
                'buyer_id' => $event->buyer->id,
                'course_id' => $event->course->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
