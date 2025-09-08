<?php

// PaystackService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PaystackTransaction;
use App\Models\Wallet;
use App\Models\Withdrawal;

class PaystackService
{
    private $secretKey;
    private $publicKey;
    private $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
        $this->baseUrl = 'https://api.paystack.co';
    }

    /**
     * Initialize wallet funding transaction
     */
    public function initializeWalletFunding(array $data): array
    {
        try {
            $reference = 'WF_' . time() . '_' . uniqid();

            $payload = [
                'email' => $data['email'],
                'amount' => $data['amount'] * 100, // Convert to kobo
                'reference' => $reference,
                'currency' => 'NGN',
                'callback_url' => route('paystack.callback'),
                'metadata' => [
                    'user_id' => $data['user_id'],
                    'transaction_type' => 'wallet_funding',
                    'custom_fields' => [
                        [
                            'display_name' => 'Purpose',
                            'variable_name' => 'purpose',
                            'value' => 'Wallet Funding'
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                // Store transaction record
                PaystackTransaction::create([
                    'reference' => $reference,
                    'access_code' => $responseData['data']['access_code'],
                    'amount' => $data['amount'],
                    'currency' => 'NGN',
                    'status' => PaystackTransaction::STATUS_PENDING,
                    'customer_email' => $data['email'],
                    'customer_name' => $data['customer_name'] ?? null,
                    'transaction_type' => PaystackTransaction::TYPE_WALLET_FUNDING,
                    'transactionable_type' => Wallet::class,
                    'transactionable_id' => $data['wallet_id']
                ]);

                return [
                    'success' => true,
                    'data' => $responseData['data'],
                    'reference' => $reference
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Payment initialization failed'
            ];

        } catch (\Exception $e) {
            Log::error('Paystack initialization error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while initializing payment'
            ];
        }
    }

    /**
     * Verify transaction from Paystack
     */
    public function verifyTransaction(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . "/transaction/verify/{$reference}");

            if ($response->successful()) {
                $data = $response->json()['data'];

                return [
                    'success' => true,
                    'data' => $data,
                    'status' => $data['status'],
                    'amount' => $data['amount'] / 100, // Convert from kobo
                    'paid_at' => $data['paid_at']
                ];
            }

            return [
                'success' => false,
                'message' => 'Transaction verification failed'
            ];

        } catch (\Exception $e) {
            Log::error('Paystack verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during verification'
            ];
        }
    }

    /**
     * Process wallet funding after successful payment
     */
    public function processWalletFunding(string $reference): array
    {
        try {
            $verification = $this->verifyTransaction($reference);

            if (!$verification['success'] || $verification['status'] !== 'success') {
                return [
                    'success' => false,
                    'message' => 'Payment verification failed'
                ];
            }

            $paystackTransaction = PaystackTransaction::where('reference', $reference)->first();

            if (!$paystackTransaction) {
                return [
                    'success' => false,
                    'message' => 'Transaction record not found'
                ];
            }

            // Prevent double processing
            if ($paystackTransaction->status === PaystackTransaction::STATUS_SUCCESS) {
                return [
                    'success' => true,
                    'message' => 'Transaction already processed'
                ];
            }

            // Update transaction status
            $paystackTransaction->update([
                'status' => PaystackTransaction::STATUS_SUCCESS,
                'paystack_reference' => $verification['data']['reference'],
                'paystack_response' => $verification['data'],
                'paid_at' => $verification['paid_at']
            ]);

            // Credit user wallet
            $wallet = $paystackTransaction->transactionable;
            $fundingTransaction = $wallet->credit(
                $verification['amount'],
                'funding',
                'Wallet funded via Paystack',
                $paystackTransaction,
                ['paystack_reference' => $verification['data']['reference']]
            );

            return [
                'success' => true,
                'message' => 'Wallet funded successfully',
                'amount' => $verification['amount'],
                'new_balance' => $wallet->fresh()->balance,
                'transaction' => $fundingTransaction
            ];

        } catch (\Exception $e) {
            Log::error('Wallet funding processing error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing payment'
            ];
        }
    }
    /**
     * Create transfer recipient for withdrawals
     */
    public function createTransferRecipient(array $data): array
    {
        try {
            $payload = [
                'type' => 'nuban',
                'name' => $data['account_name'],
                'account_number' => $data['account_number'],
                'bank_code' => $data['bank_code'],
                'currency' => 'NGN'
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transferrecipient', $payload);

            if ($response->successful()) {
                $data = $response->json()['data'];

                return [
                    'success' => true,
                    'recipient_code' => $data['recipient_code'],
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to create transfer recipient'
            ];

        } catch (\Exception $e) {
            Log::error('Create transfer recipient error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while creating recipient'
            ];
        }
    }

    /**
     * Initiate transfer for withdrawal
     */
    public function initiateTransfer(Withdrawal $withdrawal): array
    {
        try {
            // First, create or get recipient
            $recipientData = [
                'account_name' => $withdrawal->account_name,
                'account_number' => $withdrawal->account_number,
                'bank_code' => $withdrawal->bank_code
            ];

            if (!$withdrawal->paystack_recipient_code) {
                $recipient = $this->createTransferRecipient($recipientData);

                if (!$recipient['success']) {
                    return $recipient;
                }

                $withdrawal->update([
                    'paystack_recipient_code' => $recipient['recipient_code']
                ]);
            }

            // Initiate transfer
            $payload = [
                'source' => 'balance',
                'amount' => $withdrawal->amount * 100, // Convert to kobo
                'recipient' => $withdrawal->paystack_recipient_code,
                'reason' => "Instructor withdrawal - {$withdrawal->withdrawal_id}",
                'reference' => 'WD_' . $withdrawal->withdrawal_id
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transfer', $payload);

            if ($response->successful()) {
                $data = $response->json()['data'];

                $withdrawal->update([
                    'paystack_transfer_code' => $data['transfer_code'],
                    'status' => Withdrawal::STATUS_PROCESSING,
                    'processed_at' => now()
                ]);

                return [
                    'success' => true,
                    'transfer_code' => $data['transfer_code'],
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Transfer initiation failed'
            ];

        } catch (\Exception $e) {
            Log::error('Transfer initiation error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while initiating transfer'
            ];
        }
    }

    /**
     * Verify transfer status
     */
    public function verifyTransfer(string $transferCode): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . "/transfer/{$transferCode}");

            if ($response->successful()) {
                $data = $response->json()['data'];

                return [
                    'success' => true,
                    'status' => $data['status'],
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => 'Transfer verification failed'
            ];

        } catch (\Exception $e) {
            Log::error('Transfer verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during verification'
            ];
        }
    }

    /**
     * Get account balance from Paystack
     */
    public function getBalance(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/balance');

            if ($response->successful()) {
                $balances = $response->json()['data'];

                return [
                    'success' => true,
                    'balances' => $balances
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch balance'
            ];

        } catch (\Exception $e) {
            Log::error('Balance fetch error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while fetching balance'
            ];
        }
    }

    /**
     * Get list of banks
     */
    public function getBanks(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank?currency=NGN');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'banks' => $response->json()['data']
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch banks'
            ];

        } catch (\Exception $e) {
            Log::error('Banks fetch error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while fetching banks'
            ];
        }
    }

    /**
     * Resolve bank account details
     */
    public function resolveAccountNumber(string $accountNumber, string $bankCode): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . "/bank/resolve?account_number={$accountNumber}&bank_code={$bankCode}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'account_name' => $response->json()['data']['account_name'],
                    'account_number' => $response->json()['data']['account_number']
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Account resolution failed'
            ];

        } catch (\Exception $e) {
            Log::error('Account resolution error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while resolving account'
            ];
        }
    }

    /**
     * Get Paystack public key for frontend
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
    public function createRefund($paystackReference, $amount, $currency = 'NGN')
    {
        try {
            $payload = [
                'transaction' => $paystackReference,
                'amount' => $amount * 100, // Convert to kobo
                'currency' => $currency
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/refund', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'id' => $response->json()['data']['id'],
                    'data' => $response->json()['data']
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Refund creation failed'
            ];

        } catch (\Exception $e) {
            Log::error('Paystack refund error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing refund'
            ];
        }
    }

}
