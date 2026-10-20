<?php

// Controllers/FinancialController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaystackService;
use App\Services\WalletService;
use App\Services\ObservabilityService;
use App\Models\Marketplace\PaystackTransaction;
use Illuminate\Support\Facades\Log;

class FinancialController extends Controller
{
    private PaystackService $paystackService;
    private WalletService $walletService;

    public function __construct(PaystackService $paystackService, WalletService $walletService)
    {
        $this->paystackService = $paystackService;
        $this->walletService = $walletService;
    }

    /**
     * Handle Paystack callback
     */
    public function paystackCallback(Request $request)
    {
        $reference = $request->query('reference');
        
        if (!$reference) {
            return redirect()->route('wallet.index')
                ->with('error', 'Invalid payment reference');
        }

        $payment = PaystackTransaction::where('reference', $reference)->first();
        abort_unless($payment?->transactionable instanceof \App\Models\Marketplace\Wallet
            && $payment->transactionable->user_id === $request->user()->id, 403);

        $result = $this->paystackService->processWalletFunding($reference);

        if ($result['success']) {
            return redirect()->route('wallet.index')
                ->with('success', 'Wallet funded successfully! Amount: ₦' . number_format($result['amount'], 2));
        }

        return redirect()->route('wallet.index')
            ->with('error', $result['message']);
    }

    /**
     * Handle Paystack webhook
     */
    public function paystackWebhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();
        $secret = (string) config('services.paystack.secret_key');
        if ($secret === '') {
            return response('Payment gateway is not configured', 503);
        }
        $expectedSignature = hash_hmac('sha512', $payload, $secret);

        if (!hash_equals((string) $signature, $expectedSignature)) {
            Log::warning('Invalid Paystack webhook signature');
            app(ObservabilityService::class)->recordWebhookFailure(
                'paystack',
                'Invalid Paystack webhook signature',
                'Paystack webhook was rejected because the signature did not match.',
                [
                    'ip' => $request->ip(),
                ],
                \App\Models\System\ObservabilityEvent::SEVERITY_WARNING
            );

            return response('Invalid signature', 400);
        }

        $data = $request->json()->all();
        $event = $data['event'] ?? null;

        if (!$event) {
            app(ObservabilityService::class)->recordWebhookFailure(
                'paystack',
                'Paystack webhook missing event name',
                'A Paystack webhook arrived without an event field.',
                ['event' => $event]
            );

            return response('Missing event', 422);
        }

        try {
            switch ($event) {
                case 'charge.success':
                    $this->handleChargeSuccess($data['data']);
                    break;
                    
                case 'transfer.success':
                    $this->handleTransferSuccess($data['data']);
                    break;
                    
                case 'transfer.failed':
                    $this->handleTransferFailed($data['data']);
                    break;
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Paystack webhook processing failed', ['event' => $event, 'exception' => $e::class]);
            app(ObservabilityService::class)->recordWebhookFailure(
                'paystack',
                'Paystack webhook processing failed',
                $e->getMessage(),
                [
                    'event' => $event,
                    'reference' => $data['data']['reference'] ?? null,
                    'transfer_code' => $data['data']['transfer_code'] ?? null,
                    'exception' => $e::class,
                ],
                \App\Models\System\ObservabilityEvent::SEVERITY_CRITICAL
            );

            return response('Error processing webhook', 500);
        }
    }

    private function handleChargeSuccess($data)
    {
        $reference = $data['reference'];
        $result = $this->paystackService->processWalletFunding($reference);
        if (!$result['success']) {
            throw new \RuntimeException('Wallet funding could not be processed.');
        }
    }

    private function handleTransferSuccess($data)
    {
        $transferCode = $data['transfer_code'];
        
        $withdrawal = \App\Models\Marketplace\Withdrawal::where('paystack_transfer_code', $transferCode)->first();
        if ($withdrawal) {
            $previousStatus = $withdrawal->status;
            $withdrawal->update([
                'status' => \App\Models\Marketplace\Withdrawal::STATUS_COMPLETED,
                'completed_at' => now()
            ]);

            app(\App\Services\CommercialReadinessService::class)->recordPayoutAudit(
                $withdrawal->fresh(),
                'withdrawal_transfer_completed',
                $previousStatus,
                \App\Models\Marketplace\Withdrawal::STATUS_COMPLETED,
                null,
                ['webhook' => $data],
                'Paystack confirmed successful transfer.'
            );
        }
    }

    private function handleTransferFailed($data)
    {
        $transferCode = $data['transfer_code'];
        
        $withdrawal = \App\Models\Marketplace\Withdrawal::where('paystack_transfer_code', $transferCode)->first();
        if ($withdrawal) {
            $previousStatus = $withdrawal->status;
            $withdrawal->update([
                'status' => \App\Models\Marketplace\Withdrawal::STATUS_FAILED,
                'failure_reason' => $data['reason'] ?? 'Transfer failed'
            ]);

            app(\App\Services\CommercialReadinessService::class)->recordPayoutAudit(
                $withdrawal->fresh(),
                'withdrawal_transfer_failed',
                $previousStatus,
                \App\Models\Marketplace\Withdrawal::STATUS_FAILED,
                null,
                ['webhook' => $data],
                $data['reason'] ?? 'Paystack reported transfer failure.'
            );
        }
    }

    /**
     * Get banks for withdrawal form
     */
    public function getBanks()
    {
        $result = $this->paystackService->getBanks();
        
        if ($result['success']) {
            return response()->json($result['banks']);
        }

        return response()->json(['error' => 'Failed to fetch banks'], 500);
    }

    /**
     * Resolve bank account
     */
    public function resolveAccount(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string|size:10',
            'bank_code' => 'required|string'
        ]);

        $result = $this->paystackService->resolveAccountNumber(
            $request->account_number,
            $request->bank_code
        );

        return response()->json($result);
    }
}
