<?php

// Controllers/FinancialController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaystackService;
use App\Services\WalletService;
use App\Models\PaystackTransaction;
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
        $expectedSignature = hash_hmac('sha512', $payload, config('services.paystack.secret_key'));

        if (!hash_equals($signature, $expectedSignature)) {
            Log::warning('Invalid Paystack webhook signature');
            return response('Invalid signature', 400);
        }

        $data = $request->json()->all();
        $event = $data['event'];

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
            Log::error('Paystack webhook error: ' . $e->getMessage(), ['data' => $data]);
            return response('Error processing webhook', 500);
        }
    }

    private function handleChargeSuccess($data)
    {
        $reference = $data['reference'];
        $this->paystackService->processWalletFunding($reference);
    }

    private function handleTransferSuccess($data)
    {
        $transferCode = $data['transfer_code'];
        
        $withdrawal = \App\Models\Withdrawal::where('paystack_transfer_code', $transferCode)->first();
        if ($withdrawal) {
            $withdrawal->update([
                'status' => \App\Models\Withdrawal::STATUS_COMPLETED,
                'completed_at' => now()
            ]);
        }
    }

    private function handleTransferFailed($data)
    {
        $transferCode = $data['transfer_code'];
        
        $withdrawal = \App\Models\Withdrawal::where('paystack_transfer_code', $transferCode)->first();
        if ($withdrawal) {
            $withdrawal->update([
                'status' => \App\Models\Withdrawal::STATUS_FAILED,
                'failure_reason' => $data['reason'] ?? 'Transfer failed'
            ]);
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