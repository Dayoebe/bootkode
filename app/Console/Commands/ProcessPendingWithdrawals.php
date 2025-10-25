<?php

// app/Console/Commands/ProcessPendingWithdrawals.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Marketplace\Withdrawal;
use App\Services\PaystackService;

class ProcessPendingWithdrawals extends Command
{
    protected $signature = 'withdrawals:process';
    protected $description = 'Process pending withdrawals and update status';

    private PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        parent::__construct();
        $this->paystackService = $paystackService;
    }

    public function handle()
    {
        $processingWithdrawals = Withdrawal::where('status', Withdrawal::STATUS_PROCESSING)->get();

        $this->info("Processing {$processingWithdrawals->count()} withdrawals...");

        foreach ($processingWithdrawals as $withdrawal) {
            if ($withdrawal->paystack_transfer_code) {
                $result = $this->paystackService->verifyTransfer($withdrawal->paystack_transfer_code);
                
                if ($result['success']) {
                    if ($result['status'] === 'success') {
                        $withdrawal->update([
                            'status' => Withdrawal::STATUS_COMPLETED,
                            'completed_at' => now()
                        ]);
                        $this->info("✓ Withdrawal {$withdrawal->withdrawal_id} completed");
                    } elseif ($result['status'] === 'failed') {
                        $withdrawal->update([
                            'status' => Withdrawal::STATUS_FAILED,
                            'failure_reason' => $result['data']['reason'] ?? 'Transfer failed'
                        ]);
                        $this->error("✗ Withdrawal {$withdrawal->withdrawal_id} failed");
                    }
                }
            }
        }

        $this->info('Withdrawal processing completed!');
    }
}
